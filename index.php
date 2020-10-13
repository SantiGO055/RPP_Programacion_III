<?php


include_once './usuario.php';
include_once './auto.php';
include_once './servicio.php';
include_once './turno.php';


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
            case 'vehiculo'://PUNTO 3
                $header = getallheaders();
                $token = $header['token'];
                $marca = $_POST['marca'] ?? "";
                $modelo = $_POST['modelo'] ?? "";
                $patente = $_POST['patente'] ?? "";
                $precio = $_POST['precio'] ?? 0;
                
                $usuarioLogueado = Token::VerificarToken($token);
                
                // $fecha = date('d-h');
                
                $usuarioLogueadoArray = (array) $usuarioLogueado;
                $listaVehiculos = Archivos::leerJson('./vehiculos.json',$listaVehiculos);
                
                if (!$usuarioLogueado) {
                    $datos = "Usuario no logueado, token incorrecto!";
                }
                else{
                    /**si la lista no esta vacia valido si hay patente existente */
                    if ($listaVehiculos != null) {
                        $listaVehiculosArray = (array) $listaVehiculos;
                        if(!(Auto::validarPatenteVehiculo($listaVehiculosArray,$patente))){
                            $auto = new Auto($marca,$modelo,$patente,$precio);

                            if(Archivos::guardarJson($auto,'vehiculos.json')){
                                $datos = "Vehiculo guardado correctamente";
                            }
                            else{
                                $datos = "Ocurrio un error al guardar el vehiculo";
                            }
                        }
                        else{
                            $datos = "Patente existente";
                        }
                    }
                    /**si la lista esta vacia doy de alta el vehiculo */
                    else{
                        $auto = new Auto($marca,$modelo,$patente,$precio);

                        if(Archivos::guardarJson($auto,'vehiculos.json')){
                            $datos = "Vehiculo guardado correctamente";
                        }
                        else{
                            $datos = "Ocurrio un error al guardar el vehiculo";
                        }
                    }
                    
                    
                }

                break;
            case 'stats':
                $foto = $_POST['foto'] ?? "";
                $token = $header['token'];
                $usuarioLogueado = Token::VerificarToken($token);
                
                $usuarioLogueadoArray = (array) $usuarioLogueado;

                if (!$usuarioLogueado) {
                    $datos = "Usuario no logueado, token incorrecto!";
                }
                else{
                    $datos = "";
                    
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
        $patente = $_GET['patente'] ?? "";
        $marca = $_GET['marca'] ?? "";
        $modelo = $_GET['modelo'] ?? "";
        


        $path_info = explode("/",$path_info);

        if (!$usuarioLogueado) {
            $datos = "token incorrecto";
        }
        else{
            switch ($path_info[1]){
                
            case 'patente': //punto 4
                $datos = "";
                $listaAutos = Archivos::leerJson('./vehiculos.json',$listaAutos);
                
                
                /**si la patente no tiene la marca o modelo ingresado no devuelve nada */

                foreach ($listaAutos as $vehiculo) {
                    $vehiculoAux = (object) $vehiculo;
                    if($path_info[2] == $vehiculoAux->_patente){
                        if (strcasecmp ( $vehiculoAux->_patente, $patente)  == 0) {
                        
                            $datos .= "Patente encontrada: " . $vehiculoAux->_patente . " <br> ";
                            
                        }
                        else if($patente != ""){
                            $datos .= " no existe $patente <br>";
                        }
                        
                        if (strcasecmp ( $vehiculoAux->modelo, $modelo)  == 0 && $modelo != "") {
                            $datos .= "Modelo encontrado: " . $vehiculoAux->modelo . "<br>";
                        }
                        else if($modelo != ""){
                            $datos .= " no existe $modelo <br>";
                        }
                        
                        if (strcasecmp ( $vehiculoAux->marca, $marca)  == 0 && $marca != "") {
                            
                            $datos .= " marca encontrada: " . $vehiculoAux->marca . "";
    
                        }
                        else if($marca != ""){
                            $datos .= " no existe $marca <br>";
                        }
                    }
                    else{
                        $datos = "Patente inexistente";
                    }
                    
                }
                
                if ($datos == "") {
                    $datos = 'Faltan datos';
                }
                
                break;
            case 'servicio'://PUNTO 5
                $datos = " ";

                $id = $_GET['id'] ?? 0;
                $tipo = $_GET['tipo'] ?? 0;
                $precio = $_GET['precio'] ?? 0;
                $demora = $_GET['demora'] ?? "";
                
                $listaPatente = Archivos::leerJson('./tipoServicio.json',$listaServicios);
                

                if ($listaPatente != null) {
                    $listaServiciosAux = (array) $listaServicios;
                    if(!(Servicio::validarServicioId($listaServiciosAux,$id))){
                        if ($tipo == 50000 || $tipo == 20000 || $tipo == 10000) {
                            $servicio = new Servicio($id,$tipo,$precio,$demora);
                        }
                        else{
                            $tipo = 0;
                            $servicio = new Servicio($id,$tipo,$precio,$demora);
                        }
                        

                        if(Archivos::guardarJson($servicio,'tipoServicio.json')){
                            $datos = "Se dio de alta el servicio correctamente";
                        }
                        else{
                            $datos = "Ocurrio un error al guardar el servicio";
                        }
                    }
                    else{
                        $datos = "ID de servicio existente";
                    }
                }
                else{
                    if ($tipo == 50000 || $tipo == 20000 || $tipo == 10000) {
                        $servicio = new Servicio($id,$tipo,$precio,$demora);
                    }
                    else{
                        $tipo = 0;
                        $servicio = new Servicio($id,$tipo,$precio,$demora);
                    }
                    

                    if(Archivos::guardarJson($servicio,'tipoServicio.json')){
                        $datos = "Se dio de alta el servicio correctamente";
                    }
                    else{
                        $datos = "Ocurrio un error al guardar el servicio";
                    }
                }
                if($datos === "")
                {
                    $datos = 'Faltan datos';
                }
                break;
            case 'turno':
                $fecha = $_GET['fecha'] ?? "";
                $id = $_GET['id'] ?? 0;

                $listaVehiculos = Archivos::leerJson('./vehiculos.json',$listaVehiculos);
                $listaServicios = Archivos::leerJson('./tipoServicio.json',$listaServicios);
                $listaTurnos = Archivos::leerJson('./turnos.json',$listaTurnos);

                /**si la lista no esta vacia, verifico disponibilidad por fecha */
                if ($listaTurnos != null) {
                    
                    foreach ($listaVehiculos as $vehiculo) {
                        $vehiculoAux = (object) $vehiculo;
                        if ($vehiculoAux->_patente == $patente) {
                            foreach ($listaServicios as $servicio) {
                                $servicioAux = (object) $servicio;
                                if($servicioAux->id == $id){
                                    if(Turno::validarDisponibilidad($listaTurnos,$fecha)){

                                        $turno = new Turno($fecha,$vehiculoAux->_patente,$vehiculoAux->marca,$vehiculoAux->modelo,$vehiculoAux->precio,$servicioAux->tipo);
                                        Archivos::guardarJson($turno,'turno.json');
                                        $datos = "Turno dado de alta correctamaente";
                                    }
                                    else{
                                        $datos = "Fecha sin disponibilidad";
                                    }
                                break;
                                }
                            }
                        }
                        else{
                            $datos = "Patente no encontrada";
                        }
                    }
                }
                else{
                    foreach ($listaVehiculos as $vehiculo) {
                        $vehiculoAux = (object) $vehiculo;
                        if ($vehiculoAux->_patente == $patente) {
                            foreach ($listaServicios as $servicio) {
                                $servicioAux = (object) $servicio;
                                if($servicioAux->id == $id){
                                    
                                    
                                    $turno = new Turno($fecha,$vehiculoAux->_patente,$vehiculoAux->marca,$vehiculoAux->modelo,$vehiculoAux->precio,$servicioAux->tipo);
                                    Archivos::guardarJson($turno,'turno.json');
                                    $datos = "Turno dado de alta correctamaente";
                                    
                                break;
                                }
                            }
                        }
                        else{

                            $datos = "Patente no encontrada";
                        }
                    }
                    
                }
                
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