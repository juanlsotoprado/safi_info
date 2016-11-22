<?php
require("../../includes/reporteBasePdf.php"); 
require("../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../includes/html2ps/funciones.php");
require("../../includes/funciones.php");

$contenido = "";
ob_clean();

$tipo = $GLOBALS['SafiRequestVars']['tipo'];
$viaticoNacionalEntidad = $GLOBALS['SafiRequestVars']['viaticoNacionalEntidad'];
$dependencia = $GLOBALS['SafiRequestVars']['dependencia'];
$docGenera = $GLOBALS['SafiRequestVars']['docGenera'];
$perfilGerenteDirector = $GLOBALS['SafiRequestVars']['perfilGerenteDirector'];
$arrFirmas = $GLOBALS['SafiRequestVars']['arrFirmas'];
$elaboradoPor = $GLOBALS['SafiRequestVars']['elaboradoPor'];

if($tipo && $tipo!='' && $viaticoNacionalEntidad instanceof EntidadViaticoNacional){
	
	/************************************************************************
	          Obtener información de las asignaciones de viático
	*************************************************************************/
	
	$viaticoNacionalAsignaciones = $viaticoNacionalEntidad->GetViaticoResponsableAsignaciones();
	$asignacion = $viaticoNacionalAsignaciones[EntidadAsignacionViatico::COD_HOSPEDAJE];
	$montoAsignacionHospedaje = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetMonto():0);
	$unidadesHospedaje = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetUnidades():0);
	
	$asignacion = $viaticoNacionalAsignaciones[EntidadAsignacionViatico::COD_ALIMENTACION];
	$montoAsignacionAlimentacion = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetMonto():0);
	$unidadesAlimentacion = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetUnidades():0);
	
	$asignacion = $viaticoNacionalAsignaciones[EntidadAsignacionViatico::COD_RESIDENCIA_AEROPUERTO];
	$montoAsignacionTransResidAeropuerto = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetMonto():0);
	$unidadesTransResidAeropuerto = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetUnidades():0);
	
	$asignacion = $viaticoNacionalAsignaciones[EntidadAsignacionViatico::COD_AEROPUERTO_RESIDENCIA];
	$montoAsignacionTransAeropuertoResid = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetMonto():0);
	$unidadesTransAeropuertoResid = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetUnidades():0);
	
	$asignacion = $viaticoNacionalAsignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_INTERURBANO];
	$montoAsignacionTransporteInterurbano = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetMonto():0);
	$unidadesTransporteInterurbano = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetUnidades():0);
	
	$asignacion = $viaticoNacionalAsignaciones[EntidadAsignacionViatico::COD_TASA_AEROPORTUARIA];
	$montoAsignacionTasaAeroportuaria = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetMonto():0);
	$unidadesTasaAeroportuaria = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetUnidades():0);
	
	$asignacion = $viaticoNacionalAsignaciones[EntidadAsignacionViatico::COD_SERVICIO_COMUNICACIONES];
	$montoAsignacionServicioComunicaciones = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetMonto():0);
	$unidadesServicioComunicaciones = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetUnidades():0);
	
	$asignacion = $viaticoNacionalAsignaciones[EntidadAsignacionViatico::COD_ASIGNACION_TRANSPORTE];
	$montoAsignacionTransporte = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetMonto():0);
	$unidadesAsignacionTransporte = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetUnidades():0);
	
	$asignacion = $viaticoNacionalAsignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_EXTRAURBANO];
	$montoAsignacionTransporteExtraurbano = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetMonto():0);
	$unidadesTransporteExtraurbano = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetUnidades():0);
	
	$asignacion = $viaticoNacionalAsignaciones[EntidadAsignacionViatico::COD_TRANSPORTE_ENTRE_CIUDADES];
	$montoAsignacionTransporteEntreCiudad = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetMonto():0);
	$unidadesTransporteEntreCiudad = (($asignacion instanceof EntidadViaticoResponsableAsignacion)?$asignacion->GetUnidades():0);
	
	/************************************************************************
	         Fin de Obtener información de las asignaciones de viático
	*************************************************************************/
	
	$totalFirmas = 3;
	$mostrarFirmaGerente = false;
	$mostrarFirmaDirectorEjecutivo = false;
		
	// Decidir si se muestra la firma del responsable de la rendición de viático nacional
	if(
		// el responsable es el presidente
		strcmp($arrFirmas[PERFIL_PRESIDENTE]['cedula_empleado'], $viaticoNacionalEntidad->GetResponsable()->GetCedula()) == 0
		// el responsable es el director de la oficina de gestión administrativa y financiera
		|| strcmp($arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['cedula_empleado'],
				$viaticoNacionalEntidad->GetResponsable()->GetCedula()) == 0
		// el responsable es el director de la oficiona de planificación, presupuesto y control.
		|| strcmp($arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['cedula_empleado'], $viaticoNacionalEntidad->GetResponsable()->GetCedula()) == 0
		// el responsable es el director ejecutivo
		|| strcmp($arrFirmas[PERFIL_DIRECTOR_EJECUTIVO]['cedula_empleado'], $viaticoNacionalEntidad->GetResponsable()->GetCedula()) == 0
	){
		$mostrarFirmaResponsable = false;
	} else {
		$mostrarFirmaResponsable = true;
	}
	
	/********************************************************************
	 * Incluir la firma del director ejecutivo y del presidente siempre *
	 *******************************************************************/
	$mostrarFirmaDirectorEjecutivo = true;
	$totalFirmas++;
	
	 if(
		// Si el gerente/director no es el responsable del viático
		strcmp($arrFirmas[$perfilGerenteDirector]['cedula_empleado'], $viaticoNacionalEntidad->GetResponsable()->GetCedula()) != 0
	){
		 if(
			// Si la dependencia es distinta la oficina de gestión administrativa y financiera
			strcmp($dependencia->GetId(), $arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['id_dependencia']) != 0
			// O si la dependencia es distinta la oficina planificación, presupuesto y control
			&& strcmp($dependencia->GetId(), $arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['id_dependencia']) != 0
			// Y si la dependencia es distinta de presidencia
			&& strcmp($dependencia->GetId(), $arrFirmas[PERFIL_PRESIDENTE]['id_dependencia']) != 0
			// Y si la dependencia es distinta de dirección ejecutiva
			&& strcmp($dependencia->GetId(), $arrFirmas[PERFIL_DIRECTOR_EJECUTIVO]['id_dependencia']) != 0
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
		strcmp($dependencia->GetId(), $arrFirmas[PERFIL_PRESIDENTE]['id_dependencia']) == 0
	){
		$mostrarFirmaGerente = false;
		$mostrarFirmaDirectorEjecutivo = false;
	} else if(
			// Si el gerente/director es el responsable del viático
			strcmp($arrFirmas[$perfilGerenteDirector]['cedula_empleado'], $viaticoNacionalEntidad->GetResponsable()->GetCedula()) == 0
	){
		$mostrarFirmaDirectorEjecutivo = true;
		$mostrarFirmaGerente = false;
		$totalFirmas++;
	} else if(
		// Si la dependencia es la oficina de gestión administrativa y financiera
		strcmp($dependencia->GetId(), $arrFirmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS]['id_dependencia']) == 0
		// O si la dependencia es la oficina planificación, presupuesto y control
		|| strcmp($dependencia->GetId(), $arrFirmas[PERFIL_DIRECTOR_PRESUPUESTO]['id_dependencia']) == 0
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
				if ($mostrarFirmaGerente){
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
			<td class="nombre">'. $arrFirmas[PERFIL_DIRECTOR_EJECUTIVO]['nombre_empleado'].'</td>
					';
				}
				if ($mostrarFirmaGerente){
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
				Fecha: <?php
					$fecha = explode(" ", $viaticoNacionalEntidad->GetFechaViatico());
					echo $fecha[0];
				?>
			</div>
			<div class="borde numeroPaginas" style="float: right;"></div>
			<div style="clear: both;"></div>
		</div>
		<div class="titulo borde">
			VIÁTICO NACIONAL <?php echo $viaticoNacionalEntidad->GetId() ?>
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
			<td colspan='4' class='nombreCampoTitulo'>Datos del beneficiario</td>
		</tr>
		<tr>
			<td colspan='3'><span class='nombreCampo'>Nombre: </span><?php 
				echo mb_strtoupper($viaticoNacionalEntidad->GetResponsable()->GetNombres(), 'ISO-8859-1').
				" ". mb_strtoupper($viaticoNacionalEntidad->GetResponsable()->GetApellidos(), 'ISO-8859-1')
			?></td>
			<td>
				<span class='nombreCampo'>CI: </span><?php
					echo mb_strtoupper($viaticoNacionalEntidad->GetResponsable()->GetNacionalidad().
						"-" . $viaticoNacionalEntidad->GetResponsable()->GetCedula(), 'ISO-8859-1')
				?>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<span class='nombreCampo'><?php
				if(strcmp($viaticoNacionalEntidad->GetResponsable()->GetTipoResponsable(), EntidadResponsableViatico::TIPO_EMPLEADO) == 0)
				{
					echo "Cargo";
				} else {
					echo "Tipo";
				}
				?>: </span><?php
					echo mb_strtoupper($viaticoNacionalEntidad->GetResponsable()->GetTipoEmpleado(), 'ISO-8859-1')
				?>
			</td>
			<td colspan='4'>
				<span class='nombreCampo'>Dependencia: </span><?php
					echo mb_strtoupper($dependencia->GetNombre(), 'ISO-8859-1') ?>
			</td>
		</tr>
		<tr>
			<td colspan='4' class='nombreCampoTitulo'>Informaci&oacute;n de dep&oacute;sito</td>
		</tr>
		<tr>
			<td colspan="4">
				<table border="1" style="width: 100%; padding: 0px; border-spacing: 0px;">
					<tr>
						<td>
							<span class='nombreCampo'>Nro. cuenta: </span><?php
								echo $viaticoNacionalEntidad->GetResponsable()->GetNumeroCuenta() ?>
							</td>
						<td>
							<span class='nombreCampo'>Tipo cuenta: </span><?php 
								echo (($viaticoNacionalEntidad->GetResponsable()->GetTipoCuenta()==EntidadTipoCuentabancaria::CUENTA_DE_AHORRO)?
									"Ahorros":"Corriente") ?>
						</td>
					</tr>
					<tr>
						<td colspan='2'><span class='nombreCampo'>Banco: </span><?php
							echo $viaticoNacionalEntidad->GetResponsable()->GetBanco()?>
						</td>
					</tr>
				</table>
			<td>
		</tr>
		<tr>
			<td colspan='4' class='nombreCampoTitulo'>Informaci&oacute;n general</td>
		</tr>
		<tr>
			<td colspan='4'>
				<span class='nombreCampo'>
					Categor&iacute;a:
				</span><?php
					// Imprime el nombre dela categoría
					echo (($viaticoNacionalEntidad->GetCategoriaViatico() != null) ?
						$viaticoNacionalEntidad->GetCategoriaViatico()->GetNombre() : '' );

					// Imprime el campo de red
					echo (($viaticoNacionalEntidad->GetRed() != null) ?
						"&nbsp;<span class='nombreCampo'>
							Red:
						</span>
						".$viaticoNacionalEntidad->GetRed()->GetNombre() : '');
						
					// Imprime el campo de estado (del país)
					echo (($viaticoNacionalEntidad->GetEstado() != null) ?
						"&nbsp;<span class='nombreCampo'>
							Estado:
						</span>
						".$viaticoNacionalEntidad->GetEstado()->GetNombre() :'')						
				?>
			</td>
		</tr>
		<tr>
			<td colspan='4'>
				<span class='nombreCampo'>Fecha inicio: </span><?php echo $viaticoNacionalEntidad->GetFechaInicioViaje() ?>
				<span class='nombreCampo'>Fecha fin: </span><?php echo $viaticoNacionalEntidad->GetFechaFinViaje() ?>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<span class="nombreCampo"><?php
					if($viaticoNacionalEntidad->GetProyecto() != null){ echo "Proyecto"; }
					else if($viaticoNacionalEntidad->GetAccionCentralizada() != null){ echo "Acci&oacute;n Centralizada"; }
				?>: </span> 
				<?php
					if($viaticoNacionalEntidad->GetProyecto() != null){echo $viaticoNacionalEntidad->GetProyecto()->GetNombre();}
					else if($viaticoNacionalEntidad->GetAccionCentralizada() != null){
						echo $viaticoNacionalEntidad->GetAccionCentralizada()->GetNombre();
					}
				?>
				<br />
				<span class="nombreCampo">Acci&oacute;n espec&iacute;fica: </span>
				<?php
					if( $viaticoNacionalEntidad->GetProyectoEspecifica() != null){
						$especifica = $viaticoNacionalEntidad->GetProyectoEspecifica();
						echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();
					} else if($viaticoNacionalEntidad->GetAccionCentralizadaEspecifica() != null){
						$especifica = $viaticoNacionalEntidad->GetAccionCentralizadaEspecifica();
						echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();
					}
				?>
			</td>
		</tr>
		<tr>
			<td colspan='4' style="text-align: justify;"><span class='nombreCampo'>Objetivos del viaje: </span><?php
				echo $viaticoNacionalEntidad->GetObjetivosViaje() ?>
			</td>
		</tr>
		<?php 
			// Imprimir los infocentros si es necesario
			if(is_array($viaticoNacionalEntidad->GetInfocentros()) && count($viaticoNacionalEntidad->GetInfocentros())>0){
				echo '
		<tr>
			<td colspan="4" class="infocentros">
				<span class="nombreCampo">Infocentros a visitar:</span>
				<ul>
				';
				foreach($viaticoNacionalEntidad->GetInfocentros() as $infocentro){
					$label = $infocentro->GetNombre();
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
	
			// Imprimir las rutas del viaje
			if(is_array($viaticoNacionalEntidad->GetRutas()) && count($viaticoNacionalEntidad->GetRutas())>0){
				echo "
		<tr>
			<td class='infocentros' colspan='4'>
				<span class='nombreCampo'>Informaci&oacute;n de las rutas:</span>
				<ul>
				";
	
				foreach($viaticoNacionalEntidad->GetRutas() as $ruta){
					
					echo "
					<li>
						[" . $ruta->GetFechaInicio()."-".$ruta->GetFechaFin() . "]  
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
			<td class="nombreCampoTitulo" align="center">Concepto</td>
			<td class="nombreCampoTitulo" align="center" width="15%">Total d&iacute;as</td>
			<td class="nombreCampoTitulo" align="center">Monto Asig Bs.</td>
			<td class="nombreCampoTitulo" align="center">Total Bs.</td>
		</tr>
		<tr>
			<td class="nombreCampo" width="285px">Hospedaje</td>
			<td align="center"><?php echo $unidadesHospedaje ?></td>
			<td align="right"><?php echo number_format($montoAsignacionHospedaje,2,',','.') ?></td>
			<td align="right" colspan="2"><?php echo number_format(($unidadesHospedaje*$montoAsignacionHospedaje),2,',','.') ?></td>
		</tr>
		<tr>
			<td class="nombreCampo">Alimentaci&oacute;n</td>
			<td align="center"><?php echo $unidadesAlimentacion ?></td>
			<td align="right"><?php echo number_format($montoAsignacionAlimentacion,2,',','.') ?></td>
			<td align="right" colspan="2"><?php echo number_format(($unidadesAlimentacion*$montoAsignacionAlimentacion),2,',','.') ?></td>
		</tr>
		<tr>
			<td class="nombreCampo">Trans Resid-Aeropuerto</td>
			<td align="center"><?php echo $unidadesTransResidAeropuerto ?></td>
			<td align="right"><?php echo number_format($montoAsignacionTransResidAeropuerto,2,',','.') ?></td>
			<td align="right" colspan="2"><?php 
				echo number_format(($unidadesTransResidAeropuerto*$montoAsignacionTransResidAeropuerto),2,',','.')
			?></td>
		</tr>
		<tr>
			<td class="nombreCampo">Trans Aeropuerto-Resid</td>
			<td align="center"><?php echo $unidadesTransAeropuertoResid ?></td>
			<td align="right"><?php echo number_format($montoAsignacionTransAeropuertoResid,2,',','.') ?></td>
			<td align="right" colspan="2"><?php
				echo number_format(($unidadesTransAeropuertoResid*$montoAsignacionTransAeropuertoResid),2,',','.')
			?></td>
		</tr>
		<tr>
			<td class="nombreCampo">Transporte interurbano</td>
			<td align="center"><?php echo $unidadesTransporteInterurbano ?></td>
			<td align="right"><?php echo number_format($montoAsignacionTransporteInterurbano,2,',','.') ?></td>
			<td align="right" colspan="2"><?php 
				echo number_format(($unidadesTransporteInterurbano*$montoAsignacionTransporteInterurbano),2,',','.') ?>
			</td>
		</tr>
		<tr>
			<td class="nombreCampo">Tasa aeroportuaria</td>
			<td align="center"><?php echo $unidadesTasaAeroportuaria ?></td>
			<td align="right"><?php echo number_format($montoAsignacionTasaAeroportuaria,2,',','.') ?></td>
			<td align="right" colspan="2"><?php 
				echo number_format(($unidadesTasaAeroportuaria*$montoAsignacionTasaAeroportuaria),2,',','.')
			?></td>
		</tr>
		<tr>
			<td class="nombreCampo">Servicio Comunicaciones</td>
			<td align="center"><?php echo $unidadesServicioComunicaciones ?></td>
			<td align="right"><?php echo number_format($montoAsignacionServicioComunicaciones,2,',','.') ?></td>
			<td align="right" colspan="2"><?php 
				echo number_format(($unidadesServicioComunicaciones*$montoAsignacionServicioComunicaciones),2,',','.') ?></td>
		</tr>
		<tr>
			<td class="nombreCampo">Asignaci&oacute;n por transporte</td>
			<td align="center"><?php echo $unidadesAsignacionTransporte ?></td>
			<td align="right"><?php echo number_format($montoAsignacionTransporte,2,',','.') ?></td>
			<td align="right" colspan="2"><?php
				echo number_format(($unidadesAsignacionTransporte*$montoAsignacionTransporte),2,',','.') ?>
			</td>
		</tr>
		<tr>
			<td class="nombreCampo">Transporte extraurbano</td>
			<td align="center"><?php echo $unidadesTransporteExtraurbano ?></td>
			<td align="right"><?php echo number_format($montoAsignacionTransporteExtraurbano,2,',','.') ?></td>
			<td align="right" colspan="2"><?php 
				echo number_format(($unidadesTransporteExtraurbano*$montoAsignacionTransporteExtraurbano),2,',','.') ?>
			</td>
		</tr>
		<tr>
			<td class="nombreCampo">Transporte entre ciudad</td>
			<td align="center"><?php echo $unidadesTransporteEntreCiudad ?></td>
			<td align="right"><?php echo number_format($montoAsignacionTransporteEntreCiudad,2,',','.') ?></td>
			<td align="right" colspan="2"><?php 
				echo number_format(($unidadesTransporteEntreCiudad*$montoAsignacionTransporteEntreCiudad),2,',','.')
			?></td>
		</tr>
		<tr>
			<td colspan="3" class="nombreCampo" style="padding-top: 10px;">
				TOTAL Bs.
			</td>
			<td class="nombreCampo" align="right" style="padding-top: 10px;"><?php
				echo 
					number_format(					
						(
							($unidadesHospedaje*$montoAsignacionHospedaje)+
							($unidadesAlimentacion*$montoAsignacionAlimentacion)+
							($unidadesTransResidAeropuerto*$montoAsignacionTransResidAeropuerto)+
							($unidadesTransAeropuertoResid*$montoAsignacionTransAeropuertoResid)+
							($unidadesTransporteInterurbano*$montoAsignacionTransporteInterurbano)+
							($unidadesTasaAeroportuaria*$montoAsignacionTasaAeroportuaria)+
							($unidadesServicioComunicaciones*$montoAsignacionServicioComunicaciones)+
							($unidadesAsignacionTransporte*$montoAsignacionTransporte)+
							($unidadesTransporteExtraurbano*$montoAsignacionTransporteExtraurbano)+
							($unidadesTransporteEntreCiudad*$montoAsignacionTransporteEntreCiudad)
						),2,',','.')
				?>
			</td>
		</tr>
		<?php
			// Imprimir Observaciones si es necesario
			if($viaticoNacionalEntidad->GetObservaciones() != null && $viaticoNacionalEntidad->GetObservaciones() != ''){
				echo '
		<tr>
			<td colspan="4" class="nombreCampoTitulo">Observaciones</td>
		</tr>
		<tr>
			<td colspan="4">'.$viaticoNacionalEntidad->GetObservaciones().'</td>
		</tr>
				';
			}
			if(strcmp($tipo, "L") == 0){
				echo '
		<tr>
			<td colspan="4">'.$firmasAutorizacion.'</td>
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
				'.$viaticoNacionalEntidad->GetResponsable()->GetNombres()." "
							.$viaticoNacionalEntidad->GetResponsable()->GetApellidos()
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
		//echo $contenido;
	}
}
?>