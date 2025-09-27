@extends('patient.layouts.app')

@section('title', 'FAQ')

@section('content')


    <!-- Dashboard Content -->
    <main class="flex-1 ">

        <div class="max-w-8xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4 ">
                <!-- FAQ Items -->
                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-pink-500 font-normal app-text">How do I create an account?</h3>
                        <svg class="w-5 h-5 text-[#91848C] transform rotate-180 transition-transform duration-300"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="faq-answer px-6 pb-2 text-[#91848C] app-text">
                        <p>Click on the "Sign Up" button on the login page and fill in your personal details. After
                            verification, you can access your application dashboard.</p>
                    </div>
                </div>

                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">What is the Patient Application Tracking System?
                        </h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>It allows users to monitor the progress of their medical application in real time.</p>
                    </div>
                </div>

                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">Can I apply without creating an account?</h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>No, creating an account is required to apply and track your application.</p>
                    </div>
                </div>
                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">What if I forget my password?</h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>If you forget your password, click the "Forgot Password?" link on the login page. Enter your
                            registered email address, and we’ll send you instructions to reset your password securely.</p>
                    </div>
                </div>


                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">How do I change my registered email or phone number?
                        </h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>Go to Account Settings to update your email or phone number. Verification may be required.</p>
                    </div>
                </div>

                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">How do I fill out an application?</h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>Log in to your account, select "New Application", and follow the step-by-step instructions to
                            complete and submit the form.</p>
                    </div>
                </div>
                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">What documents do I need to upload?</h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>You’ll need to upload your ID and any other documents listed in the application requirements.</p>
                    </div>
                </div>


                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">How do I upload my documents?</h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>During the application process, use the Upload button on the relevant step to attach your
                            documents in PDF or image format.</p>
                    </div>
                </div>


            </div>

            <!-- Right Column -->
            <div class="space-y-4 ">
                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">Can I save my application and complete it later?
                        </h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>Yes, your progress is saved automatically. You can return anytime to complete and submit your
                            application.</p>
                    </div>
                </div>
                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">Can I edit my application after submitting it?</h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>No, you can't edit the application after submission. Please review all details carefully before
                            submitting.</p>
                    </div>
                </div>

                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">How can I check my application status?</h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>Log in to your account and go to the Dashboard to view your application status in real time.</p>
                    </div>
                </div>
                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">How long does it take for my application to be
                            reviewed?</h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>Reviews typically take 5–7 business days after submission. You’ll be notified once a decision is
                            made.</p>
                    </div>
                </div>


                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">What does "Under Review" mean?</h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>It means your application has been received and is currently being evaluated by our team.</p>
                    </div>
                </div>

                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">Can I cancel my application?</h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>Yes, you can cancel your application from the Dashboard before it is approved or processed.</p>
                    </div>
                </div>
                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">How can I contact customer support?</h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 py-4 text-[#91848C] app-text">
                        <p>You can reach customer support via the Contact Us page or email us at support@example.com.</p>
                    </div>
                </div>


                <div class="bg-[#F3E8EF] rounded-lg overflow-hidden">
                    <div onclick="toggleFAQ(this)"
                        class="faq-header px-6 py-4 flex justify-between items-center cursor-pointer">
                        <h3 class="text-[#91848C] font-normal app-text">Is my personal data secure?</h3>
                        <svg class="w-5 h-5 text-[#91848C] transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                    <div class="faq-answer hidden px-6 pb-2 text-[#91848C] app-text">
                        <p>Yes, your data is protected with advanced encryption and strict privacy policies to ensure its
                            security.</p>
                    </div>
                </div>


            </div>
        </div>




    </main>

    <script src="{{ asset('js/patient/dashboard.js') }}"></script>
@endsection
