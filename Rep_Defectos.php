<?php

/***** Defectos de las verificaciones. Subirlos a central *****/
error_reporting(E_ALL);
ini_set('display_errors', 1);

if($continua)
{
	$sql = " SELECT * FROM verificacionesdefectos WHERE Replicado = 0 AND idTaller = $idTaller";
  //echo $sql; exit;
	$respVer = $base->query($sql);
  if($respVer)
  {
		/***** Por cada defecto un insert en central ****/
		foreach ($respVer as $row)
		{		
      $sqlInSer= 
        " INSERT INTO verificacionesdefectos" .
        " (" .
        "   idVerificacion, idTaller, " .
        "   idDefecto, idNivel, " .
        "   Replicado, " .
        "   FechaHoraRep, " .
        "   DescripcionRubro, DescripcionDefecto, " .
        "   Descripcion" .
        " )" .
        " VALUES " .
        " (" .
        "   " . $row["idVerificacion"] . ", " . $row["idTaller"] . ", " .
        $row["idDefecto"] . ", " . $row["idNivel"] . ", " .
        "1, " .
        "NOW(), '" .
        addslashes($row["DescripcionRubro"]) . "', '" . addslashes($row['DescripcionDefecto']) . "', '" .
        addslashes($row["Descripcion"]) .
        "' )";
        
     // echo "<br />".$sqlInSer."<br />";exit;
			if($dbUNC->query($sqlInSer))
      {		
		  	/**** una vez que cargue en server, le indico en el taller que ya fue replicado ****/		
        $sql = "UPDATE verificacionesdefectos SET replicado = 1 WHERE idDefecto = ".$row["idDefecto"]." AND idTaller = ".$row["idTaller"];
				if(!$base->query($sql))
        {
						/**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
						$msnlog = 
              "Ocurrio un al marcar como replicado un defecto en taller. - Taller: $nomTaller - idDefecto: " . $row["idDefecto"];
						$continua = false;
						break;
				}
        else
        {
          $cantDefectos++;
				}
      }
      else
      {
			  /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
				$msnlog = "Ocurrio un error al cargar los defectos en central. Taller: $nomTaller" . $sqlInSer;
				$continua = false;
				break;
      }								
    }
  }
	else
  {
    /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
    $msnlog = "Ocurrio un error al buscar defectos para replicar . Taller: $nomTaller";
    $continua = false;
  }
} // if($continua)