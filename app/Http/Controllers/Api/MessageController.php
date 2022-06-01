<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Laravue\Models\Message;
use App\Laravue\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\MessageResource;
use Illuminate\Support\Arr;

class MessageController extends Controller
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
        $userQuery = Message::query()->orderBy('id', 'DESC');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $keyword = Arr::get($searchParams, 'user_id', '');

        if (!empty($keyword)) {
            $userQuery->where('user_id', '=',  $keyword );
        }

        return MessageResource::collection($userQuery->paginate($limit));
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
        $data = new Message;
        $data->description = $request->input('description');
        $data->user_id = $request->input('user_id');
        $data->etat = $request->input('etat');
        
        $data->save();
        return response()->json([
          'success' => true,
          'message' => $data
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Laravue\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Laravue\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
       * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return MessageResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        //
        $params = $request->all();
        $Message = Message::findOrFail($id);

         $Message->update([
            'reponseDescription' => $params['reponseDescription'],
            'etat' => '1',
    ]);
    $user = User::findOrFail( $params['user_id']);
  
    mail($user->email, 'Reponse a votre Message sur notre application',$params['reponseDescription'] );
   
    return new MessageResource($Message);

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
        $message = Message::findOrFail($id);
        try {
            $message->delete();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(null, 204);
    }
}
