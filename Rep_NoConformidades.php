<?php

/***************************************************************************
 *
 *				NO CONFORMIDADES
 *
 **************************************************************************/

if($continua)
{
  $sql = "SELECT * FROM noConformidades WHERE Replicado = 0 AND idTaller = $idTaller";

  $respNC = $base->query($sql);

  if ($respNC)
  {
    /***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
    foreach ($respNC as $RNC)
    {
      $ComandoSQL =
        " SELECT COUNT(*) as HM  
        FROM noConformidades
        WHERE
          idTaller = :idTaller AND 
          idNC = :idNC";

      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idNC', $RNC["idNC"]);
      $SSQL->bindValue(':idTaller', $RNC["idTaller"]);

      $Res = $SSQL->execute();  // Para ver si anduvo pero no le doy bola
      $R = $SSQL->fetch();

      $HM = $R["HM"];

      if ($HM == 0)
      {
        $ComandoSQL =
          " INSERT INTO noConformidades
            (
              idNC, dominio, descripcionNC, fechaHora, idTaller, activo, fechaHoraRep
            ) 
            VALUES            
	          ( 
	            :idNC, :dominio, :descripcionNC, :fechaHora, :idTaller, :activo, NOW()
	          )";
      } else
      {
        $ComandoSQL =
          " UPDATE noConformidades
	          SET        		  
		          dominio=:dominio,
		          descripcionNC=:descripcionNC,
		          fechaHora=:fechaHora,		          
		          activo=:activo,
		          fechaHoraRep= NOW()
	          WHERE 
	            idNC=:idNC AND
	            idTaller=:idTaller";
      }

      // A ejecutar
      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idNC', $RNC["idNC"]);
      $SSQL->bindValue(':dominio', $RNC["dominio"]);
      $SSQL->bindValue(':descripcionNC', $RNC["descripcionNC"]);
      $SSQL->bindValue(':fechaHora', $RNC["fechaHora"]);
      $SSQL->bindValue(':idTaller', $RNC["idTaller"]);
      $SSQL->bindValue(':activo', $RNC["activo"]);

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

      // Todo ok, marcamos el replicado

      // Ahora a marcar como replicado
      $ComandoSQL =
        " UPDATE noConformidades 
        SET
          Replicado= 1
          WHERE
            idTaller = :idTaller AND 
            idNC = :idNC";

      $SSQL = $base->prepare($ComandoSQL);

      $SSQL->bindValue(':idNC', $RNC["idNC"]);
      $SSQL->bindValue(':idTaller', $RNC["idTaller"]);

      $Res = $SSQL->execute();
      if (!$Res)
      {
        print_r($SSQL->errorInfo());
        exit();
      } else
        $cantNCs++;

    }
  }
  else
  {
    echo ("Error al obtener No conformidades");
    exit();
  }
}