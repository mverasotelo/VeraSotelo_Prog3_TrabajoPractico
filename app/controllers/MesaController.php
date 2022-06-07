<?php
require_once './models/Mesa.php';

class MesaController extends Mesa
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];
        $estado = $parametros['estado'];        
        $cliente = $parametros['cliente'];

        $mesa = new Mesa($codigo,$estado,$cliente);
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("Lista Mesa" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(302);
    }
    
}
