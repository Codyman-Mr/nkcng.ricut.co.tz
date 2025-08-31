<div>
    <div
        class="wrapper wrapper-content animated fadeInRight overflow-y-scroll scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-transparent hover:scrollbar-thumb-gray-500">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content table-responsive">
                        <div class="mt-4">
                            <section class="py-3 sm:py-5">
                                <div class="px-4 mx-auto max-w-screen-2xl lg:px-12">
                                    <div
                                        class="relative overflow-hidden bg-white-700 shadow-md dark:bg-gray-800 sm:rounded-lg">
                                        <!-- Search bar and filters -->
                                        <div x-data="installationSearch()" @keydown.window.ctrl.k.prevent="focusSearch"
                                            @keydown.window.meta.k.prevent="focusSearch"
                                            class="flex flex-col items-center justify-between gap-4 px-4 py-3 bg-gray-700 lg:flex-row lg:gap-6">
                                            <!-- Title -->
                                            <div class="flex items-center flex-1 px-2">
                                                <h4 class="text-lg font-semibold text-white">Installations</h4>
                                            </div>
                                            <!-- Live Search Bar and Status Filter -->
                                            <div
                                                class="flex-1 w-full max-w-md mt-2 space-y-3 lg:space-y-0 lg:flex lg:items-center lg:space-x-3">
                                                <div class="relative flex-1">
                                                    <label for="search-installations" class="sr-only">Search for an
                                                        installation</label>
                                                    <span
                                                        class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-300"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M21 21l-4.35-4.35M10.5 17a6.5 6.5 0 1 1 0-13 6.5 6.5 0 0 1 0 13z" />
                                                        </svg>
                                                    </span>
                                                    <input type="text" id="search-installations"
                                                        wire:model.live="search"
                                                        class="w-full py-2.5 ps-10 pe-24 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                                        placeholder="Search by ID, user, status..." />
                                                    <button type="button" wire:click="$refresh"
                                                        class="absolute end-1.5 top-1.5 px-4 py-1.5 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700 focus:ring-2 focus:ring-offset-1 focus:ring-gray-500">
                                                        Search
                                                    </button>
                                                </div>

                                            </div>
                                            <!-- Placeholder -->
                                             <!-- Status Filter -->
                                                <div class="flex-1">
                                                    <select wire:model.live="statusFilter"
                                                        class=" w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                                        <option value="">All Statuses</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="completed">Completed</option>
                                                    </select>
                                                </div>
                                        </div>
                                        <div
                                            class="overflow-x-scroll scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-transparent hover:scrollbar-thumb-gray-500">
                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead
                                                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3" style="width: 8rem;">
                                                            Installation ID</th>
                                                        <th scope="col" class="px-6 py-3" style="width: 12rem;">User
                                                            Name</th>
                                                        <th scope="col" class="px-6 py-3" style="width: 10rem;">
                                                            Status</th>
                                                        <th scope="col" class="px-6 py-3" style="width: 10rem;">
                                                            Payment Type</th>
                                                        <th scope="col" class="px-6 py-3" style="width: 12rem;">
                                                            Vehicle</th>
                                                        <th scope="col" class="px-6 py-3" style="width: 12rem;">
                                                            Cylinder Type</th>
                                                        <th scope="col" class="px-6 py-3" style="width: 1rem;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (count($installations) > 0)
                                                        @foreach ($installations as $installation)
                                                            <tr
                                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                                <td class="px-4 py-4">{{ $installation->id }}</td>
                                                                <td class="px-4 py-4">
                                                                    {{ $installation->loan->applicant_name ?? 'N/A' }}
                                                                </td>
                                                                <td class="px-4 py-4">
                                                                    @if ($installation->status == 'pending')
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900 dark:text-yellow-300">
                                                                            {{ Str::title($installation->status) }}
                                                                        </span>
                                                                    @elseif ($installation->status == 'completed')
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">
                                                                            {{ Str::title($installation->status) }}
                                                                        </span>
                                                                    @else
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-900 dark:text-gray-300">
                                                                            {{ Str::title($installation->status) }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-4">
                                                                    {{ Str::title($installation->payment_type) }}</td>
                                                                <td class="px-4 py-4">
                                                                    {{ $installation->customerVehicle->plate_number ?? 'N/A' }}
                                                                </td>
                                                                <td class="px-4 py-4">
                                                                    {{ $installation->cylinderType->name ?? 'N/A' }}
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <a href="{{ route('approve-installation', $installation->id) }}"
                                                                        class="btn btn-sm btn-outline-secondary">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            width="16" height="13"
                                                                            fill="currentColor"
                                                                            class="bi bi-folder2-open"
                                                                            viewBox="0 0 16 16"
                                                                            style="margin-bottom:0.2rem !important;">
                                                                            <path
                                                                                d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v.64c.57.265.94.876.856 1.546l-.64 5.124A2.5 2.5 0 0 1 12.733 15H3.266a2.5 2.5 0 0 1-2.481-2.19l-.64-5.124A1.5 1.5 0 0 1 1 6.14zM2 6h12v-.5a.5.5 0 0 0-.5-.5H9c-.964 0-1.71-.629-2.174-1.154C6.374 3.334 5.82 3 5.264 3H2.5a.5.5 0 0 0-.5.5zm-.367 1a.5.5 0 0 0-.496.562l.64 5.124A1.5 1.5 0 0 0 3.266 14h9.468a1.5 1.5 0 0 0 1.489-1.314l.64-5.124A.5.5 0 0 0 14.367 7z" />
                                                                        </svg>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="7" class="px-4 py-4 text-center">
                                                                @if ($search || $statusFilter)
                                                                    No installations found for
                                                                    "{{ $search ?: $statusFilter }}"
                                                                @else
                                                                    No installations found
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- Navigation table links -->
                                        @if (count($installations) > 0)
                                            <div class="my-4 flex items-center justify-between px-4">
                                                {{ $installations->onEachSide(1)->links('vendor.pagination.tailwind') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function installationSearch() {
            return {
                focusSearch() {
                    document.getElementById('search-installations').focus();
                }
            }
        }
    </script>
@endpush
