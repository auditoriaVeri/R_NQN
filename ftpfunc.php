<?php

# FUNCIONES FTP
# CONSTANTES
# Cambie estos datos por los de su Servidor FTP
define("SERVER", "vtvunc.ddns.net"); //IP o Nombre del Servidor
define("PORT", 21); //Puerto
define("USER", "usrftp"); //Nombre de Usuario
define("PASSWORD", "v8276TA"); //Contrase�a de acceso
define("PASV", true); //Activa modo pasivo
# FUNCIONES

function ConectarFTP() {
//Permite conectarse al Servidor FTP
  $id_ftp = ftp_connect(SERVER, PORT); //Obtiene un manejador del Servidor FTP
  ftp_login($id_ftp, USER, PASSWORD); //Se loguea al Servidor FTP
  ftp_pasv($id_ftp, PASV); //Establece el modo de conexi�n
  return $id_ftp; //Devuelve el manejador a la funci�n
}

function SubirArchivo($archivo_local, $archivo_remoto) {
  $host = "vtvunc.ddns.net";
  $port = 21;
  $user = "usrftp";
  $password = "v8276TA";
  $ruta = "files";
  $salida = true;

  $conn_id = @ftp_connect($host, $port);
  if ($conn_id) {
    //Realizamos el login con nuestro usuario y contrase�a 
    if (@ftp_login($conn_id, $user, $password)) {
      //Canviamos al directorio especificado 
      if (@ftp_chdir($conn_id, $ruta)) {
        //Subimos el fichero 
        if (@ftp_put($conn_id, $archivo_remoto, $archivo_local, FTP_BINARY))
        //echo "Fichero subido correctamente"; 
          $salida = true;
        else {
          $salida = false;
          echo "No ha sido posible subir el fichero $archivo_local";
        }
      } else {
        $salida = false;
        echo "No existe el directorio especificado";
      }
    } else {
      $salida = false;
      echo "El usuario o la contrase�a son incorrectos";
    }
    //Cerramos la conexion ftp 
    ftp_close($conn_id);
  } else {
    $salida = false;
    echo "No ha sido posible conectar con el servidor";
  }
  return $salida;
}

function ObtenerRuta() {
//Obriene ruta del directorio del Servidor FTP (Comando PWD)
  $id_ftp = ConectarFTP(); //Obtiene un manejador y se conecta al Servidor FTP
  $Directorio = ftp_pwd($id_ftp); //Devuelve ruta actual p.e. "/home/willy"
  ftp_quit($id_ftp); //Cierra la conexion FTP
  return $Directorio; //Devuelve la ruta a la funci�n
}

?>
