<?php
use GuzzleHttp\Psr7\Response;


require_once './middlewares/AutentificadorJWT.php';

class VerificadorPerfiles
{
    public static function VerificarPerfilSocio($request, $handler)
    {
        $method = $request->getMethod();
        $response = new Response();
        $perfil = self::obtenerPerfil($request);

        if($perfil){
            if($perfil == "SOCIO"){
                $response = $handler->handle($request);
            }else{
                $response->getBody()->write(json_encode(["ERROR"=>"Usuario no autorizado"]));
                $response = $response->withStatus(401);
            }        
        }else{
            $response->getBody()->write(json_encode(["ERROR"=>"Usuario no logueado"]));
            $response = $response->withStatus(401);
        }

        return $response;
    }

    public static function VerificarPerfilMozo($request, $handler)
    {
        $method = $request->getMethod();
        $response = new Response();
        $perfil = self::obtenerPerfil($request);

        if($perfil){
            if($perfil == "SOCIO" || $perfil == "MOZO"){
                $response = $handler->handle($request);
            }else{
                $response->getBody()->write(json_encode(["ERROR"=>"Usuario no autorizado"]));
                $response = $response->withStatus(401);
            }        
        }else{
            $response->getBody()->write(json_encode(["ERROR"=>"Usuario no logueado"]));
            $response = $response->withStatus(401);
        }
        return $response;
    }

    public static function VerificarPerfilCocinero($request, $handler)
    {
        $method = $request->getMethod();
        $response = new Response();

        $body = $request->getParsedBody();
        $perfil = self::obtenerPerfil($request);

        if($perfil){
            if($perfil == "SOCIO" || $perfil == "COCINERO" || $perfil == "MOZO"){
                $response = $handler->handle($request);
            }else{
                $response->getBody()->write(json_encode(["ERROR"=>"Usuario no autorizado"]));
                $response = $response->withStatus(401);
            }        
        }else{
            $response->getBody()->write(json_encode(["ERROR"=>"Usuario no logueado"]));
            $response = $response->withStatus(401);
        }
        return $response;
    }

    public static function VerificarPerfilCervecero($request, $handler)
    {
        $method = $request->getMethod();
        $response = new Response();
        $perfil = self::obtenerPerfil($request);

        if($perfil){
            if($perfil == "SOCIO" || $perfil == "CERVECERO" || $perfil == "MOZO"){
                $response = $handler->handle($request);
            }else{
                $response->getBody()->write(json_encode(["ERROR"=>"Usuario no autorizado"]));
                $response = $response->withStatus(401);
            }        
        }else{
            $response->getBody()->write(json_encode(["ERROR"=>"Usuario no logueado"]));
            $response = $response->withStatus(401);
        }
        return $response;
    }

    public static function VerificarPerfilBartender($request, $handler)
    {
        $method = $request->getMethod();
        $response = new Response();
        $perfil = self::obtenerPerfil($request);

        if($perfil){
            if($perfil == "SOCIO" || $perfil == "BARTENDER" || $perfil == "MOZO"){
                $response = $handler->handle($request);
            }else{
                $response->getBody()->write(json_encode(["ERROR"=>"Usuario no autorizado"]));
                $response = $response->withStatus(401);
            }        
        }else{
            $response->getBody()->write(json_encode(["ERROR"=>"Usuario no logueado"]));
            $response = $response->withStatus(401);
        }
        return $response;
    }

    private static function obtenerPerfil($request){
        $perfil=null;
        $header = $request->getHeaderLine('Authorization');
        if($header){
            $token = trim(explode("Bearer", $header)[1]);
            $data = AutentificadorJWT::ObtenerData($token);
            $perfil = $data->perfil;    
        }
        return $perfil;
    }
}

?>