<x-layout>
    <div class="max-w-xl mx-auto my-8">
        <div class="mb-6 text-center">
            <x-page-heading>Log In</x-page-heading>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
            <x-forms.form method="POST" action="/login" class="space-y-6">
                <x-forms.input label="Email" name="email" type="email" autocomplete="username" />
                <x-forms.input label="Password" name="password" type="password" autocomplete="current-password" />

                <div class="flex flex-col gap-y-3 pt-2">
                    <x-forms.button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg shadow-sm transition-colors justify-center flex">
                        Log In
                    </x-forms.button>

                    <a href="/register" class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2.5 rounded-lg border border-gray-300 transition-colors">
                        Create an account
                    </a>
                </div>
            </x-forms.form>
        </div>
    </div>
</x-layout>