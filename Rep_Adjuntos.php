<?php

/* * ******* AHORA LOS ARCHIVOS ADJUNTOS ************ */

if ($continua) {
  $sql = " SELECT * FROM adjuntos WHERE Replicado = 0 ";
  $respVer = $base->query($sql);
  if ($respVer) {
    /*     * *** Por cada adjunto vamos a insert *** */
    foreach ($respVer as $row) {
      //Aca deberia copiar el archivo al servidor de central, si todo sale bien
      $archivo_local = "/var/www/html/tonl/tnqn17veritecnica/taller/uploads/" . $row["Nombre"];
      $archivo_remoto = $row["Nombre"];
      if (SubirArchivo($archivo_local, $archivo_remoto)) {
        $sqlInSer = "INSERT INTO adjuntos (idArchivo,Nombre,idVerificacion,idTaller,FechaCarga)
								VALUES (" . $row["idArchivo"] . ",'" . $row["Nombre"] . "'," . $row["idVerificacion"] . "," . $row["idTaller"] . ",'" . $row["FechaCarga"] . "')";
        //echo $sqlInSer."<br />";
        if ($dbUNC->query($sqlInSer)) {
          /*           * ** una vez que cargue en server, le indico en el taller que ya fue replicado *** */
          $sql = "UPDATE adjuntos SET Replicado = 1 WHERE idArchivo = " . $row["idArchivo"] . " AND idTaller = " . $row["idTaller"];
          if (!$base->query($sql)) {
            /*             * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
            $msnlog = "Ocurrio un al marcar como replicado algun adjunto de una verificacion en taller. - Taller: $nomTaller";
            $continua = false;
            break;
          }
        } else {
          /*           * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
          $msnlog = "Ocurrio un error al cargar los adjuntos de verificaciones en central. (sql ins). Taller: $nomTaller";
          $continua = false;
          break;
        }
      } else {
        /*         * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
        $msnlog = "Ocurrio un error al cargar los archivos adjuntos de verificaciones en central (sftp). Taller: $nomTaller";
        $continua = false;
        break;
      }
    }/*     * ** cierra el foreach */
  } else {
    /*     * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
    $msnlog = "Ocurrio un error al buscar adjuntos de verificaciones de equipos para replicar.";
    $continua = false;
  }
}

/* * ******* AHORA LOS ARCHIVOS ADJUNTOS DE MANTENIMIENTOS ************ */

if ($continua) {
  $sql = " SELECT * FROM adjuntosmantenimientos WHERE Replicado = 0 ";
  $respVer = $base->query($sql);
  if ($respVer) {
    /*     * *** Por cada equipo vamos a ver si esta el server => update, si no esta => insert *** */
    foreach ($respVer as $row) {
      //Aca deberia copiar el archivo al servidor de central, si todo sale bien 
      $archivo_local = "/var/www/html/tonl/tnqn17veritecnica/taller/uploads/" . $row["Archivo"];
      $archivo_remoto = $row["Archivo"];
      if (SubirArchivo($archivo_local, $archivo_remoto)) {

        $sqlInSer = "INSERT INTO adjuntosmantenimientos (idMantenimiento,idTaller,Archivo,FechaCarga)
							VALUES (" . $row["idMantenimiento"] . "," . $row["idTaller"] . ",'" . $row["Archivo"] . "','" . $row["FechaCarga"] . "')";

        //echo $sqlInSer."<br />";
        if ($dbUNC->query($sqlInSer)) {
          /*           * ** una vez que cargue en server, le indico en el taller que ya fue replicado *** */
          $sql = "UPDATE adjuntosmantenimientos SET Replicado = 1 WHERE idMantenimiento = " . $row["idMantenimiento"] . " AND idTaller = " . $row["idTaller"];
          if (!$base->query($sql)) {
            /*             * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
            $msnlog = "Ocurrio un al marcar como replicado algun adjunto de un mantenimiento de equipo en taller. - Taller: $nomTaller";
            $continua = false;
            break;
          }
        } else {
          /*           * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
          $msnlog = "Ocurrio un error al cargar los adjuntos de mantenimientos de los equipos en central. Taller: $nomTaller";
          $continua = false;
          break;
        }
      } else {
        /*         * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
        $msnlog = "Ocurrio un error al cargar los archivos adjuntos de mantenimientos en central. Taller: $nomTaller";
        $continua = false;
        break;
      }
    }/*     * ** cierra el foreach */
  } else {
    /*     * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
    $msnlog = "Ocurrio un error al buscar adjuntos de mantenimientos de equipos para replicar. . Taller: $nomTaller";
    $continua = false;
  }
}

/* * ******* AHORA LOS ARCHIVOS ADJUNTOS DE AUDITORIAS ************ */

if ($continua) {
  $sql = " SELECT * FROM adjuntosauditoria WHERE Replicado = 0 ";
  $respVer = $base->query($sql);
  if ($respVer) {
    /*     * *** Por cada archivo  => insert *** */
    foreach ($respVer as $row) {
      //Aca deberia cop�ar el archivo al servidor de central, si todo sale bien cargo en la base
      $archivo_local = "/var/www/html/tonl/tnqn17veritecnica/taller/uploads/" . $row["Archivo"];
      $archivo_remoto = $row["Archivo"];
      if (SubirArchivo($archivo_local, $archivo_remoto)) {

        $sqlInSer = "INSERT INTO adjuntosauditoria(idAuditoria,idTaller,Archivo,FechaCarga,FechaHoraRep)
							VALUES (" . $row["idAuditoria"] . "," . $row["idTaller"] . ",'" . $row["Archivo"] . "','" . $row["FechaCarga"] . "',NOW())";

        //echo $sqlInSer."<br />";
        if ($dbUNC->query($sqlInSer)) {
          /*           * ** una vez que cargue en server, le indico en el taller que ya fue replicado *** */
          $sql = "UPDATE adjuntosauditoria SET Replicado = 1 WHERE idAuditoria = " . $row["idAuditoria"] . " AND idTaller = " . $row["idTaller"];
          if (!$base->query($sql)) {
            /* *** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
            $msnlog = "Ocurrio un al marcar como replicado algun adjunto de un mantenimiento de equipo en taller. - Taller: $nomTaller";
            $continua = false;
            break;
          }
        } else {
          /*           * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
          $msnlog = "Ocurrio un error al cargar los adjuntos de auditorias en central. Taller: $nomTaller";
          $continua = false;
          break;
        }
      } else {
        /*         * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
        $msnlog = "Ocurrio un error al cargar los archivos adjuntos de auditorias en central. Taller: $nomTaller";
        $continua = false;
        break;
      }
    }/*     * ** cierra el foreach */
  } else {
    /*     * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
    $msnlog = "Ocurrio un error al buscar adjuntos de auditorias en el taller para replicar. - Taller: $nomTaller";
    $continua = false;
  }
}

/********* AHORA LOS ARCHIVOS ADJUNTOS DE TARJETAS VERDES *************/

if($continua){
	$sql = " SELECT Dominio, arTarjetaVerde FROM vehiculos WHERE ReplicoArchivoTV = 0 ";
	$respTV = $base->query($sql);
	if($respTV){
		/***** Por cada adjunto vamos a insert ****/
		foreach ($respTV as $rowTV)
		{
			//Aca deberia copíar el archivo al servidor de central, si todo sale bien
			$archivo_local = "/var/www/html/tnqn17veritecnica/uploads/".$rowTV["arTarjetaVerde"];
			$archivo_remoto = $rowTV["arTarjetaVerde"];
			if(SubirArchivo($archivo_local,$archivo_remoto))
			{
				$sql = "UPDATE vehiculos SET ReplicoArchivoTV = 1 WHERE Dominio = '".$rowTV["Dominio"]."'";
				if(!$base->query($sql)){
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un al marcar como replicado un adjunto de TV en taller. - Taller: $nomTaller";
					$continua = false;
					break;
				}
			}
			else{
				/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
				$msnlog = "Ocurrio un error al cargar los archivos adjuntos de las tarjetas verdes en central. Taller: $nomTaller";
				$continua = false;
				break;
			}
		}/* *** cierra el foreach */
	}else{
		/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
		$msnlog = "Ocurrio un error al buscar adjuntos de tarjetas verdes para replicar.";
		$continua = false;
	}
}


