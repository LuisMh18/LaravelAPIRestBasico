<?php
namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait ApiResponser{
  //metodo para los mensajes success
  public function successResponse($data, $code){
      return response()->json($data, $code);
  }

  //metodo para los mensajes de error
  public function errorResponse($message, $code){
      return response()->json(['error' => $message, 'code' => $code], $code);
  }

  //metodo para mostrar una respuesta de elementos
  protected function showAll(Collection $collection, $code = 200)
	{

		return $this->successResponse(['data' => $collection], $code);

	}

  //metodo que mostrara una instancia especifica, por ejemplo cuando tenemos una instancia de un usuario existente
  public function showOne(Model $instance, $code = 200){
    return $this->successResponse(['data' => $instance], $code);
  }

  
  public function showMessage($message, $code = 200){
    return $this->successResponse(['data' => $message], $code);
  }


}


 ?>
