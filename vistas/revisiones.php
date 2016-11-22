<?php 
	$datosRevisionesDocumento = $GLOBALS['SafiRequestVars']['datosRevisionesDocumento'];
	$arrRevisionesDoc = null;

	if($datosRevisionesDocumento != null && is_array($datosRevisionesDocumento) && count($datosRevisionesDocumento) > 0){
		$arrRevisionesDoc = $datosRevisionesDocumento['arrRevisionesDoc'];
		$empleados = $datosRevisionesDocumento['empleadosRevisiones'];
		$cargos = $datosRevisionesDocumento['cargosRevisiones'];
		$dependencias = $datosRevisionesDocumento['dependenciasRevisiones'];
		$wFOpciones = $datosRevisionesDocumento['wFOpcionesRevisiones'];
	}
	
	if($arrRevisionesDoc != null && is_array($arrRevisionesDoc) && count($arrRevisionesDoc) > 0)
	{
?>
<br/>
<table cellspacing="0" cellpadding="0" class="tablaalertas content" align="center" width="600px">
	<tr>
		<td class="header normalNegroNegrita">Revisiones del documento</td>
	</tr>
	<tr>
		<td><table class="tablaalertas content" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td class="normalNegroNegrita" align="center">N&ordm;</td>
				<td class="normalNegroNegrita" align="center" width="109px">Usuario</td>
				<td class="normalNegroNegrita" align="center" width="190px">Perfil</td>
				<td class="normalNegroNegrita" align="center" width="70px;">Fecha</td>
				<td class="normalNegroNegrita" align="center" colspan="2">Tipo</td>
			</tr>
			<?php 
				$count = 0;
				foreach ($arrRevisionesDoc AS $revisionesDoc)
				{
					$nombre = "Error";
					$nombreCargo = "Error";
					$nombreDependencia = "Error";
					$nombreWFOpcion = "Error";
					
					if(
						$empleados != null && is_array($empleados) && count($empleados) > 0
						&& ($empleado = $empleados[$revisionesDoc->GetLoginUsuario()]) != null
					){
						$nombre = $empleado->GetNombres() . " " .$empleado->GetApellidos();
					}
					
					if(
						$cargos != null && is_array($cargos) && count($cargos) > 0
						&& ($cargo = $cargos[GetCargoFundacionFromIdPerfil($revisionesDoc->GetIdPerfil())]) != null
					){
						$nombreCargo = $cargo->GetNombre();
					}
					
					if(
						$dependencias != null && is_array($dependencias) && count($dependencias) > 0
						&& ($dependencia = $dependencias[GetIdDependenciaFromIdPerfil($revisionesDoc->GetIdPerfil())]) != null
					){
						$nombreDependencia = $dependencia->GetNombre();
					}
					
					if(
						$wFOpciones != null && is_array($wFOpciones) && count($wFOpciones) > 0
						&& ($wFOpcion = $wFOpciones[$revisionesDoc->GetIdWFOpcion()]) != null
					){
						$nombreWFOpcion = $wFOpcion->GetNombre();
						
						//Si la opcion es de Firma Invalidada (porq se modif el doc)
						if ($wFOpcion->GetId() == 23) {				
							$nombreWFOpcion= "<div align='center' class='error'>".$nombreWFOpcion."</div>";				
						}	
					}
					
					echo '
				<tr>
					<td align="center">'.(++$count).'</td>
					<td align="center">'.$nombre.'</td>
					<td align="center">'.$nombreCargo.'('.$nombreDependencia.')</td>
					<td align="center">'.$revisionesDoc->GetFechaRevision().'</td>
					';
					// Mostrar imágen
					if($wFOpcion->GetDescripcion() == "aprobar" || $wFOpcion->GetDescripcion() == "visto bueno")
					{
						echo '
					<td class="normal" align="center" width="38">
						<div><img src="'.('../../imagenes/' .$wFOpcion->GetDescripcion() . '.gif').'" border="0"></div>
					</td>
					<td class="normal" align="center">
						';
					} else {
						echo '
					<td class="normal" align="center" colspan="2">
						';
					}
					echo '
						'.$nombreWFOpcion.'
					</td>
					';
					echo '
				</tr>
					';
					
				}
			?>
		</table></td>
	</tr>
</table>
<?php
	}
?>