<?php
class Auto
{ 
    // public $_horaIngreso;
    public $_patente;
    // public $email;
    public $precio;
    public $marca;
    public $modelo;

    public function __construct($marca,$modelo,$patente,$precio = 0)
    {
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->_patente = $patente;
        $this->precio = $precio;
    }

    // public function AgregarImpuestos($_impuesto){
    //     if(is_double($_impuesto))
    //         $this->_precio += $_impuesto;
    // }
    public static function obtenerImporte($auto,$horaRetiro){
        
        $fechaIngreso = explode('-',$auto['_horaIngreso']);
        $horaIngresoAux = $fechaIngreso[1];

        $precioCuatro = 100;
        $precioCuatroYDoce = 60;
        $precioMayorDoce = 30;
        $tiempo = 0;
        $tiempo = $horaRetiro - $horaIngresoAux;
        if ($tiempo > 4) {
            $auto['precio'] = $precioCuatro * $tiempo;
        }
        else if($tiempo > 4 && $tiempo < 12){
            $auto['precio'] = $precioCuatroYDoce * $tiempo;
        }
        else{
            $auto['precio'] = $precioMayorDoce * $tiempo;
        }
        return $auto['precio'];
    
    }

    public static function mostrarAuto($objAuto){
        return "Patente: ". $objAuto->_patente . ' Marca: ' . $objAuto->marca . ' Modelo: ' . $objAuto->modelo . ' Precio: ' . $objAuto->precio;
    }
    
    

    public static function ordenarAutoAscendente($arrAuto){
        
        asort($arrAuto);
        return $arrAuto;
    }
    public static function ordenarAutoDescendente($arrAuto){
        arsort($arrAuto);
        return $arrAuto;
    }
    public function Equals($_auto1, $_auto2){
        if ($_auto1->_marca == $_auto2->_marca) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public static function Add($_auto1,$_auto2){
        if(Auto::Equals($_auto1,$_auto2)){
            if ($_auto1->color == $_auto2->color) {
                return $_auto1->_precio + $_auto2->_precio;
            }
            else {
                return 0;
            }
        }
        else {
            return 0;
        }
    }
    public function __toString(){

        return $this->_patente . '*' . $this->email . '*' . $this->_horaIngreso;

    }
    public static function validarPatenteVehiculo($listaVehiculos,$patente){

        $retorno = false;
        foreach ($listaVehiculos as $vehiculo) {
            $objAux = (object) $vehiculo;
            if ($objAux->_patente == $patente) {
                $retorno = true;
            }
            else{
                $retorno = false;
            }
        }
        return $retorno;
    }

    //no hacemos geters ni setters por que php ya los trae
    
    //todo lo que le llegue como parametro va a ser publico y pasar por aca primero
    //$name es la propiedad a la que estoy accediendo
    public function __get($name){
        return $this->$name; // esto seria similar a echo $this->$_marca;
    }

    //value es el valor que estamos accediendo
    public function __set($name,$value){
        $this->$name = $value;
    }

}

//hacer clase para manejar archivos, index entre metodo y path, ejercicios de la guia de POO




?>