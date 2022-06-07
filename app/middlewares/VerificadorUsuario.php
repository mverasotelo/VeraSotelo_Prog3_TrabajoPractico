<?php
use GuzzleHttp\Psr7\Response;

class VerificadorUsuario
{
    public static function verificarUsuario($request, $handler)
    {
        $method = $request->getMethod();
        $response = new Response();

        $data = $request->getParsedBody();
        $usuario = json_decode($data["obj_json"]);
        $correo = $usuario->correo;
        $clave = $usuario->clave;
        
        if(Usuario::ExisteUsuario($correo, $clave))
        {
            $response->getBody()->write(json_encode(["API" => $method]));
        }
        else
        {
            $response->getBody()->write(json_encode(["Mensaje " => "ERROR. Correo o clave incorrectas."]));
            $response = $response->withStatus(403);
        }

        return $response;
    }
}
?>