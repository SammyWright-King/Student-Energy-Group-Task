<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Http\Requests\MeterReadingRequest;
use App\Http\Requests\SaveMeterRequest;
use App\Services\MeterReadingService;
use App\Services\MeteringService;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rules\File;

class MeterController extends Controller
{
    public MeteringService $meter;
    public MeterReadingService $mr;
    public UploadService $upload;

    public function __construct(MeteringService $meter_service,
                                MeterReadingService $mr_service, 
                                UploadService $upload_service)
    {
        $this->meter = $meter_service;
        $this->mr = $mr_service;
        $this->upload = $upload_service;
    }

    /**
     * @GET landing page / index of the application
     */
    public function index(): View
    {
        $meters = $this->meter->all();
        return view('meter.list', ['meters' => $meters]);
    }

    /**
     * @GET single meter
     */
    public function show(Meter $meter): View
    {
        return view('meter.show', ['meter' => $meter]);
    }

    /**
     * @GET new meter page
     */
    public function new(): View
    {
        $types = $this->meter->getTypes();
        return view('meter.new', ['types' => $types]);
    }

    /**
     * @POST request and save new meter
     */
    public function save(SaveMeterRequest $request): JsonResponse
    {
        return $this->meter->save($request);
    }

    /**
     * @GET edit meter page
     */
    public function edit(Meter $meter): View
    {
        $types = $this->meter->getTypes();
        return view('meter.edit')->with('meter', $meter)
                            ->with('types', $types);
    }

    /**
     * @POST update meter
     */
    public function update(SaveMeterRequest $request, Meter $meter): JsonResponse
    {
        return $this->meter->update($request, $meter->id);
    }

    /**
     * @POST save new meter reading to table
     */
    public function saveMeterReading(MeterReadingRequest $request, Meter $meter)
    {
        return $this->mr->saveReading($request, $meter);
    }

    /**
     * @POST generate estimated reading
     */
    public function estimateMeterReading(Request $request, Meter $meter): JsonResponse
    {
        return $this->mr->estimateReading($request, $meter); 
    }

    /**
     * @POST bulk upload document
     */
    public function bulkUpload(Request $request, Meter $meter): JsonResponse
    {
        return $this->upload->processDocument($request);
    }
}
