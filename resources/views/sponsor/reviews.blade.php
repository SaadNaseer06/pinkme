@extends('sponsor.layouts.app')

@section('title', 'Reviews')

@section('content')
    <!---Main -->
    <main class="flex-1">
        <div class="bg-[#F3E8EF] p-4 rounded-lg mb-6">
            <h2 class="text-[#91848C] app-main">Reviews</h2>
        </div>

        <div class="flex flex-col md:flex-row w-full max-w-8xl mx-auto bg-[#FFF8FC] rounded-lg overflow-hidden">
            <!-- Left: Ratings Breakdown -->
            <div class="flex-1 p-6 mr-0 md:mr-14">
                <div class="space-y-6">
                    @foreach ($summary['distribution'] as $dist)
                        <div class="flex items-center" title="{{ $dist['rating'] }} Stars">
                            <span class="w-16 text-lg font-medium text-[#213430] program-main">{{ $dist['label'] }}</span>
                            <span class="text-[#db69a2] text-2xl mr-2 app-main">★</span>
                            <div class="flex-1 h-3 bg-[#FDD7EC] rounded-full overflow-hidden bar-h">
                                <div class="h-full rounded-full bg-[#db69a2] transition-all duration-300"
                                    style="width: {{ $dist['percentage'] }}%"></div>
                            </div>
                            <span class="ml-3 text-lg text-[#213430] font-medium app-main">
                                @if ($dist['count'] < 1000)
                                    {{ $dist['count'] }}
                                @else
                                    {{ number_format($dist['count'] / 1000, 1) }}k
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Average Rating -->
            <div
                class="flex-1 bg-[#F3E8EF] p-10 flex flex-col items-center justify-center text-center space-y-2 rounded-lg">
                <div class="text-4xl md:text-6xl font-bold text-[#db69a2]">{{ $summary['average'] ?? 'N/A' }}</div>
                <div class="text-2xl md:text-4xl text-[#db69a2] font-semibold flex space-x-2">
                    @for ($i = 1; $i <= 5; $i++)
                        <span>{{ $i <= floor($summary['average'] ?? 0) ? '★' : '☆' }}</span>
                    @endfor
                </div>
                <div class="text-xl md:text-2xl font-medium text-[#213430]">{{ $summary['total'] }} Ratings</div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-6 max-w-8xl mx-auto mt-6 feedbacks">
            <!-- Recent Feedbacks Section -->
            <div class="flex-1 bg-[#F3E8EF] rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-[#DCCFD8]">
                    <h2 class="text-xl font-medium text-[#213430] program-main">Recent Feedbacks</h2>
                </div>
                <div class="space-y-6 p-4">
                    @forelse ($recentReviews as $review)
                        <div class="flex items-center bg-[#FAEFF6] border border-[#EADAE4] rounded-xl p-4">
                            <div class="flex-grow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-base font-semibold text-[#213430] app-text">
                                            {{ $review->sponsor->profile->full_name ?? 'Anonymous' }}
                                        </h3>
                                    </div>
                                    <div class="flex space-x-1 text-[#db69a2] text-xl app-text">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                        @endfor
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-[#91848C] leading-relaxed app-text">
                                    {{ $review->comment ?? 'No comment provided.' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-[#91848C] p-4">No reviews available.</p>
                    @endforelse
                </div>
            </div>

            <!-- Add A Review Section -->
            <div class="flex-1 bg-[#F3E8EF] rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-[#DCCFD8]">
                    <h2 class="text-xl font-medium text-[#213430] program-main">Add A Review</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('sponsor.reviews.store') }}" method="POST" id="reviewForm">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-[#213430] mb-2 app-main">Add Your Rating<span
                                    class="text-[#db69a2]">*</span></label>
                            <div class="flex" id="starRating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <input type="radio" name="rating" value="{{ $i }}" class="hidden"
                                        id="rating-{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}
                                        required>
                                    <label for="rating-{{ $i }}"
                                        class="text-2xl app-main text-[#db69a2] cursor-pointer transition-all duration-200 ease-in-out hover:text-[#ff85b9] star-label"
                                        data-value="{{ $i }}"></label>
                                @endfor
                                @error('rating')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-6">
                            <label class="block text-[#213430] mb-2 app-text">Write Your Review<span
                                    class="text-[#db69a2]">*</span></label>
                            <textarea rows="3" name="comment" placeholder="Write here..."
                                class="w-full px-4 py-2 border border-[#DCCFD8] text-[#B1A4AD] rounded-md focus:outline-none focus:ring-2 focus:ring-pink-300 bg-transparent">{{ old('comment') }}</textarea>
                            @error('comment')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit"
                            class="w-full bg-[#db69a2] text-white py-3 rounded-md app-text">Submit</button>
                    </form>
                    @if (session('success'))
                        <div class="mb-4 text-green-600">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 text-red-600">{{ session('error') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const starContainer = document.getElementById('starRating');
                const stars = starContainer.querySelectorAll('.star-label');
                const initialRating = {{ old('rating', 0) }}; // Default to 0 if no old rating

                // Set initial state based on old('rating')
                if (initialRating > 0) {
                    stars.forEach(star => {
                        const value = parseInt(star.getAttribute('data-value'));
                        if (value <= initialRating) {
                            star.classList.add('selected');
                        }
                    });
                }

                // Handle click events
                stars.forEach(star => {
                    star.addEventListener('click', function() {
                        const value = parseInt(this.getAttribute('data-value'));
                        // Remove selected class from all stars
                        stars.forEach(s => s.classList.remove('selected'));
                        // Add selected class to this star and all previous stars
                        stars.forEach(s => {
                            if (parseInt(s.getAttribute('data-value')) <= value) {
                                s.classList.add('selected');
                            }
                        });
                        // Update the corresponding radio input
                        document.getElementById(`rating-${value}`).checked = true;
                    });
                });

                // Handle hover effect
                stars.forEach(star => {
                    star.addEventListener('mouseover', function() {
                        const value = parseInt(this.getAttribute('data-value'));
                        stars.forEach(s => {
                            if (parseInt(s.getAttribute('data-value')) <= value) {
                                s.classList.add('hover');
                            }
                        });
                    });

                    star.addEventListener('mouseout', function() {
                        stars.forEach(s => s.classList.remove('hover'));
                        // Restore selected state on mouseout
                        if (initialRating > 0) {
                            stars.forEach(s => {
                                const value = parseInt(s.getAttribute('data-value'));
                                if (value <= initialRating) {
                                    s.classList.add('selected');
                                } else {
                                    s.classList.remove('selected');
                                }
                            });
                        }
                    });
                });
            });
        </script>
        <style>
            .star-label {
                position: relative;
            }

            .star-label:before {
                content: '☆';
                color: #db69a2;
            }

            .star-label.selected:before {
                content: '★';
                color: #db69a2;
            }

            .star-label.hover:before {
                content: '★';
                color: #db69a2;
            }
        </style>
    @endpush
@endsection
