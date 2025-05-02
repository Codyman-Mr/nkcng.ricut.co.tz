@php
    use Carbon\Carbon;
    use Carbon\CarbonInterface;
@endphp


<div>
    <div
        class="wrapper wrapper-content animated fadeInRight overflow-y-scroll scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-transparent hover:scrollbar-thumb-gray-500">
        <div class="row">

            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content table-responsive border-0">
                        @unless (count($loans) == 0)
                            <div class="mt-4">
                                <section class="py-3 sm:py-5">
                                    <div class="px-4 mx-auto max-w-screen-2xl lg:px-12">
                                        <div
                                            class="relative overflow-hidden bg-white-700 shadow-md dark:bg-gray-800 sm:rounded-lg">
                                            <!-- search bar and other things -->
                                            <div x-data="loanSearch()" @keydown.window.ctrl.k.prevent="focusSearch"
                                                @keydown.window.meta.k.prevent="focusSearch"
                                                class="flex flex-col items-center justify-between gap-4 px-4 py-3 bg-gray-700 lg:flex-row lg:gap-6">
                                                {{-- Title --}}
                                                <div class="flex items-center flex-1 px-2">
                                                    <h4 class="text-lg font-semibold text-white">Ongoing Loans</h4>
                                                </div>
                                                {{-- Live Search Bar --}}
                                                <div class="flex-1 w-full max-w-md mt-3">
                                                    <div class="relative">
                                                        <label for="search-loans" class="sr-only">Search Loans</label>

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

                                                        {{-- 🟢 This is the key: wire:model.live updates the Livewire property --}}
                                                        <input type="text" id="search-loans"
                                                            wire:model.debounce.500ms="search"
                                                            class="w-full py-2.5 ps-10 pe-24 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                                            placeholder="Search loans..." />

                                                        {{-- Optional: You can remove this button if search updates automatically --}}
                                                        <button type="button" wire:click="$refresh"
                                                            class="absolute end-1.5 top-1.5 px-4 py-1.5 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700 focus:ring-2 focus:ring-offset-1 focus:ring-gray-500">
                                                            Search
                                                        </button>

                                                    </div>
                                                </div>

                                                {{-- placeholder --}}
                                                <div class="flex-1 w-full">

                                                </div>


                                            </div>


                                            <div
                                                class="overflow-x-scroll scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-transparent hover:scrollbar-thumb-gray-500">
                                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                    <thead
                                                        class="text-xs text-gray-700 uppercase bg-gray-100  dark:bg-gray-700 dark:text-gray-400">
                                                        <tr>
                                                            </th>
                                                            <th scope="col" class="px-4 py-3">Name</th>
                                                            <!-- sortable column: Amount Loaned -->
                                                            <th class="px-3 py-2 cursor-pointer"
                                                                wire:click="sortBy('loan_required_amount')">
                                                                Amount Loaned
                                                                @if ($sortField === 'loan_required_amount')
                                                                    @if ($sortDirection === 'asc')
                                                                        <span>↑</span>
                                                                    @else
                                                                        <span>↓</span>
                                                                    @endif
                                                                @endif
                                                            </th>
                                                            <!-- Sortable: Amount Paid -->
                                                            <th class="px-3 py-2 cursor-pointer"
                                                                wire:click="sortBy('amount_paid')">
                                                                Amount Paid
                                                                @if ($sortField === 'amount_paid')
                                                                    @if ($sortDirection === 'asc')
                                                                        <span>↑</span>
                                                                    @else
                                                                        <span>↓</span>
                                                                    @endif
                                                                @endif
                                                            </th>
                                                            <!-- Sortable: Amount Remaining -->
                                                            <th class="px-3 py-2 cursor-pointer"
                                                                wire:click="sortBy('amount_remaining')">
                                                                Amount Remaining
                                                                @if ($sortField === 'amount_remaining')
                                                                    @if ($sortDirection === 'asc')
                                                                        <span>↑</span>
                                                                    @else
                                                                        <span>↓</span>
                                                                    @endif
                                                                @endif
                                                            </th>
                                                            <!-- Sortable: Days Remaining -->
                                                            <th class="px-3 py-2 cursor-pointer"
                                                                wire:click="sortBy('days_remaining')">
                                                                Days Remaining
                                                                @if ($sortField === 'days_remaining')
                                                                    @if ($sortDirection === 'asc')
                                                                        <span>↑</span>
                                                                    @else
                                                                        <span>↓</span>
                                                                    @endif
                                                                @endif
                                                            </th>
                                                            <th scope="col" class="px-4 py-3">Status</th>
                                                            <th scope="col" class="px-4 py-3">More</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-gray-600 dark:text-gray-200">
                                                        @forelse ($loans as $loan)
                                                            <tr class=" border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 accordion-toggle cursor-pointer"
                                                                data-target="#collapse-{{ $loan->id }}">
                                                                <!-- Existing table cells -->
                                                                <td class="px-2 py-3">
                                                                    <span
                                                                        class="items-center px-1 bg-primary-50 text-primary-800 text-sm font-medium py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                                                                        {{ $loan->user->first_name }}
                                                                        {{ $loan->user->last_name }}
                                                                    </span>
                                                                </td>
                                                                <!-- loan required amount -->
                                                                <td class="px-2 py-3 text-sm">
                                                                    {{ number_format($loan->loan_required_amount) }} Tsh
                                                                </td>

                                                                <!-- paid amount -->
                                                                <td class="px-2 py-3 text-sm">
                                                                    {{ number_format($loan->payments->sum('paid_amount')) }}
                                                                    Tsh</td>

                                                                <!-- Amount Remaining -->
                                                                <td class="px-2 py-3 text-sm">
                                                                    {{ number_format($loan->loan_required_amount - $loan->payments->sum('paid_amount')) }}
                                                                    Tsh</td>

                                                                <!-- Days remaining -->
                                                                <td class="w-auto px-6 py-3 flex items-center text-sm">
                                                                    <div class="flex items-center">
                                                                        @php
                                                                            $now = Carbon::now();
                                                                            $end = $loan->loan_end_date;

                                                                            // Get the exact difference as a DateInterval
                                                                            $interval = $now->diff($end);

                                                                            // $diffindays = $now->shortRelativeDiffForHumans($end);

                                                                            // Calculate components
                                                                            $totalMonths =
                                                                                $interval->y * 12 + $interval->m;
                                                                            $weeks = floor($interval->d / 7);
                                                                            $remainingDays = $interval->d % 7;
                                                                            $hours = $interval->h;
                                                                            $minutes = $interval->i;

                                                                            // Round up hours if minutes are 30+ (only affects hour display)
                                                                            if ($minutes >= 30) {
                                                                                $hours++;
                                                                            }

                                                                            // Handle edge case where rounding creates 24 hours
                                                                            if ($hours >= 24) {
                                                                                $hours -= 24;
                                                                                $remainingDays++;
                                                                                if ($remainingDays >= 7) {
                                                                                    $weeks += floor($remainingDays / 7);
                                                                                    $remainingDays %= 7;
                                                                                }
                                                                            }

                                                                            // Build parts array
                                                                            $parts = [];
                                                                            if ($totalMonths > 0) {
                                                                                $parts[] =
                                                                                    $totalMonths .
                                                                                    ' m' .
                                                                                    ($totalMonths !== 1 ? 's' : '');
                                                                            }
                                                                            if ($weeks > 0) {
                                                                                $parts[] =
                                                                                    $weeks .
                                                                                    ' w' .
                                                                                    ($weeks !== 1 ? 's' : '');
                                                                            }
                                                                            if ($remainingDays > 0) {
                                                                                $parts[] =
                                                                                    $remainingDays .
                                                                                    ' d' .
                                                                                    ($remainingDays !== 1 ? 's' : '');
                                                                            }
                                                                            if ($hours > 0 || empty($parts)) {
                                                                                $parts[] =
                                                                                    $hours .
                                                                                    ' h' .
                                                                                    ($hours !== 1 ? 's' : '');
                                                                            }

                                                                            // Special case for less than 1 hour
                                                                            if (empty($parts)) {
                                                                                $parts[] = 'less than an hour';
                                                                            }

                                                                            // Format the output
                                                                            echo match (count($parts)) {
                                                                                1 => $parts[0],
                                                                                2 => implode(' and ', $parts),
                                                                                default => implode(
                                                                                    ', ',
                                                                                    array_slice($parts, 0, -1),
                                                                                ) .
                                                                                    ' and ' .
                                                                                    end($parts),
                                                                            };
                                                                        @endphp
                                                                    </div>
                                                                </td>

                                                                <!-- Status -->
                                                                <td>
                                                                    <div class="text-sm">

                                                                        @php
                                                                            $time_left = $interval;
                                                                            $paid_amount = $loan->payments->sum(
                                                                                'paid_amount',
                                                                            );
                                                                            $loan_amount = $loan->loan_required_amount;

                                                                            // Determine the loan status based on conditions
                                                                            if ($paid_amount >= $loan_amount) {
                                                                                $status = 'completed';
                                                                                $color_class =
                                                                                    'text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100';
                                                                            } elseif (
                                                                                $time_left->invert == 0 &&
                                                                                $paid_amount < $loan_amount
                                                                            ) {
                                                                                $status = 'ongoing';
                                                                                $color_class =
                                                                                    'text-yellow-700 bg-yellow-100 dark:bg-yellow-700 dark:text-yellow-100';
                                                                            } elseif (
                                                                                $time_left->invert == 1 &&
                                                                                $paid_amount < $loan_amount
                                                                            ) {
                                                                                $status = 'overdue';
                                                                                $color_class =
                                                                                    'text-red-700 bg-red-100 dark:bg-red-700 dark:text-red-100';
                                                                            } else {
                                                                                $status = 'unknown'; // Fallback for unexpected cases
                                                                                $color_class =
                                                                                    'text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-100';
                                                                            }
                                                                        @endphp

                                                                        <!-- Output the styled badge -->
                                                                        <span
                                                                            class="px-2 py-1 font-semibold leading-tight {{ $color_class }} rounded-full">
                                                                            {{ ucfirst($status) }}
                                                                        </span>



                                                                    </div>
                                                                </td>
                                                                <!-- More-->
                                                                <td class="px-4 py-3">
                                                                    <svg class="accordion-chevron w-5 h-5 transform transition-transform"
                                                                        fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                                    </svg>
                                                                </td>
                                                            </tr>

                                                            <tr id="collapse-{{ $loan->id }}"
                                                                class="hidden accordion-content  transition duration-300 ease-in-out">

                                                                <td colspan="7" class="p-4 bg-gray-50 dark:bg-gray-800">
                                                                    <div class="flex flex-wrap gap-4">
                                                                        <!-- Payment Progress Card -->
                                                                        <div class="w-full sm:w-full md:w-6/12 lg:w-3/12">
                                                                            <div
                                                                                class="bg-white dark:bg-gray-700 text-[#2E4057] dark:text-white rounded-lg p-4 shadow-sm">
                                                                                <h5
                                                                                    class="mb-2 font-bold text-info dark:text-blue-300">
                                                                                    Payment Progress</h5>
                                                                                @php
                                                                                    $totalAmount =
                                                                                        $loan->loan_required_amount;
                                                                                    $paidAmount = $loan->payments->sum(
                                                                                        'paid_amount',
                                                                                    );
                                                                                    $progressPercentage =
                                                                                        $totalAmount > 0
                                                                                            ? ($paidAmount /
                                                                                                    $totalAmount) *
                                                                                                100
                                                                                            : 0;
                                                                                @endphp
                                                                                <div
                                                                                    class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-6 overflow-hidden">
                                                                                    <div class="bg-blue-500 dark:bg-blue-400 h-6 text-xs text-white flex items-center justify-center transition-all duration-300"
                                                                                        role="progressbar"
                                                                                        style="width: {{ $progressPercentage }}%;"
                                                                                        aria-valuenow="{{ $progressPercentage }}"
                                                                                        aria-valuemin="0"
                                                                                        aria-valuemax="100">
                                                                                        {{ number_format($progressPercentage, 1) }}%
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Loan Details Card -->
                                                                        <div class="w-full sm:w-full md:w-6/12 lg:w-4/12">
                                                                            <div
                                                                                class="bg-white dark:bg-gray-700 text-[#2E4057] dark:text-white rounded-lg p-4 shadow-sm">
                                                                                <h5
                                                                                    class="mb-2 font-bold text-info dark:text-blue-300">
                                                                                    Loan Details</h5>
                                                                                <div
                                                                                    class="flex items-center justify-between">
                                                                                    <div>
                                                                                        <span
                                                                                            class="text-xs text-gray-500 dark:text-gray-400">Time
                                                                                            to next Payment</span>
                                                                                        <span
                                                                                            class="text-sm font-semibold text-gray-900 dark:text-white">
                                                                                            @if ($loan->time_to_next_payment !== null)
                                                                                                {{-- Show due/overdue status --}}
                                                                                            @else
                                                                                                @if ($loan->status === 'approved')
                                                                                                    @if ($loan->payments->sum('paid_amount') >= $loan->loan_required_amount)
                                                                                                        Fully Paid
                                                                                                    @elseif($today > $loan->loan_end_date)
                                                                                                        Loan Period Ended
                                                                                                    @else
                                                                                                        Payment Schedule
                                                                                                        Exceeds End Date
                                                                                                    @endif
                                                                                                @else
                                                                                                    No Payments (Not
                                                                                                    Approved)
                                                                                                @endif
                                                                                            @endif
                                                                                        </span>
                                                                                    </div>
                                                                                    <div class="flex gap-2">

                                                                                        <x-button.outline color="gray"
                                                                                            icon="icons.bell"
                                                                                            onclick="confirmSendReminder({{ $loan->id }}, '{{ $loan->user->first_name }}')">
                                                                                            Send Reminder
                                                                                        </x-button.outline>



                                                                                        <!-- Add Payment -->
                                                                                        <x-button.outline color="green"
                                                                                            icon="icons.plus"
                                                                                            onclick='$("#createModal").modal(`show`)'>
                                                                                            Add Payment
                                                                                        </x-button.outline>


                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>


                                                                        <!-- Mini Map Card (inside accordion content) -->
                                                                        <div class="w-full sm:w-full md:w-6/12 lg:w-4/12">
                                                                            <div
                                                                                class="bg-white dark:bg-gray-700 text-[#2E4057] dark:text-white rounded-lg p-1 shadow-sm">
                                                                                <div id="map-{{ $loan->id }}"
                                                                                    class="mini-map rounded-2xl w-full h-[200px] p-1 shadow-sm cursor-pointer transition-all duration-500 ease-in-out"
                                                                                    onclick="toggleMapSize('map-{{ $loan->id }}')">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Backdrop -->
                                                                        <div id="backdrop-{{ $loan->id }}"
                                                                            class="backdrop"
                                                                            onclick="toggleMapSize('map-{{ $loan->id }}')">
                                                                        </div>

                                                                    </div>
                                                                </td>

                                                            </tr>

                                                        @empty
                                                            <tr>
                                                                <td colspan="100%" class="text-center py-4 text-gray-500">
                                                                    No loans found for "{{ $search }}"
                                                                </td>
                                                            </tr>
                                                        @endforelse

                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="my-4 ">
                                                {{ $loans->onEachSide(1)->links('vendor.pagination.tailwind') }}
                                            </div>
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




    <style>
        .accordion-content {
            transition: all 0.3s ease;
        }


        .progress-bar {
            transition: width 0.5s ease-in-out;
        }

        .accordion-chevron {
            transition: transform 0.3s ease;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }

        .mini-map {
            width: 100%;
            height: 100px;
            /* default collapsed height */
            transition: all 0.3s ease;
        }

        .expanded {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70vw;
            height: 70vh !important;
            z-index: 1000;
            background-color: white;
            z-index: 1000;
            /* Optional, in case the map div has no bg */
            border-radius: 1rem;
            padding: 0;
        }

        .backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .expanded+.backdrop {
            display: block;
            /* Show the backdrop when the map is expanded */
        }

        #map-[id] {
            width: 100%;
            height: 100%;
        }

        [id^="map-"] {
            width: 100%;
            height: 100px;
            transition: all 0.3s ease;
        }

        [id^="map-"].expanded {
            width: 80vw !important;
            height: 70vh !important;
        }
    </style>

    <script>
        mapboxgl.accessToken =
            'pk.eyJ1IjoibWljaGFlbG1nb25kYXNyIiwiYSI6ImNtNXIwZHV0dDA1aDgyanIxaDd4OGQ2cWsifQ.wmLJmRnEG8S46PXSGajvSg';

        document.addEventListener('DOMContentLoaded', function() {

            // Initialize maps for all loan rows
            document.querySelectorAll('[id^="map-"]').forEach(mapContainer => {
                const mapInstance = new mapboxgl.Map({
                    container: mapContainer.id,
                    style: 'mapbox://styles/mapbox/streets-v12',
                    center: [39.241196125639995, -6.774418233335669],
                    zoom: 14
                });

                new mapboxgl.Marker({
                        color: 'red'
                    })
                    .setLngLat([39.241196125639995, -6.774418233335669])
                    .addTo(mapInstance);

                mapInstance.addControl(new mapboxgl.NavigationControl());
                mapInstance.on('style.load', () => mapInstance.setFog({}));

                // 🔑 Store the map instance for later resizing
                mapContainer._mapbox = mapInstance;
            });

        });


        function toggleMapSize(mapId) {
            const mapContainer = document.getElementById(mapId);
            const backdrop = document.getElementById('backdrop-' + mapId.split('-')[1]);
            const isExpanding = !mapContainer.classList.contains('expanded');

            mapContainer.classList.toggle('expanded');
            backdrop.style.display = isExpanding ? 'block' : 'none';

            // Wait for transition to complete before resizing the map
            setTimeout(() => {
                if (mapContainer._mapbox) {
                    mapContainer._mapbox.resize();
                }
            }, 350); // Match transition time
        }

        // Detect clicks outside the map to return it to its original size
        document.addEventListener('click', function(event) {
            const maps = document.querySelectorAll('[id^="map-"]');
            const backdrop = document.querySelector('.backdrop');

            maps.forEach(mapContainer => {
                if (mapContainer.classList.contains('expanded') && !mapContainer.contains(event.target)) {
                    mapContainer.classList.remove('expanded');
                    backdrop.style.display = 'none';

                    if (mapContainer._map) {
                        mapContainer._map.resize();
                    }
                }
            });
        });



        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.accordion-toggle').forEach(button => {
                button.addEventListener('click', function() {
                    const target = document.querySelector(this.dataset.target);
                    const chevron = this.querySelector('.accordion-chevron');

                    if (target) {
                        target.classList.toggle('hidden');
                    }

                    if (chevron) {
                        chevron.classList.toggle('rotate-180');
                    }
                });
            });
        });




        function confirmSendReminder(loanId, name) {
            if (confirm(`Tuma kumbukumbu ya malipo kwa ${name}?`)) {
                Livewire.dispatch('confirm-send-reminder', {
                    loanId
                });
            }
        }
    </script>

</div>
