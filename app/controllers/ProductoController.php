<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $tipo = $parametros['tipo'];        
        $precio = $parametros['precio'];
        $stock = intval($parametros['stock']);

        // Creamos el usuario
        $producto = new Producto(null,$nombre,$tipo,$precio,$stock);
        $producto->ingresarProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ChequearUno($request, $response, $args)
    {
      $payload = json_encode(array("mensaje" => "Paso por el controlador con exito"));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
}

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $productoId = $args['producto'];
        $producto = Producto::obtenerProducto($productoId);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProductos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(302);
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $id = $parametros['id'];
        $nombre = $parametros['nombre'];
        $tipo = $parametros['tipo'];        
        $precio = $parametros['precio'];
        $stock = $parametros['stock'];

        $producto = new Usuario($id,$nombre,$tipo,$precio,$stock);
        Producto::modificarProducto($producto);

        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        // Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
