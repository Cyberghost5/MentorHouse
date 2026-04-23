<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMentorProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        // Normalise expertise tags: lowercase, unique, strip empties
        $data['expertise'] = array_values(
            array_unique(
                array_filter(
                    array_map('trim', $data['expertise']),
                    fn ($v) => $v !== ''
                )
            )
        );

        // Clear hourly_rate when session is not paid
        if ($data['session_type'] !== 'paid') {
            $data['hourly_rate'] = null;
        }

        $request->user()->mentorProfile()->updateOrCreate(
            ['user_id' => $request->user()->id],
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
