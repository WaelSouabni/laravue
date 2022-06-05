<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Laravue\Models\AccompagnateurPackage;
use App\Http\Resources\AccompagnateurPackageResource;
use App\Laravue\Models\Package;

class AccompagnateurPackageController extends Controller
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
        //
        $params = $request->all();
        $AccompagnateurPackage = AccompagnateurPackage::create([
            'user_id' => $params['user_id'],
            'package_id' => $params['package_id'],
            'role'=>$params['role'],
        ]);
        
        $package = Package::findOrFail($params['package_id']);
        //return($params['role']);
        if($params['role']== 0){
         $package['NombreAccRestant']=$package['NombreAccRestant']-1;
         $package->save();
           return($package); 
        }
        else{
            $package->update([
                "NombrePlaceRestant" => $package['NombrePlaceRestant']-1,
            ]);
        }
    
        return new AccompagnateurPackageResource($AccompagnateurPackage);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $AccompagnateurPackage = AccompagnateurPackage::findOrFail($id);
        $package = Package::findOrFail($AccompagnateurPackage['package_id']);
        if($params['role']== 0){
            $package['NombreAccRestant']=$package['NombreAccRestant']+1;
            $package->save();
              return($package); 
           }
        else{
            $package->update([
                "NombrePlaceRestant" => $package['NombrePlaceRestant']+1,
            ]);
        }
        try {
            $AccompagnateurPackage->delete();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(null, 204);
    }
}
