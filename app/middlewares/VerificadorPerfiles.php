<?php
use GuzzleHttp\Psr7\Response;


require_once './middlewares/AutentificadorJWT.php';

class VerificadorPerfiles
{
    public static function VerificarPerfilSocio($request, $handler)
    {
        $method = $request->getMethod();
        $response = new Response();

        if($method == 'GET'){        
            $response = $handler->handle($request);
            $response->withStatus(302);
        }
        else if($method == 'POST'){
            $header = $request->getHeaderLine('Authorization');
            $token = trim(explode("Bearer", $header)[1]);
            $data = AutentificadorJWT::ObtenerData($token);
            if($data->perfil == "SOCIO"){
                $response = $handler->handle($request);
             }else{
                $response->getBody()->write(json_encode(["ERROR"=>"Usuario no autorizado"]));
                $response = $response->withStatus(403);
            }
        }
        return $response;
    }
    
}

?>