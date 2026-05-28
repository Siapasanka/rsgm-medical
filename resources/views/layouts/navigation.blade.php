@php
    $role = Auth::user()?->role?->name;
    $roleLabel = match($role) {
        'superadmin' => 'Super Admin',
        'admin' => 'Admin',
        'petugas' => 'Petugas',
        'dokter' => 'Dokter',
        default => $role ? ucfirst($role) : null,
    };
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-10 w-auto object-contain" />
                        <span class="text-sm font-semibold text-gray-800 uppercase tracking-wide hidden md:inline">
                            {{ config('app.name', 'RSGM UNDIP') }}
                        </span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(in_array($role, ['superadmin', 'admin', 'petugas']))
                        <x-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')">
                            {{ __('Pasien') }}
                        </x-nav-link>
                    @endif

                    @if($role === 'petugas')
                        <x-nav-link :href="route('registrations.index')" :active="request()->routeIs('registrations.*')">
                            {{ __('Pendaftaran') }}
                        </x-nav-link>
                    @endif

                    @if($role === 'dokter')
                        <x-nav-link :href="route('medical-records.index')" :active="request()->routeIs('medical-records.*')">
                            {{ __('Rekam Medis') }}
                        </x-nav-link>
                    @endif

                    @if(in_array($role, ['superadmin', 'admin']))
                        <x-nav-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">
                            {{ __('Audit Log') }}
                        </x-nav-link>

                        <x-nav-link :href="route('account-users.index')" :active="request()->routeIs('account-users.*')">
                            {{ __('Akun Dokter/Petugas') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            @if($roleLabel)
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-emerald-700">
                                    {{ $roleLabel }}
                                </span>
                            @endif
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(in_array($role, ['superadmin', 'admin', 'petugas']))
                <x-responsive-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')">
                    {{ __('Pasien') }}
                </x-responsive-nav-link>
            @endif

            @if($role === 'petugas')
                <x-responsive-nav-link :href="route('registrations.index')" :active="request()->routeIs('registrations.*')">
                    {{ __('Pendaftaran') }}
                </x-responsive-nav-link>
            @endif

            @if($role === 'dokter')
                <x-responsive-nav-link :href="route('medical-records.index')" :active="request()->routeIs('medical-records.*')">
                    {{ __('Rekam Medis') }}
                </x-responsive-nav-link>
            @endif

            @if(in_array($role, ['superadmin', 'admin']))
                <x-responsive-nav-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">
                    {{ __('Audit Log') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('account-users.index')" :active="request()->routeIs('account-users.*')">
                    {{ __('Akun Dokter/Petugas') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="flex items-center gap-2">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    @if($roleLabel)
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-emerald-700">
                            {{ $roleLabel }}
                        </span>
                    @endif
                </div>
                <div class="font-medium text-sm text-gray-500">Username: {{ Auth::user()->username }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
