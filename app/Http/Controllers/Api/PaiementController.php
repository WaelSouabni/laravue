<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Laravue\Models\Paiement;
use Illuminate\Http\Request;
use App\Http\Resources\PaiementResource;
use Illuminate\Support\Arr;
class PaiementController extends Controller
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
        $PaiementQuery = Paiement::query()->orderBy('id', 'DESC');
       // $packages = DB::select('select id,labelle,description,NombrePlace,NombrePlaceRestant,prix,dateDepart,image FROM packages');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $keyword = Arr::get($searchParams, 'user_id', '');    
        $keyword2 = Arr::get($searchParams, 'package_id', '');   
       // dd($keyword);
       if (!empty($keyword)) {
            $PaiementQuery->where('user_id', '=',$keyword);
        }
        if (!empty($keyword2)) {
            $PaiementQuery->where('package_id', '=',$keyword2);
        }

        return PaiementResource::collection($PaiementQuery->paginate($limit));
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

        $Paiement = Paiement::create([
            'user_id' => $params['user_id'],
            'type' => $params['type'],
            'etat' => $params['etat'],
            'montant' => $params['montant'],
            'description' => $params['description'],
            'package_id' => $params['package_id'],
           
        ]);
       

        return new PaiementResource($Paiement);
 }

    /**
     * Display the specified resource.
     *
     * @param  \App\Laravue\\Models\\Paiement  $paiement
     * @return \Illuminate\Http\Response
     */
    public function show(Paiement $paiement)
    {
        //
        return new PaiementResource($paiement);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Laravue\\Models\\Paiement  $paiement
     * @return \Illuminate\Http\Response
     */
    public function edit(Paiement $paiement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Laravue\\Models\\Paiement  $paiement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Paiement $paiement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $Paiement = Paiement::findOrFail($id);
        try {
            $Paiement->delete();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(null, 204);
    }
}
