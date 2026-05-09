<?php require __DIR__ . '/../layouts/header-minimal.php'; ?>

    <main class="grow pt-navbar">
        <section
            class="relative w-full min-h-204.75 flex items-center justify-center bg-surface overflow-hidden px-6 lg:px-8 py-20">
            <div class="absolute inset-0 z-0 opacity-20 bg-[url('/assets/images/hero-bg.jpeg')] bg-cover bg-center">
            </div>
            <div class="relative z-10 max-w-content mx-auto w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="flex flex-col gap-6 max-w-2xl">
                    <h1 class="font-display text-display text-on-surface">Plan Your Next Group Adventure Together</h1>
                    <p class="font-body text-body-lg text-on-surface-variant max-w-lg">
                        The ultimate structured exploration platform. Coordinate itineraries, settle finances, and
                        manage documents seamlessly for any group trip.
                    </p>
                    <div class="flex flex-wrap gap-3 pt-4">
                        <a class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-md px-8 py-4 shadow-sm hover:bg-on-primary-fixed-variant transition cursor-pointer"    href="#learn-more"
                            >
                            Learn More
                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
                <div
                    class="relative hidden lg:block h-125 w-full rounded-2xl shadow-xl overflow-hidden bg-surface-container-lowest p-4 border border-outline-variant">
                    <img alt="Group planning" class="w-full h-full object-cover rounded-xl"
                        src="/assets/images/hero-group.png" />
                    <div
                        class="absolute bottom-8 left-10 bg-surface-container-lowest/90 backdrop-blur-md p-4 rounded-xl shadow-md border border-outline-variant/50 flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-full bg-tertiary-container text-on-tertiary-container flex items-center justify-center">
                            <span class="material-symbols-outlined">event_note</span>
                        </div>
                        <div>
                            <p class="font-body text-label-caps text-outline uppercase">Upcoming</p>
                            <p class="font-display text-h4 text-on-surface">Euro Summer '26</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="learn-more" class="py-20 px-6 lg:px-8 bg-surface-container-lowest">
            <div class="max-w-content mx-auto">
                <div class="text-center mb-12">
                    <h2 class="font-display text-h2 text-on-surface mb-2">Structured Planning, Effortless Travel</h2>
                    <p class="font-body text-body-lg text-on-surface-variant">Everything your group needs in one
                        centralized dashboard.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="col-span-1 md:col-span-2 bg-surface p-6 rounded-xl border border-outline-variant shadow-sm relative overflow-hidden group hover:shadow-md transition">
                        <div class="relative z-10 w-2/3">
                            <div
                                class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center mb-4 border border-outline-variant">
                                <span class="material-symbols-outlined">format_list_bulleted</span>
                            </div>
                            <h3 class="font-display text-h3 text-on-surface mb-2">Collaborative Itinerary</h3>
                            <p class="font-body text-body-md text-on-surface-variant">Build your day-by-day schedule
                                together. Vote on activities, assign attendance, and resolve scheduling conflicts
                                instantly.</p>
                        </div>
                        <div
                            class="absolute -right-10 -bottom-10 w-1/2 h-[120%] opacity-20 bg-[url('https://images.unsplash.com/photo-1501504905252-473c47e087f8?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80')] bg-cover bg-center rounded-tl-3xl transform group-hover:-translate-y-2 group-hover:-translate-x-2 transition-transform duration-500">
                        </div>
                    </div>
                    <div
                        class="bg-surface p-6 rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition">
                        <div
                            class="w-10 h-10 rounded-lg bg-secondary-fixed text-secondary flex items-center justify-center mb-4 border border-outline-variant">
                            <span class="material-symbols-outlined">account_balance_wallet</span>
                        </div>
                        <h3 class="font-display text-h3 text-on-surface mb-2">Financial Settlement</h3>
                        <p class="font-body text-body-md text-on-surface-variant">Track shared expenses. Our smart
                            algorithm calculates exactly who owes who, minimizing transactions.</p>
                    </div>
                    <div
                        class="bg-surface p-6 rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition">
                        <div
                            class="w-10 h-10 rounded-lg bg-tertiary-fixed text-tertiary flex items-center justify-center mb-4 border border-outline-variant">
                            <span class="material-symbols-outlined">folder_shared</span>
                        </div>
                        <h3 class="font-display text-h3 text-on-surface mb-2">Document Vault</h3>
                        <p class="font-body text-body-md text-on-surface-variant">Keep passports, booking confirmations,
                            and tickets securely stored and accessible offline for the whole group.</p>
                    </div>
                    <div
                        class="col-span-1 md:col-span-2 bg-surface p-6 rounded-xl border border-outline-variant shadow-sm flex flex-col md:flex-row items-center gap-6 hover:shadow-md transition">
                        <div class="w-full md:w-1/2">
                            <div
                                class="w-10 h-10 rounded-lg bg-primary-fixed-dim text-on-primary-fixed flex items-center justify-center mb-4 border border-outline-variant">
                                <span class="material-symbols-outlined">how_to_vote</span>
                            </div>
                            <h3 class="font-display text-h3 text-on-surface mb-2">Social Consensus</h3>
                            <p class="font-body text-body-md text-on-surface-variant">Can't decide on dinner? Use
                                built-in polling to reach group consensus quickly without endless group chat scrolling.
                            </p>
                        </div>
                        <div
                            class="w-full md:w-1/2 bg-surface-container-low rounded-lg p-4 flex flex-col gap-3 border border-outline-variant">
                            <div
                                class="flex justify-between items-center bg-surface-container-lowest p-3 rounded-lg shadow-xs border border-outline-variant">
                                <span class="font-body text-body-sm font-semibold text-on-surface">Boat Tour</span>
                                <div class="flex -space-x-2">
                                    <div
                                        class="h-6 w-6 rounded-full border-2 border-surface-container-lowest bg-primary-fixed text-[9px] font-bold text-primary flex items-center justify-center">
                                        AK</div>
                                    <div
                                        class="h-6 w-6 rounded-full border-2 border-surface-container-lowest bg-tertiary-fixed text-[9px] font-bold text-tertiary flex items-center justify-center">
                                        SM</div>
                                    <div
                                        class="h-6 w-6 rounded-full border-2 border-surface-container-lowest bg-secondary-fixed text-[9px] font-bold text-secondary flex items-center justify-center">
                                        +2</div>
                                </div>
                            </div>
                            <div
                                class="flex justify-between items-center bg-surface-container-lowest p-3 rounded-lg shadow-xs border border-outline-variant opacity-60">
                                <span class="font-body text-body-sm text-on-surface">Museum Visit</span>
                                <div class="flex -space-x-2">
                                    <div
                                        class="h-6 w-6 rounded-full border-2 border-surface-container-lowest bg-primary-fixed text-[9px] font-bold text-primary flex items-center justify-center">
                                        OM</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 px-6 lg:px-8 bg-primary text-on-primary relative overflow-hidden">
            <div
                class="absolute inset-0 z-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] mix-blend-overlay">
            </div>
            <div class="max-w-3xl mx-auto text-center relative z-10 flex flex-col items-center gap-6">
                <h2 class="font-display text-[40px] font-bold leading-tight">Ready to synchronize your next trip?</h2>
                <p class="font-body text-body-lg text-on-primary/90 mb-2">Join thousands of groups traveling smarter
                    with Itinerary.</p>
                <a class="inline-flex items-center gap-2 rounded-lg bg-surface-container-lowest text-primary font-semibold text-body-md px-8 py-4 shadow-lg hover:scale-105 transition-transform cursor-pointer"    href="register"
                    >
                    Start Planning Now
                    <span class="material-symbols-outlined">flight_takeoff</span>
                </a>
            </div>
        </section>
    </main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>