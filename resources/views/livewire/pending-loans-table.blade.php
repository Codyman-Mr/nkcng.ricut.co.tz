<div>
   <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content table-responsive border-0">

                        @unless (count($loans) == 0)
                            <div class="mt-4">
                                <section class="py-3 sm:py-5">
                                    <div class="px-4 mx-auto max-w-screen-2xl lg:px-12">
                                        <div
                                            class="relative overflow-hidden bg-gradient-to-r from-purple-600 via-indigo-700 to-blue-700 shadow-lg rounded-lg dark:bg-gray-900">
                                            <div x-data="loanSearch()" @keydown.window.ctrl.k.prevent="focusSearch"
                                                @keydown.window.meta.k.prevent="focusSearch"
                                                class="flex flex-col items-center justify-between gap-4 px-6 py-4 bg-gradient-to-r from-indigo-800 via-purple-900 to-indigo-900 rounded-t-lg lg:flex-row lg:gap-6">
                                                {{-- Title --}}
                                                <div class="flex items-center flex-1 px-2">
                                                    <h4 class="text-lg font-extrabold text-white tracking-wide drop-shadow-lg">
                                                        Pending Loans
                                                    </h4>
                                                </div>
                                                {{-- Live Search Bar --}}
                                                <div class="flex-1 w-full max-w-md mt-3">
                                                    <div class="relative">
                                                        <label for="search-loans" class="sr-only">Search </label>

                                                        <span
                                                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                                            <svg class="w-5 h-5 text-purple-300 dark:text-purple-400"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M21 21l-4.35-4.35M10.5 17a6.5 6.5 0 1 1 0-13 6.5 6.5 0 0 1 0 13z" />
                                                            </svg>
                                                        </span>

                                                        <input type="text" id="search-loans"
                                                            wire:model.debounce.500ms="search"
                                                            class="w-full py-2.5 ps-10 pe-24 text-sm text-indigo-900 border border-purple-300 rounded-lg bg-purple-50 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 dark:bg-gray-800 dark:border-indigo-700 dark:text-purple-200 dark:placeholder-purple-400 dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                                                            placeholder="Search loans..." />

                                                        <button type="button" wire:click="$refresh"
                                                            class="absolute end-1.5 top-1.5 px-4 py-1.5 text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-offset-1 focus:ring-indigo-400">
                                                            Search
                                                        </button>

                                                    </div>
                                                </div>

                                                <div class="flex-1 w-full"></div>
                                            </div>
                                            <div class="overflow-x-scroll scrollbar-thin scrollbar-thumb-purple-500 scrollbar-track-purple-200 hover:scrollbar-thumb-purple-600">
                                                <table class="w-full text-sm text-left text-purple-900 dark:text-purple-300">
                                                    <thead
                                                        class="text-xs text-start uppercase bg-gradient-to-r from-purple-300 via-indigo-400 to-purple-400 text-white drop-shadow-md">
                                                        <tr>
                                                            <th scope="col" class="px-6 py-3"
                                                                style="width: 17rem; font-weight:700;">
                                                                Name
                                                            </th>
                                                            <th scope="col" class="px-6 py-3"
                                                                style="width: 15rem; font-weight:700;">
                                                                Phone Number
                                                            </th>
                                                            <th scope="col" class="px-6 py-3 text-left"
                                                                style="width: 10rem; font-weight:700;">
                                                                Date of Submission
                                                            </th>
                                                            <th style="width: 1rem;"></th>
                                                        </tr>
                                                    </thead>

                                                    <tbody class="text-purple-900 dark:text-purple-200 bg-gradient-to-b from-purple-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900">
                                                        @foreach ($loans as $loan)
                                                            <tr class="border-b border-purple-300 hover:bg-indigo-100 dark:hover:bg-indigo-800 cursor-pointer text-sm text-start transition duration-300 ease-in-out"
                                                                style="cursor: pointer;">
                                                                <td class="px-2 py-4 font-semibold tracking-wide">
                                                                    {{ Str::title($loan->applicant_name) }}
                                                                </td>

                                                                <td class="py-4">
    {{ $loan->applicant_phone_number ?? 'N/A' }}
</td>

                                                                <td class="py-4 font-medium">
                                                                    {{ \Carbon\Carbon::parse($loan->created_at)->format('d F Y') }}
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-secondary bg-indigo-600 hover:bg-indigo-700 text-white rounded-md p-1.5 shadow-md transition"
                                                                        onclick="window.location.href='/show-loan/{{ $loan->id }}'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                            height="13" fill="currentColor"
                                                                            class="bi bi-folder2-open" viewBox="0 0 16 16"
                                                                            style="margin-bottom:0.2rem !important;">
                                                                            <path
                                                                                d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v.64c.57.265.94.876.856 1.546l-.64 5.124A2.5 2.5 0 0 1 12.733 15H3.266a2.5 2.5 0 0 1-2.481-2.19l-.64-5.124A1.5 1.5 0 0 1 1 6.14zM2 6h12v-.5a.5.5 0 0 0-.5-.5H9c-.964 0-1.71-.629-2.174-1.154C6.374 3.334 5.82 3 5.264 3H2.5a.5.5 0 0 0-.5.5zm-.367 1a.5.5 0 0 0-.496.562l.64 5.124A1.5 1.5 0 0 0 3.266 14h9.468a1.5 1.5 0 0 0 1.489-1.314l.64-5.124A.5.5 0 0 0 14.367 7z">
                                                                            </path>
                                                                        </svg>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <nav class="flex flex-col items-end justify-between p-4 space-y-3 md:flex-row md:items-center md:space-y-0"
                                                    aria-label="Table navigation">
                                                    <div class="flex flex-wrap justify-between px-10 gap-2">
                                                        {{ $loans->onEachSide(1)->links('vendor.pagination.tailwind') }}
                                                    </div>
                                                </nav>
                                            </div>
                                        @else
                                            <p class="text-center text-purple-700 font-semibold py-6 text-lg">
                                                No Record Found
                                            </p>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        @endunless
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
