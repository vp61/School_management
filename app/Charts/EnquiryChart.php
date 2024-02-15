<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class EnquiryChart extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // $this->title('Enquiry Chart')
        //     ->loaderColor('#46b8da')
        //     ->options([
        //         'legend' => [
        //             'display' => true,
        //         ]
        //     ]);
    }
}
