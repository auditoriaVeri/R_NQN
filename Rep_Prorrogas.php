<?php

/***** Prorrogas. Tienen que subir las nuevas, bajar los dictamenes de central *****/

if($continua)
{
  $sql = " SELECT * FROM prorroga WHERE Replicado = 0 AND idTaller = $idTaller";
  $respPro = $base->query($sql);
  if ($respPro)
  {
    /***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
    foreach ($respPro as $RP)
    {
      $ComandoSQL =
        "INSERT INTO prorroga
         (
            idProrroga, idTaller, numeroCertificado, 
            replicado, modificado, fechaHoraModificacion, historialModificacion, 
            dominio, fechaHoraCreacion, 
            fundamentacionPeticion,
            fundamentacionDictamen, 
            activo, 
            usuarioPeticion, usuarioDictamen, 
            aprobado, fechaHoraDictamen,
            notifyActive
          )        
         VALUES 
         (
            :idProrroga, :idTaller, :numeroCertificado, 
            :replicado, :modificado, :fechaHoraModificacion, :historialModificacion, 
            :dominio, :fechaHoraCreacion, 
            :fundamentacionPeticion,
            :fundamentacionDictamen, 
            :activo, 
            :usuarioPeticion, :usuarioDictamen, 
            :aprobado, :fechaHoraDictamen,
            :notifyActive      
         )";


      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idProrroga', $RP["idProrroga"]);
      $SSQL->bindValue(':idTaller', $RP["idTaller"]);
      $SSQL->bindValue(':numeroCertificado', $RP["numeroCertificado"]);
      $SSQL->bindValue(':replicado', 1);
      $SSQL->bindValue(':modificado', $RP["modificado"]);
      $SSQL->bindValue(':fechaHoraModificacion', $RP["fechaHoraModificacion"]);
      $SSQL->bindValue(':historialModificacion', $RP["historialModificacion"]);
      $SSQL->bindValue(':dominio', $RP["dominio"]);
      $SSQL->bindValue(':fechaHoraCreacion', $RP["fechaHoraCreacion"]);
      $SSQL->bindValue(':fundamentacionPeticion', $RP["fundamentacionPeticion"]);
      $SSQL->bindValue(':fundamentacionDictamen', $RP["fundamentacionDictamen"]);
      $SSQL->bindValue(':activo', $RP["activo"]);
      $SSQL->bindValue(':usuarioPeticion', $RP["usuarioPeticion"]);
      $SSQL->bindValue(':usuarioDictamen', $RP["usuarioDictamen"]);
      $SSQL->bindValue(':aprobado', $RP["aprobado"]);
      $SSQL->bindValue(':fechaHoraDictamen', $RP["fechaHoraDictamen"]);
      $SSQL->bindValue(':notifyActive', $RP["notifyActive"]);


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

      /**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/
      $sql = "UPDATE prorroga SET replicado = 1 WHERE idProrroga = " . $RP["idProrroga"] . " AND idTaller = " . $RP["idTaller"];
      if (!$base->query($sql))
      {
        /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
        $msnlog =
          "Ocurrio un al marcar como replicado una prorroga en taller. - Taller: $nomTaller - idProrroga: " . $RP["idProrroga"];
        $continua = false;
        break;
      }


      $cantProrrogas++;

    } // foreach
  }
  else
  {
    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
    $msnlog = "Ocurrio un error al buscar prorrogas para replicar . Taller: $nomTaller";
    $continua = false;
  }
}

/***** Bajamos las de central *****/
if($continua)
{
  $sql = " SELECT * FROM prorroga WHERE Replicado = 0 AND idTaller = $idTaller";
  $respVer = $dbUNC->query($sql);

  if ($respVer)
  {
    foreach ($respVer as $row)
    {
      $sqlSer =
        "SELECT * FROM prorroga " .
        "WHERE " .
        "  idProrroga = " . $row["idProrroga"] . " AND" .
        "  idTaller = " . $row["idTaller"];

      $respServ = $base->query($sqlSer);

      if ($respServ->rowCount() == 0)
      {
        // Este caso no deberisa existir
        echo "Intento de replicacion de prorroga inexistente. Comunicarse con sistemas. id: " . $row["idProrroga"];
        $continua = false;
        break;
      }


      // update
        $ComandoSQL = "
            UPDATE prorroga 
            SET
              numeroCertificado= :numeroCertificado, 
              replicado= :replicado, 
              modificado= :modificado, 
              fechaHoraModificacion= :fechaHoraModificacion, 
              historialModificacion= :historialModificacion, 
              dominio= :dominio, 
              fechaHoraCreacion= :fechaHoraCreacion, 
              fundamentacionPeticion= :fundamentacionPeticion,
              fundamentacionDictamen= :fundamentacionDictamen, 
              activo= :activo, 
              usuarioPeticion= :usuarioPeticion, 
              usuarioDictamen= :usuarioDictamen, 
              aprobado= :aprobado, 
              fechaHoraDictamen= :fechaHoraDictamen,
              notifyActive= :notifyActive            
            WHERE
               idTaller = :idTaller AND 
               idProrroga = :idProrroga";

      // A ejecutar
      $SSQL = $base->prepare($ComandoSQL);


      $SSQL->bindValue(':idProrroga', $RP["idProrroga"]);
      $SSQL->bindValue(':idTaller', $RP["idTaller"]);
      $SSQL->bindValue(':numeroCertificado', $RP["numeroCertificado"]);
      $SSQL->bindValue(':replicado', 1);
      $SSQL->bindValue(':modificado', $RP["modificado"]);
      $SSQL->bindValue(':fechaHoraModificacion', $RP["fechaHoraModificacion"]);
      $SSQL->bindValue(':historialModificacion', $RP["historialModificacion"]);
      $SSQL->bindValue(':dominio', $RP["dominio"]);
      $SSQL->bindValue(':fechaHoraCreacion', $RP["fechaHoraCreacion"]);
      $SSQL->bindValue(':fundamentacionPeticion', $RP["fundamentacionPeticion"]);
      $SSQL->bindValue(':fundamentacionDictamen', $RP["fundamentacionDictamen"]);
      $SSQL->bindValue(':activo', $RP["activo"]);
      $SSQL->bindValue(':usuarioPeticion', $RP["usuarioPeticion"]);
      $SSQL->bindValue(':usuarioDictamen', $RP["usuarioDictamen"]);
      $SSQL->bindValue(':aprobado', $RP["aprobado"]);
      $SSQL->bindValue(':fechaHoraDictamen', $RP["fechaHoraDictamen"]);
      $SSQL->bindValue(':notifyActive', $RP["notifyActive"]);


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




      //      echo $sqlInSer; exit;
      /**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/
      $sql = "UPDATE prorroga SET Replicado = 1 WHERE idProrroga = " . $row["idProrroga"] . " AND idTaller = " . $row["idTaller"];
      if (!$dbUNC->query($sql))
      {
        /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
        $msnlog = "Ocurrio un al marcar como replicado una prorroga en taller. - Taller: $nomTaller";
        $continua = false;
        break;
      }


      $cantProrrogasD++;

    } // foreach
  }
  else
  {
    $msnlog = "Ocurrio un error al buscar prorrogas en taller. Taller: $nomTaller";
    $continua = false;
  }
} // if($continua)