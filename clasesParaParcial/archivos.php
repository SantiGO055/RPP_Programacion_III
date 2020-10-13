<?php

define ('SITE_ROOT', realpath(dirname(__FILE__)));

class Archivos{
    
    public static function guardarTxt($texto,$ruta){
        $retorno = false;
        $array = array();
        if(Archivos::leerTxt($ruta, $array))
        {
            array_push($array, $texto);
            $aux = json_encode($array, true);
        }
        else{
            array_push($array, $texto);
            $aux = json_encode($array, true);
        }
        $size = filesize($ruta);
        $archivo = fopen($ruta,'w+');

        if(fwrite($archivo, $aux))
        {
            $retorno = true;
        }

        fclose($archivo);

        return $retorno;

    }
    public static function leerTxt($ruta, $array)
    {
        $size = filesize($ruta);
        $retorno = false;
        if(file_exists($ruta) && $size > 0)
        {
            $archivo = fopen($ruta, 'r');
            $array = fread($archivo, filesize($ruta));

            fclose($archivo);
            $array = json_decode($array, true);
            $retorno = true;
        }
        else
        {
            $array = array();
        }
        return $retorno;
    }

    static function serializar($ruta, $objeto){
        $ar = fopen("./".$ruta, "a");

        fwrite($ar, serialize($objeto).PHP_EOL);
        fclose($ar);


    }

    static function deserializar($ruta){
        $lista = array();
        $ar = fopen("./".$ruta,"r");

        while(!feof($ar)){
            $objeto = unserialize(fgets($ar));
            if($objeto != null){
                array_push($lista, $objeto);
            }
        }
        fclose($ar);
        return $lista;

    }

    /**Guardar en JSON */
    public static function guardarJson($objeto,$ruta){
        #region codigoAnterior
        // $retorno = false;
        // if(isset($lista)){
        //     $ar = fopen("./".$ruta,"w");
        //     array_push($array,$objeto);
        //     fwrite($ar,json_encode($array));
        //     fclose($ar);
        //     $retorno = true;
        // }
        // else{
        //     $array2 = array();
        //     $ar = fopen("./".$ruta, "w");
        //     array_push($array2,$objeto);
        //     fwrite($ar,json_encode($array2));
        //     fclose($ar);
        //     $retorno = true;
        // }
        // return $retorno;
        #endregion

        $retorno = false;
        if(Archivos::leerJson($ruta, $array))
        {
            array_push($array, $objeto);
            $aux = json_encode($array, true);
        }
        else
        {
            array_push($array, $objeto);
            $aux = json_encode($array, true);
        }
        $archivo = fopen($ruta, 'w');
        if(fwrite($archivo, $aux))
        {
            $retorno = true;
        }
        $cerrar = fclose($archivo);

        return $retorno;

    }

    static function leerJson($ruta,&$array){
        #region codigoAnterior
        // if (file_exists($ruta)){
        //     $ar = fopen($ruta, "r");

        //     $lista = json_decode(fgets($ar),true); //true para poder manejarlo como array asociativo
        //     fclose($ar);
        //     if(isset($lista)){
        //         return $lista;
        //     }
        //     else{
        //         return null;
        //     }
        // }
        // else{
        //     echo "El archivo no existe";
        // }
        #endregion

        $retorno = false;
        if(file_exists($ruta) && filesize($ruta) > 0)
        {
            $archivo = fopen($ruta, 'r');
            $array = fread($archivo, filesize($ruta));
            fclose($archivo);
            $array = json_decode($array, true);
            $retorno = $array;
        }
        else
        {
            $array = array();
            $retorno = $array;
        }
        return $retorno;
    }

    public static function modificarJson($path,$index,$key,$value){
        
        //Load the file
        $data = file_get_contents($path);
        //var_dump($data);

        $json_arr = json_decode($data, true);

        
        $json_arr[$index][$key] = $value;

        file_put_contents('./users.json', json_encode($json_arr));
        
    }
    /**Valido si la extension es imagen */
    static function esImagen($extension){
        $extensionesValidas = array(
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml'
        );
        $retorno = false;
        
        foreach ($extensionesValidas as $key => $value) {
            if ($value == $extension) {
                $retorno = true;
                return $retorno;
            }
            else{
                $retorno = false;
            }
        }
        
        return $retorno;


    }
    
    //una es desde el name y otra es a traves de los mime
    //con el explode por que separador el string convierta en array
    //tambien controlar y limitar la cantidad de megas de un archivo
    // public static function modificarJson($path,$key,$valor){

    //     $json_object = file_get_contents($path);
    //     $data = json_decode($json_object, true);
    //     //Then edit what you want such as:
    //     // var_dump($data);
    //     echo $key;
    //     // echo "<br>";
    //     // echo $valor;
    //     // echo "<br>";
    //     $data[$key] = $valor;
    //     //Finally rewrite it back on the file (or a newer one):

    //     $json_object = json_encode($data);
    //     file_put_contents($path, $json_object);

    // }

    public static function asignarNombreFotoAleatorio(){
        $extensionExplode = explode('.',$_FILES['imagen']['name']);
        $extension = $extensionExplode[1];
        $aleatorio = rand(1000,100000);
        $nombreArchivo = "imagen" . $aleatorio .'.' . $extension;
        return $nombreArchivo;
    }
    public static function guardarImagen($_files,$bytes,$path,$aleat = false){

        $origen = $_files['imagen']['tmp_name'];
        if($aleat){
            $aleatorio = rand(1000,100000);
            
            $nombreArchivo = Archivos::asignarNombreFotoAleatorio();
            
            $destino = $path . "/" . $nombreArchivo;
        
            if(Archivos::esImagen($_files['imagen']['type']) && Archivos::validarBytesImagen($_files,$bytes)){
                $subido = move_uploaded_file($origen,SITE_ROOT.$destino);
                if ($subido) {
                    return $nombreArchivo;
                }
                else{
                    return "No se pudo guardar la imagen";
                }
            }
            else{
                return "El archivo no es una imagen o supera el tamaño de 3.5MB";
            }

        }
        else if ($aleat != null || $aleat == false){
            $extensionExplode = explode('.',$_FILES['imagen']['name']);
            $extension = $extensionExplode[1];
            $destino = $path . '.' . $extension;
       
            if(Archivos::esImagen($_files['imagen']['type']) && Archivos::validarBytesImagen($_files,$bytes)){
                $subido = move_uploaded_file($origen,$destino);
                if ($subido) {
                    return "Se guardo la imagen correctamente";
                }
                else{
                    return "No se pudo guardar la imagen";
                }
            }
            else{
                return "El archivo no es una imagen o supera el tamaño de 3.5MB";
            }
        }
    }

    static function validarBytesImagen($_files,$bytes){
        
        if ($_files['imagen']['size'] <= $bytes) {
            
            return true;
        }
        else{
            return false;
        }
        
    }
    static function moverImagen($origen,$destino){
        if(file_exists($origen) && file_exists($destino)){
            if(rename($origen,$destino))
                return true;
            else
               return false;
                // echo "no se renombro";
        }
        else
            return false;
            // echo "no existe la ruta";
        
    }
    

}



?>