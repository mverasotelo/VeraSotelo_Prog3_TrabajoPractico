<?php

/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

class Pedido{

    //Atributos
    public $codigo;
    public $estado;
    public $precioTotal;
    public $productos;
    public $mesa;  

    public function __construct($codigo=null,$estado=null,$precioTotal=null,$productos=null,$mesa=null){
        //Validar tipos de datos
        $this->setCodigo($codigo);
        $this->setEstado($estado);
        $this->setPrecioTotal($precioTotal);
        $this->productos=$productos;
        $this->setMesa($mesa);
    }

    /**
     * Valida y establece el codigo del pedido
     *
     * @param string $codigo
     */
    public function setCodigo($codigo){
        if ($codigo!= null && is_string($codigo) && strlen($codigo)==6){
            $this->codigo = $codigo;
        }   
    }

    /**
     * Valida y establece el estado del pedido
     *
     * @param string $estado
     */
    public function setEstado($value){
        if ($value!= null && is_string($value)){
            $estado = strtoupper($value);
            if($estado=="PENDIENTE" || $estado=="EN PREPARACION" || $estado=="LISTO PARA SERVIR"){
                $this->estado = $estado;
            }
        }
    }

    /**
     * Valida y establece el precio total del pedido
     *
     * @param float $precio
     */
    public function setPrecioTotal($value){
        $precio = floatval($value);
        if (is_float($precio) && !empty($precio) && $precio > 0) {
            $this->precioTotal = $precio;
        }
    }

    /**
     * Valida y establece la mesa de la que proviene el pedido
     *
     * @param int $mesa
     */
    public function setMesa($mesa){
        if (is_string($mesa) && !empty($mesa)) {
            $this->mesa = $mesa;
        }
    }

    public function ingresarPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo, estado, precioTotal, productos, mesa) VALUES (:codigo, :estado, :precioTotal, :productos, :mesa)");
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':precioTotal', $this->precioTotal, PDO::PARAM_STR);
        $consulta->bindValue(':productos', $this->productos, PDO::PARAM_STR);
        $consulta->bindValue(':mesa', $this->mesa, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

}
?>