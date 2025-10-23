@extends('patient.layouts.app')

@section('title', 'Profile')


@section('content')

    <!-- Dashboard Content -->
    <main class="flex-1">
        <div class="bg-[#F3E8EF] rounded-lg max-w-8xl">
            <div class="flex profile-tab">
                <!-- Left column with profile picture and basic info -->
                <div class="w-1/3 p-6 flex flex-col items-center p-box">
                    <div class="w-24 h-24 rounded-full overflow-hidden mb-3">
                        <img src="{{ asset('public/images/D-profile.png') }}" alt="User Avatar" class="w-full h-full object-cover" />
                    </div>
                    <h3 class="text-lg font-medium app-main">Sara Tylor</h3>
                    <p class="text-sm text-[#91848C] app-text">24 years, California</p>

                    <!-- Profile details in three columns -->
                    <div class="grid grid-cols-3 gap-2 w-full mt-6">
                        <div class="text-center border-r border-[#DCCFD8]">
                            <p class="text-sm text-[#db69a2] mb-1 app-text">Gender</p>
                            <p class="text-md font-medium app-text">Female</p>
                        </div>
                        <div class="text-center border-r border-[#DCCFD8] -ml-2">
                            <p class="text-sm text-[#db69a2] mb-1 app-text">Date of Birth</p>
                            <p class="text-md font-medium app-text">05/10/2003</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-[#db69a2] mb-1 app-text">Condition</p>
                            <p class="text-md font-medium app-text">Heart Disease</p>
                        </div>
                    </div>
                </div>

                <!-- Right column with detailed personal information -->
                <div class="w-2/3 border-t md:border-l border-[#DCCFD8] p-box-2">
                    <!-- Header with title and edit button -->
                    <div class="flex justify-between items-start px-6 py-3">
                        <div>
                            <h3 class="text-lg font-medium app-main">Personal Information</h3>
                            <p class="text-sm text-[#91848C] app-text">
                                Manage your basic personal details.
                            </p>
                        </div>
                        <a href="Patient-Dashboard-6.html" class="text-[#db69a2] text-md app-text mt-2">Edit Profile</a>
                    </div>
                    <div class="border-b border-[#DCCFD8]"></div>

                    <!-- Details grid with 4 columns -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-y-4 gap-x-6 p-6 text-md">
                        <div>
                            <p class="text-md text-[#213430] font-medium mb-1 app-text">Sex</p>
                            <p class="text-md text-[#91848C] font-light app-text">Female</p>
                        </div>
                        <div>
                            <p class="text-md text-[#213430] font-medium mb-1 app-text">Age</p>
                            <p class="text-md text-[#91848C] font-light app-text">24</p>
                        </div>
                        <div>
                            <p class="text-md text-[#213430] font-medium mb-1 app-text">Date of Birth</p>
                            <p class="text-md text-[#91848C] font-light app-text">05/10/2003</p>
                        </div>
                        <div>
                            <p class="text-md text-[#213430] font-medium mb-1 app-text">Blood</p>
                            <p class="text-md text-[#91848C] font-light app-text">A+</p>
                        </div>
                        <div>
                            <p class="text-md text-[#213430] font-medium mb-1 app-text">Status</p>
                            <p class="text-md text-[#91848C] font-light app-text">Active</p>
                        </div>
                        <div>
                            <p class="text-md text-[#213430] font-medium mb-1 app-text">Username</p>
                            <p class="text-md text-[#91848C] font-light app-text">saratyler232</p>
                        </div>
                        <div>
                            <p class="text-md text-[#213430] font-medium mb-1 app-text">Marital Status</p>
                            <p class="text-md text-[#91848C] font-light app-text">Single</p>
                        </div>
                        <div>
                            <p class="text-md text-[#213430] font-medium mb-1 app-text">Reg. Date</p>
                            <p class="text-md text-[#91848C] font-light app-text">18/05/2024</p>
                        </div>
                        <div>
                            <p class="text-md text-[#213430] font-medium mb-1 app-text">Reg. Number</p>
                            <p class="text-md text-[#91848C] font-light app-text">PAT-1002</p>
                        </div>
                        <div>
                            <p class="text-md text-[#213430] font-medium mb-1 app-text">Applications</p>
                            <p class="text-md text-[#91848C] font-light app-text">18</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-6 md:flex-row flex-col">
            <!-- Disease Overview Section -->
            <div class="bg-[#F3E8EF] rounded-lg p-6 w-2/3 mt-6 disease-tab-1">
                <h2 class="text-[#213430] text-lg font-semibold app-main">Patient Overview</h2>
                <p class="text-[#91848C] font-light text-md mb-6 app-text">Quick insight into diagnosis and health history
                </p>

                <hr class="border-[#DCCFD8] mb-4">

                <h3 class="text-[#213430] text-md font-semibold mb-2 app-main">About Disease:</h3>
                <p class="text-[#91848C] text-sm font-light mb-6 mr-4 app-text">
                    In March 2024, I was diagnosed with Invasive Lobular Carcinoma (ILC), a type of breast cancer that
                    quietly develops in the milk-producing glands and spreads slowly through surrounding tissue. Learning it
                    was Estrogen Receptor Positive (ER+) and Stage II B was overwhelming, but it gave me a starting point to
                    fight back.
                </p>

                <hr class="border-[#DCCFD8] mb-4">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                    <div class="flex md:flex-col flex-row justify-between">
                        <p class=" text-[#213430] text-md font-medium  mb-1 app-text">Diagnosis</p>
                        <p class="text-[#91848C] text-md font-light app-text">Invasive Lobular Carcinoma</p>
                    </div>
                    <div class="flex md:flex-col flex-row justify-between">
                        <p class="text-[#213430] text-md font-medium  mb-1 app-text">Diagnosis Date</p>
                        <p class="text-[#91848C] text-md font-light app-text">March 18, 2024</p>
                    </div>
                    <div class="flex md:flex-col flex-row justify-between">
                        <p class="text-[#213430] text-md font-medium  mb-1 app-text">Disease Stage</p>
                        <p class="text-[#91848C] text-md font-light app-text">Stage II B</p>
                    </div>

                    <div class="flex md:flex-col flex-row justify-between">
                        <p class="text-[#213430] text-md font-medium  mb-1 app-text">Disease Type</p>
                        <p class="text-[#91848C] text-md font-light app-text">Estrogen Receptor Positive (ER+)</p>
                    </div>
                    <div class="flex md:flex-col flex-row justify-between">
                        <p class="text-[#213430] text-md font-medium  mb-1 app-text">Symptoms</p>
                        <p class="text-[#91848C] text-md font-light app-text">Lump in right breast</p>
                    </div>
                    <div class="flex md:flex-col flex-row justify-between">
                        <p class="text-[#213430] text-md font-medium  mb-1 app-text">Genetic Test</p>
                        <p class="text-[#91848C] text-md font-light app-text">Positive for BRCA2 mutation</p>
                    </div>

                    <div class="flex md:flex-col flex-row justify-between">
                        <p class="text-[#213430] text-md font-medium  mb-1 app-text">Family History</p>
                        <p class="text-[#91848C] text-md font-light app-text">Mother <span
                                class="font-normal text-[#91848C]">(diagnosed at 60)</span></p>
                    </div>
                </div>
            </div>

            <!-- Files Section -->
            <div class="bg-[#F3E8EF] rounded-lg p-6 w-1/3 mt-6 disease-tab-2">
                <div class="flex justify-between items-center">
                    <div class="flex flex-col ">
                        <h2 class="text-[#213430] text-lg font-semibold app-main">Files</h2>
                        <p class="text-[14px] text-[#91848C] font-light">Securely view your documents</p>
                    </div>
                    <div class="flex items-center space-x-2 bg-pink-500 px-4 py-2 rounded-md md:flex hidden">
                        <button class="text-white text-sm font-medium">Upload</button>
                        <img src="{{ asset('public/images/upload.svg') }}" alt="" />
                    </div>
                    <div class="flex items-center space-x-2 bg-pink-500 px-4 py-3 rounded-md md:hidden flex">
                        <img src="{{ asset('public/images/upload.svg') }}" alt="" />
                    </div>
                </div>

                <hr class="border-[#DCCFD8] mb-4">
                <ul class="space-y-6">
                    <li class="flex justify-between items-center">
                        <div class="flex items-center gap-2 group cursor-pointer">
                            <img src="{{ asset('public/images/document.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                            <img src="{{ asset('public/images/document-pink.svg') }}" alt="" class="w-6 h-6 hidden group-hover:block" />
                            <span class="text-[#91848C] text-md font-light app-text group-hover:text-[#DB69A2]">
                                Mamogram_Report_Jan.pdf
                            </span>
                        </div>
                        <div class="flex items-center">
                            <button class="text-pink-500 mr-2 group relative w-6 h-6">
                                <img src="{{ asset('public/images/eye.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                                <img src="{{ asset('public/images/eye-pink.svg') }}" alt=""
                                    class="w-6 h-6 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                            <button class="group relative w-4 h-4">
                                <img src="{{ asset('public/images/cross.svg') }}" alt="" class="w-4 h-4 block group-hover:hidden" />
                                <img src="{{ asset('public/images/cross-pink.svg') }}" alt=""
                                    class="w-4 h-4 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                        </div>
                    </li>

                    <li class="flex justify-between items-center">
                        <div class="flex items-center gap-2 group cursor-pointer">
                            <img src="{{ asset('public/images/document.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                            <img src="{{ asset('public/images/document-pink.svg') }}" alt=""
                                class="w-6 h-6 hidden group-hover:block" />
                            <span class="text-[#91848C] text-md font-light app-text group-hover:text-[#DB69A2]">
                                Mamogram_Report_Feb.pdf
                            </span>
                        </div>
                        <div class="flex items-center">
                            <button class="text-pink-500 mr-2 group relative w-6 h-6">
                                <img src="{{ asset('public/images/eye.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                                <img src="{{ asset('public/images/eye-pink.svg') }}" alt=""
                                    class="w-6 h-6 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                            <button class="group relative w-4 h-4">
                                <img src="{{ asset('public/images/cross.svg') }}" alt="" class="w-4 h-4 block group-hover:hidden" />
                                <img src="{{ asset('public/images/cross-pink.svg') }}"
                                    class="w-4 h-4 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                        </div>
                    </li>

                    <li class="flex justify-between items-center">
                        <div class="flex items-center gap-2 group cursor-pointer">
                            <img src="{{ asset('public/images/document.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                            <img src="{{ asset('public/images/document-pink.svg') }}" alt=""
                                class="w-6 h-6 hidden group-hover:block" />
                            <span class="text-[#91848C] text-md font-light app-text group-hover:text-[#DB69A2]">
                                Mamogram_Report_Mar.pdf
                            </span>
                        </div>

                        <div class="flex items-center">
                            <button class="text-pink-500 mr-2 group relative w-6 h-6">
                                <img src="{{ asset('public/images/eye.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                                <img src="{{ asset('public/images/eye-pink.svg') }}" alt=""
                                    class="w-6 h-6 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                            <button class="group relative w-4 h-4">
                                <img src="{{ asset('public/images/cross.svg') }}" alt="" class="w-4 h-4 block group-hover:hidden" />
                                <img src="{{ asset('public/images/cross-pink.svg') }}" alt=""
                                    class="w-4 h-4 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                        </div>

                    </li>

                    <li class="flex justify-between items-center">
                        <div class="flex items-center gap-2 group cursor-pointer">
                            <img src="{{ asset('public/images/document.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                            <img src="{{ asset('public/images/document-pink.svg') }}" alt=""
                                class="w-6 h-6 hidden group-hover:block" />
                            <span class="text-[#91848C] text-md font-light app-text group-hover:text-[#DB69A2]">
                                Mamogram_Report_Apr.pdf
                            </span>
                        </div>
                        <div class="flex items-center">
                            <button class="text-pink-500 mr-2 group relative w-6 h-6">
                                <img src="{{ asset('public/images/eye.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                                <img src="{{ asset('public/images/eye-pink.svg') }}" alt=""
                                    class="w-6 h-6 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                            <button class="group relative w-4 h-4">
                                <img src="{{ asset('public/images/cross.svg') }}" alt="" class="w-4 h-4 block group-hover:hidden" />
                                <img src="{{ asset('public/images/cross-pink.svg') }}" alt=""
                                    class="w-4 h-4 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                        </div>
                    </li>

                    <li class="flex justify-between items-center">
                        <div class="flex items-center gap-2 group cursor-pointer">
                            <img src="{{ asset('public/images/document.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                            <img src="{{ asset('public/images/document-pink.svg') }}" alt=""
                                class="w-6 h-6 hidden group-hover:block" />
                            <span class="text-[#91848C] text-md font-light app-text group-hover:text-[#DB69A2]">
                                Mamogram_Report_May.pdf
                            </span>
                        </div>
                        <div class="flex items-center">
                            <button class="text-pink-500 mr-2 group relative w-6 h-6">
                                <img src="{{ asset('public/images/eye.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                                <img src="{{ asset('public/images/eye-pink.svg') }}" alt=""
                                    class="w-6 h-6 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                            <button class="group relative w-4 h-4">
                                <img src="{{ asset('public/images/cross.svg') }}" alt="" class="w-4 h-4 block group-hover:hidden" />
                                <img src="{{ asset('public/images/cross-pink.svg') }}" alt=""
                                    class="w-4 h-4 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                        </div>
                    </li>

                    <li class="flex justify-between items-center">
                        <div class="flex items-center gap-2 group cursor-pointer">
                            <img src="{{ asset('public/images/document.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                            <img src="{{ asset('public/images/document-pink.svg') }}" alt=""
                                class="w-6 h-6 hidden group-hover:block" />
                            <span class="text-[#91848C] text-md font-light app-text group-hover:text-[#DB69A2]">
                                Mamogram_Report_Jun.pdf
                            </span>
                        </div>
                        <div class="flex items-center">
                            <button class="text-pink-500 mr-2 group relative w-6 h-6">
                                <img src="{{ asset('public/images/eye.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                                <img src="{{ asset('public/images/eye-pink.svg') }}" alt=""
                                    class="w-6 h-6 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                            <button class="group relative w-4 h-4">
                                <img src="{{ asset('public/images/cross.svg') }}" alt="" class="w-4 h-4 block group-hover:hidden" />
                                <img src="{{ asset('public/images/cross-pink.svg') }}" alt=""
                                    class="w-4 h-4 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                        </div>
                    </li>

                    <li class="flex justify-between items-center">
                        <div class="flex items-center gap-2 group cursor-pointer">
                            <img src="{{ asset('public/images/document.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                            <img src="{{ asset('public/images/document-pink.svg') }}" alt=""
                                class="w-6 h-6 hidden group-hover:block" />
                            <span class="text-[#91848C] text-md font-light app-text group-hover:text-[#DB69A2]">
                                Mamogram_Report_Jul.pdf
                            </span>
                        </div>
                        <div class="flex items-center">
                            <button class="text-pink-500 mr-2 group relative w-6 h-6">
                                <img src="{{ asset('public/images/eye.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                                <img src="{{ asset('public/images/eye-pink.svg') }}" alt=""
                                    class="w-6 h-6 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                            <button class="group relative w-4 h-4">
                                <img src="{{ asset('public/images/cross.svg') }}" alt="" class="w-4 h-4 block group-hover:hidden" />
                                <img src="{{ asset('public/images/cross-pink.svg') }}" alt=""
                                    class="w-4 h-4 hidden group-hover:block absolute top-0 left-0" />
                            </button>
                        </div>
                    </li>
                </ul>

            </div>
        </div>
        <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
            <div class="flex justify-between items-center mb-4 ml-3">
                <h2 class="text-lg font-medium text-[#213430] app-main">
                    All Applications List
                </h2>
                <h2 class="text-md font-normal text-[#db69a2]  underline app-text">View All</h2>
            </div>

            <div class="table-container">
                <table class="min-w-full text-sm text-left mt-6">
                    <thead>
                        <tr class="border-t border-[#e0cfd8]">
                            <th class="p-2">
                                <input type="checkbox"
                                    class="accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                            </th>

                            <th class="p-2 text-lg text-[#91848C] font-normal app-text">
                                Applications Title
                            </th>
                            <th class="p-2 text-lg text-[#91848C] font-normal app-text pl-10">
                                Applications ID
                            </th>
                            <th class="p-2 text-lg text-[#91848C] font-normal app-text">
                                Applications Status
                            </th>
                            <th class="p-2 text-lg text-[#91848C] font-normal app-text">Email</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal app-text">Document</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <!-- Row 1 -->
                        <tr class="border-t border-[#e0cfd8]">
                            <td class="p-2">
                                <input type="checkbox"
                                    class="accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                            </td>


                            <td class="p-2">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('public/images/profile-1.png') }}" alt="" class="w-8 h-8 rounded-full" />
                                    <span class="text-[#91848C] text-[16px] font-light app-text">Medical Aid Request</span>
                                </div>
                            </td>

                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text pl-10">APP-20240301
                            </td>

                            <td class="p-2 align-middle">
                                <span
                                    class="bg-[#C5E8D1]  text-[16px] font-light text-[#20B354] px-4 py-2 rounded-sm text-xs font-medium app-text">
                                    Approved
                                </span>
                            </td>

                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">john.doe@email.com
                            </td>

                            <td
                                class="p-2 mt-2 text-[#91848C] text-[16px] font-light flex items-center underline gap-1 app-text">
                                <img src="{{ asset('public/images/download.svg') }}" alt="" class="w-4 h-4">Download</td>
                        </tr>

                        <!-- Row 2 -->
                        <tr class="border-t border-[#e0cfd8]">
                            <td class="p-2">
                                <input type="checkbox"
                                    class="accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                            </td>

                            <td class="p-2">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('public/images/profile-2.png') }}" alt="" class="w-8 h-8 rounded-full" />
                                    <span class="text-[#91848C] text-[16px] font-light app-text">Surgery Assistance</span>
                                </div>
                            </td>

                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text pl-10">APP-20240302
                            </td>

                            <td class="p-2 align-middle">
                                <span
                                    class="bg-[#E4D7DF]  text-[16px] font-light text-[#91848C] px-4 py-2 rounded-sm text-xs font-medium app-text">
                                    Under Review
                                </span>
                            </td>

                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                jane.smith@email.com</td>

                            <td
                                class="p-2 mt-2 text-[#91848C] text-[16px] font-light flex items-center underline gap-1 app-text">
                                <img src="{{ asset('public/images/download.svg') }}" alt="" class="w-4 h-4">Download</td>
                        </tr>

                        <!-- Row 3 -->
                        <tr class="border-t border-[#e0cfd8]">
                            <td class="p-2">
                                <input type="checkbox"
                                    class="accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                            </td>

                            <td class="p-2">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('public/images/profile-3.png') }}" alt="" class="w-8 h-8 rounded-full" />
                                    <span class="text-[#91848C] text-[16px] font-light app-text">Hospital Bill
                                        Support</span>
                                </div>
                            </td>

                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text pl-10">APP-20240303
                            </td>

                            <td class="p-2 align-middle">
                                <span
                                    class="bg-[#E8C5C5]  text-[16px] font-light text-[#B32020] px-4 py-2 rounded-sm text-xs font-medium app-text">
                                    Rejected
                                </span>
                            </td>

                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">robert.m@email.com
                            </td>

                            <td
                                class="p-2 mt-2 text-[#91848C] text-[16px] font-light flex items-center underline gap-1 app-text">
                                <img src="{{ asset('public/images/download.svg') }}" alt="" class="w-4 h-4">Download</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script src="{{ asset('js/patient/dashboard.js') }}"></script>
@endsection
