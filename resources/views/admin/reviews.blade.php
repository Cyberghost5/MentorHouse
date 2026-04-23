@extends('admin.layout')

@section('title', 'Reviews')

@section('content')
<div class="rounded-2xl overflow-hidden" style="background:white; border:1px solid #e6e0d0;">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs uppercase tracking-wide" style="color:#6b7a72; background:#ede9de; border-bottom:1px solid #e6e0d0;">
                <tr>
                    <th class="px-5 py-3 text-left">#</th>
                    <th class="px-5 py-3 text-left">Reviewer</th>
                    <th class="px-5 py-3 text-left">Reviewee</th>
                    <th class="px-5 py-3 text-left">Rating</th>
                    <th class="px-5 py-3 text-left">Comment</th>
                    <th class="px-5 py-3 text-left">Date</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reviews as $review)
                    <tr style="border-bottom:1px solid #f4f1e8;">
                        <td class="px-5 py-3" style="color:#6b7a72;">{{ $review->id }}</td>
                        <td class="px-5 py-3 font-medium" style="color:#1a3327;">{{ $review->reviewer->name }}</td>
                        <td class="px-5 py-3" style="color:#4a5e55;">{{ $review->reviewee->name }}</td>
                        <td class="px-5 py-3">
                            <span style="color:#c49a3c;">
                                {{ str_repeat('★', $review->rating) }}<span style="color:#d6cfbe;">{{ str_repeat('★', 5 - $review->rating) }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-3 max-w-xs truncate" style="color:#6b7a72;">{{ $review->comment ?? '—' }}</td>
                        <td class="px-5 py-3" style="color:#6b7a72;">{{ $review->created_at->format('M j, Y') }}</td>
                        <td class="px-5 py-3">
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                  onsubmit="return confirm('Delete this review?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs font-medium" style="color:#dc2626;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4" style="border-top:1px solid #e6e0d0;">
        {{ $reviews->links() }}
    </div>
</div>
@endsection
