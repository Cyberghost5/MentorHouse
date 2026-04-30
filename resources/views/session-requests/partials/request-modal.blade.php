{{--
  Reusable session-request modal.
  Include on any page that has $user (mentor) and $profile (MentorProfile) in scope.
  Requires Alpine.js (shipped with Breeze).
--}}

<div x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }" @keydown.escape.window="open = false">

    @php
        $hasPendingRequest = auth()->check()
            && auth()->user()->isMentee()
            && \App\Models\SessionRequest::where('mentee_id', auth()->id())
                ->where('mentor_id', $user->id)
                ->where('status', \App\Models\SessionRequest::STATUS_PENDING)
                ->exists();
    @endphp

    {{-- Trigger button --}}
    @auth
        @if (auth()->user()->isMentee())
            @if ($profile?->isOpen())
                @if ($hasPendingRequest)
                    <button disabled
                            class="w-full text-center bg-gray-200 text-gray-500 font-semibold rounded-xl py-2.5 cursor-not-allowed">
                        Request Pending
                    </button>
                    <p class="mt-2 text-xs" style="color:#8aab97;">You already have a pending request with this mentor.</p>
                @else
                    <button @click="open = true"
                            class="w-full text-center font-bold rounded-xl py-2.5 transition"
                            style="background:#c49a3c; color:#1a3327;"
                            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        Request Session
                    </button>
                @endif
            @else
                <button disabled
                        class="w-full text-center bg-gray-200 text-gray-400 font-semibold rounded-xl py-2.5 cursor-not-allowed">
                    Not Available
                </button>
            @endif
        @endif
    @else
        <a href="{{ route('login') }}"
           class="block w-full text-center font-bold rounded-xl py-2.5 transition"
           style="background:#1a3327; color:#f4f1e8;"
           onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
            Log in to Request Session
        </a>
    @endauth

    {{-- Modal backdrop --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black/50 flex items-center justify-center p-4"
         @click.self="open = false"
         style="display: none;">

        {{-- Modal panel --}}
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-y-auto max-h-[90vh]"
             @click.stop>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-black" style="color:#1a3327;">Request a Session with {{ $user->name }}</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('session-requests.store', $user->id) }}" class="p-6 space-y-5">
                @csrf

                <input type="hidden" name="mentor_id" value="{{ $user->id }}" />
                <input type="hidden" name="session_type" value="{{ $profile->session_type }}" />

                {{-- Session type info banner --}}
                @php
                    $typeInfo = match($profile->session_type) {
                        'free'          => ['label' => 'Free Session', 'color' => 'bg-green-50 text-green-700 border-green-200'],
                        'paid'          => ['label' => 'Paid · Fee ₦' . number_format($profile->one_time_fee, 0), 'color' => 'bg-amber-50 text-amber-700 border-amber-200'],
                        'project_based' => ['label' => 'Project-based', 'color' => 'bg-blue-50 text-blue-700 border-blue-200'],
                        default         => ['label' => ucfirst($profile->session_type), 'color' => 'bg-gray-50 text-gray-600 border-gray-200'],
                    };
                @endphp
                <div class="px-4 py-2.5 rounded-xl border text-sm font-medium {{ $typeInfo['color'] }}">
                    {{ $typeInfo['label'] }}
                </div>

                {{-- Proposed date --}}
                <div>
                    <label for="proposed_date" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Proposed Date & Time <span class="text-red-500">*</span>
                    </label>
                <input type="datetime-local"
                           name="proposed_date"
                           id="proposed_date"
                           min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                           value="{{ old('proposed_date') }}"
                           required
                           class="w-full rounded-xl px-4 py-2.5 text-sm transition @error('proposed_date') border-red-400 @enderror"
                           style="border:1px solid #d6cfbe; color:#1a3327;"
                           onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'" />
                    @error('proposed_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Message (free + paid) --}}
                @if (in_array($profile->session_type, ['free', 'paid']))
                    <div>
                        <label for="message" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Message <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <textarea name="message"
                                  id="message"
                                  rows="4"
                                  maxlength="2000"
                                  placeholder="What do you want to work on? What's your current challenge?"
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                {{-- Project description (project_based) --}}
                @if ($profile->session_type === 'project_based')
                    <div>
                        <label for="project_description" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Project Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="project_description"
                                  id="project_description"
                                  rows="5"
                                  maxlength="5000"
                                  placeholder="Describe your project, goals, and what help you need…"
                                  required
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none @error('project_description') border-red-400 @enderror">{{ old('project_description') }}</textarea>
                        @error('project_description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-700 mb-1.5 mt-4">
                                Additional Message <span class="text-gray-400 font-normal">(optional)</span>
                            </label>
                            <textarea name="message" id="message" rows="3"
                                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none">{{ old('message') }}</textarea>
                        </div>
                    </div>
                @endif

                {{-- Fee note for paid --}}
                @if ($profile->session_type === 'paid' && $profile->one_time_fee)
                    <input type="hidden" name="fee_amount" value="{{ $profile->one_time_fee }}" />
                    <p class="text-xs text-gray-500 bg-gray-50 rounded-xl px-4 py-3">
                        💳 The one-time fee of <strong>₦{{ number_format($profile->one_time_fee, 0) }}</strong> is set by the mentor.
                        Payment details will be shared once the request is accepted.
                    </p>
                @endif

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="open = false"
                            class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl text-sm font-bold transition"
                            style="background:#1a3327; color:#f4f1e8;"
                            onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
                        Send Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
