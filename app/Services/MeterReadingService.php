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

    /**save reading to table*/
    public function save(Request $request, int $id): JsonResponse
    {
        //set actual value for column names and values
        $new_reading = [
            "meter_id" => $id,
            "reading_value" => $request->value,
            "reading_date" => $request->date_read
        ];

        //insert into table
        $reading = $this->mr_repo->save($new_reading);

        if ($reading instanceof MeterReading) {
            return response()->json(['message' => "new reading saved successfully"]);
        }else {
            return response()->json(['error' => 'problem saving data for meter'], 500);
        }
    }

    /**
     * calculate the estimate reading
     */
    public function estimateReading(Request $request, Meter $meter)
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
        return response()->json(['notice' => "estimated annual consumption not set"], 500);
    }
}