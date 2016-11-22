<?php
class VistaFechas
{	
	
	/**************************************************************************************************************
	 * 
	 * Esta función coloca los accesos rápidos de los inputs de fecha (hoy, ayer, semana, semana pasada, etc.)
	 * 
	 * Parámetros:
	 * 	- $idInputFechaInicio: id del tag html input que tiene la fecha de inicio
	 * 	- $idInputFechaFin: id del tag html input que tiene la fecha de fin
	 * 	- $srtFormato: Formato en que se desplegaran las fechas. Este formato se construye según la especifición
	 * 		del objeto datepicker de JQuery ($.datepicker.formatDate(...))
	 * 
	 * Nota: Para el correcto funcionamiento de estos accesos rápidos se deben incluir los siguientes archivos:
	 * 	- Este archivo: require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');
	 * 	- Librería de JQuery: <script type="text/javascript" src="<path>/js/lib/jquery/plugins/jquery.min.js"></script>
	 * 	- Plugin ui de JQuery: <script type="text/javascript" src="<path>/js/lib/jquery/plugins/ui.min.js"></script>
	 * 	- css/safi0.2.css: <link type="text/css" href="<path>/css/safi0.2.css" rel="stylesheet" />
	 * 	- js/funciones.js: <script type="text/javascript" src="<path>/js/funciones.js"> </script>
	 * 
	 **************************************************************************************************************/
	public static function ConstruirAccesosRapidosFechas($idInputFechaInicio = null, $idInputFechaFin = null, $srtFormato = null)
	{
		try {
			if($idInputFechaInicio == null)
				throw new Exception("El parametro \"idInputFechaInicio\" es nulo.");
				
			if(($idInputFechaInicio = trim($idInputFechaInicio)) == "")
				throw new Exception("El parametro \"idInputFechaInicio\" está vacío.");
				
			if($idInputFechaFin == null)
				throw new Exception("El parametro \"idInputFechaFin\" es nulo.");
				
			if(($idInputFechaFin = trim($idInputFechaFin)) == "")
				throw new Exception("El parametro \"idInputFechaFin\" está vacío.");
				
			if($srtFormato == null)
				throw new Exception("El parametro \"srtFormato\" es nulo.");
				
			if(($srtFormato = trim($srtFormato)) == "")
				throw new Exception("El parametro \"srtFormato\" está vacío.");
				
			echo '
				<div class="fechaAccesoRapido">
					<a href="javascript:void(0);"
						onclick="setDateToday($(\'#'.$idInputFechaInicio.'\'), $(\'#'.$idInputFechaFin.'\'), \''.$srtFormato.'\');"
					>Hoy</a>&nbsp;&nbsp;
					<a href="javascript:void(0);"
						onclick="setDateYestarday($(\'#'.$idInputFechaInicio.'\'), $(\'#'.$idInputFechaFin.'\'), \''.$srtFormato.'\');"
					>Ayer</a>&nbsp;&nbsp;
					<a href="javascript:void(0);"
						onclick="setDateWeek($(\'#'.$idInputFechaInicio.'\'), $(\'#'.$idInputFechaFin.'\'), \''.$srtFormato.'\');"
					>Semana</a>&nbsp;&nbsp;
					<a href="javascript:void(0);"
						onclick="setDateLastWeek($(\'#'.$idInputFechaInicio.'\'), $(\'#'.$idInputFechaFin.'\'), \''.$srtFormato.'\');"
					>Semana pasada</a>&nbsp;&nbsp;
					<a href="javascript:void(0);"
						onclick="setDateMonth($(\'#'.$idInputFechaInicio.'\'), $(\'#'.$idInputFechaFin.'\'), \''.$srtFormato.'\');"
					>Mes</a>&nbsp;&nbsp;
					<a href="javascript:void(0);"
						onclick="setDateLastMonth($(\'#'.$idInputFechaInicio.'\'), $(\'#'.$idInputFechaFin.'\'), \''.$srtFormato.'\');"
					>Mes pasado</a>&nbsp;&nbsp;
					<a href="javascript:void(0);"
						onclick="setDateYear($(\'#'.$idInputFechaInicio.'\'), $(\'#'.$idInputFechaFin.'\'), \''.$srtFormato.'\');"
					>A&ntilde;o</a>&nbsp;&nbsp;
					<a href="javascript:void(0);"
						onclick="setDateLastYear($(\'#'.$idInputFechaInicio.'\'), $(\'#'.$idInputFechaFin.'\'), \''.$srtFormato.'\');"
					>A&ntilde;o pasado</a>
				</div>
			';
			
		} catch (Exception $e) {
			error_log($e, 0);
		}
	}
}