<?php

namespace App\Http\Controllers\APi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use DateTime;
class MobileUserController extends Controller
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
    }

        
     /**
     * login api.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
     // return($request->all());
     if(!empty($request->input('email')) && !empty($request->input('password')) ){
        
      /*  $users = DB::table('users')
                ->where('email', '=', $mail)
                ->get();*/

                ///return($users);
                $res=User::where('email', '=', $request->input('email'))->exists();
                if($res){
                  $users = DB::table('users')
                  ->where('email', '=', $request->input('email'))
                  ->get();
                  if(password_verify($request->input('password'), $users[0]->password )){
                    return response()->json([
                      'success' => true,
                      'user' => $users
                    ]);
                  }
                    
                    else{
                      return response()->json([
                        'success' => false,
                        'message' => 'Invalid Password',
                    ], 401);
                    }

                }
                else{
                  return response()->json([
                    'success' => false,
                    'message' => 'Invalid Email and Password',
                ], 401);
                }

  

      }
 
         
    }
    ///
    /*public function test(){
        return "tesst";
    }*/
    //
     /**
     * Register api.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
      
      //return ($request->all());
        /*$user = DB::table('users')->insert([
          "name" => $request->input('name'),
          "email" => $request->input('email'),
          "password" => bcrypt($request->input('password')),
          "type" => $request->input('type')
        ]);*/
       
        $data = new User;
        $data->name = $request->input('name');
        $data->email = $request->input('email');
        $data->password = bcrypt($request->input('password'));
        $data->save();
        return response()->json([
          'success' => true,
          'user' => $data
      ]);
    }
       /**
     * accompgnateur api.
     *
     * @return \Illuminate\Http\Response
     */
    public function accompagnateur(Request $request)
    {
      
    
      $accompagnateurs = DB::select('select id,nomArabe,prenomArabe,prenomArabe,sexe,telephoneTunisien,telephoneEtranger,image,user_id from accompagnateurs ');
        return response()->json([
          'accompagnateur' => $accompagnateurs
      ]);
    }

          /**
     * vol api.
     *
     * @return \Illuminate\Http\Response
     */
    public function vol(Request $request)
    {
      $now = new DateTime();
      $packages = DB::select('select id,labelle,description,NombrePlace,NombrePlaceRestant,prix,dateDepart,image FROM packages where dateDepart> ? and etat="1"',[$now]);
        return response()->json([
          'packages' => $packages
      ]);
    }
}
