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
        try {
            $data = InboundStuff::all()->toArray();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
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
    public function show($id)
    {
        try{
            $data = InboundStuff::where('id', $id)->first();

            if (is_null($data)) {
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found!');
            } else {
                return ApiFormatter::sendResponse(200, 'success', $data);
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
            }

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
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'stuff_id' => 'required',
                'total' => 'required',
                'date' => 'required',
                'proff_file' => 'required',
            ]);

            $checkProses = InboundStuff::where('id', $id)->update([
                'stuff_id' => $request->stuff_id,
                'total' => $request->total,
                'date' => $request->date,
                'proff_file' => $request->proff_file,
            ]);

            if($checkProses) {
                $data = InboundStuff::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengubah data! ');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
            }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InboundStuffs  $inboundStuffs
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $checkProses = InboundStuff::where('id', $id)->delete();

                return ApiFormatter::sendResponse(200, 'success', 'Data berhasil dihapus!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
        }

    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InboundStuffs  $inboundStuffs
     * @return \Illuminate\Http\Response
     */
    public function trash()
    {
        try {
            $data = InboundStuff::onlyTrashed()->get();

                return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
        }

    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InboundStuffs  $inboundStuffs
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {
            $checkProses = InboundStuff::onlyTrashed()->where('id', $id)->restore();

            if($checkProses) {
                $data = InboundStuff::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengembalikan data! ');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
            }

    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InboundStuffs  $inboundStuffs
     * @return \Illuminate\Http\Response
     */
    public function deletePermanent($id)
    {
        try {
            $checkProses = InboundStuff::onlyTrashed()->where('id', $id)->forceDelete();

                return ApiFormatter::sendResponse(200, 'success', 'Berhasil menghapus data secara permanen!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
        }
    }

    public function addStock(Request $request, $id){
        try {
            $getStuffStock = StuffStock::find($id);

            if (!$getStuffStock) {
                return ApiFormatter::sendResponse(404, false, 'Data Stuff Stock Not Found');
            } else {
                $this->validate($request, [
                    'total_available' => 'required',
                    'total_defec' => 'required',
                ]);

                $addStock = $getStuffStock->update([
                    'total_available' => $getStuffStock['total_available'] + $request->total_available,
                    'total_defec' => $getStuffStock['total_defec'] + $request->total_defec,
                ]);

                if ($addStock) {
                    $getStockAdded = StuffStock::where('id', $id)->with('stuff')->first();

                    return ApiFormatter::sendResponse(200, true, 'Successfully Add A Stock Of Stuff Stock Data', $getStockAdded);
                }
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
        }
    }

}