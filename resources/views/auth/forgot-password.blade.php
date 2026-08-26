<x-layout>
    <div class="min-h-screen w-full flex items-center justify-center bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 px-4 py-12">
        <div class="w-full max-w-md">
            <div class="flex flex-col items-center mb-8">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 mb-3">
                    <span class="text-xl font-bold">E</span>
                </div>
                <h1 class="text-white text-lg font-bold">ERP System</h1>
                <p class="text-slate-400 text-sm mt-1">Account recovery</p>
            </div>
            <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Forgot your password?</h2>
                    <p class="text-sm text-gray-500 mt-1">Enter your email and we'll send you a secure reset link.</p>
                </div>
                <x-forms.form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    <x-forms.input label="Email" name="email" type="email" autocomplete="email" required autofocus />
                    <x-forms.button class="w-full justify-center">Send reset link</x-forms.button>
                    <a href="{{ route('login') }}" class="block text-center text-sm font-semibold text-indigo-600 hover:text-indigo-700">Back to login</a>
                </x-forms.form>
            </div>
        </div>
    </div>
</x-layout>
