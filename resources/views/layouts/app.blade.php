{{-- @extends('layouts.base')


@section('content')
    @php
        $initials =
            strtoupper(substr(Auth::user()->first_name, 0, 1)) . strtoupper(substr(Auth::user()->last_name, 0, 1));
    @endphp
    <div class="w-full h-full">
        <dh-component>

            <!-- Navbar -->
            <nav class="fixed top-0 left-0 right-4 w-full h-16 bg-white shadow-md z-10" x-data="{ isOpen: false }">
                <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                    <div class="text-lg font-semibold text-gray-800 ml-64">
                        @yield('title')
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Hamburger menu for mobile -->
                        <button class="sm:hidden focus:outline-none" @click="isOpen = !isOpen">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16m-7 6h7"></path>
                            </svg>
                        </button>
                        <!-- Nav links -->
                        <div class="hidden sm:flex space-x-4 ">
                            <div class="w-12 h-6 bg-red border-r border-gray-300 text-gray-600 flex items-baseline">
                                <img src="{{ asset('svg/notification-bell.svg') }}" alt=""
                                    class="w-6 h-6 text-gray-700">
                            </div>
                            <a href="#" class="text-gray-800 hover:text-gray-900">
                                <div
                                    class="relative inline-flex items-center justify-center w-6 h-6 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $initials }}</span>
                                </div>

                                <button id="dropdownDelayButton" data-dropdown-toggle="dropdownDelay"
                                    data-dropdown-delay="500" data-dropdown-trigger="hover"
                                    class="text-gray-800  hover:text-gray-800  focus:outline-none  font-medium rounded-lg text-md  text-center inline-flex items-center "
                                    type="button">
                                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/3000/svg"
                                        fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg>
                                </button>

                                <!-- Dropdown menu -->
                                <div id="dropdownDelay"
                                    class="z-10 hidden bg-white divide-y divide-gray-400 rounded-lg shadow w-44 dark:bg-gray-700">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-700"
                                        aria-labelledby="dropdownDelayButton">
                                        <li>
                                            <a href="#"
                                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Profile</a>
                                        </li>
                                        <li>
                                            <a href="#"
                                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Settings</a>
                                        </li>
                                        <li>
                                            <a href="#"
                                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Sign
                                                out</a>
                                        </li>
                                    </ul>
                                </div>

                            </a>
                        </div>
                    </div>
                </div>

            </nav>

            <div class="flex flex-no-wrap m-0 p-0">
                <!-- Sidebar starts -->

                <div class="flex flex-no-wrap m-0 p-0">
                    <!-- Sidebar starts -->
                    <div style="min-height: 100vh"
                        class="w-60 fixed sm:relative bg-gray-800 shadow md:h-full flex-col justify-between hidden sm:flex z-20">
                        <div class="px-8">
                            <div class="h-16 w-full flex items-center text-gray-100 text-xl border-b border-gray-700">
                                <i class="bi bi-app-indicator  py-1 "></i>
                                <h1 class="font-bold text-gray-300 text-[1.5rem] ml-4">
                                    <a href="/">Nkcng</a>
                                </h1>
                            </div>
                            <ul class="mt-6">
                                <li
                                    class="block  py-2 text-gray-300 transition-all duration-300 ease-out hover:translate-x-2 hover:text-gray-800 hover:bg-gray-100/50 hover:pl-2 rounded-sm mb-2">
                                    <a href="/" class="flex items-center focus:outline-none">
                                        <svg xmlns="http://www.w3.org/3000/svg" class="icon icon-tabler icon-tabler-grid"
                                            width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"></path>
                                            <rect x="4" y="4" width="6" height="6" rx="1"></rect>
                                            <rect x="14" y="4" width="6" height="6" rx="1"></rect>
                                            <rect x="4" y="14" width="6" height="6" rx="1"></rect>
                                            <rect x="14" y="14" width="6" height="6" rx="1"></rect>
                                        </svg>
                                        <span class="text-sm ml-2">Dashboard</span>
                                    </a>
                                </li>
                                <li
                                    class="block  py-2 text-gray-300 transition-all duration-300 ease-out hover:translate-x-2 hover:text-gray-800 hover:bg-gray-100/50 hover:pl-2 rounded-sm mb-2">
                                    <a href="/loan-application" class="flex items-center focus:outline-none ">
                                        <i class="bi bi-pencil-fill text-sm"></i>
                                        <span class="text-sm ml-2">Apply Loan</span>
                                    </a>
                                </li>

                                @if (auth()->user()->isAdmin())
                                    <!-- Admin-only menu items -->
                                    <li
                                        class="block  py-2 text-gray-300 transition-all duration-300 ease-out hover:translate-x-2 hover:text-gray-800 hover:bg-gray-100/50 hover:pl-2 rounded-sm mb-2">
                                        <a href="/loans-pending" class="flex items-center focus:outline-none ">
                                            <i class="bi bi-hourglass-split text-sm"></i>
                                            <span class="text-sm ml-2">Pending Loans</span>
                                        </a>
                                    </li>
                                    <li
                                        class="block  py-2 text-gray-300 transition-all duration-300 ease-out hover:translate-x-2 hover:text-gray-800 hover:bg-gray-100/50 hover:pl-2 rounded-sm mb-2">
                                        <a href="/loans-ongoing" class="flex items-center focus:outline-none ">
                                            <i class="bi bi-file-earmark-text-fill text-sm"></i>
                                            <span class="text-sm ml-2">Ongoing Loans</span>
                                        </a>
                                    </li>
                                    <li
                                        class="block  py-2 text-gray-300 transition-all duration-300 ease-out hover:translate-x-2 hover:text-gray-800 hover:bg-gray-100/50 hover:pl-2 rounded-sm mb-2">
                                        <a href="/repayment-alerts" class="flex items-center focus:outline-none ">
                                            <i class="bi bi-chat-dots-fill text-sm"></i>
                                            <span class="text-sm ml-2">Loan Repayment</span>
                                        </a>
                                    </li>
                                    <li
                                        class="block  py-2 text-gray-300 transition-all duration-300 ease-out hover:translate-x-2 hover:text-gray-800 hover:bg-gray-100/50 hover:pl-2 rounded-sm mb-2">
                                        <a href="/report" class="flex items-center focus:outline-none ">
                                            <i class="bi bi-file-bar-graph-fill text-sm"></i>
                                            <span class="text-sm ml-2">Reports</span>
                                        </a>
                                    </li>
                                    <li
                                        class="block  py-2 text-gray-300 transition-all duration-300 ease-out hover:translate-x-2 hover:text-gray-800 hover:bg-gray-100/50 hover:pl-2 rounded-sm mb-2">
                                        <a href="/users" class="flex items-center focus:outline-none  ">
                                            <i class="bi bi-person-fill text-sm"></i>
                                            <span class="text-sm ml-2">Users</span>
                                        </a>
                                    </li>
                                    <li class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center mb-6">
                                        class="block  py-2 text-gray-300 transition-all duration-300 ease-out hover:translate-x-2 hover:text-gray-800 hover:bg-gray-100/50 hover:pl-2 rounded-sm mb-2">
                                        <a href="/testing" class="flex items-center focus:outline-none ">
                                            <i class="bi bi-hourglass-split text-sm"></i>
                                            <span class="text-sm ml-2">Testing</span>
                                        </a>
                                    </li>
                                    <!-- <div x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" x-init="if (darkMode) {
                                        document.documentElement.classList.add('dark');
                                    } else {
                                        document.documentElement.classList.remove('dark');
                                    }">
                                        <button
                                            @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', darkMode)">
                                            <span class="text-white"
                                                x-text="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"></span>
                                        </button>
                                    </div> -->
                                @endif
                            </ul>
                        </div>
                        <form method="POST" action="{{ route('user-logout') }}">
                            @csrf
                            <button type="submit"
                                class="p-2.5 my-4 w-full flex  items-center text-white cursor-pointer transition-all duration-300 hover:bg-slate-500 border-t border-gray-700">
                                <i class="bi bi-door-closed-fill text-sm"></i>
                                <span class="text-[15px] ml-4 text-gray-300">Logout</span>
                            </button>
                        </form>
                    </div>

                    <!-- Mobile sidebar (similar changes applied) -->
                    <div class="w-64 min-h-screen z-40 absolute bg-gray-800 shadow md:h-screen flex-col justify-between sm:hidden transition duration-150 ease-in-out"
                        id="mobile-nav">
                        <!-- ... mobile header ... -->
                        <ul class="mt-12">
                            <li
                                class="flex w-full justify-between text-gray-300 hover:text-gray-500 cursor-pointer items-center mb-6">
                                <!-- Dashboard -->
                            </li>
                            <li
                                class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center mb-6">
                                <!-- Apply Loan -->
                            </li>

                            @if (auth()->user()->isAdmin())
                                <!-- Admin-only mobile menu items -->
                                <li
                                    class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center mb-6">
                                    <!-- Pending Loans -->
                                </li>
                                <!-- ... other admin menu items ... -->
                            @endif
                        </ul>
                        <!-- ... logout button ... -->
                    </div>
                    <!-- Sidebar ends -->
                    <!-- Main content -->
                </div>


                <!-- Sidebar ends -->
                <!-- Main content starts -->
                <div class="container mx-2 px-1 flex justify-center  py-20  rounded  w-full h-screen  overflow-y-scroll scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-transparent hover:scrollbar-thumb-gray-500">

                    <div class="w-full h-full rounded ">

                        @yield('main-content')
                    </div>
                </div>
            </div>
            <script>
                var sideBar = document.getElementById("mobile-nav");
                var openSidebar = document.getElementById("openSideBar");
                var closeSidebar = document.getElementById("closeSideBar");
                sideBar.style.transform = "translateX(-260px)";

                function sidebarHandler(flag) {
                    if (flag) {
                        sideBar.style.transform = "translateX(0px)";
                        openSidebar.classList.add("hidden");
                        closeSidebar.classList.remove("hidden");
                    } else {
                        sideBar.style.transform = "translateX(-260px)";
                        closeSidebar.classList.add("hidden");
                        openSidebar.classList.remove("hidden");
                    }
                }
            </script>

        </dh-component>
    </div>


    @isset($slot)
        {{ $slot }}
    @endisset
@endsection --}}


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
