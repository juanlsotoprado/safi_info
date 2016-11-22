<?php

	require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');

?>
<html>
	<head>
		<title>.:SAFI:. Buscar Acta</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script type="text/javascript">
		$().ready(function(){
			$("#desi").keyup(function(){
				$("#txt_inicio").val("");
				$("#hid_hasta_itin").val("");
			});
		});
		</script>
	</head>
	
	<body class="normal">
		<table align="center">
			<tr>
				<td><?php include(SAFI_VISTA_PATH . '/mensajes.php');?></td>
			</tr>
			<tr>
				<td>
					<form name="avanceBuscarForm" id="avanceBuscarForm" method="post" action="desincorporacion.php">
						<input type="hidden" name="accion" value="Buscar">
						<table cellpadding="0" cellspacing="0" width="640" align="center"
							background="../../imagenes/fondo_tabla.gif" class="tablaalertas" border="0"
						>
							<tr> 
			    				<td height="21" colspan="2" class="normalNegroNegrita header" align="left">
			    					Buscar acta
			    				</td>
							</tr>
			  				<tr>
								<td height="10" colspan="2"></td>
							</tr>
			  				<tr>
			  					<td class="normalNegrita">Elaborados entre:</td>
			  					<td>
			  						<!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
									<?php VistaFechas::ConstruirAccesosRapidosFechas("txt_inicio", "hid_hasta_itin", "dd/mm/yy") ?>
			  						<input
			  							type="text"
			  							size="10"
			  							id="txt_inicio"
			  							name="txt_inicio"
			  							class="dateparse"
										onfocus="javascript: compararFechasYBorrarById('txt_inicio', 'hid_hasta_itin', 'txt_inicio');"
										readonly="readonly"
										value=""
									/>
									<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar"><img
										src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"
									/></a>
									
									<input
										type="text"
										size="10"
										id="hid_hasta_itin"
										name="hid_hasta_itin"
										class="dateparse"
										onfocus="javascript: compararFechasYBorrarById('txt_inicio', 'hid_hasta_itin', 'hid_hasta_itin');"
										readonly="readonly"
									/>
									<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar"><img
										src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"
									/></a>
			  					</td>
			  				</tr>
			  				<tr>
			  					<td class="normalNegrita">C&oacute;digo del acta</td>
			  					<td>
			  						<input
			  							type="text"
			  							id="desi"
			  							name="desi"
			  							class="normalNegro"
			  							<?php /* value="<?php echo ($form->GetAvance()!=null) ? $form->GetAvance()->GetId() : null ?>"*/?>
			  						>

			  					</td>
			  				</tr>
			  				<tr align="center"> 
			  					<td colspan="2">
			  						<div align="center">
										<input type="submit" value="Buscar" class="normalNegro">
									</div>
								</td>
			  				</tr>
						</table>


					</form>
					<?php 
					
					/*echo "<pre>";
					echo print_r($actadesi);
					echo "</pre>";*/


							echo '
					<table
						cellpadding="0"
						cellspacing="0"
						align="center"
						class="tablaalertas" 
						background="../../imagenes/fondo_tabla.gif"
						style="width: 100%;"
					>
						<tr class="normalNegroNegrita">
							<td class="header normalNegroNegrita">Acta</td>
							<td class="header normalNegroNegrita">Observaciones</td>
							<td class="header normalNegroNegrita">Estatus</td>
							<td class="header normalNegroNegrita">Fecha del acta</td>
							<td class="header normalNegroNegrita">Acci&oacute;n</td>
						</tr>
							';
							
						for($i=0;$i<=$actadesi[total];$i++){
							echo "
							<tr>
								<td>".$actadesi[acta_id][$i]."</td>
								<td>".$actadesi[observaciones][$i]."</td>
								<td>".$actadesi[esta_id][$i]."</td>
								<td>".$actadesi[fecha_acta][$i]."</td>";
						?> <td align="left" class="normal"><div align="center">
						<a style="font-size: 10px;" href="javascript:abrir_ventana('../../vistas/bienes/desincorporacion/PDFDesincorporacion.php?id=<?php echo trim($actadesi[acta_id][$i]); ?>')" class="copyright"><?php echo "Ver detalle"; ?></a><br>
						        </div></td><?php
							"</tr>";
						}
							echo "
						</table>
							";
						
					?>
				</td>
			</tr>
		</table>
	</body>
</html>
