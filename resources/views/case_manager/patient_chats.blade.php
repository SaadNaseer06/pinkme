@extends('case_manager.layouts.app')

@section('title', 'Patient Chats')

@section('content')

    <!---Main -->
    <main class="flex-1">


        <div class="flex max-h-[850px] max-w-8xl mx-auto gap-6 mt-2">
            <!-- Left sidebar -->
            <div class="w-96 bg-[#F3E8EF] rounded-md flex flex-col overflow-y-auto overflow-x-hidden md:flex hidden">
                <!-- Chat header -->
                <div class="p-4 font-medium text-gray-700 flex justify-between items-center app-text">
                    <h2>Chat</h2>
                    <i class="fas fa-chevron-up text-gray-400"></i>
                </div>

                <!-- Search bar -->
                <div class="px-4 pb-3">
                    <div class="relative">
                        <input type="text" placeholder="Search..."
                            class="w-full text-[#B9B1B6] bg-transparent rounded-md py-2 px-4 text-sm border border-[#B9B1B6] focus:outline-none app-text">
                        <button class="absolute right-3 top-2 text-[#DB69A2]">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- User avatars -->
                <div class="flex overflow-hidden px-4 py-2 gap-2 ">
                    <div class="flex-shrink-0 relative">
                        <img src="{{ asset('public/images/patient-1.png') }}" class="w-10 h-10 rounded-full" alt="User">
                        <div class="absolute bottom-[-1px] right-[2px] bg-green-500 rounded-full w-3 h-3 border-2 "></div>
                    </div>
                    <div class="flex-shrink-0 relative">
                        <img src="{{ asset('public/images/patient-2.png') }}" class="w-10 h-10 rounded-full" alt="User">
                        <div class="absolute bottom-[-1px] right-[2px] bg-green-500 rounded-full w-3 h-3 border-2 "></div>
                    </div>
                    <div class="flex-shrink-0 relative">
                        <img src="{{ asset('public/images/patient-7.png') }}" class="w-10 h-10 rounded-full" alt="User">
                        <div class="absolute bottom-[-1px] right-[2px] bg-green-500 rounded-full w-3 h-3 border-2 "></div>
                    </div>
                    <div class="flex-shrink-0 relative">
                        <img src="{{ asset('public/images/patient-4.png') }}" class="w-10 h-10 rounded-full" alt="User">
                        <div class="absolute bottom-[-1px] right-[2px] bg-green-500 rounded-full w-3 h-3 border-2 "></div>
                    </div>
                    <div class="flex-shrink-0 relative">
                        <img src="{{ asset('public/images/patient-3.png') }}" class="w-10 h-10 rounded-full" alt="User">
                        <div class="absolute bottom-[-1px] right-[2px] bg-green-500 rounded-full w-3 h-3 border-2 "></div>
                    </div>
                    <div class="flex-shrink-0 relative">
                        <img src="{{ asset('public/images/patient-6.png') }}" class="w-10 h-10 rounded-full" alt="User">
                        <div class="absolute bottom-[-1px] right-[2px] bg-green-500 rounded-full w-3 h-3 border-2 "></div>
                    </div>
                    <div class="flex-shrink-0 relative">
                        <img src="{{ asset('public/images/patient-5.png') }}" class="w-10 h-10 rounded-full" alt="User">
                        <div class="absolute bottom-[-1px] right-[2px] bg-green-500 rounded-full w-3 h-3 border-2 "></div>
                    </div>
                    <div class="flex-shrink-0 relative">
                        <img src="{{ asset('public/images/patient-11.png') }}" class="w-10 h-10 rounded-full" alt="User">
                        <div class="absolute bottom-[-1px] right-[2px] bg-green-500 rounded-full w-3 h-3 border-2 "></div>
                    </div>
                </div>

                <!-- Tab navigation -->
                <div class="flex border-b border-t border-[#DCCFD8]  mt-2">
                    <button
                        class="flex-1 py-3 text-center text-[#db69a2] border-b-2 border-pink-500 font-medium text-sm app-text">Chat</button>
                    <button class="flex-1 py-3 text-center text-[#B9B1B6] font-medium text-sm app-text">Unread</button>
                    <button class="flex-1 py-3 text-center text-[#B9B1B6] font-medium text-sm app-text">Contact</button>
                </div>

                <!-- Chat list -->
                <div class="max-h-[600px] overflow-y-auto">

                    <!-- User 1 -->
                    <div class="p-4 flex border-b border-gray-100 hover:bg-pink-100">
                        <div class="mr-3">
                            <img src="{{ asset('public/images/patient-1.png') }}" class="w-10 h-10 rounded-full" alt="John Doe">
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <h3 class="font-medium text-[#213430] app-main">John Doe</h3>
                                <span class="text-xs text-[#91848C]">10:45 am</span>
                            </div>
                            <p class="text-sm text-[#db69a2] app-text">Typing...</p>
                        </div>
                        <div class="self-end">
                            <i class="fas fa-check-double text-green-500 text-xs"></i>
                        </div>
                    </div>

                    <!-- User 2 -->
                    <div class="p-4 flex border-b border-gray-100 hover:bg-pink-100">
                        <div class="mr-3">
                            <img src="{{ asset('public/images/patient-7.png') }}" class="w-10 h-10 rounded-full" alt="Brian Hall">
                        </div>
                        <div class="flex-1 ">
                            <div class="flex justify-between ">
                                <h3 class="font-medium text-[#213430] app-main">Brian Hall</h3>
                                <span class="text-xs text-[#91848C]">10:45 am</span>
                            </div>
                            <p class="text-sm text-[#91848C] truncate app-text">Can you update my income <br> proof
                                document?</p>
                        </div>
                        <div class="self-end">
                            <i class="fas fa-check-double text-green-500 text-xs"></i>
                        </div>
                    </div>

                    <!-- User 3 -->
                    <div class="p-4 flex border-b border-gray-100 hover:bg-pink-100">
                        <div class="mr-3">
                            <img src="{{ asset('public/images/patient-5.png') }}" class="w-10 h-10 rounded-full"
                                alt="Daniel Ross">
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <h3 class="font-medium text-[#213430] app-main">Daniel Ross</h3>
                                <span class="text-xs text-[#91848C]">10:45 am</span>
                            </div>
                            <p class="text-sm text-[#91848C] truncate app-text">Where can I track the status <br> of my
                                application?</p>
                        </div>
                        <div class="self-end">
                            <i class="fas fa-check-double text-green-500 text-xs"></i>
                        </div>
                    </div>

                    <!-- User 4 -->
                    <div class="p-4 flex border-b border-gray-100 hover:bg-pink-100">
                        <div class="mr-3">
                            <img src="{{ asset('public/images/patient-3.png') }}" class="w-10 h-10 rounded-full"
                                alt="Michael Anderson">
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <h3 class="font-medium text-[#213430]">Michael Anderson</h3>
                                <span class="text-xs text-[#91848C]">10:45 am</span>
                            </div>
                            <p class="text-sm text-[#91848C] truncate">Is it possible to upload <br> additional medical...
                            </p>
                        </div>
                        <div class="self-end">
                            <i class="fas fa-check-double text-green-500 text-xs"></i>
                        </div>
                    </div>

                    <!-- User 5 -->
                    <div class="p-4 flex border-b border-gray-100 hover:bg-pink-100">
                        <div class="mr-3">
                            <img src="{{ asset('public/images/patient-2.png') }}" class="w-10 h-10 rounded-full"
                                alt="Sarah Johnson">
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <h3 class="font-medium text-[#213430]">Sarah Johnson</h3>
                                <span class="text-xs text-[#91848C]">10:45 am</span>
                            </div>
                            <p class="text-sm text-[#91848C] truncate">Can I edit my personal <br> details now?</p>
                        </div>
                        <div class="self-end">
                            <i class="fas fa-check-double text-green-500 text-xs"></i>
                        </div>
                    </div>

                    <!-- User 6 -->
                    <div class="p-4 flex border-b border-gray-100 hover:bg-pink-100">
                        <div class="mr-3">
                            <img src="{{ asset('public/images/patient-2.png') }}" class="w-10 h-10 rounded-full"
                                alt="Emily Watson">
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <h3 class="font-medium text-[#213430]">Emily Watson</h3>
                                <span class="text-xs text-[#91848C]">10:45 am</span>
                            </div>
                            <p class="text-sm text-[#91848C] truncate">Can I apply for another <br> application?</p>
                        </div>
                        <div class="self-end">
                            <i class="fas fa-check-double text-green-500 text-xs"></i>
                        </div>
                    </div>

                    <!-- User 7 -->
                    <div class="p-4 flex border-b border-gray-100 hover:bg-pink-100">
                        <div class="mr-3">
                            <img src="{{ asset('public/images/patient-6.png') }}" class="w-10 h-10 rounded-full"
                                alt="Lucy Taylor">
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <h3 class="font-medium text-[#213430]">Lucy Taylor</h3>
                                <span class="text-xs text-[#91848C]">10:45 am</span>
                            </div>
                            <p class="text-sm text-[#91848C] truncate">What documents are still <br> pending for review?
                            </p>
                        </div>
                        <div class="self-end">
                            <i class="fas fa-check-double text-green-500 text-xs"></i>
                        </div>
                    </div>
                    <!-- User 8 -->
                    <div class="p-4 flex border-b border-gray-100 hover:bg-pink-100">
                        <div class="mr-3">
                            <img src="{{ asset('public/images/patient-11.png') }}" class="w-10 h-10 rounded-full"
                                alt="Lucy Taylor">
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <h3 class="font-medium text-[#213430]">Lucy Taylor</h3>
                                <span class="text-xs text-[#91848C]">10:45 am</span>
                            </div>
                            <p class="text-sm text-[#91848C] truncate">What documents are still <br> pending for review?
                            </p>
                        </div>
                        <div class="self-end">
                            <i class="fas fa-check-double text-green-500 text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main chat area -->
            <div class="flex-1 flex-col  max-h-[820px] bg-[#F3E8EF] shadow-md rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-[#F3E8EF] px-4 py-3 flex justify-between items-center border-b border-[#DCCFD8] mb-2">
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-full flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('public/images/chat-profile.png') }}" alt="Support Agent"
                                class="h-full w-full object-contain" />
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
                                    <img src="{{ asset('public/images/D-profile.png') }}" alt="User"
                                        class="h-full w-full object-cover" />
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
                                    <img src="{{ asset('public/images/patient-1.png') }}" alt="Support"
                                        class="h-full w-full object-contain" />
                                </div>
                                <div class="flex items-center justify-center text-xs text-[#B1A4AD] mt-1">6:46</div>
                            </div>
                            <div
                                class="bg-[#FFF7FC] text-[#91848C] font-light border border-gray-200 px-4 py-2 rounded-lg max-w-lg app-text">
                                <p>You can track your application from the "My Application" section
                                    in your dashboard.</p>
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
                                    <img src="{{ asset('public/images/D-profile.png') }}" alt="User"
                                        class="h-full w-full object-cover" />
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
                                    <img src="{{ asset('public/images/patient-1.png') }}" alt="Support"
                                        class="h-full w-full object-contain" />
                                </div>
                                <div class="flex items-center justify-center text-xs text-[#B1A4AD] mt-1">6:49</div>
                            </div>
                            <div
                                class="bg-[#FFF7FC] text-[#91848C] font-light border border-gray-200 px-4 py-2 rounded-lg max-w-lg app-text">
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
                                    <img src="{{ asset('public/images/D-profile.png') }}" alt="User"
                                        class="h-full w-full object-cover" />
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
                                    <img src="{{ asset('public/images/patient-1.png') }}" alt="Support"
                                        class="h-full w-full object-contain" />
                                </div>
                                <div class="flex items-center justify-center text-xs text-[#B1A4AD] mt-1">6:53</div>
                            </div>
                            <div
                                class="bg-[#FFF7FC] text-[#91848C] font-light border border-gray-200 px-4 py-2 rounded-lg max-w-lg app-text">
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
                                    <img src="{{ asset('public/images/D-profile.png') }}" alt="User"
                                        class="h-full w-full object-cover" />
                                </div>
                                <div class=" flex items-center justify-center text-xs text-[#B1A4AD] mt-1">6:54</div>
                            </div>
                        </div>
                    </div>
                    <!-- Support Message 4 -->
                    <div class="flex">
                        <div class="flex items-end">
                            <div class="mr-2">
                                <div class="h-12 w-12 rounded-full  flex items-center justify-center overflow-hidden">
                                    <img src="{{ asset('public/images/patient-1.png') }}" alt="Support"
                                        class="h-full w-full object-contain" />
                                </div>
                                <div class="flex items-center justify-center text-xs text-[#B1A4AD] mt-1">6:55</div>
                            </div>
                            <div
                                class="bg-[#FFF7FC] text-[#91848C] font-light border border-gray-200 px-4 py-2 rounded-lg max-w-lg app-text">
                                <p>You're welcome!</p>
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
        </div>

    </main>
    <!-- Include Dashboard JavaScript -->
    <script src="{{ asset('js/case_manager/dashboard.js') }}"></script>
@endsection
