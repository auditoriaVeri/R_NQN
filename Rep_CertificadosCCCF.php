<?php

/***** Excepciones. Tienen que subir las nuevas, bajar las de central *****/

// Replico las empresas CCCF
if ($continua)
{
  // Obtengo lo último qie tengo
  $sql= "SELECT MAX(idEmpresa) as MaxIdEmpresa FROM cccf_empresas";
  $respVer = $base->query($sql);
  if($respVer)
  {
    $row = $respVer->fetch(PDO::FETCH_ASSOC);
    $MaxIdEmpresa= $row['MaxIdEmpresa'];
  }
  else
  {
    $msnlog = "Ocurrio un error al obtener las emprresas CCCF locales.";
    $continua = false;
  }
}

if ($continua)
{
  // Obtengo todo lo que sea nuevo de central
  if ($MaxIdEmpresa == null)
    $MaxIdEmpresa= 0;

  $sql = "SELECT * FROM cccf_empresas WHERE idEmpresa > $MaxIdEmpresa";

 // echo $sql;exit;
  $respVer = $dbUNC->query($sql);
  if ($respVer)
  {
    /***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
    foreach ($respVer as $row)
    {
      // Acá no hay update mierda
      $ComandoSQL =
        " INSERT INTO cccf_empresas " .
        " (" .
        "   idEmpresa, CUIT, RazonSocial " .
        " )" .
        " VALUES " .
        "( " .
        "   " . $row['idEmpresa'] . ", '" . $row['CUIT'] . "', '" . addslashes($row['RazonSocial']) . "'" .
        ")";

      // echo $ComandoSQL;exit();
      if (!$base->query($ComandoSQL))
      {
        /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
        $msnlog =
          "Ocurrio un error al replicar una empresa cccf - idEmpresa: " . $row["idEmpresa"];
        echo $ComandoSQL;
        $continua = false;
        break;
      } else
      {
        $cantEmpresasCCCF++;
      }
    }
  } else
  {
    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
    $msnlog = "Ocurrio un error al buscar empresas del módulo CCCF para replicar.";
    $continua = false;
  }
}


if($continua)
{
  // Obtengo lo último qie tengo
  $sql= " SELECT MAX(FechaHoraCarga) as MaxFH FROM cccf_certificados";
  $respVer = $base->query($sql);
  if($respVer)
  {
     $row = $respVer->fetch(PDO::FETCH_ASSOC);
     $MaxFH= $row['MaxFH'];
  }
  else
  {
    $msnlog = "Ocurrio un error al obtener los certificados CCCF en central.";
		$continua = false;
  }
}

if ($continua)  
{
  // Obtengo todo lo que sea nuevo de central
  if ($MaxFH == null)
    $sql= "SELECT * FROM cccf_certificados ORDER BY FechaHoraCarga";
  else
    $sql = " SELECT * FROM cccf_certificados WHERE FechaHoraCarga > '$MaxFH' ORDER BY FechaHoraCarga";
//echo $sql;exit;
	$respVer = $dbUNC->query($sql);
  if($respVer)
  {
		/***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{
      if (isset($row["FechaHoraTaller"]) || $row["FechaHoraTaller"] == "")
        $FHTAux= "NULL";
      else
        $FHTAux= "'" . row["FechaHoraTaller"] . "'";


      if ($row['FechaAnulacion'] == "")
        $FHAnulacionAux= "NULL";
      else
        $FHAnulacionAux= "'" . $row['FechaAnulacion'] . "'";

//echo $FHAnulacionAux; exit;

      // Acá no hay update mierda
      $ComandoSQL=
        " INSERT INTO cccf_certificados " .
        " (" .
        "   idCertificado, NroCertificado, idTaller, " .
        "   FechaHoraCarga, FechaCalibracion, FechaVencimiento, idEmpresa, " .
        "   PropUsuario, Dominio, NroInterno, Kilometraje, TacMarca, TacTipo, " .
        "   TacModelo, TacNroSerie, RelW, ConstanteK, Rodado, Precinto, Impresora, " .
        "   NroInforme, CantHojas, Observaciones, usuario, idEstado, FechaAnulacion, " .
        "   ObservacionesAnulacion, PatenteMercosur, CBVerificador, SinExcesos, DesconexionCantidad, " .
        "   DesconexionHora, AperturaEquipo, RetiroElementoGrabacion, " .
        "   FallasDispositivo, FaltaInformacion" .
        " )" .
	      " VALUES " .
        "( " .
        "   '" . $row['idCertificado'] . "', '" . $row['NroCertificado'] . "', '"
        . $row['idTaller'] . "', " . "'" . $row['FechaHoraCarga'] . "', '" . $row['FechaCalibracion'] . "', '" .
        $row['FechaVencimiento'] . "', '" . $row['idEmpresa'] . "', '" . addslashes($row['PropUsuario']) . "', '" . $row['Dominio'] . "', '"
        . $row['NroInterno'] . "', '" . $row['Kilometraje'] . "', '" . $row['TacMarca'] . "', '" . $row['TacTipo'] . "', '" .
        $row['TacModelo'] . "', '" . $row['TacNroSerie'] . "', '" . $row['RelW'] . "', '" . $row['ConstanteK'] . "', '" .
        $row['Rodado'] . "', '" . $row['Precinto'] . "', '" . $row['Impresora'] . "', '" . $row['NroInforme'] . "', '" . $row['CantHojas'] . "', '" .
        $row['Observaciones'] . "', '" . $row['usuario'] . "', '" . $row['idEstado'] . "', " . $FHAnulacionAux . ", '" .
        $row['ObservacionesAnulacion'] . "', '" . $row['PatenteMercosur'] . "', '" . $row['CBVerificador'] . "', '" . $row['SinExcesos'] . "', '" .
        $row['DesconexionCantidad'] . "', '" . $row['DesconexionHora'] . "', '" . $row['AperturaEquipo'] . "', '" .
        $row['RetiroElementoGrabacion'] . "', '" . $row['FallasDispositivo'] . "', '" . $row['FaltaInformacion'] . "'" .
        ")";

//echo $ComandoSQL;exit;

     // echo $ComandoSQL;exit();
      if(!$base->query($ComandoSQL))
      {
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = 
            "Ocurrio un error al replicar un certificado cccf - idCertificado: " . $row["idCertificado"];
          echo $ComandoSQL;
          $continua = false;
          break;
      }
      else
      {
        $cantCertificadosCCCF++;
      }
    }
  }
  else
  {
    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
    $msnlog = "Ocurrio un error al buscar certificados cccf para replicar.";
    $continua = false;
  }


}
 



