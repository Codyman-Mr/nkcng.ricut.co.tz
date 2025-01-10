@section('main-content')


    <div class="bg-gray-200 rounded-md shadow-md">
        <main class="content px-2 py-4">
            <div class="container-fluid">
                <div class="mb-3">
                    <h3 class="fw-bold fs-4 mb-3">Hello {{ Auth::user()->first_name }} {{ Auth::user()->last_name }},</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 xl:grid-flow-row grid-rows-auto gap-2.5">
                        <!-- Card 1 -->
                        <div class="sm:col-span-1">
                            <div class="flex flex-col rounded-2xl w-full bg-[#ffffff] shadow-xl">
                                <div class="flex flex-col p-4">
                                    <span class="flex justify-between items-center pb-1 border-b border-gray-300">
                                        <div class="text-md font-bold text-[#374151] ">Total Amount</div>
                                        <img src="{{asset('svg/money-stack.svg')}}" alt="money stack" class="w-8 h-8 object-cover">
                                    </span>
                                    <div class="text-2xl fw-bold text-[#374151] py-6">
                                        {{ number_format($totalLoanAmount) }} <strong class="text-sm">Tshs</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="sm:col-span-1">
                            <div class="flex flex-col rounded-2xl w-full bg-[#ffffff] shadow-xl">
                                <div class="flex flex-col p-4">
                                    <span class="flex justify-between items-center pb-1 border-b border-gray-300">
                                        <div class="text-md font-bold text-[#374151] ">Paid Amount</div>
                                        <img src="{{asset('svg/money-in.svg')}}" alt="money in" class="w-8 h-8 object-cover">
                                    </span>
                                    <div class="text-2xl fw-bold text-[#374151] py-6">
                                        {{ number_format($payments->sum('paid_amount')) }} <strong class="text-sm">Tshs</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="sm:col-span-1">
                            <div class="flex flex-col rounded-2xl w-full bg-[#ffffff] shadow-xl">
                                <div class="flex flex-col p-4">
                                    <span class="flex justify-between items-center pb-1 border-b border-gray-300">
                                        <div class="text-md font-bold text-[#374151] ">Due Amount</div>
                                        <img src="{{asset('svg/money-out.svg')}}" alt="money out" class="w-8 h-8 object-cover">
                                    </span>
                                    <div class="text-2xl fw-bold text-[#374151] py-6">
                                        {{ number_format($totalLoanAmount - $payments->sum('paid_amount')) }}  <strong class="text-sm">Tshs</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4 -->
                        <div class="sm:col-span-1 md:col-start-1 md:row-start-2">
                            <div class="flex flex-col rounded-2xl w-full bg-[#ffffff] shadow-xl">
                                <div class="flex flex-col p-4">
                                   <span class="flex justify-between items-center pb-1 border-b border-gray-300">
                                     <div class="text-md font-bold text-[#374151] ">All Users</div>
                                     <img src="{{asset('svg/users-many.svg')}}" alt="many users" class="w-8 h-8 object-cover">
                                   </span>
                                    <div class="text-2xl fw-bold text-[#374151] py-6">
                                       <p>
                                         {{ number_format($user->count()) }}  <strong class="text-sm">People</strong>
                                       </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 5 -->
                        <div class="sm:col-span-1 md:col-start-2 md:row-start-2">
                            <div class="flex flex-col rounded-2xl w-full bg-[#ffffff] shadow-xl">
                                <div class="flex flex-col p-4">
                                    <span class="flex justify-between items-center pb-1 border-b border-gray-300">
                                        <div class="text-md font-bold text-[#374151] ">Customers with Loans</div>
                                        <img src="{{asset('svg/user-not-paid.svg')}}" alt="loan" class="w-8 h-8 object-cover ">
                                    </span>
                                    <div class="text-2xl fw-bold text-[#374151] py-4">
                                        @php
                                    $count = 0;

                                    // Loop through all users
                                    foreach ($users as $user) {
                                        foreach ($user->loans as $loan) {
                                            $totalAmount = $loan->loan_required_amount;
                                            $paidAmount = $loan->payments->sum('paid_amount');
                                            if ($totalAmount > $paidAmount) {
                                                $count++; // Increment count if there's a due loan
                                                break; // Break to avoid counting the same user multiple times
                                            }
                                        }
                                    }
                                @endphp
                                <p>
                                    {{ $count }} <strong class="text-sm">People</strong>
                                </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 6 -->
                        <div class="sm:col-span-1 md:col-start-3 md:row-start-2">
                            <div class="flex flex-col rounded-2xl w-full bg-[#ffffff] shadow-xl">
                                <div class="flex flex-col p-4">
                                    <span class="flex justify-between items-center pb-1 border-b border-gray-300">
                                        <div class="text-md font-bold text-[#374151] ">Fully Paid Customers</div>
                                        <img src="{{asset('svg/user-paid.svg')}}" alt="paid user" class="w-8 h-8 filter-green">
                                    </span>
                                    <div class="text-2xl fw-bold text-[#374151] py-6">
                                       @php
                                    $fullyPaidCount = 0;

                                    // Iterate through all users to check their loans
                                    foreach ($users as $user) {
                                        foreach ($user->loans as $loan) {
                                            $totalAmount = $loan->loan_required_amount;
                                            $paidAmount = $loan->payments->sum('paid_amount');

                                            // Check if the loan is fully paid
                                            if ($paidAmount >= $totalAmount) {
                                                $fullyPaidCount++; // Increment count for loans with pending amount of 0
                                                break; // No need to check further loans for this user
                                            }
                                        }
                                    }
                                @endphp

                                <p>
                                    {{ $fullyPaidCount }} <strong class="text-sm">People</strong>
                                </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 7 (Large Card) -->
                        <div class="sm:col-span-2 md:col-span-2 lg:col-span-2 lg:col-start-4 lg:row-span-2 lg:row-start-1 border border-gray-400 rounded-2xl">
                            <div id="map"  class=" h-90% flex flex-col rounded-2xl w-full h-full bg-[#ffffff] shadow-xl cursor-pointer transition-all duration-500 ease-in-out" onclick="toggleMapSize(event)">

                                <div class="flex flex-col p-2">
                                    <div class="text-2xl fw-bold font-bold text-[#374151] pb-2">Map showing Users in Real-time</div>
                            </div>
                        </div>
                    </div>
                     <div class="backdrop" onclick="toggleMapSize(event)"></div>

                </div>

            </div>

            <h3 class="fw-bold fs-4 my-8  border-gray-900">payments this week</h3>

            <div class="row">
                <div class="col-12">
                    <h5 class="mb-2 fw-bold">Customers with Near Loan End Date</h5>
                    @if ($nearEndLoans->isEmpty())
                        <p>No loans nearing end date.</p>
                    @else
                        <table class="table table-striped text-sm" style="width: 100%;">
                            <thead class="text-xs bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">Name</th>
                                    <th scope="col" class="px-6 py-3" style="width: 15rem; font-weight:600;">Required
                                        Amount</th>
                                    <th scope="col" class="px-6 py-3" style="width: 15rem; font-weight:600;">Pending
                                        Amount</th>
                                    <th scope="col" class="px-6 py-3" style="width: 10rem; font-weight:600;">Days
                                        Remaining</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($nearEndLoans as $loan)
                                    <tr class="bg-white border-b">
                                        <td class="py-4">{{ $loan->user->first_name }} {{ $loan->user->last_name }}</td>
                                        <td class="py-4">{{ number_format($loan->loan_required_amount) }} Tsh</td>
                                        <td class="py-4">
                                            {{ number_format($loan->loan_required_amount - $loan->payments->sum('paid_amount')) }}
                                            Tsh</td>
                                        <td class="py-4">
                                            @php
                                                $daysRemaining = \Carbon\Carbon::now()->diffInDays(
                                                    $loan->loan_end_date,
                                                );
                                            @endphp
                                            {{ $daysRemaining }} Days
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>




    </div>
    </div>



    </main>

    </div>
    <!-- Expanded Style and Backdrop -->
    <style>
        .expanded {
            position: fixed;
            top: 50%; /* Move the top edge to the middle of the screen */
            left: 55%; /* Move the left edge to the middle of the screen */
            transform: translate(-50%, -50%); /* Shift the map back by 50% of its own width and height */
            width: 70vw; /* 70% of viewport width */
            height: 70vh; /* 70% of viewport height */
            z-index: 1000; /* Ensure the map is on top of other elements */
        }

        .backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
            z-index: 999; /* Ensure the backdrop is below the map */
            display: none; /* Hidden by default */
        }

        .expanded + .backdrop {
            display: block; /* Show the backdrop when the map is expanded */
        }
    </style>

    <!-- Initialize Map and Toggle Expanded State -->
    <script>
        mapboxgl.accessToken = 'pk.eyJ1IjoibWljaGFlbG1nb25kYXNyIiwiYSI6ImNtNXIwZHV0dDA1aDgyanIxaDd4OGQ2cWsifQ.wmLJmRnEG8S46PXSGajvSg';
        const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v11',
           center: [39.241196125639995, -6.774418233335669],
            zoom: 15
        });

        function toggleMapSize(event) {
            const mapContainer = document.getElementById('map');
            const backdrop = document.querySelector('.backdrop');

            mapContainer.classList.toggle('expanded');
            backdrop.style.display = mapContainer.classList.contains('expanded') ? 'block' : 'none';

            // Resize the map to fit the new container size
            if (mapContainer.classList.contains('expanded')) {
                map.resize();
            }

            // Stop event propagation to avoid triggering the backdrop click listener immediately
            event.stopPropagation();
        }

        // Detect clicks outside the map to return it to its original size
        document.addEventListener('click', function (event) {
            const mapContainer = document.getElementById('map');
            const backdrop = document.querySelector('.backdrop');

            if (mapContainer.classList.contains('expanded') && !mapContainer.contains(event.target)) {
                mapContainer.classList.remove('expanded');
                backdrop.style.display = 'none';
            }
        });
    </script>
@endsection
