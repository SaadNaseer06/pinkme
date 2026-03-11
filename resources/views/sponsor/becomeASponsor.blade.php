@extends('sponsor.layouts.app')

@section('title', 'Become A Sponsor')

@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    $formatAmount = function ($n) {
        $n = (float) $n;
        return $n == floor($n) ? (string) (int) $n : number_format($n, 2);
    };
    $eventImagePool = ['program-1.png', 'program-2.png', 'program-3.png', 'program-4.png', 'program-5.png'];
    $fundImagePool = ['S-1.png', 'S-2.png', 'S-3.png', 'S-4.png'];
@endphp

@section('content')
    <div class="flex-1 flex flex-col">
        <main class="flex-1">
            <div class="max-w-8xl mx-auto">
                @include('sponsor.partials.cards')
            </div>

            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-2xl font-semibold text-[#213430] mb-4 program-h">Upcoming Programs</h2>
                </div>
                @forelse ($upcomingEvents as $event)
                    @php
                        $eventImage = $event->image
                            ? (str_starts_with($event->image, 'http') ? $event->image : asset('storage/' . $event->image))
                            : asset('public/images/' . ($eventImagePool[$loop->index % count($eventImagePool)] ?? 'program-1.png'));
                        $shortDesc = Str::limit(strip_tags($event->description ?? ''), 140);
                        $fundLabel = $event->funding_goal
                            ? '$' . $formatAmount($event->funding_goal)
                            : 'To be determined';
                    @endphp
                    <div class="bg-[#F3E8EF] rounded-lg p-4 mb-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex flex-col items-center justify-center w-20 h-20 border-2 border-pink rounded-lg mr-4 bg-[#FFF7FC]">
                                <span class="text-sm text-pink">{{ $event->month_label }}</span>
                                <span class="text-4xl font-bold text-pink">{{ $event->day_label }}</span>
                            </div>
                            <div class="w-20 h-20 rounded-lg overflow-hidden mr-4">
                                <img src="{{ $eventImage }}" alt="{{ $event->title }}" class="w-full h-full object-cover" />
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-[#213430] mb-1 program-h">{{ $event->title }}</h3>
                                <p class="text-sm text-[#91848C] program-p">{{ $shortDesc ?: 'No description.' }}</p>
                                <p class="text-xs text-[#9E2469] font-medium uppercase tracking-wide">Full Funding Required</p>
                                <span class="inline-flex items-center gap-2 px-3 py-1 mt-2 text-xs font-semibold text-white bg-[#9E2469] rounded-full uppercase">Sponsor Covers 100%</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-4 text-sm items-end">
                            <div class="flex flex-col text-right">
                                <span class="text-[#91848C] app-text">Program Fund</span>
                                <span class="text-[#213430] font-medium app-text">{{ $fundLabel }}</span>
                            </div>
                            <a href="{{ route('sponsor.events.show', $event) }}" class="inline-flex items-center px-4 py-2 bg-[#9E2469] text-white text-sm font-medium rounded-lg hover:bg-[#B52D75] transition">
                                Sponsor Entire Program
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white/40 border border-[#DCCFD8] rounded-lg p-8 text-center text-[#91848C]">
                        No upcoming programs available at the moment.
                    </div>
                @endforelse
            </div>

            <div class="max-w-[97rem] mx-auto crousel-width">
                <div class="px-4 py-6 bg-[#F3E8EF] rounded-lg">
                    <div class="flex justify-between items-center pb-6">
                        <h1 class="text-2xl font-medium text-[#213430] program-h">Ongoing Support Program</h1>
                        @if ($ongoingEvents->count() > 4)
                            <div class="flex space-x-2">
                                <button type="button" id="fundPrevBtn" class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#9E2469] hover:border-none hover:text-white p-2 rounded-lg" aria-label="Previous">
                                    <svg class="md:h-6 h-4 md:w-6 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 18l-6-6 6-6" />
                                    </svg>
                                </button>
                                <button type="button" id="fundNextBtn" class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#9E2469] hover:border-none hover:text-white p-2 rounded-lg" aria-label="Next">
                                    <svg class="md:h-6 h-4 md:w-6 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 18l6-6-6-6" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="relative overflow-hidden">
                        @if ($ongoingEvents->isEmpty())
                            <div class="bg-white/40 border border-[#DCCFD8] rounded-lg p-8 text-center text-[#91848C]">
                                No active sponsorship programs right now.
                            </div>
                        @else
                            <div id="fundCarousel" class="carousel-container flex overflow-x-auto scrollbar-hide gap-4 pb-2">
                                @foreach ($ongoingEvents as $event)
                                    @php
                                        $imgSrc = $event->image
                                            ? (str_starts_with($event->image, 'http') ? $event->image : asset('storage/' . $event->image))
                                            : asset('public/images/' . ($fundImagePool[$loop->index % count($fundImagePool)] ?? 'S-1.png'));
                                        $shortDescription = Str::limit(strip_tags($event->description ?? ''), 120);
                                        $fundLabel = $event->funding_goal
                                            ? '$' . $formatAmount($event->funding_goal)
                                            : 'To be determined';
                                    @endphp
                                    <div class="carousel-item flex-shrink-0 w-[300px] md:w-[330px] lg:w-[350px]">
                                        <div class="bg-[#F3E8EF] border border-[#DCCFD8] rounded-lg overflow-hidden hover:ring-2 ring-[#9E2469] h-full flex flex-col">
                                            <div class="p-2">
                                                <img src="{{ $imgSrc }}" alt="{{ $event->title }}" class="w-full h-48 object-cover rounded" />
                                            </div>
                                            <div class="p-4 space-y-4 flex-1 flex flex-col">
                                                <h2 class="text-lg font-medium text-[#213430] app-main">{{ $event->title }}</h2>
                                                <p class="text-sm text-[#91848C] app-text flex-1">{{ $shortDescription ?: 'No description.' }}</p>
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-[#91848C] app-text">Funding Type</span>
                                                    <span class="text-[#213430] font-medium app-text">Fully Funded - Sponsor Pays 100%</span>
                                                </div>
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-[#91848C] app-text">Program Fund</span>
                                                    <span class="text-[#213430] font-medium app-text">{{ $fundLabel }}</span>
                                                </div>
                                                <a href="{{ route('sponsor.events.show', $event) }}" class="w-full py-3 text-center font-medium border border-[#DCCFD8] text-[#213430] rounded-lg hover:bg-[#9E2469] hover:text-white hover:border-[#9E2469] transition app-main">
                                                    Sponsor Entire Program
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var carousel = document.getElementById('fundCarousel');
            var prevBtn = document.getElementById('fundPrevBtn');
            var nextBtn = document.getElementById('fundNextBtn');
            if (!carousel) return;
            var scrollAmount = 350;
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                });
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                });
            }
        });
    </script>
@endpush
