<?php
/***** VAMOS A ENVIAR LOS DATOS DEL TALLER CENTRAL***********/
if($continua){
	$sql = " SELECT * FROM talleres WHERE Replicado = 0 ";
	$respVer = $base->query($sql);
	if($respVer){
		/***** Por cada taller (deberia ser uno solo) vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{		
			$sqlSer = "SELECT * FROM talleres WHERE idTaller = ".$row["idTaller"];
			if($respServ = $dbUNC->query($sqlSer)){
				if($respServ->rowCount() > 0)	
				{	
					$sqlInSer ="UPDATE talleres SET Nombre = '".$row["Nombre"]."',
								Nrotaller = '".$row["Nrotaller"]."', 
								Direccion = '".$row["Direccion"]."',
								Telefono = '".$row["Telefono"]."',
								Cuit = '".$row["Cuit"]."',
								LicenciaComercial = '".$row["LicenciaComercial"]."',
								ApellidoAd = '".$row["ApellidoAd"]."',
								DNIResponsableAd = '".$row["DNIResponsableAd"]."',
								NombreAd = '".$row["NombreAd"]."',
								FechaHoraRep = NOW() 
								WHERE idTaller = ".$row["idTaller"];				
				}else{
					
					$sqlInSer = "INSERT INTO talleres(idTaller,idLocalidad,Nombre,Nrotaller,
								Direccion,Telefono,Activo,Cuit,LicenciaComercial,ApellidoAd,DNIResponsableAd,NombreAd,FechaHoraRep)
					 VALUES (".$row["idTaller"].",".$row["idLocalidad"].",'".$row["Nombre"]."','".$row["Nrotaller"]."',
					 '".$row["Direccion"]."','".$row["Telefono"]."','".$row["Activo"]."','".$row["Cuit"]."','".$row["LicenciaComercial"]."',
					 '".$row["ApellidoAd"]."','".$row["DNIResponsableAd"]."','".$row["NombreAd"]."',NOW())";
				}
				//echo $sqlInSer."<br />";
				if($dbUNC->query($sqlInSer)){		
					/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
					$sql = "UPDATE talleres SET Replicado = 1 WHERE idTaller = ".$row["idTaller"];
					if(!$base->query($sql)){
						/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
						$msnlog = "Ocurrio error un al marcar como replicado un taller. - Taller: $nomTaller";
						$continua = false;
						break;
					}
				}
				else{
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un error al cargar un taller en central. Taller: $nomTaller";
					$continua = false;
					break;
				}				
			}
			else{
				/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
				$msnlog = "Ocurrio un error al buscar talleres en central. Taller: $nomTaller";
				$continua = false;
				break;
			}
		}/* *** cierra el foreach */
	}else{
		/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
		$msnlog = "Ocurrio un error al buscar el taller para replicar. Taller: $nomTaller";
		$continua = false;
	}
}	

/****************** AHORA VAMOS CON LOS DIRECTORES TECNICOS ************************/

if($continua){
	$inUsuario = "0";
	$sql = " SELECT * FROM direcotrestecnicos WHERE Replicado = 0 AND idTaller = $idTaller ";
	$respVer = $dbUNC->query($sql);
	if($respVer){
		/***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{	
                        $inUsuario = "0";
			$sqlSer = "SELECT * FROM direcotrestecnicos WHERE idDirector = ".$row["idDirector"]." AND idTaller = ".$row["idTaller"];
			if($respServ = $base->query($sqlSer))
			{
                                 if($row["FechaHasta"] != "" && $row["FechaHasta"] != '0000-00-00')
                                {
                                    $row["FechaHasta"] = "'".$row["FechaHasta"]."'";                                    
                                }else{
                                   $row["FechaHasta"] = "NULL"; 
                                }
                            
				if($respServ->rowCount() > 0)	
				{	
					$sqlInSer = "UPDATE direcotrestecnicos SET Apellido = '".$row["Apellido"]."', Nombre = '".$row["Nombre"]."',
								Matricula = '".$row["Matricula"]."', Curriculum = '".$row["Curriculum"]."', 
								FechaDesde = '".$row["FechaDesde"]."',FechaHasta  = ".$row["FechaHasta"].",
								FechaHoraRep = NOW(), Cuit='".$row["Cuit"]."',Activo=".$row["Activo"]."    
								WHERE idDirector = ".$row["idDirector"]." AND idTaller = ".$row["idTaller"];				
				}
				else{
					$inUsuario = "1";
					$sqlInSer = "INSERT INTO direcotrestecnicos (idDirector,idTaller,Apellido,Nombre,Matricula,Curriculum,FechaDesde,FechaHasta,FechaHoraRep,Cuit,Activo) VALUES
							(".$row["idDirector"].",".$row["idTaller"].",'".$row["Apellido"]."','".$row["Nombre"]."','".$row["Matricula"]."',
							'".$row["Curriculum"]."','".$row["FechaDesde"]."',".$row["FechaHasta"].",NOW(),'".$row["Cuit"]."',".$row["Activo"].")";
				}
				//echo $sqlInSer."<br />";
				if($base->query($sqlInSer)){		
					/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
					$sql = "UPDATE direcotrestecnicos SET Replicado = 1 WHERE idDirector = ".$row["idDirector"]." AND idTaller = ".$row["idTaller"];
					if(!$dbUNC->query($sql)){
						/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
						$msnlog = "Ocurrio un al marcar como replicado us director tecnico en taller. - Taller: $nomTaller";
						$continua = false;
						break;
					}
					
					if($inUsuario == "1")
					{					
						$sqlInSer = "INSERT INTO usuarios (Usuario,idPerfil,Password,Activo,Apellido,Nombre) VALUES
							('".$row["Usuario"]."',2,'".$row["ClaveInicial"]."',1,'".$row["Apellido"]."','".$row["Nombre"]."')";
						
						if(!$base->query($sqlInSer)){
							/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
							$msnlog = "Ocurrio un error al cargar un usuario en taller. - Taller: $nomTaller";
							$continua = false;
							break;
						}
						
						$idUsu = $base->lastInsertId();
						
						$sqlInSer = "INSERT INTO usuariostaller (idUsuario,idTaller) VALUES
                                                                    ($idUsu,".$row["idTaller"].")";
						
						if(!$base->query($sqlInSer)){
							/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
							$msnlog = "Ocurrio un error al cargar un usuario en taller. - Taller: $nomTaller";
							$continua = false;
							break;
						}
												
						$sqlInSer = "INSERT INTO directoresusuarios (idUsuario,idTaller,idDirector) VALUES
									($idUsu,".$row["idTaller"].",".$row["idDirector"].")";
						
						if(!$base->query($sqlInSer)){
							/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
							$msnlog = "Ocurrio un error al cargar un usuario en taller. - Taller: $nomTaller";
							$continua = false;
							break;
						}
					}
					
					
                                        $sqlInSer = "UPDATE usuarios SET Activo = ".$row["Activo"]." WHERE idUsuario = (SELECT idUsuario FROM directoresusuarios "
                                                . "  WHERE idTaller = ".$row["idTaller"]." AND idDirector= ".$row["idDirector"].")";

                                            if(!$base->query($sql)){
                                                    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
                                                    $msnlog = "Ocurrio un error al dar de baja un usuario en taller. - Taller: $nomTaller";
                                                    $continua = false;
                                                    break;
                                            }
					
				}
				else{
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un error al cargar un director tecnico en el taller. Taller: $nomTaller";
					$continua = false;
					break;
				}
			}else{
				/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
				$msnlog = "Ocurrio un error al buscar directores tecnicos en el taller. Taller: $nomTaller";
				$continua = false;
				break;
			}
		}/* *** cierra el foreach */
	}else{
		/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
		$msnlog = "Ocurrio un error al buscar direcotres tecnicos en central para replicar. - Taller: $nomTaller";
		$continua = false;
	}

}

/****************** AHORA VAMOS CON LOS INSPECTORES ************************/
if($continua){
$sql = " SELECT * FROM inspectores WHERE Replicado = 0 AND idTaller = $idTaller";
	$respVer = $dbUNC->query($sql);
	if($respVer){
		/***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{			
			$sqlSer = "SELECT * FROM inspectores WHERE idInspector = ".$row["idInspector"]." AND idTaller = ".$row["idTaller"];
			if($respServ = $base->query($sqlSer))
			{
                                if($row["FechaHasta"] != "" && $row["FechaHasta"] != '0000-00-00')
                                {
                                    $row["FechaHasta"] = "'".$row["FechaHasta"]."'";                                    
                                }else{
                                   $row["FechaHasta"] = "NULL"; 
                                }
                                
				if($respServ->rowCount() > 0)	
				{	
                                    $sqlInSer = "UPDATE inspectores SET Apellido = '".$row["Apellido"]."', Nombre = '".$row["Nombre"]."',
                                                    Matricula = '".$row["Matricula"]."', Curriculum = '".$row["Curriculum"]."', 
                                                    FechaDesde = '".$row["FechaDesde"]."',FechaHasta  = ".$row["FechaHasta"].",
                                                    FechaHoraRep = NOW(),Cuit='".$row["Cuit"]."',Activo=".$row["Activo"]."
                                                    WHERE idInspector = ".$row["idInspector"]." AND idTaller = ".$row["idTaller"];				
				}
				else{
			
                                    $sqlInSer = "INSERT INTO inspectores (idInspector,idTaller,Apellido,Nombre,Matricula,Curriculum,FechaDesde,FechaHasta,FechaHoraRep,Cuit,Activo) VALUES
							(".$row["idInspector"].",".$row["idTaller"].",'".$row["Apellido"]."','".$row["Nombre"]."','".$row["Matricula"]."',
							'".$row["Curriculum"]."','".$row["FechaDesde"]."',".$row["FechaHasta"].",NOW(),'".$row["Cuit"]."',".$row["Activo"].") ";
				}
				//echo $sqlInSer."<br />";
				if($base->query($sqlInSer))
                                {		
                                    /**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
                                    $sql = "UPDATE inspectores SET Replicado = 1 WHERE idInspector = ".$row["idInspector"]." AND idTaller = ".$row["idTaller"];
                                    if(!$dbUNC->query($sql)){
                                            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
                                            $msnlog = "Ocurrio un al marcar como replicado un inspector en taller. - Taller: $nomTaller";
                                            $continua = false;
                                            break;
                                    }
				}
				else{
                                    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
                                    $msnlog = "Ocurrio un error al cargar un inspector en central. Taller: $nomTaller";
                                    $continua = false;
                                    break;
				}
			}else{
                            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
                            $msnlog = "Ocurrio un error al buscar inspector en central. Taller: $nomTaller";
                            $continua = false;
                            break;
			}
		}/* *** cierra el foreach */
	}else{
            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
            $msnlog = "Ocurrio un error al buscar inspectores en el taller para replicar. - Taller: $nomTaller";
            $continua = false;
	}
}


/****************** AHORA VAMOS CON LOS ADMINSTRATIVOS ************************/
if($continua){
	$inAdmins = "0";
	$sql = " SELECT * FROM administrativos WHERE Replicado = 0 AND idTaller = $idTaller";
	$respVer = $dbUNC->query($sql);
	if($respVer){
		/***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{
                        $inAdmins = "0";
			$sqlSer = "SELECT * FROM administrativos WHERE idAdministrativo = ".$row["idAdministrativo"]." AND idTaller = ".$row["idTaller"];
			if($respServ = $base->query($sqlSer))
			{
                                                      
				if($respServ->rowCount() > 0)
				{
                                    $sqlInSer = "UPDATE administrativos SET Apellido = '".$row["Apellido"]."', Nombre = '".$row["Nombre"]."',								
                                                            FechaDesde = '".$row["FechaDesde"]."',FechaHasta  = '".$row["FechaHasta"]."',
                                                            FechaHoraRep = NOW(), Cuit='".$row["Cuit"]."',Activo=".$row["Activo"]."
                                                            WHERE idAdministrativo = ".$row["idAdministrativo"]." AND idTaller = ".$row["idTaller"];
				}
				else{
                                    $inAdmins = "1";
                                    $sqlInSer = "INSERT INTO administrativos (idAdministrativo,idTaller,Apellido,Nombre,FechaDesde,FechaHasta,FechaHoraRep,Cuit,Activo) VALUES
                                                    (".$row["idAdministrativo"].",".$row["idTaller"].",'".$row["Apellido"]."','".$row["Nombre"]."',
                                                    '".$row["FechaDesde"]."','".$row["FechaHasta"]."',NOW(),'".$row["Cuit"]."',".$row["Activo"].")";
				}
				//echo $sqlInSer."<br />";
				if($base->query($sqlInSer)){
					/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/
					$sql = "UPDATE administrativos SET Replicado = 1 WHERE idAdministrativo = ".$row["idAdministrativo"]." AND idTaller = ".$row["idTaller"];
					if(!$dbUNC->query($sql)){
                                            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
                                            $msnlog = "Ocurrio un al marcar como replicado un administrativo en taller. - Taller: $nomTaller";
                                            $continua = false;
                                            break;
					}
						
					if($inAdmins == "1")
					{						
                                            $sqlInAd = "INSERT INTO usuarios (Usuario,idPerfil,Password,Activo,Apellido,Nombre) VALUES
                                                    ('".$row["Usuario"]."',3,'".$row["ClaveInicial"]."',1,'".$row["Apellido"]."','".$row["Nombre"]."')";

                                            if(!$base->query($sqlInAd)){
                                                    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
                                                    $msnlog = "Ocurrio un error al cargar un usuario administrativo en taller. - Taller: $nomTaller";
                                                    $continua = false;
                                                    break;
                                            }

                                            $idUsuA = $base->lastInsertId();

                                            $sqlInSer = "INSERT INTO usuariostaller (idUsuario,idTaller) VALUES
                                            ($idUsuA,".$row["idTaller"].")";

                                            if(!$base->query($sqlInSer)){
                                                    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
                                                    $msnlog = "Ocurrio un error al cargar un usuario administrativo en taller. - Taller: $nomTaller";
                                                    $continua = false;
                                                    break;
                                            }
						
					}
						
					$sqlUPAd = "UPDATE usuarios SET Activo = ".$row["Activo"]." WHERE Usuario='".$row["Usuario"]."'";

                                        if(!$base->query($sqlUPAd)){
                                            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
                                            $msnlog = "Ocurrio un error al dar de baja un usuario en taller. - Taller: $nomTaller";
                                            $continua = false;
                                            break;
                                        }
						
				}
				else{
                                    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
                                    $msnlog = "Ocurrio un error al cargar un director tecnico en el taller. Taller: $nomTaller";
                                    $continua = false;
                                    break;
				}
			}else{
                            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
                            $msnlog = "Ocurrio un error al buscar directores tecnicos en el taller. Taller: $nomTaller";
                            $continua = false;
                            break;
			}
		}/* *** cierra el foreach */
	}else{
            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
            $msnlog = "Ocurrio un error al buscar direcotres tecnicos en central para replicar. - Taller: $nomTaller";
            $continua = false;
	}

}



if($continua){
$sql = " SELECT * FROM categoriastalleres WHERE Replicado = 0 ";
$respVer = $base->query($sql);
	if($respVer){
		/***** Por cada categoriaequipo vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{			
			$sqlSer = "SELECT * FROM categoriastalleres WHERE idCategoria = ".$row["idCategoria"]." AND idTaller = ".$row["idTaller"];
			if($respServ = $dbUNC->query($sqlSer))
			{
				if($respServ->rowCount() > 0)	
				{	
					$sqlInSer = "UPDATE categoriastalleres SET Porcentaje = ".$row["Porcentaje"].", FechaHoraRep = NOW()
								WHERE idCategoria = ".$row["idCategoria"]." AND idTaller = ".$row["idTaller"];				
				}
				else{
			
				$sqlInSer = "INSERT INTO categoriastalleres (idCategoria,idTaller,Porcentaje,FechaHoraRep) VALUES
							(".$row["idCategoria"].",".$row["idTaller"].",".$row["Porcentaje"].",NOW()) ";
				}
				//echo $sqlInSer."<br />";
				if($dbUNC->query($sqlInSer)){		
					/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
					$sql = "UPDATE categoriastalleres SET Replicado = 1 WHERE idCategoria = ".$row["idCategoria"]." AND idTaller = ".$row["idTaller"];
					if(!$base->query($sql)){
						/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
						$msnlog = "Ocurrio un al marcar como replicado un registro de categoriaTaller en taller. - Taller: $nomTaller";
						$continua = false;
						break;
					}
				}
				else{
					/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
					$msnlog = "Ocurrio un error al cargar un registro de categoriaTaller en central. Taller: $nomTaller";
					$continua = false;
					break;
				}
			}else{
				/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
				$msnlog = "Ocurrio un error al buscar registros de categoriaTaller en central. Taller: $nomTaller";
				$continua = false;
				break;
			}
		}/* *** cierra el foreach */
	}else{
		/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
		$msnlog = "Ocurrio un error al buscar registros de categoriaTaller en el taller para replicar. - Taller: $nomTaller";
		$continua = false;
	}


}

/****************** INSTALACIONES ************************/
if($continua)
{  
  $hmInstalaciones= 0;
  
  $sql = " SELECT * FROM instalaciones WHERE Replicado = 0 AND idTaller = $idTaller";
  
//  echo $sql; die;
  
	$respVer = $dbUNC->query($sql);
	if($respVer)
  {
		/***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
		foreach ($respVer as $row)
		{			
			$sqlSer = "SELECT * FROM instalaciones WHERE idInstalacion = ".$row["idInstalacion"]." AND idTaller = ".$row["idTaller"];
			if($respServ = $base->query($sqlSer))
			{                                
				if($respServ->rowCount() > 0)	
				{	
          $sqlInSer = 
            "UPDATE instalaciones SET Descripcion = '".$row["Descripcion"]."', 
            Activo=".$row["Activo"]."
            WHERE idInstalacion = ".$row["idInstalacion"]." AND idTaller = ".$row["idTaller"];				
        }
				else
        {
          $sqlInSer = 
            "INSERT INTO instalaciones (idInstalacion,idTaller,Descripcion,Activo) VALUES
						(".$row["idInstalacion"].",".$row["idTaller"].",'".$row["Descripcion"]."',".$row["Activo"].") ";
        }
				
        if($base->query($sqlInSer))
        {		
          /**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
          $sql = "UPDATE instalaciones SET Replicado = 1 WHERE idInstalacion = ".$row["idInstalacion"]." AND idTaller = ".$row["idTaller"];
          if(!$dbUNC->query($sql)){
                  /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
                  $msnlog = "Ocurrio un al marcar como replicado una instalacion en taller. - Taller: $nomTaller";
                  $continua = false;
                  break;
          }
				}
				else
        {
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = "Ocurrio un error al cargar una instalacion en central. Taller: $nomTaller";
          $continua = false;
          break;
				}
			}else
      {
        /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
        $msnlog = "Ocurrio un error al buscar instalacion en central. Taller: $nomTaller";
        $continua = false;
        break;
			}
      
      $hmInstalaciones++;
      
		}/* *** cierra el foreach */
	}else
   {
    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
    $msnlog = "Ocurrio un error al buscar instalaciones en el taller para replicar. - Taller: $nomTaller";
    $continua = false;
	}
}
