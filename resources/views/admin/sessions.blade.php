@extends('admin.layout')

@section('title', 'Session Requests')

@section('content')
{{-- Status filter --}}
<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <select name="status" class="rounded-xl px-3 py-2 text-sm"
            style="border:1px solid #d6cfbe; color:#1a3327;"
            onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'">
        <option value="">All statuses</option>
        @foreach (['pending','accepted','declined','completed','cancelled'] as $s)
            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-bold transition"
            style="background:#1a3327; color:#f4f1e8;"
            onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">Filter</button>
    <a href="{{ route('admin.sessions') }}" class="px-4 py-2 rounded-xl text-sm transition"
       style="background:#ede9de; color:#4a5e55;"
       onmouseover="this.style.background='#d6cfbe'" onmouseout="this.style.background='#ede9de'">Reset</a>
</form>

<div class="rounded-2xl overflow-hidden" style="background:white; border:1px solid #e6e0d0;">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs uppercase tracking-wide" style="color:#6b7a72; background:#ede9de; border-bottom:1px solid #e6e0d0;">
                <tr>
                    <th class="px-5 py-3 text-left">#</th>
                    <th class="px-5 py-3 text-left">Mentee</th>
                    <th class="px-5 py-3 text-left">Mentor</th>
                    <th class="px-5 py-3 text-left">Type</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Amount</th>
                    <th class="px-5 py-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sessions as $s)
                    <tr style="border-bottom:1px solid #f4f1e8;">
                        <td class="px-5 py-3" style="color:#6b7a72;">{{ $s->id }}</td>
                        <td class="px-5 py-3 font-medium" style="color:#1a3327;">{{ $s->mentee->name }}</td>
                        <td class="px-5 py-3" style="color:#4a5e55;">{{ $s->mentor->name }}</td>
                        <td class="px-5 py-3" style="color:#6b7a72;">{{ ucfirst(str_replace('_', ' ', $s->session_type)) }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $s->statusColor() }}">
                                {{ $s->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-5 py-3" style="color:#4a5e55;">
                            {{ $s->fee_amount ? '₦' . number_format($s->fee_amount, 0) : '—' }}
                        </td>
                        <td class="px-5 py-3" style="color:#6b7a72;">{{ $s->created_at->format('M j, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4" style="border-top:1px solid #e6e0d0;">
        {{ $sessions->links() }}
    </div>
</div>
@endsection
