<?php
	require("../../includes/reporteBasePdf.php"); 
	require("../../includes/html2ps/config.inc.php");
	require(HTML2PS_DIR.'pipeline.factory.class.php');
	require("../../includes/html2ps/funciones.php");
	require("../../includes/funciones.php");
	
	$tipo = $GLOBALS['SafiRequestVars']['tipo'];
	$rendicion = $GLOBALS['SafiRequestVars']['rendicionAvance'];
	$avance = $GLOBALS['SafiRequestVars']['avance'];
	$docGenera = $GLOBALS['SafiRequestVars']['docGenera'];
	$elaboradoPor = $GLOBALS['SafiRequestVars']['elaboradoPor'];
	$perfilGerenteDirector = $GLOBALS['SafiRequestVars']['perfilGerenteDirector'];
	$arrFirmas = $GLOBALS['SafiRequestVars']['arrFirmas'];
	$compromiso = $GLOBALS['SafiRequestVars']['compromiso'];
	
	if(
		($tipo=trim($tipo))!='' && $avance instanceof EntidadAvance && $rendicion instanceof EntidadRendicionAvance
	){
		$totalFirmas = 3;
		$mostrarFirmaDirectorEjecutivo = false;
		$mostrarFirmaGerente = false;
		
		/********************************************************************
		 * Incluir la firma del director ejecutivo y del presidente siempre *
		 *******************************************************************/
		$mostrarFirmaDirectorEjecutivo = true;
		$totalFirmas++;
		
		// Verificar si el gerente/director de la dependencia aparece como responsable del avance
		$hayGerente = false;
		foreach ($rendicion->GetResponsablesRendicionAvancePartidas() as $responsableRendicionAvancePartidas)
		{
			$responsableRendicionAvance = $responsableRendicionAvancePartidas->GetResponsableRendicionAvance();
		
			// Obtener los datos del empleado/beneficiario
			if(
				$responsableRendicionAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO
				&& $responsableRendicionAvance->GetEmpleado() != null
			){
				if(
					// Si el gerente/director es un responsable del avance
					strcmp($arrFirmas[$perfilGerenteDirector]['cedula_empleado'], 
						$responsableRendicionAvance->GetEmpleado()->GetId()) == 0
				){
					$hayGerente = true;
					break;
				}
			}
		}
		
		if(
			// Si el gerente/Director no aparece como responsable del avance
			!$hayGerente
			// Y Si la dependencia es la oficina de gestión administrativa y financiera
			&& strcmp($avance->GetDependencia()->GetId(), $arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['id_dependencia']) != 0
			// Y si la dependencia es la oficina planificación, presupuesto y control
			&& strcmp($avance->GetDependencia()->GetId(), $arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['id_dependencia']) != 0
			// Y si la dependencia es distinta de presidencia
			&& strcmp($avance->GetDependencia()->GetId(), $arrFirmas[PERFIL_PRESIDENTE]['id_dependencia']) != 0
			// Y si la dependencia es distinta de dirección ejecutiva
			&& strcmp($avance->GetDependencia()->GetId(), $arrFirmas[PERFIL_DIRECTOR_EJECUTIVO]['id_dependencia']) != 0
			)	
		{
			$mostrarFirmaGerente = true;
			$totalFirmas++;
		}
		
		/**************************************************************************
		 * Fin de incluir la firma del director ejecutivo y del presidente siempre*
		 *************************************************************************/
		
		/**********************************************************************************
		 * Código con la condición de que se muestra la firma del gerente/director solo   *
		 * si un gerente/director de la dependencia aparece como responsable en el avance *
		 * o se muestra la firma del director ejecutivo                                   *
		 *********************************************************************************/
		/*
		// Decidir si se muestra la firma del gerente/director o del director ejecutivo
		if(
			// Si la dependencia del avance es presidencia
			strcmp($rendicion->GetDependencia()->GetId(), $arrFirmas[PERFIL_PRESIDENTE]['cedula_empleado']) == 0
		){
			$mostrarFirmaGerente = false;
			$mostrarFirmaDirectorEjecutivo = false;
		} else {
	
			$hayGerente = false;
			foreach ($rendicion->GetResponsablesRendicionAvancePartidas() as $responsableRendicionAvancePartidas)
			{
				$responsableRendicionAvance = $responsableRendicionAvancePartidas->GetResponsableRendicionAvance();
			
				// Obtener los datos del empleado/beneficiario
				if(
					$responsableRendicionAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO
					&& $responsableRendicionAvance->GetEmpleado() != null
				){
					if(
						// Si el gerente/director es un responsable del avance
						strcmp($arrFirmas[$perfilGerenteDirector]['cedula_empleado'], 
							$responsableRendicionAvance->GetEmpleado()->GetId()) == 0
					){
						$hayGerente = true;
						break;
					}
				}
			}
			
			if($hayGerente)
			{
				$mostrarFirmaDirectorEjecutivo = true;
				$mostrarFirmaGerente = false;
				$totalFirmas++;
			} else if(
				// Si la dependencia es la oficina de gestión administrativa y financiera
				strcmp($avance->GetDependencia()->GetId(), $arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['id_dependencia']) == 0
				// O si la dependencia es la oficina planificación, presupuesto y control
				|| strcmp($avance->GetDependencia()->GetId(), $arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['id_dependencia']) == 0
			) {
				$mostrarFirmaDirectorEjecutivo = false;
				$mostrarFirmaGerente = false;
			} else {
				$mostrarFirmaDirectorEjecutivo = false;
				$mostrarFirmaGerente = true;
				$totalFirmas++;
			}
		}
		*/
		/*****************************************************************************************
		 * Fin de Código con la condición de que se muestra la firma del gerente/director solo   *
		 * si el gerente/director de la dependencia aparece como responsable en el avance        *
		 * o se muestra la firma del director ejecutivo                                          *
		 ****************************************************************************************/
		
		$widthTdFirma = floor(100/$totalFirmas) . "%";
		$colspanElaboradoPor = $totalFirmas;
		
		// Obtener el css del contenido
		$filePath = SAFI_CSS."/pdfContenido.css";
		if(file_exists($filePath) && ($fp = fopen($filePath, "r"))){
			$cssContenido = stream_get_contents($fp);
			fclose($fp);
		}
		
		$contenido = "";
		ob_clean();
?>
<table class="textoTabla firmasAutorizacion" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<?php
			if($mostrarFirmaDirectorEjecutivo || $mostrarFirmaGerente){
				$classLeft1 = "left ";
				$classLeft2 = "";
			} else {
				$classLeft1 = "";
				$classLeft2 = "left ";
			}
		
			if($mostrarFirmaDirectorEjecutivo){
				echo '
		<td class="'.$classLeft1.'top oficina" width="'.$widthTdFirma.'">
			'.$arrFirmas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_cargo_dependencia'].'
		</td>		
					';
			}
			if($mostrarFirmaGerente){
				echo '
		<td class="'.$classLeft1.'top oficina" width="'.$widthTdFirma.'">
			'.$arrFirmas[$perfilGerenteDirector]['nombre_cargo_dependencia'].'
		</td>		
				';
			}
		?>
		<td class="top oficina" width="<?php echo $widthTdFirma; ?>">
			<?php echo $arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['nombre_cargo_dependencia']; ?>
		</td>
		<td class="top oficina" width="<?php echo $widthTdFirma ?>">
			<?php echo $arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_cargo_dependencia']; ?>
		</td>
		<td class="right top oficina" width="<?php echo $widthTdFirma ?>">
			<?php echo $arrFirmas[PERFIL_PRESIDENTE]['nombre_cargo_dependencia']; ?>
		</td>
	</tr>
	<tr>
		<?php
			if($mostrarFirmaDirectorEjecutivo){
				echo '
		<td class="'.$classLeft1.'nombre">'. $arrFirmas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_empleado'].'</td>
					';
			}
			if($mostrarFirmaGerente){
				echo '
		<td class="'.$classLeft1.'nombre">'.$arrFirmas[$perfilGerenteDirector]['nombre_empleado'].'</td>		
				';
			}
		?>
		<td class="nombre"><?php echo $arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['nombre_empleado']; ?></td>
		<td class="nombre"><?php echo $arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_empleado']; ?></td>
		<td class="right nombre"><?php echo $arrFirmas[PERFIL_PRESIDENTE]['nombre_empleado']; ?></td>
	</tr>
</table>
<?php
		$firmasAutorizacion = ob_get_contents();
		ob_clean();
?>
<style type='text/css'>
	@page {
 		margin-top: 30mm;
 		@top-right{
 			margin-top: 35mm;
 			margin-right: 3px;
    		content: 'Página ' counter(page) ' de ' counter(pages);
    		font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
			font-size: 17px;
  		}
	}
</style>
<img width='100%' src='http://safi.infocentro.gob.ve/imagenes/encabezado.jpg'/>

<div style="padding-top: 5px;">
	<div style="float: right; clear: right; width: 152px;">
		<div class="fechaDocumento" style="float: right;">Fecha: <?php 
			$fecha = explode(" ",  $rendicion->GetFechaRendicion());
			echo $fecha[0];
		?></div>
		<div class="numeroPaginas" style="float: right;"></div>
		<div style="clear: both;"></div>
	</div>
	<div class="titulo">
		RENDICIÓN DE AVANCE <?php echo $rendicion->GetId() ?>
	</div>
	<div style="clear: both;"></div>
</div>
<?php
		$headerHtml = ob_get_contents();
		ob_clean();
?>
<style type="text/css">
<?php 
	echo $cssContenido;
?>
</style>

<?php if($docGenera->GetIdEstatus() == "15"){?>
<img  style=' width:20%; left:80%; height:200px;position:absolute;z-index:1000;' src='<?php echo GetConfig("siteURL")?>/imagenes/anulado_safi_3.gif'/>
<?php } ?>

<table class='container textoTabla' border='0' cellspacing='0' cellpadding='0'>
	<tr>
		<td colspan='2' class='subtitulo'>Informaci&oacute;n general</td>
	</tr>
	<tr>
		<td>
			<span class='nombreCampo'>Avance: </span><?php echo $avance->GetId() ?>
		</td>
	<td>
	<tr>
		<td colspan="2" class="right">
			<span class='nombreCampo'>Fecha inicio: </span><?php echo $rendicion->GetFechaInicioActividad() ?> 
			- <span class='nombreCampo'>Fecha fin: </span><?php echo $rendicion->GetFechaFinActividad() ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<span class="NombreCampo"><?php
				if($avance->GetProyecto() != null){
					echo "Proyecto";
				} else if($avance->GetAccionCentralizada() != null){
					echo "Acci&oacute;n centralizada";
				}
			?>: </span>
			<?php
				if($avance->GetProyecto() != null){
					echo $avance->GetProyecto()->GetNombre();
				} else if($avance->GetAccionCentralizada() != null){
					echo $avance->GetAccionCentralizada()->GetNombre();
				}
			?>
			<br />
			<span class="NombreCampo">Acci&oacute;n espec&iacute;fica: </span>
			<?php
				if($avance->GetProyecto() != null && $avance->GetProyectoEspecifica() != null){
					$especifica = $avance->GetProyectoEspecifica(); 
					echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();
				} else if($avance->GetAccionCentralizada() != null && $avance->GetAccionCentralizadaEspecifica() != null){
					$especifica = $avance->GetAccionCentralizadaEspecifica();
					echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();
				}
			?>
		</td>
	</tr>
	<tr>
		<td colspan='2' class="right" style="text-align: justify;"><span class='nombreCampo'>Logros alcanzados: </span>
		<?php echo ToHtmlEncode($rendicion->GetObjetivos()); ?>
		</td>
	</tr>
	<?php
		if (($descripcion=$rendicion->GetDescripcion()) != null && ($descripcion=trim($descripcion)) != ""){
			echo '
	<tr>
		<td colspan="2" class="right" style="text-align: justify;"><span class="nombreCampo">Descripci&oacute;n de la actividad: </span>
		'.ToHtmlEncode($rendicion->GetDescripcion()).'
		</td>
	</tr>
			';
		}
	?>
	<tr>
		<td class="right" colspan='2'>
			<span class="nombreCampo">Nro. participantes: </span><?php echo $avance->GetNroParticipantes();?>
		</td>
	</tr>
	<?php
		if($compromiso != null && false){
	?>
	<tr>
		<td>
			<span class="nombreCampo">Compromiso: </span><?php echo $compromiso->GetId();?>
		</td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td colspan='2' class='subtitulo'>Responsables</td>
	</tr>
	<tr>
		<td colspan="2"><table class="responsables" cellpadding="0" cellspacing="0" border="0" width="100%">
	<?php 
		$total = 0;
		foreach ($rendicion->GetResponsablesRendicionAvancePartidas() as $responsableRendicionAvancePartidas)
		{
			$responsableRendicionAvance = $responsableRendicionAvancePartidas->GetResponsableRendicionAvance();
			$id = "";
			$nombre = "";
			
			// Obtener los datos del empleado/beneficiario
			if(
				$responsableRendicionAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO
				&& $responsableRendicionAvance->GetEmpleado() != null
			){
				$empleado = $responsableRendicionAvance->GetEmpleado();
	
				$id = $empleado->GetId();
				$nombre = mb_strtoupper($empleado->GetNombres() . ' ' .$empleado->GetApellidos(), 'ISO-8859-1'); 
			}
			else if (
				$responsableRendicionAvance->GetTipoResponsable() == EntidadResponsable::TIPO_BENEFICIARIO
				&& $responsableRendicionAvance->GetBeneficiario() != null
			){
				$beneficiario = $responsableRendicionAvance->GetBeneficiario();
				
				$id = $beneficiario->GetId();
				$nombre = mb_strtoupper($beneficiario->GetNombres() . ' ' .$beneficiario->GetApellidos(), 'ISO-8859-1');
			}
			
			echo '
			<tr>
				<td>
					<table class="subResponsables textoTabla" cellspacing="0" cellpadding="0" style="width: 100%;">
						<tr>
							<td><span class="nombreCampo">Nombre: </span>'.$nombre.'</td>
							<td><span class="nombreCampo">C.I.: </span>'.$id.'</td>
							<td class="right"><span class="nombreCampo">Estado: </span>
							'.(
									($responsableRendicionAvance->GetEstado() != null) ? $responsableRendicionAvance->GetEstado()->GetNombre() : ""
								).'
							</td>
						</tr>
					</table>
			';
							
			if(
					is_array($responsableRendicionAvancePartidas->GetRendicionAvancePartidas())
					&& count($responsableRendicionAvancePartidas->GetRendicionAvancePartidas()) > 0
			){
				echo '
					<table class="subResponsables textoTabla" cellspacing="0" cellpadding="0" style="width: 100%;">
						<tr>
							<td class="nombreCampo" style="width: 25%;">Partida</td>
							<td class="nombreCampo" style="width: 25%;">Monto</td>
							<td class="nombreCampo" style="width: 25%;">Partida</td>
							<td class="nombreCampo right" style="width: 25%;">Monto</td>
						</tr>
				';
				
				$countRendicionAvancePartida = 0;
				$esPar = true;
				$subtotal = 0;
				foreach ($responsableRendicionAvancePartidas->GetRendicionAvancePartidas() as $rendicionAvancePartida)
				{
					$idpartida = ($rendicionAvancePartida->GetPartida() ? $rendicionAvancePartida->GetPartida()->GetId() : "");
					
					if($countRendicionAvancePartida % 2 == 0) $esPar = true;
					else $esPar = false;
					
					if($esPar){
						echo '
						<tr>
						';
						$classTdRight = "";
					} else {
						$classTdRight = "right";
					}
					echo '
							<td>'.$idpartida.'</td>
							<td class="'.$classTdRight.'">'.number_format($rendicionAvancePartida->GetMonto(),2,',','.').'</td>
					';
					if(!$esPar){echo '
						</tr>
					';}
					
					$countRendicionAvancePartida++;
				}
				if($esPar){
					echo '
							<td>&nbsp;</td>
							<td class="right">&nbsp;</td>
						</tr>
					';
				}
				
				echo '
					</table>
				';
				
				$rendicionAvanceReintegros = $responsableRendicionAvancePartidas->GetRendicionAvanceReintegros();
				
				if (is_array($rendicionAvanceReintegros)&& count($rendicionAvanceReintegros) > 0)
				{
					echo '
					<table class="subResponsables textoTabla" cellspacing="0" cellpadding="0" style="width: 100%;">
						<tr>
							<td class="nombreCampo">Banco</td>
							<td class="nombreCampo">Nro. referencia</td>
							<td class="nombreCampo">Fecha</td>
							<td class="nombreCampo right">Monto</td>
						</tr>
					';
					
					foreach ($rendicionAvanceReintegros as $rendicionAvanceReintegro)
					{
						$bancoNombre = $rendicionAvanceReintegro->GetBanco() != null ?
							mb_strtoupper($rendicionAvanceReintegro->GetBanco()->GetNombre(), 'ISO-8859-1') : "";
							
							echo '
						<tr>
							<td>'.$bancoNombre.'</td>
							<td>'.$rendicionAvanceReintegro->GetReferencia().'</td>
							<td>'.$rendicionAvanceReintegro->GetFecha().'</td>
							<td>'.number_format($rendicionAvanceReintegro->GetMonto(),2,',','.').'</td>
						</tr>
							';
					}
					
					echo '
					</table>
					';
				}
				
				$montoAnticipo = $responsableRendicionAvancePartidas->GetMontoAnticipo();
				$montoGastado = $responsableRendicionAvancePartidas->GetMontoTotal();
				$montoReintegrado = $responsableRendicionAvancePartidas->GetMontoReintegrado();
				$montoReintegro = $montoAnticipo - $montoGastado;
				$diferencia = $montoReintegro - $montoReintegrado;
				
				$textoEtiquetaReintegro = "Reintegro";
				if($montoAnticipo < $montoGastado - 0.000001)
				{
					$textoEtiquetaReintegro = "Asumido por el trabajador";
					$montoReintegro *= -1;
				}
				else if($montoAnticipo > $montoGastado + 0.000001)
				{
					$textoEtiquetaReintegro = "Reintegro a la Fundaci&oacute;n";
				}
				
				echo '
					<table class="subResponsables textoTabla" cellspacing="0" cellpadding="0" style="width: 100%;">
						<tr>
							<td class="right" style="text-align: right;" width="30%">
								<span class="nombreCampo">Monto anticipo: </span>'. number_format($montoAnticipo,2,',','.').'
							</td>
							<td class="right bottom" style="text-align: right;" width="30%">
								<span class="nombreCampo">Monto gastado: </span>'. number_format($montoGastado,2,',','.').'
							</td>
							<td class="right" style="text-align: right;">
								<span class="nombreCampo">'.$textoEtiquetaReintegro.': </span>'. number_format($montoReintegro,2,',','.').'
							</td>
						</tr>
						<tr>
							<td colspan="2" class="right" style="text-align: right;">
								<span class="nombreCampo">Total reintegrado: </span>'. number_format($montoReintegrado,2,',','.').'
							</td>
							<td class="right bottom" style="text-align: right;">
								<span class="nombreCampo">Diferencia: </span>'. number_format($diferencia,2,',','.').'
							</td>
						</tr>
					</table>
				';
			}
			echo '
				</td>
			</tr>
			';
		}
	?>
		</table></td>
	</tr>
	<?php
		// Imprimir Observaciones si es necesario
		if($rendicion->GetObservaciones() != null && trim(($rendicion->GetObservaciones())) != '')
		{
			echo '
	<tr>
		<td colspan="5" class="subtitulo">Observaciones</td>
	</tr>
	<tr>
		<td colspan="5">'.ToHtmlEncode($rendicion->GetObservaciones()).'</td>
	</tr>
			';
		}
		if(strcmp($tipo, "L") == 0){
			echo '
	<tr>
		<td colspan="5">'.$firmasAutorizacion.'</td>
	</tr>
			';
		}
	?>
</table>
<br /><br />
<table style="font-size: 14px;">
	<tr>
		<td style="width: 95px; font-weight: bold;">Elaborado por:</td>
		<td style="width: 250px; border-bottom: 1px solid #000000;"></td>
	</tr>
	<tr>
		<td></td>
		<td style="text-align: center;"><?php echo $elaboradoPor->GetNombres()." ".$elaboradoPor->GetApellidos(); ?></td>
	</tr>
</table>
<?php
		$contenido .= ob_get_contents();
		ob_clean();
	
		if($tipo=="F"){
			$footer = 	utf8_encode($firmasAutorizacion).
						"<style type='text/css'>
							@page {
						 		@bottom-right {
						 			margin-top: 124mm;
						    		content: 'Página ' counter(page) ' de ' counter(pages);
						  		}
							}
						</style>".
						"<span style='align=center;font-family: arial;font-style:italic;font-weight:bold;font-size: 10pt;'>SAFI - Fundación Infocentro</span><br/>".
						"<span style='align=center;font-family: arial;font-size: 10pt;'>Fecha impresión: ".fecha()."</span>";
			$properties = array("marginBottom" => 105, "headerHtml" => $headerHtml, "footerHtml" => $footer);
			convert_to_pdf($contenido, $properties);
			
		} else if($tipo=="L"){
			$footer = 	"
				<br/>
				<span style='align=center;font-family: arial;font-style:italic;font-weight:bold;font-size: 10pt;'>
					SAFI - Fundación Infocentro
				</span>
				<br/>
				<span style='align=center;font-family: arial;font-size: 10pt;'>
					Fecha impresión: ".fecha()."
				</span>
			";
			$properties = array("marginBottom" => 15, "headerHtml" => $headerHtml, "footerHtml" => $footer);
			
			convert_to_pdf($contenido, $properties);
			
			//echo $headerHtml; echo $contenido;
		}

	}
?>