<?php
require_once './models/Pedido.php';

class PedidoController extends Pedido
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];
        $estado = $parametros['estado'];        
        $precioTotal = $parametros['precioTotal'];
        $productos = $parametros['productos'];        
        $mesa = $parametros['mesa'];

        $pedido = new Pedido($codigo,$estado,$precioTotal,$productos,$mesa);
        $pedido->ingresarPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("Lista Pedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(302);
    }
    
}
