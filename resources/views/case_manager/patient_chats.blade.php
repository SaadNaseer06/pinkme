@extends('case_manager.layouts.app')

@section('title', 'Patient Chats')

@section('content')
    <main class="flex-1">
        @if ($contacts->isEmpty())
            <div class="max-w-4xl mx-auto bg-[#F3E8EF] rounded-xl shadow-sm p-12 text-center space-y-4">
                <div class="flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-[#DB69A2]" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-[#213430]">No Conversations Yet</h2>
                <p class="text-sm text-[#91848C]">You can start a conversation once a patient reaches out or you send the
                    first message from their profile.</p>
            </div>
        @else
            <div class="flex flex-col lg:flex-row lg:max-h-[850px] max-w-7xl mx-auto gap-6">
                <!-- Contact sidebar -->
                <aside class="w-full lg:w-96 bg-[#F3E8EF] rounded-2xl flex flex-col overflow-hidden shadow-sm">
                    <div class="p-5 border-b border-[#E4D6DF] flex items-center justify-between">
                        <h2 class="text-base font-semibold text-[#213430]">My Patients</h2>
                        <span class="text-xs text-[#91848C]">{{ $contacts->count() }} active</span>
                    </div>
                    <div class="px-5 py-4 border-b border-[#E4D6DF]">
                        <div class="relative">
                            <input type="text" placeholder="Search..."
                                class="w-full text-sm text-[#91848C] bg-transparent rounded-lg py-2.5 pl-4 pr-10 border border-[#D1C2CC] focus:outline-none focus:border-[#DB69A2]"
                                disabled>
                            <span class="absolute right-3 top-2.5 text-[#DB69A2]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="flex border-b border-[#E4D6DF] text-sm text-[#91848C]">
                        <span
                            class="flex-1 text-center py-3 font-medium text-[#DB69A2] border-b-2 border-[#DB69A2]">Chat</span>
                        <span class="flex-1 text-center py-3">Unread</span>
                        <span class="flex-1 text-center py-3">Files</span>
                    </div>
                    <div class="flex-1 overflow-y-auto" data-chat-contact-list>
                        @foreach ($contacts as $contact)
                            <a href="{{ route('case_manager.patientChats', ['contact' => $contact['id']]) }}"
                                class="flex items-center gap-3 px-5 py-4 border-b border-[#E9DCE4] hover:bg-[#F9F1F6] transition @if ($contact['id'] === $activeContactId) bg-[#F7E3ED] @endif"
                                data-contact-item data-contact-id="{{ $contact['id'] }}">
                                <div class="relative">
                                    <img src="{{ $contact['avatar_url'] ?? asset('public/images/chat-profile.png') }}"
                                        alt="{{ $contact['name'] }}"
                                        class="w-11 h-11 rounded-full object-cover border border-white shadow-sm">
                                    <span
                                        class="absolute -top-1 -right-1 bg-[#DB69A2] text-white text-[10px] font-semibold rounded-full px-1.5 @if (($contact['unread_count'] ?? 0) === 0) hidden @endif"
                                        data-contact-unread>{{ $contact['unread_count'] ?? 0 }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-sm font-semibold text-[#213430] truncate" data-contact-name>
                                            {{ $contact['name'] }}</p>
                                        <span class="text-xs text-[#B1A4AD]"
                                            data-contact-time>{{ $contact['latest_at'] ?? '' }}</span>
                                    </div>
                                    <p class="text-xs text-[#91848C] truncate" data-contact-last>
                                        {{ $contact['latest_message'] ?? 'No messages yet' }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </aside>

                <!-- Conversation panel -->
                <section class="flex-1 bg-[#F3E8EF] rounded-2xl shadow-sm flex flex-col overflow-hidden" data-chat-app
                    data-current-user-id="{{ auth()->id() }}" data-active-contact-id="{{ $activeContact['id'] }}"
                    data-fetch-url="{{ route('chat.messages.index', ['contact' => $activeContact['id']]) }}"
                    data-send-url="{{ route('chat.messages.store', ['contact' => $activeContact['id']]) }}"
                    data-default-avatar="{{ asset('public/images/chat-profile.png') }}"
                    data-self-avatar="{{ auth()->user()->avatar_url }}">
                    <header class="flex items-center justify-between px-6 py-4 border-b border-[#E4D6DF]">
                        <div class="flex items-center gap-3">
                            <img src="{{ $activeContact['avatar_url'] ?? asset('public/images/chat-profile.png') }}"
                                alt="{{ $activeContact['name'] }}"
                                class="w-12 h-12 rounded-full object-cover border border-white shadow-sm">
                            <div>
                                <h3 class="text-base font-semibold text-[#213430]">{{ $activeContact['name'] }}</h3>
                                <p class="text-xs text-[#91848C]">Patient conversation</p>
                            </div>
                        </div>
                        <div class="flex gap-3 text-[#DB69A2]">
                            <button type="button" class="p-2 rounded-full hover:bg-[#F4D9E6] transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12h12" />
                                </svg>
                            </button>
                            <button type="button" class="p-2 rounded-full hover:bg-[#F4D9E6] transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12h12M6 16h12M6 8h12" />
                                </svg>
                            </button>
                        </div>
                    </header>

                    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6 bg-[#F8EEF4]" data-chat-thread>
                        @foreach ($messagesPayload as $message)
                            @php
                                $isOwn = $message['sender_id'] === auth()->id();
                                $attachment = $message['attachment'] ?? null;
                            @endphp
                            <div class="flex {{ $isOwn ? 'justify-end' : '' }}" data-message-id="{{ $message['id'] }}">
                                <div class="flex items-end gap-3 max-w-[70%]">
                                    @if (!$isOwn)
                                        <div class="flex flex-col items-center">
                                            <img src="{{ $message['sender']['avatar_url'] ?? asset('public/images/chat-profile.png') }}"
                                                class="w-10 h-10 rounded-full object-cover"
                                                alt="{{ $message['sender']['name'] }}">
                                            <span
                                                class="text-[10px] text-[#B1A4AD] mt-1">{{ $message['sent_at_display'] }}</span>
                                        </div>
                                        <div
                                            class="bg-white border border-[#E5D2DE] text-[#4C4047] text-sm leading-relaxed px-4 py-3 rounded-2xl rounded-bl-sm shadow-sm">
                                            @if (filled($message['content']))
                                                <div class="whitespace-pre-line">{{ $message['content'] }}</div>
                                            @endif
                                            @if ($attachment)
                                                <div class="mt-3">
                                                    @if ($attachment['is_image'])
                                                        <a href="{{ $attachment['url'] }}" target="_blank" class="block">
                                                            <img src="{{ $attachment['url'] }}"
                                                                alt="{{ $attachment['name'] ?? 'Attachment' }}"
                                                                class="max-w-[220px] rounded-lg shadow-sm">
                                                        </a>
                                                    @else
                                                        <a href="{{ $attachment['url'] }}" target="_blank"
                                                            class="inline-flex items-center gap-2 text-sm text-[#DB69A2] hover:underline">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="1.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M8.25 15.75L15.75 8.25M9 9l6 6M6 12l6 6 6-6" />
                                                            </svg>
                                                            <span>{{ $attachment['name'] ?? 'Download attachment' }}</span>
                                                            @if (!empty($attachment['size']))
                                                                <span
                                                                    class="text-xs text-[#91848C]">({{ \Illuminate\Support\Number::fileSize($attachment['size']) }})</span>
                                                            @endif
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div
                                            class="bg-[#DB69A2] text-white text-sm leading-relaxed px-4 py-3 rounded-2xl rounded-br-sm shadow-sm text-left">
                                            @if (filled($message['content']))
                                                <div class="whitespace-pre-line">{{ $message['content'] }}</div>
                                            @endif
                                            @if ($attachment)
                                                <div class="mt-3">
                                                    @if ($attachment['is_image'])
                                                        <a href="{{ $attachment['url'] }}" target="_blank"
                                                            class="block">
                                                            <img src="{{ $attachment['url'] }}"
                                                                alt="{{ $attachment['name'] ?? 'Attachment' }}"
                                                                class="max-w-[220px] rounded-lg shadow-sm">
                                                        </a>
                                                    @else
                                                        <a href="{{ $attachment['url'] }}" target="_blank"
                                                            class="inline-flex items-center gap-2 text-sm text-white hover:underline">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="1.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M8.25 15.75L15.75 8.25M9 9l6 6M6 12l6 6 6-6" />
                                                            </svg>
                                                            <span>{{ $attachment['name'] ?? 'Download attachment' }}</span>
                                                            @if (!empty($attachment['size']))
                                                                <span
                                                                    class="text-xs text-[#F7D7EB]">({{ \Illuminate\Support\Number::fileSize($attachment['size']) }})</span>
                                                            @endif
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-center">
                                            <img src="{{ auth()->user()->avatar_url }}"
                                                class="w-10 h-10 rounded-full object-cover" alt="You">
                                            <span
                                                class="text-[10px] text-[#B1A4AD] mt-1">{{ $message['sent_at_display'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <footer class="border-t border-[#E4D6DF] bg-[#F3E8EF]">
                        <form class="flex items-center gap-3 px-5 py-4" data-chat-form>
                            <label
                                class="p-2.5 rounded-full bg-white border border-[#E0D0D9] text-[#70626A] hover:text-[#DB69A2] cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.75V12m0 0v5.25m0-5.25h5.25M12 12H6.75" />
                                </svg>
                                <input type="file" name="attachment" accept="image/*,application/pdf" class="hidden"
                                    data-chat-upload>
                            </label>
                            <input type="text" name="content" placeholder="Type your message"
                                class="flex-1 bg-white border border-[#DACFD6] rounded-xl px-4 py-2.5 text-sm text-[#4C4047] focus:outline-none focus:border-[#DB69A2]"
                                autocomplete="off">
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-[#DB69A2] text-white rounded-xl px-5 py-2 text-sm font-medium hover:bg-[#C35A91] transition">
                                Send
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            </button>
                        </form>
                    </footer>
                </section>
            </div>
        @endif
    </main>
@endsection
