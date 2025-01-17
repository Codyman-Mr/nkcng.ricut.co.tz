<div>
    <!-- Filters -->
    <div class="mb-6">
        <input type="date" wire:model="startDate" class="border p-2">
        <input type="date" wire:model="endDate" class="border p-2">
        <select wire:model="loanType" class="border p-2">
            <option value="all">All Loan Types</option>
            <option value="NK CNG Automotive Loan">NK CNG Automotive Loan</option>
            <option value="Maendeleo Bank Loan">Maendeleo Bank Loan</option>
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
            <canvas id="paymentMethodsChart"></canvas>
        </div>
    </div>

    <!-- Tables -->
    <div class="mt-6">
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
                        <td class="py-2 px-4 border">{{ number_format($loan->loan_required_amount - $loan->payments->sum('paid_amount'), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:load', function () {
        // Loan Status Chart
        const loanStatusCtx = document.getElementById('loanStatusChart').getContext('2d');
        new Chart(loanStatusCtx, {
            type: 'pie',
            data: {
                labels: ['Pending', 'Approved', 'Rejected'],
                datasets: [{
                    label: 'Loan Status',
                    data: [{{ $loans->where('status', 'pending')->count() }}, {{ $loans->where('status', 'approved')->count() }}, {{ $loans->where('status', 'rejected')->count() }}],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                }]
            },
        });

        // Payment Methods Chart
        const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
        new Chart(paymentMethodsCtx, {
            type: 'bar',
            data: {
                labels: ['Cash', 'Bank'],
                datasets: [{
                    label: 'Payment Methods',
                    data: [{{ $payments->where('payment_method', 'cash')->count() }}, {{ $payments->where('payment_method', 'bank')->count() }}],
                    backgroundColor: ['#4CAF50', '#2196F3'],
                }]
            },
        });
    });
</script>
