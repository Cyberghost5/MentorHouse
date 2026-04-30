<?php

namespace Database\Seeders;

use App\Models\MentorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Nigerian Tech Mentors ──────────────────────────────────────────────

        $mentors = [
            [
                'user' => [
                    'name'     => 'Prosper Otemuyiwa',
                    'email'    => 'prosper@mentorhouse.test',
                    'headline' => 'Developer Experience Engineer · unicodeveloper',
                    'bio'      => 'I am a Developer Experience Engineer and prolific open-source contributor known as @unicodeveloper. I have built developer tools and APIs used by thousands of engineers across Africa and the world, and I am passionate about growing the next generation of African software developers.',
                ],
                'profile' => [
                    'expertise'           => ['JavaScript', 'Node.js', 'Laravel', 'Developer Experience', 'API Design'],
                    'availability'        => 'open',
                    'session_type'        => 'free',
                    'one_time_fee'        => null,
                    'years_of_experience' => 12,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Ire Aderinokun',
                    'email'    => 'ire@mentorhouse.test',
                    'headline' => 'VP Engineering · Frontend & CSS Expert',
                    'bio'      => 'I am a frontend developer and co-founder of BuyCoins, Nigeria\'s leading cryptocurrency exchange. I write extensively about CSS, JavaScript, and accessibility, and I love helping developers master the fundamentals of the web platform.',
                ],
                'profile' => [
                    'expertise'           => ['CSS', 'JavaScript', 'React', 'Web Accessibility', 'Frontend Architecture'],
                    'availability'        => 'open',
                    'session_type'        => 'free',
                    'one_time_fee'        => null,
                    'years_of_experience' => 9,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Shola Akinlade',
                    'email'    => 'shola@mentorhouse.test',
                    'headline' => 'Co-founder & CEO, Paystack (acquired by Stripe)',
                    'bio'      => 'I co-founded Paystack to simplify payments for African businesses. After Stripe acquired Paystack in 2020, I have continued scaling payments infrastructure across the continent. I mentor founders and product builders navigating fintech and B2B SaaS.',
                ],
                'profile' => [
                    'expertise'           => ['Fintech', 'Product Strategy', 'Startups', 'Payments Infrastructure', 'Fundraising'],
                    'availability'        => 'open',
                    'session_type'        => 'paid',
                    'one_time_fee'        => 85000,
                    'years_of_experience' => 14,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Iyin Aboyeji',
                    'email'    => 'iyin@mentorhouse.test',
                    'headline' => 'Co-founder Flutterwave & Andela · Partner, Future Africa',
                    'bio'      => 'I co-founded both Andela and Flutterwave — two of Africa\'s most recognised technology companies. I now run Future Africa, investing in African tech founders. I enjoy mentoring early-stage entrepreneurs building category-defining companies on the continent.',
                ],
                'profile' => [
                    'expertise'           => ['Entrepreneurship', 'Fundraising', 'Venture Capital', 'Team Building', 'Product-Market Fit'],
                    'availability'        => 'open',
                    'session_type'        => 'paid',
                    'one_time_fee'        => 120000,
                    'years_of_experience' => 13,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Mark Essien',
                    'email'    => 'mark@mentorhouse.test',
                    'headline' => 'Founder & CTO · Hotels.ng',
                    'bio'      => 'I founded Hotels.ng, Nigeria\'s largest hotel booking platform. I write about engineering management, distributed systems, and growing tech companies in emerging markets. My mentorship sessions focus on technical architecture and CTO-track career paths.',
                ],
                'profile' => [
                    'expertise'           => ['Software Architecture', 'Engineering Management', 'PHP', 'System Design', 'Technical Leadership'],
                    'availability'        => 'open',
                    'session_type'        => 'paid',
                    'one_time_fee'        => 60000,
                    'years_of_experience' => 15,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Timi Ajiboye',
                    'email'    => 'timi@mentorhouse.test',
                    'headline' => 'Co-founder & CEO · BuyCoins',
                    'bio'      => 'I co-founded BuyCoins, the crypto exchange behind the Yellow Card ecosystem. I spend my time thinking deeply about product, cryptocurrency, and what it means to build truly user-centric financial products for Africans.',
                ],
                'profile' => [
                    'expertise'           => ['Blockchain', 'Cryptocurrency', 'Product Management', 'React Native', 'Fintech'],
                    'availability'        => 'open',
                    'session_type'        => 'paid',
                    'one_time_fee'        => 75000,
                    'years_of_experience' => 10,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Adewale Yusuf',
                    'email'    => 'adewale@mentorhouse.test',
                    'headline' => 'Founder & CEO · AltSchool Africa',
                    'bio'      => 'I founded AltSchool Africa to give ambitious Africans access to world-class, practical tech education. Before AltSchool I built and invested in several startups. I am passionate about EdTech, career transitions into tech, and product-led growth.',
                ],
                'profile' => [
                    'expertise'           => ['EdTech', 'Career Development', 'Product-Led Growth', 'Startups', 'Frontend Development'],
                    'availability'        => 'open',
                    'session_type'        => 'free',
                    'one_time_fee'        => null,
                    'years_of_experience' => 11,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Elvis Chidera',
                    'email'    => 'elvis@mentorhouse.test',
                    'headline' => 'Senior Software Engineer · Android & Mobile',
                    'bio'      => 'I am a senior software engineer with deep expertise in Android and cross-platform mobile development. I have shipped features used by millions of users and I enjoy breaking down complex engineering problems into clear, teachable patterns for junior engineers.',
                ],
                'profile' => [
                    'expertise'           => ['Android', 'Kotlin', 'Flutter', 'Mobile Architecture', 'Java'],
                    'availability'        => 'open',
                    'session_type'        => 'free',
                    'one_time_fee'        => null,
                    'years_of_experience' => 8,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Fikayo Adepoju',
                    'email'    => 'fikayo@mentorhouse.test',
                    'headline' => 'Developer Advocate · Full-Stack Engineer',
                    'bio'      => 'I am a full-stack engineer and Developer Advocate who has written tutorials and built courses for thousands of developers worldwide. I specialise in Vue.js, React, and Node.js, and I am always happy to help developers level up through structured 1-on-1 sessions.',
                ],
                'profile' => [
                    'expertise'           => ['Vue.js', 'React', 'Node.js', 'GraphQL', 'Technical Writing'],
                    'availability'        => 'open',
                    'session_type'        => 'free',
                    'one_time_fee'        => null,
                    'years_of_experience' => 7,
                ],
            ],
        ];

        foreach ($mentors as $data) {
            $user = User::factory()->mentor()->create(array_merge($data['user'], [
                'password' => Hash::make('password'),
            ]));

            MentorProfile::create(array_merge(['user_id' => $user->id], $data['profile']));
        }

        // ── Mentees ────────────────────────────────────────────────────────────
        User::factory()->mentee()->create([
            'name'     => 'Chioma Obi',
            'email'    => 'chioma@mentorhouse.test',
            'password' => Hash::make('password'),
            'headline' => 'CS Graduate · Lagos State University',
            'bio'      => 'Frontend developer looking to break into product engineering at a top startup.',
        ]);

        User::factory()->mentee()->create([
            'name'     => 'Emeka Nwosu',
            'email'    => 'emeka@mentorhouse.test',
            'password' => Hash::make('password'),
            'headline' => 'Self-taught Developer',
            'bio'      => 'Self-taught JavaScript developer seeking guidance on transitioning to a full-time engineering role.',
        ]);
    }
}
