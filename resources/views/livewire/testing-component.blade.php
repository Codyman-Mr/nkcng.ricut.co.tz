<div>
    {{-- resources/views/livewire/reports-page.blade.php --}}

    <div class="p-6 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">Reports Filters</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="date" wire:model.lazy="startDate" class="input">
                    <input type="date" wire:model.lazy="endDate" class="input">
                    <select wire:model.lazy="loanType" class="input">
                        <option value="all">All Loan Types</option>
                        <option value="NK CNG Automotive Loan">NK CNG Loans</option>
                        <option value="Maendeleo Bank Loan">Maendeleo Loans</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="text-gray-500 text-sm">Total Loans</h3>
                    <p class="text-2xl font-bold">{{ number_format($totalLoans) }}</p>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="text-gray-500 text-sm">Total Amount Loaned</h3>
                    <p class="text-2xl font-bold">TZS {{ number_format($totalAmount) }}</p>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="text-gray-500 text-sm">Total Amount Paid</h3>
                    <p class="text-2xl font-bold">TZS {{ number_format($totalPaid) }}</p>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="text-gray-500 text-sm">Total Customers</h3>
                    <p class="text-2xl font-bold">{{ number_format($customers) }}</p>
                </div>
            </div>
{{-- 
<div class="grid md:grid-cols-2 gap-6">
    <div class=" h-80 overflow-y-auto bg-white p-6 rounded shadow">
        <h3 class="text-lg font-bold mb-4">Installations by Cylinder Type</h3>
        <ul class="space-y-2">
            @foreach ($installations as $type => $group)
                <li class="flex justify-between">
                    <span>{{ $type }}</span>
                    <span class="font-bold">{{ $group->count() }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
--}}

               {{--  <div class=" h-80 overflow-y-auto bg-white p-6 rounded shadow">
                    <h3 class="text-lg font-bold mb-4">Payments by Method</h3>
                    <ul class="space-y-2">
                        @foreach ($paymentMethods as $method)
                            <li class="flex justify-between">
                                <span class="capitalize">{{ $method->payment_method }}</span>
                                <span class="font-bold">TZS {{ number_format($method->total) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>--}}
            <div class="flex row gap-4 mt-4 mx-1">
                <div class=" bg-white p-6  rounded shadow">
                    <h3 class="text-lg font-bold mb-4">Loan Trends Over Time</h3>
                    <div wire:wire:ignore id="loan-trends-chart" style="height: 350px;"></div>
                </div>
<div class="bg-white p-3 rounded shadow max-w-sm">
    <h3 class="text-md font-bold mb-2">Payments by Method</h3>
    <div wire:ignore id="payment-methods-chart" style="height: 200px;"></div>
</div>


                <div class="bg-white p-6 rounded shadow">
                    <h3 class="text-lg font-bold mb-4">Installations by Cylinder Type</h3>
                    <div wire:ignore id="installations-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function initializeCharts() {
        // console.log("Loan Trends Data:", @json($loanTrends));
        console.log("Time Periods:", @json($timePeriods));
        console.log("Loan Trends Data:", @json($loanTrends));
        console.log("filtered loans:", @json($filteredLoans));

        var loanTrendsOptions = {
            chart: {
                type: 'line',
                height: 350
            },
            series: [{
                name: 'Loans',
                data: @json($loanTrends)
            }],
            xaxis: {
                categories: @json($timePeriods)
            }
        };
        var loanTrendsChart = new ApexCharts(document.querySelector("#loan-trends-chart"), loanTrendsOptions);
        loanTrendsChart.render();

        console.log("Payment Methods Totals:", @json($paymentMethodsTotalPie));
        console.log("Payment Methods Labels:", @json($paymentMethodsPieLabels));

        var paymentMethodsOptions = {
            chart: {
                type: 'pie',
                height: 350
            },
            series: @json($paymentMethodsTotalPie),
            labels: @json($paymentMethodsPieLabels)
        };
        var paymentMethodsChart = new ApexCharts(document.querySelector("#payment-methods-chart"),
            paymentMethodsOptions);
        paymentMethodsChart.render();

        console.log("Installation Counts:", @json($installationCounts));
        console.log("Installation Keys:", @json($installationKeys));


        var installationCountsObject = @json($installationCounts);
        var installationCountsArray = Object.values(installationCountsObject);

        var installationsOptions = {
            chart: {
                type: 'bar',
                height: 350
            },
            series: [{
                name: 'Installations',
                data: @json($installationCounts)
            }],
            xaxis: {
                categories: @json($installationKeys)
            }
        };
        var installationsChart = new ApexCharts(document.querySelector("#installations-chart"), installationsOptions);
        installationsChart.render();

    }

    initializeCharts();
</script>
