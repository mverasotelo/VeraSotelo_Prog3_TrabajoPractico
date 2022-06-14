<?php

require_once './models/Usuario.php';

use GuzzleHttp\Psr7\Response;
use \App\Models\Usuario as Usuario;

class LoginController
{
    public function LoguearUsuario($request, $response, $args)
    {
        $response = new Response();
        $parametros = $request->getParsedBody();

        $nombre = $parametros['usuario'];
        $clave = $parametros['clave'];  

        try{
            $usuario = Usuario::where('nombre', $nombre)->first();

            if($usuario!= null && password_verify($clave, $usuario->clave)){
                $datos = array('usuario' => $nombre, 'clave' => $clave, 'perfil'=> $usuario->perfil);
                $token = AutentificadorJWT::CrearToken($datos);
                $payload = json_encode(array('jwt' => $token));
                $response->getBody()->write($payload);
             }else{
                $response->getBody()->write("Usuario o clave incorrectos".password_hash($clave, PASSWORD_DEFAULT));
                $response = $response->withStatus(401);
            }
        }catch(Exception $e){
            $response->getBody()->write(json_encode(["ERROR"=>  $e->getMessage()]));
            $response = $response->withStatus(401);
        }

        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
