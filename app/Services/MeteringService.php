<?php

namespace App\Services;

use App\Models\Meter;
use App\Models\MeterType;
use App\Repositories\MeterRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class MeteringService
{
    public function __construct(public MeterRepository $meter_repo)
    {
    }

    /**
     * return all meters in database
     */
    public function all(): Collection
    {
        return $this->meter_repo->fetchAll();
    }

    /**
     * return types of meter
     */
    public function getTypes(): Collection
    {
        return MeterType::all();
    }


    /**
     * return true if meter was saved successfully or false otherwise
     */
    public function save(Request $request): JsonResponse
    {
        //check if meter already exists
        $meter = $this->meter_repo->findWhere('mpxn', $request->serial_number);

        if (!$meter) {
            //pass value to correct column name
            $new_meter_info = [
                "mpxn" => $request->serial_number,
                "meter_type_id" => $request->meter_type,
                "installation_date" => $request->date_installed,
                "estimated_annual_consumption" => $request->eac
            ];

            $meter = $this->meter_repo->save($new_meter_info);

            if (! $meter instanceof Meter) {
                return response()->json(['error' => 'problem saving data for meter'], 500);
            }
        }

        return response()->json(['message' => "successful", 'meter' => $meter]);
        //return redirect()->route('home');
    }

    /**
     * edit meter record
     */
    public function update(Request $request, int $id): JsonResponse
    {
         $new_meter_info = [
            "mpxn" => $request->serial_number,
            "meter_type_id" => $request->meter_type,
            "installation_date" => $request->date_installed,
            "estimated_annual_consumption" => $request->eac
        ];

        $meter = $this->meter_repo->update($id, $new_meter_info);

        if (! $meter instanceof Meter) {
            return response()->json(['error' => 'problem saving data for meter'], 500);
        }

        return response()->json(['message' => "successful", 'meter' => $meter]);
        //return redirect()->route('home');
    }
}