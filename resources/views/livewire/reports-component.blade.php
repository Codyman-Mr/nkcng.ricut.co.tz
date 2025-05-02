<div>
   {{-- <div class="container mx-auto p-6">

    <!-- Filters -->
    <div class="mb-6">
        <input type="date" wire:model="startDate" class="border p-2">
        <input type="date" wire:model="endDate" class="border p-2">
        <select wire:model="loanType" class="border p-2 px-2">
            <option value="all">All Loan Types</option>
            <option value="NK CNG Automotive Loan">NK CNG Automotive Loan</option>
            <option class="px-2" value="Maendeleo Bank Loan">Maendeleo Bank Loan</option>
        </select>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Loan Status Chart -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Loan Status</h2>
            <canvas id="loanStatusChart"></canvas>
        </div>

        <!-- Payment Methods Chart -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Payment Methods</h2>
            <canvas class="" id="paymentMethodsChart"></canvas>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6 my-4 ">
        <h2 class="text-lg font-bold mb-4">Loan Performance Analytics</h2>

        <!-- Loan Status -->
        <div class="mb-4">
            <h3 class="text-md font-semibold mb-1.5">Loan Status Summary</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="p-4 bg-blue-100 rounded-lg">
                    <p class="text-sm">Active Loans</p>
                    <p class="text-2xl">{{ $metrics['loanStatus']['active'] }}</p>
                </div>
                <div class="p-4 bg-green-100 rounded-lg">
                    <p class="text-sm">Repaid Loans</p>
                    <p class="text-2xl">{{ $metrics['loanStatus']['repaid'] }}</p>
                </div>
                <div class="p-4 bg-red-100 rounded-lg">
                    <p class="text-sm">Overdue Loans</p>
                    <p class="text-2xl">{{ $metrics['loanStatus']['overdue'] }}</p>
                </div>
            </div>
        </div>

        <!-- Repayment Trends -->
        <div class="mb-4">
            <h3 class="text-md font-semibold">Monthly Repayment Trends</h3>

        </div>

        <!-- Loan Amount Distribution -->
        <div class="mb-4">
            <h3 class="text-md font-semibold mb-2">Loan Amount Distribution</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="p-4 bg-yellow-100 rounded-lg">
                    <p class="text-sm">Small Loans (&lt; $1000)</p>
                    <p class="text-2xl">{{ $metrics['loanDistribution']['small'] }}</p>
                </div>
                <div class="p-4 bg-orange-100 rounded-lg">
                    <p class="text-sm">Medium Loans ($1000-$5000)</p>
                    <p class="text-2xl">{{ $metrics['loanDistribution']['medium'] }}</p>
                </div>
                <div class="p-4 bg-purple-100 rounded-lg">
                    <p class="text-sm">Large Loans (&gt; $5000)</p>
                    <p class="text-2xl">{{ $metrics['loanDistribution']['large'] }}</p>
                </div>
            </div>
        </div>

        <div class="p-4 bg-gray-100 rounded-lg">
            <h3 class="text-md font-semibold">Default Rate</h3>
            <p class="text-2xl">{{ number_format($metrics['defaultRate'], 2) }}%</p>
        </div>
    </div>

    <!-- Loan Details -->
    <div class="mt-4">
        <h2 class="text-xl font-bold mb-4">Loan Details</h2>
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border">Loan ID</th>
                    <th class="py-2 px-4 border">Customer Name</th>
                    <th class="py-2 px-4 border">Loan Amount</th>
                    <th class="py-2 px-4 border">Outstanding Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($loans as $loan)
                    <tr>
                        <td class="py-2 px-4 border">{{ $loan->id }}</td>
                        <td class="py-2 px-4 border">{{ $loan->user->first_name }} {{ $loan->user->last_name }}</td>
                        <td class="py-2 px-4 border">{{ number_format($loan->loan_required_amount, 2) }}</td>
                        <td class="py-2 px-4 border">
                            {{ number_format($loan->loan_required_amount - $loan->payments->sum('paid_amount'), 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>


    </div>


</div> --}}


<livewire:testing-component />

</div>
