<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
require_once(SAFI_INCLUDE_PATH. '/perfiles/constantesPerfiles.php');

require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');

?>
<html>
<head>
<title>.:SAFI:. Comprobante diario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" 
 rel="stylesheet" type="text/css" charset="utf-8" />
<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>
<link href="<?=GetConfig("siteURL").'/css/safi0.2.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />

<?php require(SAFI_INCLUDE_PATH.'/fechaJs.php'); ?>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"
	charset="utf-8"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/ui.min.js';?>"
	charset="utf-8"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/constantes.js';?>"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/funciones.js';?>"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/func_montletra.js';?>"
	charset="utf-8">
	dependencia = <?php echo $_SESSION['user_depe_id'];?>

</script>
	
<style type="text/css">
.ui-autocomplete {
       max-height: 210px;
       font-size: 12px;
       overflow-y: auto;
       /* prevent horizontal scrollbar */
       overflow-x: hidden;
       /* add padding to account for vertical scrollbar */
       padding-right: 30px;
}
/* IE 6 doesn't support max-height
                       * we use height instead, but this forces the menu to always be this tall
                       */
* html .ui-autocomplete {
       font-size: 10px;
}

.ui-menu-item a {
       font-size: 10px;
}
</style>	
	
	
	<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/uploadify/uploadify/jquery.uploadify.min.js';?>"
	charset="utf-8"></script>

	<link type="text/css" href="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/uploadify/uploadify/uploadify.css';?>"
	media="screen" rel="stylesheet" />
	
	<link type="text/css" href="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/calendarPopup/css/calpopup.css';?>"
	media="screen" rel="stylesheet" />

	<!-- estilo para el autocompletar -->
	<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />	

	<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/codi/codi.js';?>"
	charset="utf-8"></script>	
	
 <script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/detalleDocumento.js';?>"
	charset="utf-8"></script>	
	
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/codi/buscar.js';?>"
	charset="utf-8"></script>			

</head>

<body>
<?php include(SAFI_VISTA_PATH . '/codi/detalleDocumentoCodi.html');?>
	<form name="formCodiFiltro" id="formCodiFiltro" method="post" action="">
	
		<input type="hidden" id="pagina" name="pagina" value="<?= $params['pagina']?>"/>
		<table style="width: 80%; align:center; background-image: url('../../imagenes/fondo_tabla.gif')" class="tablaalertas">
  <tr>
  <th class="header" colspan='2' ><span class="normalNegroNegrita">.:Buscar comprobantes manuales :.</span></th> 
  </tr>
  <tr id="codiBuscarTr1">
				<td width="175" height="29" class="normalNegrita" align="left">Fecha emisi&oacute;n
					entre:</td>
				<td>
				<!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
				<?php VistaFechas::ConstruirAccesosRapidosFechas("fecha_inicio", "fecha_fin", "dd/mm/yy") ?>
				<input type="text" size="10" id="fecha_inicio" name="fecha_inicio"
					class="dateparse" readonly="readonly" value="<?php echo $params['fecha_inicio'];//echo $params['fecha_inicio'] == '' ? date("d/m/Y") : $params['fecha_inicio']; ?>" /> <a href="javascript:void(0);"
					onclick="g_Calendar.show(event, 'fecha_inicio');"
					title="Show popup calendar"> <img
						src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"
						alt="Open popup calendar" /> </a>
				<input type="text" size="10"
					id="fecha_fin" name="fecha_fin" value="<?php echo $params['fecha_fin'];//echo $params['fecha_fin'] == '' ? date("d/m/Y") : $params['fecha_fin']; ?>" class="dateparse"
					readonly="readonly"
					value="" /> <a href="javascript:void(0);"
					onclick="g_Calendar.show(event, 'fecha_fin');"
					title="Show popup calendar"> <img
						src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"
						alt="Open popup calendar" /> </a> <a href="javascript:limpiarFem();">Limpiar Fecha emisi&oacute;n</a>
				</td>
			</tr>
			<tr id="codiBuscarTr2" >
				<td class="normalNegrita" align="left">C&oacute;digo del documento:</td>
				<td><span class="normalNegrita"><input name="idCodi" id="idCodi" type="text"
						class="normalNegro" size="18" value="<?php echo $params['idCodi']; ?>"/></span>
				</td>
			</tr>
		<tr id="codiBuscarTr3">
			<td class="normalNegrita" align="left">Nro. Compromiso:</td>
			<td><span class="normalNegrita"><input name="nro_compromiso" type="text" class="normalNegro" id="nro_compromiso"  size="25" value="<?php echo $params['nro_compromiso']; ?>"/></span>
			</td>
		   </tr>			
			<tr id="codiBuscarTr4">
	<td class="normalNegrita" align="left">Nro. Referencia bancaria:</td>
	<td><span class="normalNegrita"><input name="referenciaBancaria" type="text" class="normalNegro" id="referenciaBancaria"  size="25" value="<?php echo $params['referenciaBancaria']; ?>"/></span>
	</td>
   </tr>
	<tr id="codiBuscarTr5">
	<td class="normalNegrita" align="left">Documento asociado:</td>
	<td><span class="normalNegrita"><input name="documentoAsociado" type="text" class="normalNegro" id="documentoAsociado"  size="25" value="<?php echo $params['documentoAsociado']; ?>"/></span>
	</td>
   </tr>
	<tr id="codiBuscarTr6">
	<td class="normalNegrita" align="left">Justificaci&oacute;n:</td>
	<td><span class="normalNegrita"><input name="justificacion" type="text" class="normalNegro" id="justificacion"  size="25" value="<?php echo $params['justificacion']; ?>"/></span>
	</td>
   </tr>   
<tr>
				<td width="175" height="29" class="normalNegrita" align="left">Fecha elaboraci&oacute;n
					entre:</td>
				<td>
				<!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
				<?php VistaFechas::ConstruirAccesosRapidosFechas("fechae_inicio", "fechae_fin", "dd/mm/yy") ?>
				<input type="text" size="10" id="fechae_inicio" name="fechae_inicio"
					class="dateparse" 
					readonly="readonly" value="<?php echo $params['fechae_inicio']; ?>" /> <a href="javascript:void(0);"
					onclick="g_Calendar.show(event, 'fechae_inicio');"
					title="Show popup calendar"> <img
						src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"
						alt="Open popup calendar" /> </a> <input type="text" size="10"
					id="fechae_fin" name="fechae_fin" value="<?php echo $params['fechae_fin']; ?>" class="dateparse"
					readonly="readonly"
					value="" /> <a href="javascript:void(0);"
					onclick="g_Calendar.show(event, 'fechae_fin');"
					title="Show popup calendar"> <img
						src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"
						alt="Open popup calendar" /> </a> <a href="javascript:limpiarFel();">Limpiar fecha elaboraci&oacute;n</a>
				</td>
			</tr>      

  <tr id="codiBuscarTr7">
	<td class="normalNegrita" align="left">Analista:</td>
	<td>

	
 <select name="empleado" id="empleado" class="normalNegrita">
									<option selected value="">.: Todos :.</option>
									<?php
									
									$idEmpleadoActual = '';
									if(isset($params['empleado']) && $params['empleado']->getId() != null && $params['empleado']->getId() != '')
									{
										$idEmpleadoActual = $params['empleado']->getId();
									}
									
									if(is_array($GLOBALS['SafiRequestVars']['empleado'])){
										foreach ($GLOBALS['SafiRequestVars']['empleado'] as  $empleado){
									?>

									<option<?php echo $idEmpleadoActual != '' && $idEmpleadoActual == $empleado->GetId() ? ' selected="selected"': ''  ?> value="<?php echo $empleado->GetId(); ?>">
									<?php echo $empleado->GetNombres().' '.$empleado->GetApellidos();?>
									</option>


									<?php } }?>

							</select>

	</td>
	</tr>
	<tr id="codiBuscarTr8">
		<td  class="normalNegrita"  align="left">Cuenta contable:</td>
	<td><span class="normalNegrita">
		<input type="text" name="cuentaContable" id="cuentaContable" class="normalNegro" size="60" value="" />	
	</span></td>
  </tr>
  <tr id="codiBuscarTr9">
  	<td class="normalNegrita" align="left">Estatus:</td> 
	<td>

	<select class="normalNegrita" id="estatusCodi" name="estatusCodi">
		<option value="" selected="selected">.:Todos:.</option>
		<?php
		
		if($GLOBALS['SafiRequestVars']['EstadoCodi']){
			foreach ( $GLOBALS['SafiRequestVars']['EstadoCodi'] as  $estado){ ?>
	
			<option <?php echo $params['estatusCodi'] == $estado->GetId()? 'selected': ''  ?> value="<? echo $estado->GetId()?>"> <? echo $estado->GetNombre()?></option>
				
			<?php } }?>

    </select>  
	</td>
  </tr>
  
  <tr>
  <td colspan="2" align="center">
  <br/>
  <input  type="button" value="Buscar"   id="BuscarCodi"  onClick="javascript:buscarAccion()"/>
  <input type="reset" value="Limpiar" id="reset" name="reset" />
  </td>
  </tr>
</table>
		
</form>
				<form id="formImpresionMultiple" action="codi.php" method="post">
					<input type="hidden" name="accion" value="GenerarPDF">
					<input type="hidden" name="caso" value="GenerarPDF">
					<input type="hidden" name="salidaEstandar" value="true">
					<input type="hidden" name="codis">
					<div align="center">
	 					<a title="Generar archivo en formato PDF con los comprobantes seleccionados" href="javascript: imprimirSalidaEstandar();">
							<img border="0" src="../../imagenes/pdf_ico.jpg"/>
						</a>
					</div>
				</form>
				
				
                   <?php
                   if(is_array($GLOBALS['SafiRequestVars']['listaCodi'])){
					 $i = 0;
					 /*Paginacion*/
					 $contador = $GLOBALS['SafiRequestVars']['listaCodiContador'];
					 if ($contador >= 1) {
					 if ($params['tamanoPagina'] == 0) {
						$params['tamanoPagina'] = 1;
						$params['tamanoVentana'] = 1;
					}	
					 $totalPaginas = ($contador%$params['tamanoPagina'] == 0)?$contador/$params['tamanoPagina']:intval($contador/$params['tamanoPagina'])+1;
					 $ventanaActual = ($params['pagina']%$params['tamanoVentana']==0)?$params['pagina']/$params['tamanoVentana']:intval($params['pagina']/$params['tamanoVentana'])+1;
					 $i = (($ventanaActual-1)*$params['tamanoVentana'])+1;
					 echo "<font size='-2'> <div align='center'>";
					 while($i<=$ventanaActual*$params['tamanoVentana'] && $i<=$totalPaginas) {
							if($i==(($ventanaActual-1)*$params['tamanoVentana'])+1 && $i!=1){
						 		echo "<a onclick='buscar(".($i-1).");' style='cursor: pointer;text-decoration: underline;'>&lt;</a> ";
						 	}
						 	if($i==$params['pagina']){
						 		echo $i." ";
						 	}else{
						 		echo "<a onclick='buscar(".$i.");' style='cursor: pointer;text-decoration: underline;'>".$i."</a> ";
						 	}
						 	if($i==$ventanaActual*$params['tamanoVentana'] && $i<$totalPaginas){
						 		echo "<a onclick='buscar(".($i+1).");' style='cursor: pointer;text-decoration: underline;'>&gt;</a> ";
						 	}
						 	$i++;
					 	}
					 }
					 echo "</div></font>\n";
					 
					 $i = $params['desplazamiento']+1;
					}
					 ?>
					
		<table style="width: 100%;">
		<tr>
			<td><table style="width: 100%; align:center; background-image: url('../../imagenes/fondo_tabla.gif')" class="tablaalertas" id="tablaBusqueda">
					 <tbody id="tablaFiltro" >					 
					<tr>
						<th align="center" class="header">
							<input type="checkbox" id="controlCodis" name="controlCodis" onclick="marcarTodosNinguno();"/>
						</th>					
						<th class="header"><span class="normalNegroNegrita">C&oacute;digo del documento</span></th>
						<th class="header"><span class="normalNegroNegrita">Elaborado por</span></th>						
						<th class="header"><span class="normalNegroNegrita">Nro. Referencia bancaria</span></th>
						<th class="header"><span class="normalNegroNegrita">Documento asociado</span></th>
						<th class="header"><span class="normalNegroNegrita">Nro. Compromiso</span></th>
						<th class="header"><span class="normalNegroNegrita">Justificaci&oacute;n</span></th>
						<th class="header"><span class="normalNegroNegrita">Fecha</span></th>
						<th class="header"><span class="normalNegroNegrita">Estado</span></th>
						<th class="header"><span class="normalNegroNegrita">Observaciones</span></th>
						<th class="header"><span class="normalNegroNegrita">Modificado/Anulado por</span></th>						
						<th class="header"><span class="normalNegroNegrita">Anular</span></th>						
					</tr>					 
					 <?php 
					 if(is_array($GLOBALS['SafiRequestVars']['listaCodi'])){
                     foreach ($GLOBALS['SafiRequestVars']['listaCodi'] as $listaCodi) {
                     ?>
                      <tr>
                      <td><?=$i;?><input type="checkbox" id="codis<?= $listaCodi->GetId();?>" name="codis<?= $listaCodi->GetId();?>" onclick='agregarQuitarCodi(this, true);' value="<?= $listaCodi->GetId();?>"/></td>
                       <td class="link"> 
                       <!-- <a href="../../documentos/codi/codiMultiplePDF.php?codis=<?=$listaCodi->GetId();?>"><?=$listaCodi->GetId();?> </a>-->
						<a href="codi.php?accion=GenerarPDF&salidaEstandar=true&codis=<?=$listaCodi->GetId();?>"><?=$listaCodi->GetId();?> </a>                       
                       
                       
                       </td>
                             <td><?php echo $listaCodi->GetIdUsuario();?></td>                       
                         <td align="center"><?php echo $listaCodi->GetNumeroReferencia();?></td>
                          <td align="center"><?php echo $listaCodi->GetDocumentoAsociado() ;?></td>
						<td align="center"><?php echo $listaCodi->GetNroCompromiso();?></td>
                          <td><?php echo $listaCodi->GetJustificacion();?></td>
                            <td align="center"><?php echo $listaCodi->GetFechaEmision();?></td>
                            <td align="center"><?php echo $listaCodi->GetIdEstado();?></td>
      						 <td><?php echo $listaCodi->GetMemoContenido();?></td>                            
      						 <td><?php echo $listaCodi->GetMemoResponsable();?></td>  
      						  <?php 
                             if (strcmp($listaCodi->GetIdEstado(), 'Anulado')!=0) {
                             ?>    						 
                            
                             <td align="center">
								<a href="#dialog" docgId="<?=$listaCodi->GetId();?>"
								class="detalleOpcion">A:(<?=$listaCodi->GetId();?>) </a>                             
                             <!--  <a id="anularCodi" href="javascript:anular('<?=$listaCodi->GetId();?>');">Anular</a>-->
                             <?php if ($_SESSION['user_perfil_id']==PERFIL_COORDINADOR_CONTABILIDAD) {?>
                           	<a href="../../acciones/codi/codi.php?accion=Modificar&idCodi=<?=$listaCodi->GetId();?>">M:(<?=$listaCodi->GetId();?>)</a>
                           	<?php }?>
                             </td>
                             <?php }?>
                        </tr>
                   <?php 
                   $i++; 
					}
				

					echo "<tr class='header'><td colspan='12' align='center'>";
					$ventanaActual = ($params['pagina']%$params['tamanoVentana']==0)?$params['pagina']/$params['tamanoVentana']:intval($params['pagina']/$params['tamanoVentana'])+1;
					$i = (($ventanaActual-1)*$params['tamanoVentana'])+1;
					while($i<=$ventanaActual*$params['tamanoVentana'] && $i<=$totalPaginas) {
						if($i==(($ventanaActual-1)*$params['tamanoVentana'])+1 && $i!=1){
							echo "<a onclick='buscar(".($i-1).");' style='cursor: pointer;text-decoration: underline;'>&lt;</a> ";
						}
						if($i==$params['pagina']){
							echo $i." ";
						}else{
							echo "<a onclick='buscar(".$i.");' style='cursor: pointer;text-decoration: underline;'>".$i."</a> ";
						}
						if($i==$ventanaActual*$params['tamanoVentana'] && $i<$totalPaginas){
							echo "<a onclick='buscar(".($i+1).");' style='cursor: pointer;text-decoration: underline;'>&gt;</a> ";
						}
						$i++;
					}
					echo "</td></tr>\n";					
				}	
				?>
				</table>
				</td>
			</tr>
			</table>		
</body>
