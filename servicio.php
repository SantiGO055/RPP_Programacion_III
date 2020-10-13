<?php
class Servicio
{ 
    public $id;
    public $tipo;
    public $precio;
    public $demora;

    public function __construct($id,$tipo,$precio,$demora)
    {
        $this->id = $id;
        $this->tipo = $tipo;
        $this->precio = $precio;
        $this->demora = $demora;
    }
    function validarTipo($tipoAux){
        

    }

    public static function validarServicioId($listaServicios,$id){

        $retorno = false;
        foreach ($listaServicios as $servicio) {
            $servicioA = (object) $servicio;
            if ($servicioA->id == $id) {
                $retorno = true;
            }
            else{
                $retorno = false;
            }
        }
        return $retorno;
    }
    
}
?>