<?php


include_once './usuario.php';
include_once './auto.php';


session_start();

$request_method = $_SERVER['REQUEST_METHOD'];
$path_info = $_SERVER['PATH_INFO'];

$listaDeMaterias = array();
$listaDeMateriasProfe = array();

$header = getallheaders();
$listaProfes = [];


$pathAux = explode('/', getenv('REQUEST_URI'));

// var_dump($_SERVER);
// echo "<br>";
// var_dump($pathAux);
// echo "<br>";


switch($request_method)
{
    case 'POST':
        switch ($pathAux[3]) 
        {
            
            case 'registro'://PUNTO 1
                // if (isset($pathAux[3])) {
                //     // echo $pathAux[4];
                //     $foto = $_POST['foto'] ?? "";

                //     if(Usuario::asignarFotoNueva($pathAux[4],$foto)){
                        
                //         $datos = "Se asigno la foto al usuario correctamente";
                //     }
                //     else{
                //         $datos = "Error al asignar la foto";
                //     }

                // }
                
                $email = $_POST['email'] ?? "";
                $password = $_POST['password'] ?? 0;
                $tipo = $_POST['tipo'] ?? "";
                
                // $foto = $_POST['foto'] ?? "";
                $claveEncriptada = Usuario::encriptarContraseña($password);
                if(Usuario::CrearUsuario($email,$claveEncriptada,$tipo))
                {
                    $datos = 'Se creo el usuario correctamente!';
                }
                else
                {
                    $datos = 'Error al crear usuario. Email no valido o existente';
                }

                
                break;
            case 'login'://PUNTO 2
                $email = $_POST['email'] ?? "";
                $password = $_POST['password'] ?? "";
                $token = Usuario::Login($email, $password);
                if(Usuario::Login($email, $password))
                {
                    $datos = 'Login Exitoso. TOKEN: ' . $token;
                }
                else
                {
                    $datos = 'Nombre o Clave Incorrectas';                       
                }
                break;
            case 'ingreso'://PUNTO 3
                $header = getallheaders();
                $token = $header['token'];
                $patente = $_POST['patente'] ?? "";
                
                $usuarioLogueado = Token::VerificarToken($token);
                
                $fecha = date('d-h');
                
                $usuarioLogueadoArray = (array) $usuarioLogueado;
                
                
                if (!$usuarioLogueado) {
                    $datos = "Usuario no logueado, token incorrecto!";
                }
                else{
                    
                    $auto = new Auto($patente,$fecha,$usuarioLogueadoArray['email']);
                    
                    if(Archivos::guardarJson($auto,'autos.json')){
                        $datos = "Auto guardado correctamente";
                    }
                    else{
                        $datos = "Ocurrio un error al guardar el auto";
                    }
                }

                break;
            case 'users':
                $foto = $_POST['foto'] ?? "";
                $token = $header['token'];
                $usuarioLogueado = Token::VerificarToken($token);
                
                $usuarioLogueadoArray = (array) $usuarioLogueado;

                if (!$usuarioLogueado) {
                    $datos = "Usuario no logueado, token incorrecto!";
                }
                else{
                    if(Usuario::asignarFotoNueva($usuarioLogueadoArray['email'],$foto)){
                        $datos = "Imagen modificada correctamente";
                    }
                    else{
                        $datos = "Ocurrio un error al guardar la imagen";
                    }
                    
                }
            break;
            
            // case 'profesor'://PUNTO 4
            //     $header = getallheaders();
            //     $token = $header['token'];
            //     $nombre = $_POST['nombre'] ?? "";
            //     $legajo = $_POST['legajo'] ?? 0;

            //     $usuarioLogueado = Token::VerificarToken($token);

            //     $profesor = new Profesor($nombre,$legajo);

            //     if(Archivos::guardarJson($profesor,'profesores.json')){
            //         $datos = "Profesor dado de alta";
            //     }
            //     else{
            //         $datos = "Profesor no dado de alta";
            //     }

            //     break;
            default:
                $datos = 'faltan datos';
                break;

            
        }
    break;
    
    case 'GET':
        //$datos = Usuario::Mostrar($token);
        $token = $header['token'];
        $usuarioLogueado = Token::VerificarToken($token);
        $patenteGet =$_GET['patente'] ?? "";
        $path_info = explode("/",$path_info);
        
        if (!$usuarioLogueado) {
            $datos = "token incorrecto";
        }
        else{
            switch ($path_info[1]){
                
            case 'retiro': //punto 4
                $fechaEgreso = date('h');
                
                $listaAutos = Archivos::leerJson('./autos.json',$listaAutos);
                $patente = $pathAux[4];
                
                foreach ($listaAutos as $auto) {
                    if ($auto['_patente'] == $patente) {
                        //var_dump($auto);
                        $importe = Auto::obtenerImporte($auto,$fechaEgreso);
                        
                        $datos = "importe: {$importe} , patente: {$patente} , ingreso: {$auto['_horaIngreso']} , egreso: {$fechaEgreso} hs";
                    break;
                    }
                    else{
                        $datos = "patente no encontrada";
                    }
                }


                
                //$datos = Materias::mostrarMaterias();
                if ($datos === "") {
                    $datos = 'Faltan datos';
                }
                
                break;
            case 'ingreso'://PUNTO 5
                $datos = " ";
                if ($patenteGet == null) {
                    $listaAutos = Archivos::leerJson('./autos.json',$listaAutos);
                    // $listaAutosAux = (object) $listaAutos;
                    $listaAutosOrdenados = Auto::ordenarAutoAscendente($listaAutos);
                    
                    foreach ($listaAutosOrdenados as $auto) {
                        $autoAux = (object) $auto;
                        $mostrarAux = Auto::mostrarAuto($autoAux);
                        
                        $datos .= "<br>". $mostrarAux;
                    }
                }
                else{
                    $listaAutos = Archivos::leerJson('./autos.json',$listaAutos);
                    foreach ($listaAutos as $auto) {
                        if ($patenteGet == $auto['_patente']) {
                            $datos = " ". $patenteGet . " fecha ingreso: " . $auto['_horaIngreso'] . " email: " . $auto['email'] . " importe: " . $auto['importe'];
                        }
                        
                    }
                }
                
                
                if($datos === "")
                {
                    $datos = 'Faltan datos';
                }
                //echo json_encode($respuesta);
                break;
            default:
            $datos = 'faltan datos';
            break;
            }
        }
        
        
    break;  
    default:
    break;
}


$respuesta = new stdClass;
$respuesta->success = true;
$respuesta->data = $datos; 

echo json_encode($respuesta);
?>