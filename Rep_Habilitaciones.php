<?php

/***** Excepciones. Tienen que subir las nuevas, bajar las de central *****/


if($continua)
{
  // Obtengo lo último qie tengo
  $sql= " SELECT MAX(fechaHoraUltModificacion) as MaxFH FROM habilitacion";
  $respVer = $base->query($sql);
  if($respVer)
  {
     $row = $respVer->fetch(PDO::FETCH_ASSOC);
     $MaxFH= $row['MaxFH'];
  }
  else
  {
    $msnlog = "Ocurrio un error al obtener las habilitaciones en central.";
		$continua = false;
  }
}
  

if ($continua)  
{
  // Obtengo todo lo que sea nuevo de central
	$sql = " SELECT * FROM habilitacion WHERE fechaHoraUltModificacion > '$MaxFH'";
	$respVer = $dbUNC->query($sql);
  if($respVer)
  {
		/***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{		
      // Acá no hay update mierda
      $ComandoSQL= "DELETE FROM habilitacion WHERE idHabilitacion = " . $row['idHabilitacion'];
      $respVer = $base->query($sql);
      if (!$respVer)
      {
        $msnlog = "Ocurrio un error al guardar las habilitaciones en taller PASO 1.";
        $continua = false;        
        break;
      }
        
      $ComandoSQL=
        " INSERT INTO habilitacion" .
        " (" . 
        "    idHabilitacion, nroCodigoBarrasHab, activo, fechaHoraCreacion, modificado, fechaHoraUltModificacion, " .
        "    historialModificacion, dominio, marcaVehiculo, modeloVehiculo, idLocalidadVehiculo, nombreTitular, " . 
        "    apellidoTitular, domicilioTitular, idLocalidadTitular, nombreConductor, apellidoConductor, domicilioConductor, " .
        "    idLocalidadConductor, usuarioDictamen, fechaHoraDictamen, tipoDocTitular, nroDocTitular, tipoPersona, " . 
        "     razonSocialTitular, cuitTitular, idTipoServicio" .
        " )" . 
        " VALUES" . 
        " (" . 
        $row['idHabilitacion'] . ", '" . $row['nroCodigoBarrasHab'] . "', " . $row['activo'] . ", '" . $row['fechaHoraCreacion'] . "', " .
        $row['modificado'] . ", '" . $row['fechaHoraUltModificacion'] . "', '" . $row['historialModificacion'] . "', '" .
        $row['dominio'] . "', '" . $row['marcaVehiculo'] . "', '" . $row['modeloVehiculo'] . "', " . 
        $row['idLocalidadVehiculo'] . ", '" . $row['nombreTitular'] . "', '" . $row['apellidoTitular'] . "', '" .
        $row['domicilioTitular'] . "', " . $row['idLocalidadTitular'] . ", '" . $row['nombreConductor'] . "', '" .
        $row['apellidoConductor'] . "', '" . $row['domicilioConductor'] .  "', "  . $row['idLocalidadConductor'] . ", '" . 
        $row['usuarioDictamen'] . "', '" . $row['fechaHoraDictamen'] . "', '". $row['tipoDocTitular'] . "', '" .
        $row['nroDocTitular'] . "', '" . $row['tipoPersona'] . "', '" . $row['razonSocialTitular'] . "', '" . 
        $row['cuitTitular'] . "', " . $row['idTipoServicio'] .
        " )";
      
     // echo $ComandoSQL;exit();
      if(!$base->query($ComandoSQL))
      {
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = 
            "Ocurrio un error al replicar una habilitacion - idHabilitacion: " . $row["idHabilitacion"];
          echo $ComandoSQL;
          $continua = false;
          break;
      }
      else
      {
        $cantHabilitaciones++;
      }
    }
  }
  else
  {
    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
    $msnlog = "Ocurrio un error al buscar habilitaciones para replicar.";
    $continua = false;
  }
}
 



