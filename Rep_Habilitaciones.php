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
  if ($respVer)
  {
    /***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
    foreach ($respVer as $row)
    {

      $idHabilitacion = $row['idHabilitacion'];

      // Acá no hay update mierda
      $ComandoSQL = "DELETE FROM habilitacion WHERE idHabilitacion = " . $idHabilitacion;
      $respVer = $base->query($ComandoSQL);
      if (!$respVer) {
        $msnlog = "Ocurrio un error al guardar las habilitaciones en taller PASO 1.";
        $continua = false;
        break;
      }

      // Armando los servicios
      $sql =
        " SELECT * " .
        " FROM " .
        "   serviciohab SH" .
        "     INNER JOIN serviciostransportehab STH ON" .
        "       SH.idServiciosTransporteHab = STH.idServiciosTransporteHab" .
        " WHERE" .
        "   idhabilitacion = " . $idHabilitacion;

      // a Recorrar y Armar
      $ServiciosHabilitados = "";

      $Servicios = $dbUNC->query($sql);
      if ($Servicios)
      {
        /*   * *** Por cada persona vamos a ver si esta el server => update, si no esta => insert *** */

        foreach ($Servicios as $RowS)
        {
          $ServiciosHabilitados.= $RowS["descripcion"] . ";";
        }

        $ComandoSQL =
          " INSERT INTO habilitacion" .
          " (" .
          "    idHabilitacion, nroCodigoBarrasHab, activo, fechaHoraCreacion, modificado, fechaHoraUltModificacion, " .
          "    historialModificacion, dominio, marcaVehiculo, modeloVehiculo, idLocalidadVehiculo, nombreTitular, " .
          "    apellidoTitular, domicilioTitular, idLocalidadTitular, nombreConductor, apellidoConductor, domicilioConductor, " .
          "    idLocalidadConductor, usuarioDictamen, fechaHoraDictamen, tipoDocTitular, nroDocTitular, tipoPersona, " .
          "     razonSocialTitular, cuitTitular, idTipoServicio, serviciosHabilitados" .
          " )" .
          " VALUES" .
          " (" .
          $idHabilitacion . ", '" . $row['nroCodigoBarrasHab'] . "', " . $row['activo'] . ", '" . $row['fechaHoraCreacion'] . "', " .
          $row['modificado'] . ", '" . $row['fechaHoraUltModificacion'] . "', '" . $row['historialModificacion'] . "', '" .
          $row['dominio'] . "', '" . $row['marcaVehiculo'] . "', '" . $row['modeloVehiculo'] . "', " .
          $row['idLocalidadVehiculo'] . ", '" . $row['nombreTitular'] . "', '" . $row['apellidoTitular'] . "', '" .
          $row['domicilioTitular'] . "', " . $row['idLocalidadTitular'] . ", '" . $row['nombreConductor'] . "', '" .
          $row['apellidoConductor'] . "', '" . $row['domicilioConductor'] . "', " . $row['idLocalidadConductor'] . ", '" .
          $row['usuarioDictamen'] . "', '" . $row['fechaHoraDictamen'] . "', '" . $row['tipoDocTitular'] . "', '" .
          $row['nroDocTitular'] . "', '" . $row['tipoPersona'] . "', '" . $row['razonSocialTitular'] . "', '" .
          $row['cuitTitular'] . "', " . $row['idTipoServicio'] . ", '" . $ServiciosHabilitados . "'" .
          " )";

        // echo $ComandoSQL;exit();
        if (!$base->query($ComandoSQL)) {
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog =
            "Ocurrio un error al replicar una habilitacion - idHabilitacion: " . $row["idHabilitacion"];
          echo $ComandoSQL;
          $continua = false;
          break;
        } else {
          $cantHabilitaciones++;
        }
      }
      else
      {
        $msnlog =
          "Ocurrio un error al obtener los servicios habilitados - idHabilitacion: " . $row["idHabilitacion"];
        echo $ComandoSQL;
        $continua = false;
        break;
      }
    }


  } else {
    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
    $msnlog = "Ocurrio un error al buscar habilitaciones para replicar.";
    $continua = false;
  }
}




