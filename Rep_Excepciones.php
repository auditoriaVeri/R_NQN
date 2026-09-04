<?php

/***** Excepciones. Tienen que subir las nuevas, bajar las de central *****/

// De taller a central
if ($continua)
{
  $sql = " SELECT * FROM excepcion WHERE Replicado = 0 AND idTaller = $idTaller";

  $respVer = $base->query($sql);

  foreach ($respVer as $Excep)
  {
    $ComandoSQL =
      " SELECT COUNT(*) as HM  
        FROM excepcion
        WHERE
          idTaller = :idTaller AND 
          idExcepcion = :idExcepcion";

    $SSQL = $dbUNC->prepare($ComandoSQL);

    $SSQL->bindValue(':idExcepcion', $Excep["idExcepcion"]);
    $SSQL->bindValue(':idTaller', $Excep["idTaller"]);

    $Res = $SSQL->execute();  // Para ver si anduvo pero no le doy bola
    $R = $SSQL->fetch();

    $HM = $R["HM"];

    if ($HM == 0)
    {
      // Hay que preguntar si la excepción ya está.
      $ComandoSQL =
        "INSERT INTO excepcion
         (idExcepcion, replicado, modificado, fechaHoraModificacion, historialModificacion, dominio, marcaVehiculo, modeloVehiculo, idLocalidadVehiculo, nombreTitular, apellidoTitular, domicilioTitular, idLocalidadTitular, nombreConductor, apellidoConductor, domicilioConductor, idLocalidadConductor, fecha, observacion, idTaller, activo, usuario, usuarioDictamen, aprobado, fechaHoraDictamen, observacionDictamen, idCategoria, chasisNro, motorAnio, motorMarca, motorNumero, tipoDocConductor, nroDocConductor, codigoTitular, companiaSeguro, nroPoliza, ultimoRecPatente, idTipoUso, idTipoVehiculo, vAnio, chasisMarca, chasisAnio, tipoCombustible, nroEjes, tipoDocTitular, nroDocTitular, telefonoTitular, tipoPersona, razonSocialTitular, cuitTitular, codigoPJTitular, emailTitular, notifyActive)
        VALUES
        (:idExcepcion, :replicado, :modificado, :fechaHoraModificacion, :historialModificacion, :dominio, :marcaVehiculo, :modeloVehiculo, :idLocalidadVehiculo, :nombreTitular, :apellidoTitular, :domicilioTitular, :idLocalidadTitular, :nombreConductor, :apellidoConductor, :domicilioConductor, :idLocalidadConductor, :fecha, :observacion, :idTaller, :activo, :usuario, :usuarioDictamen, :aprobado, :fechaHoraDictamen, :observacionDictamen, :idCategoria, :chasisNro, :motorAnio, :motorMarca, :motorNumero, :tipoDocConductor, :nroDocConductor, :codigoTitular, :companiaSeguro, :nroPoliza, :ultimoRecPatente, :idTipoUso, :idTipoVehiculo, :vAnio, :chasisMarca, :chasisAnio, :tipoCombustible, :nroEjes, :tipoDocTitular, :nroDocTitular, :telefonoTitular, :tipoPersona, :razonSocialTitular, :cuitTitular, :codigoPJTitular, :emailTitular, :notifyActive)";
    }
    else
    {
      $ComandoSQL=
        "UPDATE excepcion
        SET
          replicado= :replicado, modificado= :modificado, fechaHoraModificacion= :fechaHoraModificacion, 
          historialModificacion= :historialModificacion, dominio= :dominio, marcaVehiculo= :marcaVehiculo, 
          modeloVehiculo= :modeloVehiculo, idLocalidadVehiculo= :idLocalidadVehiculo, 
          nombreTitular= :nombreTitular, apellidoTitular= :apellidoTitular, 
          domicilioTitular= :domicilioTitular, idLocalidadTitular= :idLocalidadTitular, 
          nombreConductor= :nombreConductor, apellidoConductor= :apellidoConductor, 
          domicilioConductor= :domicilioConductor, idLocalidadConductor= :idLocalidadConductor, 
          fecha= :fecha, observacion= :observacion, activo= :activo, usuario= :usuario, 
          usuarioDictamen= :usuarioDictamen, aprobado= :aprobado, fechaHoraDictamen= :fechaHoraDictamen, 
          observacionDictamen= :observacionDictamen, idCategoria= :idCategoria, chasisNro= :chasisNro, 
          motorAnio= :motorAnio, motorMarca= :motorMarca, motorNumero= :motorNumero, 
          tipoDocConductor= :tipoDocConductor, nroDocConductor= :nroDocConductor, 
          codigoTitular= :codigoTitular, companiaSeguro= :companiaSeguro, nroPoliza= :nroPoliza, 
          ultimoRecPatente= :ultimoRecPatente, idTipoUso= :idTipoUso, idTipoVehiculo= :idTipoVehiculo, 
          vAnio= :vAnio, chasisMarca= :chasisMarca, chasisAnio= :chasisAnio, 
          tipoCombustible= :tipoCombustible, nroEjes= :nroEjes, tipoDocTitular= :tipoDocTitular, 
          nroDocTitular= :nroDocTitular, telefonoTitular= :telefonoTitular, tipoPersona= :tipoPersona, 
          razonSocialTitular= :razonSocialTitular, cuitTitular= :cuitTitular, 
          codigoPJTitular= :codigoPJTitular, emailTitular= :emailTitular, notifyActive= :notifyActive, 
          cuitTitular= :cuitTitular, codigoPJTitular= :codigoPJTitular, emailTitular= :emailTitular, 
          notifyActive= :notifyActive
        WHERE
          idTaller = :idTaller AND
          idExcepcion = :idExcepcion";
          
    }

    $SSQL = $dbUNC->prepare($ComandoSQL);

    $SSQL->bindValue(':idExcepcion', $Excep["idExcepcion"]);
    $SSQL->bindValue(':modificado', $Excep["modificado"]);
    $SSQL->bindValue(':fechaHoraModificacion', $Excep["fechaHoraModificacion"]);
    $SSQL->bindValue(':historialModificacion', $Excep["historialModificacion"]);
    $SSQL->bindValue(':dominio', $Excep["dominio"]);
    $SSQL->bindValue(':marcaVehiculo', $Excep["marcaVehiculo"]);
    $SSQL->bindValue(':modeloVehiculo', $Excep["modeloVehiculo"]);
    $SSQL->bindValue(':idLocalidadVehiculo', $Excep["idLocalidadVehiculo"]);
    $SSQL->bindValue(':nombreTitular', $Excep["nombreTitular"]);
    $SSQL->bindValue(':apellidoTitular', $Excep["apellidoTitular"]);
    $SSQL->bindValue(':domicilioTitular', $Excep["domicilioTitular"]);
    $SSQL->bindValue(':idLocalidadTitular', $Excep["idLocalidadTitular"]);
    $SSQL->bindValue(':nombreConductor', $Excep["nombreConductor"]);
    $SSQL->bindValue(':apellidoConductor', $Excep["apellidoConductor"]);
    $SSQL->bindValue(':domicilioConductor', $Excep["domicilioConductor"]);
    $SSQL->bindValue(':idLocalidadConductor', $Excep["idLocalidadConductor"]);
    $SSQL->bindValue(':fecha', $Excep["fecha"]);
    $SSQL->bindValue(':observacion', $Excep["observacion"]);
    $SSQL->bindValue(':idTaller', $Excep["idTaller"]);
    $SSQL->bindValue(':activo', $Excep["activo"]);
    $SSQL->bindValue(':usuario', $Excep["usuario"]);
    $SSQL->bindValue(':usuarioDictamen', $Excep["usuarioDictamen"]);
    $SSQL->bindValue(':aprobado', $Excep["aprobado"]);
    $SSQL->bindValue(':fechaHoraDictamen', $Excep["fechaHoraDictamen"]);
    $SSQL->bindValue(':observacionDictamen', $Excep["observacionDictamen"]);
    $SSQL->bindValue(':idCategoria', $Excep["idCategoria"]);
    $SSQL->bindValue(':chasisNro', $Excep["chasisNro"]);
    $SSQL->bindValue(':motorAnio', $Excep["motorAnio"]);
    $SSQL->bindValue(':motorMarca', $Excep["motorMarca"]);
    $SSQL->bindValue(':motorNumero', $Excep["motorNumero"]);
    $SSQL->bindValue(':tipoDocConductor', $Excep["tipoDocConductor"]);
    $SSQL->bindValue(':nroDocConductor', $Excep["nroDocConductor"]);
    $SSQL->bindValue(':codigoTitular', $Excep["codigoTitular"]);
    $SSQL->bindValue(':companiaSeguro', $Excep["companiaSeguro"]);
    $SSQL->bindValue(':nroPoliza', $Excep["nroPoliza"]);
    $SSQL->bindValue(':ultimoRecPatente', $Excep["ultimoRecPatente"]);
    $SSQL->bindValue(':idTipoUso', $Excep["idTipoUso"]);
    $SSQL->bindValue(':idTipoVehiculo', $Excep["idTipoVehiculo"]);
    $SSQL->bindValue(':vAnio', $Excep["vAnio"]);
    $SSQL->bindValue(':chasisMarca', $Excep["chasisMarca"]);
    $SSQL->bindValue(':chasisAnio', $Excep["chasisAnio"]);
    $SSQL->bindValue(':tipoCombustible', $Excep["tipoCombustible"]);
    $SSQL->bindValue(':nroEjes', $Excep["nroEjes"]);
    $SSQL->bindValue(':tipoDocTitular', $Excep["tipoDocTitular"]);
    $SSQL->bindValue(':nroDocTitular', $Excep["nroDocTitular"]);
    $SSQL->bindValue(':telefonoTitular', $Excep["telefonoTitular"]);
    $SSQL->bindValue(':tipoPersona', $Excep["tipoPersona"]);
    $SSQL->bindValue(':razonSocialTitular', $Excep["razonSocialTitular"]);
    $SSQL->bindValue(':cuitTitular', $Excep["cuitTitular"]);
    $SSQL->bindValue(':codigoPJTitular', $Excep["codigoPJTitular"]);
    $SSQL->bindValue(':emailTitular', $Excep["emailTitular"]);
    $SSQL->bindValue(':notifyActive', $Excep["notifyActive"]);
    $SSQL->bindValue(':replicado', 1);

    try
    {
      $Res = $SSQL->execute();

      if (!$Res)
      {
        print_r($SSQL->errorInfo());
        exit();
      }

      // Ahora a marcar como replicado
      $ComandoSQL =
        " UPDATE excepcion 
        SET
          Replicado= 1
          WHERE
            idTaller = :idTaller AND 
            idExcepcion = :idExcepcion";

      $SSQL = $base->prepare($ComandoSQL);

      $SSQL->bindValue(':idExcepcion', $Excep["idExcepcion"]);
      $SSQL->bindValue(':idTaller', $Excep["idTaller"]);

      $Res = $SSQL->execute();
      if (!$Res)
      {
        print_r($SSQL->errorInfo());
        exit();
      }
      else
        $cantExcepciones++;
    } catch (Exception $e)
    {
      exit("ERROR");
      throw $e;
    }

  }
}

// De central a taller. Todo igual pero medio al reves
if ($continua)
{
  $sql = " SELECT * FROM excepcion WHERE Replicado = 0 AND idTaller = $idTaller";

  $respVer = $dbUNC->query($sql);

  foreach ($respVer as $Excep)
  {
    $ComandoSQL =
      " SELECT COUNT(*) as HM  
        FROM excepcion
        WHERE
          idTaller = :idTaller AND 
          idExcepcion = :idExcepcion";

    $SSQL = $base->prepare($ComandoSQL);

    $SSQL->bindValue(':idExcepcion', $Excep["idExcepcion"]);
    $SSQL->bindValue(':idTaller', $Excep["idTaller"]);

    $Res = $SSQL->execute();  // Para ver si anduvo pero no le doy bola
    $R = $SSQL->fetch();

    $HM = $R["HM"];

    if ($HM == 0)
    {
      // Hay que preguntar si la excepción ya está.
      $ComandoSQL =
        "INSERT INTO excepcion
         (idExcepcion, replicado, modificado, fechaHoraModificacion, historialModificacion, dominio, marcaVehiculo, modeloVehiculo, idLocalidadVehiculo, nombreTitular, apellidoTitular, domicilioTitular, idLocalidadTitular, nombreConductor, apellidoConductor, domicilioConductor, idLocalidadConductor, fecha, observacion, idTaller, activo, usuario, usuarioDictamen, aprobado, fechaHoraDictamen, observacionDictamen, idCategoria, chasisNro, motorAnio, motorMarca, motorNumero, tipoDocConductor, nroDocConductor, codigoTitular, companiaSeguro, nroPoliza, ultimoRecPatente, idTipoUso, idTipoVehiculo, vAnio, chasisMarca, chasisAnio, tipoCombustible, nroEjes, tipoDocTitular, nroDocTitular, telefonoTitular, tipoPersona, razonSocialTitular, cuitTitular, codigoPJTitular, emailTitular, notifyActive)
        VALUES
        (:idExcepcion, :replicado, :modificado, :fechaHoraModificacion, :historialModificacion, :dominio, :marcaVehiculo, :modeloVehiculo, :idLocalidadVehiculo, :nombreTitular, :apellidoTitular, :domicilioTitular, :idLocalidadTitular, :nombreConductor, :apellidoConductor, :domicilioConductor, :idLocalidadConductor, :fecha, :observacion, :idTaller, :activo, :usuario, :usuarioDictamen, :aprobado, :fechaHoraDictamen, :observacionDictamen, :idCategoria, :chasisNro, :motorAnio, :motorMarca, :motorNumero, :tipoDocConductor, :nroDocConductor, :codigoTitular, :companiaSeguro, :nroPoliza, :ultimoRecPatente, :idTipoUso, :idTipoVehiculo, :vAnio, :chasisMarca, :chasisAnio, :tipoCombustible, :nroEjes, :tipoDocTitular, :nroDocTitular, :telefonoTitular, :tipoPersona, :razonSocialTitular, :cuitTitular, :codigoPJTitular, :emailTitular, :notifyActive)";
    }
    else
    {
      $ComandoSQL=
        "UPDATE excepcion
        SET
          replicado= :replicado, modificado= :modificado, fechaHoraModificacion= :fechaHoraModificacion, 
          historialModificacion= :historialModificacion, dominio= :dominio, marcaVehiculo= :marcaVehiculo, 
          modeloVehiculo= :modeloVehiculo, idLocalidadVehiculo= :idLocalidadVehiculo, 
          nombreTitular= :nombreTitular, apellidoTitular= :apellidoTitular, 
          domicilioTitular= :domicilioTitular, idLocalidadTitular= :idLocalidadTitular, 
          nombreConductor= :nombreConductor, apellidoConductor= :apellidoConductor, 
          domicilioConductor= :domicilioConductor, idLocalidadConductor= :idLocalidadConductor, 
          fecha= :fecha, observacion= :observacion, activo= :activo, usuario= :usuario, 
          usuarioDictamen= :usuarioDictamen, aprobado= :aprobado, fechaHoraDictamen= :fechaHoraDictamen, 
          observacionDictamen= :observacionDictamen, idCategoria= :idCategoria, chasisNro= :chasisNro, 
          motorAnio= :motorAnio, motorMarca= :motorMarca, motorNumero= :motorNumero, 
          tipoDocConductor= :tipoDocConductor, nroDocConductor= :nroDocConductor, 
          codigoTitular= :codigoTitular, companiaSeguro= :companiaSeguro, nroPoliza= :nroPoliza, 
          ultimoRecPatente= :ultimoRecPatente, idTipoUso= :idTipoUso, idTipoVehiculo= :idTipoVehiculo, 
          vAnio= :vAnio, chasisMarca= :chasisMarca, chasisAnio= :chasisAnio, 
          tipoCombustible= :tipoCombustible, nroEjes= :nroEjes, tipoDocTitular= :tipoDocTitular, 
          nroDocTitular= :nroDocTitular, telefonoTitular= :telefonoTitular, tipoPersona= :tipoPersona, 
          razonSocialTitular= :razonSocialTitular, cuitTitular= :cuitTitular, 
          codigoPJTitular= :codigoPJTitular, emailTitular= :emailTitular, notifyActive= :notifyActive, 
          cuitTitular= :cuitTitular, codigoPJTitular= :codigoPJTitular, emailTitular= :emailTitular, 
          notifyActive= :notifyActive
        WHERE
          idTaller = :idTaller AND
          idExcepcion = :idExcepcion";

    }

    $SSQL = $base->prepare($ComandoSQL);

    $SSQL->bindValue(':idExcepcion', $Excep["idExcepcion"]);
    $SSQL->bindValue(':modificado', $Excep["modificado"]);
    $SSQL->bindValue(':fechaHoraModificacion', $Excep["fechaHoraModificacion"]);
    $SSQL->bindValue(':historialModificacion', $Excep["historialModificacion"]);
    $SSQL->bindValue(':dominio', $Excep["dominio"]);
    $SSQL->bindValue(':marcaVehiculo', $Excep["marcaVehiculo"]);
    $SSQL->bindValue(':modeloVehiculo', $Excep["modeloVehiculo"]);
    $SSQL->bindValue(':idLocalidadVehiculo', $Excep["idLocalidadVehiculo"]);
    $SSQL->bindValue(':nombreTitular', $Excep["nombreTitular"]);
    $SSQL->bindValue(':apellidoTitular', $Excep["apellidoTitular"]);
    $SSQL->bindValue(':domicilioTitular', $Excep["domicilioTitular"]);
    $SSQL->bindValue(':idLocalidadTitular', $Excep["idLocalidadTitular"]);
    $SSQL->bindValue(':nombreConductor', $Excep["nombreConductor"]);
    $SSQL->bindValue(':apellidoConductor', $Excep["apellidoConductor"]);
    $SSQL->bindValue(':domicilioConductor', $Excep["domicilioConductor"]);
    $SSQL->bindValue(':idLocalidadConductor', $Excep["idLocalidadConductor"]);
    $SSQL->bindValue(':fecha', $Excep["fecha"]);
    $SSQL->bindValue(':observacion', $Excep["observacion"]);
    $SSQL->bindValue(':idTaller', $Excep["idTaller"]);
    $SSQL->bindValue(':activo', $Excep["activo"]);
    $SSQL->bindValue(':usuario', $Excep["usuario"]);
    $SSQL->bindValue(':usuarioDictamen', $Excep["usuarioDictamen"]);
    $SSQL->bindValue(':aprobado', $Excep["aprobado"]);
    $SSQL->bindValue(':fechaHoraDictamen', $Excep["fechaHoraDictamen"]);
    $SSQL->bindValue(':observacionDictamen', $Excep["observacionDictamen"]);
    $SSQL->bindValue(':idCategoria', $Excep["idCategoria"]);
    $SSQL->bindValue(':chasisNro', $Excep["chasisNro"]);
    $SSQL->bindValue(':motorAnio', $Excep["motorAnio"]);
    $SSQL->bindValue(':motorMarca', $Excep["motorMarca"]);
    $SSQL->bindValue(':motorNumero', $Excep["motorNumero"]);
    $SSQL->bindValue(':tipoDocConductor', $Excep["tipoDocConductor"]);
    $SSQL->bindValue(':nroDocConductor', $Excep["nroDocConductor"]);
    $SSQL->bindValue(':codigoTitular', $Excep["codigoTitular"]);
    $SSQL->bindValue(':companiaSeguro', $Excep["companiaSeguro"]);
    $SSQL->bindValue(':nroPoliza', $Excep["nroPoliza"]);
    $SSQL->bindValue(':ultimoRecPatente', $Excep["ultimoRecPatente"]);
    $SSQL->bindValue(':idTipoUso', $Excep["idTipoUso"]);
    $SSQL->bindValue(':idTipoVehiculo', $Excep["idTipoVehiculo"]);
    $SSQL->bindValue(':vAnio', $Excep["vAnio"]);
    $SSQL->bindValue(':chasisMarca', $Excep["chasisMarca"]);
    $SSQL->bindValue(':chasisAnio', $Excep["chasisAnio"]);
    $SSQL->bindValue(':tipoCombustible', $Excep["tipoCombustible"]);
    $SSQL->bindValue(':nroEjes', $Excep["nroEjes"]);
    $SSQL->bindValue(':tipoDocTitular', $Excep["tipoDocTitular"]);
    $SSQL->bindValue(':nroDocTitular', $Excep["nroDocTitular"]);
    $SSQL->bindValue(':telefonoTitular', $Excep["telefonoTitular"]);
    $SSQL->bindValue(':tipoPersona', $Excep["tipoPersona"]);
    $SSQL->bindValue(':razonSocialTitular', $Excep["razonSocialTitular"]);
    $SSQL->bindValue(':cuitTitular', $Excep["cuitTitular"]);
    $SSQL->bindValue(':codigoPJTitular', $Excep["codigoPJTitular"]);
    $SSQL->bindValue(':emailTitular', $Excep["emailTitular"]);
    $SSQL->bindValue(':notifyActive', $Excep["notifyActive"]);
    $SSQL->bindValue(':replicado', 1);

    try
    {
      $Res = $SSQL->execute();

      if (!$Res)
      {
        print_r($SSQL->errorInfo());
        exit();
      }

      // Ahora a marcar como replicado
      $ComandoSQL =
        " UPDATE excepcion 
        SET
          Replicado= 1
          WHERE
            idTaller = :idTaller AND 
            idExcepcion = :idExcepcion";

      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idExcepcion', $Excep["idExcepcion"]);
      $SSQL->bindValue(':idTaller', $Excep["idTaller"]);

      $Res = $SSQL->execute();
      if (!$Res)
      {
        print_r($SSQL->errorInfo());
        exit();
      }
      else
        $cantExcepciones++;
    } catch (Exception $e)
    {
      exit("ERROR");
      throw $e;
    }

  }
}