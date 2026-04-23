<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'skill')]
    public string $skill = '';

    #[Url(as: 'type')]
    public string $sessionType = '';

    public function updatedSearch(): void  { $this->resetPage(); }
    public function updatedSkill(): void   { $this->resetPage(); }
    public function updatedSessionType(): void { $this->resetPage(); }

    public function with(): array
    {
        $query = User::query()
            ->where('role', 'mentor')
            ->with('mentorProfile')
            ->whereHas('mentorProfile', fn ($q) => $q->where('availability', 'open'));

        if ($this->search !== '') {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('headline', 'like', $term);
            });
        }

        if ($this->skill !== '') {
            $query->whereHas('mentorProfile', fn ($q) =>
                $q->whereJsonContains('expertise', $this->skill)
            );
        }

        if ($this->sessionType !== '') {
            $query->whereHas('mentorProfile', fn ($q) =>
                $q->where('session_type', $this->sessionType)
            );
        }

        $mentors = $query->paginate(9);

        // Collect all unique skills across current result set for the filter chips
        $allSkills = User::query()
            ->where('role', 'mentor')
            ->whereHas('mentorProfile', fn ($q) => $q->where('availability', 'open'))
            ->with('mentorProfile:id,user_id,expertise')
            ->get()
            ->flatMap(fn ($u) => $u->mentorProfile?->expertise ?? [])
            ->unique()
            ->sort()
            ->values();

        return [
            'mentors'   => $mentors,
            'allSkills' => $allSkills,
        ];
    }
}; ?>
<div style="min-height:100vh; padding: 3rem 0 4rem;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Page header row --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-10">
            <div>
                <p class="text-xs font-bold tracking-widest uppercase mb-3" style="color:#c49a3c;">The Mentor Roster</p>
                <h1 class="text-4xl font-black leading-tight" style="color:#1a3327;">Find a Mentor.</h1>
                <p class="mt-2 text-base font-medium" style="color:#4a5e55;">Highest-ranked practitioners across design, tech, and business.</p>
            </div>

            {{-- Search --}}
            <div class="relative w-full sm:w-80 shrink-0">
                <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="#6b7a72" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Search skills, roles, names..."
                       style="width:100%; padding:0.75rem 1rem 0.75rem 2.75rem; background:white; border:1px solid #e6e0d0; border-radius:0.875rem; font-size:0.875rem; color:#1a3327; outline:none;"
                       onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#e6e0d0'" />
            </div>
        </div>

        {{-- Session type filter --}}
        <div class="flex flex-wrap gap-2 mb-8">
            @foreach ([''=>'All','free'=>'Free','paid'=>'Paid','project_based'=>'Project-based'] as $val => $label)
                <button wire:click="$set('sessionType', '{{ $val }}')"
                        class="px-4 py-1.5 text-xs font-bold tracking-wide uppercase rounded-full transition"
                        style="{{ $sessionType === $val
                            ? 'background:#1a3327; color:#f4f1e8; border:1px solid #1a3327;'
                            : 'background:transparent; color:#6b7a72; border:1px solid #d6cfbe;' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Results --}}
        @if ($mentors->isEmpty())
            <div class="text-center py-24">
                <svg class="mx-auto mb-4 w-12 h-12" fill="none" stroke="#6b7a72" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 20h5v-2a4 4 0 0 0-4-4h-1M9 20H4v-2a4 4 0 0 1 4-4h1m4-4a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
                </svg>
                <p class="text-lg font-bold" style="color:#1a3327;">No mentors found</p>
                <p class="text-sm mt-1" style="color:#6b7a72;">Try adjusting your filters or search term.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($mentors as $mentor)
                    <x-mentor-card :mentor="$mentor" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $mentors->links() }}
            </div>
        @endif

    </div>
</div>
