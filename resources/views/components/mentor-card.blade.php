@props(['mentor'])

@php
    $profile  = $mentor->mentorProfile;
    $initials = collect(explode(' ', $mentor->name))
        ->take(2)
        ->map(fn ($w) => strtoupper($w[0]))
        ->implode('');

    // Access protocol display
    if ($profile?->session_type === 'paid' && $profile?->hourly_rate) {
        $accessLabel = '₦' . number_format($profile->hourly_rate, 0);
    } elseif ($profile?->session_type === 'project_based') {
        $accessLabel = 'Exam Required';
    } else {
        $accessLabel = 'Free';
    }

    // Avatar background colours (cycle through a set)
    $colours = ['#2d5240','#1a3327','#4a7c5a','#355c3e','#213d2e'];
    $bg = $colours[abs(crc32($mentor->name)) % count($colours)];
@endphp

<div style="background:white; border-radius:1rem; padding:1.5rem; display:flex; flex-direction:column; gap:0; box-shadow:0 1px 3px rgba(0,0,0,.06); border:1px solid #e6e0d0;">
    {{-- Avatar + name --}}
    <div style="display:flex; align-items:flex-start; gap:1rem;">
        <div style="position:relative; flex-shrink:0;">
            @if ($mentor->profile_photo)
                <img src="{{ Storage::url($mentor->profile_photo) }}"
                     alt="{{ $mentor->name }}"
                     style="width:72px; height:72px; border-radius:50%; object-fit:cover;" />
            @else
                <div style="width:72px; height:72px; border-radius:50%; background:{{ $bg }}; display:flex; align-items:center; justify-content:center; font-size:1.25rem; font-weight:800; color:#f4f1e8;">
                    {{ $initials }}
                </div>
            @endif
            {{-- Gold star badge --}}
            <span style="position:absolute; bottom:-2px; right:-2px; width:22px; height:22px; border-radius:50%; background:#c49a3c; display:flex; align-items:center; justify-content:center; font-size:11px; color:white; border:2px solid white;">🌟</span>
        </div>

        <div style="min-width:0;">
            <h3 style="font-size:1.125rem; font-weight:900; color:#1a3327; line-height:1.2;">{{ $mentor->name }}</h3>
            @if ($mentor->headline)
                <p style="font-size:0.8125rem; color:#6b7a72; margin-top:2px;">{{ $mentor->headline }}</p>
            @endif
        </div>
    </div>

    {{-- Bio --}}
    @if ($mentor->bio)
        <p style="margin-top:1rem; font-size:0.875rem; color:#4a5e55; line-height:1.6; display:-webkit-box; -webkit-line-clamp:2; line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">{{ $mentor->bio }}</p>
    @endif

    {{-- Skills --}}
    @if ($profile && !empty($profile->expertise))
        <div style="margin-top:1rem; display:flex; flex-wrap:wrap; gap:0.375rem;">
            @foreach (array_slice($profile->expertise, 0, 4) as $skill)
                <span style="padding:0.25rem 0.75rem; border-radius:999px; font-size:0.6875rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; color:#6b7a72; border:1px solid #d6cfbe;">
                    {{ $skill }}
                </span>
            @endforeach
            @if (count($profile->expertise) > 4)
                <span style="padding:0.25rem 0.75rem; border-radius:999px; font-size:0.6875rem; font-weight:700; color:#6b7a72; border:1px solid #d6cfbe;">
                    +{{ count($profile->expertise) - 4 }}
                </span>
            @endif
        </div>
    @endif

    {{-- Divider --}}
    <hr style="border:none; border-top:1px solid #e6e0d0; margin:1.25rem 0 1rem;">

    {{-- Access Protocol + CTA --}}
    <div style="display:flex; align-items:flex-end; justify-content:space-between;">
        <div>
            <p style="font-size:0.625rem; font-weight:800; letter-spacing:0.12em; text-transform:uppercase; color:#c49a3c;">Access Protocol</p>
            <p style="font-size:1.25rem; font-weight:900; color:#1a3327; margin-top:2px; line-height:1;">{{ $accessLabel }}</p>
        </div>

        <a href="{{ route('mentors.show', $mentor->username) }}"
           style="width:44px; height:44px; border-radius:0.625rem; background:#1a3327; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:background .15s;"
           onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
            <svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
            </svg>
        </a>
    </div>

</div>
