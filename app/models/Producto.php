<?php

/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

class Producto{

    //Atributos
    public $id;
    public $nombre;
    public $tipo;
    public $precio;
    public $stock;  

    public function __construct($id=null,$nombre=null,$tipo=null,$precio=null,$stock=null){
        //Validar tipos de datos
        $this->$id=$id;
        $this->setNombre($nombre);
        $this->setPrecio($precio);
        $this->setTipo($tipo);
        $this->setStock($stock);
    }

     /**
     * Valida y establece el nombre del producto
     *
     * @param string $nombre
     */
    public function setNombre($nombre){
        if (is_string($nombre) && !empty($nombre)) {
            $this->nombre = $nombre;
        }
    }

    /**
     * Valida y establece el tipo del producto
     * @param string $tipo
     */
    public function setTipo($value){
        if ($value!= null && is_string($value)){
            $tipo = strtoupper($value);
            if($tipo=="BEBIDA" || $tipo=="CERVEZA" || $tipo=="COMIDA" || $tipo=="POSTRE"){
                $this->tipo = $tipo;
            }
        }
    }

    /**
     * Valida y establece el precio del producto
     *
     * @param float $precio
     */
    public function setPrecio($value){
        $precio = floatval($value);
        if (is_float($precio) && !empty($precio) && $precio > 0) {
            $this->precio = $precio;
        }
    }

    /**
     * Valida y establece el stock del producto
     *
     * @param int $stock
     */
    public function setStock($stock){
        if (is_int($stock) && !empty($stock) && $stock > 0) {
            $this->stock = $stock;
        }
    }

    /**
     * Compara el objeto actual con un objeto pasado por parámetro según su código
     * @return boolean
     */
    public function __Equals($otroProducto){
        $retorno=false;
        if(is_a($otroProducto,"Producto")){
            if($this->id == $$otroProducto->id){
                $retorno = true;
            }
        }
        return $retorno;
    }

    public function ingresarProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (nombre, tipo, precio, stock) VALUES (:nombre, :tipo, :precio, :stock)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':stock', $this->stock, PDO::PARAM_INT);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, tipo, precio, stock FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerProducto($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, tipo, precio, stock FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function modificarProducto($producto)
    {
        if($usuario!=null && is_a($producto, "Producto")){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET nombre = :nombre, tipo = :tipo, precio = :precio, stock = :stock WHERE id = :id");
            $consulta->bindValue(':id', $producto->id, PDO::PARAM_INT);
            $consulta->bindValue(':nombre', $producto->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $producto->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':stock', $producto->stock, PDO::PARAM_INT);
            $consulta->bindValue(':precio', $producto->precio, PDO::PARAM_FLOAT);
            $consulta->execute();
            return true;
        }
        return false;
    }

    // public static function borrarUsuario($usuario)
    // {
    //     $objAccesoDato = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
    //     $fecha = new DateTime(date("d-m-Y"));
    //     $consulta->bindValue(':id', $usuario->id, PDO::PARAM_INT);
    //     $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
    //     $consulta->execute();
    // }

}
 
?>