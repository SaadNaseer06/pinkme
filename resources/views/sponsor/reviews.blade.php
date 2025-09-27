@extends('sponsor.layouts.app')

@section('title', 'Reviews')

@section('content')

    <!---Main -->
    <main class="flex-1">

        <div class="bg-[#F3E8EF] p-4 rounded-lg  mb-6">
            <h2 class="text-[#91848C] app-main">Reviews</h2>
        </div>

        <div class="flex flex-col md:flex-row w-full max-w-8xl mx-auto bg-[#FFF8FC] rounded-lg  overflow-hidden">
            <!-- Left: Ratings Breakdown -->
            <div class="flex-1 p-6 mr-0 md:mr-14">
                <div class="space-y-6">
                    <!-- Loop for each rating row -->
                    <div class="flex items-center" title="5 Stars">
                        <span class="w-16 text-lg font-medium text-[#213430] program-main">FIVE</span>
                        <span class="text-[#db69a2] text-2xl mr-2 app-main">★</span>
                        <div class="flex-1 h-3 bg-[#FDD7EC] rounded-full overflow-hidden bar-h">
                            <div class="h-full rounded-full bg-[#db69a2] transition-all duration-300" style="width: 82%">
                            </div>
                        </div>
                        <span class="ml-3 text-lg text-[#213430] font-medium app-main">14.5k</span>
                    </div>

                    <div class="flex items-center" title="4 Stars">
                        <span class="w-16 text-lg font-medium text-[#213430] program-main">FOUR</span>
                        <span class="text-[#db69a2] text-2xl mr-2 app-main">★</span>
                        <div class="flex-1 h-3 bg-[#FDD7EC] rounded-full overflow-hidden bar-h">
                            <div class="h-full rounded-full bg-[#db69a2] transition-all duration-300" style="width: 70%">
                            </div>
                        </div>
                        <span class="ml-3 text-lg text-[#213430] font-medium app-main">12.4k</span>
                    </div>

                    <div class="flex items-center" title="3 Stars">
                        <span class="w-16 text-lg font-medium text-[#213430] program-main">THREE</span>
                        <span class="text-[#db69a2] text-2xl mr-2 app-main">★</span>
                        <div class="flex-1 h-3 bg-[#FDD7EC] rounded-full overflow-hidden bar-h">
                            <div class="h-full rounded-full bg-[#db69a2] transition-all duration-300" style="width: 90%">
                            </div>
                        </div>
                        <span class="ml-3 text-lg text-[#213430] font-medium app-main">17.9k</span>
                    </div>

                    <div class="flex items-center" title="2 Stars">
                        <span class="w-16 text-lg font-medium text-[#213430] program-main">TWO</span>
                        <span class="text-[#db69a2] text-2xl mr-2 app-main">★</span>
                        <div class="flex-1 h-3 bg-[#FDD7EC] rounded-full overflow-hidden bar-h">
                            <div class="h-full rounded-full bg-[#db69a2] transition-all duration-300" style="width: 48%">
                            </div>
                        </div>
                        <span class="ml-3 text-lg text-[#213430] font-medium app-main">9.5k</span>
                    </div>

                    <div class="flex items-center" title="1 Star">
                        <span class="w-16 text-lg font-medium text-[#213430] program-main">ONE</span>
                        <span class="text-[#db69a2] text-2xl mr-2 app-main">★</span>
                        <div class="flex-1 h-3 bg-[#FDD7EC] rounded-full overflow-hidden bar-h">
                            <div class="h-full rounded-full bg-[#db69a2] transition-all duration-300" style="width: 75%">
                            </div>
                        </div>
                        <span class="ml-3 text-lg text-[#213430] font-medium app-main">13.8k</span>
                    </div>
                </div>
            </div>

            <!-- Right: Average Rating -->
            <div
                class="flex-1 bg-[#F3E8EF] p-10 flex flex-col items-center justify-center text-center space-y-2 rounded-lg">
                <div class="text-4xl md:text-6xl font-bold text-[#db69a2]">4.8</div>
                <div class="text-2xl md:text-4xl text-[#db69a2] font-semibold flex space-x-2">
                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                </div>

                <div class="text-xl md:text-2xl font-medium text-[#213430]">50 Ratings</div>
            </div>
        </div>




        <div class="flex flex-col md:flex-row gap-6 max-w-8xl mx-auto mt-6 feedbacks">
            <!-- Recent Feedbacks Section -->
            <div class="flex-1 bg-[#F3E8EF] rounded-lg shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-[#DCCFD8]">
                    <h2 class="text-xl font-medium text-[#213430] program-main">Recent Feedbacks</h2>
                </div>

                <!-- Testimonials Wrapper -->
                <div class="space-y-6 p-4">
                    <!-- Testimonial 1 -->
                    <div class="flex items-center bg-[#FAEFF6] border border-[#EADAE4] rounded-xl p-4 space-x-4">
                        <img src="/images/patient-7.png" alt="John Carter" class="w-20 h-20 rounded-full object-cover">
                        <div class="flex-grow">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-base font-semibold text-[#213430] app-text">John Carter</h3>
                                    <p class="text-sm text-[#db69a2] font-normal app-text">Carter Enterprises</p>
                                </div>
                                <div class="flex space-x-1 text-[#db69a2] text-xl app-text">
                                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-[#91848C] leading-relaxed app-text">
                                Becoming A Sponsor For The Breast Cancer Awareness Campaign Has Been An Incredibly Rewarding
                                Experience.
                            </p>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="flex items-center bg-[#FAEFF6] border border-[#EADAE4] rounded-xl p-4 space-x-4">
                        <img src="/images/patient-4.png" alt="Emily Johnson" class="w-20 h-20 rounded-full object-cover">
                        <div class="flex-grow">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-base font-semibold text-[#213430] app-text">Emily Johnson</h3>
                                    <p class="text-sm text-[#db69a2] font-normal app-text">Thompson Tech</p>
                                </div>
                                <div class="flex space-x-1 text-[#db69a2] text-xl app-text">
                                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-[#91848C] leading-relaxed app-text">
                                Our Company Sponsored Multiple Events Over The Last Year, And It Has Been An Absolute
                                Pleasure.
                            </p>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="flex items-center bg-[#FAEFF6] border border-[#EADAE4] rounded-xl p-4 space-x-4">
                        <img src="/images/patient-5.png" alt="Mark Roberts" class="w-20 h-20 rounded-full object-cover">
                        <div class="flex-grow">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-base font-semibold text-[#213430] app-text">Mark Roberts</h3>
                                    <p class="text-sm text-[#db69a2] font-normal app-text">Roberts Financial Services</p>
                                </div>
                                <div class="flex space-x-1 text-[#db69a2] text-xl app-text">
                                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-[#91848C] leading-relaxed app-text">
                                Becoming A Sponsor For The Breast Cancer Awareness Campaign Has Been An Incredibly Rewarding
                                Experience.
                            </p>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Add A Review Section -->
            <div class="flex-1 bg-[#F3E8EF] rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-[#DCCFD8]">
                    <h2 class="text-xl font-medium text-[#213430] program-main">Add A Review</h2>
                </div>

                <div class="p-6">
                    <form>
                        <div class="mb-4">
                            <label class="block text-[#213430] mb-2 app-main">Add Your Rating<span
                                    class="text-[#db69a2]">*</span></label>
                            <div class="flex">
                                <span
                                    class="text-2xl app-main text-[#db69a2] cursor-pointer transition-all duration-200 ease-in-out hover:before:content-['★'] before:content-['☆']"></span>
                                <span
                                    class="text-2xl app-main text-[#db69a2] cursor-pointer transition-all duration-200 ease-in-out hover:before:content-['★'] before:content-['☆']"></span>
                                <span
                                    class="text-2xl app-main text-[#db69a2] cursor-pointer transition-all duration-200 ease-in-out hover:before:content-['★'] before:content-['☆']"></span>
                                <span
                                    class="text-2xl app-main text-[#db69a2] cursor-pointer transition-all duration-200 ease-in-out hover:before:content-['★'] before:content-['☆']"></span>
                                <span
                                    class="text-2xl app-main text-[#db69a2] cursor-pointer transition-all duration-200 ease-in-out hover:before:content-['★'] before:content-['☆']"></span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-[#213430] mb-2 app-text">Name<span
                                    class="text-[#db69a2]">*</span></label>
                            <input type="text" placeholder="Enter your Name"
                                class="w-full px-4 py-2 border border-[#DCCFD8] text-[#B1A4AD] rounded-md focus:outline-none focus:ring-2 focus:ring-pink-300 bg-transparent">
                        </div>

                        <div class="mb-6">
                            <label class="block text-[#213430] mb-2 app-text">Email<span
                                    class="text-[#db69a2]">*</span></label>
                            <input type="email" placeholder="Enter your email"
                                class="w-full px-4 py-2 border border-[#DCCFD8] text-[#B1A4AD] rounded-md focus:outline-none focus:ring-2 focus:ring-pink-300 bg-transparent">
                        </div>

                        <div class="mb-6">
                            <label class="block text-[#213430] mb-2 app-text">Write Your Review<span
                                    class="text-[#db69a2]">*</span></label>
                            <textarea rows="3" placeholder="Write here..."
                                class="w-full px-4 py-2 border border-[#DCCFD8] text-[#B1A4AD] rounded-md focus:outline-none focus:ring-2 focus:ring-pink-300 bg-transparent"></textarea>
                        </div>

                        <button type="submit"
                            class="w-full bg-[#db69a2] text-white py-3 rounded-md app-text">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

@endsection
