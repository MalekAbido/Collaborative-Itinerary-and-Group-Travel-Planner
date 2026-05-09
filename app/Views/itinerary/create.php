<?php require __DIR__ . '/../layouts/header.php'; ?>

    <div class="flex items-center gap-2 pb-3 mb-6 border-b border-outline-variant">
            <span class="material-symbols-outlined text-primary text-[28px]">flight_takeoff</span>
            <h2 class="font-display text-h2 text-on-surface m-0">Plan a New Trip</h2>
        </div>

        <form action="/itinerary/store" method="POST" enctype="multipart/form-data" class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 lg:p-8">
            
            <div class="mb-6">
                <label class="block text-label-caps uppercase text-on-surface-variant mb-2" for="title">Trip Title</label>
                <input id="title" name="title" type="text" placeholder="e.g., Summer in Kyoto" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
            </div>

            <div class="mb-6">
                <label class="block text-label-caps uppercase text-on-surface-variant mb-2" for="description">Trip Notes / Description</label>
                <textarea id="description" name="description" rows="3" placeholder="What is the vibe of this trip?"
                    class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition resize-y"></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-label-caps uppercase text-on-surface-variant mb-2" for="coverImage">
                    Cover Image <span class="normal-case text-outline font-normal ml-1">(Optional)</span>
                </label>
                <input id="coverImage" name="coverImage" type="file" accept="image/*"
                    class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2 text-body-sm text-on-surface file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-body-sm file:font-semibold file:bg-primary-fixed file:text-primary hover:file:bg-primary-fixed-variant transition cursor-pointer" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-label-caps uppercase text-on-surface-variant mb-2" for="startDate">Departure Date</label>
                    <input id="startDate" name="startDate" type="date" required
                        class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                </div>
                <div>
                    <label for="endDate" class="block text-label-caps uppercase text-on-surface-variant mb-2">End Date</label>
                    <input type="date" name="endDate" id="endDate" required 
                        class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 outline-none transition">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-label-caps uppercase text-on-surface-variant mb-2" for="baseCurrency">Itinerary Base Currency</label>
                <select id="baseCurrency" name="baseCurrency" required
                    class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest px-4 py-2.5 text-body-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition">
                    <option value="USD">US Dollar (USD)</option>
                    <option value="EUR">Euro (EUR)</option>
                    <option value="GBP">British Pound (GBP)</option>
                    <option value="JPY">Japanese Yen (JPY)</option>
                    <option value="EGP">Egyptian Pound (EGP)</option>
                    <option value="AED">United Arab Emirates Dirham (AED)</option>
                    <option value="SAR">Saudi Riyal (SAR)</option>
                    <option value="CAD">Canadian Dollar (CAD)</option>
                    <option value="AUD">Australian Dollar (AUD)</option>
                    <option value="CHF">Swiss Franc (CHF)</option>
                    <option value="CNY">Chinese Yuan (CNY)</option>
                    <option value="INR">Indian Rupee (INR)</option>
                </select>
                <p class="mt-1.5 text-body-xs text-outline">This will be the main currency for all trip expenses and cannot be changed later.</p>
            </div>

            <div class="mb-8">
                <label class="block text-label-caps uppercase text-on-surface-variant mb-2" for="inviteEmails">
                    Invite Members <span class="normal-case text-outline font-normal ml-1">(Optional)</span>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute inset-y-0 left-3 flex items-center text-[20px] text-outline pointer-events-none">mail</span>
                    <input id="inviteEmails" name="inviteEmails" type="text" placeholder="e.g., ahmed@gmail.com, sara@gmail.com"
                        class="w-full rounded-lg border border-outline-variant bg-surface-container-lowest pl-11 pr-4 py-2.5 text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition" />
                </div>
                <p class="mt-1.5 text-body-xs text-outline">Separate multiple email addresses with a comma.</p>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-outline-variant">
                <a class="inline-flex items-center gap-2 rounded-lg border-2 border-outline-variant text-on-surface font-semibold text-body-sm px-6 py-2.5 hover:bg-surface-container transition cursor-pointer"    href="/dashboard" >Cancel</a>
                <button class="inline-flex items-center gap-2 rounded-lg bg-primary text-on-primary font-semibold text-body-sm px-6 py-2.5 shadow-sm hover:bg-on-primary-fixed-variant transition cursor-pointer"    type="submit" >
                    Create Itinerary <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </button>
            </div>
        </form>
    <?php require __DIR__ . '/../layouts/footer.php'; ?>