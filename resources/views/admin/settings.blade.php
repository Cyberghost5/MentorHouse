@extends('admin.layout')

@section('title', 'Settings')

@section('content')
<div class="max-w-lg">
    <div class="rounded-2xl p-6" style="background:white; border:1px solid #e6e0d0;">
        <h2 class="font-black mb-5" style="color:#1a3327;">Payment Gateway</h2>

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-3 mb-6">
                @foreach (['paystack' => 'Paystack', 'korapay' => 'Korapay'] as $value => $label)
                    <label class="flex items-center gap-3 p-4 rounded-xl cursor-pointer transition"
                           style="border:{{ $gateway === $value ? '2px solid #1a3327; background:rgba(26,51,39,.05)' : '1px solid #d6cfbe' }};">
                        <input type="radio" name="payment_gateway" value="{{ $value }}"
                               @checked($gateway === $value)
                               style="accent-color:#1a3327;">
                        <div>
                            <p class="font-semibold" style="color:#1a3327;">{{ $label }}</p>
                            <p class="text-xs" style="color:#6b7a72;">
                                @if ($value === 'paystack')
                                    Uses <code>PAYSTACK_SECRET_KEY</code> from .env
                                @else
                                    Uses <code>KORAPAY_SECRET_KEY</code> + <code>KORAPAY_ENCRYPTION_KEY</code> from .env
                                @endif
                            </p>
                        </div>
                    </label>
                @endforeach
            </div>

            <button type="submit"
                    class="px-6 py-2 rounded-xl font-bold transition"
                    style="background:#1a3327; color:#f4f1e8;"
                    onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
                Save Settings
            </button>
        </form>
    </div>

    <div class="mt-6 rounded-2xl p-5 text-sm" style="background:rgba(196,154,60,.08); border:1px solid rgba(196,154,60,.3); color:#8a6a1a;">
        <p class="font-semibold mb-1">Environment keys required</p>
        <ul class="list-disc list-inside space-y-0.5 text-xs" style="color:#8a6a1a;">
            <li>PAYSTACK_SECRET_KEY, PAYSTACK_PUBLIC_KEY</li>
            <li>KORAPAY_SECRET_KEY, KORAPAY_PUBLIC_KEY, KORAPAY_ENCRYPTION_KEY</li>
        </ul>
        <p class="mt-2 text-xs">Add these to your <code>.env</code> file and run <code>php artisan config:clear</code>.</p>
    </div>
</div>
@endsection
