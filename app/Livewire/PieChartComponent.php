<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PieChartComponent extends Component
{
    public $chartOptions;
    public $chartSeries;

    public function mount()
    {

        // Example data for the chart
        $this->chartOptions = [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
            ],
            'xaxis' => [
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            ],
        ];

        $this->chartSeries = [
            [
                'name' => 'Sales',
                'data' => [30, 40, 35, 50, 49],
            ],
        ];
    }
    public function render()
    {
        return view('livewire.pie-chart-component');
    }
}
