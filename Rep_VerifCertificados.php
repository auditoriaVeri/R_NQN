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



if($continua){
	$sql = " SELECT * FROM verificaciones WHERE Replicado = 0 ";
	$respVer = $base->query($sql);
	if($respVer){
		/***** Por cada verificaciones vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{		
			$sqlSer = "SELECT * FROM verificaciones WHERE idVerificacion = ".$row["idVerificacion"]." AND 
							idTaller = ".$row["idTaller"];
			if($respServ = $dbUNC->query($sqlSer)){
				
				if($row["NroDocConductor"] == "") $row["NroDocConductor"] = 'NULL';
				if($row["Eje1_Tara"] == "") $row["Eje1_Tara"] = 'NULL';
				if($row["Eje2_Tara"] == "") $row["Eje2_Tara"] = 'NULL';
				if($row["Eje3_Tara"] == "") $row["Eje3_Tara"] = 'NULL';
				if($row["Eje4_Tara"] == "") $row["Eje4_Tara"] = 'NULL';
				if($row["Eje5_Tara"] == "") $row["Eje5_Tara"] = 'NULL';
				if($row["Eje1_FzaIzq"] == "") $row["Eje1_FzaIzq"] = 'NULL';
				if($row["Eje2_FzaIzq"] == "") $row["Eje2_FzaIzq"] = 'NULL';
				if($row["Eje3_FzaIzq"] == "") $row["Eje3_FzaIzq"] = 'NULL';
				if($row["Eje4_FzaIzq"] == "") $row["Eje4_FzaIzq"] = 'NULL';
				if($row["Eje5_FzaIzq"] == "") $row["Eje5_FzaIzq"] = 'NULL';
				if($row["Eje1_FzaDer"] == "") $row["Eje1_FzaDer"] = 'NULL';
				if($row["Eje2_FzaDer"] == "") $row["Eje2_FzaDer"] = 'NULL';
				if($row["Eje3_FzaDer"] == "") $row["Eje3_FzaDer"] = 'NULL';
				if($row["Eje4_FzaDer"] == "") $row["Eje4_FzaDer"] = 'NULL';
				if($row["Eje5_FzaDer"] == "") $row["Eje5_FzaDer"] = 'NULL';
				if($row["Eje1_Dif"] == "") $row["Eje1_Dif"] = 'NULL';
				if($row["Eje2_Dif"] == "") $row["Eje2_Dif"] = 'NULL';
				if($row["Eje3_Dif"] == "") $row["Eje3_Dif"] = 'NULL';
				if($row["Eje4_Dif"] == "") $row["Eje4_Dif"] = 'NULL';
				if($row["Eje5_Dif"] == "") $row["Eje5_Dif"] = 'NULL';
				if($row["Eje1_Eficiencia"] == "") $row["Eje1_Eficiencia"] = 'NULL';
				if($row["Eje2_Eficiencia"] == "") $row["Eje2_Eficiencia"] = 'NULL';
				if($row["Eje3_Eficiencia"] == "") $row["Eje3_Eficiencia"] = 'NULL';
				if($row["Eje4_Eficiencia"] == "") $row["Eje4_Eficiencia"] = 'NULL';
				if($row["Eje5_Eficiencia"] == "") $row["Eje5_Eficiencia"] = 'NULL';
				if($row["Interior"] == "") $row["Interior"] = 'NULL';
				if($row["Escape"] == "") $row["Escape"] = 'NULL';
				if($row["Bach"] == "") $row["Bach"] = 'NULL';
				if($row["PorcentajeCo"] == "") $row["PorcentajeCo"] = 'NULL';
				if($row["Opac"] == "") $row["Opac"] = 'NULL';
				if($row["ppmHC"] == "") $row["ppmHC"] = 'NULL';
				if($row["Freno_FzaIsq"] == "") $row["Freno_FzaIsq"] = 'NULL';
				if($row["Freno_FzaDer"] == "") $row["Freno_FzaDer"] = 'NULL';
				if($row["Freno_Dif"] == "") $row["Freno_Dif"] = 'NULL';
				if($row["Freno_Eficiencia"] == "") $row["Freno_Eficiencia"] = 'NULL';
				if($row["Susp_FzaIsq"] == "") $row["Susp_FzaIsq"] = 'NULL';
				if($row["Susp_FzaDer"] == "") $row["Susp_FzaDer"] = 'NULL';
				if($row["Susp_Dif"] == "") $row["Susp_Dif"] = 'NULL';
				if($row["Susp_Eficiencia"] == "") $row["Susp_Eficiencia"] = 'NULL';
				if($row["MotorAnio"] == "") $row["MotorAnio"] = 'NULL';
				if($row["VAnio"] == "") $row["VAnio"] = 'NULL';
				if($row["ChasisAnio"] == "") $row["ChasisAnio"] = 'NULL';
				if($row["VPotencia"] == "") $row["VPotencia"] = 'NULL';
				if($row["VCaja"] == "") $row["VCaja"] = 'NULL';
				if($row["Tara"] == "") $row["Tara"] = 'NULL';
				if($row["PesoMax"] == "") $row["PesoMax"] = 'NULL';
				if($row["CargaUtil"] == "") $row["CargaUtil"] = 'NULL';
				if($row["Asientos"] == "") $row["Asientos"] = 'NULL';
				if($row["idLocTMServ"] == "") $row["idLocTMServ"] = 'NULL';
				if($row["idTipoServicio"] == "") $row["idTipoServicio"] = 'NULL';
				if($row["idClaseServicio"] == "") $row["idClaseServicio"] = 'NULL';
				if($row["PNroDoc"] == "") $row["PNroDoc"] = 'NULL';
				if($row["PidLocalidad"] == "") $row["PidLocalidad"] = 'NULL';
				if($row["NroInterno"] == "") $row["NroInterno"] = 'NULL';
				if($row["idTipoUso"] == "") $row["idTipoUso"] = 'NULL';
				if($row["idVerificacionOriginal"] == "") $row["idVerificacionOriginal"] = 'NULL';
				
				if($respServ->rowCount() > 0)	
				{	
					$sqlInSer ="UPDATE verificaciones SET Reverificacion = ".$row["Reverificacion"].",idEstado = ".$row["idEstado"].", FechaHoraRep = NOW() WHERE idVerificacion = ".$row["idVerificacion"]." AND idTaller = ".$row["idTaller"];				
				}else{
					
					$sqlInSer = "INSERT INTO verificaciones(idVerificacion,idTaller,Fecha,Hora,DominioVehiculo,ChasisNro,MotorNumero,CodigoTitular,DescripcionTitular,
					TipoDocConductor,NroDocConductor,NombreConductor,ApellidoConductor,Inspector,DirectorTecnico,idEstado,Eje1_Tara,
						Eje2_Tara,Eje3_Tara,Eje4_Tara,Eje1_FzaIzq,Eje2_FzaIzq,Eje3_FzaIzq,Eje4_FzaIzq,Eje1_FzaDer,Eje2_FzaDer,Eje3_FzaDer,
						Eje4_FzaDer,Eje1_Dif,Eje2_Dif,Eje3_Dif,Eje4_Dif,Eje1_Eficiencia,Eje2_Eficiencia,Eje3_Eficiencia,Eje4_Eficiencia,
						Alineacion,NivelSonoro,Interior,Escape,Bach,PorcentajeCo,Opac,ppmHC,Freno_FzaIsq,Freno_FzaDer,Freno_Dif,Freno_Eficiencia,
						Susp_FzaIsq,Susp_FzaDer,Susp_Dif,Susp_Eficiencia,CCCF,Observaciones,CompaniaSeguro,NroPoliza,UltimoRecPatente,
						MotorAnio,MotorMarca,idLocalidadVehiculo,MarcaTac,NroTac,NroInterno,idTipoUso,
						Eje5_Tara,Eje5_FzaIzq,Eje5_FzaDer,Eje5_Dif,Eje5_Eficiencia,idTipoVehiculo,VMarca,VModelo,
						VAnio,ChasisMarca,ChasisAnio,TipoCombustible,VPotencia,NroEjes,VCaja,PocisionMotor,Carroceria,Expediente,AireAco,
						Bar,Banio,Calefaccion,Suspencion,Tara,PesoMax,CargaUtil,Asientos,idLocTMServ,TipoServTM,idTipoServicio,idClaseServicio,
						PTipoDoc,PTipoPersona,PNroDoc,PCuit,PDomicilio,PTelefono,PEmail,PidLocalidad,usuarioCarga,TipoCarga,idVerificacionOriginal,
Reverificacion,PCodigoPJ, idHabilitacion, codigoHabilitacion)  
						VALUES 
						(".$row["idVerificacion"].",".$row["idTaller"].",'".$row["Fecha"]."','".$row["Hora"]."','".$row["DominioVehiculo"]."',
						  '".$row["ChasisNro"]."','".$row["MotorNumero"]."','".$row["CodigoTitular"]."','".addslashes($row["DescripcionTitular"])."',
						 '".$row["TipoDocConductor"]."',".$row["NroDocConductor"].",'".addslashes($row["NombreConductor"])."','".addslashes($row["ApellidoConductor"])."',
						 '".addslashes($row["Inspector"])."','".addslashes($row["DirectorTecnico"])."',".$row["idEstado"].",".$row["Eje1_Tara"].",
						".$row["Eje2_Tara"].",".$row["Eje3_Tara"].",".$row["Eje4_Tara"].",".$row["Eje1_FzaIzq"].",".$row["Eje2_FzaIzq"].",
						".$row["Eje3_FzaIzq"].",".$row["Eje4_FzaIzq"].",".$row["Eje1_FzaDer"].",".$row["Eje2_FzaDer"].",".$row["Eje3_FzaDer"].",
						".$row["Eje4_FzaDer"].",".$row["Eje1_Dif"].",".$row["Eje2_Dif"].",".$row["Eje3_Dif"].",".$row["Eje4_Dif"].",".$row["Eje1_Eficiencia"].
						",".$row["Eje2_Eficiencia"].",".$row["Eje3_Eficiencia"].",".$row["Eje4_Eficiencia"].",'".$row["Alineacion"]."','".$row["NivelSonoro"]."',
						".$row["Interior"].",".$row["Escape"].",".$row["Bach"].",".$row["PorcentajeCo"].
						",".$row["Opac"].",".$row["ppmHC"].",".$row["Freno_FzaIsq"].",".$row["Freno_FzaDer"].",".$row["Freno_Dif"].",".$row["Freno_Eficiencia"].
						",".$row["Susp_FzaIsq"].",".$row["Susp_FzaDer"].",".$row["Susp_Dif"].",".$row["Susp_Eficiencia"].",'".addslashes($row["CCCF"])."','".addslashes($row["Observaciones"]).
						"','".addslashes($row["CompaniaSeguro"])."','".addslashes($row["NroPoliza"])."','".addslashes($row["UltimoRecPatente"])."',".$row["MotorAnio"].",'".addslashes($row["MotorMarca"]).
						"',".$row["idLocalidadVehiculo"].",'".addslashes($row["MarcaTac"])."','".addslashes($row["NroTac"])."','".$row["NroInterno"]."',".$row["idTipoUso"].",".
						$row["Eje5_Tara"].",".$row["Eje5_FzaIzq"].",".$row["Eje5_FzaDer"].",".$row["Eje5_Dif"].",".$row["Eje5_Eficiencia"].",".$row["idTipoVehiculo"].",'".
						addslashes($row["VMarca"])."','".addslashes($row["VModelo"])."','".$row["VAnio"]."','".addslashes($row["ChasisMarca"])."','".$row["ChasisAnio"]."','".$row["TipoCombustible"]."','".$row["VPotencia"].
						"',".$row["NroEjes"].",'".$row["VCaja"]."','".$row["PocisionMotor"]."','".addslashes($row["Carroceria"])."','".addslashes($row["Expediente"])."',".$row["AireAco"].",
						".$row["Bar"].",".$row["Banio"].",".$row["Calefaccion"].",'".$row["Suspencion"]."',".$row["Tara"].",".$row["PesoMax"].",".$row["CargaUtil"].","
						.$row["Asientos"].",".$row["idLocTMServ"].",'".$row["TipoServTM"]."',".$row["idTipoServicio"].",".$row["idClaseServicio"].",'".$row["PTipoDoc"]
						."','".$row["PTipoPersona"]."',".$row["PNroDoc"].",'".$row["PCuit"]."','".addslashes($row["PDomicilio"])."','".$row["PTelefono"]."','".addslashes($row["PEmail"]).
						"',".$row["PidLocalidad"].",'".$row["usuarioCarga"]."','".$row["TipoCarga"]."',".$row["idVerificacionOriginal"].",".$row["Reverificacion"].",'".$row["PCodigoPJ"]."'," . $row["idHabilitacion"] . ", '" . $row['codigoHabilitacion'] . "'" . ")";
				}
				//echo $sqlInSer."<br />";
				if($dbUNC->query($sqlInSer)){		
					/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
					$sql = "UPDATE verificaciones SET Replicado = 1 WHERE idVerificacion = ".$row["idVerificacion"]." AND idTaller = ".$row["idTaller"];
					if(!$base->query($sql)){
						/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
						$msnlog = "Ocurrio un al marcar como replicado una verificacion en taller. - Taller: $nomTaller";
						$continua = false;
						break;
					}else{
						$cantVeraC++;
					}
				}
				else{
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un error al cargar las verificaciones en central. Taller: $nomTaller";
					$continua = false;
					break;
				}
			}
			else{
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un error al buscar personas en central.";
					$continua = false;
					break;
				}
			
		}
	}else{
	$msnlog = "Error al intentar leer los datos de las verificaciones en la base del Taller: $nomTaller";
	$continua = false;
	}
}
if($continua){
	$sql = " SELECT * FROM certificados WHERE Replicado = 0 ";
	$respCert = $base->query($sql);
	if($respCert){
	/***** Por cada certificado vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respCert as $row)
		{		
			
			$sqlSer = "SELECT * FROM certificados WHERE idCertificado = ".$row["idCertificado"]." AND idTaller = ".$row["idTaller"];
			if($respServ = $dbUNC->query($sqlSer)){
				if($row["porcentajeCategoria"] == "") $row["porcentajeCategoria"] = 'NULL';
				if($row["idCategoria"] == "") $row["idCategoria"] = 'NULL';
				
				if($respServ->rowCount() > 0)	
				{	
					$sqlInSer ="UPDATE certificados SET idEstado = ".$row["idEstado"].",Anulado = ".$row["Anulado"].",
					         FechaAnulacion='".$row["FechaAnulacion"]."',Observaciones = '".$row["Observaciones"]."', 
					         Vencido = ".$row["Vencido"].", Reverificado = ".$row["Reverificado"].", FechaHoraRep = NOW() 
					  		WHERE idVerificacion = ".$row["idVerificacion"]." AND idTaller = ".$row["idTaller"];				
				}else{
					
					$sqlInSer = "INSERT INTO certificados(idCertificado,idTaller,NroCertificado,Fecha,Hora,idEstado,VigenciaHasta,
										idVerificacion,idCategoria,porcentajeCategoria,Anulado,FechaAnulacion,Observaciones,Auditoria,Vencido,Reverificado) 
										VALUES 
								(".$row["idCertificado"].",".$row["idTaller"].",".$row["NroCertificado"].",'".$row["Fecha"].
								"','".$row["Hora"]."',".$row["idEstado"].",'".$row["VigenciaHasta"]."',".$row["idVerificacion"].
								",".$row["idCategoria"].",".$row["porcentajeCategoria"].",".$row["Anulado"].",'".$row["FechaAnulacion"]."',
								'".$row["Observaciones"]."','".$row["Auditoria"]."',".$row["Vencido"].",".$row["Reverificado"].")";
				}
				//echo $sqlInSer;
				if($dbUNC->query($sqlInSer)){		
					/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
					$sql = "UPDATE certificados SET Replicado = 1 WHERE idCertificado = ".$row["idCertificado"]." AND idTaller = ".$row["idTaller"];
					if(!$base->query($sql)){
						/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
						$msnlog = "Ocurrio un al marcar como replicado un certificado en taller. - Taller: $nomTaller";
						$continua = false;
						break;
					}else{
						$cantCeraC++;
					}
				}
				else{
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un error al cargar los certificados en central. Taller: $nomTaller";
					$continua = false;
					break;
				}
			}
			else{
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un error al buscar certificados en central.";
					$continua = false;
					break;
				}
			
		}
	}else{
	$msnlog = "Error al intentar leer los datos de los certificados en la base del Taller: $nomTaller";
	$continua = false;
	}
}


