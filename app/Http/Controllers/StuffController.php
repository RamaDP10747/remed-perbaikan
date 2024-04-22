<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\Stuff;
use Illuminate\Http\Request;

class StuffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Stuff::Stock()->toArray();

            return ApiFormatter::sendResponse(200, 'succes',$data);
        }catch(\Exception $err) {
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

            $this->validate($request, [
                'name' => 'required',
                'category'=> 'required'
            ]);

            $data = Stuff::create([
                'name'=> $request->name,
                'category'=> $request->category,
            ]);
            
            return ApiFormatter::sendResponse(200, 'succes', $data);
        }catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stuff  $stuff
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = Stuff::where('id', $id)->first();

            if (is_null($data)){
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found!');
            }else{
                return ApiFormatter::sendResponse(200, 'succes', $data );
            }
        }catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Stuff  $stuff
     * @return \Illuminate\Http\Response
     */
    public function edit(Stuff $stuff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stuff  $stuff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'category' => 'required',
            ]);

            $checkproses = Stuff::where('id', $id )->update([
                'name' => $request->name,
                'category' => $request->category,
            ]);

            if ($checkproses) {
                $data = Stuff::find($id);
                return ApiFormatter::sendResponse(200, 'succes', $data);
            }else {
                return ApiFormatter::sendResponse(400, 'badRequest', 'gagal mengubah data!');
            }
        }catch(\Exception $err ) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stuff  $stuff
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $checkProses = Stuff::where('id', $id)->delete();

            return ApiFormatter::sendResponse(200, 'success', 'Data Stuff berhasil dihapus');
        }catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'Bad Request', $err->getMessage());
        }
    }

    public function trash(){
        try {
            $data = Stuff::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'succes', $data);
        }catch (\Exception $err){
            return ApiFormatter::sendResponse(400, 'Bad Request', $err->getMessage());
        }
    }

    public function restore($id){
        try {

            $checkProses = Stuff::onlyTrashed()->where('id',$id)->restore();

            if($checkProses){
                $data = Stuff::find($id);
                return ApiFormatter::sendResponse(200, 'Succes', $data);
            }else {
                return ApiFormatter::sendResponse(400, 'Bad Request', 'Gagal mengembalikan data!' );
            }
        } catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'Bad Request',$err->getMessage());
        }
    }

    public function deletePermanent($id){
        try{
            $checkProses = Stuff::onlyTrashed()->where('id', $id)->forceDelete();

            return ApiFormatter::sendResponse(200, 'Succes', 'Berhasil menghapus data permanen dari stuff');
        }catch (\Exception $err){
            return ApiFormatter::sendResponse(400, 'Bad Request',$err->getMessage());
        }
    }
}