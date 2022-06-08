<?php
use GuzzleHttp\Psr7\Response;

class MiddlewareJWT
{
    
    public static function verificarToken($request, $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $response = new Response();

        if($header!=null){
            $token = trim(explode("Bearer", $header)[1]);
            
            try{
                AutentificadorJWT::verificarToken($token);
                $response = $handler->handle($request);
            }catch(Exception $e){
                $response->getBody()->write(json_encode(["ERROR"=>"Usuario no autorizado"]));
                $response = $response->withStatus(401);
            }
        }else{
            $response->getBody()->write(json_encode(["ERROR"=>"Usuario no logueado"]));
            $response = $response->withStatus(400);
        }
        return $response ->withHeader('Content-Type', 'application/json');
    }
}

?>