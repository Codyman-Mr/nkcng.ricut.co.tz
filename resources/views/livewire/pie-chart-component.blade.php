<div>
    <div id="chart"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const options = @json($chartOptions);
            const series = @json($chartSeries);

            const chart = new ApexCharts(document.querySelector("#chart"), {
                ...options,
                series: series,
            });

            chart.render();
        });
    </script>
</div>
