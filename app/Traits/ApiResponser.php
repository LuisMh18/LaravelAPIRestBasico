<?php
namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser{
  //metodo para los mensajes success
  public function successResponse($data, $code){
      return response()->json(['data' => $data], $code);
  }


  protected function showAll($data, $code = 200)
  {

    $data = $this->paginate($data);//paginación

    return $this->successResponse($data, $code);

  }

  //metodo que mostrara una instancia especifica, por ejemplo cuando tenemos una instancia de un usuario existente
  public function showOne(Model $instance, $message = '', $code = 200){
    return $this->successResponse(
                    [
                      'data' => $instance,
                      'message' => $message
                    ],
                     $code
                   );
  }


  //metodo para la paginación 
  protected function paginate($data)
  {

    /*Reglas para permitirle al usuario definir el numero de la paginación, como minimo seran 2 y como maximo 50 */
    $rules = [
      'per_page' => 'integer|min:2|max:50'
    ];

    Validator::validate(request()->all(), $rules);//validamos

    $page = LengthAwarePaginator::resolveCurrentPage();

    $perPage = 10; //valor predifinido a la cantidad de elementos por pagina
    //si rcibimos el parametro per_page sustituimos el valor de la cantidad de elementos de pagina por defecto por el recibido
    if (request()->has('per_page')) {
      $perPage = (int) request()->per_page;
    }

    $results = $data->slice(($page - 1) * $perPage, $perPage)->values();

    $paginated = new LengthAwarePaginator($results, $data->count(), $perPage, $page, [
      'path' => LengthAwarePaginator::resolveCurrentPath(),
    ]);

    $paginated->appends(request()->all());

    return $paginated;
  }


}


 ?>
