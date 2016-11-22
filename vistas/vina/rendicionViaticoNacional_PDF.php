<?php
	require("../../includes/reporteBasePdf.php"); 
	require("../../includes/html2ps/config.inc.php");
	require(HTML2PS_DIR.'pipeline.factory.class.php');
	require("../../includes/html2ps/funciones.php");
	require("../../includes/funciones.php");
	
	$contenido = "";
	ob_clean();

	$arrFirmas = $GLOBALS['SafiRequestVars']['arrFirmas'];
	$tipo = $GLOBALS['SafiRequestVars']['tipo'];
	$rendicion = $GLOBALS['SafiRequestVars']['rendicion'];
	$viatico = $GLOBALS['SafiRequestVars']['viatico'];
	$docGenera = $GLOBALS['SafiRequestVars']['docGenera'];
	$asignaciones = $GLOBALS['SafiRequestVars']['asignaciones'];
	$elaboradoPor = $GLOBALS['SafiRequestVars']['elaboradoPor'];
	$perfilGerenteDirector = $GLOBALS['SafiRequestVars']['perfilGerenteDirector'];
	$compromiso = $GLOBALS['SafiRequestVars']['compromiso'];
	$solicitudPago = $GLOBALS['SafiRequestVars']['solicitudPago'];
	$pagoTransferencia = $GLOBALS['SafiRequestVars']['pagoTransferencia'];
	$bancoTransferencia = $GLOBALS['SafiRequestVars']['bancoTransferencia'];
	$cheque = $GLOBALS['SafiRequestVars']['cheque'];
	$chequera = $GLOBALS['SafiRequestVars']['chequera'];
	
	if($tipo!='' && $rendicion instanceof EntidadRendicionViaticoNacional){
		
		$totalFirmas = 3;
		
		// Decidir si se muestra la firma del responsable de la rendición de viático nacional
		if(
			// el responsable es el presidente
			strcmp($arrFirmas[PERFIL_PRESIDENTE]['cedula_empleado'], $viatico->GetResponsable()->GetCedula()) == 0
			// el responsable es el director de la oficina de gestión administrativa y financiera
			|| strcmp($arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['cedula_empleado'], $viatico->GetResponsable()->GetCedula()) == 0
			// el responsable es el director de la oficiona de planificación, presupuesto y control.
			|| strcmp($arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['cedula_empleado'], $viatico->GetResponsable()->GetCedula()) == 0
			// el responsable es el director ejecutivo
			|| strcmp($arrFirmas[PERFIL_DIRECTOR_EJECUTIVO]['cedula_empleado'], $viatico->GetResponsable()->GetCedula()) == 0
		){
			$mostrarFirmaResponsable = false;
		} else {
			$mostrarFirmaResponsable = true;
		}
		
		$mostrarFirmaGerente = false;
		$mostrarFirmaDirectorEjecutivo = false;
		
		/********************************************************************
		 * Incluir la firma del director ejecutivo y del presidente siempre *
		 *******************************************************************/
		$mostrarFirmaDirectorEjecutivo = true;
		$totalFirmas++;
		
		 if(
			// Si el gerente/director no es el responsable del viático			
			strcmp($arrFirmas[$perfilGerenteDirector]['cedula_empleado'], $viatico->GetResponsable()->GetCedula()) != 0
		){
			 if(
				// Si la dependencia es distinta la oficina de gestión administrativa y financiera
				strcmp($rendicion->GetDependencia()->GetId(), $arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['id_dependencia']) != 0
				// O si la dependencia es distinta la oficina planificación, presupuesto y control
				&& strcmp($rendicion->GetDependencia()->GetId(), $arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['id_dependencia']) != 0
				// Y si la dependencia es distinta de presidencia
				&& strcmp($rendicion->GetDependencia()->GetId(), $arrFirmas[PERFIL_PRESIDENTE]['id_dependencia']) != 0
				// Y si la dependencia es distinta de dirección ejecutiva
				&& strcmp($rendicion->GetDependencia()->GetId(), $arrFirmas[PERFIL_DIRECTOR_EJECUTIVO]['id_dependencia']) != 0
			) {
				$mostrarFirmaGerente = true;
				$totalFirmas++;
			}
		}
		/***************************************************************************
		 * Fin de Incluir la firma del director ejecutivo y del presidente siempre *
		 ***************************************************************************/
		
		/**********************************************************************************
		* Código con la condición de que se muestra la firma del gerente/director solo    *
		* si el gerente/director de la dependencia aparece como responsable del viático   *
		* o se muestra la firma del director ejecutivo                                    *
		**********************************************************************************/
		/*
		// Decidir si se muestra la firma del gerente/director o del director ejecutivo
		if(
			// Si la dependencia de la rendición es presidencia
			strcmp($rendicion->GetDependencia()->GetId(), $arrFirmas[PERFIL_PRESIDENTE]['cedula_empleado']) == 0
		){
			$mostrarFirmaGerente = false;
			$mostrarFirmaDirectorEjecutivo = false;
		} else if(
				// Si el gerente/director es el responsable de la rendición
				strcmp($arrFirmas[$perfilGerenteDirector]['cedula_empleado'], $viatico->GetResponsable()->GetCedula()) == 0
		){
			$mostrarFirmaDirectorEjecutivo = true;
			$mostrarFirmaGerente = false;
			$totalFirmas++;
		} else if(
			// Si la dependencia es la oficina de gestión administrativa y financiera
			strcmp($rendicion->GetDependencia()->GetId(), $arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['id_dependencia']) == 0
			// O si la dependencia es la oficina planificación, presupuesto y control
			|| strcmp($rendicion->GetDependencia()->GetId(), $arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['id_dependencia']) == 0
		) {
			$mostrarFirmaDirectorEjecutivo = false;
			$mostrarFirmaGerente = false;
		} else {
			$mostrarFirmaDirectorEjecutivo = false;
			$mostrarFirmaGerente = true;
			$totalFirmas++;
		}
		*/
		/******************************************************************************************
		* Fin del código con la condición de que se muestra la firma del director/director solo   *
		* si el gerente/director de la dependencia aparece como responsable del viático           *
		* o se muestra la firma del director ejecutivo                                            *
		******************************************************************************************/
		
		$widthTdFirma = floor(100/$totalFirmas) . "%";
		$colspanElaboradoPor = $totalFirmas;
		
		// Html de las firmas de autorización
?>
<table class="bordeTabla textoTabla firmasAutorizacion" border="1" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<?php
			if($mostrarFirmaDirectorEjecutivo){
				echo '
		<td class="oficina" width="'.$widthTdFirma.'">'.$arrFirmas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_cargo_dependencia'].'</td>		
					';
			}
			if($mostrarFirmaGerente){
				echo '
		<td class="oficina" width="'.$widthTdFirma.'">'.$arrFirmas[$perfilGerenteDirector]['nombre_cargo_dependencia'].'</td>		
				';
			}
		?>
		<td class="oficina" width="<?php echo $widthTdFirma; ?>">
			<?php echo $arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['nombre_cargo_dependencia']; ?>
		</td>
		<td class="oficina" width="<?php echo $widthTdFirma; ?>">
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
		<td class="nombre">'.$arrFirmas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_empleado'].'</td>
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
			Fecha: <?php echo $rendicion->GetFechaRendicion(); ?>
		</div>
		<div class="borde numeroPaginas" style="float: right;"></div>
		<div style="clear: both;"></div>
	</div>
	<div class="titulo borde">
		RENDICIÓN DE VIÁTICO NACIONAL <?php echo $rendicion->GetId() ?>
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
		font-size: 12pt;						
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
</style>

<?php if($docGenera->GetIdEstatus() == "15"){?>
<img  style=' width:20%; left:80%; height:200px;position:absolute;z-index:1000;' src='<?php echo GetConfig("siteURL")?>/imagenes/anulado_safi_3.gif'/>
<?php } ?>

<table class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0' width='100%'>
	<tr>
		<td colspan='2' class='nombreCampoTitulo'>Datos del beneficiario</td>
	</tr>
	<tr>
		<td colspan="2"><table class="sinBordesExternosTabla textoTabla" border="1" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan='2'><span class='nombreCampo'>Nombre: </span><?php 
					echo mb_strtoupper($viatico->GetResponsable()->GetNombres(), 'ISO-8859-1').
					" ". mb_strtoupper($viatico->GetResponsable()->GetApellidos(), 'ISO-8859-1')
				?>
				</td>
				<td>
					<span class='nombreCampo'>CI: </span><?php
						echo mb_strtoupper($viatico->GetResponsable()->GetNacionalidad().
							"-" . $viatico->GetResponsable()->GetCedula(), 'ISO-8859-1')
					?>
				</td>
			</tr>
		</table></td>
	</tr>
	<tr>
		<td colspan="2"><table class="sinBordesExternosTabla textoTabla" border="1" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan='1'>
					<span class='nombreCampo'><?php
					if(strcmp($viatico->GetResponsable()->GetTipoResponsable(), EntidadResponsableViatico::TIPO_EMPLEADO) == 0){
						echo "Cargo";
					} else {
						echo "Tipo";
					}
					?>: </span><?php
						echo mb_strtoupper($viatico->GetResponsable()->GetTipoEmpleado(), 'ISO-8859-1') ?>
				</td>
				<td colspan='1'>
					<span class='nombreCampo'>Dependencia: </span><?php
						echo mb_strtoupper($rendicion->GetDependencia()->GetNombre(), 'ISO-8859-1') ?>
				</td>
			</tr>
		</table></td>
	</tr>
	<tr>
		<td colspan='2' class='nombreCampoTitulo'>Informaci&oacute;n general</td>
	</tr>
	<tr>
		<td colspan="2">
			<span class='nombreCampo'>Fecha inicio: </span><?php echo $rendicion->GetFechaInicioViaje() ?>
			<span class='nombreCampo'>Fecha fin: </span><?php echo $rendicion->GetFechaFinViaje() ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<span class="nombreCampo"><?php
				if($viatico->GetProyecto() != null){ echo "Proyecto:"; }
				else if($viatico->GetAccionCentralizada() != null){ echo "Acci&oacute;n Centralizada:"; }
			?>: </span> 
			<?php
				if($viatico->GetProyecto() != null){echo $viatico->GetProyecto()->GetNombre();}
				else if($viatico->GetAccionCentralizada() != null){ echo $viatico->GetAccionCentralizada()->GetNombre(); }
			?>
			<br />
			<span class="nombreCampo">Acci&oacute;n espec&iacute;fica: </span>
			<?php
				if( $viatico->GetProyectoEspecifica() != null){
					$especifica = $viatico->GetProyectoEspecifica();
					echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();
				} else if($viatico->GetAccionCentralizadaEspecifica() != null){
					$especifica = $viatico->GetAccionCentralizadaEspecifica();
					echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();
				}
			?>
		</td>
	</tr>
	<?php
		if($compromiso != null || $solicitudPago != null){
	?>
	<tr>
		<td colspan="2">
			<?php
				if($compromiso != null){
			?>
			<span class='nombreCampo'>Compromiso: </span><?php echo $compromiso->GetId() ?>
			<?php
				}
				if($solicitudPago != null){
			?>
			<span class='nombreCampo'>Solicitud de pago: </span><?php echo $solicitudPago->GetId() ?>
			<?php
				}
			?>
		</td>
	<tr>
	<?php
		}
	
		if(is_object($pagoTransferencia)){
	?>
	<tr>
		<td colspan='2' class='nombreCampoTitulo'>Datos de la transferencia recibida</td>
	</tr>
	<tr>
		<td colspan="2"><table class="sinBordesExternosTabla textoTabla" border="1" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<span class='nombreCampo'>Banco: </span><?php
						echo is_object($bancoTransferencia) ? $bancoTransferencia->GetNombre() : "";	
					?>
				</td>
				<td>
					<span class='nombreCampo'>Nro. cuenta: </span><?php echo $pagoTransferencia->GetCuentaEmisor()->GetId() ?>
				</td>
			</tr>
		</table></td>
	</tr>
	<tr>
		<td colspan="2"><table class="sinBordesExternosTabla textoTabla" border="1" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<span class='nombreCampo'>Nro. referencia: </span><?php echo $pagoTransferencia->GetNumeroReferencia() ?>
				</td>
				<td>
					<span class='nombreCampo'>Monto de la transferencia: </span><?php
						echo number_format($pagoTransferencia->GetMontoTransferencia(),2,',','.')
				?></td>
			</tr>
		</table></td>
	</tr>
	<?php
		}
		
		if($cheque!=null && $chequera!=null && false){
	?>
	<tr>
		<td colspan='2' class='nombreCampoTitulo'>Datos del cheque recibido</td>
	</tr>
	<tr>
		<td colspan="2"><table class="sinBordesExternosTabla textoTabla" border="1" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<span class='nombreCampo'>Banco: </span><?php echo $chequera->Getbanco()->GetNombre() ?>
				</td>
				<td>
					<span class='nombreCampo'>Nro. cuenta: </span><?php echo $chequera->GetNumeroCuentaBancaria() ?>
				</td>
			</tr>
		</table></td>
	</tr>
	<tr>
		<td colspan="2"><table class="sinBordesExternosTabla textoTabla" border="1" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<span class='nombreCampo'>Nro. cheque: </span><?php echo $cheque->GetNumero() ?>
				</td>
				<td>
					<span class='nombreCampo'>Monto del cheque: </span><?php echo number_format($cheque->GetMonto(),2,',','.') ?>
				</td>
			</tr>
		</table></td>
	</tr>
	<?php 
		}
	?>
	<tr>
		<td colspan='2' class='nombreCampoTitulo'>Informe de la actividad</td>
	</tr>
	<tr>
		<td colspan='2' style="text-align: justify;"><span class='nombreCampo'>Logros alcanzados: </span><?php
			echo $rendicion->GetObjetivosViaje() ?>
		</td>
	</tr>
	<?php 
		// Imprimir las rutas del viaje
		if(is_array($viatico->GetRutas()) && count($viatico->GetRutas())>0){
			echo "
	<tr>
		<td class='infocentros' colspan='2'>
			<span class='nombreCampo'>Informaci&oacute;n de las rutas:</span>
			<ul>
			";
	
			foreach($viatico->GetRutas() as $ruta){
					
				echo "
				<li>
					[" .  $ruta->GetNombreFromEstado() . "-" . $ruta->GetNombreToEstado() . "]
					[NH: " . $ruta->GetDiasHospedaje() . ",
					DA: " . $ruta->GetDiasAlimentacion() . ",
					DTI: " . $ruta->GetUnidadTransporteInterurbano() . "]
					[TT: " . $ruta->GetNombreTransporte() . "]
				</li>
				";
			}
				echo "
			</ul>
			<span class='textoPie'>
				<br />
				<span class='nombreCampo '>* NH:</span> Noches de hospedaje.
				<span class='nombreCampo '>DA:</span> D&iacute;as alimentaci&oacute;n.
				<span class='nombreCampo '>DTI:</span> D&iacute;as trasporte interurbano.
				<span class='nombreCampo '>TT:</span> Tipo transporte.
				<br />
			</span>
		</td>
	</tr>
				";
		}
	?>
	<tr>
		<td colspan="2"><table class="sinBordesExternosTabla textoTabla" border="1" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="3" class="nombreCampoTitulo">Asignaci&oacute;n total</td>
			</tr>
			<?php
				$totalMonto = 0.0;
				if(is_array($viatico->GetViaticoResponsableAsignaciones())){
					$viaticoRespAsignaciones = $viatico->GetViaticoResponsableAsignaciones();
					foreach($viaticoRespAsignaciones as $codigoAsignacion => $viaticoRespAsignacion){
						$asignacion = $asignaciones[$codigoAsignacion];
						$totalMonto += $viaticoRespAsignacion->GetMonto() * $viaticoRespAsignacion->GetUnidades();
					}
				}
			?>
			<tr>
				<td>
					<span class="nombreCampo">Anticipo: </span><?php echo number_format($totalMonto,2,',','.') ?>
				</td>
				<td>
					<span class="nombreCampo">Total gastado: </span><?php echo number_format($rendicion->GetTotalGastos(),2,',','.') ?>
				</td>
				<td>
					<span class="nombreCampo">Reintegro a la fundaci&oacute;n: </span>
					<?php echo number_format($totalMonto - $rendicion->GetTotalGastos(),2,',','.') ?>
				</td>
			<tr>
			<!-- 
			<tr>
				<td colspan="4" class="nombreCampo" align="right">Anticipo entregado Bs. F.&nbsp;</td>
				<td align="right" class="nombreCampo"><?php echo number_format($totalMonto,2,',','.') ?></td>
			</tr>
			<tr>
				<td colspan="4" class="nombreCampo" align="right">Total gastos Bs. F.&nbsp;</td>
				<td align="right" class="nombreCampo"><?php echo number_format($rendicion->GetTotalGastos(),2,',','.') ?></td>
			</tr>
			<tr>
				<td colspan="4" class="nombreCampo" align="right">Reintegro a la fundaci&oacute;n Bs. F.&nbsp;</td>
				<td align="right" class="nombreCampo"><?php echo number_format($totalMonto - $rendicion->GetTotalGastos(),2,',','.') ?></td>
			</tr>
			 -->
		</table></td>
	</tr>
	<tr>
		<td colspan="2" class="nombreCampoTitulo">Datos del reintegro</td>
	</tr>
	<tr>
		<td colspan="1">
			<span class='nombreCampo'>Banco: </span><?php
				echo ($rendicion->GetReintegroBanco() != null && $rendicion->GetReintegroBanco() instanceof EntidadBanco) ?
					$rendicion->GetReintegroBanco()->GetNombre() : "";
			?>
		</td>
		<td colspan="1">
			<span class='nombreCampo'>Nro. referencia: </span><?php echo $rendicion->GetReintegroReferencia() ?>
		</td>
	</tr>
	<tr>
		<td colspan="1">
			<span class='nombreCampo'>Fecha: </span><?php echo $rendicion->GetReintegroFecha() ?>
		</td>
		<td colspan="1">
			<span class='nombreCampo'>Monto: </span><?php echo number_format($totalMonto - $rendicion->GetTotalGastos(),2,',','.') ?>
		</td>
	</tr>
	<?php
		// Imprimir Observaciones si es necesario
		if($rendicion->GetObservaciones() != null && $rendicion->GetObservaciones() != ''){
			echo '
	<tr>
		<td colspan="5" class="nombreCampoTitulo">Observaciones</td>
	</tr>
	<tr>
		<td colspan="5">'.$rendicion->GetObservaciones().'</td>
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
			<?php
				if($mostrarFirmaResponsable){
					echo '
			<td style="width: 50px;"></td>
			<td style="width: 170px; font-weight: bold;">Beneficiario / Responsable:</td>
			<td style="width: 250px; border-bottom: 1px solid #000000;"></td>
					';
				}
			?>
		</tr>
		<tr>
			<td></td>
			<td style="text-align: center;"><?php echo $elaboradoPor->GetNombres()." ".$elaboradoPor->GetApellidos(); ?></td>
			<?php
				if($mostrarFirmaResponsable){
					echo '
			<td></td>
			<td></td>
			<td style="text-align: center;">
				'.$viatico->GetResponsable()->GetNombres()." ".$viatico->GetResponsable()->GetApellidos()
				.'
			</td>
					';
				}
			?>
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
			//echo $contenido;
		}

	}
?>