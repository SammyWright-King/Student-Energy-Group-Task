<?php

namespace App\Services;

use App\Models\Meter;
use App\Models\MeterReading;
use App\Repositories\MeterReadingRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MeterReadingService
{
    public function __construct(public MeterReadingRepository $mr_repo,
                                public ConsumptionService $consumption)
    {
    }

    /**
     * save reading to table
     * */
    public function sendToDB(int $meter_id, array $data): void
    {
        //set actual value for column names and values
        $new_reading = [
            "meter_id" => $meter_id,
            "reading_value" => $data['value'],
            "reading_date" => $data['date_read']
        ];

        //insert into table
        $reading = $this->mr_repo->save($new_reading);
    }

    /**
     * Optional A3
     * Validate reading against 25% expectation i.e estimatedReading
     */
    public function validateReading(int $reading_value, int $estimated_value): bool
    {
        /**
         * 1. Calculate the range i.e 25% of the expected(estimated) value
         * 2. Get the upper and the lower range
         * 3.validate the reading value in range i.e 
         *      lower_range < x < upper_range
         *      where x is the reading value
         */
        $range = (25 * $estimated_value) / 100;
        $lower_range = $estimated_value - $range;
        $upper_range = $estimated_value + $range;

        //validate
        if (($reading_value >= $lower_range) && ($reading_value <= $upper_range)) {
            return true;
        }else {
            return false;
        }
    }

    /**
     * calculate the estimate reading
     */
    public function estimateReading(Request $request, Meter $meter): JsonResponse
    {
        //validate input
        $validator = Validator::make($request->all(), [
            'new_date' => "required|date"
        ]);

        if ($validator->fails()) return response()->json(['error'=> $validator->errors()], 402);
        
        //get previous reading
        // if ($meter->readings->count() > 1) {
        //     $previous_reading= $meter->readings->first()->reading_value;  
        //     $previous_date = $meter->readings->first()->reading_date;
        // }else {
        //     $previous_reading = 0;
        //     $previous_date = null;
        // }

        // $estimate = $this->consumption->calculateEstimate($request->new_date, eac: $meter->estimated_annual_consumption,   
        //                                     meter_type: $meter->meter_type_id, previous_reading: $previous_reading,
        //                                     previous_date: $previous_date);
        // return $meter;

        $estimate = 1996;

        //save $estimate to table
        $this->sendTODB($meter->id, ["value" => $estimate, "date_read" => $request->new_date]);

        return response()->json(['message' => "estimated reading processed successfully"]);
    }

    /**
     * save new meter reading
     */
    public function saveReading(Request $request, Meter $meter): JsonResponse
    {
        //get estimated reading
        $estimated_value = 1000;

        //validate the new value against the estimated value
        if (!$this->validateReading($request->value, $estimated_value)) {
            
            return response()->json(['error' => "Not a valid meter reading."], 422);
        }

        //save entry to database
        $this->sendToDB($meter->id, $request->only(['value', 'date_read']));

        return response()->json(['message' => "New meter reading recorded successfully"]);
    }
}