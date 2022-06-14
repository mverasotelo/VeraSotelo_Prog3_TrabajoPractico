<?php
use GuzzleHttp\Psr7\Response;


require_once './middlewares/AutentificadorJWT.php';

class VerificadorPerfiles
{
    public static function VerificarPerfilSocio($request, $handler)
    {
        $method = $request->getMethod();
        $response = new Response();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);
        if($data->perfil == "SOCIO"){
            $response = $handler->handle($request);
        }else{
            $response->getBody()->write(json_encode(["ERROR"=>"Usuario no autorizado"]));
            $response = $response->withStatus(403);
        }
        return $response;
    }

    public static function VerificarPerfilMozo($request, $handler)
    {
        $method = $request->getMethod();
        $response = new Response();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);
        if($data->perfil == "SOCIO" || $data->perfil == "MOZO"){
            $response = $handler->handle($request);
        }else{
            $response->getBody()->write(json_encode(["ERROR"=>"Usuario no autorizado"]));
            $response = $response->withStatus(403);
        }
        return $response;
    }

    public static function VerificarPerfilPendientes($request, $handler)
    {
        $method = $request->getMethod();
        $response = new Response();

        $body = $request->getParsedBody();
        $estado = $body['estado'];

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);
        $perfil = $data->perfil;
        $response->getBody()->write($perfil);

        if($perfil == "MOZO"){
            $request->attributes->set('perfil', 'MOZO');            
            $response = $handler->handle($request);
        }
        return $response;
    }
}

?>