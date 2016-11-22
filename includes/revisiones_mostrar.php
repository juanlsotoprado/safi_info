<?
//Buscar la revisiones del documento
$sql = "SELECT * FROM sai_consulta_revisiones_doc('".$request_codigo_documento."') AS (revi_id int4, revi_doc varchar, usua_login varchar, perf_id varchar, revi_fecha timestamp, wfop_id int4, revi_firma text)";
$resultado_revisiones=pg_query($conexion,$sql) or die("Error al mostrar lista de documentos");
$total_revisiones=pg_num_rows($resultado_revisiones);
	
if ($total_revisiones == null){
	$total_revisiones=0;
}
	
if ($total_revisiones > 0) {
?>

<form action="" method="post" name="form_firma" id="form_firma">
     <table width="550" border="0" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
     
          <tr class="normal">
            <td height="15" colspan="6" valign="middle" class="td_gray"><div align="left" class="normalNegroNegrita">REVISIONES DEL DOCUMENTO </div>
              <div align="center"></div></td>
          </tr>
          <tbody id="body1"  class="normal">
          </tbody>
          <tr>
            <td width="19"><div align="center" class="normalNegroNegrita">N&ordm;</div></td>
            <td ><div align="center" class="normalNegroNegrita">Usuario</div></td>
            <td ><div align="center" class="normalNegroNegrita">Perfil</div></td>
            <td ><div align="center" class="normalNegroNegrita">Fecha</div></td>
            <td colspan="2" ><div align="center" class="normalNegroNegrita">Tipo</div>
			</td>
          </tr>
		   <?php
		$firmante_anterior = "";			
		$cont=0;	
		$cant_aprobar = 0;	
		while ($row = pg_fetch_array($resultado_revisiones)) {
		
	    	$cont++;
			$usuario_login = $row["usua_login"];
			$usuario_perf_id = $row["perf_id"];
			$fecha_revision = $row["revi_fecha"];
			$id_opcion_revision = $row["wfop_id"];	
			$firma_usuario = $row["revi_firma"];
			
			$ano=substr($fecha_revision,0,4);
			$mes=substr($fecha_revision,5,2);
			$dia=substr($fecha_revision,8,2);
			$hora=substr($fecha_revision,11,8);		
			
			//Buscar nombre del usuario
			$sql_u = " SELECT * FROM sai_buscar_nombre_usuario('$usuario_login') as resultado ";
			$resultado_u = pg_query($conexion,$sql_u) or die("Error al mostrar1");
			if ($row_u = pg_fetch_array($resultado_u)) {
				$nombre_usuario = $row_u["resultado"];
			}
						
			//Buscar nombre del perfil
			$sql_p = " SELECT * FROM sai_buscar_cargo_depen('$usuario_perf_id') as resultado ";
			$resultado_p = pg_query($conexion,$sql_p) or die("Error al mostrar2");
			if ($row_p = pg_fetch_array($resultado_p)) {
				$cargo_depen_usuario = $row_p["resultado"];
			}		
						
			$directorio_imagenes = "imagenes/";
			if ($directorio_imagenes_2 != "") {					
				$directorio_imagenes = $directorio_imagenes_2 ;
			}
			if(
				strpos($request_codigo_documento, 'vnac') !== false
				|| strpos($request_codigo_documento, GetConfig("preCodigoRendicionViaticoNacional")) !== false
				|| strpos($request_codigo_documento, GetConfig("preCodigoAvance")) !== false
				|| strpos($request_codigo_documento, GetConfig("preCodigoRendicionAvance")) !== false
			){
				$directorio_imagenes = '../../' . $directorio_imagenes;
			}
			
			$imagen_tipo_revision = "";
			$mostrar_imagen = 0;
			//Buscar nombre de la opcion
			$sql_o = "select * from sai_buscar_opcion(".$id_opcion_revision.") as (nombre_opcion varchar, desc_opcion varchar)";	
			$resultado_o = pg_query($conexion,$sql_o) or die("Error al mostrar3");
			if ($row_o = pg_fetch_array($resultado_o)) {
				$nombre_opcion = $row_o["nombre_opcion"];	
				$desc_opcion = strtolower($row_o["desc_opcion"]);
				
				if (($desc_opcion == "aprobar") || ($desc_opcion == "visto bueno")) {				
					$mostrar_imagen = 1;
					$imagen_tipo_revision = $directorio_imagenes.$desc_opcion.".gif";				
				}	
				
				//Si la opcion es de Firma Invalidada (porq se modif el doc)
				if ($id_opcion_revision == 23) {				
					$nombre_opcion= "<div align='center' class='error'>".$nombre_opcion."</div>";				
				}			
			}			
	 ?>
          <tr>
		 
            <td align="center" class="normal" height="30"><?php echo $cont; ?>&nbsp;</td>
            <td width="109" height="50%" align="center" class="normal"><?php echo $nombre_usuario; ?> </td>
            <td width="174" align="center" class="normal"><?php echo $cargo_depen_usuario; ?></td>
            <td width="67" align="center" class="normal"><?php echo $dia."-".$mes."-".$ano."<br>".$hora; ?></td>
			
			<?php if ($mostrar_imagen == 1) {?>
            <td width="38" align="center" class="normal">
			  <div align="right">
			    
			    <img src="<?php echo $imagen_tipo_revision; ?>" border="0">				
			    
		       				
	        </div></td>
		    <td width="117" align="center" class="normal">
			 <?php
				} 
				else {?>
				 <td colspan="2" align="center" class="normal">
				
				<?php
				} 
				?>
				<?php  
				
				echo $nombre_opcion."<br />";
				
				if ($cant_aprobar == 0) {	
					$textoAFirmarVerificar = $textoAFirmarV;			
				}
				else {
					$textoAFirmarVerificar = $textoAFirmarV."/".trim($ultimo_firmante);					
				}				
				?>			</td>
          </tr> <?php }  //fin while ?>
        </table>
</form>
<? } ?>