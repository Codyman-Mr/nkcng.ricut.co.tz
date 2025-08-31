@extends('layouts.base')

@section('content')
    @php
        $initials =
            strtoupper(substr(Auth::user()->first_name, 0, 1)) . strtoupper(substr(Auth::user()->last_name, 0, 1));
        // Check if user can apply for a loan (no loan or only rejected loans)
        $userId = Auth::id();
        $canApplyLoan =
            \DB::table('loans')->where('user_id', $userId)->doesntExist() ||
            \DB::table('users')->where('role','admin')->exists()||
            \DB::table('loans')->where('user_id', $userId)->where('status', 'rejected')->exists();
        // Session-based user ID check to detect new logins
        $sessionUserId = session('current_user_id', $userId);
        if ($sessionUserId !== $userId) {
            // New login detected, log out and redirect with warning
            Auth::logout();
            session()->flush();
            return redirect('/login')->with(
                'warning',
                'You were logged out because a different user logged in on another tab.',
            );
        }
        // Store current user ID in session
        session(['current_user_id' => $userId]);
    @endphp

    <div x-data="{
        sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen')) ?? false,
        sidebarCollapsed: JSON.parse(localStorage.getItem('sidebarCollapsed')) ?? false,
        toggleSidebar() {
            if (window.innerWidth >= 640) { // sm: breakpoint
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('sidebarCollapsed', JSON.stringify(this.sidebarCollapsed));
            } else {
                this.sidebarOpen = !this.sidebarOpen;
                this.sidebarCollapsed = false; // Ensure not collapsed on mobile
                localStorage.setItem('sidebarOpen', JSON.stringify(this.sidebarOpen));
                localStorage.setItem('sidebarCollapsed', JSON.stringify(this.sidebarCollapsed));
            }
        },
        openSidebar() {
            this.sidebarOpen = true;
            this.sidebarCollapsed = false; // Ensure fully open on mobile
            localStorage.setItem('sidebarOpen', JSON.stringify(this.sidebarOpen));
            localStorage.setItem('sidebarCollapsed', JSON.stringify(this.sidebarCollapsed));
        }
    }" class="flex h-screen bg-gray-100">

        <!-- Display warning if present -->
        @if (session('warning'))
            <div class="fixed top-4 right-4 bg-yellow-500 text-white px-4 py-2 rounded shadow-lg z-50" x-data="{ show: true }"
                x-show="show" x-transition.opacity @click="show = false">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Debug State (remove after testing) -->
        <div class="hidden"
            x-text="JSON.stringify({ sidebarOpen, sidebarCollapsed, width: window.innerWidth, userId: {{ $userId }} })">
        </div>

        <!-- Overlay on mobile -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-black bg-opacity-50 sm:hidden"
            @click="sidebarOpen = false; localStorage.setItem('sidebarOpen', JSON.stringify(sidebarOpen))"></div>

        <!-- Sidebar -->
        <aside
            class="z-30 bg-gray-800 text-white transition-all duration-300 ease-in-out flex flex-col !bg-gray-800 !text-white"
            :class="{
                'fixed inset-y-0 left-0 w-64 z-40': window.innerWidth < 640 && sidebarOpen,
                'hidden': window.innerWidth < 640 && !sidebarOpen,
                'sm:relative sm:w-64 z-30': window.innerWidth >= 640 && !sidebarCollapsed,
                'sm:relative sm:w-16 z-30': window.innerWidth >= 640 && sidebarCollapsed
            }">

            <div class="h-16 flex items-center px-4 border-b border-gray-700">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" id="logo"
                    :class="{ 'h-10': !sidebarCollapsed, 'hidden': sidebarCollapsed }" />
                <h1 class="font-bold text-gray-300 text-[1.5rem] ml-4" :class="{ 'hidden': sidebarCollapsed }">
                    <a href="/">Nkcng</a>
                </h1>
                <button @click="toggleSidebar()" class="ml-auto focus:outline-none">
                    <i
                        :class="{
                            'bi': true,
                            'bi-arrow-bar-left': !sidebarCollapsed,
                            'bi-arrow-bar-right': sidebarCollapsed,
                            'text-2xl': !sidebarCollapsed,
                            'text-sm': sidebarCollapsed
                        }"></i>
                </button>
            </div>

            <nav class="flex-1 px-2 py-6 space-y-4 overflow-y-auto shadow-sm">
                @php
                    function isActive($route)
                    {
                        $currentPath = request()->path();
                        $routePath = trim($route, '/');
                        return $currentPath === $routePath || str_starts_with($currentPath, $routePath . '/')
                            ? 'bg-gray-700 !bg-gray-700'
                            : '';
                    }
                @endphp
                <div class="relative group">
                    <a href="/"
                        class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/') }}"
                        :class="{ 'justify-center': sidebarCollapsed }">
                        <i class="bi bi-grid text-lg"></i>
                        <span class="ml-3 text-sm" :class="{ 'hidden': sidebarCollapsed }">Dashboard</span>
                    </a>
                    <div x-show="sidebarCollapsed" x-transition.opacity
                        class="absolute left-full top-0 ml-2 bg-gray-900 text-white text-sm px-2 py-1 rounded shadow-lg hidden !hidden group-hover:block !group-hover:block z-50">
                        Dashboard
                    </div>
                </div>

                @if ($canApplyLoan)
                    <div class="relative group">
                        <a href="/loan-application"
                            class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/loan-application') }}"
                            :class="{ 'justify-center': sidebarCollapsed }">
                            <i class="bi bi-pencil-fill text-lg"></i>
                            <span class="ml-3 text-sm" :class="{ 'hidden': sidebarCollapsed }">Apply Loan</span>
                        </a>
                        <div x-show="sidebarCollapsed" x-transition.opacity
                            class="absolute left-full top-0 ml-2 bg-gray-900 text-white text-sm px-2 py-1 rounded shadow-lg hidden !hidden group-hover:block !group-hover:block z-50">
                            Apply Loan
                        </div>
                    </div>
                @endif

                @if (auth()->user()->isAdmin())
                    <div class="relative group">
                        <!-- <a href="/loans-pending"
                            class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/loans-pending') }}"
                            :class="{ 'justify-center': sidebarCollapsed }">
                            <i class="bi bi-hourglass-split text-lg"></i>
                            <span class="ml-3 text-sm" :class="{ 'hidden': sidebarCollapsed }">Pending Loans</span>
                        </a> -->
                        <div x-show="sidebarCollapsed" x-transition.opacity
                            class="absolute left-full top-0 ml-2 bg-gray-900 text-white text-sm px-2 py-1 rounded shadow-lg hidden !hidden group-hover:block !group-hover:block z-50">
                            Pending Loans
                        </div>
                    </div>
                    <div class="relative group">
                        <a href="/loans-ongoing"
                            class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/loans-ongoing') }}"
                            :class="{ 'justify-center': sidebarCollapsed }">
                            <i class="bi bi-file-earmark-text-fill text-lg"></i>
                            <span class="ml-3 text-sm" :class="{ 'hidden': sidebarCollapsed }">Ongoing Loans</span>
                        </a>
                        <div x-show="sidebarCollapsed" x-transition.opacity
                            class="absolute left-full top-0 ml-2 bg-gray-900 text-white text-sm px-2 py-1 rounded shadow-lg hidden !hidden group-hover:block !group-hover:block z-50">
                            Ongoing Loans
                        </div>
                    </div>
                    <div class="relative group">
                        <a href="/repayment-alerts"
                            class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/repayment-alerts') }}"
                            :class="{ 'justify-center': sidebarCollapsed }">
                            <i class="bi bi-chat-dots-fill text-lg"></i>
                            <span class="ml-3 text-sm" :class="{ 'hidden': sidebarCollapsed }">Loan Repayment</span>
                        </a>
                        <div x-show="sidebarCollapsed" x-transition.opacity
                            class="absolute left-full top-0 ml-2 bg-gray-900 text-white text-sm px-2 py-1 rounded shadow-lg hidden !hidden group-hover:block !group-hover:block z-50">
                            Loan Repayment
                        </div>
                    </div>
                    <div class="relative group">
                        <a href="/report"
                            class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/report') }}"
                            :class="{ 'justify-center': sidebarCollapsed }">
                            <i class="bi bi-file-bar-graph-fill text-lg"></i>
                            <span class="ml-3 text-sm" :class="{ 'hidden': sidebarCollapsed }">Reports</span>
                        </a>
                        <div x-show="sidebarCollapsed" x-transition.opacity
                            class="absolute left-full top-0 ml-2 bg-gray-900 text-white text-sm px-2 py-1 rounded shadow-lg hidden !hidden group-hover:block !group-hover:block z-50">
                            Reports
                        </div>
                    </div>

                    <div class="relative group">
                        <a href="/gps-devices"
                            class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/gps-devices') }}"
                            :class="{ 'justify-center ': sidebarCollapsed  }">
                            <img src="{{ asset('svg/gps-filled-white.svg') }}" alt="money stack"
                                class="w-6 h-6 object-cover">
                            <span class="ml-3 text-sm" :class="{ 'hidden': sidebarCollapsed }">Gps Devices</span>
                        </a>
                        <div x-show="sidebarCollapsed" x-transition.opacity
                            class="absolute left-full top-0 ml-2 bg-gray-900 text-white text-sm px-2 py-1 rounded shadow-lg hidden !hidden group-hover:block !group-hover:block z-50">
                            Gps Devices
                        </div>
                    </div>

                    <div class="relative group">
                        <!-- <a href="/installations"
                            class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/installations') }}"
                            :class="{ 'justify-center ': sidebarCollapsed  }">
                            <i class="bi bi-wrench-adjustable-circle-fill"></i>
                            <span class="ml-3 text-sm" :class="{ 'hidden': sidebarCollapsed }">Installations</span>
                        </a> -->
                        <div x-show="sidebarCollapsed" x-transition.opacity
                            class="absolute left-full top-0 ml-2 bg-gray-900 text-white text-sm px-2 py-1 rounded shadow-lg hidden !hidden group-hover:block !group-hover:block z-50">
                            Installations
                        </div>
                    </div>

                    <div class="relative group">
                        <a href="/users"
                            class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/users') }}"
                            :class="{ 'justify-center': sidebarCollapsed }">
                            <i class="bi bi-person-fill text-lg"></i>
                            <span class="ml-3 text-sm" :class="{ 'hidden': sidebarCollapsed }">Users</span>
                        </a>
                        <div x-show="sidebarCollapsed" x-transition.opacity
                            class="absolute left-full top-0 ml-2 bg-gray-900 text-white text-sm px-2 py-1 rounded shadow-lg hidden !hidden group-hover:block !group-hover:block z-50">
                            Users
                        </div>
                    </div>
                    {{-- <div class="relative group">
                        <a href="/testing"
                            class="flex items-center px-3 py-2 rounded transition-transform duration-300 ease-in-out transform hover:scale-105 hover:bg-gray-700 {{ isActive('/testing') }}"
                            :class="{ 'justify-center': sidebarCollapsed }">
                            <i class="bi bi-wrench-adjustable text-lg"></i>
                            <span class="ml-3 text-sm" :class="{ 'hidden': sidebarCollapsed }">Testing</span>
                        </a>
                        <div x-show="sidebarCollapsed" x-transition.opacity
                            class="absolute left-full top-0 ml-2 bg-gray-900 text-white text-sm px-2 py-1 rounded shadow-lg hidden !hidden group-hover:block !group-hover:block z-50">
                            Testing
                        </div>
                    </div> --}}
                @endif
            </nav>
  <li class="nav-item">
    <a class="nav-link" href="{{ route('password.edit') }}">
        Change Password
    </a>
</li>

            <!-- Logout -->
            <div class="relative group border-t border-gray-700 p-4">
                <form method="POST" action="{{ route('user-logout') }}"
                    class="flex items-center px-3 py-2 rounded hover:bg-gray-700 w-full text-left"
                    :class="{ 'justify-center': sidebarCollapsed }">
                    @csrf
                    <button type="submit">
                        <i class="bi bi-door-closed-fill text-lg"></i>
                        <span class="ml-3 text-sm" :class="{ 'hidden': sidebarCollapsed }">Logout</span>
                    </button>
                </form>
                <div x-show="sidebarCollapsed" x-transition.opacity
                    class="absolute left-full top-0 ml-2 bg-gray-900 text-white text-sm px-2 py-1 rounded shadow-lg hidden !hidden group-hover:block !group-hover:block z-50">
                    Logout
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col w-full overflow-hidden z-10">
            <!-- Navbar -->
            <header class="h-16 bg-white shadow-md flex items-center justify-between px-4 sm:px-6 z-20">
                <button class=" sm:hidden text-gray-600" :class="{'hidden':sidebarCollapsed}" @click="openSidebar()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
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
