<?php

require_once './services/fpdf.php';
require_once './models/Usuario.php';

use \App\Models\Usuario as Usuario;

class ListadoEmpleados extends FPDF{

    function Header(){
        $this->SetFont("Helvetica", 'B', 24);
        $this->Image("./static/logo.png", 1, 1,6,5);
        $this->Cell(9);
        $this->Cell(10, 2, "Listado de empleados", 0, 0, 'C');
        $this->Ln();
    }

    function Body(){
        $lista = Usuario::where('perfil','!=','SOCIO')->orderBy('updated_at', 'DESC')->get();

        if(count($lista)>0){
            $this->Ln();
            $this->Ln();
            $this->SetFont("Arial", 'B', 10);
            $this->SetTextColor(62, 72, 204);
            
            //Titulos
            $this->Cell(4,1,"Empleado", 1, 0, 'C');   
            $this->Cell(3,1,"Sector", 1, 0, 'C');
            $this->Cell(3,1,"Fecha Alta", 1, 0, 'C');
            $this->Cell(3,1,"Operaciones", 1, 0, 'C');
            $this->Cell(4,1,"Fecha suspension", 1, 0, 'C');
            $this->Cell(3,1,"Fecha borrado", 1, 1, 'C');

            $this->SetFont("Arial", '', 10);
            $this->SetTextColor(0, 0, 0);
            
            foreach($lista as $empleado){
                $this->Cell(4,1,$empleado->nombre, 1, 0, 'C');   
                $this->Cell(3,1,$empleado->perfil, 1, 0, 'C');
                $this->Cell(3,1, date_format($empleado->created_at, "d-m-Y"), 1, 0, 'C');
                $this->Cell(3,1,count($empleado->registros), 1, 0, 'C');
                if($empleado->suspended_at){
                    $this->Cell(4,1,date_format($empleado->suspended_at, "d-m-Y"), 1, 0, 'C');
                }else{
                    $this->Cell(4,1,"----", 1, 0, 'C');
                }
                if($empleado->deleted_at){
                    $this->Cell(3,1,date_format($empleado->deleted_at, "d-m-Y"), 1, 1, 'C');
                }else{
                    $this->Cell(3,1,"----", 1, 1, 'C');
                }
            }
        }else{
            $this->Cell(10, 3, "No hay registros que mostrar", 1, 1, 'C');
        }     
    }

    function Footer(){
        $this->SetY(-2);
        $this->SetFont("Arial", 'I', 10);
        $this->Cell(0, 1, "Lista de empleados al ".date("d-m-Y"), 0, 0, 'C');
    }
}