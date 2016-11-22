
<tr>
	<td height="46" colspan="2"> <dl class="normal">
		<?php  
			$codig=$cod_doc;
			$sql_resp0="SELECT * FROM sai_any_tabla('sai_respaldo','docg_id','docg_id=''$codig''') resultado_set(docg_id varchar)"; 
	  		$resultado_resp0=pg_query($conexion,$sql_resp0) or die("Error al mostrar lista de respaldos");
	  		$total0=pg_num_rows($resultado_resp0);
	 
	  		if ($total0>0) {
	   			$row_resp0=pg_fetch_array($resultado_resp0)
	 
	   	?>
        <table width="479" border="0" align="center" class="tablaalertas" background="../../imagenes/fondo_tabla.gif">
        	<tr class="normal">
            	<td height="15" colspan="4" valign="middle" class="td_gray">
					<div align="left" class="normalNegroNegrita">
						RESPALDOS AGREGADOS 
					</div>
            	</td>
          	</tr>
          	<tbody id="body1"  class="normal">
          	</tbody>
          	<tr>
            	<td width="21" align="center" class="normal style4">
					N&ordm;
				</td>
            	<td height="50%" align="center" class="normal style4">
					Detalle
				</td>
            	<td align="center" class="normal style4">
					Ingresado Por 
				</td>
            	<td align="center" class="style4 normal">
					Tipo
				</td>
          	</tr>
          	<tr>
		  	<?php
				$cont=0;
		
	  			$sql_resp="SELECT * FROM sai_any_tabla('sai_respaldo','resp_nombre,resp_tipo,usua_login','docg_id=''$codig''')      
resultado_set(resp_nombre varchar,resp_tipo varchar,usua_login varchar)"; 
	  			$resultado_resp=pg_query($conexion,$sql_resp) or die("Error al Mostrar Lista de Respaldos");
	  			$total=pg_num_rows($resultado_resp);
	  			if ($total>0) 
				{
	   				while($row_resp=pg_fetch_array($resultado_resp))
					{
	    				$cont=$cont+1;
	 		?>
            	<td align="center" class="normal">
					<?php 
						echo $cont; 
					?>
					&nbsp;
				</td>
            	<td width="162" height="50%" align="center" class="normal">
					<? 
						if($row_resp['resp_tipo']=="Digital") 
						{ 
							if(isset($request_id_tipo_documento)) 
							{
								echo("<a href='documentos/tmp/".trim($row_resp['usua_login'])."/".trim($row_resp['resp_nombre'])."' target='_blank'>".$row_resp['resp_nombre']."</a>"); 
							} 
							else 
							{
								echo("<a href='../tmp/".trim($row_resp['usua_login'])."/".trim($row_resp['resp_nombre'])."' target='_blank'>".$row_resp['resp_nombre']."</a>"); 
							}
						} 
						else 
							echo($row_resp['resp_nombre']); 
					?>
				</td>
				<?php
					$login=$row_resp['usua_login'];
					$sql_emp="SELECT * FROM sai_any_tabla('sai_usuario','empl_cedula','usua_login=''$login''') 
resultado_set(empl_cedula varchar)"; 
	 				//echo $sql_emp;
					$resultado_emp=pg_query($conexion,$sql_emp) or die("Error al Mostrar Nombre");
					$row_emp=pg_fetch_array($resultado_emp);
					$empl_cedula=$row_emp['empl_cedula'];
					$sql_emp="SELECT * FROM sai_any_tabla('sai_empleado','empl_nombres,empl_apellidos','empl_cedula=''$empl_cedula''')      resultado_set(empl_nombres varchar, empl_apellidos varchar)"; 
	  				$resultado_emp=pg_query($conexion,$sql_emp) or die("Error al Mostrar Nombre");
	  				$row_emp=pg_fetch_array($resultado_emp)
				?>
            	<td width="144" align="center" class="normal">
					<?php 
						echo $row_emp['empl_nombres']." ".$row_emp['empl_apellidos'];
					?>
				</td>
            	<td width="134" align="center" class="normal">
					<?php 
						echo $row_resp['resp_tipo'];
					?>
				</td>
		  	</tr> 
			<?php 
					}
				}
			?>
		</table>
		<?php 
			}
			else 
			{
		?>
		<div align="center">
		<?php 
				echo "No se especificaron respaldos";
			} 
		?>
		</div>
      	</dl> 
	</td>
</tr>		  		
<tr>
	<td height="46" colspan="2"><dl class="normal">
	<?
		$c=0;
				
		$sql_sopor="SELECT * FROM sai_buscar_doc_soporte('$codig') resultado_set(doso_doc_soport varchar)"; 
		$resultado_sopor=pg_query($conexion,$sql_sopor) or die("Error al mostrar lista de documentos relacionados");
		$total=pg_num_rows($resultado_sopor);
		if ($total>0) 
		{
		$soportes= array(array());
	?>
		<table width="479" border="0" align="center" class="tablaalertas">    
	  	 <tr class="normal">
            	  <td height="15" colspan="2" valign="middle" class="td_gray">
			<div align="left" class="normalNegroNegrita">DOCUMENTOS ASOCIADOS </div>
            	  </td>
          	</tr>
		<tr>
            	  <td width="21" align="center" class="normal style4">C&oacute;digo</td>
            	  <td height="50%" align="center" class="normal style4">Descripci&oacute;n</td>
          	</tr>
		<?	
			$c=0;
				$nivel=0;
				while($row_sopor0=pg_fetch_array($resultado_sopor)) {
					$soporte[$nivel][$c]=$row_sopor0['doso_doc_soport'];

					$carpeta=substr($row_sopor0['doso_doc_soport'],0,4);
					$codigo_sopor=$row_sopor0['doso_doc_soport'];
				
					$c=$c+1;
				}
					for($nivel=0;$nivel<10;$nivel++)
					for($i=0;$i<10;$i++) {
						$sql_sopor="SELECT * FROM sai_buscar_doc_soporte('".$soporte[$nivel][$i]."')      resultado_set(doso_doc_soport varchar)"; 
						$resultado_sopor=pg_query($conexion,$sql_sopor) or die("Error al Mostrar Lista de documentos relacionados");
						$total=pg_num_rows($resultado_sopor);
						if($total>0) {
							$c=0;
							while($row_sopor=pg_fetch_array($resultado_sopor))	{
								$soporte[$nivel+1][$c]=$row_sopor['doso_doc_soport'];
								$c=$c+1;
							}
						}
					}
					
					for($i=0;$i<count($soporte);$i++)
					for($j=0;$j<count($soporte[$i]);$j++) {
					?>
					<tr>
						<td align="center" class="normal style4">
						<?
							$carpeta=substr($soporte[$i][$j],0,4);
							$codigo_sopor=$soporte[$i][$j];
						?>
<?if ($carpeta!="memo"){?>
<a href="javascript:abrir_ventana('../<?php echo $carpeta."/".$carpeta?>_detalle.php?codigo=<? echo $codigo_sopor;?>')" class="copyright"><?echo($soporte[$i][$j]);?></a>
<?}else{?>
  <a href="javascript:abrir_ventana('../memo/memo_detalle.php?codigo=<? echo $codigo_sopor;?>')" class="copyright">	
			<?php echo($soporte[$i][$j]);?></a>

<?}?>
						</td>
						<td align="center" class="normal style4">
						<?
							$sql_detalle="
								SELECT
									*
								FROM
									sai_documento
								WHERE
									docu_id='$carpeta'
							";
							$resultado_detalle=pg_query($conexion,$sql_detalle) or die("Error al mostrar el detalle de los doc. relacionados");
							if($row_detalle=pg_fetch_array($resultado_detalle))
							{
								echo($row_detalle['docu_nombre']);
							}
						?>
						</td>
					</tr>
					<?	} ?>
		</table>
	<?
		}
		else {
	?>
		<div align="center">
	<?php 
			echo "No posee documento asociado";
		} 
	?>
		</div>
		</dl>
	</td>
</tr>


<?php 
	//Si es una ventana *_detalle, mostrar revisiones
	if (!isset($request_id_tipo_documento)) {
	 //if ((substr($codig,0,4)!="pcta") && (substr($codig,0,4)!="comp") ) {
		if (substr($codig,0,4)!="comp")  {
?>

<tr>
	<td height="46" colspan="2">
<?php 		
	
		//Incluir la lista de las revisiones
		echo "<br>";
		$request_codigo_documento = $codig;
		$directorio_imagenes_2 = "../../imagenes/";
		include("revisiones_mostrar.php");
		echo "<br>";
		
		//Buscar el perfil actual 
		$sql_d = "SELECT wfob_id_ini, perf_id_act FROM sai_doc_genera WHERE docg_id='".$request_codigo_documento."' ";
		
		$resultado = pg_query($conexion,$sql_d) or die("Error al mostrar");
		if ($row = pg_fetch_array($resultado)) {
			$objeto_actual = $row["wfob_id_ini"];
			$perfil_actual = $row["perf_id_act"];
		}
		$mensaje= "";
		if (($objeto_actual == 99) || ($objeto_actual == 98)) {		
			$mensaje= "Documento finalizado";
		}
		else {
			include('funciones.php');
			//Buscar el nombre del cargo y dependencia actual
			$cargo_depen_actual="";
			$sql = " SELECT * FROM sai_buscar_cargo_depen('".$perfil_actual."') as resultado ";	//	echo "ROSA $sql;";	
			$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
			if ($row = pg_fetch_array($resultado)) {
				$cargo_depen_actual = $row["resultado"];
			}
		if (strlen($cargo_depen_actual)>2)			
			$mensaje=  " Instancia actual: ".$cargo_depen_actual ;		
		}
		
		echo "<div align='center'><span class='normalNegrita'> ".$mensaje." </span></div><br>";
?>
	</td>
</tr>
<?php 
	}}
?>