<?php

/* * ******* AHORA LOS ARCHIVOS ADJUNTOS ************ */

if ($continua)
{
  $sql = " SELECT * FROM adjuntos WHERE Replicado = 0 ";
  $respVer = $base->query($sql);
  if ($respVer) {
    /*     * *** Por cada adjunto vamos a insert *** */
    foreach ($respVer as $row) {
      //Aca deberia copiar el archivo al servidor de central, si todo sale bien
      $archivo_local = $UploadsBasePath . $row["Nombre"];
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
          $msnlog = "Ocurrio un error al cargar los adjuntos de verificaciones en central. (sql ins). Taller: $nomTaller.<br />" . $sqlInSer;
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
      $archivo_local = $UploadsBasePath . $row["Archivo"];
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

/* * ******* ADJUNTOS EXCEPCIONES ************ */
if ($continua)
{
  $sql = " SELECT * FROM adjuntosauditoria WHERE Replicado = 0 ";


}

/* * ******* AHORA LOS ARCHIVOS ADJUNTOS DE AUDITORIAS ************ */

if ($continua) {
  $sql = " SELECT * FROM adjuntosauditoria WHERE Replicado = 0 ";
  $respVer = $base->query($sql);
  if ($respVer) {
    /*     * *** Por cada archivo  => insert *** */
    foreach ($respVer as $row) {
      //Aca deberia cop�ar el archivo al servidor de central, si todo sale bien cargo en la base
      $archivo_local = $UploadsBasePath . $row["Archivo"];
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
///force push

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

      $AE= ArchivoExistente($archivo_remoto);

/*      echo "<br/>";
      echo $archivo_remoto;
      echo "Existente: "; echo $AE;*/

      if ($AE != 1)
      {
        if (SubirArchivo($archivo_local, $archivo_remoto))
        {
          $sql = "UPDATE vehiculos SET ReplicoArchivoTV = 1 WHERE Dominio = '" . $rowTV["Dominio"] . "'";
          if (!$base->query($sql))
          {
            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
            $msnlog = "Ocurrio un al marcar como replicado un adjunto de TV en taller. - Taller: $nomTaller";
            $continua = false;
            break;
          }
        } else
        {
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = "Ocurrio un error al cargar los archivos adjuntos de las tarjetas verdes en central. Taller: $nomTaller";
          $continua = false;
          break;
        }
      }
      else
      {
        // esta queriendo subir otra vez el mismo
        $sql = "UPDATE vehiculos SET ReplicoArchivoTV = 1 WHERE Dominio = '" . $rowTV["Dominio"] . "'";
        if (!$base->query($sql))
        {
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = "Ocurrio un al marcar como replicado un adjunto de TV en taller. - Taller: $nomTaller";
          $continua = false;
          break;
        }
      }
		}/* *** cierra el foreach */
	}else{
		/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
		$msnlog = "Ocurrio un error al buscar adjuntos de tarjetas verdes para replicar.";
		$continua = false;
	}
}



/***************************************************************************
 *
 *				ADJUNTOSPENDIENTES
 *
 **************************************************************************/

if($continua)
{
  $sql = "SELECT * FROM AdjuntosPendientes WHERE Replicado = 0 AND idTaller = $idTaller";

  $respAdjuntosPendientes = $base->query($sql);

  if ($respAdjuntosPendientes)
  {
    /***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
    foreach ($respAdjuntosPendientes as $AP)
    {
      // Ahora a subirlo
      $archivo_local = $UploadsBasePath . $AP["Nombre"];
      $archivo_remoto = $AP["Nombre"];
      $B= SubirArchivo($archivo_local, $archivo_remoto);
      if (!$B)
      {
        /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
        $msnlog = "Ocurrio un al subir un adjuntoPendiente. - $archivo_local";
        $continua = false;
        exit;
      }

      $ComandoSQL =
        " SELECT COUNT(*) as HM  
        FROM AdjuntosPendientes
        WHERE
          idTaller = :idTaller AND 
          idArchivo = :idArchivo";

      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idArchivo', $AP["idArchivo"]);
      $SSQL->bindValue(':idTaller', $AP["idTaller"]);

      $Res = $SSQL->execute();
      $R = $SSQL->fetch();

      $HM = $R["HM"];

      if ($HM == 0)
      {
        $ComandoSQL =
          "INSERT INTO adjuntospendientes
            (idArchivo, Nombre, idPendiente, idTaller, FechaCarga, FechaHoraRep, Activo)
          VALUES 
            (:idArchivo, :Nombre, :idPendiente, :idTaller, :FechaCarga, NOW(), :Activo)";
      } else
      {
        $ComandoSQL =
          "UPDATE adjuntospendientes
          SET
            Nombre= :Nombre,
            idPendiente= :idPendiente,
            FechaCarga= :FechaCarga,
            FechaHoraRep= NOW(),
            Activo= :Activo
          WHERE 
           idArchivo = :idArchivo AND
        idTaller = :idTaller";
      }

      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idArchivo', $AP["idArchivo"]);
      $SSQL->bindValue(':idTaller', $AP["idTaller"]);
      $SSQL->bindValue(':Nombre', $AP["Nombre"]);
      $SSQL->bindValue(':idPendiente', $AP["idPendiente"]);
      $SSQL->bindValue(':FechaCarga', $AP["FechaCarga"]);
      $SSQL->bindValue(':Replicado', true);
      $SSQL->bindValue(':Activo', $AP["Activo"]);

      try
      {
        $Res = $SSQL->execute();

        if (!$Res)
        {
          print_r($SSQL->errorInfo());
          $continua= false;
          exit();
        }


        // Marcar como replicado
        $sql =
          " UPDATE adjuntospendientes 
          SET Replicado = 1 
          WHERE 
            idArchivo = " . $AP["idArchivo"] . " AND
            idTaller = " . $AP["idTaller"];

        if (!$base->query($sql))
        {
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = "Ocurrio un al marcar como replicado un adjuntoPendiente en taller. - Taller: $nomTaller";
          $continua = false;
          exit;
        } else
          $cantAdjuntosPendientes++;
      } catch (Exception $e)
      {
        $continua = false;
        exit("ERROR");
        throw $e;
      }
    }
  }
}




/********* ADEJUNTOSEXCEPCION *************/

if ($continua)
{
  $sql = " SELECT * FROM adjuntosexcepcion WHERE Replicado = 0 ";
  $respVer = $base->query($sql);
  /*     * *** Por cada archivo  => insert *** */
  foreach ($respVer as $RAdjExpcep)
  {
    //Aca deberia cop�ar el archivo al servidor de central, si todo sale bien cargo en la base
    $archivo_local = $UploadsBasePath . $row["Archivo"];
    $archivo_remoto = $row["Archivo"];

    if (SubirArchivo($archivo_local, $archivo_remoto))
    {
      $ComandoSQL =
        " SELECT COUNT(*) as HM  
        FROM adjuntosexcepcion
        WHERE
          idAdjuntosExcepcion = :idAdjuntosExcepcion AND 
          idNC = :idNC";

      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idAdjuntosExcepcion', $RAdjExpcep["idAdjuntosExcepcion"]);
      $SSQL->bindValue(':idTaller', $RAdjExpcep["idTaller"]);

      $Res = $SSQL->execute();  // Para ver si anduvo pero no le doy bola
      $R = $SSQL->fetch();

      $HM = $R["HM"];

      if ($HM == 0)
      {
        $ComandoSQL =
          " INSERT INTO adjuntosexcepcion
            (
                idAdjuntosExcepcion, idExcepcion, idTaller, archivo, fechaCarga, replicado, activo
            ) 
            VALUES            
	          ( 
	              :idAdjuntosExcepcion, :idExcepcion, :idTaller, :archivo, :fechaCarga, :replicado, :activo
	          )";
      }
      else
      {
        $ComandoSQL =
          " UPDATE adjuntosexcepcion
	          SET        		  
		          idExcepcion= :idExcepcion, 
		          archivo= :archivo, 
		          fechaCarga= :fechaCarga, 
		          replicado= :replicado, 
		          activo= :activo
	          WHERE 
	            idAdjuntosExcepcion=:idAdjuntosExcepcion AND
	            idTaller=:idTaller";
      }

      // A ejecutar
      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idAdjuntosExcepcion', $RAdjExpcep["idAdjuntosExcepcion"]);
      $SSQL->bindValue(':idExcepcion', $RAdjExpcep["idExcepcion"]);
      $SSQL->bindValue(':idTaller', $RAdjExpcep["idTaller"]);
      $SSQL->bindValue(':archivo', $RAdjExpcep["archivo"]);
      $SSQL->bindValue(':replicado', $RAdjExpcep["replicado"]);
      $SSQL->bindValue(':activo', $RAdjExpcep["activo"]);

      try
      {
        $Res = $SSQL->execute();

        if (!$Res)
        {
          print_r($SSQL->errorInfo());
          exit();
        }
      } catch (Exception $e)
      {
        $continua = false;
        exit("ERROR");
        throw $e;
      }

      // Ahora a marcar como replicado
      $ComandoSQL = "
          UPDATE adjuntosexcepcion 
          SET
            Replicado= 1
          WHERE
            idAdjuntosExcepcion=:idAdjuntosExcepcion AND
	          idTaller=:idTaller";

      $SSQL = $base->prepare($ComandoSQL);

      $SSQL->bindValue(':idAdjuntosExcepcion', $RAdjExpcep["idAdjuntosExcepcion"]);
      $SSQL->bindValue(':idTaller', $RAdjExpcep["idTaller"]);

      $Res = $SSQL->execute();
      if (!$Res)
      {
        print_r($SSQL->errorInfo());
        exit();
      }
      else
        $cantAdjuntosExcepciones++;
    }
    else
    {
      /*         * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
      $msnlog = "Ocurrio un error al cargar los archivos adjuntos de excepciones a central. Taller: $nomTaller";
      $continua = false;
      break;
    }
  } // foreach
}

/********* ADEJUNTOSPRORROGA *************/

if ($continua)
{
  $sql = " SELECT * FROM adjuntosprorroga WHERE Replicado = 0 ";
  $respVer = $base->query($sql);
  /*     * *** Por cada archivo  => insert *** */
  foreach ($respVer as $RAdjProrroga)
  {
    //Aca deberia cop�ar el archivo al servidor de central, si todo sale bien cargo en la base
    $archivo_local = $UploadsBasePath . $row["Archivo"];
    $archivo_remoto = $row["Archivo"];

    if (SubirArchivo($archivo_local, $archivo_remoto))
    {
      $ComandoSQL =
        " SELECT COUNT(*) as HM  
        FROM adjuntosprorroga
        WHERE
          idAdjuntosProrroga = :idAdjuntosExcepcion AND 
          idTaller = :idTaller";

      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idAdjuntosProrroga', $RAdjProrroga["idAdjuntosProrroga"]);
      $SSQL->bindValue(':idTaller', $RAdjProrroga["idTaller"]);

      $Res = $SSQL->execute();  // Para ver si anduvo pero no le doy bola
      $R = $SSQL->fetch();

      $HM = $R["HM"];

      if ($HM == 0)
      {
        $ComandoSQL= "
            INSERT INTO adjuntosprorroga
            (
                idAdjuntosProrroga, idProrroga, idTaller, archivo, fechaCarga, replicado, activo
            ) 
            VALUES            
	          ( 
	              :idAdjuntosProrroga, :idProrroga, :idTaller, :archivo, :fechaCarga, :replicado, :activo
	          )";
      }
      else
      {
        $ComandoSQL =
          " UPDATE adjuntosprorroga
	          SET        		  
		          idProrroga= :idProrroga, 
		          archivo= :archivo, 
		          fechaCarga= :fechaCarga, 
		          replicado= :replicado, 
		          activo= :activo
	          WHERE 
	            idAdjuntosProrroga= :idAdjuntosProrroga AND
	            idTaller=:idTaller";
      }

      // A ejecutar
      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idAdjuntosProrroga', $RAdjProrroga["idAdjuntosProrroga"]);
      $SSQL->bindValue(':idTaller', $RAdjProrroga["idTaller"]);
      $SSQL->bindValue(':idProrroga', $RAdjProrroga["idProrroga"]);
      $SSQL->bindValue(':fechaCarga', $RAdjProrroga["fechaCarga"]);
      $SSQL->bindValue(':archivo', $RAdjProrroga["fechaCarga"]);
      $SSQL->bindValue(':activo', $RAdjProrroga["activo"]);
      $SSQL->bindValue(':replicado', 1);


      try
      {
        $Res = $SSQL->execute();

        if (!$Res)
        {
          print_r($SSQL->errorInfo());
          exit();
        }
      } catch (Exception $e)
      {
        $continua = false;
        exit("ERROR");
        throw $e;
      }

      // Ahora a marcar como replicado
      $ComandoSQL = "
          UPDATE adjuntosprorroga 
          SET
            Replicado= 1
          WHERE
            idAdjuntosProrroga=:idAdjuntosProrroga AND
	          idTaller=:idTaller";

      $SSQL = $base->prepare($ComandoSQL);

      $SSQL->bindValue(':adjuntosprorroga', $RAdjProrroga["adjuntosprorroga"]);
      $SSQL->bindValue(':idTaller', $RAdjProrroga["idTaller"]);

      $Res = $SSQL->execute();
      if (!$Res)
      {
        print_r($SSQL->errorInfo());
        exit();
      }
      else
        $cantAdjuntosProrrogas++;
    }
    else
    {
      /*         * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
      $msnlog = "Ocurrio un error al cargar los archivos adjuntos de excepciones a central. Taller: $nomTaller";
      $continua = false;
      break;
    }
  } // foreach


}

/********* VERIFICACIONESPDF *************/
if ($continua)
{
  $sql = " SELECT * FROM verificacionesPDF WHERE Replicado = 0 ";
  $respVer = $base->query($sql);

  foreach ($respVer as $RVPDF)
  {
    //Aca deberia copr el archivo al servidor de central, si todo sale bien cargo en la base
    $archivo_local = $UploadsBasePath . "pdf/" . $RVPDF["NombreA4"] . ".pdf";
    $archivo_remoto = "pdf/" . $RVPDF["NombreA4"] . ".pdf";

    // son dos archivos
    $B= SubirArchivo($archivo_local, $archivo_remoto);

    if ($B)
    {
      $archivo_local = $UploadsBasePath . "pdf/" . $RVPDF["NombreTC"] . ".pdf";
      $archivo_remoto = "pdf/" . $RVPDF["NombreTC"] . ".pdf";

      $B= SubirArchivo($archivo_local, $archivo_remoto);
    }

    if ($B)
    {
      $ComandoSQL =
        " SELECT COUNT(*) as HM  
        FROM verificacionesPDF
        WHERE
          idPDF = :idPDF AND 
          idTaller = :idTaller";

      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idPDF', $RVPDF["idPDF"]);
      $SSQL->bindValue(':idTaller', $RVPDF["idTaller"]);

      $Res = $SSQL->execute();  // Para ver si anduvo pero no le doy bola
      $R = $SSQL->fetch();

      $HM = $R["HM"];

      if ($HM == 0)
      {
        $ComandoSQL= "
            INSERT INTO verificacionesPDF
            (
                idPDF, NombreA4, NombreTC, HashA4, idVerificacion, idTaller, FechaCarga, FechaHoraRep, Enviado
            ) 
            VALUES            
	          ( 
	              :idPDF, :NombreA4, :NombreTC, :HashA4, :idVerificacion, :idTaller, :FechaCarga, NOW(), :Enviado
	          )";
      }
      else
      {
        $ComandoSQL =
          " UPDATE verificacionesPDF
	          SET        		  
                NombreA4= :NombreA4, 
                NombreTC= :NombreTC, 
                HashA4= :HashA4, 
                idVerificacion= :idVerificacion,  
                FechaCarga= :FechaCarga, 
                FechaHoraRep= NOW(),
                Enviado= :Enviado
	          WHERE 
	            idPDF= :idPDF AND
	            idTaller=:idTaller";
      }



      // A ejecutar
      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idPDF', $RVPDF["idPDF"]);
      $SSQL->bindValue(':NombreA4', $RVPDF["NombreA4"]);
      $SSQL->bindValue(':NombreTC', $RVPDF["NombreTC"]);
      $SSQL->bindValue(':HashA4', $RVPDF["HashA4"]);
      $SSQL->bindValue(':idVerificacion', $RVPDF["idVerificacion"]);
      $SSQL->bindValue(':idTaller', $RVPDF["idTaller"]);
      $SSQL->bindValue(':FechaCarga', $RVPDF["FechaCarga"]);
      $SSQL->bindValue(':Enviado', $RVPDF["Enviado"]);

      try
      {
        $Res = $SSQL->execute();

        if (!$Res)
        {
          print_r($SSQL->errorInfo());
          exit();
        }
      } catch (Exception $e)
      {
        $continua = false;
        exit("ERROR");
        throw $e;
      }

      // Ahora a marcar como replicado
      $ComandoSQL = "
          UPDATE verificacionesPDF 
          SET
            Replicado= 1
          WHERE
            idPDF= :idPDF AND
	          idTaller= :idTaller";

      $SSQL = $base->prepare($ComandoSQL);

      $SSQL->bindValue(':idPDF', $RVPDF["idPDF"]);
      $SSQL->bindValue(':idTaller', $RVPDF["idTaller"]);

      $Res = $SSQL->execute();
      if (!$Res)
      {
        print_r($SSQL->errorInfo());
        exit();
      }
      else
        $cantVerificacionesPDF++;

    }
    else
    {
      /*         * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
      $msnlog = "Ocurrio un error al cargar los archivos de verificacionespdf a central. Taller: $nomTaller";
      $continua = false;
      break;
    }
  } // foreach


}



