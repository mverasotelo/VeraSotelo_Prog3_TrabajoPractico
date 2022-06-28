<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

require_once './interfaces/IApiUsable.php';
require_once './models/Usuario.php';
require_once './models/ListadoEmpleados.php';

use \App\Models\Usuario as Usuario;

class UsuarioController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre = $parametros['nombre'];
    $clave = $parametros['clave'];
    $perfil = $parametros['perfil'];

    if(self::ValidarPerfil($perfil)){
      $usr = new Usuario();
      $usr->nombre = $nombre;
      $usr->perfil = $perfil;
      $usr->clave = password_hash($clave, PASSWORD_DEFAULT);
      $usr->save();
  
      $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
    }
    else{
      $payload = json_encode(array("ERROR" => "Perfil invalido"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $nombre = $args['usuario'];

    $usuario = Usuario::where('nombre', $nombre)->first();

    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::all();
    foreach($lista as $u){
      $u->registros;
    }
    $payload = json_encode(array("listaUsuarios" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombreModificado = $parametros['nombre'];
    $perfilModificado = $parametros['perfil'];
    $claveModificada = $parametros['clave'];

    $usuarioId = $args['id'];

    $usr = Usuario::where('id', '=', $usuarioId)->first();
    
    if ($usr !== null) {
      if(ValidarPerfil($perfilModificado)){
        $usr = new Usuario();
        $usr->nombre = $nombreModificado;
        $usr->perfil = $perfilModificado;
        $usr->clave = password_hash($claveModificada, PASSWORD_DEFAULT);
        $usr->save();
    
        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
      }
      else{
        $payload = json_encode(array("ERROR" => "Perfil invalido"));
      }    
    }else{
      $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $usuarioId = $args['id'];
    $usuario = Usuario::find($usuarioId);
    $usuario->delete();

    $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function DescargarListaEmpleados($request, $response, $args)
  {
    ob_start();
    
    $pdf = new ListadoEmpleados('P', 'cm','letter');
    $pdf->SetAuthor("Mercedes Vera Sotelo", true);
    $pdf->AddPage();
    $pdf->Body();
    $titulo = "Listado de empleados.pdf";
    $pdf->Output($titulo, 'D', true);

    ob_end_flush(); 

    $response->getBody()->write($payload);
    return $response
    ->withHeader('Content-Type', 'application/pdf');
  }

  private static function ValidarEstado($value){
    $estado = strtoupper($value);
    if($estado=="PENDIENTE" || $estado=="EN PREPARACION" || $estado=="LISTO PARA SERVIR"){
      return true;
    }
    return false;
  }

  private static function ValidarPerfil($value){
    $perfil = strtoupper($value);
    if($perfil=="SOCIO" || $perfil=="MOZO" || $perfil=="COCINERO" || $perfil=="BARTENDER" || $perfil=="CERVECERO"){
        return true;
    }
    return false;
  }

}