<?php

/* * *** VAMOS CARGAR CAMBIOS EN TABLAS PARAMETRICAS********** */

if ($continua) 
{
  $numSal = 1;
  $sql = "SELECT MAX(FechaCarga) AS fechacarga FROM rep_tablasparametricas ";
//$mensaje = $sql;
  $resp = $base->query($sql);
  if ($resp) 
  {
    $row = $resp->fetch(PDO::FETCH_ASSOC);
    $sqlUNC = "SELECT * FROM rep_tablasparametricas WHERE FechaCarga > '" . $row["fechacarga"] . "'";
    $respUNC = $dbUNC->query($sqlUNC);
    //echo $sqlUNC;
    if ($respUNC) {
      if ($respUNC->rowCount() > 0) {
        foreach ($respUNC as $rowUNC) {
          $sql = $rowUNC["Texto"];
          //echo $sql;
          if ($base->query($sql)) {
            $fechaAct = date('Y-m-d H:i');
            $sqlRep = " INSERT INTO rep_tablasparametricas(Texto,FechaCarga,Usuario) VALUES 
								('".addslashes($sql)."','$fechaAct','$usu')";
            //echo $sqlRep;
            $base->query($sqlRep);
            $mensaje .= "Se ejecuto correctamente: \\** $sql \\** <br />";
          } else {
            $mensaje .= "Ocurrio un error al ejecutar: \\** $sql \\** <br />";
            $msnlog = "Ocurrio un error actualizando tablas parametricas:  $sql .<br />";
            $continua = false;
            $numSal = 0;
            break;
          }
        }
      } else {
        $mensaje = "No se encontraron datos para actualizar";
        /*$continua = false;*/
        $numSal = 0;
      }
    } else {
      $mensaje = "Error al consultar los datos ";
      $msnlog = "Error al consultar los datos para actualizar tablas parametricas";
      $continua = false;
      $numSal = 0;
    }
  }
}