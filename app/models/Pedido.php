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

    public function __construct($id=null,$estado=null,$precioTotal=null,$productos=null,$mesa=null){
        //Validar tipos de datos
        $this->setId=$id;
        $this->setEstado($estado);
        $this->setPrecioTotal($precioTotal);
        $this->setProductos($productos);
        $this->setMesa($mesa);
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
    public function setPrecioTotal($precio){
        if (is_float($precio) && !empty($precio) && $precio > 0) {
            $this->precio = $precio;
        }
    }

    /**
     * Valida y establece los productos solicitados por la mesa
     *
     * @param int $productos
     */
    public function setProductos($productos){
        if (is_array($productos) && !empty($cantidad)) {
            $this->productos = $productos;
        }
    }

    /**
     * Valida y establece la mesa de la que proviene el pedido
     *
     * @param int $mesa
     */
    public function setMesa($mesa){
        if (is_int($mesa) && !empty($mesa)) {
            $this->mesa = $mesa;
        }
    }

    /**
     * Compara el objeto actual con un objeto pasado por parámetro según su codigo
     * @return boolean
     */
    public function __Equals($otroPedido){
        $retorno=false;
        if(is_a($otroPedido,"Pedido")){
            if(strcasecmp($this->codigo, $otroPedido->codigo)==0){
                $retorno = true;
            }
        }
        return $retorno;
    }

    // /**
    //  * Actualiza los productos del pedido
    //  * @return boolean true si se actualizo el pedido, false sino habia suficiente stock o hubo algun error.
    //  */
    // public static function actualizarPedido($hamburguesa, $cantidad){
    //     $retorno = false;
    //     if(is_int($cantidad)){
    //         $hamburguesas = Hamburguesa::leerArchivoJson("Hamburguesas.json");
    //         var_dump($hamburguesas);
    //         for($i = 0; $i < count($hamburguesas);$i++){
    //             if($hamburguesa->__Equals($hamburguesas[$i])){
    //                 var_dump($hamburguesas[$i]);
    //                 $nuevoStock =  $this->getCantidad()-$cantidad;
    //                 $hamburguesas[$i]->setCantidad($nuevoStock);  
    //                 var_dump($hamburguesas[$i]);
    //             }
    //         }
    //         if(Hamburguesa::guardarJson("Hamburguesas.json", $hamburguesas)){
    //             $retorno=true;
    //         }   
    //     }
    //     return $retorno;
    // }

}
?>