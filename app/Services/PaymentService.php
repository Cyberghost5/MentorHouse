<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentService
{
    public function activeGateway(): string
    {
        return Setting::get('payment_gateway', 'paystack');
    }

    /**
     * Initialize a payment for the given payment record.
     * Returns the checkout URL to redirect the user to.
     *
     * @throws \RuntimeException
     */
    public function initiate(Payment $payment, string $email): string
    {
        $reference = 'MH-' . strtoupper(Str::random(12));

        $payment->update(['gateway_reference' => $reference]);

        return match ($payment->gateway) {
            'paystack' => $this->initiatePaystack($payment, $email, $reference),
            'korapay'  => $this->initiateKorapay($payment, $email, $reference),
            default    => throw new \RuntimeException("Unknown gateway: {$payment->gateway}"),
        };
    }

    // ─── Paystack ───────────────────────────────────────────────────────────

    private function initiatePaystack(Payment $payment, string $email, string $reference): string
    {
        // Paystack amounts are in kobo (smallest unit); multiply by 100
        $response = Http::withToken(config('services.paystack.secret'))
            ->post('https://api.paystack.co/transaction/initialize', [
                'email'        => $email,
                'amount'       => (int) ($payment->amount * 100),
                'reference'    => $reference,
                'currency'     => $payment->currency,
                'callback_url' => route('payments.callback'),
                'metadata'     => ['payment_id' => $payment->id],
            ]);

        $this->assertSuccess($response, 'Paystack initialization');

        return $response->json('data.authorization_url');
    }

    public function verifyPaystack(string $reference): array
    {
        $response = Http::withToken(config('services.paystack.secret'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        $this->assertSuccess($response, 'Paystack verification');

        return $response->json('data');
    }

    public function verifyPaystackWebhookSignature(string $payload, string $signature): bool
    {
        $expected = hash_hmac('sha512', $payload, config('services.paystack.secret'));
        return hash_equals($expected, $signature);
    }

    // ─── Korapay ────────────────────────────────────────────────────────────

    private function initiateKorapay(Payment $payment, string $email, string $reference): string
    {
        $response = Http::withToken(config('services.korapay.secret'))
            ->post('https://api.korapay.com/merchant/api/v1/charges/initialize', [
                'reference'            => $reference,
                'amount'               => (float) $payment->amount,
                'currency'             => $payment->currency,
                'notification_url'     => route('payments.webhook'),
                'merchant_bears_cost'  => false,
                'customer'             => ['email' => $email],
                'metadata'             => ['payment_id' => $payment->id],
            ]);

        $this->assertSuccess($response, 'Korapay initialization');

        return $response->json('data.checkout_url');
    }

    public function verifyKorapay(string $reference): array
    {
        $response = Http::withToken(config('services.korapay.secret'))
            ->get("https://api.korapay.com/merchant/api/v1/charges/{$reference}");

        $this->assertSuccess($response, 'Korapay verification');

        return $response->json('data');
    }

    public function verifyKorapayWebhookSignature(string $payload, string $signature): bool
    {
        $expected = hash_hmac('sha256', $payload, config('services.korapay.encryption_key'));
        return hash_equals($expected, $signature);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function assertSuccess(Response $response, string $context): void
    {
        if ($response->failed() || ! $response->json('status')) {
            $msg = $response->json('message') ?? $response->body();
            throw new \RuntimeException("{$context} failed: {$msg}");
        }
    }
}
