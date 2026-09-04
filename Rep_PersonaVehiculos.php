<?php

require_once('PDOConfig.php');
// require_once("sftpFunc.php");
require_once('utilidades.php');
$mensaje = "";
$msnlog = "";

/* Para que nos e corte la ejecucion por timeout */
set_time_limit(0);

function SubirArchivo($archivo_local, $archivo_remoto) {
  return true;
}

function ArchivoExistente($archivo_remoto)
{
	return false;
}

try 
{
  $dbUNC = new PDO('mysql:host=vtvunc.ddns.net;dbname=vehicularunc;charset=utf8', 'usrRem', 'rtovtv*');

} catch (Exception $e) {
  echo $e->getMessage();
  exit();
}

//$dbUNC = new PDO('mysql:host=localhost;dbname=vehicularunc;charset=utf8', 'root', '');
$base = new PDOConfig();
$idTaller = 18;
$nomTaller = "Control SRL";
$usu = "replicación automatica";
$cantPaC = 0;
$cantVaC = 0;
$cantPaT = 0;
$cantVaT = 0;
$cantVeraC = 0;
$cantCeraC = 0;
$cantEquiposAC = 0;
$cantMantEqAC = 0;
$cantAudAC = 0;
$cantDefectos= 0;
$actTaller = 0;
$actDT = 0;
$actINS = 0;
$cantHabilitaciones= 0;
$cantEmpresasCCCF= 0;
$cantCertificadosCCCF= 0;
$obleasEnv = 0;
$obleasRec = 0;
$cantNCs= 0;
$cantExcepciones= 0;
$cantPendientes= 0;
$cantPendientesDefectos= 0;
$cantAdjuntosPendientes= 0;
$cantPendientesServicios= 0;
$cantFotoValidaciones= 0;
$cantAdjuntosExcepciones= 0;
$cantVerificacionesServicios= 0;
$cantVerificacionesPDF= 0;



$msnlog = "Inicia Replicaci&oacute;n - Taller: $nomTaller. (" . Date('d/m/Y H:i') . ")";
echo $msnlog;

$sqlRep = " INSERT INTO replicacionlogs(idTaller,Usuario,FechaHora,Observaciones,Exito) VALUES 
								($idTaller,'$usu',NOW(),'$msnlog',1)";
$respUNC = $dbUNC->query($sqlRep);
$respUNC = $base->query($sqlRep);
$continua = true;
$UploadsBasePath= "/var/www/html/taller/uploads/";

/* * *************************** EXPORTAR DEL TALLER AL SERVER ******************** */
/* * ************* Vamos a buscar los datos del taller que deberiamos replicar ************* */

/* * *** buscamos en el taller las persona que no se han replicado *** */
$sql = " SELECT * FROM personas WHERE Replicado = 0 ";
$respPersonas = $base->query($sql);
if ($respPersonas) {
  /*   * *** Por cada persona vamos a ver si esta el server => update, si no esta => insert *** */

  foreach ($respPersonas as $row) {
    $sqlSer = "SELECT * FROM personas WHERE CodigoTitular = '" . $row["CodigoTitular"] . "' ";
    if ($respServ = $dbUNC->query($sqlSer)) {
      if ($row["NroDoc"] == "")
        $row["NroDoc"] = 'NULL';
      if ($row["idLocalidad"] == "")
        $row["idLocalidad"] = 'NULL';
      if ($respServ->rowCount() > 0) {
        $sqlInSer = "UPDATE personas SET TipoDoc = '" . $row["TipoDoc"] . "',NroDoc = " . $row["NroDoc"] . ",
                                              Cuit = '" . $row["Cuit"] . "',Apellido = '" . addslashes($row["Apellido"]) . "',
                                              Nombre = '" . addslashes($row["Nombre"]) . "',Domicilio = '" . addslashes($row["Domicilio"]) . "',
                                              Telefono = '" . addslashes($row["Telefono"]) . "',RazonSocial = '" . addslashes($row["RazonSocial"]) . "',
                                              TipoPersona = '" . $row["TipoPersona"] . "',Email = '" . addslashes($row["Email"]) . "',
                                              idLocalidad = " . $row["idLocalidad"] . ", FechaHoraServ = NOW(),
                                              CodigoPJ = '" . $row["CodigoPJ"] . "'
                                      WHERE CodigoTitular = '" . $row["CodigoTitular"] . "'";
      } else {
        $sqlInSer = "INSERT INTO personas(CodigoTitular,TipoDoc,NroDoc,Cuit,Apellido,Nombre,Domicilio,
							Telefono,RazonSocial,TipoPersona,Email,idLocalidad,FechaHoraServ,CodigoPJ) VALUES
							('" . $row["CodigoTitular"] . "','" . $row["TipoDoc"] . "'," . $row["NroDoc"] . ",'" . $row["Cuit"] . "',
							'" . addslashes($row["Apellido"]) . "','" . addslashes($row["Nombre"]) . "','" . addslashes($row["Domicilio"]) . "','" . addslashes($row["Telefono"]) . "',
							'" . addslashes($row["RazonSocial"]) . "','" . $row["TipoPersona"] . "','" . addslashes($row["Email"]) . "','" . $row["idLocalidad"] .
            "',NOW(),'" . $row["CodigoPJ"] . "')";
      }
      //echo $sqlInSer."<br />";
      if ($dbUNC->query($sqlInSer)) {
        /*         * ** una vez que cargue en server, le indico en el taller que ya fue replicado *** */
        $sql = "UPDATE personas SET Replicado = 1 WHERE CodigoTitular = '" . $row["CodigoTitular"] . "'";
        if (!$base->query($sql)) {
          /*           * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
          $msnlog = "Ocurrio un al marcar como replicado una persona en taller. - Taller: $nomTaller";
          $continua = false;
          break;
        } else {
          $cantPaC++;
        }
      } else {
        /*         * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
        $msnlog = "Ocurrio un error al cargar las personas en central. Taller: $nomTaller";
        $continua = false;
        break;
      }
    } else {
      /*       * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
      $msnlog = "Ocurrio un error al buscar personas en central.";
      $continua = false;
      break;
    }
  }/*   * ******* Cierra el foreach ****************** */
} else {
  $msnlog = "Error al intentar leer los datos de las personas en la base del Taller: $nomTaller";
  $continua = false;
}


/* * *****************SI TERMINAMOS DE CARGAR LAS PERSONAS CORRECTAMENTE ************************ */
if ($continua) {
  /*   * *** buscamos en el taller los vehivulos que no se han replicado *** */
  $sql = " SELECT * FROM vehiculos WHERE Replicado = 0 ";
  $respVehiculos = $base->query($sql);

  if ($respVehiculos)
  {
    foreach ($respVehiculos as $rowV)
    {
      /*       * *** Por cada vehiculo vamos a ver si esta el server => update, si no esta => insert *** */
      $sqlSer = "SELECT * FROM vehiculos WHERE Dominio = '" . $rowV["Dominio"] . "' ";

      $respServ = $dbUNC->query($sqlSer); // sin el if de control, no lo veo necesario

      if ($respServ->rowCount() == 0)
      {
        $ComandoSQL =
          "INSERT INTO vehiculos
            (Dominio, idTipoVehiculo, Marca, Modelo, Anio, idLocalidad, MotorMarca, MotorNro, MotorAnio, ChasisMarca, ChasisNro, ChasisAnio, TacografoMarca, TacografoNro, TacografoRodado, CCCF, TipoCombustible, Pot, NroEjes, idTipoUso, Caja, PocisionMotor, AnioFabricacion, Carroceria, Expediente, AireAco, Bar, Banio, Calefaccion, Suspencion, Tara, PesoMax, CargaUtil, Asientos, CodigoTitular, idLocTMServ, TipoServTM, idTipoServicio, tiposServicios, idHabilitacion, codigoHabilitacion, idClaseServicio, prestadorServ, CuitPrestServ, NroInterno, CompaniaSeguro, NroPoliza, UltimoRecPatente, idCategoria, FechaHoraServ, TipoCarga, CertificadoDiscapacidad, PatenteMercosur, TipoDocConductor, NroDocConductor, NombreConductor, ApellidoConductor, DomicilioConductor, LocalidadConductor, FechaActualizacion, arTarjetaVerde, esReverificacion, idVerificacionOriginal, Status, idTallerVerif, nroFactura, Activo)
          VALUES
              (:Dominio, :idTipoVehiculo, :Marca, :Modelo, :Anio, :idLocalidad, :MotorMarca, :MotorNro, :MotorAnio, :ChasisMarca, :ChasisNro, :ChasisAnio, :TacografoMarca, :TacografoNro, :TacografoRodado, :CCCF, :TipoCombustible, :Pot, :NroEjes, :idTipoUso, :Caja, :PocisionMotor, :AnioFabricacion, :Carroceria, :Expediente, :AireAco, :Bar, :Banio, :Calefaccion, :Suspencion, :Tara, :PesoMax, :CargaUtil, :Asientos, :CodigoTitular, :idLocTMServ, :TipoServTM, :idTipoServicio, :tiposServicios, :idHabilitacion, :codigoHabilitacion, :idClaseServicio, :prestadorServ, :CuitPrestServ, :NroInterno, :CompaniaSeguro, :NroPoliza, :UltimoRecPatente, :idCategoria, NOW(), :TipoCarga, :CertificadoDiscapacidad, :PatenteMercosur, :TipoDocConductor, :NroDocConductor, :NombreConductor, :ApellidoConductor, :DomicilioConductor, :LocalidadConductor, :FechaActualizacion, :arTarjetaVerde, :esReverificacion, :idVerificacionOriginal, :Status, :idTallerVerif, :nroFactura, :Activo)";
      }
      else
      {
        $ComandoSQL = "
          UPDATE vehiculos
          SET
            idTipoVehiculo= :idTipoVehiculo, Marca= :Marca, Modelo= :Modelo, Anio= :Anio, idLocalidad= :idLocalidad, MotorMarca= :MotorMarca, MotorNro= :MotorNro, MotorAnio= :MotorAnio, ChasisMarca= :ChasisMarca, ChasisNro= :ChasisNro, ChasisAnio= :ChasisAnio, TacografoMarca= :TacografoMarca, TacografoNro= :TacografoNro, TacografoRodado= :TacografoRodado, CCCF= :CCCF, TipoCombustible= :TipoCombustible, Pot= :Pot, NroEjes= :NroEjes, idTipoUso= :idTipoUso, Caja= :Caja, PocisionMotor= :PocisionMotor, AnioFabricacion= :AnioFabricacion, Carroceria= :Carroceria, Expediente= :Expediente, AireAco= :AireAco, Bar= :Bar, Banio= :Banio, Calefaccion= :Calefaccion, Suspencion= :Suspencion, Tara= :Tara, PesoMax= :PesoMax, CargaUtil= :CargaUtil, Asientos= :Asientos, CodigoTitular= :CodigoTitular, idLocTMServ= :idLocTMServ, TipoServTM= :TipoServTM, idTipoServicio= :idTipoServicio, tiposServicios= :tiposServicios, idHabilitacion= :idHabilitacion, codigoHabilitacion= :codigoHabilitacion, idClaseServicio= :idClaseServicio, prestadorServ= :prestadorServ, CuitPrestServ= :CuitPrestServ, NroInterno= :NroInterno, CompaniaSeguro= :CompaniaSeguro, NroPoliza= :NroPoliza, UltimoRecPatente= :UltimoRecPatente, idCategoria= :idCategoria, FechaHoraServ= NOW(), TipoCarga= :TipoCarga, CertificadoDiscapacidad= :CertificadoDiscapacidad, PatenteMercosur= :PatenteMercosur, TipoDocConductor= :TipoDocConductor, NroDocConductor= :NroDocConductor, NombreConductor= :NombreConductor, ApellidoConductor= :ApellidoConductor, DomicilioConductor= :DomicilioConductor, LocalidadConductor= :LocalidadConductor, FechaActualizacion= :FechaActualizacion, arTarjetaVerde= :arTarjetaVerde, esReverificacion= :esReverificacion, idVerificacionOriginal= :idVerificacionOriginal, Status= :Status, idTallerVerif= :idTallerVerif, nroFactura= :nroFactura, Activo= :Activo
          WHERE
            Dominio= :Dominio";
      }

      $SSQL = $dbUNC->prepare($ComandoSQL);


      // Binds
      //<editor-fold desc="Binds">
      $SSQL->bindValue(':Dominio', $rowV["Dominio"]);
      $SSQL->bindValue(':idTipoVehiculo', $rowV["idTipoVehiculo"]);
      $SSQL->bindValue(':Marca', $rowV["Marca"]);
      $SSQL->bindValue(':Modelo', $rowV["Modelo"]);
      $SSQL->bindValue(':Anio', $rowV["Anio"]);
      $SSQL->bindValue(':idLocalidad', $rowV["idLocalidad"]);
      $SSQL->bindValue(':MotorMarca', $rowV["MotorMarca"]);
      $SSQL->bindValue(':MotorNro', $rowV["MotorNro"]);
      $SSQL->bindValue(':MotorAnio', $rowV["MotorAnio"]);
      $SSQL->bindValue(':ChasisMarca', $rowV["ChasisMarca"]);
      $SSQL->bindValue(':ChasisNro', $rowV["ChasisNro"]);
      $SSQL->bindValue(':ChasisAnio', $rowV["ChasisAnio"]);
      $SSQL->bindValue(':TacografoMarca', $rowV["TacografoMarca"]);
      $SSQL->bindValue(':TacografoNro', $rowV["TacografoNro"]);
      $SSQL->bindValue(':TacografoRodado', $rowV["TacografoRodado"]);
      $SSQL->bindValue(':CCCF', $rowV["CCCF"]);
      $SSQL->bindValue(':TipoCombustible', $rowV["TipoCombustible"]);
      $SSQL->bindValue(':Pot', $rowV["Pot"]);
      $SSQL->bindValue(':NroEjes', $rowV["NroEjes"]);
      $SSQL->bindValue(':idTipoUso', $rowV["idTipoUso"]);
      $SSQL->bindValue(':Caja', $rowV["Caja"]);
      $SSQL->bindValue(':PocisionMotor', $rowV["PocisionMotor"]);
      $SSQL->bindValue(':AnioFabricacion', $rowV["AnioFabricacion"]);
      $SSQL->bindValue(':Carroceria', $rowV["Carroceria"]);
      $SSQL->bindValue(':Expediente', $rowV["Expediente"]);
      $SSQL->bindValue(':AireAco', $rowV["AireAco"]);
      $SSQL->bindValue(':Bar', $rowV["Bar"]);
      $SSQL->bindValue(':Banio', $rowV["Banio"]);
      $SSQL->bindValue(':Calefaccion', $rowV["Calefaccion"]);
      $SSQL->bindValue(':Suspencion', $rowV["Suspencion"]);
      $SSQL->bindValue(':Tara', $rowV["Tara"]);
      $SSQL->bindValue(':PesoMax', $rowV["PesoMax"]);
      $SSQL->bindValue(':CargaUtil', $rowV["CargaUtil"]);
      $SSQL->bindValue(':Asientos', $rowV["Asientos"]);
      $SSQL->bindValue(':CodigoTitular', $rowV["CodigoTitular"]);
      $SSQL->bindValue(':idLocTMServ', $rowV["idLocTMServ"]);
      $SSQL->bindValue(':TipoServTM', $rowV["TipoServTM"]);
      $SSQL->bindValue(':idTipoServicio', $rowV["idTipoServicio"]);
      $SSQL->bindValue(':tiposServicios', $rowV["tiposServicios"]);
      $SSQL->bindValue(':idHabilitacion', $rowV["idHabilitacion"]);
      $SSQL->bindValue(':codigoHabilitacion', $rowV["codigoHabilitacion"]);
      $SSQL->bindValue(':idClaseServicio', $rowV["idClaseServicio"]);
      $SSQL->bindValue(':prestadorServ', $rowV["prestadorServ"]);
      $SSQL->bindValue(':CuitPrestServ', $rowV["CuitPrestServ"]);
      $SSQL->bindValue(':NroInterno', $rowV["NroInterno"]);
      $SSQL->bindValue(':CompaniaSeguro', $rowV["CompaniaSeguro"]);
      $SSQL->bindValue(':NroPoliza', $rowV["NroPoliza"]);
      $SSQL->bindValue(':UltimoRecPatente', $rowV["UltimoRecPatente"]);
      $SSQL->bindValue(':idCategoria', $rowV["idCategoria"]);
      $SSQL->bindValue(':TipoCarga', $rowV["TipoCarga"]);
      $SSQL->bindValue(':CertificadoDiscapacidad', $rowV["CertificadoDiscapacidad"]);
      $SSQL->bindValue(':PatenteMercosur', $rowV["PatenteMercosur"]);
      $SSQL->bindValue(':TipoDocConductor', $rowV["TipoDocConductor"]);
      $SSQL->bindValue(':NroDocConductor', $rowV["NroDocConductor"]);
      $SSQL->bindValue(':NombreConductor', $rowV["NombreConductor"]);
      $SSQL->bindValue(':ApellidoConductor', $rowV["ApellidoConductor"]);
      $SSQL->bindValue(':DomicilioConductor', '');
      $SSQL->bindValue(':LocalidadConductor', 0);
      $SSQL->bindValue(':FechaActualizacion', $rowV["FechaActualizacion"]);
      $SSQL->bindValue(':arTarjetaVerde', $rowV["arTarjetaVerde"]);
      $SSQL->bindValue(':esReverificacion', $rowV["esReverificacion"]);
      $SSQL->bindValue(':idVerificacionOriginal', $rowV["idVerificacionOriginal"]);
      $SSQL->bindValue(':Status', $rowV["Status"]);
      $SSQL->bindValue(':idTallerVerif', $rowV["idTallerVerif"]);
      $SSQL->bindValue(':nroFactura', $rowV["nroFactura"]);
      $SSQL->bindValue(':Activo', $rowV["Activo"]);
      //</editor-fold>

      try
      {
        $Res = $SSQL->execute();

        if (!$Res)
        {
          echo "Error al replicar vehiculo " . $rowV["Dominio"] . "<br />";

          print_r($SSQL->errorInfo());
          exit();
        }

        // Ahora a marcar como replicado
        $ComandoSQL = " 
          UPDATE vehiculos
          SET
            Replicado= 1
          WHERE
            Dominio = :Dominio";

        $SSQL = $base->prepare($ComandoSQL);

        $SSQL->bindValue(':Dominio', $rowV["Dominio"]);

        $Res = $SSQL->execute();


        if (!$Res)
        {

          print_r($SSQL->errorInfo());
          exit();
        }


        $cantVaC++;
      } catch (Exception $e)
      {
        exit("ERROR");
        throw $e;
      }
    }

  }
  else
  {
    /*     * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
    $msnlog = "Error al intentar leer los datos de los vehiculos en la base del Taller: $nomTaller";
    $continua = false;
  }
}


/* * **************************IMPORTAR DESDE EL SERVER AL TALLER*********************** */
if ($continua) {
  /*   * ************* Ahora buscamos los datos en el server que se deben actualizar en el taller ************* */
  $sql = " SELECT MAX(FechaHoraServ) as FechaMax FROM personas ";
  $respPers = $base->query($sql);
  if ($respPers) {
    $rowF = $respPers->fetch(PDO::FETCH_ASSOC);
    $fechaBuscar = $rowF["FechaMax"];
    /* if($rowF["FechaMax"] != ""){
      $fechaBuscar = substr($fechaBuscar,0,10);
      } */

    $sqlCoSer = " SELECT * FROM personas WHERE FechaHoraServ > '" . $fechaBuscar . "' ORDER BY FechaHoraServ";
    //echo $sqlCoSer;
    $respPerServer = $dbUNC->query($sqlCoSer);
    if ($respPerServer) {

      foreach ($respPerServer as $row) {
        $sqlCoTa = "SELECT * FROM personas WHERE CodigoTitular = '" . $row["CodigoTitular"] . "' ";
        if ($respCoTa = $base->query($sqlCoTa)) {
          if ($row["NroDoc"] == "")
            $row["NroDoc"] = 'NULL';
          if ($row["idLocalidad"] == "")
            $row["idLocalidad"] = 'NULL';
          if ($respCoTa->rowCount() > 0) {
            $sqlInTa = "UPDATE personas SET TipoDoc = '" . $row["TipoDoc"] . "',NroDoc = " . $row["NroDoc"] . ",
                      Cuit = '" . $row["Cuit"] . "',Apellido = '" . addslashes($row["Apellido"]) . "',
                      Nombre = '" . addslashes($row["Nombre"]) . "',Domicilio = '" . addslashes($row["Domicilio"]) . "',
                      Telefono = '" . addslashes($row["Telefono"]) . "',RazonSocial = '" . addslashes($row["RazonSocial"]) . "',
                      TipoPersona = '" . $row["TipoPersona"] . "',Email = '" . addslashes($row["Email"]) . "',
                      idLocalidad = " . $row["idLocalidad"] . ", FechaHoraServ = '" . $row["FechaHoraServ"] . "',
                      Replicado = 1,
                      CodigoPJ = '" . $row["CodigoPJ"] . "'   
                WHERE CodigoTitular = '" . $row["CodigoTitular"] . "'";
          } else {
            $sqlInTa = "INSERT INTO personas(CodigoTitular,TipoDoc,NroDoc,Cuit,Apellido,Nombre,Domicilio,
                    Telefono,RazonSocial,TipoPersona,Email,idLocalidad,FechaHoraServ,Replicado,CodigoPJ) VALUES
                    ('" . $row["CodigoTitular"] . "','" . $row["TipoDoc"] . "'," . $row["NroDoc"] . ",'" . $row["Cuit"] . "',
                    '" . addslashes($row["Apellido"]) . "','" . addslashes($row["Nombre"]) . "','" . addslashes($row["Domicilio"]) . "','" . addslashes($row["Telefono"]) . "',
                    '" . addslashes($row["RazonSocial"]) . "','" . $row["TipoPersona"] . "','" . addslashes($row["Email"]) . "'," . $row["idLocalidad"] .
                ",'" . $row["FechaHoraServ"] . "',1,'" . $row["CodigoPJ"] . "')";
          }
   //       echo $sqlInTa;exit;
          if (!$base->query($sqlInTa)) {
            /*             * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
            $msnlog = "Ocurrio un al cargar una persona en taller. - Taller: $nomTaller";
            $continua = false;
            break;
          } else {
            $cantPaT++;
          }
        } else {
          $msnlog = "Error al intentar leer los datos de las personas en la base central";
          $continua = false;
          break;
        }
      }/*       * CIERRA FOREACH* */
    } else {
      $msnlog = "Error al intentar leer los datos de las personas en la central";
      $continua = false;
    }
  } else {
    $msnlog = "Error al intentar leer los datos de las personas en la central";
    $continua = false;
  }
}/* * * cierra if continua * */


/* * ************* Si todo salio bien vamos a buscar los vehiculos al server para traer al taller ****** */
if ($continua)
{
  $sql = " SELECT MAX(FechaHoraServ) as FechaMax FROM vehiculos ";
  $respVehiculos = $base->query($sql);
  $rowFV = $respVehiculos->fetch(PDO::FETCH_ASSOC);

  $fechaBuscarV = $rowFV["FechaMax"];

  $sqlCoSer = " SELECT * FROM vehiculos WHERE FechaHoraServ > '" . $fechaBuscarV . "' ORDER BY FechaHoraServ";
  //echo $sqlCoSer;
  $respVeServer = $dbUNC->query($sqlCoSer);
  if ($respVeServer)
  {
    foreach ($respVeServer as $rowV)
    {
      $sqlSer = "SELECT * FROM vehiculos WHERE Dominio = '" . $rowV["Dominio"] . "' ";
      $respServ = $base->query($sqlSer);

      if ($respServ->rowCount() == 0)
      {
        $ComandoSQL =
          "INSERT INTO vehiculos
            (Dominio, idTipoVehiculo, Marca, Modelo, Anio, idLocalidad, MotorMarca, MotorNro, MotorAnio, ChasisMarca, ChasisNro, ChasisAnio, TacografoMarca, TacografoNro, TacografoRodado, CCCF, TipoCombustible, Pot, NroEjes, idTipoUso, Caja, PocisionMotor, AnioFabricacion, Carroceria, Expediente, AireAco, Bar, Banio, Calefaccion, Suspencion, Tara, PesoMax, CargaUtil, Asientos, CodigoTitular, idLocTMServ, TipoServTM, idTipoServicio, tiposServicios, idHabilitacion, codigoHabilitacion, idClaseServicio, prestadorServ, CuitPrestServ, NroInterno, CompaniaSeguro, NroPoliza, UltimoRecPatente, idCategoria, FechaHoraServ, TipoCarga, CertificadoDiscapacidad, PatenteMercosur, TipoDocConductor, NroDocConductor, NombreConductor, ApellidoConductor, FechaActualizacion, arTarjetaVerde, esReverificacion, idVerificacionOriginal, Status, idTallerVerif, nroFactura, Replicado)
          VALUES
              (:Dominio, :idTipoVehiculo, :Marca, :Modelo, :Anio, :idLocalidad, :MotorMarca, :MotorNro, :MotorAnio, :ChasisMarca, :ChasisNro, :ChasisAnio, :TacografoMarca, :TacografoNro, :TacografoRodado, :CCCF, :TipoCombustible, :Pot, :NroEjes, :idTipoUso, :Caja, :PocisionMotor, :AnioFabricacion, :Carroceria, :Expediente, :AireAco, :Bar, :Banio, :Calefaccion, :Suspencion, :Tara, :PesoMax, :CargaUtil, :Asientos, :CodigoTitular, :idLocTMServ, :TipoServTM, :idTipoServicio, :tiposServicios, :idHabilitacion, :codigoHabilitacion, :idClaseServicio, :prestadorServ, :CuitPrestServ, :NroInterno, :CompaniaSeguro, :NroPoliza, :UltimoRecPatente, :idCategoria, :FechaHoraServ, :TipoCarga, :CertificadoDiscapacidad, :PatenteMercosur, :TipoDocConductor, :NroDocConductor, :NombreConductor, :ApellidoConductor, :FechaActualizacion, :arTarjetaVerde, :esReverificacion, :idVerificacionOriginal, :Status, :idTallerVerif, :nroFactura, :Replicado)";
      }
      else
      {
        $ComandoSQL = "
            UPDATE vehiculos
            SET
                idTipoVehiculo= :idTipoVehiculo, Marca= :Marca, Modelo= :Modelo, Anio= :Anio, idLocalidad= :idLocalidad, MotorMarca= :MotorMarca, MotorNro= :MotorNro, MotorAnio= :MotorAnio, ChasisMarca= :ChasisMarca, ChasisNro= :ChasisNro, ChasisAnio= :ChasisAnio, TacografoMarca= :TacografoMarca, TacografoNro= :TacografoNro, TacografoRodado= :TacografoRodado, CCCF= :CCCF, TipoCombustible= :TipoCombustible, Pot= :Pot, NroEjes= :NroEjes, idTipoUso= :idTipoUso, Caja= :Caja, PocisionMotor= :PocisionMotor, AnioFabricacion= :AnioFabricacion, Carroceria= :Carroceria, Expediente= :Expediente, AireAco= :AireAco, Bar= :Bar, Banio= :Banio, Calefaccion= :Calefaccion, Suspencion= :Suspencion, Tara= :Tara, PesoMax= :PesoMax, CargaUtil= :CargaUtil, Asientos= :Asientos, CodigoTitular= :CodigoTitular, idLocTMServ= :idLocTMServ, TipoServTM= :TipoServTM, idTipoServicio= :idTipoServicio, tiposServicios= :tiposServicios, idHabilitacion= :idHabilitacion, codigoHabilitacion= :codigoHabilitacion, idClaseServicio= :idClaseServicio, prestadorServ= :prestadorServ, CuitPrestServ= :CuitPrestServ, NroInterno= :NroInterno, CompaniaSeguro= :CompaniaSeguro, NroPoliza= :NroPoliza, UltimoRecPatente= :UltimoRecPatente, idCategoria= :idCategoria, FechaHoraServ= :FechaHoraServ, TipoCarga= :TipoCarga, CertificadoDiscapacidad= :CertificadoDiscapacidad, PatenteMercosur= :PatenteMercosur, TipoDocConductor= :TipoDocConductor, NroDocConductor= :NroDocConductor, NombreConductor= :NombreConductor, ApellidoConductor= :ApellidoConductor, FechaActualizacion= :FechaActualizacion, arTarjetaVerde= :arTarjetaVerde, esReverificacion= :esReverificacion, idVerificacionOriginal= :idVerificacionOriginal, Status= :Status, idTallerVerif= :idTallerVerif, nroFactura= :nroFactura, Replicado= :Replicado
            WHERE
                Dominio= :Dominio";
      }

      $SSQL = $base->prepare($ComandoSQL);

      // Binds
      //<editor-fold desc="Binds">
      $SSQL->bindValue(':Dominio', $rowV["Dominio"]);
      $SSQL->bindValue(':idTipoVehiculo', $rowV["idTipoVehiculo"]);
      $SSQL->bindValue(':Marca', $rowV["Marca"]);
      $SSQL->bindValue(':Modelo', $rowV["Modelo"]);
      $SSQL->bindValue(':Anio', $rowV["Anio"]);
      $SSQL->bindValue(':idLocalidad', $rowV["idLocalidad"]);
      $SSQL->bindValue(':MotorMarca', $rowV["MotorMarca"]);
      $SSQL->bindValue(':MotorNro', $rowV["MotorNro"]);
      $SSQL->bindValue(':MotorAnio', $rowV["MotorAnio"]);
      $SSQL->bindValue(':ChasisMarca', $rowV["ChasisMarca"]);
      $SSQL->bindValue(':ChasisNro', $rowV["ChasisNro"]);
      $SSQL->bindValue(':ChasisAnio', $rowV["ChasisAnio"]);
      $SSQL->bindValue(':TacografoMarca', $rowV["TacografoMarca"]);
      $SSQL->bindValue(':TacografoNro', $rowV["TacografoNro"]);
      $SSQL->bindValue(':TacografoRodado', $rowV["TacografoRodado"]);
      $SSQL->bindValue(':CCCF', $rowV["CCCF"]);
      $SSQL->bindValue(':TipoCombustible', $rowV["TipoCombustible"]);
      $SSQL->bindValue(':Pot', $rowV["Pot"]);
      $SSQL->bindValue(':NroEjes', $rowV["NroEjes"]);
      $SSQL->bindValue(':idTipoUso', $rowV["idTipoUso"]);
      $SSQL->bindValue(':Caja', $rowV["Caja"]);
      $SSQL->bindValue(':PocisionMotor', $rowV["PocisionMotor"]);
      $SSQL->bindValue(':AnioFabricacion', $rowV["AnioFabricacion"]);
      $SSQL->bindValue(':Carroceria', $rowV["Carroceria"]);
      $SSQL->bindValue(':Expediente', $rowV["Expediente"]);
      $SSQL->bindValue(':AireAco', $rowV["AireAco"]);
      $SSQL->bindValue(':Bar', $rowV["Bar"]);
      $SSQL->bindValue(':Banio', $rowV["Banio"]);
      $SSQL->bindValue(':Calefaccion', $rowV["Calefaccion"]);
      $SSQL->bindValue(':Suspencion', $rowV["Suspencion"]);
      $SSQL->bindValue(':Tara', $rowV["Tara"]);
      $SSQL->bindValue(':PesoMax', $rowV["PesoMax"]);
      $SSQL->bindValue(':CargaUtil', $rowV["CargaUtil"]);
      $SSQL->bindValue(':Asientos', $rowV["Asientos"]);
      $SSQL->bindValue(':CodigoTitular', $rowV["CodigoTitular"]);
      $SSQL->bindValue(':idLocTMServ', $rowV["idLocTMServ"]);
      $SSQL->bindValue(':TipoServTM', $rowV["TipoServTM"]);
      $SSQL->bindValue(':idTipoServicio', $rowV["idTipoServicio"]);
      $SSQL->bindValue(':tiposServicios', $rowV["tiposServicios"]);
      $SSQL->bindValue(':idHabilitacion', $rowV["idHabilitacion"]);
      $SSQL->bindValue(':codigoHabilitacion', $rowV["codigoHabilitacion"]);
      $SSQL->bindValue(':idClaseServicio', $rowV["idClaseServicio"]);
      $SSQL->bindValue(':prestadorServ', $rowV["prestadorServ"]);
      $SSQL->bindValue(':CuitPrestServ', $rowV["CuitPrestServ"]);
      $SSQL->bindValue(':NroInterno', $rowV["NroInterno"]);
      $SSQL->bindValue(':CompaniaSeguro', $rowV["CompaniaSeguro"]);
      $SSQL->bindValue(':NroPoliza', $rowV["NroPoliza"]);
      $SSQL->bindValue(':UltimoRecPatente', $rowV["UltimoRecPatente"]);
      $SSQL->bindValue(':idCategoria', $rowV["idCategoria"]);
      $SSQL->bindValue(':FechaHoraServ', $rowV["FechaHoraServ"]);
      $SSQL->bindValue(':TipoCarga', $rowV["TipoCarga"]);
      $SSQL->bindValue(':CertificadoDiscapacidad', $rowV["CertificadoDiscapacidad"]);
      $SSQL->bindValue(':PatenteMercosur', $rowV["PatenteMercosur"]);
      $SSQL->bindValue(':TipoDocConductor', $rowV["TipoDocConductor"]);
      $SSQL->bindValue(':NroDocConductor', $rowV["NroDocConductor"]);
      $SSQL->bindValue(':NombreConductor', $rowV["NombreConductor"]);
      $SSQL->bindValue(':ApellidoConductor', $rowV["ApellidoConductor"]);
      $SSQL->bindValue(':FechaActualizacion', $rowV["FechaActualizacion"]);
      $SSQL->bindValue(':arTarjetaVerde', $rowV["arTarjetaVerde"]);
      $SSQL->bindValue(':esReverificacion', $rowV["esReverificacion"]);
      $SSQL->bindValue(':idVerificacionOriginal', $rowV["idVerificacionOriginal"]);
      $SSQL->bindValue(':Status', $rowV["Status"]);
      $SSQL->bindValue(':idTallerVerif', $rowV["idTallerVerif"]);
      $SSQL->bindValue(':nroFactura', $rowV["nroFactura"]);
      $SSQL->bindValue(':Replicado', 1);
      //</editor-fold>

      try
      {
        $Res = $SSQL->execute();

        if (!$Res)
        {
          echo "Error al replicar vehiculo desde central " . $rowV["Dominio"] . "<br />";

          print_r($SSQL->errorInfo());
          exit();
        }

        $cantVaT++;
      } catch (Exception $e)
      {
        exit("ERROR");
        throw $e;
      }

    }
  }
  else
  {
    /*       * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
    $msnlog = "Error al intentar leer los datos de los vehiculos en la base del Taller: $nomTaller";
    $continua = false;
  }
}

//echo "so far"; exit;

include 'Rep_VerifCertificados.php';
include 'Rep_Adjuntos.php';
include 'Rep_Equipos.php';
include 'Rep_Auditorias.php';
include 'Rep_Taller.php';
include 'Rep_Habilitaciones.php';
include 'Rep_CertificadosCCCF.php';
include 'Rep_Defectos.php';
include 'Rep_Pendientes.php';
include 'Rep_Excepciones.php';
include 'Rep_Prorrogas.php';
include 'Rep_FotoValidaciones.php';
include 'Rep_NoConformidades.php';
include 'Rep_Parametricas.php';


if ($continua) {
  $msnlog = "Finalizo con exito la replicaci&oacute;n del taller: $nomTaller";
  $sqlRep = " INSERT INTO replicacionlogs(idTaller,Usuario,FechaHora,Observaciones,Exito) VALUES
								($idTaller,'$usu',NOW(),'$msnlog',1)";
  $respUNC = $dbUNC->query($sqlRep);
  $respUNC = $base->query($sqlRep);
} else {

  $sqlRep = " INSERT INTO replicacionlogs(idTaller,Usuario,FechaHora,Observaciones,Exito) VALUES
								($idTaller,'$usu',NOW(),'$msnlog',0)";
  $respUNC = $dbUNC->query($sqlRep);
  $respUNC = $base->query($sqlRep);
}
/* * ********************************************************************* */
echo "<p><b>" . $msnlog . "</b></p>";
echo "<p>Se enviaron $cantPaC registros de personas a Central.<br /> Se enviaron $cantVaC registros de vehiculos a Central. <br />
	  Se recibieron $cantPaT registros de personas desde central. <br /> Se recibieron $cantVaT registros de vehiculos desde central.<br />
 	  Se recibieron $obleasRec obleas desde Central. <br />Se enviaron $obleasEnv obleas modificadas a Central. <br />
    Se enviaron $cantVeraC registros de verificaciones a Central. <br />
    Se enviaron $cantDefectos registros de defectos en verificaciones a Central. <br />
    Se enviaron $cantCeraC de registros de certificados a central.   
	  <br /> Se enviaron $cantEquiposAC registros de equipos a Central.
	  <br /> Se enviaron $cantMantEqAC registros de mantenimientos a equipos a Central.
	  <br /> Se enviaron $cantAudAC registros de auditorias a Central.	  
<br />Se replicaron $cantHabilitaciones habilitaciones.
<br />Se replicaron $cantEmpresasCCCF empresas desde el m&oacute;dulo CCCF.
<br />Se replicaron $cantCertificadosCCCF Certificados CCCF.
<br />Se replicaron $cantExcepciones Excepciones.
<br />Se replicaron $cantFotoValidaciones Excepciones.
<br />Se replicaron $cantVerificacionesServicios Verificaciones Servicios.


 	  </p>";
?>
