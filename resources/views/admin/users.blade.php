@extends('admin.layout')

@section('title', 'Users')

@section('content')

@if (session('status'))
    <div class="mb-5 px-5 py-3 rounded-xl text-sm font-medium" style="background:rgba(196,154,60,.1); border:1px solid rgba(196,154,60,.3); color:#8a6a1a;">
        {{ session('status') }}
    </div>
@endif

{{-- Pending approval alert --}}
@if ($pendingApprovalCount > 0)
    <div class="mb-5 flex items-center justify-between px-5 py-3 rounded-xl text-sm font-medium"
         style="background:#fff7ed; border:1px solid #fdba74; color:#c2410c;">
        <span>⏳ <strong>{{ $pendingApprovalCount }}</strong> mentor profile(s) awaiting approval</span>
        <a href="{{ request()->fullUrlWithQuery(['pending_approval' => 1, 'role' => 'mentor']) }}"
           style="color:#c2410c; text-decoration:underline; font-weight:700;">View pending →</a>
    </div>
@endif

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search name or email…"
           class="rounded-xl px-3 py-2 text-sm"
           style="border:1px solid #d6cfbe; color:#1a3327;"
           onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'">
    <select name="role" class="rounded-xl px-3 py-2 text-sm"
            style="border:1px solid #d6cfbe; color:#1a3327;"
            onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'">
        <option value="">All roles</option>
        <option value="mentor"  @selected(request('role') === 'mentor')>Mentors</option>
        <option value="mentee"  @selected(request('role') === 'mentee')>Mentees</option>
        <option value="admin"   @selected(request('role') === 'admin')>Admins</option>
    </select>
    <label class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm cursor-pointer"
           style="border:1px solid #d6cfbe; color:#1a3327;">
        <input type="checkbox" name="pending_approval" value="1" @checked(request('pending_approval'))>
        Pending approval only
    </label>
    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-bold transition"
            style="background:#1a3327; color:#f4f1e8;"
            onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
        Filter
    </button>
    <a href="{{ route('admin.users') }}" class="px-4 py-2 rounded-xl text-sm transition"
       style="background:#ede9de; color:#4a5e55;"
       onmouseover="this.style.background='#d6cfbe'" onmouseout="this.style.background='#ede9de'">Reset</a>
</form>

<div class="rounded-2xl overflow-hidden" style="background:white; border:1px solid #e6e0d0;">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs uppercase tracking-wide" style="color:#6b7a72; background:#ede9de; border-bottom:1px solid #e6e0d0;">
                <tr>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Role</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Joined</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr style="border-bottom:1px solid #f4f1e8;">
                        <td class="px-5 py-3 font-medium" style="color:#1a3327;">
                            {{ $user->name }}
                            @if ($user->isMentor() && $user->mentorProfile)
                                @if (! $user->mentorProfile->is_approved)
                                    <span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold" style="background:#fff7ed; border:1px solid #fdba74; color:#c2410c;">Pending</span>
                                @else
                                    <span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold" style="background:#f0fdf4; border:1px solid #86efac; color:#15803d;">Approved</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-5 py-3" style="color:#6b7a72;">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            @php
                                $roleStyle = match($user->role) {
                                    'admin'  => 'background:#fff0f0; border:1px solid #fca5a5; color:#dc2626;',
                                    'mentor' => 'background:rgba(26,51,39,.08); border:1px solid #2d5240; color:#1a3327;',
                                    'mentee' => 'background:rgba(196,154,60,.1); border:1px solid rgba(196,154,60,.3); color:#8a6a1a;',
                                    default  => 'background:#ede9de; color:#4a5e55;',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="{{ $roleStyle }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if ($user->isActive())
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background:#f0fdf4; border:1px solid #86efac; color:#15803d;">Active</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background:#fff0f0; border:1px solid #fca5a5; color:#dc2626;">Suspended</span>
                            @endif
                        </td>
                        <td class="px-5 py-3" style="color:#6b7a72;">{{ $user->created_at->format('M j, Y') }}</td>
                        <td class="px-5 py-3">
                            <div class="flex flex-wrap items-center gap-3">
                                @unless ($user->id === auth()->id())
                                    {{-- Suspend / Activate --}}
                                    @if ($user->isActive())
                                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="inline"
                                              onsubmit="return confirm('Suspend {{ addslashes($user->name) }}?')">
                                            @csrf @method('PATCH')
                                            <button class="text-xs font-medium" style="color:#dc2626;">Suspend</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="text-xs font-medium" style="color:#15803d;">Activate</button>
                                        </form>
                                    @endif

                                    {{-- Approve / Reject mentor --}}
                                    @if ($user->isMentor() && $user->mentorProfile)
                                        @if (! $user->mentorProfile->is_approved)
                                            <form method="POST" action="{{ route('admin.users.approve', $user) }}" class="inline">
                                                @csrf @method('PATCH')
                                                <button class="text-xs font-bold px-2 py-0.5 rounded transition"
                                                        style="background:rgba(26,51,39,.1); color:#1a3327;"
                                                        onmouseover="this.style.background='rgba(26,51,39,.2)'" onmouseout="this.style.background='rgba(26,51,39,.1)'">
                                                    ✓ Approve
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.reject', $user) }}" class="inline"
                                                  onsubmit="return confirm('Hide {{ addslashes($user->name) }} from discovery?')">
                                                @csrf @method('PATCH')
                                                <button class="text-xs font-bold px-2 py-0.5 rounded transition"
                                                        style="background:#fff0f0; color:#dc2626;"
                                                        onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fff0f0'">
                                                    ✕ Revoke
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    {{-- Impersonate --}}
                                    @unless ($user->isAdmin())
                                        <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="inline"
                                              onsubmit="return confirm('Impersonate {{ addslashes($user->name) }}?')">
                                            @csrf
                                            <button class="text-xs font-bold px-2 py-0.5 rounded transition"
                                                    style="background:rgba(124,58,237,.1); color:#7c3aed;"
                                                    onmouseover="this.style.background='rgba(124,58,237,.2)'" onmouseout="this.style.background='rgba(124,58,237,.1)'">
                                                👤 Impersonate
                                            </button>
                                        </form>
                                    @endunless
                                @endunless
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4" style="border-top:1px solid #e6e0d0;">
        {{ $users->links() }}
    </div>
</div>
@endsection
