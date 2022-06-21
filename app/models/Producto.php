<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $fillable = ['nombre','tipo','precio','stock'];
    public $incrementing = true;
    public $timestamps = false;

    const DELETED_AT = 'fechaBaja';
 


    /**
     * Recibe un objeto de tipo Producto y crea un string en formato csv con sus datos
     * @return string con los datos si el parámetro es un objeto de tipo Producto y string vacio si el parámetro no es un Producto
     */
    public static function toCsv($producto){
        $productoCsv = "";
            $productoCsv = 
            $producto->id
            .","
            .$producto->nombre
            .","
            .$producto->tipo
            .","
            .$producto->precio
            .","
            .$producto->stock
            .PHP_EOL;
        return $productoCsv;
    }

    /**
     * Recibe un string en formato csv y crea un objeto de tipo Producto a partir de los datos obtenidos de el
     * @return Producto creado a partir de los datos del string
     */
    public static function toProducto($stringProducto){
        $arrayProducto = explode(",",$stringProducto);
        $producto = new Producto();
        $producto->nombre = $arrayProducto[1];
        $producto->tipo = $arrayProducto[2];
        $producto->precio = $arrayProducto[3];
        $producto->stock = $arrayProducto[4];
        return $producto;
    }

    /**
     * Guarda los datos de la tabla Productos en un archivo csv
     */
    public static function descargarCsv(){
        $retorno = false;
        $filename = './archivos/productos.csv'; 
        try{

            $lista = Producto::all();
            if(!file_exists($filename)){
                mkdir(dirname($filename, 1), 0777, true);
            }

            // header('Content-Type: text/csv');
            // header('Content-Disposition: attachment; filename='.$filename);
            // $file = fopen("php://output", "w");

            $file = fopen($filename, "w");
            if($file){
                foreach($lista as $producto){
                    fwrite($file, Producto::toCsv($producto));
                }
                $retorno = true;
            }
        }catch(\Throwable $th){
            echo "Error al guardar la lista";
        }finally{
            fclose($file);
            return $retorno;
        }
    }

    /**
     * Lee el archivo productos.csv y guarda los productos en la base de datos.
     */
    public static function leerCsv(){
        $retorno = false;
        $filename = './archivos/productos.csv';
        try{
            $file = fopen($filename, "r");
            if($file!=false){
                while(!feof($file)){
                    $stringProducto = fgets($file);
                    $stringProducto = substr($stringProducto, 0, -1); 
                    if(!empty($stringProducto)){                           
                        Producto::toProducto($stringProducto)->save();
                    }
                }
                $retorno = true;
            }
        }catch(Exception $e){
            echo "Error: ".$e->getMessage();
        }finally{
            fclose($file);
            return $retorno;
        }
    }

}
?>