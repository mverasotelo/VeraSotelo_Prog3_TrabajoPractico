<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

class Usuario
{
    public $id;
    public $nombre;
    public $clave;
    public $perfil;
    public $fechaBaja;


    public function __construct($id=null,$nombre=null,$clave=null,$perfil=null,$fechaBaja=null){
        //Validar tipos de datos
        $this->$id = $id;
        $this->$nombre = $nombre;
        $this->$clave = $clave;
        $this->setPerfil($perfil);
        $this->fechaBaja=$fechaBaja;
    }

    /**
     * Valida y establece el perfil del usuario
     *
     * @param string $perfil
     */
    public function setPerfil($value){
        if ($value!= null && is_string($value)){
            $perfil = strtoupper($value);
            if($perfil=="BARTENDER" || $perfil=="CERVECERO" || $perfil=="COCINERO" || $perfil=="MOZO" || $perfil=="SOCIO"){
                $this->perfil = $perfil;
            }
        }
    }

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (nombre, clave, perfil) VALUES (:nombre, :clave, :perfil)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':perfil',$this->perfil, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, perfil, clave FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, clave ,perfil FROM usuarios WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function modificarUsuario($usuario)
    {
        if($usuario!=null && is_a($usuario, "Usuario")){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET nombre = :nombre, clave = :clave, perfil = :perfil WHERE id = :id");
            $consulta->bindValue(':nombre', $usuario->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $usuario->clave, PDO::PARAM_STR);
            $consulta->bindValue(':id', $usuario->id, PDO::PARAM_INT);
            $consulta->bindValue(':perfil',$usuario->perfil, PDO::PARAM_STR);
            $consulta->execute();
            return true;
        }
        return false;
    }

    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario->id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }

    
    public static function existeUsuario($usuario, $clave)
    {
        $usuarios = self::obtenerTodos();

        foreach($usuarios as $u)
        {
            if($u->nombre == $usuario && password_verify($clave,$u->clave))
            {
                return true;
            }
        }
        return false;
    }
}

?>