<?php

namespace App\Http\Controllers\APi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\AccompgnateurResource;
use App\Http\Resources\UserListResource;
use App\Laravue\Models\Accompagnateur;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Validator;
use DateTime;
use Illuminate\Support\Facades\DB;


class AccompagnateurController extends Controller
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
        $searchParams = $request->all();
        $AccompagnateurQuery = Accompagnateur::query()->orderBy('id', 'DESC');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $keyword = Arr::get($searchParams, 'nomArabe', '');    
       // dd($keyword);
       if (!empty($keyword)) {
            $AccompagnateurQuery->where('nomArabe', 'LIKE', '%' . $keyword . '%');
        }

        return AccompgnateurResource::collection($AccompagnateurQuery->paginate($limit));
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
        $validator = Validator::make(
            $request->all(),
            array_merge(
                $this->getValidationRules(),
                [
                    'nomArabe' => 'required|string|max:191',
                    'prenomArabe' => 'required|string|max:191',
                    'sexe' => 'required|in:0,1',
                    'telephoneTunisien' => 'required|numeric|min:8',
                    'telephoneEtranger' => 'required|numeric|min:8',
                ]
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $params = $request->all();
            //image
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


            //
            $Accompagnateur = Accompagnateur::create([
                'nomArabe' => $params['nomArabe'],
                'prenomArabe' => $params['prenomArabe'],
                'sexe' => $params['sexe'],
                'telephoneTunisien' => $params['telephoneTunisien'],
                'telephoneEtranger' => $params['telephoneEtranger'],
                'image'=>'http://localhost:8000/storage/'.$imageName,
                'user_id' => $params['user_id'],
                'package_id' => $params['package_id'],            
            ]);
           

            return new AccompgnateurResource($Accompagnateur);
    }
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
        $validator = Validator::make($request->all(), $this->getValidationRules(false));
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $params = $request->all();
            $Accompagnateur = Accompagnateur::findOrFail($id);

             $Accompagnateur->update([
                'nomArabe' => $params['nomArabe'],
                'prenomArabe' => $params['prenomArabe'],
                'sexe' => $params['sexe'],
                'telephoneTunisien' => $params['telephoneTunisien'],
                'telephoneEtranger' => $params['telephoneEtranger'],
                'user_id' => $params['user_id'],
                'package_id' => $params['package_id'],    
        ]);
      
        return new AccompgnateurResource($Accompagnateur);
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

        $Accompagnateur = Accompagnateur::findOrFail($id);
        try {
            $Accompagnateur->delete();
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
    {
    
        return [
            'nomArabe' => 'required|string|max:191',
            'prenomArabe' => 'required|string|max:191',
            'sexe' => 'required|in:0,1',
            'telephoneTunisien' => 'required|numeric|min:8',
            'telephoneEtranger' => 'required|numeric|min:8',
        ];
    }

        //
     /**
     * Register api.
     *
     * @return \Illuminate\Http\Response
     */
    public function registerAccompgnateur(Request $request)
    {
        $params = $request->all();
  
        //
        $Accompagnateur = Accompagnateur::create([
            'nomArabe' => $params['nomArabe'],
            'prenomArabe' => $params['prenomArabe'],
            'sexe' => $params['sexe'],
            'telephoneTunisien' => $params['telephoneTunisien'],
            'telephoneEtranger' => $params['telephoneEtranger'],
            'user_id' => $params['user_id'],
                  
        ]);
       
        return response()->json([
          'success' => true,
          'user' => $Accompagnateur
      ]);
    }
         /**
     **/
    public function accompgnateurs()
    { $users =DB::select('select distinct(users.id),users.name from users,accompagnateurs where users.id=accompagnateurs.user_id');

        return UserListResource::collection($users);
        
    }

        /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function masquer($id)
    {
        $Accompagnateur = Accompagnateur::findOrFail($id);
        $Accompagnateur->etat = '0';
        $Accompagnateur->save();

        return response()->json($Accompagnateur, 204); 
     
    }
            /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function publier($id)
    {
        $Accompagnateur = Accompagnateur::findOrFail($id);
        $Accompagnateur->etat = '1';
        $Accompagnateur->save();

        return response()->json($Accompagnateur, 204); 
     
    }
}
