<?php

namespace App\Http\Controllers;

use App\Insurance as Insurance;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('import');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file =  file($request->file->getRealPath());
        $data = array_slice($file, 1);
        $parts = array_chunk($data, 5000);

        foreach($parts as $index => $part){
            $index++;
            $fileName = resource_path('./pending-files/'.date('y-m-d-H-i-s-').$index.".csv");
            file_put_contents($fileName, $part);
        }
        
        (new Insurance())->importToDb();

        session()->flash('status', 'queued for importing');

        return redirect('import');
    }
}
