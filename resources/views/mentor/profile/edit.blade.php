<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-xl" style="color:#1a3327;">Manage Mentor Profile</h2>
            <a href="{{ route('mentors.show', auth()->user()->username) }}"
               class="px-4 py-2 rounded-xl text-sm font-bold transition"
               style="background:white; border:1px solid #d6cfbe; color:#1a3327;"
               onmouseover="this.style.borderColor='#1a3327'" onmouseout="this.style.borderColor='#d6cfbe'">
                👁 View public profile
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-6 px-5 py-3 rounded-xl text-sm font-medium" style="background:rgba(196,154,60,.1); border:1px solid rgba(196,154,60,.3); color:#8a6a1a;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('mentor.profile.update') }}" enctype="multipart/form-data"
                  class="rounded-2xl p-8 space-y-8" style="background:white; border:1px solid #e6e0d0;">
                @csrf
                @method('PUT')

                {{-- Mentor images --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="profile_photo" class="block text-sm font-semibold mb-2" style="color:#1a3327;">Profile Picture</label>
                        @if (auth()->user()->profile_photo)
                            <img src="{{ Storage::url(auth()->user()->profile_photo) }}"
                                 alt="Current profile picture"
                                 class="w-20 h-20 rounded-full object-cover mb-3"
                                 style="border:2px solid #e6e0d0;" />
                        @endif
                        <input type="file"
                               name="profile_photo"
                               id="profile_photo"
                               accept="image/png,image/jpeg,image/webp"
                               class="block w-full text-sm rounded-xl file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold"
                               style="border:1px solid #d6cfbe; color:#1a3327;" />
                        <p class="mt-1.5 text-xs" style="color:#8aab97;">Recommended: 400 &times; 400 px, square. Max 5 MB (JPG, PNG, WebP).</p>
                        @error('profile_photo')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="cover_photo" class="block text-sm font-semibold mb-2" style="color:#1a3327;">Cover Picture</label>
                        @if ($profile->cover_photo)
                            <img src="{{ Storage::url($profile->cover_photo) }}"
                                 alt="Current cover picture"
                                 class="w-full h-20 rounded-xl object-cover mb-3"
                                 style="border:2px solid #e6e0d0;" />
                        @endif
                        <input type="file"
                               name="cover_photo"
                               id="cover_photo"
                               accept="image/png,image/jpeg,image/webp"
                               class="block w-full text-sm rounded-xl file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold"
                               style="border:1px solid #d6cfbe; color:#1a3327;" />
                        <p class="mt-1.5 text-xs" style="color:#8aab97;">Recommended: 1200 &times; 400 px, landscape (3:1). Max 5 MB (JPG, PNG, WebP).</p>
                        @error('cover_photo')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Headline --}}
                <div>
                    <label for="headline" class="block text-sm font-semibold mb-2" style="color:#1a3327;">Headline <span class="font-normal" style="color:#6b7a72;">(short tagline shown on your card)</span></label>
                    <input type="text"
                           name="headline"
                           id="headline"
                           maxlength="100"
                           value="{{ old('headline', auth()->user()->headline) }}"
                           placeholder="e.g. Senior Backend Engineer · 8 yrs"
                           class="w-full rounded-xl px-4 py-2.5 text-sm transition @error('headline') border-red-400 @enderror"
                           style="border:1px solid #d6cfbe; color:#1a3327;"
                           onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'" />
                    @error('headline')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bio --}}
                <div>
                    <label for="bio" class="block text-sm font-semibold mb-2" style="color:#1a3327;">Bio <span class="font-normal" style="color:#6b7a72;">(shown on your public profile)</span></label>
                    <textarea name="bio"
                              id="bio"
                              rows="5"
                              maxlength="2000"
                              placeholder="Tell mentees about your background, what you're passionate about, and how you can help…"
                              class="w-full rounded-xl px-4 py-2.5 text-sm transition resize-none @error('bio') border-red-400 @enderror"
                              style="border:1px solid #d6cfbe; color:#1a3327;"
                              onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'">{{ old('bio', auth()->user()->bio) }}</textarea>
                    @error('bio')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Expertise / skills --}}
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color:#1a3327;">
                        Expertise <span class="font-normal" style="color:#6b7a72;">(comma-separated skills)</span>
                    </label>
                    <input type="text"
                           name="expertise_raw"
                           id="expertise_raw"
                           value="{{ old('expertise_raw', implode(', ', $profile->expertise ?? [])) }}"
                           placeholder="e.g. PHP, Laravel, System Design"
                           class="w-full rounded-xl px-4 py-2.5 text-sm transition @error('expertise') border-red-400 @enderror"
                           style="border:1px solid #d6cfbe; color:#1a3327;"
                           onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'" />
                    {{-- Hidden expertise[] inputs are generated by JS from the raw input --}}
                    <div id="expertise-hidden"></div>
                    @error('expertise')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-gray-400">Separate each skill with a comma.</p>
                </div>

                {{-- Availability --}}
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color:#1a3327;">Availability</label>
                    <div class="flex gap-4">
                        @foreach (['open' => 'Open to mentoring', 'closed' => 'Not available'] as $val => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="availability" value="{{ $val }}"
                                       {{ old('availability', $profile->availability) === $val ? 'checked' : '' }}
                                       style="accent-color:#1a3327;" />
                                <span class="text-sm" style="color:#4a5e55;">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('availability')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Session type --}}
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color:#1a3327;">Session Type</label>
                    <div class="flex flex-wrap gap-4">
                        @foreach (['free' => 'Free', 'paid' => 'Paid', 'project_based' => 'Project-based'] as $val => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="session_type" value="{{ $val }}"
                                       id="session_type_{{ $val }}"
                                       {{ old('session_type', $profile->session_type) === $val ? 'checked' : '' }}
                                       style="accent-color:#1a3327;" />
                                <span class="text-sm" style="color:#4a5e55;">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('session_type')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- One-time fee (shown only when session_type = paid) --}}
                <div id="one-time-fee-field" class="{{ old('session_type', $profile->session_type) === 'paid' ? '' : 'hidden' }}">
                    <label for="one_time_fee" class="block text-sm font-semibold mb-2" style="color:#1a3327;">Fee (₦)</label>
                    <div class="relative w-48">
                        <span class="absolute inset-y-0 left-3 flex items-center text-sm" style="color:#6b7a72;">₦</span>
                        <input type="number" name="one_time_fee" id="one_time_fee" min="0" max="9999999" step="1"
                               value="{{ old('one_time_fee', $profile->one_time_fee) }}"
                               class="w-full pl-7 pr-4 py-2.5 rounded-xl text-sm transition @error('one_time_fee') border-red-400 @enderror"
                               style="border:1px solid #d6cfbe; color:#1a3327;"
                               onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'" />
                    </div>
                    @error('one_time_fee')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Years of experience --}}
                <div>
                    <label for="years_of_experience" class="block text-sm font-semibold mb-2" style="color:#1a3327;">Years of Experience</label>
                    <input type="number" name="years_of_experience" id="years_of_experience"
                           min="0" max="50" step="1"
                           value="{{ old('years_of_experience', $profile->years_of_experience) }}"
                           class="w-32 rounded-xl px-4 py-2.5 text-sm transition @error('years_of_experience') border-red-400 @enderror"
                           style="border:1px solid #d6cfbe; color:#1a3327;"
                           onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'" />
                    @error('years_of_experience')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Country --}}
                <div>
                    <label for="country" class="block text-sm font-semibold mb-2" style="color:#1a3327;">Country</label>
                    <select name="country" id="country"
                            class="w-64 rounded-xl px-4 py-2.5 text-sm transition @error('country') border-red-400 @enderror"
                            style="border:1px solid #d6cfbe; color:#1a3327;"
                            onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'">
                        <option value="">— Select country —</option>
                        @php
                            $countries = [
                                'AF'=>'Afghanistan','AL'=>'Albania','DZ'=>'Algeria','AD'=>'Andorra','AO'=>'Angola',
                                'AG'=>'Antigua & Barbuda','AR'=>'Argentina','AM'=>'Armenia','AU'=>'Australia',
                                'AT'=>'Austria','AZ'=>'Azerbaijan','BS'=>'Bahamas','BH'=>'Bahrain','BD'=>'Bangladesh',
                                'BB'=>'Barbados','BY'=>'Belarus','BE'=>'Belgium','BZ'=>'Belize','BJ'=>'Benin',
                                'BT'=>'Bhutan','BO'=>'Bolivia','BA'=>'Bosnia & Herzegovina','BW'=>'Botswana',
                                'BR'=>'Brazil','BN'=>'Brunei','BG'=>'Bulgaria','BF'=>'Burkina Faso','BI'=>'Burundi',
                                'CV'=>'Cabo Verde','KH'=>'Cambodia','CM'=>'Cameroon','CA'=>'Canada',
                                'CF'=>'Central African Republic','TD'=>'Chad','CL'=>'Chile','CN'=>'China',
                                'CO'=>'Colombia','KM'=>'Comoros','CG'=>'Congo','CD'=>'Congo (DRC)',
                                'CR'=>'Costa Rica','HR'=>'Croatia','CU'=>'Cuba','CY'=>'Cyprus','CZ'=>'Czechia',
                                'DK'=>'Denmark','DJ'=>'Djibouti','DM'=>'Dominica','DO'=>'Dominican Republic',
                                'EC'=>'Ecuador','EG'=>'Egypt','SV'=>'El Salvador','GQ'=>'Equatorial Guinea',
                                'ER'=>'Eritrea','EE'=>'Estonia','SZ'=>'Eswatini','ET'=>'Ethiopia','FJ'=>'Fiji',
                                'FI'=>'Finland','FR'=>'France','GA'=>'Gabon','GM'=>'Gambia','GE'=>'Georgia',
                                'DE'=>'Germany','GH'=>'Ghana','GR'=>'Greece','GD'=>'Grenada','GT'=>'Guatemala',
                                'GN'=>'Guinea','GW'=>'Guinea-Bissau','GY'=>'Guyana','HT'=>'Haiti',
                                'HN'=>'Honduras','HU'=>'Hungary','IS'=>'Iceland','IN'=>'India','ID'=>'Indonesia',
                                'IR'=>'Iran','IQ'=>'Iraq','IE'=>'Ireland','IL'=>'Israel','IT'=>'Italy',
                                'JM'=>'Jamaica','JP'=>'Japan','JO'=>'Jordan','KZ'=>'Kazakhstan','KE'=>'Kenya',
                                'KI'=>'Kiribati','KW'=>'Kuwait','KG'=>'Kyrgyzstan','LA'=>'Laos','LV'=>'Latvia',
                                'LB'=>'Lebanon','LS'=>'Lesotho','LR'=>'Liberia','LY'=>'Libya','LI'=>'Liechtenstein',
                                'LT'=>'Lithuania','LU'=>'Luxembourg','MG'=>'Madagascar','MW'=>'Malawi',
                                'MY'=>'Malaysia','MV'=>'Maldives','ML'=>'Mali','MT'=>'Malta','MH'=>'Marshall Islands',
                                'MR'=>'Mauritania','MU'=>'Mauritius','MX'=>'Mexico','FM'=>'Micronesia',
                                'MD'=>'Moldova','MC'=>'Monaco','MN'=>'Mongolia','ME'=>'Montenegro',
                                'MA'=>'Morocco','MZ'=>'Mozambique','MM'=>'Myanmar','NA'=>'Namibia','NR'=>'Nauru',
                                'NP'=>'Nepal','NL'=>'Netherlands','NZ'=>'New Zealand','NI'=>'Nicaragua',
                                'NE'=>'Niger','NG'=>'Nigeria','NO'=>'Norway','OM'=>'Oman','PK'=>'Pakistan',
                                'PW'=>'Palau','PA'=>'Panama','PG'=>'Papua New Guinea','PY'=>'Paraguay',
                                'PE'=>'Peru','PH'=>'Philippines','PL'=>'Poland','PT'=>'Portugal','QA'=>'Qatar',
                                'RO'=>'Romania','RU'=>'Russia','RW'=>'Rwanda','KN'=>'Saint Kitts & Nevis',
                                'LC'=>'Saint Lucia','VC'=>'Saint Vincent & the Grenadines','WS'=>'Samoa',
                                'SM'=>'San Marino','ST'=>'São Tomé & Príncipe','SA'=>'Saudi Arabia',
                                'SN'=>'Senegal','RS'=>'Serbia','SC'=>'Seychelles','SL'=>'Sierra Leone',
                                'SG'=>'Singapore','SK'=>'Slovakia','SI'=>'Slovenia','SB'=>'Solomon Islands',
                                'SO'=>'Somalia','ZA'=>'South Africa','SS'=>'South Sudan','ES'=>'Spain',
                                'LK'=>'Sri Lanka','SD'=>'Sudan','SR'=>'Suriname','SE'=>'Sweden',
                                'CH'=>'Switzerland','SY'=>'Syria','TW'=>'Taiwan','TJ'=>'Tajikistan',
                                'TZ'=>'Tanzania','TH'=>'Thailand','TL'=>'Timor-Leste','TG'=>'Togo',
                                'TO'=>'Tonga','TT'=>'Trinidad & Tobago','TN'=>'Tunisia','TR'=>'Turkey',
                                'TM'=>'Turkmenistan','TV'=>'Tuvalu','UG'=>'Uganda','UA'=>'Ukraine',
                                'AE'=>'United Arab Emirates','GB'=>'United Kingdom','US'=>'United States',
                                'UY'=>'Uruguay','UZ'=>'Uzbekistan','VU'=>'Vanuatu','VE'=>'Venezuela',
                                'VN'=>'Vietnam','YE'=>'Yemen','ZM'=>'Zambia','ZW'=>'Zimbabwe',
                            ];
                            asort($countries);
                        @endphp
                        @foreach ($countries as $code => $name)
                            <option value="{{ $code }}" @selected(old('country', $profile->country) === $code)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('country')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end pt-2" style="border-top:1px solid #e6e0d0;">
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl text-sm font-bold transition"
                            style="background:#1a3327; color:#f4f1e8;"
                            onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
                        Save Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Convert comma-separated expertise to hidden inputs on submit
        document.querySelector('form').addEventListener('submit', function () {
            const raw = document.getElementById('expertise_raw').value;
            const container = document.getElementById('expertise-hidden');
            container.innerHTML = '';
            raw.split(',')
               .map(s => s.trim())
               .filter(s => s !== '')
               .forEach(skill => {
                   const input = document.createElement('input');
                   input.type  = 'hidden';
                   input.name  = 'expertise[]';
                   input.value = skill;
                   container.appendChild(input);
               });
        });

        // Show/hide one-time fee field based on session type selection
        document.querySelectorAll('input[name="session_type"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                document.getElementById('one-time-fee-field').classList.toggle('hidden', this.value !== 'paid');
            });
        });
    </script>
</x-app-layout>
