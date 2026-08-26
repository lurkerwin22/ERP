<x-layout>
    <div class="min-h-screen w-full flex items-center justify-center bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 px-4 py-12">
        <div class="w-full max-w-md">
            <div class="flex flex-col items-center mb-8">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 mb-3"><span class="text-xl font-bold">E</span></div>
                <h1 class="text-white text-lg font-bold">ERP System</h1>
                <p class="text-slate-400 text-sm mt-1">Create a new password</p>
            </div>
            <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8">
                <x-forms.form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    <input type="hidden" name="token" value="{{ $token }}">
                    <x-forms.input label="Email" name="email" type="email" autocomplete="email" required />
                    <x-forms.input label="New password" name="password" type="password" autocomplete="new-password" minlength="8" required />
                    <x-forms.input label="Confirm password" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required />
                    <x-forms.button class="w-full justify-center">Reset password</x-forms.button>
                </x-forms.form>
            </div>
        </div>
    </div>
</x-layout>
