<?php

/***************************************************************************
 *
 *				Fotovalidaciones
 *
 **************************************************************************/
if($continua)
{
  $sql = "SELECT * FROM fotovalidacionPatente WHERE Replicado = 0 AND idTaller = $idTaller";

  $respPV= $base->query($sql);

  if ($respPV)
  {
    /***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
    foreach ($respPV as $RFV)
    {
      $ComandoSQL =
        " SELECT COUNT(*) as HM  
        FROM fotovalidacionPatente
        WHERE
          idTaller = :idTaller AND 
          idFotovalidacion = :idFotovalidacion";

      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idFotovalidacion', $RFV["idFotovalidacion"]);
      $SSQL->bindValue(':idTaller', $RFV["idTaller"]);

      $Res= $SSQL->execute();  // Para ver si anduvo pero no le doy bola
      $R = $SSQL->fetch();

      $HM = $R["HM"];

      if ($HM == 0)
      {
        $ComandoSQL =
          "INSERT INTO fotovalidacionPatente
           (
             idFotovalidacion, dominio, fechaHora, codigo, resultado, path, idTaller, 
             output, activo, fechaHoraRep
           )
           VALUES 
           (
              :idFotovalidacion, :dominio, :fechaHora, :codigo, :resultado, :path, :idTaller, 
              :output, :activo, NOW()           
           )";
      }
      else
      {
        $ComandoSQL =
          "UPDATE fotovalidacionPatente
            SET
              dominio= :dominio, 
              fechaHora= :fechaHora, 
              codigo= :codigo, 
              resultado= :resultado, 
              path= :path,              
              output= :output, 
              activo= :activo, 
              fechaHoraRep= NOW()
            WHERE 
               idFotovalidacion=:idFotovalidacion AND
               idTaller = :idTaller";
      }

      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idFotovalidacion', $RFV["idFotovalidacion"]);
      $SSQL->bindValue(':idTaller', $RFV["idTaller"]);
      $SSQL->bindValue(':dominio', $RFV["dominio"]);
      $SSQL->bindValue(':fechaHora', $RFV["fechaHora"]);
      $SSQL->bindValue(':codigo', $RFV["codigo"]);
      $SSQL->bindValue(':resultado', $RFV["resultado"]);
      $SSQL->bindValue(':path', $RFV["path"]);
      $SSQL->bindValue(':output', $RFV["output"]);
      $SSQL->bindValue(':activo', $RFV["activo"]);

      try
      {
        $Res = $SSQL->execute();

        if (!$Res) {
          print_r($SSQL->errorInfo());
          exit();
        }
      }
      catch (Exception $e)
      {
        $continua = false;
        exit("ERROR");
        throw $e;
      }

      // Ahora a marcar como replicado
      $ComandoSQL=
        " UPDATE fotovalidacionPatente 
          SET
            Replicado= 1
          WHERE
            idTaller = :idTaller AND 
            idFotovalidacion = :idFotovalidacion";

      $SSQL = $base->prepare($ComandoSQL);

      $SSQL->bindValue(':idFotovalidacion', $RFV["idFotovalidacion"]);
      $SSQL->bindValue(':idTaller', $RFV["idTaller"]);

      $Res= $SSQL->execute();
      if (!$Res) {
        print_r($SSQL->errorInfo());
        exit();
      }
      else
        $cantFotoValidaciones++;
    }
  }
}
