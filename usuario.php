<?php

include_once './clasesParaParcial/token.php';
include_once './clasesParaParcial/archivos.php';


class Usuario{
    public $email;
    public $clave;
    public $imagenNombre;
    public $tipo;

    public function __construct($email, $clave, $imagenNombre,$tipo)
    {
        $this->setEmail($email);
        $this->clave = $clave;
        $this->imagenNombre = $imagenNombre;
        $this->tipo = $tipo;
        
    }
    
    public function setEmail($email){
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->email = "emailNoValido";
        }
        else{
            $this->email = $email;
        }
    }

    public static function Login($email,$clave){
        $retorno = false;
        $lista = Archivos::leerJson('users.json',$listaUsuarios);

        
        if(isset($lista)){
            foreach ($lista as $usuario) {
                //$usuario['clave'] es la clave encriptada del json que levanto
                if ($usuario['email'] == $email && Usuario::verificarContraseña($clave,$usuario['clave'])) {
                    
                    //Usuario::verificarContraseña($clave,$usuario['clave']);

                    $token = Token::crearToken($usuario);
                    
                    return $token;
                break;
                }
            }
        }
    }

    public static function encriptarContraseña($clave){
        return password_hash($clave, PASSWORD_DEFAULT);
    }

    public static function verificarContraseña($clave,$hash){
        return password_verify($clave, $hash);
    }

    

    public static function CrearUsuario($email,$claveEncriptada,$tipo){
        $retorno = false;
        $usuarioExistente = Usuario::buscarUsuario($email);
        if(!$usuarioExistente){
            

            $imagenNombre = Archivos::guardarImagen($_FILES,9999999999,'./imagenes/',true);
            
            
            $usuario = new Usuario($email,$claveEncriptada,$imagenNombre,$tipo);
            
            if ($usuario->email != "emailNoValido") {
                if(Archivos::guardarJson($usuario,'users.json')){
                    $retorno = true;
                }
                if (isset($listaUsuarios)) {
                    
                    array_push($listaUsuarios);
                }
                else{
                    
                    $listaUsuarios = $usuario;
                }
            }
            else{
                $retorno = false;
            }

            
        }
        else
        {
            $retorno = false;
        }
        return $retorno;
    }


    public static function buscarUsuario($email)
    {   
        $retorno = false;
        Archivos::leerJson('./users.json',$listaUsuarios);

        foreach ($listaUsuarios as $usuario) {
            if ($usuario['email'] === $email) {
                $retorno = true;
            }
            else{
                $retorno = false;
            }
        }
        return $retorno;
        
    }
    public static function asignarFotoNueva($email,$foto){
        $listaUsuarios = Archivos::leerJson('./users.json',$listaUsuarios);
        
        // $nombreFoto = $_FILES["imagen"]["name"];
        for ($i=0; $i < count($listaUsuarios); $i++) { 
            if ($listaUsuarios[$i]['email'] === $email) {
                
                //$pathMover = "./imagenes/" . $listaUsuarios[$i]['imagenNombre'];
                $root = __DIR__.DIRECTORY_SEPARATOR."clasesParaParcial".DIRECTORY_SEPARATOR;
                $rutaImagen = $root."imagenes" . DIRECTORY_SEPARATOR;
                $rutaBackup = $root."backup" . DIRECTORY_SEPARATOR;
                $origen =  $rutaImagen . $listaUsuarios[$i]['imagenNombre'];
                $destino = $rutaBackup .$listaUsuarios[$i]['imagenNombre'];
                
                rename($origen,$destino);
                $nombreFoto = Archivos::guardarImagen($_FILES,9999999999,'./imagenes/',true);
                
                Archivos::modificarJson("./users.json",$i,"imagenNombre",$nombreFoto);
                $retorno = true;
                return $retorno;
            }
            else{
                $retorno = false;
            }
        }
        // foreach ($listaUsuarios as $usuario) {
            
        // }

        
    }



}


?>