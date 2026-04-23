@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
        $cards = [
            ['label' => 'Total Users',    'value' => $stats['total_users'],    'style' => 'color:#1a3327;'],
            ['label' => 'Mentors',        'value' => $stats['total_mentors'],  'style' => 'color:#1a3327;'],
            ['label' => 'Mentees',        'value' => $stats['total_mentees'],  'style' => 'color:#1a3327;'],
            ['label' => 'Total Sessions', 'value' => $stats['total_sessions'], 'style' => 'color:#1a3327;'],
            ['label' => 'Completed',      'value' => $stats['completed'],      'style' => 'color:#1a3327;'],
            ['label' => 'Pending',        'value' => $stats['pending'],        'style' => 'color:#c49a3c;'],
            ['label' => 'Total Reviews',  'value' => $stats['total_reviews'],  'style' => 'color:#1a3327;'],
            ['label' => 'Total Revenue',  'value' => '₦' . number_format($stats['total_revenue'], 2), 'style' => 'color:#c49a3c;'],
        ];
    @endphp
    @foreach ($cards as $card)
        <div class="rounded-2xl p-5" style="background:white; border:1px solid #e6e0d0;">
            <p class="text-xs font-medium uppercase tracking-wide" style="color:#6b7a72;">{{ $card['label'] }}</p>
            <p class="mt-1 text-2xl font-black" style="{{ $card['style'] }}">{{ $card['value'] }}</p>
        </div>
    @endforeach
</div>

<div class="rounded-2xl p-6" style="background:white; border:1px solid #e6e0d0;">
    <h2 class="font-black mb-4" style="color:#1a3327;">Recent Sessions</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs uppercase tracking-wide" style="color:#6b7a72; border-bottom:1px solid #e6e0d0;">
                <tr>
                    <th class="pb-3 text-left">Mentee</th>
                    <th class="pb-3 text-left">Mentor</th>
                    <th class="pb-3 text-left">Status</th>
                    <th class="pb-3 text-left">Type</th>
                    <th class="pb-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentSessions as $s)
                    <tr style="border-bottom:1px solid #f4f1e8;">
                        <td class="py-3 font-medium" style="color:#1a3327;">{{ $s->mentee->name }}</td>
                        <td class="py-3" style="color:#4a5e55;">{{ $s->mentor->name }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $s->statusColor() }}">
                                {{ $s->statusLabel() }}
                            </span>
                        </td>
                        <td class="py-3" style="color:#6b7a72;">{{ ucfirst(str_replace('_', ' ', $s->session_type)) }}</td>
                        <td class="py-3" style="color:#6b7a72;">{{ $s->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
