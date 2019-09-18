<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Validator;
use App\User;
use JWTAuth;
use Auth;
use Tymon\JWTAuthExceptions\JWTException;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;

class AuthenticateController extends Controller
{

    public function __construct(){
      // Aplicar el middleware jwt.auth a todos los métodos de este controlador
      // excepto el método authenticate. No queremos evitar
      // el usuario de recuperar su token si no lo tiene ya
      $this->middleware('jwt.auth', ['only' => ['user_data']]);

   }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

   public function register(Request $request)
   {
        //reglas de validacion
        $rules = [
          'name' => 'required',
          'surname' => 'required',
          'email' => 'required|email|unique:users',//el email debe de ser unico en la tabla usuarios
          'password' => 'required|min:6|confirmed',//la coontrasea debe de ser confirmada con un campo llamado password_confirmation
        ];


        $this->validate($request, $rules);

        //el $request->all(); obtiene todos los datos del formulario con los campos correspondientes, en este caso del usuario
        $campos = $request->all();
        $campos['role'] = 0;
        $campos['password'] = bcrypt($request->password);//encriptamos la contrasea
        $usuario = User::create($campos);

        return $this->showOne($usuario, "Usuario $usuario->name creado exitosamente!", 201);
    }

    public function login(Request $request)
    { 
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
 
       
 
        $user = DB::table('usuario');
        if(Auth::user()->rol_id === 1){
             $user->join('cliente', 'usuario.id', '=', 'cliente.usuario_id')
             ->select('usuario.id', 'email', 'usuario', 'rol_id', 'nombre_cliente', 'paterno', 'materno');
        } else {
             $user->join('usuario_detalle', 'usuario.id', '=', 'usuario_detalle.usuario_id')
             ->select('usuario.id', 'email', 'usuario', 'rol_id', 'nombre', 'paterno', 'materno', 'imagen');      
        }
 
             $user = $user->where('usuario.id', Auth::user()->id)
             ->first();
 
 
        return response()->json([ 'token' => $token, 'data' => $user ], 200);
    }
 
 
    public function logout(Request $request) {
 
         $validate = Validator::make($request->all(), [
            'token' => 'required',
         ]);
 
 
        if ($validate->fails()) {
            return response()->json([
             'error' => 'validate',
             'errors' => $validate->errors(),
             'code' => 422
            ]);
        }
 
 
         try {
             JWTAuth::invalidate($request->input('token'));
             return response()->json([
                            'error' => false,
                            'message' => 'Tu sesión ha sido serrada correctamente.',
                             200
                   ]);
         } catch (JWTException $e) {
             // something went wrong whilst attempting to encode the token
             return response()->json([
                            'error' => true,
                            'message' => 'Error al cerrar la sesión, intente de nuevo.',
                             500
                   ]);
         }
     }

    //recuperar contraseña
    public function recover(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                           'error' => true,
                           'message' => 'Tu dirección de correo electrónico no fue encontrada.',
                            401
                  ]);
        }
        try {
            Password::sendResetLink($request->only('email'), function (Message $message) {
                $message->subject('Su enlace de restablecimiento de contraseña');
            });
        } catch (\Exception $e) {
            //Return with error
            $error_message = $e->getMessage();
            return response()->json([
                           'error' => true,
                           'message' => $error_message,
                            401
                  ]);
        }
        return response()->json([
                           'error' => false,
                           'message' => '¡Se ha enviado un correo electrónico de reinicio! Por favor revise su correo electrónico.',
                            401
                  ]);
    }

}


