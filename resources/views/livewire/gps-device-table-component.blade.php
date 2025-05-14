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
                                        <!-- Search bar and other things -->
                                        <div x-data="loanSearch()" @keydown.window.ctrl.k.prevent="focusSearch"
                                            @keydown.window.meta.k.prevent="focusSearch"
                                            class="flex flex-col items-center justify-between gap-4 px-4 py-3 bg-gray-700 lg:flex-row lg:gap-6">
                                            {{-- Title --}}
                                            <div class="flex items-center flex-1 px-2">
                                                <h4 class="text-lg font-semibold text-white">GPS Devices</h4>
                                            </div>
                                            {{-- Live Search Bar --}}
                                            <div class="flex-1 w-full max-w-md mt-3">
                                                <div class="relative">
                                                    <label for="search-devices" class="sr-only">Search for a
                                                        device</label>
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
                                                    <input type="text" id="search-devices"
                                                        wire:model.live="search"
                                                        class="w-full py-2.5 ps-10 pe-24 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                                        placeholder="Search by device ID or status..." />
                                                    <button type="button" wire:click="$refresh"
                                                        class="absolute end-1.5 top-1.5 px-4 py-1.5 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700 focus:ring-2 focus:ring-offset-1 focus:ring-gray-500">
                                                        Search
                                                    </button>
                                                </div>
                                            </div>
                                            {{-- Placeholder --}}
                                            <div class="flex-1 w-full"></div>
                                        </div>
                                        <div
                                            class="overflow-x-scroll scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-transparent hover:scrollbar-thumb-gray-500">
                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead
                                                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3" style="width: 10rem;">
                                                            Device ID</th>

                                                        <th scope="col" class="px-6 py-3" style="width: 10rem;">
                                                            Activity Status</th>
                                                        <th scope="col" class="px-6 py-3" style="width: 10rem;">
                                                            Assignment Status</th>
                                                        <th scope="col" class="px-6 py-3" style="width: 10rem;">Power
                                                            Status</th>
                                                        <th scope="col" class="px-6 py-3" style="width: 1rem;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (count($devices) > 0)
                                                        @foreach ($devices as $device)
                                                            <tr
                                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                                <td class="px-4 py-4">{{ $device->device_id }}</td>


                                                                <td class="px-4 py-4">
                                                                    @if ($device->activity_status == 'active')
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">
                                                                            {{ Str::title($device->activity_status) }}
                                                                        </span>
                                                                    @elseif ($device->activity_status == 'inactive')
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-300">
                                                                            {{ Str::title($device->activity_status) }}
                                                                        </span>
                                                                    @elseif ($device->activity_status == 'unknown')
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-900 dark:text-gray-300">
                                                                            {{ Str::title($device->activity_status) }}
                                                                        </span>
                                                                    @else
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900 dark:text-yellow-300">
                                                                            {{ Str::title($device->activity_status) }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-4">
                                                                    @if ($device->assignment_status == 'assigned')
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">
                                                                            {{ Str::title($device->assignment_status) }}
                                                                        </span>
                                                                    @elseif ($device->assignment_status == 'unassigned')
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-300">
                                                                            {{ Str::title($device->assignment_status) }}
                                                                        </span>
                                                                    @elseif ($device->assignment_status == 'unknown')
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-900 dark:text-gray-300">
                                                                            {{ Str::title($device->assignment_status) }}
                                                                        </span>
                                                                    @else
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900 dark:text-yellow-300">
                                                                            {{ Str::title($device->assignment_status) }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-4">
                                                                    @if ($device->power_status == 'on')
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">
                                                                            {{ Str::title($device->power_status) }}
                                                                        </span>
                                                                    @elseif ($device->power_status == 'off')
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-300">
                                                                            {{ Str::title($device->power_status) }}
                                                                        </span>
                                                                    @else
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-900 dark:text-gray-300">
                                                                            {{ Str::title($device->power_status) }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <a href="{{ route('gps-device.show', $device->device_id) }}"
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
                                                            <td colspan="5" class="px-4 py-4 text-center">
                                                                @if ($search)
                                                                    No devices found for "{{ $search }}"
                                                                @else
                                                                    No devices found
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- Navigation table links -->
                                        @if (count($devices) > 0)
                                            <div class="my-4 flex items-center justify-between px-4">
                                                {{ $devices->onEachSide(1)->links('vendor.pagination.tailwind') }}
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

        {{-- @foreach ($devices as $device)
            <div class="">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Device Details</h2>
                    {{$device}}
                    <button @click="open = false"
                        class="mt-4 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Close</button>
                </div>
            </div>

        @endforeach --}}

    </div>
</div>


@push('scripts')
    <script>
        function loanSearch() {
            return {
                focusSearch() {
                    document.getElementById('search-devices').focus();
                }
            }
        }
    </script>
@endpush
