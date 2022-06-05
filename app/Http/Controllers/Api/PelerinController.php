<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PelerinResource;
use App\Http\Resources\Pelerins2Resource;
use App\Http\Resources\UserListResource;
use App\Http\Controllers\Controller;
use App\Laravue\Models\Pelerin;
use App\Laravue\Models\Package;
use App\Laravue\Models\User;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Validator;
use DateTime;
use DateInterval;


class PelerinController extends Controller
{
    const ITEM_PER_PAGE = 15;
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|ResourceCollection
     */
    public function index(Request $request)
    {
        //
        $searchParams = $request->all();
        $userQuery = Pelerin::query()->orderBy('id', 'DESC');
        //$userQuery = Pelerin::orderBy('id', 'DESC')->get();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $nomArabe = Arr::get($searchParams, 'nomArabe', '');
        $package_id = Arr::get($searchParams, 'package_id', '');
      
        if (!empty($nomArabe)) {
            $userQuery->where('nomArabe', 'LIKE', '%' . $nomArabe . '%');
            $userQuery->where('prenomArabe', 'LIKE', '%' . $nomArabe . '%');
        }
        if (!empty($package_id)) {
            $userQuery->where('package_id', '=', $package_id);
        }

        return PelerinResource::collection($userQuery->paginate($limit)); 
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
        $d1 = new DateTime();
        $d1->add(new DateInterval('P6M'));
        $validator = Validator::make(
            $request->all(),
            array_merge(
                $this->getValidationRules(),
                [
                    'nomArabe' => 'required|string|max:191',
                    'prenomArabe' => 'required|string|max:191',
                    'dateNaissance' => 'required|date|before:' .$now->format('Y-m-d'),
                    'sexe' => 'required|in:0,1',
                    'telephoneTunisien' => 'required|numeric|min:8',
                    'numeroDePassport' => 'required|string|min:7',
                    'dateExpiration' => 'required|date|after:' .$d1->format('Y-m-d'),
                    'dateEmission' => 'required|date',
                ]
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $params = $request->all();
           
            //Storage::disk('public')->put('public', base64_decode($image));
  
            $Pelerin = Pelerin::create([
                'nomArabe' => $params['nomArabe'],
                'prenomArabe' => $params['prenomArabe'],
                'dateNaissance' => new DateTime($params['dateNaissance']),
                'sexe' => $params['sexe'],
                'telephoneTunisien' => $params['telephoneTunisien'],
                'numeroDePassport' => $params['numeroDePassport'],
                'dateExpiration'=>new DateTime($params['dateExpiration']),
                'dateEmission' => new DateTime($params['dateEmission']),
                'user_id' => $params['user_id'],
                'createur_id'=>1,
                'package_id' => $params['package_id'],
                'etat'=>$params['etat'],
             
               
            ]);
            $package = Package::findOrFail($params['package_id']);
            $package->update([
                "NombrePlaceRestant" => $package['NombrePlaceRestant']-1,
            ]);
           

            return new PelerinResource($Pelerin);
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  Pelerin $Pelerin
     * @return \Illuminate\Http\Response
     */
    public function show(Pelerin $Pelerin)
    {
        //
        return new PelerinResource($Pelerin);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return PelerinResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        //
        $validator = Validator::make($request->all(), $this->getValidationRules(false));
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $params = $request->all();
            $Pelerin = Pelerin::findOrFail($id);

             $Pelerin->update([
                'nomArabe' => $params['nomArabe'],
                'prenomArabe' => $params['prenomArabe'],
                'dateNaissance' => new DateTime($params['dateNaissance']),
                'sexe' => $params['sexe'],
                'telephoneTunisien' => $params['telephoneTunisien'],
                'numeroDePassport' => $params['numeroDePassport'],
                'dateExpiration'=>new DateTime($params['dateExpiration']),
                'dateEmission' => new DateTime($params['dateEmission']),
                'user_id' => $params['user_id'],
                'createur_id'=>1,
                'package_id' => $params['package_id'],
        ]);

        return new PelerinResource($Pelerin);
    }
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
        $Pelerin = Pelerin::findOrFail($id);
        try {
            $Pelerin->delete();
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
         $Pelerin = Pelerin::findOrFail($id);
        $Pelerin->etat = '2';
        $Pelerin->save();

        return response()->json($Pelerin, 204); 
    }

      /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function invalider($id)
    {
        $Pelerin = Pelerin::findOrFail($id);
        $Pelerin->etat = '0';
        $Pelerin->save();

        return response()->json($Pelerin, 204); 
     
    }

         /**
     * @param bool $isNew
     * @return array
     */
    private function getValidationRules()
    {
        $now = new DateTime();
        $d1 = new DateTime();
        $d1->add(new DateInterval('P6M'));
        return [
           
            'nomArabe' => 'required|string|max:191',
            'prenomArabe' => 'required|string|max:191',
            'dateNaissance' => 'required|date|before:' .$now->format('Y-m-d'),
            'sexe' => 'required|in:0,1',
            'telephoneTunisien' => 'required|numeric|min:8',
            'numeroDePassport' => 'required|string|min:7',
            'dateExpiration' => 'required|date|after:' .$d1->format('Y-m-d'),
            'dateEmission' => 'required|date',  
        ];
    }

        //
        public function usersPackages($id)
        { $users = DB::table('pelerins')
            ->where('package_id', '==', $id)
            ->get();
            return UserListResource::collection($users);
            
        }
        //
        public function PackagesUser($id)
        {       
            $package = DB::select('select packages.image,packages.labelle,packages.dateDepart,pelerins.created_at,pelerins.etat from pelerins,packages where packages.id=pelerins.package_id and pelerins.user_id=?',[$id]);

            return $package;
            
        }

            /**
     * Register api.
     *
     * @return \Illuminate\Http\Response
     */
    public function registerPelerin(Request $request)
    {
        $params = $request->all();
  
        //
        $Pelerin = Pelerin::create([
            'nomArabe' => $params['nomArabe'],
            'prenomArabe' => $params['prenomArabe'],
            'dateNaissance' => new DateTime($params['dateNaissance']),
            'sexe' => $params['sexe'],  
            'user_id' => $params['user_id'], 
            'createur_id' => $params['user_id'], 
        ]);
       
        return response()->json([
          'success' => true,
          'Pelerin' => $Pelerin
      ]);
    }
    
    /**
     * Register api.
     *
     * @return \Illuminate\Http\Response
     */
    public function modfierPelerin(Request $request)
    {
        $params = $request->all();
        //$Pelerin = Pelerin::findOrFail($params['user_id'],);
       // return($params['user_id']);
        $query = Pelerin::where('user_id','=',$params['user_id']);
        $Pelerin = $query->latest()->firstOrFail();
        if( $Pelerin['package_id']== null){
            $Pelerin->package_id=$params['package_id'];
            $Pelerin->numeroDePassport=$params['numeroDePassport'];
            $Pelerin->dateExpiration=$params['dateExpiration'];
            $Pelerin->dateEmission=$params['dateEmission'];
            $Pelerin->telephoneTunisien=$params['telephoneTunisien'];
            $Pelerin->save();
            $package = Package::findOrFail($params['package_id']);
            $package->update([
                "NombrePlaceRestant" => $package['NombrePlaceRestant']-1,
            ]);
            return response()->json([
                'success' => true,
                'Pelerin' => $Pelerin
            ]);
        }else{
             $pelerin = Pelerin::create([
            'nomArabe' => $Pelerin['nomArabe'],
            'prenomArabe' => $Pelerin['prenomArabe'],
            'dateNaissance' => new DateTime($Pelerin['dateNaissance']),
            'sexe' => $Pelerin['sexe'],  
            'user_id' => $Pelerin['user_id'], 
            'createur_id' => $Pelerin['user_id'], 
            'package_id' => $params['package_id'],
            'numeroDePassport' => $params['numeroDePassport'],
            'dateExpiration' => new DateTime($params['dateExpiration']),
            'dateEmission' => new DateTime($params['dateEmission']),
            'telephoneTunisien' => $params['telephoneTunisien'], 
        ]);
        $package = Package::findOrFail($params['package_id']);
        $package->update([
            "NombrePlaceRestant" => $package['NombrePlaceRestant']-1,
        ]);
        return response()->json([
            'success' => true,
            'Pelerin' => $pelerin
        ]);
        }
    }
}