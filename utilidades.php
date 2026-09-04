<?php
function formatFecha($cadFecha)
{
	if($cadFecha != "")
	{
		$cadFecha = str_replace("/","-",$cadFecha);
		list( $v1, $v2, $v3 ) = explode( "-", $cadFecha);
		return $v3."-".$v2."-".$v1;
	}
	else
		return "";
}

function formatFechaHora($cadFecha){
	if(($cadFecha != "") && ($cadFecha != "0000-00-00 00:00"))
	{
		list($v1, $v2) = explode(" ", $cadFecha);
		list($v3, $v4, $v5) = explode( "-", $v1);
		return $v5."/".$v4."/".$v3." ".$v2;
	}
	else
		return "";
}


function boolSINO($bool){
	
	if(($bool) || ($bool == "1"))
	{
		return "SI";
	}
	return "NO";
}
 
function bool01($bool){
	
	if(($bool))
	{
		return "1";
	}
	return "0";
} 

function _01bool($bool){
	
	if(($bool == "1"))
	{
		return true;
	}
	return false;
} 

function _01Checked($bool){
	
	if(($bool == "1"))
	{
		return "checked";
	}
	return "";
} 

function formatNroCert($nro){
	
	return str_pad($nro,6,"0",STR_PAD_LEFT);
}

function _toUp($str){
	
	return mb_strtoupper($str,'utf-8');
}


function decimalParaMostrar($numero)
{	
	//return str_replace(".",",",$numero);
	return number_format($numero, 2, ",", "");	
}

function decimalParaGuardar($numero)
{
	return str_replace(",",".",$numero);
	
}

function sumarDias($fecha,$dias){
$nuevafecha = strtotime("+$dias day" , strtotime($fecha)) ;
$nuevafecha = date("d/m/Y",$nuevafecha);
return $nuevafecha;
}

function restarDias($fecha,$dias){
$nuevafecha = strtotime("-$dias day" , strtotime($fecha)) ;
$nuevafecha = date("d/m/Y",$nuevafecha);
return $nuevafecha;
}


function sumarMeses($fecha,$meses){
$nuevafecha = strtotime("+$meses month" , strtotime($fecha)) ;
$nuevafecha = date("d/m/Y",$nuevafecha);
return $nuevafecha;
}

function diferenciaDias($FechaD,$FechaH){	
list( $dD, $mD, $yD ) = explode('-', $FechaD);	
list( $dH, $mH, $yH ) = explode('-', $FechaH);	
//calculo timestam de las dos fechas
$timestamp1 = mktime(0,0,0,$mD,$dD,$yD);
$timestamp2 = mktime(0,0,0,$mH,$dH,$yH);

//resto a una fecha la otra
$segundos_diferencia = $timestamp1 - $timestamp2;
//echo $segundos_diferencia;

//convierto segundos en d�as
$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

//obtengo el valor absoulto de los d�as (quito el posible signo negativo)
$dias_diferencia = abs($dias_diferencia);

//quito los decimales a los d�as de diferencia
$dias_diferencia = floor($dias_diferencia);

return $dias_diferencia + 1;

}

function diasEntreFechas($Inicio,$fin){
$dif= (($d*24)+$h).hrs." ".$m."min";
$dif2= $d.$space.dias." ".$h.hrs." ".$m."min";

echo "Diferencia en horas: ".$dif;

echo "Diferencia en dias: ".$dif2;
	
$date1 = new DateTime(formatFecha($Inicio));
$date2 = new DateTime(formatFecha($fin));
$intervalo = $date1->diff($date2);
return $intervalo->format('%a');
	
}

function suma_fechas($fecha,$ndias)      
{       
      if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
           list($dia,$mes,$a�o)=explode("/", $fecha);
         
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))          
      	list($dia,$mes,$a�o)=explode("-",$fecha);
      
        $nueva = mktime(0,0,0, $mes,$dia,$a�o) + $ndias * 24 * 60 * 60;
        $nuevafecha=date("Y-m-d",$nueva);
         
      return ($nuevafecha);
       
}

function compara_fechas($fecha1,$fecha2)
{           

      if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha1))
              list($dia1,$mes1,$a�o1)=explode("/",$fecha1);            

      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha1))
              list($dia1,$mes1,$a�o1)=explode("-",$fecha1);
              
      if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha2))
              list($dia2,$mes2,$a�o2)=explode("/",$fecha2);

      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha2))
              list($dia2,$mes2,$a�o2)=explode("-",$fecha2);
              
        $dif = mktime(0,0,0,$mes1,$dia1,$a�o1) - mktime(0,0,0, $mes2,$dia2,$a�o2);
        
        return ($dif);
}

function fechaEspaniol()
{
$diaSt =date('w'); 
$dia=date('d');$mes=date('n');$ano=date('Y');
$meses=array('','Enero','Febrero',
'Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre',
'Noviembre','Diciembre');

$diass=array('Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado');
	return $diass[$diaSt].', '.$dia.' de '.$meses[$mes].' de '.$ano; 
	
}

function redondearDecimal($num)
{
	return round($num * 100) / 100;
	
}

?>