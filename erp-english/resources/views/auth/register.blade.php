<x-layout>
    <div class="max-w-md mx-auto my-8">
        <div class="mb-6 text-center">
            <x-page-heading>Register</x-page-heading>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 md:p-8">
            <x-forms.form method="POST" action="/register" enctype="multipart/form-data" class="space-y-6">
                <x-forms.input label="Name" name="name" />
                <x-forms.input label="Email" name="email" type="email" autocomplete="username" />
                <x-forms.input label="Password" name="password" type="password" autocomplete="new-password" />
                <x-forms.input label="Password Confirmation" name="password_confirmation" type="password" autocomplete="new-password" />

                <div class="flex flex-col gap-y-3 pt-2">
                    <x-forms.button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg shadow-sm transition-colors justify-center flex">
                        Create Account
                    </x-forms.button>

                    <a href="/login" class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2.5 rounded-lg border border-gray-300 transition-colors">
                        Log In
                    </a>
                </div>
            </x-forms.form>
        </div>
    </div>
</x-layout>