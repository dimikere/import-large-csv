<?php

namespace App\Jobs;

use App\Insurance as Insurance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class ProcessCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Redis::throttle('upload-csv')->allow(1)->every(30)->then(function () {
            
          
            dump("Proccessing this file: -- ", $this->file);
            $csv = array_map('str_getcsv', file($this->file)); 
            //dd($csv);
            
            foreach($csv as $row){
                //$row_arr = explode (";", $row[0]);  

                dd($row);
                Insurance::updateOrCreate([
                    'policy_id' => $row[0]
                ],[
                    'county' => $row[1],
                    'lat' => $row[2],
                    'lng' => $row[3]
                ]);

                // Insurance::insert([
                //     'policy_id' => $row_arr[0],
                //     'county' => $row_arr[1],
                //     'lat' => $row_arr[2],
                //     'lng' => $row_arr[3]
                // ]);
            }
            dump("End the proccess from this file: -- ", $this->file);
            unlink($this->file);
        }, function () {
            // Could not obtain lock...
            return $this->release(30);
        });
        
    }
}
