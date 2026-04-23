@extends('admin.layout')

@section('title', 'Users')

@section('content')
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
                        <td class="px-5 py-3 font-medium" style="color:#1a3327;">{{ $user->name }}</td>
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
                            @unless ($user->id === auth()->id())
                                @if ($user->isActive())
                                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="inline"
                                          onsubmit="return confirm('Suspend {{ $user->name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-xs font-medium" style="color:#dc2626;">Suspend</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-xs font-medium" style="color:#15803d;">Activate</button>
                                    </form>
                                @endif
                            @endunless
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
