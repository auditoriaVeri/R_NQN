<?php
/***** VAMOS A ENVIAR LOS DATOS DEL TALLER CENTRAL***********/
if($continua && 0 == 1) // esto no
{
	$sql = " SELECT * FROM talleres WHERE Replicado = 0 ";
	$respVer = $base->query($sql);
	if($respVer){
		/***** Por cada taller (deberia ser uno solo) vamos a ver si esta el server => update, si no esta => insert  ****/
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

if($continua)
{
  $inUsuario = "0";
  $sql = " SELECT * FROM direcotrestecnicos WHERE Replicado = 0 AND idTaller = $idTaller ";
  $respVer = $dbUNC->query($sql);

  if ($respVer)
  {
    /***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
    foreach ($respVer as $row)
    {
      $inUsuario = "0";

      $Usuario = $row["Usuario"];
      $ClaveInicial = $row["ClaveInicial"];

      $base->beginTransaction();

      $sqlSer = "SELECT * FROM direcotrestecnicos WHERE idDirector = " . $row["idDirector"] . " AND idTaller = " . $row["idTaller"];
      if ($respServ = $base->query($sqlSer))
      {
        if ($respServ->rowCount() == 0)
        {
          $inUsuario = "1";

          $ComandoSQL =
            "INSERT INTO direcotrestecnicos
             (
                idDirector, Apellido, Nombre, Matricula, Curriculum, idTaller, 
                FechaDesde, FechaHasta, Replicado, Cuit, Activo, FechaHoraRep
             )
	           VALUES 
	           (					           
	              :idDirector, :Apellido, :Nombre, :Matricula, :Curriculum, :idTaller, 
                :FechaDesde, :FechaHasta, :Replicado, :Cuit, :Activo, NOW()   
             )";


        }
        else
        {
          $ComandoSQL =
            "UPDATE direcotrestecnicos
            SET
              Apellido= :Apellido,
              Nombre= :Nombre,
              Matricula= :Matricula,
              Curriculum= :Curriculum,             
              FechaDesde= :FechaDesde,
              FechaHasta= :FechaHasta,
              Replicado= :Replicado,
              Cuit= :Cuit,
              Activo= :Activo,
              FechaHoraRep= NOW() 
            WHERE 
                idDirector = :idDirector AND
                idTaller = :idTaller";
        }

        $SSQL = $base->prepare($ComandoSQL);

        $SSQL->bindValue(':idDirector', $row["idDirector"]);
        $SSQL->bindValue(':Apellido', $row["Apellido"]);
        $SSQL->bindValue(':Nombre', $row["Nombre"]);
        $SSQL->bindValue(':Matricula', $row["Matricula"]);
        $SSQL->bindValue(':Curriculum', $row["Curriculum"]);
        $SSQL->bindValue(':idTaller', $row["idTaller"]);
        $SSQL->bindValue(':FechaDesde', $row["FechaDesde"]);
        $SSQL->bindValue(':FechaHasta', $row["FechaHasta"]);
        $SSQL->bindValue(':Replicado', $row["Replicado"]);
        $SSQL->bindValue(':Cuit', $row["Cuit"]);
        $SSQL->bindValue(':Activo', $row["Activo"]);

        try
        {
          $Res = $SSQL->execute();

          if (!$Res)
          {
            $base->rollBack();
            print_r($SSQL->errorInfo());
            exit();
          }
        } catch (Exception $e)
        {
          $base->rollBack();
          exit("ERROR");
          throw $e;
        }

        if ($inUsuario == "1")
        {
          $ComandoSQL =
            "INSERT INTO usuarios
              (Usuario, idPerfil, Password, Activo, Apellido, Nombre, Email, Cuit)
	             VALUES
	            (:Usuario, :idPerfil, :Password, :Activo, :Apellido, :Nombre, :Email, :Cuit)";

          $SSQL = $base->prepare($ComandoSQL);

          // $SSQL->bindValue(':idUsuario', );
          $SSQL->bindValue(':Usuario', $Usuario);
          $SSQL->bindValue(':idPerfil', 2);
          $SSQL->bindValue(':Password', $row["ClaveInicial"]);
          $SSQL->bindValue(':Activo', $row["Activo"]);
          $SSQL->bindValue(':Apellido', $row["Apellido"]);
          $SSQL->bindValue(':Nombre', $row["Nombre"]);
          $SSQL->bindValue(':Email', null);
          $SSQL->bindValue(':Cuit', $row["Cuit"]);

          try
          {
            $Res = $SSQL->execute();

            if (!$Res)
            {
              $base->rollBack();
              print_r($SSQL->errorInfo());
              exit();
            }

          } catch (Exception $e)
          {
            $base->rollBack();
            exit("Ocurrio un error al cargar un usuario director técnico en taller $Usuario");
            throw $e;
          }

          $idUsu = $base->lastInsertId();

          $sqlInSer =
            "INSERT INTO usuariostaller (idUsuario,idTaller) 
            VALUES
            ($idUsu," . $row["idTaller"] . ")";

          if (!$base->query($sqlInSer))
          {
            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
            $base->rollBack();
            $msnlog =
              "Ocurrio un error al cargar un usuario director técnico en taller $Usuario 2da parte";
            $continua = false;
            break;
          }

          $sqlInSer = "INSERT INTO directoresusuarios (idUsuario,idTaller,idDirector) VALUES
									($idUsu," . $row["idTaller"] . "," . $row["idDirector"] . ")";

          if (!$base->query($sqlInSer))
          {
            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
            $base->rollBack();
            $msnlog = "Ocurrio un error al cargar un usuario director técnico en taller $Usuario 3ra parte";
            $continua = false;
            break;
          }
        }

        // Vemos el tema del activo
        $sqlInSer =
          "UPDATE usuarios SET Activo = " . $row["Activo"] . " 
            WHERE idUsuario = (SELECT idUsuario FROM directoresusuarios "
          . "  WHERE idTaller = " . $row["idTaller"] . " AND idDirector= " . $row["idDirector"] . ")";

        if (!$base->query($sqlInSer))
        {
          $base->rollBack();
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = "Ocurrio un error al dar de baja/reactivar un usuario en taller. - Taller: $nomTaller";
          $continua = false;
          break;
        }

        //echo $sqlInSer."<br />";

        /**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/
        $sql = "UPDATE direcotrestecnicos SET Replicado = 1, FechaHoraRep= NOW() WHERE idDirector = " . $row["idDirector"] . " AND idTaller = " . $row["idTaller"];
        if (!$dbUNC->query($sql))
        {
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = "Ocurrio un al marcar como replicado us director tecnico en taller. - Taller: $nomTaller";
          $continua = false;
          break;
        }


        $sqlInSer = "UPDATE usuarios SET Activo = " . $row["Activo"] . " WHERE idUsuario = (SELECT idUsuario FROM directoresusuarios "
          . "  WHERE idTaller = " . $row["idTaller"] . " AND idDirector= " . $row["idDirector"] . ")";

        if (!$base->query($sql))
        {
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = "Ocurrio un error al dar de baja un usuario en taller. - Taller: $nomTaller";
          $continua = false;
          break;
        }

        $cantUsuarios++;
        $base->commit();
      }
      else
      {
        /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
        $msnlog = "Ocurrio un error al cargar un director tecnico en el taller. Taller: $nomTaller";
        $continua = false;
        break;
      }
    }
  }
  else
  {
    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
    $msnlog = "Ocurrio un error al buscar direcotres tecnicos en central para replicar. - Taller: $nomTaller";
    $continua = false;
  }

}

/****************** AHORA VAMOS CON LOS INSPECTORES ************************/
if($continua)
{
  $sql = " SELECT * FROM inspectores WHERE Replicado = 0 AND idTaller = $idTaller";
  $respVer = $dbUNC->query($sql);
  if ($respVer)
  {
    /***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
    foreach ($respVer as $row)
    {
      $Usuario = $row["Usuario"];
      $ClaveInicial = $row["ClaveInicial"];

      $base->beginTransaction();

      $sqlSer =
        "SELECT * 
        FROM inspectores 
        WHERE 
            idInspector = " . $row["idInspector"] . " AND idTaller = " . $row["idTaller"];

      $inUsuario = "0";

      if ($respServ = $base->query($sqlSer))
      {
        if ($respServ->rowCount() == 0)
        {
          $inUsuario = "1";

          $ComandoSQL =
            "INSERT INTO inspectores
              (	
                idInspector, idTaller, Apellido, Nombre, Matricula, Curriculum, 
                FechaDesde, FechaHasta, Replicado, Cuit, Activo, FechaHoraRep
              )
              VALUES 
              (
                :idInspector, :idTaller, :Apellido, :Nombre, :Matricula, :Curriculum, 
                :FechaDesde, :FechaHasta, :Replicado, :Cuit, :Activo, NOW()
              )";
        }
        else
        {
          $ComandoSQL =
            "UPDATE inspectores
            SET
              Apellido= :Apellido,
              Nombre= :Nombre,
              Matricula= :Matricula,
              Curriculum= :Curriculum,
              FechaDesde= :FechaDesde,
              FechaHasta= :FechaHasta,
              Replicado= :Replicado,
              Cuit= :Cuit,
              Activo= :Activo,
              FechaHoraRep= NOW()
            WHERE 
                idInspector = :idInspector AND 
                idTaller = :idTaller";
        }

        $SSQL = $base->prepare($ComandoSQL);

        // $SSQL->bindValue(':idUsuario', );
        $SSQL->bindValue(':Apellido', $row["Apellido"]);
        $SSQL->bindValue(':Nombre', $row["Nombre"]);
        $SSQL->bindValue(':Matricula', $row["Matricula"]);
        $SSQL->bindValue(':Curriculum', $row["Curriculum"]);
        $SSQL->bindValue(':FechaDesde', $row["FechaDesde"]);
        $SSQL->bindValue(':FechaHasta', $row["FechaHasta"]);
        $SSQL->bindValue(':Replicado', 1);
        $SSQL->bindValue(':Cuit', $row["Cuit"]);
        $SSQL->bindValue(':Activo', $row["Activo"]);
        $SSQL->bindValue(':idInspector', $row["idInspector"]);
        $SSQL->bindValue(':idTaller', $row["idTaller"]);

        try
        {
          $Res = $SSQL->execute();

          if (!$Res)
          {
            $base->rollBack();
            print_r($SSQL->errorInfo());
            exit();
          }

          $cantUsuarios++;
        } catch (Exception $e)
        {

          exit("Ocurrio un error al cargar un usuario inspector en taller $Usuario");
          throw $e;
        }

        if ($inUsuario == "1")
        {
          $ComandoSQL =
            "INSERT INTO usuarios
              (
                Usuario, idPerfil, Password, Activo, Apellido, Nombre, Email, Cuit
              )
	            VALUES
	            (
	              :Usuario, :idPerfil, :Password, :Activo, :Apellido, :Nombre, :Email, :Cuit
	            )";

          $SSQL = $base->prepare($ComandoSQL);

          // $SSQL->bindValue(':idUsuario', );
          $SSQL->bindValue(':Usuario', $Usuario);
          $SSQL->bindValue(':idPerfil', 12);
          $SSQL->bindValue(':Password', $row["ClaveInicial"]);
          $SSQL->bindValue(':Activo', $row["Activo"]);
          $SSQL->bindValue(':Apellido', $row["Apellido"]);
          $SSQL->bindValue(':Nombre', $row["Nombre"]);
          $SSQL->bindValue(':Email', null);
          $SSQL->bindValue(':Cuit', $row["Cuit"]);

          try
          {
            $Res = $SSQL->execute();

            if (!$Res)
            {
              $base->rollBack();
              print_r($SSQL->errorInfo());
              exit();
            }

          } catch (Exception $e)
          {
            $base->rollBack();
            exit("Ocurrio un error al cargar un usuario director técnico en taller $Usuario");
            throw $e;
          }

          $idUsu = $base->lastInsertId();

          $sqlInSer =
            "INSERT INTO usuariostaller (idUsuario,idTaller) 
            VALUES
            ($idUsu," . $row["idTaller"] . ")";

          if (!$base->query($sqlInSer))
          {
            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
            $base->rollBack();
            $msnlog =
              "Ocurrio un error al cargar un usuario inspector en taller $Usuario 2da parte";
            $continua = false;
            break;
          }

          $sqlInSer = "INSERT INTO inspectoresusuarios (idUsuario,idTaller,idInspector) VALUES
									($idUsu," . $row["idTaller"] . "," . $row["idInspector"] . ")";

          if (!$base->query($sqlInSer))
          {
            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
            $base->rollBack();
            $msnlog = "Ocurrio un error al cargar un usuario director técnico en taller $Usuario 3ra parte";
            $continua = false;
            break;
          }

        }

        // Vemos el tema del activo
        $sqlInSer =
          "UPDATE usuarios SET Activo = " . $row["Activo"] . " 
            WHERE Usuario = '$Usuario'";

        if (!$base->query($sqlInSer))
        {
          $base->rollBack();
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = "Ocurrio un error al dar de baja/reactivar un usuario inspector en taller. - Usuario: $Usuario";
          $continua = false;
          break;
        }

        /**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/
        $sql = "UPDATE inspectores SET Replicado = 1, ReestablecerClave=0,FechaHoraRep='$AhoraST' WHERE idInspector = " . $row["idInspector"] . " AND idTaller = " . $row["idTaller"];
        if (!$dbUNC->query($sql))
        {
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $base->errorCode();
          $msnlog = "Ocurrio un al marcar como replicado un inspector en taller. - Taller: $nomTaller";
          $continua = false;
          break;
        }
      }
      else
      {
        $base->rollBack();
        /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
        $msnlog = "Ocurrio un error al buscar inspectores en taller. ";
        $continua = false;
        break;
      }

      $cantUsuarios++;
      $base->commit();
    }
  }
  else
  {
    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
    $msnlog = "Ocurrio un error al buscar inspectores en el taller para replicar. - Taller: $nomTaller";
    $continua = false;
  }
}


/****************** AHORA VAMOS CON LOS ADMINSTRATIVOS ************************/
if($continua)
{
  $inAdmins = "0";
  $sql = " SELECT * FROM administrativos WHERE Replicado = 0 AND idTaller = $idTaller";
  $respVer = $dbUNC->query($sql);
  if ($respVer)
  {
    /***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
    foreach ($respVer as $row)
    {
      $inAdmins = "0";

      $Usuario = $row["Usuario"];
      $ClaveInicial = $row["ClaveInicial"];

      $base->beginTransaction();

      $sqlSer = "SELECT * FROM administrativos WHERE idAdministrativo = " . $row["idAdministrativo"] . " AND idTaller = " . $row["idTaller"];
      if ($respServ = $base->query($sqlSer))
      {
        if ($respServ->rowCount() == 0)
        {
          $inAdmins = "1";

          $ComandoSQL =
            "INSERT INTO administrativos
              (
                idAdministrativo, idTaller, Apellido, Nombre, FechaDesde, FechaHasta,
                FechaHoraRep, Cuit, Usuario, ClaveInicial, Activo, Replicado
              )
              VALUES
              (
                :idAdministrativo, :idTaller, :Apellido, :Nombre, :FechaDesde, :FechaHasta, 
                :FechaHoraRep, :Cuit, :Usuario, :ClaveInicial, :Activo, :Replicado
              )";
        }
        else
        {
          $ComandoSQL =
            "UPDATE administrativos
            SET
              Apellido= :Apellido,
              Nombre= :Nombre,
              FechaDesde= :FechaDesde,
              FechaHasta= :FechaHasta,
              FechaHoraRep= :FechaHoraRep,
              Cuit= :Cuit,
              Usuario= :Usuario,
              ClaveInicial= :ClaveInicial,
              Activo= :Activo,
              Replicado= :Replicado
            WHERE
             idAdministrativo= :idAdministrativo AND 
             idTaller= :idTaller";
        }

        $SSQL = $base->prepare($ComandoSQL);

        $SSQL->bindValue(':idAdministrativo', $row["idAdministrativo"]);
        $SSQL->bindValue(':idTaller', $row["idTaller"]);
        $SSQL->bindValue(':Apellido', $row["Apellido"]);
        $SSQL->bindValue(':Nombre', $row["Nombre"]);
        $SSQL->bindValue(':FechaDesde', $row["FechaDesde"]);
        $SSQL->bindValue(':FechaHasta', $row["FechaHasta"]);
        $SSQL->bindValue(':FechaHoraRep', $row["FechaHoraRep"]);
        $SSQL->bindValue(':Cuit', $row["Cuit"]);
        $SSQL->bindValue(':Usuario', $row["Usuario"]);
        $SSQL->bindValue(':ClaveInicial', $row["ClaveInicial"]);
        $SSQL->bindValue(':Activo', $row["Activo"]);
        $SSQL->bindValue(':Replicado', 2);

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
          exit("ERROR");
          throw $e;
        }

        if ($inAdmins == "1")
        {
          $ComandoSQL =
            "INSERT INTO usuarios
              (Usuario, idPerfil, Password, Activo, Apellido, Nombre, Email, Cuit)
	             VALUES
	            (:Usuario, :idPerfil, :Password, :Activo, :Apellido, :Nombre, :Email, :Cuit)";

          $SSQL = $base->prepare($ComandoSQL);

          // $SSQL->bindValue(':idUsuario', );
          $SSQL->bindValue(':Usuario', $Usuario);
          $SSQL->bindValue(':idPerfil', 3);
          $SSQL->bindValue(':Password', $row["ClaveInicial"]);
          $SSQL->bindValue(':Activo', $row["Activo"]);
          $SSQL->bindValue(':Apellido', $row["Apellido"]);
          $SSQL->bindValue(':Nombre', $row["Nombre"]);
          $SSQL->bindValue(':Email', null);
          $SSQL->bindValue(':Cuit', $row["Cuit"]);

          try
          {
            $Res = $SSQL->execute();

            if (!$Res)
            {
              print_r($SSQL->errorInfo());
              $base->rollBack();
              exit();
            }
          } catch (Exception $e)
          {
            exit("Ocurrio un error al cargar un usuario administrativo en taller $Usuario");
            throw $e;
          }

          $idUsu = $base->lastInsertId();

          $sqlInSer =
            "INSERT INTO usuariostaller (idUsuario,idTaller) 
            VALUES
            ($idUsu," . $row["idTaller"] . ")";

          if (!$base->query($sqlInSer))
          {
            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
            $base->rollBack();
            $msnlog =
              "Ocurrio un error al cargar un usuario administrativo técnico en taller $Usuario 2da parte";
            $continua = false;
            break;
          }

          $sqlInSer = "INSERT INTO administrativosusuarios (idUsuario,idTaller,idAdministrativo) VALUES
									($idUsu," . $row["idTaller"] . "," . $row["idAdministrativo"] . ")";

          if (!$base->query($sqlInSer))
          {
            /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
            $base->rollBack();
            $msnlog = "Ocurrio un error al cargar un usuario administrativo en taller $Usuario 3ra parte";
            $continua = false;
            break;
          }
        }

        // Vemos el tema del activo
        $sqlInSer =
          "UPDATE usuarios 
            SET Activo = " . $row["Activo"] . " 
            WHERE
               idUsuario = (SELECT idUsuario FROM administrativosusuarios "
          . "  WHERE idTaller = " . $row["idTaller"] . " AND idAdministrativo = " . $row["idAdministrativo"] . ")";

        if (!$base->query($sqlInSer))
        {
          $base->rollBack();
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = "Ocurrio un error al dar de baja/reactivar un usuario en taller. - Usuario: $Usuario";
          $continua = false;
          break;
        }

        /**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/
        $sql =
          "UPDATE administrativos SET Replicado = 1,ReestablecerClave=0, FechaHoraRep='$AhoraST' WHERE idAdministrativo = " . $row["idAdministrativo"] . " AND idTaller = " . $row["idTaller"];

        if (!$dbUNC->query($sql))
        {
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = "Ocurrio un al marcar como replicado un administrativo en central. - Usuario: $Usuario";
          $continua = false;
          break;
        }
      }
      $cantUsuarios++;
      $base->commit();
    }/* *** cierra el foreach */
  }
  else
  {
    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
    $msnlog = "Ocurrio un error al buscar direcotres tecnicos en central para replicar. - Taller: $nomTaller";
    $continua = false;
  }

}


if($continua && 0 == 1)
{
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
if($continua && 0 == 1)
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
