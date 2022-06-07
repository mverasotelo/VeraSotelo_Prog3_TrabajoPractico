<?php
require_once './models/Usuario.php';
use GuzzleHttp\Psr7\Response;


class LoginController
{
    public function LoguearUsuario($request, $response, $args)
    {
        $response = new Response();
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];  

        if(Usuario::existeUsuario($usuario,$clave)){
            $usuarioBD = Usuario::obtenerUsuario($usuario);
            $perfil = $usuarioBD->perfil;
            $datos = array('usuario' => $usuario, 'perfil' => $perfil);
            $token = AutentificadorJWT::CrearToken($datos);
            $payload = json_encode(array('jwt' => $token));
            $response->getBody()->write($payload);
        }else{
            $response->getBody()->write("Usuario o clave incorrectos");
        }

        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
