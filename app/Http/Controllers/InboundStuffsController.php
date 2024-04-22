<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\InboundStuffs;
use App\Models\Stuff;
use App\Models\StuffStock;
use Illuminate\Http\Request;


class InboundStuffsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        } {
            $this->validate($request, [
                'stuff_id' => 'required',
                'total' => 'required',
                'date' => 'required',
                'proff_file' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            if ($request->hasfile('proff_file')) {
                $proof = $request->file('proff_file');
                $destinationPath = 'proof/';
                $proffName = date('YmdhHis') . "." . $proof->getClientOriginalExtension();
                $proof->move($destinationPath, $proffName);
            }

            $createstock = InboundStuffs::create([
                'stuff_id' => $request->stuff_id,
                'total' => $request->total,
                'date' => $request->date,
                'proff_file' => $proffName,
            ]);

            if ($createstock) {
                $getStuff = Stuff::where('id', $request->stuff_id)->first();
                $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();

                if (!$getStuffStock) {
                    $updateStock = StuffStock::create([
                        'stuff_id' => $request ->stuff_id,
                        'total_available' => $request->total,
                        'total_defec' => 0,
                    ]);
                } else {
                    $updateStock = $getStuffStock->update ([
                        'stuff_id' => $request ->stuff_id,
                        'total_available' => $getStuffStock['total_available'] + $request->total,
                        'total_defec' => $getStuffStock['total_defec'],
                    ]);
                } 

                if ($updateStock) {
                    $getStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
                    $Stuff = [
                        'stuff' => $getStuff,
                        'InboundStuff' => $createstock,
                        'stuffStock' => $getStock,
                    ];

                    return ApiFormatter::sendResponse(200, 'Successfully Create A Inbound Stuff Data', $Stuff);
                } else {
                    return ApiFormatter::sendResponse(400, false, 'Successfully Create A Inbound Stuff Data');
                }
                    
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InboundStuffs  $inboundStuffs
     * @return \Illuminate\Http\Response
     */
    public function show(InboundStuffs $inboundStuffs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InboundStuffs  $inboundStuffs
     * @return \Illuminate\Http\Response
     */
    public function edit(InboundStuffs $inboundStuffs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InboundStuffs  $inboundStuffs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InboundStuffs $inboundStuffs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InboundStuffs  $inboundStuffs
     * @return \Illuminate\Http\Response
     */
    public function destroy(InboundStuffs $inboundStuffs)
    {
        //
    }
}
