<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use AppHttpRequests;
use AppHttpControllersController;
use JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;
use App\User;
use Auth;

class AuthenticateController extends Controller{

  public function __construct(){
      // Aplicar el middleware jwt.auth a todos los métodos de este controlador
      // excepto el método authenticate. No queremos evitar
      // el usuario de recuperar su token si no lo tiene ya
      //$this->middleware('jwt.auth', ['except' => ['login']]);

       //Route::group(['middleware' => 'authenticated'], function () {
   }

  public function index(){

    $data = [];
    $data['user_sesion'] = Auth::user(); //usuario que inicio sesion

    // Recuperar todos los usuarios de la base de datos y devolverlos
    $data['users'] = User::all();
    return response()->json([
                     'error' => false,
                     'data' => $data,
                     'status' => 'success',
                      200
            ]);
   }


   public function show(){
     return response()->json([
                      'error' => false,
                      'data' => "Hola Mundo",
                      'status' => 'success',
                       200
             ]);
   }

    public function login(Request $request)
    {

        //validamos el email y la contraseña
       $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);


        if ($validate->fails()) {
            return response()->json([
             'error' => 'validate',
             'errors' => $validate->errors(),
             'code' => 422
            ]);
        }


        $credentials = $request->only('email', 'password');

        try {
            // verifique las credenciales y cree un token para el usuario
              if (!$token = JWTAuth::attempt($credentials)) {
                  /*-- Nota:
                   * Error 401 - no autorizado: -> indica que se denegó el acceso a causa de las credenciales no válidas.*/
                  return response()->json([
                           'error' => true,
                           'message' => 'El email o la contraseña son incorrectos',
                            401
                  ]);
              }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                           'error' => true,
                           'message' => 'No se pudo crear token, intentelo otravez.',
                            500
                  ]);
        }
        // si no se encuentran errores, podemos devolver un token
        //return response()->json(['status' => 'success', 'data'=> [ 'token' => $token ]]);
        //return $this->successResponse([ 'token' => $token ], 200);
        return response()->json([ 'token' => $token ], 200);
    }



}
