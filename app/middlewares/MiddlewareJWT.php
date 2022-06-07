<?php
use GuzzleHttp\Psr7\Response;

class MiddlewareJWT
{
    
    public static function verificarToken($request, $handler)
    {
        $response = new Response();

        $header = $request->getHeaderLine('Authorization');

        $token = trim(explode("Bearer", $header)[1]);

        AutentificadorJWT::verificarToken($token);
        $response = $handler->handle($request);

        return $response ->withHeader('Content-Type', 'application/json');
    }
}

?>