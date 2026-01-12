<?php

namespace App\Livewire\MarketDetails;

use Livewire\Component;

class Chart extends Component
{
    public $event;
    public $seriesData = [];

    public function mount($event)
    {
        $this->event = $event;
        $this->prepareChartData();
    }

    public function refreshEvent()
    {
        $this->event->refresh();
        $this->prepareChartData();
    }

    protected function prepareChartData()
    {
        // Reload event with markets relationship
        $this->event->load(['markets' => function ($query) {
            $query->limit(8)->orderBy('id');
        }]);

        // Color palette for markets (Polymarket style)
        $marketColors = [
            '#ff7b2c', // Orange
            '#4c8df5', // Blue
            '#9cdbff', // Light Blue
            '#ffe04d', // Yellow
            '#ff6b9d', // Pink
            '#4ecdc4', // Teal
            '#a8e6cf', // Green
            '#ff8b94', // Coral
        ];

        // Prepare data for Highcharts Stock
        $now = now();
        $startDate = $this->event->start_date ? \Carbon\Carbon::parse($this->event->start_date) : $now->copy()->subDays(30);

        // Generate timestamps for data points
        $points = 50;
        $allTimes = [];

        for ($i = $points; $i >= 0; $i--) {
            $time = $startDate->copy()->addHours($i * (($now->diffInHours($startDate)) / $points));
            if ($time > $now) {
                $time = $now->copy();
            }
            $allTimes[] = $time;
        }

        if (empty($allTimes)) {
            for ($i = 6; $i >= 0; $i--) {
                $allTimes[] = $now->copy()->subDays($i);
            }
        }

        // Prepare data for each market (show up to 4 markets)
        $marketsToShow = $this->event->markets->take(4);
        $this->seriesData = [];

        foreach ($marketsToShow as $index => $market) {
            // Get current price
            $currentPrice = 50;
            if ($market->outcome_prices) {
                $prices = json_decode($market->outcome_prices, true);
                if (is_array($prices)) {
                    if (isset($prices[1])) {
                        $currentPrice = floatval($prices[1]) * 100;
                    } elseif (isset($prices[0])) {
                        $currentPrice = (1 - floatval($prices[0])) * 100;
                    }
                }
            }

            if ($market->best_ask !== null && $market->best_ask > 0) {
                $currentPrice = floatval($market->best_ask) * 100;
            }

            // Generate historical data points
            $basePrice = $currentPrice;
            $priceVariation = min(20, abs($basePrice - 50));
            $highchartsData = [];

            foreach ($allTimes as $timeIndex => $time) {
                if ($time < $startDate) {
                    continue;
                }

                $progress = ($timeIndex + 1) / count($allTimes);
                $targetPrice = 50 + ($basePrice - 50) * $progress;
                $volatility = (($timeIndex % 3 - 1) / 3) * $priceVariation * 0.2;
                $price = max(1, min(99, $targetPrice + $volatility));

                $highchartsData[] = [
                    $time->timestamp * 1000,
                    round($price, 2)
                ];
            }

            if (count($highchartsData) > 0) {
                $highchartsData[count($highchartsData) - 1][1] = round($currentPrice, 2);
            }

            // Format name
            $priceText = $currentPrice < 1 ? '<1%' : ($currentPrice >= 99 ? '>99%' : round($currentPrice, 1) . '%');
            $marketName = $market->groupItem_title;
            if (strlen($marketName) > 40) {
                $marketName = substr($marketName, 0, 37) . '...';
            }

            // Get color
            $marketColor = $market->series_color ?? $marketColors[$index % count($marketColors)];
            if (empty($marketColor) || trim($marketColor) === '') {
                $marketColor = $marketColors[$index % count($marketColors)];
            }
            if (!str_starts_with($marketColor, '#')) {
                $marketColor = '#' . $marketColor;
            }

            $this->seriesData[] = [
                'name' => $marketName . ' ' . $priceText,
                'color' => $marketColor,
                'data' => $highchartsData,
                'market_id' => $market->id,
            ];
        }
    }
    
    public function render()
    {
        return view('livewire.market-details.chart', [
            'seriesData' => $this->seriesData
        ]);
    }
}