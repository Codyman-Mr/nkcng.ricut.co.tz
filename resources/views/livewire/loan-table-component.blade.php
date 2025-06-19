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
                                            <!-- Search bar and other things -->
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
                                                        <input type="text" id="search-loans" wire:model.live="search"
                                                            class="w-full py-2.5 ps-10 pe-24 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                                            placeholder="Search loans..." />
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
                                                            <th scope="col" class="px-4 py-3">Name</th>
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
                                                            <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 accordion-toggle cursor-pointer"
                                                                data-target="#collapse-{{ $loan->id }}">
                                                                <td class="px-2 py-3">
                                                                    <span
                                                                        class="items-center px-1 bg-primary-50 text-primary-800 text-sm font-medium py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                                                                        {{ $loan->user->first_name }}
                                                                        {{ $loan->user->last_name }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-2 py-3 text-sm">
                                                                    {{ number_format($loan->loan_required_amount) }} Tsh
                                                                </td>
                                                                <td class="px-2 py-3 text-sm">
                                                                    {{ number_format($loan->payments->sum('paid_amount')) }}
                                                                    Tsh</td>
                                                                <td class="px-2 py-3 text-sm">
                                                                    {{ number_format($loan->loan_required_amount - $loan->payments->sum('paid_amount')) }}
                                                                    Tsh</td>
                                                                <td class="w-auto px-6 py-3 flex items-center text-sm">
                                                                    <div class="flex items-center">
                                                                        @php
                                                                            $now = Carbon::now();
                                                                            $end = $loan->loan_end_date;
                                                                            $interval = $now->diff($end);
                                                                            $totalMonths =
                                                                                $interval->y * 12 + $interval->m;
                                                                            $weeks = floor($interval->d / 7);
                                                                            $remainingDays = $interval->d % 7;
                                                                            $hours = $interval->h;
                                                                            $minutes = $interval->i;
                                                                            if ($minutes >= 30) {
                                                                                $hours++;
                                                                            }
                                                                            if ($hours >= 24) {
                                                                                $hours -= 24;
                                                                                $remainingDays++;
                                                                                if ($remainingDays >= 7) {
                                                                                    $weeks += floor($remainingDays / 7);
                                                                                    $remainingDays %= 7;
                                                                                }
                                                                            }
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
                                                                            if (empty($parts)) {
                                                                                $parts[] = 'less than an hour';
                                                                            }
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
                                                                <td>
                                                                    <div class="text-sm">
                                                                        @php
                                                                            $time_left = $interval;
                                                                            $paid_amount = $loan->payments->sum(
                                                                                'paid_amount',
                                                                            );
                                                                            $loan_amount = $loan->loan_required_amount;
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
                                                                                $status = 'unknown';
                                                                                $color_class =
                                                                                    'text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-100';
                                                                            }
                                                                        @endphp
                                                                        <span
                                                                            class="px-2 py-1 font-semibold leading-tight {{ $color_class }} rounded-full">
                                                                            {{ ucfirst($status) }}
                                                                        </span>
                                                                    </div>
                                                                </td>
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
                                                                class="hidden accordion-content transition duration-300 ease-in-out">
                                                                <td colspan="7" class="p-4 bg-gray-50 dark:bg-gray-800">
                                                                    <div class="flex flex-wrap gap-4">
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
                                                                        <div class="w-full sm:w-full md:full lg:w-4/12">
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
                                                                                        <x-button.outline color="green"
                                                                                            icon="icons.plus"
                                                                                            wire:click="openPaymentModal({{ $loan->id }})">
                                                                                            Add Payment
                                                                                        </x-button.outline>

                                                                                            <x-button.outline
                                                                                                color='gray'
                                                                                                icon="icons.bell"
                                                                                                onclick="window.location.href='/payment-history/{{ $loan->id }}'"
                                                                                                >
                                                                                                Payment History
                                                                                            </x-button.outline>




                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="w-full sm:w-full md:w-6/12 lg:w-4/12">
                                                                            <div
                                                                                class="bg-white dark:bg-gray-700 text-[#2E4057] dark:text-white rounded-lg p-1 shadow-sm">
                                                                                <div id="map-{{ $loan->id }}"
                                                                                    class="mini-map rounded-2xl w-full h-[200px] p-1 shadow-sm cursor-pointer transition-all duration-500 ease-in-out"
                                                                                    onclick="toggleMapSize('map-{{ $loan->id }}')">
                                                                                </div>
                                                                            </div>
                                                                        </div>
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
                                                                    No loans found for "{{ $search }}"</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="my-4">
                                                {{ $loans->onEachSide(1)->links('vendor.pagination.tailwind') }}</div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        @endunless
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        @if ($showPaymentModal)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50"
                wire:loading.class="opacity-50 pointer-events-none">
                <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                    <h2 class="text-lg font-semibold mb-4 text-gray-800">Add Payment</h2>
                    <form wire:submit.prevent="addPayment">
                        <div class="mb-4">
                            <label for="phoneNumber" class="block text-sm font-medium text-gray-700">Phone
                                Number</label>
                            <input type="text" wire:model.live="phoneNumber" id="phoneNumber"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('phoneNumber')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="provider" class="block text-sm font-medium text-gray-700">Payment
                                Provider</label>
                            <select wire:model.live="provider" id="provider"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Provider</option>
                                <option value="Mpesa">Mpesa</option>
                                <option value="TigoPesa">TigoPesa</option>
                                <option value="AirtelMoney">AirtelMoney</option>
                                <option value="HaloPesa">HaloPesa</option>
                            </select>
                            @error('provider')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="paymentAmount" class="block text-sm font-medium text-gray-700">Payment Amount
                                (TZS)</label>
                            <input type="number" wire:model.live="paymentAmount" id="paymentAmount"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                min="1000" step="1">
                            @error('paymentAmount')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex space-x-4 items-center">
                            <button type="submit" wire:loading.attr="disabled"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                                <span>Confirm Payment</span>
                                <span wire:loading wire:target="addPayment">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </span>
                            </button>
                            <button type="button" wire:click="closePaymentModal"
                                class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="fixed top-4 right-4 z-50 mt-4 p-4 bg-green-100 text-green-700 rounded shadow-lg">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="fixed top-4 right-4 z-50 mt-4 p-4 bg-red-100 text-red-700 rounded shadow-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- SweetAlert2 and Livewire Event Listener -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Livewire.on('notify', (event) => {
                    console.log('Notify event received:', event); // Debugging
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: event.type || 'info',
                        title: event.message || 'No message provided',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                });

                Livewire.on('payment-processing', () => {
                    console.log('Payment processing started'); // Debugging
                });

                Livewire.on('payment-processed', () => {
                    console.log('Payment processing finished'); // Debugging
                });
            });

            // Fallback notification system
            function showFallbackNotification(message, type = 'info') {
                const div = document.createElement('div');
                div.className =
                    `fixed top-4 right-4 z-50 p-4 rounded shadow-lg text-white ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
                div.textContent = message;
                document.body.appendChild(div);
                setTimeout(() => div.remove(), 5000);
            }
        </script>

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
                    mapContainer._mapbox = mapInstance;
                });

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

            function toggleMapSize(mapId) {
                const mapContainer = document.getElementById(mapId);
                const backdrop = document.getElementById('backdrop-' + mapId.split('-')[1]);
                const isExpanding = !mapContainer.classList.contains('expanded');
                mapContainer.classList.toggle('expanded');
                backdrop.style.display = isExpanding ? 'block' : 'none';
                setTimeout(() => {
                    if (mapContainer._mapbox) {
                        mapContainer._mapbox.resize();
                    }
                }, 350);
            }

            document.addEventListener('click', function(event) {
                const maps = document.querySelectorAll('[id^="map-"]');
                const backdrop = document.querySelector('.backdrop');
                maps.forEach(mapContainer => {
                    if (mapContainer.classList.contains('expanded') && !mapContainer.contains(event.target)) {
                        mapContainer.classList.remove('expanded');
                        backdrop.style.display = 'none';
                        if (mapContainer._mapbox) {
                            mapContainer._mapbox.resize();
                        }
                    }
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
</div>
