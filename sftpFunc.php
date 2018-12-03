<?php
include_once("conexionSFTP.php");

function SubirArchivo($archivo_local, $archivo_remoto) 
{


$host = "vtvunc.ddns.net";
  $puerto = 22;
  $usuario = "usrftp";
  $password = "v8276TA";
  $raiz = "files/";

  $salida = true;

  $conexion = new conexionSFTP($host, $puerto, $usuario, $password);
  if ($conexion->conectar()) {
    if ($conexion->put_contents($raiz . $archivo_remoto, file_get_contents($archivo_local))) {
      $salida = true;
    } else {
      $salida = false;
      echo "No ha sido posible subir el fichero $archivo_local <br />";
    }
  } else {
    $salida = false;
    echo "No ha sido posible conectar con el servidor <br />";

  }

  return $salida;
}
