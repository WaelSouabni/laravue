<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Laravue\Models\AccompagnateurPackage;
use App\Http\Resources\AccompagnateurPackageResource;
use App\Laravue\Models\Package;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Validator;
use DateTime;
use Illuminate\Support\Facades\DB;

class AccompagnateurPackageController extends Controller
{
    const ITEM_PER_PAGE = 15;

    /**
     * Display a listing of the user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|ResourceCollection
     */
    public function index(Request $request)
    {
        //
        $searchParams = $request->all();
        $AccompagnateurPackageQuery = AccompagnateurPackage::query()->orderBy('id', 'DESC');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $keyword = Arr::get($searchParams, 'user_id', '');    
        $keyword2 = Arr::get($searchParams, 'package_id', '');   
       // dd($keyword);
       if (!empty($keyword)) {
            $AccompagnateurPackageQuery->where('user_id', '=',$keyword);
        }
        if (!empty($keyword2)) {
            $AccompagnateurPackageQuery->where('package_id', '=',$keyword2);
        }

        return AccompagnateurPackageResource::collection($AccompagnateurPackageQuery->paginate($limit));
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
        ]);
        
       /* $package = Package::findOrFail($params['package_id']);
        //return($params['role']);
        if($package['role']== 0){
         $package['NombreAccRestant']=$package['NombreAccRestant']-1;
         $package->save();
           return($package); 
        }
        else{
            $package->update([
                "NombrePlaceRestant" => $package['NombrePlaceRestant']-1,
            ]);
        }*/
    
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
        $params = $request->all();
        $AccompagnateurPackage = AccompagnateurPackage::findOrFail($id);
         $AccompagnateurPackage->update([
            'user_id' => $params['user_id'],
            'package_id' => $params['package_id'],
    ]);

    return new AccompagnateurPackageResource($AccompagnateurPackage);
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
        if($package['role']== 0){
            $package['NombreAccRestant']=$package['NombreAccRestant']+1;
            $package->save();
             // return($package); 
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
     
    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function valider($id)
    {
        //return ($id);
         $AccompagnateurPackage = AccompagnateurPackage::findOrFail($id);
        $AccompagnateurPackage->etat = '2';
        $AccompagnateurPackage->save();

        $package = Package::findOrFail($AccompagnateurPackage['package_id']);
        $package['NombreAccRestant']=$package['NombreAccRestant']-1;
        $package->save();
        

        return response()->json($AccompagnateurPackage, 204); 
    }

      /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function invalider($id)
    {
        $AccompagnateurPackage = AccompagnateurPackage::findOrFail($id);
        $AccompagnateurPackage->etat = '0';
        $AccompagnateurPackage->save();

        return response()->json($AccompagnateurPackage, 204); 
     
    }


            //
            public function AccompgnateurPackage($id)
            {       
                $package = DB::select('select packages.image,packages.labelle,packages.dateDepart,accompagnateur_packages.created_at,accompagnateur_packages.etat from accompagnateur_packages,packages where packages.id=accompagnateur_packages.package_id and accompagnateur_packages.user_id=?',[$id]);
    
                return $package;
                
            }
    

}
