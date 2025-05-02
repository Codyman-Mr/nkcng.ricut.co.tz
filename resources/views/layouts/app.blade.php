

@extends('layouts.base')

@section('content')
    @php
        $initials =
            strtoupper(substr(Auth::user()->first_name, 0, 1)) . strtoupper(substr(Auth::user()->last_name, 0, 1));
    @endphp

    <div x-data="{
        sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen')) ?? false,
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebarOpen', JSON.stringify(this.sidebarOpen));
        }
    }" class="flex h-screen bg-gray-100">

        <!-- Overlay on mobile -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-black bg-opacity-50 sm:hidden"
            @click="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-800 text-white transform transition-transform duration-300 ease-in-out sm:relative sm:translate-x-0 sm:flex sm:flex-col"
            :class="{ '-translate-x-full': !sidebarOpen }">
            <div class="h-16 flex items-center px-8 border-b border-gray-700">

                {{-- <span class="text-xl font-bold"><a href="/">Nkcng</a></span> --}}
                <i class="bi bi-app-indicator  py-1 "></i>
                <h1 class="font-bold text-gray-300 text-[1.5rem] ml-4">
                    <a href="/">Nkcng</a>
                </h1>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto z-1000 shadow-sm">
                @php
                    function isActive($route)
                    {
                        return request()->is($route) || request()->is($route . '/*') ? 'bg-gray-700' : '';
                    }
                @endphp
                <a href="/" class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/') }}">
                    <i class="bi bi-grid text-lg"></i>
                    <span class="ml-3 text-sm">Dashboard</span>
                </a>
                <a href="/loan-application" class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/loans-ongoing') }}">
                    <i class="bi bi-pencil-fill text-lg"></i>
                    <span class="ml-3 text-sm">Apply Loan</span>
                </a>

                @if (auth()->user()->isAdmin())
                    <a href="/loans-pending" class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/loans-ongoing') }}">
                        <i class="bi bi-hourglass-split text-lg"></i>
                        <span class="ml-3 text-sm">Pending Loans</span>
                    </a>
                    <a href="/loans-ongoing"
                        class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/loans-ongoing') }}">
                        <i class="bi bi-file-earmark-text-fill text-lg"></i>
                        <span class="ml-3 text-sm">Ongoing Loans</span>
                    </a>
                    <a href="/repayment-alerts" class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/loans-ongoing') }}">
                        <i class="bi bi-chat-dots-fill text-lg"></i>
                        <span class="ml-3 text-sm">Loan Repayment</span>
                    </a>
                    <a href="/report" class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/loans-ongoing') }}">
                        <i class="bi bi-file-bar-graph-fill text-lg"></i>
                        <span class="ml-3 text-sm">Reports</span>
                    </a>
                    <a href="/users" class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/loans-ongoing') }}">
                        <i class="bi bi-person-fill text-lg"></i>
                        <span class="ml-3 text-sm">Users</span>
                    </a>

                    <!-- comant this after -->
                    <a href="/testing" class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/loans-ongoing') }}">
                        <i class="bi bi-wrench-adjustable text-lg"></i>
                        <span class="ml-3 text-sm">Testing</span>
                    </a>
                @endif
            </nav>

            <!-- Logout -->
            <form method="POST" action="{{ route('user-logout') }}" class="border-t border-gray-700 p-4">
                @csrf
                <button type="submit" class="flex items-center px-3 py-2 rounded hover:bg-gray-700 w-full text-left">
                    <i class="bi bi-door-closed-fill text-lg"></i>
                    <span class="ml-3 text-sm">Logout</span>
                </button>
            </form>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col w-full overflow-hidden">
            <!-- Navbar -->
            <header class="h-16 bg-white shadow-md flex items-center justify-between px-4 sm:px-6">
                <button class="sm:hidden text-gray-600" @click="toggleSidebar()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <h1 class="text-lg font-semibold text-gray-700">@yield('title')</h1>

                <div class="flex items-center gap-4">
                    <img src="{{ asset('svg/notification-bell.svg') }}" alt="Notifications" class="w-5 h-5" />
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium">{{ $initials }}</span>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                @yield('main-content')
            </main>
        </div>
    </div>

    @isset($slot)
        {{ $slot }}
    @endisset
@endsection
