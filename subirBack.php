<?php
function SubirArchivo($archivo_local,$archivo_remoto){

$host="www.eurekasoluciones.com.ar"; 
$port=21; 
$user="w1080237@eurekasoluciones.com.ar"; 
$password="03GOnutome"; 
$ruta="bckUNC/veritecnica"; 
/*
$host="uncbackups.ddns.net"; 
$port=21; 
$user="usrftp"; 
$password="v8276TA"; 
$ruta="bck/veritecnica"; 
*/
$salida = true;

$conn_id=@ftp_connect($host,$port); 
	if($conn_id) { 
	//Realizamos el login con nuestro usuario y contraseña 
		if(@ftp_login($conn_id,$user,$password)) { 
		//Canviamos al directorio especificado
ftp_pasv($conn_id, true); 
			if(@ftp_chdir($conn_id,$ruta)) {
				//Subimos el fichero
				if(@ftp_put($conn_id,$archivo_remoto,$archivo_local,FTP_BINARY)) 
				 	//echo "Fichero subido correctamente"; 
					$salida = true;
				else {$salida = false; echo "No ha sido posible subir el fichero $archivo_local"; }
			}else 
			  {$salida = false; echo "No existe el directorio especificado";} 
		}else {$salida = false; echo "El usuario o la contraseña son incorrectos";} 
		//Cerramos la conexion ftp 
		ftp_close($conn_id); 
	}else {$salida = false; echo "No ha sido posible conectar con el servidor"; }
 return $salida;
}


//Aca deberia copíar el archivo al servidor de central, si todo sale bien
$path_local = "/var/www/html/tonl/tnqn17veritecnica/bck";
$dir = opendir($path_local);
$files = array ();
while ( $current = readdir ( $dir ) ) 
{
	if ($current != "." && $current != ".." && !is_dir($current)) 
	{
		$archivo_local = $path_local."/".$current;



		$archivo_remoto = $current;

		if(SubirArchivo($archivo_local,$archivo_remoto))
		{		  
			if (rename($archivo_local,"/var/www/html/tonl/tnqn17veritecnica/bck/sub/".$archivo_remoto)) 
			{
		 		//unlink($archivo_local);
		 		echo "El archivo /var/www/html/tonl/tnqn17veritecnica/bck".$archivo_remoto." se movio <br />";
	      		}else
				echo "El archivo /var/www/html/tonl/tnqn17veritecnica/bck".$archivo_remoto." no se movio <br />";
			
			echo "El archivo $archivo_local se subio correctamente";
		}
	}
	
}
