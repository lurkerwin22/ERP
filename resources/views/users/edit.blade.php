<x-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <x-page-heading>Edit user: {{ $user->name }}</x-page-heading>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 md:p-8">
            <x-forms.form
                method="POST"
                action="{{ route('users.update', $user) }}"
                class="space-y-6"
            >
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Full name --}}
                    <x-forms.input
                        label="Full name"
                        name="name"
                        required
                        :value="$user->name"
                    />

                    {{-- Email --}}
                    <x-forms.input
                        label="Email"
                        name="email"
                        type="email"
                        required
                        :value="$user->email"
                    />

                    {{-- Phone: full width --}}
                    <div class="md:col-span-2">
                        <x-forms.input
                            label="Phone"
                            name="phone"
                            type="tel"
                            :value="$user->phone"
                        />
                    </div>

                    {{-- Role + Status: only for manager/employee --}}
                    @if(in_array($user->role, ['manager', 'employee']))

                        <x-forms.select
                            label="Role"
                            name="role"
                            required
                        >
                            @foreach(\App\Models\User::ROLES as $role)
                                @if(in_array($role, ['manager', 'employee']))
                                    <option
                                        value="{{ $role }}"
                                        @selected($user->role === $role)
                                    >
                                        {{ ucfirst($role) }}
                                    </option>
                                @endif
                            @endforeach
                        </x-forms.select>

                        <x-forms.select
                            label="Status"
                            name="status"
                            required
                        >
                            <option
                                value="active"
                                @selected($user->status === 'active')
                            >
                                Active
                            </option>

                            <option
                                value="inactive"
                                @selected($user->status === 'inactive')
                            >
                                Inactive
                            </option>
                        </x-forms.select>

                    @endif

                </div>

                {{-- Password --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-forms.input
                        label="New password (optional)"
                        name="password"
                        type="password"
                        minlength="8"
                    />

                    <x-forms.input
                        label="Confirm new password"
                        name="password_confirmation"
                        type="password"
                        minlength="8"
                    />
                </div>

                <div class="flex justify-end gap-3">
                    <x-forms.link-button href="{{ route('users.index') }}">
                        Cancel
                    </x-forms.link-button>

                    <x-forms.button>
                        Save changes
                    </x-forms.button>
                </div>
            </x-forms.form>
        </div>
    </div>
</x-layout>