<?php
/***** VAMOS A ENVIAR LAS AUDITORIAS CENTRAL***********/
/***** buscamos en el taller las auditorias cargados que no se han replicado ****/

if($continua){
	$sql = " SELECT * FROM auditorias WHERE Replicado = 0 ";
	$respVer = $base->query($sql);
	if($respVer){
		/***** Por cada auditoria vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{		
			$sqlSer = "SELECT * FROM auditorias WHERE idAuditoria = ".$row["idAuditoria"]." AND 
										idTaller = ".$row["idTaller"];
			if($respServ = $dbUNC->query($sqlSer)){
				if($respServ->rowCount() > 0)	
				{					
					$sqlInSer ="UPDATE auditorias SET Observaciones = '".$row["Observaciones"]."', FechaHoraRep = NOW() 
								WHERE idAuditoria = ".$row["idAuditoria"]." AND idTaller = ".$row["idTaller"];				
				}else{
					
					$sqlInSer = "INSERT INTO auditorias(idAuditoria,idTaller,Fecha,Observaciones,FechaHoraRep)
					 VALUES (".$row["idAuditoria"].",".$row["idTaller"].",'".$row["Fecha"]."','".$row["Observaciones"]."',NOW())";
				}
				//echo $sqlInSer."<br />";
				if($dbUNC->query($sqlInSer)){		
					/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
					$sql = "UPDATE auditorias SET Replicado = 1 WHERE idAuditoria = ".$row["idAuditoria"]." AND idTaller = ".$row["idTaller"];
					if(!$base->query($sql)){
						/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
						$msnlog = "Ocurrio un al marcar como replicada una auditoria en taller. - Taller: $nomTaller";
						$continua = false;
						break;
					}else{
						$cantAudAC++;
					}
				}
				else{
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un error al cargar una auditoria en central. Taller: $nomTaller";
					$continua = false;
					break;
				}				
			}
			else{
				/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
				$msnlog = "Ocurrio un error al buscar auditorias en central. Taller: $nomTaller";
				$continua = false;
				break;
			}
		}/* *** cierra el foreach */
	}else{
		/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
		$msnlog = "Ocurrio un error al buscar auditorias en el taller para replicar. Taller: $nomTaller";
		$continua = false;
	}
}	

/****************** AHORA VAMOS CON LOS DETALLES DE AUDITORIAEQUIPOS ************************/
if($continua){
$sql = " SELECT * FROM auditoriasequipos WHERE Replicado = 0 ";
	$respVer = $base->query($sql);
	if($respVer){
		/***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{	
					
			$sqlInSer = "INSERT INTO auditoriasequipos (idAuditoria,idTaller,idEquipo,FuncionamientoCorrecto,Observaciones,FechaHoraRep)
							VALUES (".$row["idAuditoria"].",".$row["idTaller"].",".$row["idEquipo"].",'".$row["FuncionamientoCorrecto"]."',
									'".$row["Observaciones"]."',NOW()) ";
			
				//echo $sqlInSer."<br />";
				if($dbUNC->query($sqlInSer)){		
					/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
					$sql = "UPDATE auditoriasequipos SET Replicado = 1 WHERE idAuditoria = ".$row["idAuditoria"]." AND idEquipo = ".$row["idEquipo"]." AND idTaller = ".$row["idTaller"];
					if(!$base->query($sql)){
						/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
						$msnlog = "Ocurrio un al marcar como replicado un registro de auditoriaequipo en taller. - Taller: $nomTaller";
						$continua = false;
						break;
					}
				}
				else{
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un error al cargar un regitro de auditoriaEquipos en central. Taller: $nomTaller";
					$continua = false;
					break;
				}
		}/* *** cierra el foreach */
	}else{
		/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
		$msnlog = "Ocurrio un error al buscar auditoriaEquipos en el taller para replicar. - Taller: $nomTaller";
		$continua = false;
	}

}




