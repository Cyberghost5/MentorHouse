<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SessionRequest;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * Initiate payment for an accepted paid session request.
     * Redirects the mentee to the gateway checkout page (or local simulator).
     */
    public function pay(Request $request, SessionRequest $sessionRequest): \Illuminate\Http\Response|RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->id === $sessionRequest->mentee_id, 403);
        abort_unless($sessionRequest->requiresPayment(), 400, 'This session does not require payment.');
        abort_unless($sessionRequest->isAccepted(), 400, 'Session must be accepted before payment.');

        /** @var Payment $payment */
        $payment = $sessionRequest->payment;

        if (! $payment) {
            abort(404, 'No payment record found for this session.');
        }

        if ($payment->isPaid()) {
            return redirect()->route('session-requests.index')
                ->with('status', 'This session has already been paid for.');
        }

        // When no gateway key is configured, use the built-in simulator.
        if (! config('services.paystack.secret') && ! config('services.korapay.secret')) {
            return redirect()->route('payments.simulate', $sessionRequest);
        }

        try {
            $checkoutUrl = $this->paymentService->initiate($payment, $user->email);
        } catch (\RuntimeException $e) {
            return redirect()->route('session-requests.index')
                ->with('error', 'Payment gateway error: ' . $e->getMessage());
        }

        return redirect()->away($checkoutUrl);
    }

    /**
     * Show the simulated checkout page (local / no-key mode).
     */
    public function simulate(Request $request, SessionRequest $sessionRequest): View
    {
        abort_unless($request->user()->id === $sessionRequest->mentee_id, 403);

        $payment = $sessionRequest->payment;
        abort_if(! $payment, 404);

        return view('payments.simulate', compact('sessionRequest', 'payment'));
    }

    /**
     * Confirm a simulated payment — marks it paid immediately.
     */
    public function confirmSimulate(Request $request, SessionRequest $sessionRequest): RedirectResponse
    {
        abort_unless($request->user()->id === $sessionRequest->mentee_id, 403);

        $payment = $sessionRequest->payment;
        abort_if(! $payment, 404);

        if (! $payment->isPaid()) {
            $payment->update([
                'status'            => 'paid',
                'gateway_reference' => 'SIM-' . strtoupper(\Illuminate\Support\Str::random(10)),
            ]);
        }

        return redirect()->route('session-requests.index')
            ->with('status', 'Payment successful! Your session is confirmed.');
    }

    /**
     * Handle the redirect callback from the payment gateway.
     */
    public function callback(Request $request): RedirectResponse
    {
        $reference = $request->query('reference') ?? $request->query('trxref');

        if (! $reference) {
            return redirect()->route('session-requests.index')
                ->with('error', 'Payment reference missing.');
        }

        $payment = Payment::where('gateway_reference', $reference)->first();

        if (! $payment) {
            return redirect()->route('session-requests.index')
                ->with('error', 'Payment not found.');
        }

        // Verify with the gateway
        try {
            $this->verifyAndConfirm($payment, $reference);
        } catch (\RuntimeException $e) {
            return redirect()->route('session-requests.index')
                ->with('error', 'Payment verification failed: ' . $e->getMessage());
        }

        $payment->refresh();

        if ($payment->isPaid()) {
            return redirect()->route('session-requests.index')
                ->with('status', 'Payment successful! Your session is confirmed.');
        }

        return redirect()->route('session-requests.index')
            ->with('error', 'Payment was not completed. Please try again.');
    }

    /**
     * Webhook endpoint — called directly by Paystack or Korapay.
     */
    public function webhook(Request $request): Response
    {
        $payload   = $request->getContent();
        $gateway   = $this->detectGateway($request);

        $valid = match ($gateway) {
            'paystack' => $this->paymentService->verifyPaystackWebhookSignature(
                $payload,
                $request->header('X-Paystack-Signature', '')
            ),
            'korapay'  => $this->paymentService->verifyKorapayWebhookSignature(
                $payload,
                $request->header('X-Korapay-Signature', '')
            ),
            default    => false,
        };

        if (! $valid) {
            return response('Unauthorized', 401);
        }

        $data      = $request->json()->all();
        $reference = match ($gateway) {
            'paystack' => data_get($data, 'data.reference'),
            'korapay'  => data_get($data, 'data.reference'),
            default    => null,
        };

        $isSuccess = match ($gateway) {
            'paystack' => data_get($data, 'data.status') === 'success',
            'korapay'  => data_get($data, 'data.status') === 'success',
            default    => false,
        };

        if ($reference && $isSuccess) {
            $payment = Payment::where('gateway_reference', $reference)->first();
            if ($payment && $payment->isPending()) {
                $payment->update(['status' => 'paid']);
            }
        }

        return response('OK', 200);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function detectGateway(Request $request): string
    {
        if ($request->hasHeader('X-Paystack-Signature')) {
            return 'paystack';
        }
        if ($request->hasHeader('X-Korapay-Signature')) {
            return 'korapay';
        }
        return 'unknown';
    }

    private function verifyAndConfirm(Payment $payment, string $reference): void
    {
        if ($payment->gateway === 'paystack') {
            $data   = $this->paymentService->verifyPaystack($reference);
            $status = data_get($data, 'status');
            if ($status === 'success') {
                $payment->update(['status' => 'paid']);
            }
        } elseif ($payment->gateway === 'korapay') {
            $data   = $this->paymentService->verifyKorapay($reference);
            $status = data_get($data, 'status');
            if ($status === 'success') {
                $payment->update(['status' => 'paid']);
            }
        }
    }
}
