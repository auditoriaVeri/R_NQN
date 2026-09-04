<?php

/***** VAMOS A TRAER DE CENTRAL LOS CERIFICADOS ASOCIADOS AL TALLER, SI EXISTIERAN***********/

if($continua){

	$sqlNroCert = " SELECT  NroCertificado,idTaller,FechaCarga,Disponible,Replicado FROM certificadosasignadosportaller WHERE Replicado = 0 AND idTaller = $idTaller ";
	//echo $sqlCoSer;
	$respServer = $dbUNC->query($sqlNroCert);
	if($respServer){
		foreach ($respServer as $row)
		{
			$sqlIncert = " INSERT certificadosasignadosportaller(NroCertificado,idTaller,FechaCarga,Disponible,Replicado)
			           VALUES (".$row["NroCertificado"].",".$row["idTaller"].",'".$row["FechaCarga"]."',".$row["Disponible"].",1)";
			//echo $sqlIncert."<br />";
			if($base->query($sqlIncert)){
				/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/
				$sqlNroCert = "UPDATE certificadosasignadosportaller SET Replicado = 1 WHERE NroCertificado = ".$row["NroCertificado"]." AND idTaller = ".$row["idTaller"];
				if(!$dbUNC->query($sqlNroCert)){
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un al marcar como replicado un certificado Asociado en Central.- Taller: $nomTaller";
					$continua = false;
					break;
				}else{
					$cantVeraC++;
				}
			}
			else{
				/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
				$msnlog = "Ocurrio un error al cargar los certificados asociados al taller. Taller: $nomTaller";
				$continua = false;
				break;
			}
		}
	}


	/*************************** PASAR LOS NROS USADOS A CENTRAL *****************************/
	$sqlNroCertT = " SELECT  NroCertificado,idTaller,FechaCarga,Disponible,Replicado FROM certificadosasignadosportaller WHERE Replicado = 0 AND idTaller = $idTaller ";
	//echo $sqlCoSer;
	$respTa = $base->query($sqlNroCertT);
	if($respTa){
		foreach ($respTa as $row)
		{
			$sqlIncertC = " UPDATE certificadosasignadosportaller SET Disponible = ".$row["Disponible"].", Replicado = 1
			               WHERE NroCertificado = ".$row["NroCertificado"]." AND idTaller = ".$row["idTaller"];
			//echo $sqlIncert."<br />";
			if($dbUNC->query($sqlIncertC)){
				/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/
				$sqlNroCertT = "UPDATE certificadosasignadosportaller SET Replicado = 1 WHERE NroCertificado = ".$row["NroCertificado"]." AND idTaller = ".$row["idTaller"];
				if(!$base->query($sqlNroCertT)){
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un al marcar como replicado un certificado Asociado en el Taller.- Taller: $nomTaller";
					$continua = false;
					break;
				}else{
					$cantVeraC++;
				}
			}
			else{
				/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
				$msnlog = "Ocurrio un error al cargar los certificados asociados a Central. Taller: $nomTaller";
				$continua = false;
				break;
			}
		}
	}
}


/***** VAMOS A ENVIAR LAS VERIFICACIONES Y CERTIFICADOS A CENTRAL***********/
/***** buscamos en el taller las verificaciones que no se han replicado ****/



if($continua)
{
	$sql = " SELECT * FROM verificaciones WHERE Replicado = 0 ";
	$respVer = $base->query($sql);
	if($respVer)
	{
		/***** Por cada verificaciones vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{		
			$sqlSer = "SELECT * FROM verificaciones WHERE idVerificacion = ".$row["idVerificacion"]." AND 
							idTaller = ".$row["idTaller"];
			if($respServ = $dbUNC->query($sqlSer))
			{
				if($respServ->rowCount() == 0)
        {
          $ComandoSQL= "
            INSERT INTO verificaciones
            (idVerificacion, Fecha, Hora, HoraFinal, idTaller, idFotovalidacion, DominioVehiculo, idHabilitacion, codigoHabilitacion, ChasisNro, MotorAnio, MotorMarca, MotorNumero, idLocalidadVehiculo, TipoDocConductor, NroDocConductor, NombreConductor, ApellidoConductor, Reverificacion, idVerificacionOriginal, Inspector, DirectorTecnico, idEstado, Eje1_Tara, Eje2_Tara, Eje3_Tara, Eje4_Tara, Eje1_FzaIzq, Eje2_FzaIzq, Eje3_FzaIzq, Eje4_FzaIzq, Eje1_FzaDer, Eje2_FzaDer, Eje3_FzaDer, Eje4_FzaDer, Eje1_Dif, Eje2_Dif, Eje3_Dif, Eje4_Dif, Eje1_Eficiencia, Eje2_Eficiencia, Eje3_Eficiencia, Eje4_Eficiencia, Eje5_Tara, Eje5_FzaIzq, Eje5_FzaDer, Eje5_Dif, Eje5_Eficiencia, Alineacion, NivelSonoro, Interior, 
            Escape, 
            Bach, PorcentajeCo, Opac, ppmHC, Freno_FzaIsq, Freno_FzaDer, Freno_Dif, Freno_Eficiencia, CCCF, MarcaTac, NroTac, RodadoTac, NroInterno, CodigoTitular, DescripcionTitular, Susp_FzaIsq, Susp_FzaDer, Susp_Dif, Susp_Eficiencia, Observaciones, CompaniaSeguro, NroPoliza, UltimoRecPatente, idTipoUso, usuarioCarga, idTipoVehiculo, VMarca, VModelo, VAnio, ChasisMarca, ChasisAnio, TipoCombustible, VPotencia, NroEjes, VCaja, PocisionMotor, AnioFabricacion, Carroceria, Expediente, AireAco, Bar, Banio, Calefaccion, Suspencion, Tara, PesoMax, CargaUtil, Asientos, idLocTMServ, TipoServTM, idTipoServicio, idClaseServicio, prestadorServ, CuitPrestServ, PTipoDoc, PNroDoc, PCuit, PDomicilio, PTelefono, PEmail, PidLocalidad, PTipoPersona, FechaHoraRep, TipoCarga, PCodigoPJ, CertificadoDiscapacidad, Firma, nroFactura)
            VALUES
            (:idVerificacion, :Fecha, :Hora, :HoraFinal, :idTaller, :idFotovalidacion, :DominioVehiculo, :idHabilitacion, :codigoHabilitacion, :ChasisNro, :MotorAnio, :MotorMarca, :MotorNumero, :idLocalidadVehiculo, :TipoDocConductor, :NroDocConductor, :NombreConductor, :ApellidoConductor, :Reverificacion, :idVerificacionOriginal, :Inspector, :DirectorTecnico, :idEstado, :Eje1_Tara, :Eje2_Tara, :Eje3_Tara, :Eje4_Tara, :Eje1_FzaIzq, :Eje2_FzaIzq, :Eje3_FzaIzq, :Eje4_FzaIzq, :Eje1_FzaDer, :Eje2_FzaDer, :Eje3_FzaDer, :Eje4_FzaDer, :Eje1_Dif, :Eje2_Dif, :Eje3_Dif, :Eje4_Dif, :Eje1_Eficiencia, :Eje2_Eficiencia, :Eje3_Eficiencia, :Eje4_Eficiencia, :Eje5_Tara, :Eje5_FzaIzq, :Eje5_FzaDer, :Eje5_Dif, :Eje5_Eficiencia, :Alineacion, :NivelSonoro, :Interior, 
            :Escape, 
            :Bach, :PorcentajeCo, :Opac, :ppmHC, :Freno_FzaIsq, :Freno_FzaDer, :Freno_Dif, :Freno_Eficiencia, :CCCF, :MarcaTac, :NroTac, :RodadoTac, :NroInterno, :CodigoTitular, :DescripcionTitular, :Susp_FzaIsq, :Susp_FzaDer, :Susp_Dif, :Susp_Eficiencia, :Observaciones, :CompaniaSeguro, :NroPoliza, :UltimoRecPatente, :idTipoUso, :usuarioCarga, :idTipoVehiculo, :VMarca, :VModelo, :VAnio, :ChasisMarca, :ChasisAnio, :TipoCombustible, :VPotencia, :NroEjes, :VCaja, :PocisionMotor, :AnioFabricacion, :Carroceria, :Expediente, :AireAco, :Bar, :Banio, :Calefaccion, :Suspencion, :Tara, :PesoMax, :CargaUtil, :Asientos, :idLocTMServ, :TipoServTM, :idTipoServicio, :idClaseServicio, :prestadorServ, :CuitPrestServ, :PTipoDoc, :PNroDoc, :PCuit, :PDomicilio, :PTelefono, :PEmail, :PidLocalidad, :PTipoPersona, :FechaHoraRep, :TipoCarga, :PCodigoPJ, :CertificadoDiscapacidad, :Firma, :nroFactura)";

          $SSQL= $dbUNC->prepare($ComandoSQL);

          // Binds
          //<editor-fold desc="Binds">
          $SSQL->bindValue(':idVerificacion', $row["idVerificacion"]);
          $SSQL->bindValue(':Fecha', $row["Fecha"]);
          $SSQL->bindValue(':Hora', $row["Hora"]);
          $SSQL->bindValue(':HoraFinal', $row["HoraFinal"]);
          $SSQL->bindValue(':idTaller', $row["idTaller"]);
          $SSQL->bindValue(':idFotovalidacion', $row["idFotovalidacion"]);
          $SSQL->bindValue(':DominioVehiculo', $row["DominioVehiculo"]);
          $SSQL->bindValue(':idHabilitacion', $row["idHabilitacion"]);
          $SSQL->bindValue(':codigoHabilitacion', $row["codigoHabilitacion"]);
          $SSQL->bindValue(':ChasisNro', $row["ChasisNro"]);
          $SSQL->bindValue(':MotorAnio', $row["MotorAnio"]);
          $SSQL->bindValue(':MotorMarca', $row["MotorMarca"]);
          $SSQL->bindValue(':MotorNumero', $row["MotorNumero"]);
          $SSQL->bindValue(':idLocalidadVehiculo', $row["idLocalidadVehiculo"]);
          $SSQL->bindValue(':TipoDocConductor', $row["TipoDocConductor"]);
          $SSQL->bindValue(':NroDocConductor', $row["NroDocConductor"]);
          $SSQL->bindValue(':NombreConductor', $row["NombreConductor"]);
          $SSQL->bindValue(':ApellidoConductor', $row["ApellidoConductor"]);
          $SSQL->bindValue(':Reverificacion', $row["Reverificacion"]);
          $SSQL->bindValue(':idVerificacionOriginal', $row["idVerificacionOriginal"]);
          $SSQL->bindValue(':Inspector', $row["Inspector"]);
          $SSQL->bindValue(':DirectorTecnico', $row["DirectorTecnico"]);
          $SSQL->bindValue(':idEstado', $row["idEstado"]);
          $SSQL->bindValue(':Eje1_Tara', $row["Eje1_Tara"]);
          $SSQL->bindValue(':Eje2_Tara', $row["Eje2_Tara"]);
          $SSQL->bindValue(':Eje3_Tara', $row["Eje3_Tara"]);
          $SSQL->bindValue(':Eje4_Tara', $row["Eje4_Tara"]);
          $SSQL->bindValue(':Eje1_FzaIzq', $row["Eje1_FzaIzq"]);
          $SSQL->bindValue(':Eje2_FzaIzq', $row["Eje2_FzaIzq"]);
          $SSQL->bindValue(':Eje3_FzaIzq', $row["Eje3_FzaIzq"]);
          $SSQL->bindValue(':Eje4_FzaIzq', $row["Eje4_FzaIzq"]);
          $SSQL->bindValue(':Eje1_FzaDer', $row["Eje1_FzaDer"]);
          $SSQL->bindValue(':Eje2_FzaDer', $row["Eje2_FzaDer"]);
          $SSQL->bindValue(':Eje3_FzaDer', $row["Eje3_FzaDer"]);
          $SSQL->bindValue(':Eje4_FzaDer', $row["Eje4_FzaDer"]);
          $SSQL->bindValue(':Eje1_Dif', $row["Eje1_Dif"]);
          $SSQL->bindValue(':Eje2_Dif', $row["Eje2_Dif"]);
          $SSQL->bindValue(':Eje3_Dif', $row["Eje3_Dif"]);
          $SSQL->bindValue(':Eje4_Dif', $row["Eje4_Dif"]);
          $SSQL->bindValue(':Eje1_Eficiencia', $row["Eje1_Eficiencia"]);
          $SSQL->bindValue(':Eje2_Eficiencia', $row["Eje2_Eficiencia"]);
          $SSQL->bindValue(':Eje3_Eficiencia', $row["Eje3_Eficiencia"]);
          $SSQL->bindValue(':Eje4_Eficiencia', $row["Eje4_Eficiencia"]);
          $SSQL->bindValue(':Eje5_Tara', $row["Eje5_Tara"]);
          $SSQL->bindValue(':Eje5_FzaIzq', $row["Eje5_FzaIzq"]);
          $SSQL->bindValue(':Eje5_FzaDer', $row["Eje5_FzaDer"]);
          $SSQL->bindValue(':Eje5_Dif', $row["Eje5_Dif"]);
          $SSQL->bindValue(':Eje5_Eficiencia', $row["Eje5_Eficiencia"]);
          $SSQL->bindValue(':Alineacion', $row["Alineacion"]);
          $SSQL->bindValue(':NivelSonoro', $row["NivelSonoro"]);
          $SSQL->bindValue(':Interior', $row["Interior"]);
          $SSQL->bindValue(':Escape', $row["Escape"]);
          $SSQL->bindValue(':Bach', $row["Bach"]);
          $SSQL->bindValue(':PorcentajeCo', $row["PorcentajeCo"]);
          $SSQL->bindValue(':Opac', $row["Opac"]);
          $SSQL->bindValue(':ppmHC', $row["ppmHC"]);
          $SSQL->bindValue(':Freno_FzaIsq', $row["Freno_FzaIsq"]);
          $SSQL->bindValue(':Freno_FzaDer', $row["Freno_FzaDer"]);
          $SSQL->bindValue(':Freno_Dif', $row["Freno_Dif"]);
          $SSQL->bindValue(':Freno_Eficiencia', $row["Freno_Eficiencia"]);
          $SSQL->bindValue(':CCCF', $row["CCCF"]);
          $SSQL->bindValue(':MarcaTac', $row["MarcaTac"]);
          $SSQL->bindValue(':NroTac', $row["NroTac"]);
          $SSQL->bindValue(':RodadoTac', $row["RodadoTac"]);
          $SSQL->bindValue(':NroInterno', $row["NroInterno"]);
          $SSQL->bindValue(':CodigoTitular', $row["CodigoTitular"]);
          $SSQL->bindValue(':DescripcionTitular', $row["DescripcionTitular"]);
          $SSQL->bindValue(':Susp_FzaIsq', $row["Susp_FzaIsq"]);
          $SSQL->bindValue(':Susp_FzaDer', $row["Susp_FzaDer"]);
          $SSQL->bindValue(':Susp_Dif', $row["Susp_Dif"]);
          $SSQL->bindValue(':Susp_Eficiencia', $row["Susp_Eficiencia"]);
          $SSQL->bindValue(':Observaciones', $row["Observaciones"]);
          $SSQL->bindValue(':CompaniaSeguro', $row["CompaniaSeguro"]);
          $SSQL->bindValue(':NroPoliza', $row["NroPoliza"]);
          $SSQL->bindValue(':UltimoRecPatente', $row["UltimoRecPatente"]);
          $SSQL->bindValue(':idTipoUso', $row["idTipoUso"]);
          $SSQL->bindValue(':usuarioCarga', $row["usuarioCarga"]);
          $SSQL->bindValue(':idTipoVehiculo', $row["idTipoVehiculo"]);
          $SSQL->bindValue(':VMarca', $row["VMarca"]);
          $SSQL->bindValue(':VModelo', $row["VModelo"]);
          $SSQL->bindValue(':VAnio', $row["VAnio"]);
          $SSQL->bindValue(':ChasisMarca', $row["ChasisMarca"]);
          $SSQL->bindValue(':ChasisAnio', $row["ChasisAnio"]);
          $SSQL->bindValue(':TipoCombustible', $row["TipoCombustible"]);
          $SSQL->bindValue(':VPotencia', $row["VPotencia"]);
          $SSQL->bindValue(':NroEjes', $row["NroEjes"]);
          $SSQL->bindValue(':VCaja', $row["VCaja"]);
          $SSQL->bindValue(':PocisionMotor', $row["PocisionMotor"]);
          $SSQL->bindValue(':AnioFabricacion', $row["AnioFabricacion"]);
          $SSQL->bindValue(':Carroceria', $row["Carroceria"]);
          $SSQL->bindValue(':Expediente', $row["Expediente"]);
          $SSQL->bindValue(':AireAco', $row["AireAco"]);
          $SSQL->bindValue(':Bar', $row["Bar"]);
          $SSQL->bindValue(':Banio', $row["Banio"]);
          $SSQL->bindValue(':Calefaccion', $row["Calefaccion"]);
          $SSQL->bindValue(':Suspencion', $row["Suspencion"]);
          $SSQL->bindValue(':Tara', $row["Tara"]);
          $SSQL->bindValue(':PesoMax', $row["PesoMax"]);
          $SSQL->bindValue(':CargaUtil', $row["CargaUtil"]);
          $SSQL->bindValue(':Asientos', $row["Asientos"]);
          $SSQL->bindValue(':idLocTMServ', $row["idLocTMServ"]);
          $SSQL->bindValue(':TipoServTM', $row["TipoServTM"]);
          $SSQL->bindValue(':idTipoServicio', $row["idTipoServicio"]);
          $SSQL->bindValue(':idClaseServicio', $row["idClaseServicio"]);
          $SSQL->bindValue(':prestadorServ', $row["prestadorServ"]);
          $SSQL->bindValue(':CuitPrestServ', $row["CuitPrestServ"]);
          $SSQL->bindValue(':PTipoDoc', $row["PTipoDoc"]);
          $SSQL->bindValue(':PNroDoc', $row["PNroDoc"]);
          $SSQL->bindValue(':PCuit', $row["PCuit"]);
          $SSQL->bindValue(':PDomicilio', $row["PDomicilio"]);
          $SSQL->bindValue(':PTelefono', $row["PTelefono"]);
          $SSQL->bindValue(':PEmail', $row["PEmail"]);
          $SSQL->bindValue(':PidLocalidad', $row["PidLocalidad"]);
          $SSQL->bindValue(':PTipoPersona', $row["PTipoPersona"]);
          $SSQL->bindValue(':FechaHoraRep', $row["FechaHoraRep"]);
          $SSQL->bindValue(':TipoCarga', $row["TipoCarga"]);
          $SSQL->bindValue(':PCodigoPJ', $row["PCodigoPJ"]);
          $SSQL->bindValue(':CertificadoDiscapacidad', $row["CertificadoDiscapacidad"]);
          $SSQL->bindValue(':Firma', $row["Firma"]);
          $SSQL->bindValue(':nroFactura', $row["nroFactura"]);
          //</editor-fold>
        }
				else
        {
          $ComandoSQL= "
            UPDATE verificaciones
            SET
              Reverificacion= :Reverificacion, idEstado= :idEstado, FechaHoraRep = NOW(), Firma= :Firma
            WHERE
              idVerificacion = :idVerificacion AND
              idTaller = :idTaller";

          $SSQL= $dbUNC->prepare($ComandoSQL);

          // Binds
          //<editor-fold desc="Binds">
          $SSQL->bindValue(':idVerificacion', $row["idVerificacion"]);
          $SSQL->bindValue(':idTaller', $row["idTaller"]);
          $SSQL->bindValue(':idEstado', $row["idEstado"]);
          $SSQL->bindValue(':Firma', $row["Firma"]);
          $SSQL->bindValue(':Reverificacion', $row["Reverificacion"]);

				}
					

        //</editor-fold>


        try
        {
          $Res = $SSQL->execute();

          if (!$Res)
          {
            echo "Error al replicar verificacion " . $row["idVerificacion"] . "<br />";

            print_r($SSQL->errorInfo());
            exit();
          }

          // Ahora a marcar como replicado
          $ComandoSQL = " 
            UPDATE verificaciones
            SET
              Replicado= 1
            WHERE              
              idVerificacion = :idVerificacion AND
              idTaller = :idTaller";

          $SSQL = $base->prepare($ComandoSQL);

          $SSQL->bindValue(':idVerificacion', $row["idVerificacion"]);
          $SSQL->bindValue(':idTaller', $row["idTaller"]);

          $Res = $SSQL->execute();


          if (!$Res)
          {
            print_r($SSQL->errorInfo());
            exit();
          }


          $cantVeraC++;
        }
        catch (Exception $e)
        {
          exit("ERROR");
          throw $e;
        }
			}
			else{
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un error al buscar personas en central.";
					$continua = false;
					break;
				}
			
		}
	}
	else
  {
    $msnlog = "Error al intentar leer los datos de las verificaciones en la base del Taller: $nomTaller";
    $continua = false;
  }
}

/********* VERIFICACIONESSERVICIOS *************/
if ($continua)
{
  $sql = " SELECT * FROM verificacionesservicios WHERE Replicado = 0 ";
  $respVer = $base->query($sql);

  foreach ($respVer as $RVServ)
  {
    $ComandoSQL =
      " SELECT COUNT(*) as HM  
        FROM verificacionesservicios
        WHERE
          idVerificacionesServicios	= :idVerificacionesServicios	 AND 
          idTaller = :idTaller";

    $SSQL = $dbUNC->prepare($ComandoSQL);

    $SSQL->bindValue(':idVerificacionesServicios	', $RVServ["idVerificacionesServicios"]);
    $SSQL->bindValue(':idTaller', $RVServ["idTaller"]);

    $Res = $SSQL->execute();  // Para ver si anduvo pero no le doy bola
    $R = $SSQL->fetch();

    $HM = $R["HM"];

    if ($HM == 0)
    {
      $ComandoSQL = "
            INSERT INTO verificacionesservicios
            (
                idVerificacionesServicios, idVerificacion, idTaller, idServicio, fechaHoraRep
            ) 
            VALUES            
	          ( 
	              :idVerificacionesServicios, :idVerificacion, :idTaller, :idServicio, now()
	          )";
    }
    else
    {
      $ComandoSQL =
        " UPDATE verificacionesservicios
	          SET        		  
              idVerificacion= :idVerificacion,
              idServicio= :idServicio,
              fechaHoraRep= NOW()
	          WHERE 
	            idVerificacionesServicios = :idVerificacionesServicios AND
	            idTaller =:idTaller";
    }

    // A ejecutar
    $SSQL = $dbUNC->prepare($ComandoSQL);

    $SSQL->bindValue(':idVerificacionesServicios', $RVServ["idVerificacionesServicios"]);
    $SSQL->bindValue(':idVerificacion', $RVServ["idVerificacion"]);
    $SSQL->bindValue(':idTaller', $RVServ["idTaller"]);
    $SSQL->bindValue(':idServicio', $RVServ["idServicio"]);

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
          UPDATE verificacionesservicios 
          SET
            Replicado= 1
          WHERE
            idVerificacionesServicios= :idVerificacionesServicios AND
	          idTaller= :idTaller";

    $SSQL = $base->prepare($ComandoSQL);

    $SSQL->bindValue(':idVerificacionesServicios', $RVServ["idVerificacionesServicios"]);
    $SSQL->bindValue(':idTaller', $RVServ["idTaller"]);

    $Res = $SSQL->execute();
    if (!$Res)
    {
      print_r($SSQL->errorInfo());
      exit();
    }
    else
      $cantVerificacionesServicios++;

  } // foreach
}


if($continua)
{
  $sql = " SELECT * FROM certificados WHERE Replicado = 0 ";
  $respCert = $base->query($sql);
  if ($respCert)
  {
    /***** Por cada certificado vamos a ver si esta el server => update, si no esta => insert ****/
    foreach ($respCert as $RCert)
    {
      $sqlSer = "SELECT * FROM certificados WHERE idCertificado = " . $RCert["idCertificado"] . " AND idTaller = " . $RCert["idTaller"];
      $respServ = $dbUNC->query($sqlSer);
      if ($respServ)
      {
        if ($respServ->rowCount() == 0)
        {
          $ComandoSQL = "
					  INSERT INTO certificados
					  (
					    idCertificado, NroCertificado, Fecha, Hora, idTaller, idEstado, VigenciaHasta, idVerificacion, idConvenio, Anulado, FechaAnulacion, Observaciones, Auditoria, Serie, idCategoria, porcentajeCategoria, FechaHoraRep, Vencido, Reverificado
					  )
					  VALUES
					  (
					    :idCertificado, :NroCertificado, :Fecha, :Hora, :idTaller, :idEstado, :VigenciaHasta, :idVerificacion, :idConvenio, :Anulado, :FechaAnulacion, :Observaciones, :Auditoria, :Serie, :idCategoria, :porcentajeCategoria, NOW(), :Vencido, :Reverificado
					  )";

          // A ejecutar
          $SSQL = $dbUNC->prepare($ComandoSQL);

          $SSQL->bindValue(':idCertificado', $RCert["idCertificado"]);
          $SSQL->bindValue(':NroCertificado', $RCert["NroCertificado"]);
          $SSQL->bindValue(':Fecha', $RCert["Fecha"]);
          $SSQL->bindValue(':Hora', $RCert["Hora"]);
          $SSQL->bindValue(':idTaller', $RCert["idTaller"]);
          $SSQL->bindValue(':idEstado', $RCert["idEstado"]);
          $SSQL->bindValue(':VigenciaHasta', $RCert["VigenciaHasta"]);
          $SSQL->bindValue(':idVerificacion', $RCert["idVerificacion"]);
          $SSQL->bindValue(':idConvenio', $RCert["idConvenio"]);
          $SSQL->bindValue(':Anulado', $RCert["Anulado"]);
          $SSQL->bindValue(':FechaAnulacion', $RCert["FechaAnulacion"]);
          $SSQL->bindValue(':Observaciones', $RCert["Observaciones"]);
          $SSQL->bindValue(':Auditoria', $RCert["Auditoria"]);
          $SSQL->bindValue(':Serie', $RCert["Serie"]);
          $SSQL->bindValue(':idCategoria', $RCert["idCategoria"]);
          $SSQL->bindValue(':porcentajeCategoria', $RCert["porcentajeCategoria"]);
          $SSQL->bindValue(':Vencido', $RCert["Vencido"]);
          $SSQL->bindValue(':Reverificado', $RCert["Reverificado"]);

        }
        else
        {
          $ComandoSQL = " 
            UPDATE certificados
            SET
              idEstado= :idEstado,
              Anulado= :Anulado, 
              FechaAnulacion= :FechaAnulacion,
              Observaciones= :Observaciones, 
              Vencido= :Vencido, 
              Reverificado= :Reverificado, 
              FechaHoraRep = NOW() 
            WHERE 
              idVerificacion = :idVerificacion AND 
              idTaller = :idTaller";

          // A ejecutar
          $SSQL = $dbUNC->prepare($ComandoSQL);

          $SSQL->bindValue(':idVerificacion', $RCert["idVerificacion"]);
          $SSQL->bindValue(':idTaller', $RCert["idTaller"]);
          $SSQL->bindValue(':idEstado', $RCert["idEstado"]);
          $SSQL->bindValue(':Anulado', $RCert["Anulado"]);
          $SSQL->bindValue(':FechaAnulacion', $RCert["FechaAnulacion"]);
          $SSQL->bindValue(':Observaciones', $RCert["Observaciones"]);
          $SSQL->bindValue(':Vencido', $RCert["Vencido"]);
          $SSQL->bindValue(':Reverificado', $RCert["Reverificado"]);

        }

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
        $sql = "UPDATE certificados SET Replicado = 1 WHERE idCertificado = " . $RCert["idCertificado"] . " AND idTaller = " . $RCert["idTaller"];
        if (!$base->query($sql))
        {
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = "Ocurrio un al marcar como replicado un certificado en taller. - Taller: $nomTaller";
          $continua = false;
          break;
        }
        else
        {
          $cantCeraC++;
        }
      }
      else
      {
        /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
        $msnlog = "Ocurrio un error al buscar certificados en central. " . $sqlSer;
        $continua = false;
        break;
      }

    } // foreach

  }
}

