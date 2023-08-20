<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class EstimationService
{
    const MPAN = 1;
    const MPRN = 2;

    const MONTHLY_ELECTRICITY_PERCENTAGE = array(13.36, 9.61, 10.01, 7.98, 	8.99, 6.68,
                                        4.72, 4.41, 6.4, 9.3, 10.17, 8.38);
    const MONTHLY_GAS_PERCENTAGE = array(15.19, 14.25, 12.67, 9.6, 7.98, 2.42, 1.38, 1.5,
                                        2.44, 7.1, 12.31, 13.16);

    const CATEGORY = [ self::MPAN => self::MONTHLY_ELECTRICITY_PERCENTAGE , 
                        self::MPRN => self::MONTHLY_GAS_PERCENTAGE];
    

    private int $eac;
    private int $type;
    private Carbon $new_date;
    private Carbon $previous_date;

    /**
     * main logic to calculate the estimated reading for a given date on receiving parameters
     *  new-date, 
     *  eac .i.e estimated annual consumption 
     *  meter_type .i.e electric or gas specified in numbers
     *  previous meter reading if given
     *  previous date if given
     */

    public function calculateEstimate(string $new_date, int $eac, int $meter_type,
                                        int $previous_reading, string $previous_date): int
    {
        /**
        * steps
        * 1. check if previous value and date are set
        * 2. if not, just calculate estimate reading for the new date
        * 3. else, find the range of months between both dates
        * 4. iterate through months excluding the start and end months to sum all the intermediate 
        *      estimated usage for each month straight away
        * 4. calculate the estimated usage for start date and end date
        * 5. add their estimates to the previous estimates from the months between both dates
        * 6. finally estimates to previous reading to get new estimated reading
        */
        $this->eac = $eac;
        $this->type = $meter_type;
        $this->new_date = Carbon::parse($new_date);
        $this->previous_date = Carbon::parse($previous_date);

        $estimates = 0;

        //check if previous dates and values are set
        if ($previous_date && $previous_reading > 0) {

            //get month interval between both dates
            $months_interval = $this->new_date->diffInMonths($this->previous_date);

            switch (true) {
                case ($months_interval < 1):
                    $estimates += $this->calculateEstimatedUsageForSameMonth($this->new_date->month);
                    break;
                case ($months_interval >= 1):
                    $estimates += $this->calculateEstimatedUsageNotSameMonth();
                    break;
                default:
                    break;
            }

        }else {
            //calculate estimate only for the new date
            $estimates += $this->estimatedUsageToNewDate($this->new_date);
        }

        $estimated_reading = $previous_reading + $estimates;

        return $estimated_reading;  
    }

    /**
     * calculate estimate for both dates within the same month
     * arrived at by multiplying 
     *  days_interval x estimated_daily_usage
     */
    private function calculateEstimatedUsageForSameMonth(int $month): int 
    {
        //get the number of days between both dates 
        $days_interval = $this->new_date->diffInDays($this->previous_date);

        //get estimated monthly usage
        $estimated_daily_usage = $this->getEstimatedDailyUsage($this->eac, $this->type, $month); 

        return $estimated_daily_usage * $days_interval;
    }

    /**
     * calculate estimate for dates that are not within the same month
     * 1. Get the list of months starting from the previous date to the new date
     * 2. if the list only contains the previous date and the new date
     * 3. Do not go into the loop,
     *      4. calculate the Estimates for the previous date and the new date and sum both together
     * 5. Else, 
     *      6. Excluding the first and the last months(dates) sum all their monthly estimated usage/consumption 
     *      7. Go to step 4 and sum with value from step 4
     * 8. Return new estimate for date range between previous and new date
     */
    private function calculateEstimatedUsageNotSameMonth(): int
    {
        
        $estimate = 0;
        //Get an array of months from start dates to end date
        $months = CarbonPeriod::create($this->previous_date, '30 day', $this->new_date);
        $count = count($months); 

        if ($count > 2) {
            //iterate through the months and sum their monthly estimated usage
            foreach ($months as $key => $month) {
                if (($key > 0) && ($key < $count-1)) {
                    //calculate the estimated usage for that month and add to estimates
                    $estimate += $this->getEstimatedMonthlyUsage($this->eac, $this->type, $month->month); 
                }
            }
        }
        
        //calculate estimate of previous date and add to estimates
       $estimate += $this->estimatedUsageFromPreviousDate($this->previous_date);

        
        //calculate estimate on new date and add to estimates
        $estimate += $this->estimatedUsageToNewDate($this->new_date);

        return $estimate;
    }

    /**
     * get estimate from the previous date
     * 1. Get number of days left to the end of the month on that date
     * 2. Get the estimated daily consumption for that month
     * 3. Mulitiply the days left by the estimated daily consumption 
     * 4. Return the new value as the estimated usage on the old date
     */
    private function estimatedUsageFromPreviousDate(Carbon $date): int
    {
        //gets days left to the end of the month on that date
        $days_remaining = $date->daysInMonth - $date->day;

        //get estimated daily usage for the month
        $estimated_daily_consumption = $this->getEstimatedDailyUsage($this->eac, $this->type, $date->month);

        return $estimated_daily_consumption * $days_remaining;
    }

    /**
     * get estimate to the new date
     * 1. Get the estimated daily consumption for the month
     * 2. Multiply estimated daily consumption by the day on given date
     * 3. Return as new value for the estimated usage/consumption to the new date
     */
    private function estimatedUsageToNewDate(Carbon $date): int
    {
        //get estimated daily usage for the month on date
        $estimated_daily_consumption = $this->getEstimatedDailyUsage($this->eac, $this->type, $date->month);

        //multiply estimated daily consumption by the day on date
        return $estimated_daily_consumption * $date->day;
    }

    /**
     * get estimated usage / consumption in a given month depending on the type of meter and 
     * estimated annual consumption
     */
    public function getEstimatedMonthlyUsage(int $eac, int $meter_type, int $month): int
    {
        $month_index = $month - 1;
        return (self::CATEGORY[$meter_type][$month_index] * $eac) / 100;
    }

    /**
     * get estimated usage / consumption in a single day depending on the type of meter and 
     * estimated annual consumption
     */
    public function getEstimatedDailyUsage(int $eac, int $meter_type, int $month): float
    {
        //calculate monthly consumption
        $estimated_monthly_consumption = $this->getEstimatedMonthlyUsage($eac, $meter_type, $month);

        //get number of days in the month
        $days = Carbon::now()->month($month)->daysInMonth;

        //divide by number of days
        return $estimated_monthly_consumption / $days;
    }

}