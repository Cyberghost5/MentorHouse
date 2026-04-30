<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMentorProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MentorProfileController extends Controller
{
    /**
     * Show the authenticated mentor's profile edit form.
     */
    public function edit(Request $request): View
    {
        $profile = $request->user()
            ->mentorProfile()
            ->firstOrCreate(
                ['user_id' => $request->user()->id],
                [
                    'expertise'           => [],
                    'availability'        => 'open',
                    'session_type'        => 'free',
                    'years_of_experience' => 0,
                ]
            );

        return view('mentor.profile.edit', compact('profile'));
    }

    /**
     * Persist the mentor's profile changes.
     */
    public function update(UpdateMentorProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $profile = $user->mentorProfile()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'expertise'           => [],
                'availability'        => 'open',
                'session_type'        => 'free',
                'years_of_experience' => 0,
            ]
        );

        // Normalise expertise tags: lowercase, unique, strip empties
        $data['expertise'] = array_values(
            array_unique(
                array_filter(
                    array_map('trim', $data['expertise']),
                    fn ($v) => $v !== ''
                )
            )
        );

        // Clear fee when session type is not paid.
        if ($data['session_type'] !== 'paid') {
            $data['one_time_fee'] = null;
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $user->update([
                'profile_photo' => $request->file('profile_photo')->store('mentor/profile-photos', 'public'),
            ]);
        }

        if ($request->hasFile('cover_photo')) {
            if ($profile->cover_photo) {
                Storage::disk('public')->delete($profile->cover_photo);
            }

            $data['cover_photo'] = $request->file('cover_photo')->store('mentor/cover-photos', 'public');
        }

        unset($data['profile_photo']);

        $user->mentorProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return redirect()->route('mentor.profile.edit')
            ->with('status', 'Profile updated successfully.');
    }

    /**
     * Display a public mentor profile page.
     */
    public function show(string $username): View
    {
        // username format: slug-{id}
        $id = (int) substr(strrchr($username, '-'), 1);

        $user = User::where('id', $id)
            ->where('role', 'mentor')
            ->with('mentorProfile')
            ->firstOrFail();

        $profile = $user->mentorProfile;

        return view('mentor.profile.show', compact('user', 'profile'));
    }
}
