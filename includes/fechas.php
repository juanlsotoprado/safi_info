<?php
function actual_date ()  
{  
    $week_days = array ("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado");  
    $months = array ("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");  
    $year_now = date ("Y");  
    $month_now = date ("n");  
    $day_now = date ("j");  
    $week_day_now = date ("w");  
    $date = $week_days[$week_day_now] . ", " . $day_now . " de " . $months[$month_now] . " de " . $year_now;   
    return $date;    
} 


//cambia de formato ingl�s a espa�ol
function cambia_esp($fecha){
    ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha);
    $lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
    return $lafecha;
}
// Cambia la fecha de Formato Espa�ol a Fecha de ingl�s
function cambia_ing($fecha){

    ereg( "([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $mifecha);
    $lafecha=$mifecha[3]."-".$mifecha[2]."-".$mifecha[1];
    return $lafecha;
}

//Cambia la fecha para realizar consultas
function cambia_fecha_iso($fecha){

    ereg( "([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $mifecha);
    $lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
    return $lafecha;
  
}


//Convierte la fecha dada en numeros a la larga en letras
function convertir_fecha_letras($dia,$mes,$anno) {

    switch ($mes) {
		case 1:
			$mes_letras = "ENERO";
			break;
		case 2:
			$mes_letras = "FEBRERO";
			break;
		case 3:
			$mes_letras = "MARZO";
			break;
		case 4:
			$mes_letras = "ABRIL";
			break;
		case 5:
			$mes_letras = "MAYO";
			break;
		case 6:
			$mes_letras = "JUNIO";
			break;
		case 7:
			$mes_letras = "JULIO";
			break;
		case 8:
			$mes_letras = "AGOSTO";
			break;
		case 9:
			$mes_letras = "SEPTIEMBRE";
			break;
		case 10:
			$mes_letras = "OCTUBRE";
			break;
		case 11:
			$mes_letras = "NOVIEMBRE";
			break;
		case 12:
			$mes_letras = "DICIEMBRE";
			break;
	}


	$fecha_letras = $dia." DE ".$mes_letras." DE ".$anno;
    return $fecha_letras;
  
}

function convertir_mes_letras($mes) {

    switch ($mes) {
		case 1:
			$mes_letras = "ENERO";
			break;
		case 2:
			$mes_letras = "FEBRERO";
			break;
		case 3:
			$mes_letras = "MARZO";
			break;
		case 4:
			$mes_letras = "ABRIL";
			break;
		case 5:
			$mes_letras = "MAYO";
			break;
		case 6:
			$mes_letras = "JUNIO";
			break;
		case 7:
			$mes_letras = "JULIO";
			break;
		case 8:
			$mes_letras = "AGOSTO";
			break;
		case 9:
			$mes_letras = "SEPTIEMBRE";
			break;
		case 10:
			$mes_letras = "OCTUBRE";
			break;
		case 11:
			$mes_letras = "NOVIEMBRE";
			break;
		case 12:
			$mes_letras = "DICIEMBRE";
			break;
	}

    return $mes_letras;
  
}


function dias_del_mes($anho,$mes){
   if (((fmod($anho,4)==0) and (fmod($anho,100)!=0)) or (fmod($anho,400)==0)) {
       $dias_febrero = 29;
   } else {
       $dias_febrero = 28;
   }
   switch($mes) {
       case 1: return 31; break;
       case 2: return $dias_febrero; break;
       case 3: return 31; break;
       case 4: return 30; break;
       case 5: return 31; break;
       case 6: return 30; break;
       case 7: return 31; break;
       case 8: return 31; break;
       case 9: return 30; break;
       case 10: return 31; break;
       case 11: return 30; break;
       case 12: return 31; break;
   }
}
?>