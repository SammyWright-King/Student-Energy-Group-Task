<?php

namespace App\Services;

use App\Jobs\ProcessBulkEntryJob;
use App\Repositories\MeterRepository;
use App\Repositories\MeterReadingRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UploadService
{
    public function __construct(public MeterRepository $meter_repo,
                            public MeterReadingRepository $mr_repo)
    {
    }

    /**
     * upload document
     */
    public function upload(Request $request): string
    {
        $fileName = time().'_'.$request->file->getClientOriginalName();
        $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');

        return $filePath;
    }

    /**
     * read the csv
     */
    public function readCSV(string $path): void
    {
        try{
            // $file_path = Storage_path($path);
            $file_path = public_path("storage/".$path);
            $file = fopen($file_path, 'r');

            $header = fgetcsv($file);
        
            //$reading = [];
            while ($row = fgetcsv($file)) {
                // $reading[] = array_combine($header, $row);

                $this->saveEntry($row[2], $row[0], $row[1]);
            }

            fclose($file);

        }catch (\Exception $e) {
            Log::error($e->errors());
        }
       
    }

    /**
     * save entry to readings table
     */
    private function saveEntry(string $identifier, int $reading_value, string $date): void
    {
        //check if meter exists
        $meter = $this->meter_repo->findWhere('mpxn', $identifier);

        if($meter && is_int($reading_value)){
            $entry = [
                "meter_id" => $meter->id,
                "reading_value" => $reading_value,
                "reading_date" => Carbon::parse($date)->format("Y-m-d")
            ];
            //save to meter reading table
            $this->mr_repo->save($entry);
        }   
    }

    /**
     * upload document and process it
     */
    public function processDocument(Request $request): JsonResponse
    {
         //validate input request first
         $validator = Validator::make($request->all(), [
            'file' => ['required', File::types(['csv'])]
        ]);
        
        //upload document
        $file_path = $this->upload($request);

        //use job to read data
        ProcessBulkEntryJob::dispatch($file_path);

        return response()->json(['message' => "bulk insert successful"]);
    }

}