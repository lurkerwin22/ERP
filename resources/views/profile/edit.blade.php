<x-layout>
    <div class="space-y-6">
        <x-page-heading>Profile & Settings</x-page-heading>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 space-y-6">
                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 md:p-8">
                    <div class="mb-6"><h2 class="text-lg font-semibold text-slate-900">Personal information</h2><p class="text-sm text-slate-500 mt-1">Keep your account information up to date.</p></div>
                    <x-forms.form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @method('PATCH')
                        <div class="flex flex-col sm:flex-row gap-5 items-start">
                            <div class="shrink-0">
                                @if(auth()->user()->profile_photo)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile photo" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-slate-100">
                                @else
                                    <div class="w-20 h-20 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl font-bold ring-4 ring-slate-100">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                                @endif
                            </div>
                            <div class="flex-1 w-full"><x-forms.input label="Profile photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp"/><p class="text-xs text-slate-500 mt-1">JPG, PNG or WEBP. Maximum 2 MB.</p></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <x-forms.input label="Full name" name="name" required :value="auth()->user()->name" />
                            <x-forms.input label="Email" name="email" type="email" required :value="auth()->user()->email" />
                        </div>
                        <x-forms.input label="Phone" name="phone" type="tel" :value="auth()->user()->phone" placeholder="+216 ..." />
                        <div class="flex justify-end"><x-forms.button>Save changes</x-forms.button></div>
                    </x-forms.form>
                </section>

                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 md:p-8">
                    <div class="mb-6"><h2 class="text-lg font-semibold text-slate-900">Security</h2><p class="text-sm text-slate-500 mt-1">Change your password regularly to keep your account secure.</p></div>
                    <x-forms.form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                        @method('PATCH')
                        <x-forms.input label="Current password" name="current_password" type="password" autocomplete="current-password" required />
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <x-forms.input label="New password" name="password" type="password" autocomplete="new-password" minlength="8" required />
                            <x-forms.input label="Confirm new password" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required />
                        </div>
                        <div class="flex justify-end"><x-forms.button>Change password</x-forms.button></div>
                    </x-forms.form>
                </section>
            </div>

            <aside class="bg-slate-900 rounded-2xl p-6 text-white h-fit">
                <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Account</p>
                <h2 class="text-xl font-semibold mt-2">{{ auth()->user()->name }}</h2>
                <p class="text-sm text-slate-400 mt-1">{{ auth()->user()->email }}</p>
                <div class="mt-6 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><span class="text-slate-400">Role</span><span class="font-semibold capitalize">{{ auth()->user()->role }}</span></div>
                    <div class="flex justify-between gap-4"><span class="text-slate-400">Status</span><span class="font-semibold capitalize">{{ auth()->user()->status }}</span></div>
                </div>
            </aside>
        </div>
    </div>
</x-layout>
