<?php
/***** VAMOS A ENVIAR LOS EQUIPOS Y MANTENIMIENTOS A CENTRAL***********/
/***** buscamos en el taller los equipos cargados que no se han replicado ****/

if($continua){
	$sql = " SELECT * FROM equipos WHERE Replicado = 0 ";
	$respVer = $base->query($sql);
	if($respVer){
		/***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{		
			$sqlSer = "SELECT * FROM equipos WHERE idEquipo = ".$row["idEquipo"]." AND 
							idTaller = ".$row["idTaller"];
			if($respServ = $dbUNC->query($sqlSer)){
				if($respServ->rowCount() > 0)	
				{					
					$sqlInSer ="UPDATE equipos SET NroLinea = ".$row["NroLinea"].",NroSerie = '".$row["NroSerie"].
								"',Tipo = '".$row["Tipo"]."',Descripcion = '".$row["Descripcion"]."',Marca = '".$row["Marca"]."',
								 Modelo = '".$row["Modelo"]."', FechaHoraRep = NOW() WHERE idEquipo = ".$row["idEquipo"]." AND 
																idTaller = ".$row["idTaller"];				
				}else{
					
					$sqlInSer = "INSERT INTO equipos(idEquipo,idTaller,NroSerie,Tipo,Descripcion,Marca,Modelo,NroInterno,NroLinea,
										PeriodicidadMantenimiento,FechaHoraRep)
					 VALUES (".$row["idEquipo"].",".$row["idTaller"].",'".$row["NroSerie"]."','".$row["Tipo"]."',
					 		'".$row["Descripcion"]."','".$row["Marca"]."','".$row["Modelo"]."','".$row["NroInterno"]."',".$row["NroLinea"].",".
							$row["PeriodicidadMantenimiento"].",NOW())";
				}
				//echo $sqlInSer."<br />";
				if($dbUNC->query($sqlInSer)){		
					/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
					$sql = "UPDATE equipos SET Replicado = 1 WHERE idEquipo = ".$row["idEquipo"]." AND idTaller = ".$row["idTaller"];
					if(!$base->query($sql)){
						/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
						$msnlog = "Ocurrio un al marcar como replicado un equipo en taller. - Taller: $nomTaller";
						$continua = false;
						break;
					}else{
						$cantEquiposAC++;
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
				$msnlog = "Ocurrio un error al buscar equipos en central. . Taller: $nomTaller";
				$continua = false;
				break;
			}
		}/* *** cierra el foreach */
	}else{
		/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
		$msnlog = "Ocurrio un error al buscar equipos para replicar. Taller: $nomTaller";
		$continua = false;
	}
}	

/****************** AHORA VAMOS CON LOS CAMBIOS DE LINEA DE LOS EQUIPOS ************************/

if($continua){
$sql = " SELECT * FROM lineastaller WHERE Replicado = 0 ";
	$respVer = $base->query($sql);
	if($respVer){
		/***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{		
			$sqlSer = "SELECT * FROM lineastaller WHERE NroLinea = ".$row["NroLinea"]." AND idTaller = ".$row["idTaller"];
			if($respServ = $dbUNC->query($sqlSer)){
				if($respServ->rowCount() <= 0)	
				{			
					$sqlInSer = "INSERT INTO lineastaller(NroLinea,idTaller,Descripcion)
								VALUES (".$row["NroLinea"].",".$row["idTaller"].",'".$row["Descripcion"]."')";
					
					//echo $sqlInSer."<br />";
					if($dbUNC->query($sqlInSer)){		
						/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
						$sql = "UPDATE lineastaller SET Replicado = 1 WHERE NroLinea = ".$row["NroLinea"]." AND idTaller = ".$row["idTaller"];
						if(!$base->query($sql)){
							/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
							$msnlog = "Ocurrio un al marcar como replicado una linea en taller. - Taller: $nomTaller";
							$continua = false;
							break;
						}					
					}
					else{
						/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
						$msnlog = "Ocurrio un error al cargar las linea de los taller en central. Taller: $nomTaller";
						$continua = false;
						break;
					}
				}
			}
			else{
				/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
				$msnlog = "Ocurrio un error al cargar las linea de los taller en central. Taller: $nomTaller";
				$continua = false;
				break;
			}
		}/* *** cierra el foreach */
	}else{
		/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
		$msnlog = "Ocurrio un error al buscar las linea de los talleres para replicar. Taller: $nomTaller";
		$continua = false;
	}
}


if($continua){
$sql = " SELECT * FROM lineasequipos WHERE Replicado = 0 ";
	$respVer = $base->query($sql);
	if($respVer){
		/***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{		
			if($row["FechaHasta"] == "")  $row["FechaHasta"] = "NULL";
			
			$sqlSer = "SELECT * FROM lineasequipos WHERE idEquipo = ".$row["idEquipo"]." AND idTaller = ".$row["idTaller"]
																." AND NroLinea = ".$row["NroLinea"]." AND FechaDesde = '".$row["FechaDesde"]."'";
			if($respServ = $dbUNC->query($sqlSer)){
				if($respServ->rowCount() > 0)	
				{
					$sqlInSer = "UPDATE lineasequipos SET FechaHasta = '".$row["FechaHasta"]."', FechaHoraRep = NOW() WHERE idEquipo = ".$row["idEquipo"]." AND idTaller = ".$row["idTaller"]
																." AND NroLinea = ".$row["NroLinea"]." AND FechaDesde = '".$row["FechaDesde"]."'";
				}
				else{			
					$sqlInSer = "INSERT INTO lineasequipos (idEquipo,idTaller,NroLinea,FechaDesde,FechaHasta,Observaciones,FechaHoraRep)
							VALUES (".$row["idEquipo"].",".$row["idTaller"].",".$row["NroLinea"].",'".$row["FechaDesde"]."',
							'".$row["FechaHasta"]."','".$row["Observaciones"]."',NOW()) ";
				}
				//echo $sqlInSer."<br />";
				if($dbUNC->query($sqlInSer)){		
					/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
					$sql = "UPDATE lineasequipos SET Replicado = 1 WHERE idEquipo = ".$row["idEquipo"]." AND idTaller = ".$row["idTaller"]
																." AND NroLinea = ".$row["NroLinea"]." AND FechaDesde = '".$row["FechaDesde"]."'";
					if(!$base->query($sql)){
						/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
						$msnlog = "Ocurrio un al marcar como replicado una cambio de linea en taller. - Taller: $nomTaller";
						$continua = false;
						break;
					}					
				}
				else{
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un error al cargar los cambios de linea de los equipos en central. Taller: $nomTaller";
					$continua = false;
					break;
				}
			}
			else{
				/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
				$msnlog = "Ocurrio un error al cargar los cambios de linea de los equipos en central. Taller: $nomTaller";
				$continua = false;
				break;
			}
		}/* *** cierra el foreach */
	}else{
		/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
		$msnlog = "Ocurrio un error al buscar cambios de linea de equipos para replicar. Taller: $nomTaller";
		$continua = false;
	}

}

/****************** REPLICAMOS LOS MANTENIMIENTOS REGISTRADOS DE LOS EQUIPOS ************************/
if($continua){	
	$sql = " SELECT * FROM mantenimientos WHERE Replicado = 0 ";
	$respVer = $base->query($sql);
	if($respVer){
		/***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{	
			$sqlInSer = "INSERT INTO mantenimientos (idMantenimiento,idTaller,idEquipo,Fecha,Observaciones,FechaHoraRep)
							VALUES (".$row["idMantenimiento"].",".$row["idTaller"].",".$row["idEquipo"].",'".$row["Fecha"]."',
							'".$row["Observaciones"]."',NOW()) ";
			
				//echo $sqlInSer."<br />";
				if($dbUNC->query($sqlInSer)){		
					/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
					$sql = "UPDATE mantenimientos SET Replicado = 1 WHERE idMantenimiento = ".$row["idMantenimiento"]." AND idTaller = ".$row["idTaller"];
					if(!$base->query($sql)){
						/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
						$msnlog = "Ocurrio un al marcar como replicado el mantenimiento de un equipo en taller. - Taller: $nomTaller";
						$continua = false;
						break;
					}else{
						$cantMantEqAC++;
					}
				}
				else{
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un error al cargar los mantenimientos de los equipos en central. Taller: $nomTaller";
					$continua = false;
					break;
				}
		}/* *** cierra el foreach */
	}else{
		/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
		$msnlog = "Ocurrio un error al buscar mantenimientos de equipos para replicar. Taller: $nomTaller";
		$continua = false;
	}
	
}


