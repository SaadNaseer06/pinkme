@extends('patient.layouts.app')

@section('title', 'Patient Chats')

@section('content')

    <!-- Dashboard Content -->
    <main class="flex-1 ">
        <div class="max-w-8xl mx-auto bg-[#F3E8EF] shadow-md rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-[#F3E8EF] px-4 py-3 flex justify-between items-center border-b border-[#DCCFD8] mb-2">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/chat-profile.png') }}" alt="Support Agent" class="h-full w-full object-contain" />
                    </div>
                    <span class="ml-3 font-medium text-gray-700 app-main">Support Team</span>
                </div>
                <button class="text-[#DB69A2] bg-[#F1C7DE] p-2 rounded-md focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                    </svg>
                </button>
            </div>

            <!-- Chat Area -->
            <div class="max-h-[650px] overflow-y-auto p-4 space-y-6 ">
                <!-- User Message 1 -->
                <div class="flex justify-end">
                    <div class="flex items-start justify-center">
                        <div class="bg-[#DB69A2] text-white font-light px-4 py-2 rounded-lg max-w-sm mt-1 app-text">
                            <p>How do I check my application status?</p>
                        </div>
                        <div class="ml-2">
                            <div class="h-12 w-12 rounded-full  flex items-center justify-center overflow-hidden">
                                <img src="{{ asset('images/D-profile.png') }}" alt="User" class="h-full w-full object-cover" />
                            </div>
                            <div class=" flex items-center justify-center text-xs text-[#B1A4AD] mt-1">6:45</div>
                        </div>
                    </div>
                </div>

                <!-- Support Message 1 -->
                <div class="flex">
                    <div class="flex items-end">
                        <div class="mr-2">
                            <div class="h-12 w-12 rounded-full  flex items-center justify-center overflow-hidden">
                                <img src="{{ asset('images/chat-profile.png') }}" alt="Support" class="h-full w-full object-contain" />
                            </div>
                            <div class="flex items-center justify-center text-xs text-[#B1A4AD] mt-1">6:46</div>
                        </div>
                        <div
                            class="bg-[#FFF7FC] text-[#91848C] font-light border border-gray-200 px-4 py-2 rounded-lg max-w-md app-text">
                            <p>You can track your application from the "My Application" section in your dashboard.</p>
                        </div>
                    </div>
                </div>

                <!-- User Message 2 -->
                <div class="flex justify-end">
                    <div class="flex items-start justify-center">
                        <div class="bg-[#DB69A2] text-white font-light px-4 py-2 rounded-lg max-w-sm mt-1 app-text">
                            <p>I need to upload missing documents.</p>
                        </div>
                        <div class="ml-2">
                            <div class="h-12 w-12 rounded-full  flex items-center justify-center overflow-hidden">
                                <img src="{{ asset('images/D-profile.png') }}" alt="User" class="h-full w-full object-cover" />
                            </div>
                            <div class=" flex items-center justify-center text-xs text-[#B1A4AD] mt-1">6:48</div>
                        </div>
                    </div>
                </div>

                <!-- Support Message 2 -->
                <div class="flex">
                    <div class="flex items-end">
                        <div class="mr-2">
                            <div class="h-12 w-12 rounded-full  flex items-center justify-center overflow-hidden">
                                <img src="{{ asset('images/chat-profile.png') }}" alt="Support" class="h-full w-full object-contain" />
                            </div>
                            <div class="flex items-center justify-center text-xs text-[#B1A4AD] mt-1">6:49</div>
                        </div>
                        <div
                            class="bg-[#FFF7FC] text-[#91848C] font-light border border-gray-200 px-4 py-2 rounded-lg max-w-md app-text">
                            <p>You can upload documents under the "Uploaded Documents"
                                section on dashboard page</p>
                        </div>
                    </div>
                </div>

                <!-- User Message 3 -->
                <div class="flex justify-end">
                    <div class="flex items-start justify-center">
                        <div class="bg-[#DB69A2] text-white font-light px-4 py-2 rounded-lg max-w-sm mt-1 app-text">
                            <p>How long does application review take?</p>
                        </div>
                        <div class="ml-2">
                            <div class="h-12 w-12 rounded-full  flex items-center justify-center overflow-hidden">
                                <img src="{{ asset('images/D-profile.png') }}" alt="User" class="h-full w-full object-cover" />
                            </div>
                            <div class=" flex items-center justify-center text-xs text-[#B1A4AD] mt-1">6:52</div>
                        </div>
                    </div>
                </div>

                <!-- Support Message 3 -->
                <div class="flex">
                    <div class="flex items-end">
                        <div class="mr-2">
                            <div class="h-12 w-12 rounded-full  flex items-center justify-center overflow-hidden">
                                <img src="{{ asset('images/chat-profile.png') }}" alt="Support" class="h-full w-full object-contain" />
                            </div>
                            <div class="flex items-center justify-center text-xs text-[#B1A4AD] mt-1">6:53</div>
                        </div>
                        <div
                            class="bg-[#FFF7FC] text-[#91848C] font-light border border-gray-200 px-4 py-2 rounded-lg max-w-md app-text">
                            <p>Our team typically reviews applications within 5-7 business days.
                                You’ll be notified once a decision is made.</p>
                        </div>
                    </div>
                </div>

                <!-- User Message 4 -->
                <div class="flex justify-end">
                    <div class="flex items-start justify-center">
                        <div class="bg-[#DB69A2] text-white font-light px-4 py-2 rounded-lg max-w-sm mt-1 app-text">
                            <p>Okay Thanks.</p>
                        </div>
                        <div class="ml-2">
                            <div class="h-12 w-12 rounded-full  flex items-center justify-center overflow-hidden">
                                <img src="{{ asset('images/D-profile.png') }}" alt="User" class="h-full w-full object-cover" />
                            </div>
                            <div class=" flex items-center justify-center text-xs text-[#B1A4AD] mt-1">6:54</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="border-t px-4 py-3 flex items-center">
                <button class="text-black focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
                <button class="text-black ml-2 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                </button>
                <input type="text" placeholder="Type your message"
                    class="ml-4 flex-1 py-2 px-3 bg-transparent focus:outline-none border border-[#DACFD6] rounded-md app-text" />
                <button
                    class="bg-[#db69a2] text-white rounded-md px-4 py-2 ml-2 hover:bg-pink-500 transition-colors duration-200 flex items-center app-text">
                    Send
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </div>
        </div>

    </main>



    <script src="{{ asset('js/patient/dashboard.js') }}"></script>

@endsection
