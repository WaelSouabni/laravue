<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Resources\PackageResource;
use App\Http\Resources\PackageListeResource;
use App\Http\Resources\PackagesDetaillResource;
use App\Laravue\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;
use DateTime;
use Illuminate\Support\Facades\DB;


class PackageController extends Controller
{
    //
    const ITEM_PER_PAGE = 15;

    /**
     * Display a listing of the user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|ResourceCollection
     */
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $packageQuery = Package::query()->orderBy('id', 'DESC');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $keyword = Arr::get($searchParams, 'labelle', '');    
       // dd($keyword);
       if (!empty($keyword)) {
            $packageQuery->where('labelle', 'LIKE', '%' . $keyword . '%');
        }

        return PackageResource::collection($packageQuery->paginate($limit));
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
        $now = new DateTime();


        
        $validator = Validator::make(
            $request->all(),
            array_merge(
                $this->getValidationRules(),
                [
                    'labelle' => 'required|string|max:191',
                    'description' => 'required|string|max:200',
                    'NombrePlace' => 'required|numeric',
                    'NombreAcc' => 'required|numeric',
                    'prix' => 'required|numeric',
                    'dateDepart' => 'required|date|after:' .$now->format("Y-m-d H:i:s"),   
                ]
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $params = $request->all();
           // $image_64 = str_replace('data:image/jpeg;base64,', '', $params['image']);
            $test = $params['image']["dataURL"];
            //var_dump($test);
            $extension = explode('/', explode(':', substr($test, 0, strpos($test, ';')))[1])[1];
            $replace = substr($test, 0, strpos($test, ',')+1); 
            $image = str_replace($replace, '', $test); 
            $image = str_replace(' ', '+', $image); 
            $date = new DateTime();
            $imageNameWithSpace=  $date->format('Y_m_d_H_i_s').'.' .$extension;
            $imageName = str_replace(' ', '_', $imageNameWithSpace);
            Storage::disk('public')->put($imageName, base64_decode($image));

  
            $package = Package::create([
                'labelle' => $params['labelle'],
                'description' => $params['description'],
                'NombrePlace' => $params['NombrePlace'],
                'NombrePlaceRestant' => $params['NombrePlace'],
                'prix' => $params['prix'],
                'image'=>'http://localhost:8000/storage/'.$imageName,
                'NombreAcc'=>$params['NombreAcc'],
                'dateDepart' => new DateTime($params['dateDepart']),
               
            ]);
           

            return new PackageResource($package);
    }
}

    /**
     * Display the specified resource.
     *
      * @param $id
     * @return PackageResource|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        //
       // return new PackageResource($package);
       $Package = Package::findOrFail($id);
       return new PackageResource($Package);
    }

    /**
     * Update the specified resource in storage.
     *
    * @param Request $request
     * @param $id
     * @return PackageResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        //
        $validator = Validator::make($request->all(), $this->getValidationRules(false));
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $params = $request->all();
            $package = Package::findOrFail($id);

             $package->update([
                 "labelle" => $params['labelle'],
                 "description" => $params['description'],
                 "NombrePlace" => $params['NombrePlace'],
                 "prix" => $params['prix'],
                 "dateDepart" =>new DateTime($params['dateDepart']),
        ]);
        return new PackageResource($package);
    }}

    /**
     * Remove the specified resource from storage.
     *
     *
     * @param Package $id
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function destroy(Package $package)
    {
        //
      
        try {
            $package->delete();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(null, 204);
    }

     /**
     * @param bool $isNew
     * @return array
     */
    private function getValidationRules()
    {$now = new DateTime();
        return [
           
            'labelle' => 'required|string|max:191',
            'description' => 'required|string|max:200',
            'NombrePlace' => 'required|numeric',
            'prix' => 'required|numeric',
            'dateDepart' => 'required|date|after:' .$now->format("Y-m-d H:i:s"),   
        ];
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  Package $package
     * @return \Illuminate\Http\Response
     */
    public function publier($id)
    {
        $Package = Package::findOrFail($id);
        $Package->etat = '1';
 
        $Package->save();

        return new PackageResource($Package);
    }

      /**
     * Remove the specified resource from storage.
     *
     * @param  Package $package
     * @return \Illuminate\Http\Response
     */
    public function masquer($id)
    {
        $Package = Package::findOrFail($id);
        $Package->etat = '0';
 
        $Package->save();
        return new PackageResource($Package);
    }

        /**
     **/
    public function packages()
    { $packages = DB::table('packages')
        ->get();
        return PackageListeResource::collection($packages);
        
    }

    
    /**
     * Display the specified resource.
     *
      * @param $id
     * @return PackagesDetaillResource|\Illuminate\Http\JsonResponse
     */
    public function listePelerins($id)
    {
      // $package = DB::select('select nomArabe,prenomArabe,numeroDePassport,pelerins.etat,labelle,dateDepart from pelerins,packages where packages.id=pelerins.package_id and package_id=?',[$id]);

      /* $data=array(
       "package"=>$package,
    );
       return $data;*/
       $Package = Package::findOrFail($id);
       return new PackagesDetaillResource($Package);
    }

}
