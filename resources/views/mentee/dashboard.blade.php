<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl" style="color:#1a3327;">Mentee Dashboard</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @php $user = auth()->user(); @endphp

            {{-- Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl p-6" style="background:white; border:1px solid #e6e0d0;">
                    <p class="text-xs font-bold uppercase tracking-wide" style="color:#6b7a72;">My Requests</p>
                    <p class="mt-1 text-3xl font-black" style="color:#1a3327;">
                        {{ \App\Models\SessionRequest::where('mentee_id', $user->id)->count() }}
                    </p>
                    <a href="{{ route('session-requests.index') }}" class="text-xs font-semibold mt-1 inline-block transition" style="color:#1a3327;" onmouseover="this.style.color='#c49a3c'" onmouseout="this.style.color='#1a3327'">View all →</a>
                </div>
                <div class="rounded-2xl p-6" style="background:white; border:1px solid #e6e0d0;">
                    <p class="text-xs font-bold uppercase tracking-wide" style="color:#6b7a72;">Accepted</p>
                    <p class="mt-1 text-3xl font-black" style="color:#c49a3c;">
                        {{ \App\Models\SessionRequest::where('mentee_id', $user->id)->where('status','accepted')->count() }}
                    </p>
                </div>
                <div class="rounded-2xl p-6" style="background:white; border:1px solid #e6e0d0;">
                    <p class="text-xs font-bold uppercase tracking-wide" style="color:#6b7a72;">Completed</p>
                    <p class="mt-1 text-3xl font-black" style="color:#1a3327;">
                        {{ \App\Models\SessionRequest::where('mentee_id', $user->id)->where('status','completed')->count() }}
                    </p>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('mentors.index') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-bold transition"
                   style="background:#1a3327; color:#f4f1e8;"
                   onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
                    🔍 Find a Mentor
                </a>
                <a href="{{ route('session-requests.index') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-bold transition"
                   style="background:white; border:1px solid #d6cfbe; color:#1a3327;"
                   onmouseover="this.style.borderColor='#1a3327'" onmouseout="this.style.borderColor='#d6cfbe'">
                    📋 My Sessions
                </a>
                <a href="{{ route('messages.index') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-bold transition"
                   style="background:white; border:1px solid #d6cfbe; color:#1a3327;"
                   onmouseover="this.style.borderColor='#1a3327'" onmouseout="this.style.borderColor='#d6cfbe'">
                    💬 Messages
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
