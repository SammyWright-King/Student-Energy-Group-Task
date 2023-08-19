<?php

namespace App\Services;

class ConsumptionService
{
    const MPAN = 1;
    const MPRN = 2;

    const MONTHLY_ELECTRICITY_PERCENTAGE = array(13.36, 9.61, 10.01, 7.98, 	8.99, 6.68,
                                        4.72, 4.41, 6.4, 9.3, 10.17, 8.38);
    const MONTHLY_GAS_PERCENTAGE = array(15.19, 14.25, 12.67, 9.6, 7.98, 2.42, 1.38, 1.5,
                                        2.44, 7.1, 12.31, 13.16);

    const CATEGORY = [ 1 => "MONTHLY_ELECTRICITY_PERCENTAGE", 2 => "MONTHLY_GAS_PERCENTAGE"];
    
    public function calculateEstimate(string $new_date, int $eac, int $meter_type,
                                int $previous_reading, string $previous_date)
    {
        /**
         * steps
         * 1. Check the meter type to know which energy category it belongs to
         * 2. Get month from new date
         * 3. Calculate estimated usage for that month based on percentage by eac
         * 4. Get the number of days in that month and divide estimated usage by it to get daily usage
         * 5. find the difference between previous date and new date
         * 6. 
         */

        return 3;
    }

    public function calculateEstimate2(mixed $new_date, int $eac,
                                    int $previous_reading, string $previous_date): int
    {
        /**
         * steps to arrive at the estimated reading
         * 1. Divide the EAC(Estimated Annual Consumption) by 365 days an
         * 2. Assign value from step 1 to variable estimated_daily_consumption
         * 3. Calculate number of days between previous date and new date
         * 4. Assign value from step 3 to variable days_difference
         * 5. Multiply the number of days by the estimated_daily_consumption
         * 6. Assign value from step 5 to variable estimated_usage
         * 7. Add previous reading to estimated - usage to get new estimated-reading
         */

        //divide the eac by 365
        $estimated_daily_consumption = $eac / 365;

        //calculate the number of days between previous date and new date
        $days = $this->days_difference($new_date, $previous_date);

        //multiply the estimatated daily consumption by the number of days to get estimated usage
        $estimated_usage = $days * $estimated_daily_consumption;

        //new estimated reading
        $estimated_reading = $previous_reading + $estimated_usage;

        return $estimated_reading;
    }

    private function days_difference(string $date1, string $date2)
    {
        $date1 = date_create($date1);
        $date2 = date_create($date2);
        $interval = $date1->diff($date2);
        echo $interval->days;
    }

    private function getEstimatedUsageForMonth(): int
    {

    }
}