@extends($layout)

@section('title', $title)

@section('content')
    <div class="max-w-8xl mx-auto space-y-6">
        <section class="rounded-3xl bg-gradient-to-r from-[#9E2469] to-[#C95A92] text-white p-8 shadow-sm">
            <div class="max-w-3xl">
                <p class="text-sm uppercase tracking-[0.25em] text-white/80">Portal Guide</p>
                <h1 class="mt-3 text-3xl md:text-4xl font-semibold">{{ $portalName }}</h1>
                <p class="mt-4 text-base md:text-lg leading-7 text-white/90">{{ $intro }}</p>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-[1.25fr_0.75fr] gap-6">
            <div class="rounded-3xl bg-white border border-[#EAD7E2] shadow-sm p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-10 w-10 rounded-2xl bg-[#F9E6F0] text-[#9E2469] flex items-center justify-center">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-semibold text-[#213430]">Quick Start</h2>
                        <p class="text-sm text-[#7E7279]">Follow these steps to get oriented quickly.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach ($quickStart as $index => $step)
                        <div class="flex items-start gap-4 rounded-2xl bg-[#FCF7FA] border border-[#F0E2EA] p-4">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#9E2469] text-white font-semibold">
                                {{ $index + 1 }}
                            </div>
                            <p class="text-[#3B4A47] leading-7">{{ $step }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <aside class="rounded-3xl bg-[#FFF9FC] border border-[#EAD7E2] shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-10 w-10 rounded-2xl bg-[#F9E6F0] text-[#9E2469] flex items-center justify-center">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-[#213430]">Helpful Tips</h2>
                </div>

                <div class="space-y-3">
                    @foreach ($tips as $tip)
                        <div class="rounded-2xl bg-white border border-[#F0E2EA] p-4 text-[#4C5A57] leading-7">
                            {{ $tip }}
                        </div>
                    @endforeach
                </div>
            </aside>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($sections as $section)
                <div class="rounded-3xl bg-white border border-[#EAD7E2] shadow-sm p-6">
                    <h2 class="text-2xl font-semibold text-[#213430] mb-5">{{ $section['title'] }}</h2>
                    <div class="space-y-4">
                        @foreach ($section['items'] as $item)
                            <div class="flex items-start gap-4">
                                <div class="min-w-[3.25rem] rounded-2xl bg-[#F9E6F0] px-3 py-2 text-center text-sm font-semibold text-[#9E2469]">
                                    {{ $item['label'] }}
                                </div>
                                <p class="text-[#495754] leading-7">{{ $item['text'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </section>
    </div>
@endsection
