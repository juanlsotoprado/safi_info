<?php
	require("../../includes/reporteBasePdf.php"); 
	require("../../includes/html2ps/config.inc.php");
	require(HTML2PS_DIR.'pipeline.factory.class.php');
	require("../../includes/html2ps/funciones.php");
	require("../../includes/funciones.php");
	
	$contenido = "";
	ob_clean();
	
	$tipo = $GLOBALS['SafiRequestVars']['tipo'];
	$avance = $GLOBALS['SafiRequestVars']['avance'];
	$docGenera = $GLOBALS['SafiRequestVars']['docGenera'];
	$elaboradoPor = $GLOBALS['SafiRequestVars']['elaboradoPor'];
	$perfilGerenteDirector = $GLOBALS['SafiRequestVars']['perfilGerenteDirector'];
	$arrFirmas = $GLOBALS['SafiRequestVars']['arrFirmas'];
	
	if($tipo!='' && $avance instanceof EntidadAvance)
	{
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
		foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
		{
			$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
		
			// Obtener los datos del empleado/beneficiario
			if(
				$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO
				&& $responsableAvance->GetEmpleado() != null
			){
				if(
					// Si el gerente/director es un responsable del avance
					strcmp($arrFirmas[$perfilGerenteDirector]['cedula_empleado'], $responsableAvance->GetEmpleado()->GetId()) == 0
				){
					$hayGerente = true;
					break;
				}
			}
		}
		
		if(!$hayGerente)	// Si el gerente/Director no aparece como responsable del avance
		{
			if(
				// Si la dependencia es distinta de la oficina de gestión administrativa y financiera
				strcmp($avance->GetDependencia()->GetId(), $arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['id_dependencia']) != 0
				// Y si la dependencia es distinta de la la oficina planificación, presupuesto y control
				&& strcmp($avance->GetDependencia()->GetId(), $arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['id_dependencia']) != 0
				// Y si la dependencia es distinta de presidencia
				&& strcmp($avance->GetDependencia()->GetId(), $arrFirmas[PERFIL_PRESIDENTE]['id_dependencia']) != 0
				// Y si la dependencia es distinta de dirección ejecutiva
				&& strcmp($avance->GetDependencia()->GetId(), $arrFirmas[PERFIL_DIRECTOR_EJECUTIVO]['id_dependencia']) != 0
			) {
				$mostrarFirmaGerente = true;
				$totalFirmas++;
			}
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
			strcmp($avance->GetDependencia()->GetId(), $arrFirmas[PERFIL_PRESIDENTE]['cedula_empleado']) == 0
		){
			$mostrarFirmaGerente = false;
			$mostrarFirmaDirectorEjecutivo = false;
		} else {
	
			$hayGerente = false;
			foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
			{
				$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
			
				// Obtener los datos del empleado/beneficiario
				if(
					$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO
					&& $responsableAvance->GetEmpleado() != null
				){
					if(
						// Si el gerente/director es un responsable del avance
						strcmp($arrFirmas[$perfilGerenteDirector]['cedula_empleado'], $responsableAvance->GetEmpleado()->GetId()) == 0
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
?>
<table class="bordeTabla textoTabla firmasAutorizacion" border="1" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<?php
			if($mostrarFirmaDirectorEjecutivo){
				echo '
		<td class="oficina" width="'.$widthTdFirma.'">
			'.$arrFirmas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_cargo_dependencia'].'
		</td>		
					';
			}
			if($mostrarFirmaGerente){
				echo '
		<td class="oficina" width="'.$widthTdFirma.'">
			'.$arrFirmas[$perfilGerenteDirector]['nombre_cargo_dependencia'].'
		</td>		
				';
			}
		?>
		<td class="oficina" width="<?php echo $widthTdFirma; ?>">
			<?php echo $arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['nombre_cargo_dependencia']; ?>
		</td>
		<td class="oficina" width="<?php echo $widthTdFirma ?>">
			<?php echo $arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_cargo_dependencia']; ?>
		</td>
		<td class="oficina" width="<?php echo $widthTdFirma ?>">
			<?php echo $arrFirmas[PERFIL_PRESIDENTE]['nombre_cargo_dependencia']; ?>
		</td>
	</tr>
	<tr>
		<?php
			if($mostrarFirmaDirectorEjecutivo){
				echo '
		<td class="nombre">'. $arrFirmas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_empleado'].'</td>
					';
			}
			if($mostrarFirmaGerente){
				echo '
		<td class="nombre">'.$arrFirmas[$perfilGerenteDirector]['nombre_empleado'].'</td>		
				';
			}
		?>
		<td class="nombre"><?php echo $arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['nombre_empleado']; ?></td>
		<td class="nombre"><?php echo $arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['nombre_empleado']; ?></td>
		<td class="nombre"><?php echo $arrFirmas[PERFIL_PRESIDENTE]['nombre_empleado']; ?></td>
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

<div class="borde" style="padding-top: 5px;">
	<div class="borde" style="float: right; clear: right; width: 152px;">
		<div class="borde fechaDocumento" style="float: right;">
			Fecha: <?php echo $avance->GetFechaAvance(); ?>
		</div>
		<div class="borde numeroPaginas" style="float: right;"></div>
		<div style="clear: both;"></div>
	</div>
	<div class="titulo borde">
		AVANCE <?php echo $avance->GetId() ?>
	</div>
	<div style="clear: both;"></div>
</div>
<?php
		$headerHtml = ob_get_contents();
		ob_clean();
?>
<style type="text/css">
	.fechaDocumento, .numeroPaginas{
		font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
		font-size: 17px;
		height: 20px;
		width: 150px;
		text-align: right;
	}
	.numeroPaginas{
		margin-top: 5px;
	}
	.borde{
		/*
		border-color: black;
		border-style: solid;
		border-width: 1px;
		*/
	}
	.titulo
	{
		clear: left;
		float: right;
		padding-top: 10px;
		text-align:center;
		width: 70%;
	}
	.titulo,
	.subTitulo{
		font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
		font-size: 22px;
		font-weight: bold;
		text-decoration: none;
	}
	.nombreCampo{
		vertical-align: middle;
		font-weight:bold;
	}
	.nombreCampoTitulo{
		font-weight:bold;							
		vertical-align: middle;
		text-align:center;
		background-color: #CCCCCC;
		height: 25px;
	}
	.nombreCampoTituloSinFondo{
		font-weight:bold;							
		vertical-align: middle;
		text-align:center;
		height: 25px;
	}
	.nombreCampoTituloPequeno{
		font-weight:bold;
		vertical-align: middle;
		text-align:center;
		background-color: #CCCCCC;
		height: 25px;
	}
	.bordeTabla{
		border: solid 1px #000000;
	}
	.sinBordesExternosTabla{
		border: none;
	}
	.textoTabla{
		font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
		font-size: 22px;
		font-weight: normal;
		text-decoration: none;
	}
	.textoPie{
		font-family: arial;
		font-size: 8pt;						
	}
	.alineadoAbajo{
		vertical-align: bottom;
	}
	.alineadoMedio{
		vertical-align: middle;
	}
	.alineadoCentro{
		text-align:center;
	}
	.alineadoDerecha{
		text-align:right;
	}
	.alturaMaxima{
		height: 100px;
	}
	.firmasAutorizacion{
		font-size: 16px;
	}
	.firmasAutorizacion .oficina{
		text-align: center;
		vertical-align: top;
	}
	.firmasAutorizacion .nombre{
		height: 80px;
		text-align: center;
		vertical-align: bottom;
	}
	.firmasAutorizacion .fecha{
		vertical-align: top;
	}
	.firmasAutorizacion .elaboradoPor{
		height: 20px;
	}
	.fechaViatico{
		margin-right:-2px;
		margin-top:-4px;
		border: solid 1px #000000;
		border-bottom: none;
	}
	.infocentros ul{
		margin-bottom: 5px;
		margin-top: 5px;
	}
	.infocentros li{
	
	}
	.responsables{
		border: solid 2px #000000;
	}
	.responsables > table > tbody > tr > td,
	.responsables > table > tr > td{
		border-bottom: solid 1px #000000;
		border-right: solid 1px #000000;
	}
	.responsables td.right{
		border-right: none;
	}
	.responsables td.bottom{
		border-bottom: none;
	}
</style>

<?php if($docGenera->GetIdEstatus() == "15"){?>
<img  style=' width:20%; left:80%; height:200px;position:absolute;z-index:1000;' src='<?php echo GetConfig("siteURL")?>/imagenes/anulado_safi_3.gif'/>
<?php } ?>

<table class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0' width='100%'>
	<tr>
		<td colspan='2' class='nombreCampoTitulo'>Informaci&oacute;n general</td>
	</tr>
	<tr>
		<td colspan='2'>
			<span class='nombreCampo'>Categor&iacute;a: </span>
			<?php
				// Imprime el nombre dela categoría
				echo (($avance->GetCategoria() != null) ? $avance->GetCategoria()->GetNombre() : '' );

				// Imprime el campo de red
				echo (($avance->GetRed() != null) ?
					"&nbsp;&nbsp;<span class='nombreCampo'>
						Red:
					</span>
					".$avance->GetRed()->GetNombre() : '');
			?>
			&nbsp;&nbsp;<span class="nombreCampo">Nro. participantes: </span><?php echo $avance->GetNroParticipantes();?>
		</td>
	</tr>
	<?php
		if(
			$avance->GetPuntoCuenta() != null && $avance->GetPuntoCuenta()->GetId() != null
			&& trim($avance->GetPuntoCuenta()->GetId()) != ''
		){
			echo '
	<tr>
		<td colspan="2">
			<span class="nombreCampo">
				Punto de cuenta:
			</span>'.trim($avance->GetPuntoCuenta()->GetId()).'
		</td>
	</tr>
			';
		}
	?>
	<tr>
		<td colspan="2">
			<span class='nombreCampo'>Fecha inicio: </span><?php echo $avance->GetFechaInicioActividad() ?> 
			- <span class='nombreCampo'>Fecha fin: </span><?php echo $avance->GetFechaFinActividad() ?>
		</td>
	<tr>
	<tr>
		<td colspan="2">
			<span class='nombreCampo'><?php
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
			?><br />
			<span class="nombreCampo">Acci&oacute;n espec&iacute;fica: </span>
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
		<td colspan='2' style="text-align: justify;"><span class='nombreCampo'>Objetivos: </span>
		<?php
			$objetivos = ($avance != null) ? $avance->GetObjetivos() : "";
			echo ToHtmlEncode($objetivos);
		?>
		</td>
	</tr>
	<?php 
		if($avance->GetDescripcion() != null && ($descripcion=trim($avance->GetDescripcion())) != '')
		{
			echo '
	<tr>
		<td colspan="2" style="text-align: justify;"><span class="nombreCampo">Descripci&oacute;n: </span>
			'.ToHtmlEncode($descripcion).'
		</td>
	</tr>
			';
		}
		
		if($avance->GetJustificacion() != null && ($justificacion=trim($avance->GetJustificacion())) != '')
		{
			echo '
	<tr>
		<td colspan="2" style="text-align: justify;"><span class="nombreCampo">Justificaci&oacute;n: </span>'.
			ToHtmlEncode($avance->GetJustificacion()).'</td>
	</tr>
			';
		}
		
		
	?>
	<?php 
		// Imprimir los infocentros si es necesario
		if(is_array($avance->GetInfocentros()) && count($avance->GetInfocentros())>0){
			echo '
	<tr>
		<td colspan="2" class="infocentros">
			<span class="nombreCampo">Infocentros a visitar:</span>
			<ul>
			';
			foreach($avance->GetInfocentros() as $infocentro){
				$label = $infocentro->GetNombre() . ' - ' . $infocentro->GetNemotecnico();
				if(	($parroquia=$infocentro->GetParroquia()) != null &&
					($municipio=$parroquia->GetMunicipio()) != null &&
					($estado=$municipio->GetEstado()) != null
				){
					$label .= ' - ' . $estado->GetNombre();
				}
				if($infocentro->GetEtapa() != ''){
					$label .= ' - ' . $infocentro->GetEtapa();  
				}
				echo '
				<li>'.$label.'</li>
				';
			}
			echo '
			</ul>
		</td>
	</tr>
			';
		}

		echo '
	<tr>
		<td colspan="2" class="nombreCampoTitulo">Responsables</td>
	</tr>
		';
		$total = 0;
		foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
		{
			$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
			$id = "";
			$nombre = "";
			
			// Obtener los datos del empleado/beneficiario
			if(
				$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO
				&& $responsableAvance->GetEmpleado() != null
			){
				$empleado = $responsableAvance->GetEmpleado();

				$id = $empleado->GetId();
				$nombre = mb_strtoupper($empleado->GetNombres() . ' ' .$empleado->GetApellidos(), 'ISO-8859-1'); 
			}
			else if (
				$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_BENEFICIARIO
				&& $responsableAvance->GetBeneficiario() != null
			){
				$beneficiario = $responsableAvance->GetBeneficiario();
				
				$id = $beneficiario->GetId();
				$nombre = mb_strtoupper($beneficiario->GetNombres() . ' ' .$beneficiario->GetApellidos(), 'ISO-8859-1');
			}
			
			// Obtener los datos del tipo de cuanta bancarios
			if(strcmp($responsableAvance->GetTipoCuenta(), EntidadTipoCuentabancaria::CUENTA_DE_AHORRO) == 0)
			{
				$tipoCuenta = "Ahorro";
			}
			elseif (strcmp($responsableAvance->GetTipoCuenta(), EntidadTipoCuentabancaria::CUENTA_CORRRIENTE) == 0)
			{
				$tipoCuenta = "Corriente";
			}
			
			echo '
	<tr>
		<td colspan="2" class="responsables">
			<table class="textoTabla" cellspacing="0" cellpadding="0" style="width: 100%;">
				<tr>
					<td><span class="nombreCampo">Nombre: </span>'.$nombre.'</td>
					<td><span class="nombreCampo">C.I.: </span>'.$id.'</td>
					<td class="right"><span class="nombreCampo">Estado: </span>
					'.(
							($responsableAvance->GetEstado() != null) ? $responsableAvance->GetEstado()->GetNombre() : ""
						).'
					</td>
				</tr>
			</table>
			<table class="textoTabla" cellspacing="0" cellpadding="0" style="width: 100%;">
			';
			if(
				$responsableAvance->GetNumeroCuenta() != null && trim($responsableAvance->GetNumeroCuenta()) != ''
				&& isset($tipoCuenta) && $tipoCuenta != null && trim($tipoCuenta) != ''
				&& $responsableAvance->GetBanco() != null && trim($responsableAvance->GetBanco()) != ''
			){
				echo '
				<tr>
					<td><span class="nombreCampo">Nro. cuenta: </span>'.$responsableAvance->GetNumeroCuenta().'</td>
					<td><span class="nombreCampo">Tipo cuenta: </span>'.$tipoCuenta.'</td>
					<td class="right"><span class="nombreCampo">Banco: </span>'.$responsableAvance->GetBanco().'</td>
				</tr>
				';
			}
			echo '
			</table>
			';
			if(
				is_array($responsableAvancePartidas->GetAvancePartidas())
				&& count($responsableAvancePartidas->GetAvancePartidas()) > 0
			){
				echo '
			<table class="textoTabla" cellspacing="0" cellpadding="0" style="width: 100%;">
				<tr>
					<td class="nombreCampo" style="width: 25%;">Partida</td>
					<td class="nombreCampo" style="width: 25%;">Monto</td>
					<td class="nombreCampo" style="width: 25%;">Partida</td>
					<td class="nombreCampo right" style="width: 25%;">Monto</td>
				</tr>
				';
				$countAvancePartida = 0;
				$esPar = true;
				$subtotal = 0;
				foreach ($responsableAvancePartidas->GetAvancePartidas() as $avanPartida)
				{
					$idpartida = ($avanPartida->GetPartida() ? $avanPartida->GetPartida()->GetId() : "");
					
					$subtotal += $avanPartida->GetMonto();
					$total += $avanPartida->GetMonto();
					
					if($countAvancePartida % 2 == 0) $esPar = true;
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
					<td class="'.$classTdRight.'">'.number_format($avanPartida->GetMonto(),2,',','.').'</td>
					';
					if(!$esPar){echo '</tr>';}
					
					$countAvancePartida++;
				}
				if($esPar){
					echo '
						<td>&nbsp;</td>
						<td class="right">&nbsp;</td>
					</tr>';
				}
				echo '
				<tr>
					<td class="right bottom" colspan="4" style="text-align: right;">
						<span class="nombreCampo">Subtotal: </span>'. number_format($subtotal,2,',','.').'
					</td>
				</tr>
			</table>
				';
			}
			echo '
		</td>
	</tr>
			';
		} // Fin de foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
		echo '
	<tr>
		<td colspan="2" class="responsables" style="text-align: right;">
			<span class="nombreCampo">Total: </span>'.number_format($total,2,',','.').'
		</td>
	</tr>
		';
	?>
	<?php
		// Imprimir las rutas
		if(is_array($avance->GetRutasAvance()) && count($avance->GetRutasAvance())>0)
		{
			echo '
	<tr>
		<td colspan="2" class="nombreCampoTitulo">Informaci&oacute;n de las rutas</td>
	</tr>
	<tr>
		<td colspan="2" class="infocentros">
			';
			$nombreEstados = array();
			foreach ($avance->GetRutasAvance() as $ruta)
			{
				$nombreEstados[] = $ruta->GetEstado()->GetNombre();
			}
			echo '
			'.implode(", ", $nombreEstados).'
		</td>
	</tr>
				';
		}
	
		// Imprimir Observaciones si es necesario
		if($avance->GetObservaciones() != null && trim(($avance->GetObservaciones())) != '')
		{
			echo '
	<tr>
		<td colspan="5" class="nombreCampoTitulo">Observaciones</td>
	</tr>
	<tr>
		<td colspan="5">'.ToHtmlEncode($avance->GetObservaciones()).'</td>
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
						"<p align='center'>Av. Universidad, Esquina el Chorro, Torre Ministerial, Piso 11, La Hoyada, Caracas<br/>Teléfono: 0212-7718520/7718672 Fax: 0212-7718672<br/>www.infocentro.gob.ve</p><br/>".
						"<span style='align=center;font-family: arial;font-style:italic;font-weight:bold;font-size: 10pt;'>SAFI - Fundación Infocentro</span><br/>".
						"<span style='align=center;font-family: arial;font-size: 10pt;'>".fecha()."</span>";
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