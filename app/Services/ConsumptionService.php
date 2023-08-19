<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ConsumptionService
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

    /**
     * 
     */
    public function calculateEstimate(string $new_date, int $eac, int $meter_type,
                                int $previous_reading, string $previous_date)
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

        $estimates = 0;

        //check if previous dates and values are set
        if ($previous_date && $previous_reading > 0) {
            //Get an array of months between both dates
            $months = CarbonPeriod::create($previous_date, '30 day', $new_date);
            $count = count($months);
            
            if ($count > 1) {
                //iterate through the months and sum their monthly estimated usage
                foreach ($months as $key => $month) {
                    if (($key > 0) && ($key < $count-1)) {
                        //calculate the estimated usage for that month and add to estimates
                        $estimates += $this->getEstimatedUsageForMonth($eac, $meter_type, $month->month); 
                    }
                }
            }

            //calculate estimate of previous date and add to estimates
            $estimates += $this->estimateFromPreviousDate($previous_date, $eac, $meter_type);

            //calculate estimate on new date and add to estimates
            $estimates += $this->estimateForNewDate($new_date, $eac, $meter_type);
        
        }else {
            //calculate estimate only for the new date
            $estimates += $this->estimateForNewDate($new_date, $eac, $meter_type);
        }

        $estimated_reading = $previous_reading + $estimates;

        return $estimated_reading;
    }

    /**
     * estimate from the previous date
     */
    private function estimateFromPreviousDate(string $date): int 
    {
        $estimate = 0;
        $date = Carbon::parse($date);

         //check if current day is at the end of the month
         $days_remaining = $this->daysToEndofMonthOnDate($date);

        // if it is not the last day on the previous date
        if ($days_remaining > 0) {

            //get estimate for month
            $monthly_usage = $this->getEstimatedUsageForMonth($this->eac, $this->type, $date->month); 
            //add estimates for remaining days in the month
            $estimate += $this->getRemainingEstimateInMonth($date, $monthly_usage, $days_remaining); 

        }

        return $estimate;
    }

    /**
     * estimate for the new date 
     * */
    private function estimateForNewDate(string $date): int 
    {
        $estimate = 0;
        $date = Carbon::parse($date);

        //check if current day is at the end of the month
        $days_remaining = $this->daysToEndofMonthOnDate($date);

        // if it is not the last day on the previous date
        if ($days_remaining > 0) {
            //get estimate for month
            $monthly_usage = $this->getEstimatedUsageForMonth($this->eac, $this->type, $date->month);  
            
            //subtract estimates for remaining days in the month from the estimated_monthly_usage
            $monthly_usage -= $this->getRemainingEstimateInMonth($date, $monthly_usage, $days_remaining); 
            
            //add remaing from the process above to the estimates
            $estimate += $monthly_usage;

        }else {
            $estimate += $this->getEstimatedUsageForMonth($this->eac, $this->type, $date->month);  
        }

        return $estimate;
    }

    /**
     * get estimated usage / consumption in a given month depending on the type of meter and 
     * estimated annual consumption
     */
    private function getEstimatedUsageForMonth(int $eac, int $meter_type, int $month): int
    {
        $month_index = $month - 1;
        return (self::CATEGORY[$meter_type][$month_index] * $eac) / 100;
    }

    /**
     * count how many days is remaining for month to finish
     */
    private function daysToEndofMonthOnDate(Carbon $date): int
    {
        $current_day = $date->day; //get current day of the month
        $last_day = $date->endOfMonth()->day; //get last day of the month

        //subtract current day in the month from last day of the month
        return $last_day - $current_day;
    }

    /**
     * get estimate for remaining days in the month for a given date
     */
    private function getRemainingEstimateInMonth(Carbon $date, int $monthly_usage, int $days_remaining): int
    {
        //get number of days in month on date
        $days = $date->daysInMonth;

        //get daily usage
        $daily_usage = $monthly_usage / $days;

        //return estimate for the remaining days in the month
        return $daily_usage * $days_remaining;
    }
}