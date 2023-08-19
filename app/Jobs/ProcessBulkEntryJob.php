<?php

namespace App\Jobs;

use App\Services\UploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;

class ProcessBulkEntryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    /**
     * Create a new job instance.
     */
    public function __construct(public string $path)
    {
        
    }

    /**
     * Execute the job.
     */
    public function handle(UploadService $upload): void
    {
        $upload->readCSV($this->path);
       
    }
}
