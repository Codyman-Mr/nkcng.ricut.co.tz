@extends('layouts.base')


@section('content')
    @php
        $initials =
            strtoupper(substr(Auth::user()->first_name, 0, 1)) . strtoupper(substr(Auth::user()->last_name, 0, 1));
    @endphp
    <div class="w-full h-full">
        <dh-component>

            <!-- Navbar -->
            <nav class="fixed top-0 left-0 right-4 w-full h-16 bg-white shadow z-20" x-data="{ isOpen: false }">
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
                            <div class="w-12 h-6 bg-red border-r border-gray-200 text-gray-600 flex items-baseline">
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
                                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
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
                <!-- Mobile menu -->
                <div class="sm:hidden flex w-full h-24" x-show="isOpen">
                    <div class="px-2 pt-2 pb-3 space-y-1 bg-gray-900 text-gray-200">
                        <a href="#" class="block  hover:text-gray-600">Home</a>

                        <a href="#" class="block  hover:text-gray-600">Contact</a>
                    </div>
                </div>
            </nav>

            <div class="flex flex-no-wrap m-0 p-0">
                <!-- Sidebar starts -->
                <div style="min-height: 100vh"
                    class="w-64 fixed sm:relative bg-gray-800 shadow md:h-full flex-col justify-between hidden sm:flex z-30">
                    <div class="px-8">
                        <div class="h-16 w-full flex items-center text-gray-100 text-xl border-b border-gray-700">
                            <i class="bi bi-app-indicator  py-1 "></i>
                            <h1 class="font-bold text-gray-300 text-[1.5rem] ml-4">
                                <a href="/">Nkcng</a>
                            </h1>
                        </div>
                        <ul class="mt-6">
                            <li class="flex w-full justify-between text-gray-300 cursor-pointer items-center mb-6">
                                <a href="/"
                                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-grid"
                                        width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z"></path>
                                        <rect x="4" y="4" width="6" height="6" rx="1"></rect>
                                        <rect x="14" y="4" width="6" height="6" rx="1"></rect>
                                        <rect x="4" y="14" width="6" height="6" rx="1"></rect>
                                        <rect x="14" y="14" width="6" height="6" rx="1"></rect>
                                    </svg>
                                    <span class="text-sm ml-2">Dashboard</span>
                                </a>
                                <div
                                    class="py-1 px-3 bg-gray-600 rounded text-gray-300 flex items-center justify-center text-xs hidden">
                                    5</div>
                            </li>
                            <li
                                class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center mb-6">
                                <a href="/loan-application"
                                    class="flex items-center focus:outline-none focus:ring-8 focus:ring-white">
                                    <i class="bi bi-pencil-fill text-sm"></i>
                                    <span class="text-sm ml-2">Apply Loan</span>
                                </a>
                                <div
                                    class="py-1 px-3 bg-gray-600 rounded text-gray-300 flex items-center justify-center text-xs hidden">
                                    8</div>
                            </li>
                            <li
                                class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center mb-6">
                                <a href="/loans-pending"
                                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-white">
                                    <i class="bi bi-hourglass-split text-sm"></i>
                                    <span class="text-sm ml-2">Pending Loans</span>
                                </a>
                            </li>
                            <li
                                class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center mb-6">
                                <a href="/loans-ongoing"
                                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-white">
                                    <i class="bi bi-file-earmark-text-fill text-sm"></i>
                                    <span class="text-sm ml-2">Ongoing Loans</span>
                                </a>
                            </li>
                            <li
                                class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center mb-6">
                                <a href="/repayment-alerts"
                                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-white">
                                    <i class="bi bi-chat-dots-fill text-sm"></i>
                                    <span class="text-sm ml-2">Loan Repayment</span>
                                </a>
                                <div
                                    class="py-1 px-3 bg-gray-600 rounded text-gray-300 flex items-center justify-center text-xs hidden">
                                    25</div>
                            </li>
                            <li
                                class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center mb-6">
                                <a href="/report"
                                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-white">
                                    <i class="bi bi-file-bar-graph-fill text-sm"></i>
                                    <span class="text-sm ml-2">Reports</span>
                                </a>
                            </li>
                            <li
                                class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center">
                                <a href="/users"
                                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-white">
                                    <i class="bi bi-person-fill text-sm"></i>
                                    <span class="text-sm ml-2">Users</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div
                        class="p-2.5 mt-4 flex items-center text-white    cursor-pointer transition-all duration-300  hover:bg-slate-500 border-t border-gray-700">
                        <i class="bi bi-door-closed-fill text-sm"></i>
                        <span class="text-[15px] ml-4 text-gray-300 ">Logout</span>
                    </div>


                </div>
                <div class="w-64 min-h-screen z-40 absolute bg-gray-800 shadow md:h-screen flex-col justify-between sm:hidden transition duration-150 ease-in-out"
                    id="mobile-nav">
                    <button aria-label="toggle sidebar" id="openSideBar"
                        class="h-10 w-10 bg-gray-800 absolute right-0 mt-16 -mr-10 flex items-center shadow rounded-tr rounded-br justify-center cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 rounded focus:ring-gray-800"
                        onclick="sidebarHandler(true)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-adjustments"
                            width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFFFFF"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" />
                            <circle cx="6" cy="10" r="2" />
                            <line x1="6" y1="4" x2="6" y2="8" />
                            <line x1="6" y1="12" x2="6" y2="20" />
                            <circle cx="12" cy="16" r="2" />
                            <line x1="12" y1="4" x2="12" y2="14" />
                            <line x1="12" y1="18" x2="12" y2="20" />
                            <circle cx="18" cy="7" r="2" />
                            <line x1="18" y1="4" x2="18" y2="5" />
                            <line x1="18" y1="9" x2="18" y2="20" />
                        </svg>
                    </button>
                    <button aria-label="Close sidebar" id="closeSideBar"
                        class=" h-10 w-10 bg-gray-800 absolute right-0 mt-16 -mr-10 flex items-center shadow rounded-tr rounded-br justify-center cursor-pointer text-white"
                        onclick="sidebarHandler(false)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="20"
                            height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" />
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </button>
                    <div class="px-8">
                        <div class="h-16 w-full  border-b border-gray-600 flex items-center text-gray-100 text-xl">
                            <i class="bi bi-app-indicator  py-1 "></i>
                            <h1 class="font-bold text-gray-300 text-[1.5rem] ml-4">
                                <a href="/">Nkcng</a>
                            </h1>
                        </div>
                        <ul class="mt-12">
                            <li
                                class="flex w-full justify-between text-gray-300 hover:text-gray-500 cursor-pointer items-center mb-6">
                                <a href="/"
                                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-grid"
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
                                <div
                                    class="py-1 px-3 bg-gray-600 rounded text-gray-300 flex items-center justify-center text-xs hidden">
                                    5</div>
                            </li>
                            <li
                                class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center mb-6">
                                <a href="/loan-application"
                                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-white">
                                    <i class="bi bi-pencil-fill text-sm"></i>
                                    <span class="text-sm ml-2">Apply Loan</span>
                                </a>
                                <div
                                    class="py-1 px-3 bg-gray-600 rounded text-gray-300 flex items-center justify-center text-xs hidden">
                                    8</div>
                            </li>
                            <li
                                class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center mb-6">
                                <a href="/loans-pending"
                                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-white">
                                    <i class="bi bi-hourglass-split text-sm"></i>
                                    <span class="text-sm ml-2">Pending Loans</span>
                                </a>
                            </li>
                            <li
                                class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center mb-6">
                                <a href="/loans-ongoing"
                                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-white">
                                    <i class="bi bi-file-earmark-text-fill text-sm"></i>
                                    <span class="text-sm ml-2">Ongoing Loans</span>
                                </a>
                            </li>
                            <li
                                class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center mb-6">
                                <a href="/repayment-alerts"
                                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-white">
                                    <i class="bi bi-chat-dots-fill text-sm"></i>
                                    <span class="text-sm ml-2">Loan Repayment</span>
                                </a>
                                <div
                                    class="py-1 px-3 bg-gray-600 rounded text-gray-300 flex items-center justify-center text-xs hidden">
                                    25</div>
                            </li>
                            <li
                                class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center mb-6">
                                <a href="/reports"
                                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-white">
                                    <i class="bi bi-file-bar-graph-fill text-sm"></i>
                                    <span class="text-sm ml-2">Reports</span>
                                </a>
                            </li>
                            <li
                                class="flex w-full justify-between text-gray-400 hover:text-gray-300 cursor-pointer items-center">
                                <a href="/users"
                                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-white">
                                    <i class="bi bi-person-fill text-sm"></i>
                                    <span class="text-sm ml-2">Users</span>
                                </a>
                            </li>
                        </ul>



                    </div>
                    <div
                        class="p-2.5  flex items-center text-white    cursor-pointer transition-all duration-300  hover:bg-slate-500 border-t border-gray-700">
                        <i class="bi bi-door-closed-fill text-sm"></i>
                        <span class="text-[15px] ml-4 text-gray-300 ">Logout</span>
                    </div>
                </div>
                <!-- Sidebar ends -->
                <!-- Main content starts -->
                <div class="container mx-2 px-1 flex justify-center  py-20  rounded  w-full h-screen  overflow-y-auto">

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
@endsection
