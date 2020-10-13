<?php
class Turno
{ 
    public $fecha;
    public $patente;
    public $marca;
    public $modelo;
    public $precio;
    public $tipoServicio;

    /**recibo objeto vehiculo y objeto servicio */

    public function __construct($fecha,$patente,$marca,$modelo,$precio,$tipoServicio)
    {
        $this->fecha = $fecha;
        $this->patente = $patente;
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->precio = $precio;
        $this->tipoServicio = $tipoServicio;
    }
    
    public static function validarDisponibilidad($listaTurnos,$fechaAux){
        $retorno = false;
        echo $fechaAux;
        foreach ($listaTurnos as $turno) {
            $turnoAux = (object) $turno;
            if($turnoAux->fecha == $fechaAux){
                $retorno = false;
            }
            else{
                $retorno = true;
            }
        }
        return $retorno;
    }
}
?>