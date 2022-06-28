<?php

/*
Mercedes Vera Sotelo
Trabajo Práctico
*/ 

require_once './models/Producto.php';

use \App\Models\Producto as Producto;
use Slim\Http\UploadedFile;

class ManejoArchivos
{
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
          header('Content-Disposition: attachment; filename='.$filename);
          $file = fopen("php://output", "w");

          // $file = fopen($filename, "w");
          if($file){
              foreach($lista as $producto){
                  fwrite($file, Self::toCsv($producto));
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
                    Self::toProducto($stringProducto)->save();
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
  
  public static function guardarImagenClientes($pedido,$archivo){
    Self::crearDirectorioSiNoExiste("./FotosClientes");
    $extension = Self::obtenerExtension($archivo->getClientFilename());
    $name = Self::generarNombreImagenClientes($pedido,$extension);
    try{
      $archivo->moveTo("./FotosClientes".DIRECTORY_SEPARATOR.$name);
      return $name;
    }
    catch(Exception $e){
      return null;
    }
  }

  private static function crearDirectorioSiNoExiste($directorio){
      if (is_string($directorio) && !is_dir($directorio)) {
          mkdir($directorio, 0777, true);
      }
  }

  // Obtiene la extensión del archivo pasado por parámetro 
  private static function obtenerExtension($path){
      $extension = "";
      if($path!=null){
          $explode= explode('.', $path);
          $extension=$explode[count($explode)-1];
      }
      return $extension;
  }

  private static function generarNombreImagenClientes($pedido, $extension){
      $cliente=$pedido->cliente;
      $fecha = date_format($pedido->created_at, "Y-m-d");
      return $cliente. $fecha . '.'.$extension;
  }

}
?>