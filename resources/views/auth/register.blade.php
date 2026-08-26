<x-layout>
    <div class="min-h-screen w-full flex items-center justify-center bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 px-4 py-12">
        <div class="w-full max-w-md">

            <!-- Brand -->
            <div class="flex flex-col items-center mb-8">
                <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h1 class="text-white text-lg font-bold tracking-tight">ERP System</h1>
                <p class="text-slate-400 text-sm mt-0.5">Management Portal</p>
            </div>

            <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Create an account</h2>
                    <p class="text-sm text-gray-500 mt-1">Fill in your details to get started.</p>
                </div>

                <x-forms.form method="POST" action="/register" enctype="multipart/form-data" class="space-y-5">
                    <x-forms.input label="Name" name="name" required />
                    <x-forms.input label="Email" name="email" type="email" autocomplete="username" required />
                    <x-forms.input label="Password" name="password" type="password" autocomplete="new-password" required />
                    <x-forms.input label="Password Confirmation" name="password_confirmation" type="password" autocomplete="new-password" required />

                    <div class="flex flex-col gap-y-3 pt-2">
                        <x-forms.button class="w-full py-2.5 justify-center">
                            Create Account
                        </x-forms.button>

                        <a href="/login" class="w-full text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold py-2.5 rounded-lg border border-gray-300 transition-colors">
                            Log In
                        </a>
                    </div>
                </x-forms.form>
            </div>
        </div>
    </div>
</x-layout>