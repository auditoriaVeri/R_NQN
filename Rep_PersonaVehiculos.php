<?php

require_once('PDOConfig.php');
require_once("sftpFunc.php");
require_once('utilidades.php');
$mensaje = "";
$msnlog = "";

/* Para que nos e corte la ejecucion por timeout */
set_time_limit(0);

try {
  //$dbUNC = new PDO('mysql:host=vtvunc.ddns.net;dbname=vehicularunc;charset=utf8', 'usrRem', 'eureRemoto');
 //wewe
  //wewew
  $dbUNC = new PDO('mysql:host=localhost;dbname=uncrto_centralnqn;charset=utf8', 'root', 'eurePass');
} catch (Exception $e) {
  echo $e->getMessage();
  exit();
}

//$dbUNC = new PDO('mysql:host=localhost;dbname=vehicularunc;charset=utf8', 'root', '');
$base = new PDOConfig();
$idTaller = 1111117;
$nomTaller = "Veritecnica SRL";
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
$cantDefectosEnVerificaciones= 0;
$actTaller = 0;
$actDT = 0;
$actINS = 0;
$cantHabilitaciones= 0;
$cantCertificadosCCCF= 0;
$obleasEnv = 0;
$obleasRec = 0;

$msnlog = "Inicia Replicaci&oacute;n - Taller: $nomTaller. (" . Date('d/m/Y H:i') . ")";
echo $msnlog;

$sqlRep = " INSERT INTO replicacionlogs(idTaller,Usuario,FechaHora,Observaciones,Exito) VALUES 
								($idTaller,'$usu',NOW(),'$msnlog',1)";
$respUNC = $dbUNC->query($sqlRep);
$respUNC = $base->query($sqlRep);
$continua = true;

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
  if ($respVehiculos) {

    foreach ($respVehiculos as $rowV) {
      /*       * *** Por cada vehiculo vamos a ver si esta el server => update, si no esta => insert *** */
      $sqlSer = "SELECT * FROM vehiculos WHERE Dominio = '" . $rowV["Dominio"] . "' ";
      if ($respServ = $dbUNC->query($sqlSer)) {

        if ($rowV["idTipoVehiculo"] == "")
          $rowV["idTipoVehiculo"] = 'NULL';
        if ($rowV["Anio"] == "")
          $rowV["Anio"] = 'NULL';
        if ($rowV["idLocalidad"] == "")
          $rowV["idLocalidad"] = 'NULL';
        if ($rowV["MotorAnio"] == "")
          $rowV["MotorAnio"] = 'NULL';
        if ($rowV["ChasisAnio"] == "")
          $rowV["ChasisAnio"] = 'NULL';
        if ($rowV["NroEjes"] == "")
          $rowV["NroEjes"] = 'NULL';
        if ($rowV["idTipoUso"] == "")
          $rowV["idTipoUso"] = 'NULL';
        if ($rowV["Tara"] == "")
          $rowV["Tara"] = 'NULL';
        if ($rowV["PesoMax"] == "")
          $rowV["PesoMax"] = 'NULL';
        if ($rowV["CargaUtil"] == "")
          $rowV["CargaUtil"] = 'NULL';
        if ($rowV["Asientos"] == "")
          $rowV["Asientos"] = 'NULL';
        if ($rowV["idLocTMServ"] == "")
          $rowV["idLocTMServ"] = 'NULL';
        if ($rowV["idTipoServicio"] == "")
          $rowV["idTipoServicio"] = 'NULL';
        if ($rowV["idClaseServicio"] == "")
          $rowV["idClaseServicio"] = 'NULL';
        if ($rowV["NroInterno"] == "")
          $rowV["NroInterno"] = 'NULL';
        if ($rowV["idCategoria"] == "")
          $rowV["idCategoria"] = 'NULL';
        if($rowV["arTarjetaVerde"] == "") 
        {	
            $rowV["arTarjetaVerde"] = 'NULL';					
        }
        else{
            $cargarArchiTV = true;
            $rowV["arTarjetaVerde"] = "'".$rowV["arTarjetaVerde"] ."'";
        }

        if ($respServ->rowCount() > 0) {
          $sqlInSer = "UPDATE vehiculos SET idTipoVehiculo = " . $rowV["idTipoVehiculo"] . ",Marca = '" . addslashes($rowV["Marca"]) . "',
                Modelo='" . addslashes($rowV["Modelo"]) . "',Anio = " . $rowV["Anio"] . ",idLocalidad = " . $rowV["idLocalidad"] . ",
                MotorMarca = '" . addslashes($rowV["MotorMarca"]) . "',MotorNro = '" . addslashes($rowV["MotorNro"]) . "',MotorAnio = " . $rowV["MotorAnio"] . ",
                ChasisMarca = '" . addslashes($rowV["ChasisMarca"]) . "',ChasisNro = '" . addslashes($rowV["ChasisNro"]) . "',ChasisAnio = " . $rowV["ChasisAnio"] . ",
                TacografoMarca = '" . addslashes($rowV["TacografoMarca"]) . "',TacografoNro = '" . addslashes($rowV["TacografoNro"]) . "',CCCF='" . addslashes($rowV["CCCF"]) . "',
                TipoCombustible = '" . addslashes($rowV["TipoCombustible"]) . "',Pot = '" . addslashes($rowV["Pot"]) . "',NroEjes = " . $rowV["NroEjes"] . ",
                idTipoUso = " . $rowV["idTipoUso"] . ",Caja = '" . addslashes($rowV["Caja"]) . "',PocisionMotor = '" . addslashes($rowV["PocisionMotor"]) . "',
                Carroceria = '" . addslashes($rowV["Carroceria"]) . "',Expediente = '" . addslashes($rowV["Expediente"]) . "',AireAco=" . $rowV["AireAco"] . ",
                Bar = " . $rowV["Bar"] . ",Banio = " . $rowV["Banio"] . ",Calefaccion = " . $rowV["Calefaccion"] . ",Suspencion = '" . addslashes($rowV["Suspencion"]) . "',
                Tara = " . $rowV["Tara"] . ",PesoMax = " . $rowV["PesoMax"] . ",CargaUtil = " . $rowV["CargaUtil"] . ",Asientos = " . $rowV["Asientos"] . ",
                CodigoTitular = '" . $rowV["CodigoTitular"] . "',idLocTMServ = " . $rowV["idLocTMServ"] . ",TipoServTM = '" . addslashes($rowV["TipoServTM"]) . "',
                idTipoServicio = " . $rowV["idTipoServicio"] . ",idClaseServicio = " . $rowV["idClaseServicio"] . ",NroInterno = '" . $rowV["NroInterno"] . "',
                CompaniaSeguro = '" . addslashes($rowV["CompaniaSeguro"]) . "',NroPoliza = '" . addslashes($rowV["NroPoliza"]) . "',UltimoRecPatente='" . addslashes($rowV["UltimoRecPatente"]) . "',
                idCategoria = " . $rowV["idCategoria"] . ",FechaHoraServ = NOW(),PatenteMercosur=" . $rowV["PatenteMercosur"] . ",arTarjetaVerde=". $rowV["arTarjetaVerde"] . " WHERE Dominio = '" . $rowV["Dominio"] . "'";
        } else {
          $sqlInSer = "INSERT INTO vehiculos(Dominio,idTipoVehiculo,Marca,Modelo,Anio,idLocalidad,MotorMarca,MotorNro,MotorAnio,
              ChasisMarca,ChasisNro,ChasisAnio,TacografoMarca,TacografoNro,CCCF,TipoCombustible,Pot,NroEjes,idTipoUso,Caja,
              PocisionMotor,Carroceria,Expediente,AireAco,Bar,Banio,Calefaccion,Suspencion,Tara,PesoMax,CargaUtil,Asientos,
              CodigoTitular,idLocTMServ,TipoServTM,idTipoServicio,idClaseServicio,NroInterno,CompaniaSeguro,NroPoliza,UltimoRecPatente,
              idCategoria,FechaHoraServ,PatenteMercosur,arTarjetaVerde) VALUES
                       ('" . $rowV["Dominio"] . "'," . $rowV["idTipoVehiculo"] . ",'" . addslashes($rowV["Marca"]) . "','" . addslashes($rowV["Modelo"]) . "'," . $rowV["Anio"] . ",
                      " . $rowV["idLocalidad"] . ",'" . addslashes($rowV["MotorMarca"]) . "','" . addslashes($rowV["MotorNro"]) . "'," . $rowV["MotorAnio"] . ",'" . addslashes($rowV["ChasisMarca"]) . "',
                      '" . addslashes($rowV["ChasisNro"]) . "'," . $rowV["ChasisAnio"] . ",'" . addslashes($rowV["TacografoMarca"]) . "','" . addslashes($rowV["TacografoNro"]) . "','" . addslashes($rowV["CCCF"]) . "',
                      '" . addslashes($rowV["TipoCombustible"]) . "','" . addslashes($rowV["Pot"]) . "'," . $rowV["NroEjes"] . "," . $rowV["idTipoUso"] . ",'" . addslashes($rowV["Caja"]) . "',
                      '" . addslashes($rowV["PocisionMotor"]) . "','" . addslashes($rowV["Carroceria"]) . "','" . addslashes($rowV["Expediente"]) . "'," . $rowV["AireAco"] . "," . $rowV["Bar"] . ",
                      " . $rowV["Banio"] . "," . $rowV["Calefaccion"] . ",'" . addslashes($rowV["Suspencion"]) . "'," . $rowV["Tara"] . "," . $rowV["PesoMax"] . "," . $rowV["CargaUtil"] . ",
                      " . $rowV["Asientos"] . ",'" . addslashes($rowV["CodigoTitular"]) . "'," . $rowV["idLocTMServ"] . ",'" . addslashes($rowV["TipoServTM"]) . "'," . $rowV["idTipoServicio"] . ",
                      " . $rowV["idClaseServicio"] . ",'" . $rowV["NroInterno"] . "','" . addslashes($rowV["CompaniaSeguro"]) . "','" . addslashes($rowV["NroPoliza"]) . "','" . addslashes($rowV["UltimoRecPatente"]) . "',
                      " . $rowV["idCategoria"] . ",NOW()," . $rowV["PatenteMercosur"] . "," . $rowV["arTarjetaVerde"] . ")";
        }
        //echo $sqlInSer;
        if ($dbUNC->query($sqlInSer)) {
          /*           * ** una vez que cargue en server, le indico en el taller que ya fue replicado *** */
          $sql = "UPDATE vehiculos SET Replicado = 1 WHERE Dominio = '" . $rowV["Dominio"] . "'";
          if (!$base->query($sql)) {
            /*             * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
            $msnlog = "Ocurrio un al marcar como replicado un vehiculo en taller. - Taller: $nomTaller";
            $continua = false;
            break;
          } else {
            $cantVaC++;
          }
        } else {
          /*           * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
          $msnlog = "Ocurrio un error al cargar un vehiculo en central. Taller: $nomTaller ";
          $continua = false;
          break;
        }
      } else {
        /*         * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
        $msnlog = "Ocurrio un error al buscar un vehiculo en central.";
        $continua = false;
        break;
      }
    }/*     * ******* Cierra el foreach ****************** */
  } else {
    /*     * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
    $msnlog = "Error al intentar leer los datos de los vehiculos en la base del Taller: $nomTaller";
    $continua = false;
  }
}/* * * cierra if continua * */


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
          //echo $sqlInTa;
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
if ($continua) {
  /*   * ************* Ahora buscamos los datos en el server que se deben actualizar en el taller ************* */
  /*   * *** buscamos en el server los vehivulos que no se han replicado en el taller *** */
  //veamos cual fue la ultima fecha que se replico	
  $sql = " SELECT MAX(FechaHoraServ) as FechaMax FROM vehiculos ";
  $respVehiculos = $base->query($sql);
  if ($respVehiculos) {
    $rowFV = $respVehiculos->fetch(PDO::FETCH_ASSOC);
    $fechaBuscarV = $rowFV["FechaMax"];
    /* if($rowFV["FechaMax"] != ""){
      $fechaBuscarV = substr($fechaBuscarV,0,10);
      } */
    $sqlCoSer = " SELECT * FROM vehiculos WHERE FechaHoraServ > '" . $fechaBuscarV . "' ORDER BY FechaHoraServ";
    //echo $sqlCoSer;
    $respVeServer = $dbUNC->query($sqlCoSer);
    if ($respVeServer) {
      foreach ($respVeServer as $rowV) {
        /*         * *** Por cada vehiculo vamos a ver si esta el server => update, si no esta => insert *** */
        $sqlSer = "SELECT * FROM vehiculos WHERE Dominio = '" . $rowV["Dominio"] . "' ";
        if ($respServ = $base->query($sqlSer)) {

          if ($rowV["idTipoVehiculo"] == "")
            $rowV["idTipoVehiculo"] = 'NULL';
          if ($rowV["Anio"] == "")
            $rowV["Anio"] = 'NULL';
          if ($rowV["idLocalidad"] == "")
            $rowV["idLocalidad"] = 'NULL';
          if ($rowV["MotorAnio"] == "")
            $rowV["MotorAnio"] = 'NULL';
          if ($rowV["ChasisAnio"] == "")
            $rowV["ChasisAnio"] = 'NULL';
          if ($rowV["NroEjes"] == "")
            $rowV["NroEjes"] = 'NULL';
          if ($rowV["idTipoUso"] == "")
            $rowV["idTipoUso"] = 'NULL';
          if ($rowV["Tara"] == "")
            $rowV["Tara"] = 'NULL';
          if ($rowV["PesoMax"] == "")
            $rowV["PesoMax"] = 'NULL';
          if ($rowV["CargaUtil"] == "")
            $rowV["CargaUtil"] = 'NULL';
          if ($rowV["Asientos"] == "")
            $rowV["Asientos"] = 'NULL';
          if ($rowV["idLocTMServ"] == "")
            $rowV["idLocTMServ"] = 'NULL';
          if ($rowV["idTipoServicio"] == "")
            $rowV["idTipoServicio"] = 'NULL';
          if ($rowV["idClaseServicio"] == "")
            $rowV["idClaseServicio"] = 'NULL';
          if ($rowV["NroInterno"] == "")
            $rowV["NroInterno"] = 'NULL';
          if ($rowV["idCategoria"] == "")
            $rowV["idCategoria"] = 'NULL';

          if ($respServ->rowCount() > 0) {
            $sqlInTa = "UPDATE vehiculos SET idTipoVehiculo = " . $rowV["idTipoVehiculo"] . ",Marca = '" . addslashes($rowV["Marca"]) . "',
                  Modelo='" . addslashes($rowV["Modelo"]) . "',Anio = " . $rowV["Anio"] . ",idLocalidad = " . $rowV["idLocalidad"] . ",
                  MotorMarca = '" . addslashes($rowV["MotorMarca"]) . "',MotorNro = '" . addslashes($rowV["MotorNro"]) . "',MotorAnio = " . $rowV["MotorAnio"] . ",
                  ChasisMarca = '" . addslashes($rowV["ChasisMarca"]) . "',ChasisNro = '" . addslashes($rowV["ChasisNro"]) . "',ChasisAnio = " . $rowV["ChasisAnio"] . ",
                  TacografoMarca = '" . addslashes($rowV["TacografoMarca"]) . "',TacografoNro = '" . addslashes($rowV["TacografoNro"]) . "',CCCF='" . addslashes($rowV["CCCF"]) . "',
                  TipoCombustible = '" . addslashes($rowV["TipoCombustible"]) . "',Pot = '" . addslashes($rowV["Pot"]) . "',NroEjes = " . $rowV["NroEjes"] . ",
                  idTipoUso = " . $rowV["idTipoUso"] . ",Caja = '" . $rowV["Caja"] . "',PocisionMotor = '" . $rowV["PocisionMotor"] . "',
                  Carroceria = '" . addslashes($rowV["Carroceria"]) . "',Expediente = '" . addslashes($rowV["Expediente"]) . "',AireAco=" . $rowV["AireAco"] . ",
                  Bar = " . $rowV["Bar"] . ",Banio = " . $rowV["Banio"] . ",Calefaccion = " . $rowV["Calefaccion"] . ",Suspencion = '" . addslashes($rowV["Suspencion"]) . "',
                  Tara = " . $rowV["Tara"] . ",PesoMax = " . $rowV["PesoMax"] . ",CargaUtil = " . $rowV["CargaUtil"] . ",Asientos = " . $rowV["Asientos"] . ",
                  CodigoTitular = '" . $rowV["CodigoTitular"] . "',idLocTMServ = " . $rowV["idLocTMServ"] . ",TipoServTM = '" . $rowV["TipoServTM"] . "',
                  idTipoServicio = " . $rowV["idTipoServicio"] . ",idClaseServicio = " . $rowV["idClaseServicio"] . ",NroInterno = '" . addslashes($rowV["NroInterno"]) . "',
                  CompaniaSeguro = '" . addslashes($rowV["CompaniaSeguro"]) . "',NroPoliza = '" . addslashes($rowV["NroPoliza"]) . "',UltimoRecPatente='" . addslashes($rowV["UltimoRecPatente"]) . "',
                  idCategoria = " . $rowV["idCategoria"] . ", FechaHoraServ = '" . $rowV["FechaHoraServ"] . "', FechaHoraTaller = '" . $row["FechaHoraTaller"] . "',
                  Replicado = 1,PatenteMercosur=" . $rowV["PatenteMercosur"] . " WHERE Dominio = '" . $rowV["Dominio"] . "'";
          } else {
            $sqlInTa = "INSERT INTO vehiculos(Dominio,idTipoVehiculo,Marca,Modelo,Anio,idLocalidad,MotorMarca,MotorNro,MotorAnio,
              ChasisMarca,ChasisNro,ChasisAnio,TacografoMarca,TacografoNro,CCCF,TipoCombustible,Pot,NroEjes,idTipoUso,Caja,
              PocisionMotor,Carroceria,Expediente,AireAco,Bar,Banio,Calefaccion,Suspencion,Tara,PesoMax,CargaUtil,Asientos,
              CodigoTitular,idLocTMServ,TipoServTM,idTipoServicio,idClaseServicio,NroInterno,CompaniaSeguro,NroPoliza,
              UltimoRecPatente,idCategoria,FechaHoraServ,FechaHoraTaller,Replicado,PatenteMercosur) VALUES
                ('" . $rowV["Dominio"] . "'," . $rowV["idTipoVehiculo"] . ",'" . addslashes($rowV["Marca"]) . "','" . addslashes($rowV["Modelo"]) . "'," . $rowV["Anio"] . ",
               " . $rowV["idLocalidad"] . ",'" . addslashes($rowV["MotorMarca"]) . "','" . addslashes($rowV["MotorNro"]) . "'," . $rowV["MotorAnio"] . ",'" . addslashes($rowV["ChasisMarca"]) . "',
               '" . addslashes($rowV["ChasisNro"]) . "'," . $rowV["ChasisAnio"] . ",'" . addslashes($rowV["TacografoMarca"]) . "','" . addslashes($rowV["TacografoNro"]) . "','" . addslashes($rowV["CCCF"]) . "',
               '" . addslashes($rowV["TipoCombustible"]) . "','" . addslashes($rowV["Pot"]) . "'," . $rowV["NroEjes"] . "," . $rowV["idTipoUso"] . ",'" . addslashes($rowV["Caja"]) . "',
               '" . addslashes($rowV["PocisionMotor"]) . "','" . addslashes($rowV["Carroceria"]) . "','" . addslashes($rowV["Expediente"]) . "'," . $rowV["AireAco"] . "," . $rowV["Bar"] . ",
               " . $rowV["Banio"] . "," . $rowV["Calefaccion"] . ",'" . addslashes($rowV["Suspencion"]) . "'," . $rowV["Tara"] . "," . $rowV["PesoMax"] . "," . $rowV["CargaUtil"] . ",
               " . $rowV["Asientos"] . ",'" . addslashes($rowV["CodigoTitular"]) . "'," . $rowV["idLocTMServ"] . ",'" . addslashes($rowV["TipoServTM"]) . "'," . $rowV["idTipoServicio"] . ",
               " . $rowV["idClaseServicio"] . ",'" . addslashes($rowV["NroInterno"]) . "','" . addslashes($rowV["CompaniaSeguro"]) . "','" . addslashes($rowV["NroPoliza"]) . "','" . addslashes($rowV["UltimoRecPatente"]) . "',
               " . $rowV["idCategoria"] . ",'" . $rowV["FechaHoraServ"] . "','" . $row["FechaHoraTaller"] . "',1," . $rowV["PatenteMercosur"] . ")";
          }
          //echo $sqlInTa;
          if (!$base->query($sqlInTa)) {
            /*             * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
            $msnlog = "Ocurrio un al cargar un vehiculo en taller. - Taller: $nomTaller";
            $continua = false;
            break;
          } else {
            $cantVaT++;
          }
        } else {
          /*           * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
          $msnlog = "Ocurrio un error al buscar un vehiculo en central.";
          $continua = false;
          break;
        }
      }/*       * ******* Cierra el foreach ****************** */
    } else {
      /*       * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
      $msnlog = "Error al intentar leer los datos de los vehiculos en la base del Taller: $nomTaller";
      $continua = false;
    }
  } else {
    /*     * ** SI OCURRIO UN ERROR CORTAMOS EL PROCESO **** */
    $msnlog = "Error al intentar leer los datos de los vehiculos en la base del Taller: $nomTaller";
    $continua = false;
  }
}

include 'Rep_VerifCertificados.php';
include 'Rep_Equipos.php';
include 'Rep_Auditorias.php';
include 'Rep_Taller.php';
include 'Rep_Habilitaciones.php';
include 'Rep_CertificadosCCCF.php';
include 'Rep_Adjuntos.php';
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
	  Se recibieron $cantPaT registros de personas desde central. <br /> Se recibieron $cantVaT resistros de vehiculos desde central.<br />
 	  Se recibieron $obleasRec obleas desde Central. <br />Se enviaron $obleasEnv obleas modificadas a Central. <br />
    Se enviaron $cantVeraC registros de verificaciones a Central. <br />
    Se enviaron $cantDefectosEnVerificaciones registros de defectos en verificaciones a Central. <br />
    Se enviaron $cantCeraC de registros de certificados a central.   
	  <br /> Se enviaron $cantEquiposAC registros de equipos a Central.
	  <br /> Se enviaron $cantMantEqAC registros de mantenimientos a equipos a Central.
	  <br /> Se enviaron $cantAudAC registros de auditorias a Central.	  
<br />Se replicaron $cantHabilitaciones habilitaciones.
<br />Se replicaron $cantCertificadosCCCF Certificados CCCF.
    <br /> Se recibieron $hmInstalaciones instalaciones. 
 	  </p>";
?>
