@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('sponsor.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!---Main -->
    <main class="flex-1">
        <div class="max-w-8xl mx-auto">
            <!-- Status Cards -->
            @include('sponsor.partials.cards')

            <!-- Charts Section -->
            <div class="bg-[#F3E8EF] rounded-lg p-6 mt-10">
                <div class="flex justify-between items-center mb-2 pb-4">
                    <h2 class="text-xl font-medium text-[#213430] app-main">Our Sponsors</h2>
                    <div class="flex flex-wrap mb-2 mobile-btn">
                        <div class="w-full md:w-1/2">
                            <button onclick="showTab('individual')"
                                class="tab-btn w-full bg-[#DB69A2] text-white py-2 px-6 font-normal text-center rounded-t-lg md:rounded-tr-none md:rounded-l-lg app-text">
                                Individual
                            </button>
                        </div>
                        <div class="w-full md:w-1/2">
                            <button onclick="showTab('company')"
                                class="tab-btn w-full bg-[#F3E8EF] text-[#91848C] py-2 px-6 font-normal text-center rounded-t-lg md:rounded-tl-none md:rounded-r-lg app-text">
                                Company
                            </button>
                        </div>
                    </div>
                </div>

                <div id="tabContents">
                    <!-- Individual Sponsors Tab -->
                    <div id="individual" class="tab-content">
                        <div class="table-container">
                            <table class="min-w-full text-sm text-left mt-1 text-center">
                                <thead>
                                    <tr class="border-t border-[#e0cfd8]">
                                        <th class="p-2 text-lg text-left font-medium text-[#91848C] font-normal app-text">Name</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text pad-left">Country</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text">Sponsorships</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text">Contribution</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text">Event Type</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text">Sponsor Type</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text text-center">Duration Of Sponsorships</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @forelse ($individualSponsors as $sponsor)
                                        @php
                                            $profile = optional($sponsor->profile);
                                            $avatar = $profile && $profile->avatar ? Storage::url($profile->avatar) : asset('images/patient-7.png');
                                            $latestSponsorship = optional($sponsor->sponsorships->first());
                                            $latestProgram = optional($latestSponsorship->program);
                                            $eventType = $latestProgram->title ?? 'N/A';
                                            $firstDate = $sponsor->first_contribution_date ? Carbon::parse($sponsor->first_contribution_date) : null;
                                            $lastDate = $sponsor->last_contribution_date ? Carbon::parse($sponsor->last_contribution_date) : null;
                                            $durationLabel = 'N/A';
                                            if ($firstDate && $lastDate) {
                                                if ($firstDate->equalTo($lastDate)) {
                                                    $durationLabel = 'Less than 1 year';
                                                } else {
                                                    $years = $firstDate->diffInYears($lastDate);
                                                    if ($years >= 1) {
                                                        $durationLabel = $years . ' Year' . ($years > 1 ? 's' : '');
                                                    } else {
                                                        $months = max(1, $firstDate->diffInMonths($lastDate));
                                                        $durationLabel = $months . ' Month' . ($months > 1 ? 's' : '');
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr class="border-t border-[#e0cfd8]">
                                            <td class="p-2">
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $avatar }}" alt="{{ $profile->full_name ?? $sponsor->email }}" class="w-8 h-8 rounded-full object-cover" />
                                                    <span class="text-[#91848C] text-[16px] font-light app-text">
                                                        {{ $profile->full_name ?? $sponsor->email }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text pad-left">
                                                {{ $profile->country ?? 'N/A' }}
                                            </td>
                                            <td class="p-2 align-middle text-[#91848C] text-center text-[16px] font-light app-text">
                                                {{ $sponsor->sponsorships_count }}
                                            </td>
                                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                                ${{ number_format($sponsor->total_contribution ?? 0, 0) }}
                                            </td>
                                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                                {{ $eventType }}
                                            </td>
                                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                                Individual
                                            </td>
                                            <td class="p-2 align-middle text-center text-[#91848C] text-[16px] font-light app-text">
                                                {{ $durationLabel }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="border-t border-[#e0cfd8]">
                                            <td colspan="7" class="p-3 text-center text-[#91848C] text-[16px] font-light app-text">
                                                No individual sponsors found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Company Sponsors Tab -->
                    <div id="company" class="tab-content hidden">
                        <div class="table-container">
                            <table class="min-w-full text-sm text-left mt-1 text-center">
                                <thead>
                                    <tr class="border-t border-[#e0cfd8]">
                                        <th class="p-2 text-left text-lg font-medium text-[#91848C] font-normal app-text">Name</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text pad-left">Country</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text">Sponsorships</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text">Contribution</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text">Event Type</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text">Sponsor Type</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text">Duration Of Sponsorships</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @forelse ($companySponsors as $sponsor)
                                        @php
                                            $profile = optional($sponsor->profile);
                                            $detail = optional($sponsor->sponsorDetail);
                                            $logo = $detail && $detail->logo ? Storage::url($detail->logo) : asset('images/patient-4.png');
                                            $latestSponsorship = optional($sponsor->sponsorships->first());
                                            $latestProgram = optional($latestSponsorship->program);
                                            $eventType = $latestProgram->title ?? 'N/A';
                                            $firstDate = $sponsor->first_contribution_date ? Carbon::parse($sponsor->first_contribution_date) : null;
                                            $lastDate = $sponsor->last_contribution_date ? Carbon::parse($sponsor->last_contribution_date) : null;
                                            $durationLabel = 'N/A';
                                            if ($firstDate && $lastDate) {
                                                if ($firstDate->equalTo($lastDate)) {
                                                    $durationLabel = 'Less than 1 year';
                                                } else {
                                                    $years = $firstDate->diffInYears($lastDate);
                                                    if ($years >= 1) {
                                                        $durationLabel = $years . ' Year' . ($years > 1 ? 's' : '');
                                                    } else {
                                                        $months = max(1, $firstDate->diffInMonths($lastDate));
                                                        $durationLabel = $months . ' Month' . ($months > 1 ? 's' : '');
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr class="border-t border-[#e0cfd8]">
                                            <td class="p-2 app-text">
                                            	<div class="flex items-center gap-3">
                                                    <img src="{{ $logo }}" alt="{{ $detail->company_name ?? $sponsor->email }}" class="w-8 h-8 rounded-full object-cover" />
                                                    <span class="text-[#91848C] text-[16px] font-light app-text">
                                                        {{ $detail->company_name ?? $sponsor->email }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light pad-left">
                                                {{ $profile->country ?? 'N/A' }}
                                            </td>
                                            <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">
                                                {{ $sponsor->sponsorships_count }}
                                            </td>
                                            <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">
                                                ${{ number_format($sponsor->total_contribution ?? 0, 0) }}
                                            </td>
                                            <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">
                                                {{ $eventType }}
                                            </td>
                                            <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">
                                                Company
                                            </td>
                                            <td class="p-2 app-text align-middle text-center text-[#91848C] text-[16px] font-light">
                                                {{ $durationLabel }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="border-t border-[#e0cfd8]">
                                            <td colspan="7" class="p-3 text-center text-[#91848C] text-[16px] font-light app-text">
                                                No company sponsors found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-[60rem_36rem] gap-6 mt-8 charts-section">
                <div class="bg-[#F3E8EF] rounded-xl p-6">
                    <div class="flex justify-between items-center mb-2 pb-3">
                        <h2 class="text-xl font-medium text-[#213430] app-main">Global Sponsorships</h2>
                    </div>
                    <div class="table-container">
                        <table class="min-w-full text-sm text-left mt-0 text-center">
                            <thead>
                                <tr class="border-t border-[#e0cfd8]">
                                    <th class="p-2 text-left text-lg font-medium text-[#91848C] font-normal app-text">Country</th>
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text">Individual</th>
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text">Organization</th>
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-text">Total</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <tr class="border-t border-[#e0cfd8]">
                                    <td class="p-2">
                                        <div class="flex items-center gap-3">
                                            <img src="/images/china.svg" alt="China" class="w-8 h-8 rounded-full" />
                                            <span class="text-[#91848C] text-[16px] font-light app-text">China</span>
                                        </div>
                                    </td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">120</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">85</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">205</td>
                                </tr>
                                <tr class="border-t border-[#e0cfd8]">
                                    <td class="p-2">
                                        <div class="flex items-center gap-3">
                                            <img src="/images/france.svg" alt="France" class="w-8 h-8 rounded-full" />
                                            <span class="text-[#91848C] text-[16px] font-light app-text">France</span>
                                        </div>
                                    </td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">95</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">70</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">165</td>
                                </tr>
                                <tr class="border-t border-[#e0cfd8]">
                                    <td class="p-2">
                                        <div class="flex items-center gap-3">
                                            <img src="/images/germany.svg" alt="Germany" class="w-8 h-8 rounded-full" />
                                            <span class="text-[#91848C] text-[16px] font-light app-text">Germany</span>
                                        </div>
                                    </td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">80</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">65</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">145</td>
                                </tr>
                                <tr class="border-t border-[#e0cfd8]">
                                    <td class="p-2">
                                        <div class="flex items-center gap-3">
                                            <img src="/images/italy.svg" alt="Italy" class="w-8 h-8 rounded-full" />
                                            <span class="text-[#91848C] text-[16px] font-light app-text">Italy</span>
                                        </div>
                                    </td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">70</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">60</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">130</td>
                                </tr>
                                <tr class="border-t border-[#e0cfd8]">
                                    <td class="p-2">
                                        <div class="flex items-center gap-3">
                                            <img src="/images/netherlands.svg" alt="Netherlands" class="w-8 h-8 rounded-full" />
                                            <span class="text-[#91848C] text-[16px] font-light app-text">Netherlands</span>
                                        </div>
                                    </td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">110</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">95</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">205</td>
                                </tr>
                                <tr class="border-t border-[#e0cfd8]">
                                    <td class="p-2">
                                        <div class="flex items-center gap-3">
                                            <img src="/images/norway.svg" alt="Norway" class="w-8 h-8 rounded-full" />
                                            <span class="text-[#91848C] text-[16px] font-light app-text">Norway</span>
                                        </div>
                                    </td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">75</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">55</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">55</td>
                                </tr>
                                <tr class="border-t border-[#e0cfd8]">
                                    <td class="p-2">
                                        <div class="flex items-center gap-3">
                                            <img src="/images/south-koria.svg" alt="South Korea" class="w-8 h-8 rounded-full" />
                                            <span class="text-[#91848C] text-[16px] font-light app-text">South Korea</span>
                                        </div>
                                    </td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">65</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">50</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">115</td>
                                </tr>
                                <tr class="border-t border-[#e0cfd8]">
                                    <td class="p-2">
                                        <div class="flex items-center gap-3">
                                            <img src="/images/switzerlands.svg" alt="Switzerland" class="w-8 h-8 rounded-full" />
                                            <span class="text-[#91848C] text-[16px] font-light app-text">Switzerland</span>
                                        </div>
                                    </td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">85</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">70</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">155</td>
                                </tr>
                                <tr class="border-t border-[#e0cfd8]">
                                    <td class="p-2">
                                        <div class="flex items-center gap-3">
                                            <img src="/images/usa.svg" alt="United States" class="w-8 h-8 rounded-full" />
                                            <span class="text-[#91848C] text-[16px] font-light app-text">United States</span>
                                        </div>
                                    </td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">95</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">80</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">175</td>
                                </tr>
                                <tr class="border-t border-[#e0cfd8]">
                                    <td class="p-2">
                                        <div class="flex items-center gap-3">
                                            <img src="/images/sweden.svg" alt="Sweden" class="w-8 h-8 rounded-full" />
                                            <span class="text-[#91848C] text-[16px] font-light app-text">Sweden</span>
                                        </div>
                                    </td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">70</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">50</td>
                                    <td class="p-2 app-text align-middle text-[#91848C] text-[16px] font-light">120</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-[#F3E8EF] rounded-xl p-6">
                    <div class="flex justify-between items-center mb-2 pb-3">
                        <h2 class="text-2xl font-medium text-[#213430] app-main">How We Use Your Donations</h2>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="border-t border-[#e0cfd8]"></div>
                        <div class="flex items-center gap-3 p-2">
                            <img src="/images/Donation-1.png" alt="Life-Saving Medical Treatment" class="w-20 h-20 rounded-full donation-img" />
                            <div>
                                <h3 class="text-[#213430] text-xl font-semibold mb-1 app-main">Life-Saving Medical Treatment</h3>
                                <p class="text-[#91848C] font-md app-text">Your donations help cover chemotherapy, radiation, and surgeries for women who cannot afford them.</p>
                            </div>
                        </div>
                        <div class="border-b border-[#e0cfd8]"></div>
                        <div class="flex items-center gap-3 p-2">
                            <img src="/images/Donation-2.png" alt="Free Breast Cancer Screenings" class="w-20 h-20 rounded-full donation-img" />
                            <div>
                                <h3 class="text-[#213430] text-xl font-semibold mb-1 app-main">Free Breast Cancer Screenings</h3>
                                <p class="text-[#91848C] font-md app-text">Early detection saves lives! Thanks to your contributions, we organize free breast cancer screening camps.</p>
                            </div>
                        </div>
                        <div class="border-b border-[#e0cfd8]"></div>
                        <div class="flex items-center gap-3 p-2">
                            <img src="/images/Donation-3.png" alt="Financial Aid for Women" class="w-20 h-20 rounded-full donation-img" />
                            <div>
                                <h3 class="text-[#213430] text-xl font-semibold mb-1 app-main">Financial Aid for Women</h3>
                                <p class="text-[#91848C] font-md app-text">Many women delay or skip treatment due to financial constraints. With your help, we provide financial grants and sponsorships.</p>
                            </div>
                        </div>
                        <div class="border-b border-[#e0cfd8]"></div>
                        <div class="flex items-center gap-3 p-2">
                            <img src="/images/program-1.png" alt="Nutritional & Wellness Support" class="w-20 h-20 rounded-full donation-img" />
                            <div>
                                <h3 class="text-[#213430] text-xl font-semibold mb-1 app-main">Nutritional & Wellness Support</h3>
                                <p class="text-[#91848C] font-md app-text">A strong body fights better! Your support funds nutrition programs, meal plans, and exercise workshops to help women.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection




