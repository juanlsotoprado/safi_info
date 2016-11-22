<?php 
    ob_start();
	session_start();
	
	require_once("../../../includes/perfiles/constantesPerfiles.php");
	require_once("../../../includes/conexion.php");
	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	     {
		   header('Location:../../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	     }
	
	$idPerfil = $_SESSION['user_perfil_id'];
	     
	ob_end_flush();
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../../includes/js/funciones.js"> </SCRIPT>
<script>
function deshabilitar_combo(valor)
{
 if(valor=='1')
 { 
  document.form.cod_bien.disabled=false;
  document.form.cod_partida.disabled=true;
  document.form.des_bien.disabled=true;
  document.form.edo.disabled=true;
  document.form.cod_partida.value='0';
  document.form.des_bien.value='0';
  document.form.edo.value='0';
  
 }
 else
 if(valor=='2')
 { 
  document.form.cod_bien.disabled=true;
  document.form.cod_partida.disabled=false;
  document.form.des_bien.disabled=true;
  document.form.edo.disabled=true;
  document.form.cod_bien.value='0';
  document.form.des_bien.value='0';
  document.form.edo.value='0';
 }
 if(valor=='3')
 { 
  document.form.cod_bien.disabled=true;
  document.form.cod_partida.disabled=true;
  document.form.des_bien.disabled=false;
  document.form.edo.disabled=true;
  document.form.cod_bien.value='0';
  document.form.cod_partida.value='0';
  document.form.edo.value='0';
  
 }
  if(valor=='4')
 { 
  document.form.cod_bien.disabled=true;
  document.form.cod_partida.disabled=true;
  document.form.des_bien.disabled=true;
  document.form.edo.disabled=true;
  document.form.cod_bien.value='0';
  document.form.cod_partida.value='0';
  document.form.edo.value='0';
  
 }
   if(valor=='5')
 { 
  document.form.cod_bien.disabled=true;
  document.form.cod_partida.disabled=true;
  document.form.des_bien.disabled=true;
  document.form.edo.disabled=true;
  document.form.cod_bien.value='0';
  document.form.cod_partida.value='0';
  document.form.edo.value='0';
  
 }
    if(valor=='6')
 { 
  document.form.cod_bien.disabled=true;
  document.form.cod_partida.disabled=true;
  document.form.des_bien.disabled=true;
  document.form.edo.disabled=false;
  document.form.cod_bien.value='0';
  document.form.cod_partida.value='0';
  document.form.edo.value='0';
  
 }
}
function detalle(codigo)
{
    url="detalle.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}
function ejecutar(codigo1,codigo2,codigo3,codigo6)
{
  
  if ((codigo1=='0') && (codigo2=='0') && (codigo3=='0') && (codigo6=='0'))
  {
     document.form.hid_buscar1.value=1;
  }
   else {document.form.hid_buscar1.value=2;
   }
  window.location="buscar.php?cod_bien="+codigo1+"&cod_partida="+codigo2+"&des_bien="+codigo3+"&edo="+codigo6+"&hid_buscar1="+document.form.hid_buscar1.value
}
function ejecutar_varios(codigo1,codigo2,codigo3)
{
  window.location="buscar.php?des_partida="+codigo1+"&txt_anno="+codigo2+"&txt_clave="+codigo3
}
</script>

</head>
<body>
<form name="form" method="post" action="buscar.php">
<input type="hidden" name="hid_buscar1" value="0" />
<br />
<br />
<table width="747" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
      <td height="15" colspan="3" valign="midden" class="normalNegroNegrita">B&uacute;squeda de activos</td>
</tr>
<tr>
  <td width="38">
    <div align="center">
      <input name="opt_articulo" type="radio" value="1" onClick="javascript:deshabilitar_combo(1)" />
      </div></td>
  <td width="137" height="33" class="normalNegrita">C&oacute;digo:</td>
  <td width="456" height="33" colspan="2" class="normal">
  <select name="cod_bien" id="cod_bien" class="normal" disabled="true">
	   <option value="0">[Seleccione]</option>
	   <?php
	   #Efectuamos la consulta SQL  -------------   Con estado=5(Operativo)
	    $sqlc="SELECT * FROM sai_seleccionar_campo('sai_item','id','id_tipo=2','id',1) resultado_set(id varchar)"; 
		$resultado_set_c=pg_query($conexion,$sqlc) or die("Error al mostrar articulo");
	    while($rowc=pg_fetch_array($resultado_set_c)) 
	    { 
 		 $arti_id=$rowc['id'];
		?>
   	     <option value="<?=$arti_id?>"><?=$arti_id?></option> 
  <?php } ?>
  </select>  </td>
</tr>
<tr>
  <td>
    <div align="center">
      <input name="opt_articulo" type="radio" value="2" onClick="javascript:deshabilitar_combo(2)" />
      </div></td>
  <td width="137" height="35" valign="midden" class="normalNegrita">Partida:</td>
  <td width="456" height="35" colspan="2" valign="midden" class="normal">
  <select name="cod_partida" id="cod_partida" class="normal" disabled="true">
          <option value="0">[Seleccione]</option>
          <?php
	      $sql_part="select *  from sai_buscar_art_bien_comp(".$_SESSION['an_o_presupuesto'].",2,1) as resultado_set(partida_id varchar,partida_nombre varchar)"; 
	      $resultado_part=pg_query($conexion,$sql_part) or die("Error al mostrar");
	 	  while($row_part=pg_fetch_array($resultado_part))
	      { $part_id=$row_part['partida_id'];
		$part_nombre=$row_part['partida_nombre'];
	        ?>
           <option value="<?=$part_id?>"><?php echo"$part_id";?>:<?php echo"$part_nombre";?></option>
           <?php 
	      } ?>
      </select>  </td>
</tr>
<tr>
  <td>
    <div align="center">
      <input name="opt_articulo" type="radio" value="3" onClick="javascript:deshabilitar_combo(3)" />
      </div></td>
  <td width="137" height="32" class="normalNegrita">Nombre:</td>
  <td width="456" height="32" colspan="2" class="normal">
  <select name="des_bien" id="des_bien" class="normal" disabled="true">
	   <option value="0">[Seleccione]</option>
	   <?php
	   #Efectuamos la consulta SQL  -------------   Con estado=5(Operativo)
	    $sql_d="SELECT * FROM sai_seleccionar_campo('sai_item','id,nombre','id_tipo=2','nombre',1) resultado_set(id varchar, nombre varchar)"; 
	    $resultado_set_d=pg_query($conexion,$sql_d) or die("Error al mostrar");
	   #Mostramos los resultados obtenidos
	    while($rowd=pg_fetch_array($resultado_set_d)) 
	    { 
 		 $arti_id=$rowd['id'];
		 $arti_descripcion=$rowd['nombre'];
	   ?>
   	     <option value="<?=$arti_id?>"><?=$arti_descripcion?></option> 
  <?php } ?>
  </select>  </td>
</tr>

<tr>
  <td height="37" align="center"><input name="opt_articulo" type="radio" value="6" onclick="javascript:deshabilitar_combo(6)" /></td>
  <td height="37"  class="normalNegrita">Estado:</td>
  <td height="37" align="center"><div align="left">
    <select name="edo" class="normal" id="edo" disabled="disabled">
      <option value="0">Seleccione</option>
      <option value="1">Activo</option>
      <option value="2">Inactivo</option>
    </select>
  </div></td>
</tr>
<tr>
  <td height="44" colspan="3" align="center">
  <input type="button" value="Buscar" onclick="javascript:ejecutar(document.form.cod_bien.value,document.form.cod_partida.value,document.form.des_bien.value,document.form.edo.value)" class="normalNegro">
</tr>
</table>
<br />
</form>
<form name="form2" method="post" action="">
<?php 
if ($_GET['hid_buscar1']==1)
{
    echo "<SCRIPT LANGUAGE='JavaScript'>"."alert ('Debe seleccionar una opci\u00F3n de b\u00FAsqueda');"."</SCRIPT>";
}
else
   {  //echo "entro";
	   if ( (($_GET['cod_bien'])!=0) or (($_GET['des_bien'])!=0) )
	   {
		   if (($_GET['cod_bien'])!=0) { $codigo=trim($_GET['cod_bien']); }
		   else { $codigo=trim($_GET['des_bien']); }
		   $sql_tab="SELECT * FROM sai_seleccionar_campo('sai_item','nombre, esta_id','id='||'''$codigo''','',2)
		   resultado_set(nombre varchar, esta_id int4)";  //echo($sql_tab);
		   $resultado_set_tab=pg_query($conexion,$sql_tab) or die("Error al mostrar"); 
		   
		   if($rowa=pg_fetch_array($resultado_set_tab)) 
		   {    $arti_descripcion=$rowa['nombre']; 
		        $estado=trim($rowa['esta_id']);
				
				//consulta a la tabla de sai_estado
				$sql_edo="select * from sai_consulta_desc_estado($estado) as descripcion"; 
				$resultado_set_edo=pg_query($conexion,$sql_edo) or die("Error al consultar estado");
				
				if($rowedo=pg_fetch_array($resultado_set_edo))
				{$nombre_edo=trim($rowedo['descripcion']);}
				
				//consulta a la tabla sai_arti_part_a�o para obtener el codigo de la partida 
				//$sql_tabl="SELECT * FROM sai_seleccionar_campo('sai_item_partida','part_id','id_item='||'''$codigo''','',2) resultado_set(part_id varchar)";  //echo($sql_tabl);
				$sql_tabl = "SELECT part_id, pres_anno
				FROM
					sai_item_partida
				where
					id_item = '637'
					AND pres_anno = 
  					(
					SELECT 
  						MAX(pres_anno)
					FROM
  						sai_item_partida
					WHERE
						id_item = '637'
					)
				";
			    $resultado_set_tabl=pg_query($conexion,$sql_tabl) or die("Error al mostrar codigo de la partida"); 
			    
			    //echo $sql_tabl;
			
			    if($rowar=pg_fetch_array($resultado_set_tabl))
				{$partida=trim($rowar['part_id']);}
		   		?>
		   		<table width="647" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
		   		 <div align="center" class="normalNegroNegrita">
		   		 <?php if(($_GET['cod_bien'])!=0) {?>C&oacute;digo seleccionado: <?php echo $codigo;?>
		   		 <?php }else{?>Nombre seleccionado: <?php echo $arti_descripcion;?><?php }?>
		   		 </div>
				<tr class="td_gray">
				<td width="136" align="center"  class="normalNegroNegrita"><strong>Partida</strong></td>
    			<td width="112" align="center"  class="normalNegroNegrita"><strong>C&oacute;digo</strong></td>
    			<td width="165" align="center"  class="normalNegroNegrita"><strong>Nombre</strong></td>
				<td width="127" align="center"  class="normalNegroNegrita"><strong>Estado</strong></td>
				<td width="83" align="center"  class="normalNegroNegrita"><strong>Opciones</strong></td>
  				</tr>
  				<tr class="normal">
				<td height="28" align="center" class="normal"><?=$partida?></td>
    			<td align="center"><?=$codigo?></td>
    			<td align="center"><?=$arti_descripcion?></td>
				<td align="center"><?=$nombre_edo?></td>
    			<td align="left" class="normal">
				  <img src="../../../imagenes/vineta_azul.gif" width="11" height="7"><a href="javascript:detalle('<?=$codigo?>')" class="normal"> Ver Detalle</a><br>
				  <?php if($idPerfil != PERFIL_ANALISTA_I_PASANTE_BIENES){?>
				  <img src="../../../imagenes/vineta_azul.gif" width="11" height="7"><a href="bien_2.php?codigo=<?=$codigo?>" class="normal"> Modificar</a>
				  <?php }?>
				</td>
 				</tr>
  </table> 
		   		<?php
		  } 	//fin del row de la tabla
		}	//fin del post del articulo
		else
			if (($_GET['cod_partida'])!=0)
			{
		   		$codigo=trim($_GET['cod_partida']);
		   		$sql_tab="SELECT part_nombre,id_item FROM sai_partida t1,sai_item_partida t2 
		   		WHERE t1.part_id=t2.part_id and t1.pres_anno='".$_SESSION['an_o_presupuesto']."' and t1.part_id='".$codigo."'";
		   		$resultado_set_tab=pg_query($conexion,$sql_tab) or die("Error al mostrar"); 
		   		if ($row_partida=pg_fetch_array($resultado_set_tab)){
		   			$nombre_partida=$row_partida['part_nombre'];
		   		}
				$nroFila= pg_num_rows($resultado_set_tab);
			    if($nroFila<=0)
				{
			 		echo "<center><font  class='normalNegrita'>"."No existen registros que cumplan con el criterio de b&uacute;squeda seleccionado"."</font></center>";
	  	    	}
				else
				   {	?>
						<table width="647" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">

						  <div align="center" class="normalNegroNegrita">Partida seleccionada: <?php echo $codigo.":".$nombre_partida;?></div>

						<tr  class="td_gray">
						<td width="151" align="center"   class="normalNegroNegrita"><strong>Partida</strong></td>
						<td width="106" align="center"   class="normalNegroNegrita"><strong>C&oacute;digo </strong></td>
						<td width="156" align="center"   class="normalNegroNegrita"><strong>Nombre</strong></td>
						<td width="127" align="center"   class="normalNegroNegrita"><strong>Estado</strong></td>
						<td width="83" align="center"   class="normalNegroNegrita"><strong>Opciones</strong></td>
						</tr>
						<?php
						$resultado_set_tab=pg_query($conexion,$sql_tab);
		   				while($rowa=pg_fetch_array($resultado_set_tab)) 
		   				{ 
							$arti_id=trim($rowa['id_item']);
							$sql_tabla1="SELECT * FROM sai_seleccionar_campo('sai_item','nombre, esta_id','id_tipo=2 and id='||'''$arti_id''','',2)
		   					resultado_set(nombre varchar, esta_id int4)";  
		   					$resultado_set_tabla1=pg_query($conexion,$sql_tabla1) or die("Error al mostrar"); 
		   					
							if($rowarti=pg_fetch_array($resultado_set_tabla1)) 
		   					{ 
								$arti_descripcion=$rowarti['nombre']; 
								$estado=trim($rowarti['esta_id']);
								
								//consulta a la tabla de sai_estado
								$sql_edo="select * from sai_consulta_desc_estado($estado) as descripcion"; 
								$resultado_set_edo=pg_query($conexion,$sql_edo) or die("Error al consultar estado");
								
								if($rowedo=pg_fetch_array($resultado_set_edo))
								{$nombre_edo=$rowedo['descripcion'];} 
								
								//consulta a la tabla sai_arti_part_a�o para obtener el codigo de la partida 
								$sql_tabl="SELECT * FROM sai_seleccionar_campo('sai_item_partida','part_id','id_item='||'''$arti_id''','',2) resultado_set(part_id varchar)";  
								$resultado_set_tabl=pg_query($conexion,$sql_tabl) or die("Error al mostrar codigo de la partida"); 
							
								if($rowar=pg_fetch_array($resultado_set_tabl))
								{$partida=$rowar['part_id'];} 
								?>
			   					<tr class="normal">
								<td height="28" align="center"><?=$partida?></td>
    							<td align="center"><?=$arti_id?></td>
    							<td align="center"><?=$arti_descripcion?></td>
								<td align="center"><?=$nombre_edo?></td>
    							<td align="left" class="normal"><img src="../../../imagenes/vineta_azul.gif" width="11" height="7"><a href="javascript:detalle('<?=$arti_id?>')" class="normal"> Ver Detalle</a><br>
								<img src="../../../imagenes/vineta_azul.gif" width="11" height="7"><a href="bien_2.php?codigo=<?=$arti_id?>" class="normal"> Modificar</a>								</td>
 								</tr>
  <?php
		  					}  //fin de la consulta interna a sai_articulo	
						} ?> </table> 
						<?php //fin del while
					} 	//fin sino es null el resultado de la consulta	
				}	//fin del post de codigo de la partida

	if (($_GET['edo'])!=0)
			{
		   	   $edo=trim($_GET['edo']);
			  $sql_tab="SELECT * FROM sai_seleccionar_campo('sai_item','id','id_tipo=2 and esta_id='||'''$edo''','nombre',1)
		   resultado_set(id varchar)";    
			  
		   	   $resultado_set_tab=pg_query($conexion,$sql_tab) or die("Error al mostrar"); 
		   	   $nroFila= pg_num_rows($resultado_set_tab);
			  
			   if($nroFila<=0)
				{
			 		echo "<center><font color='#003399' class='titularMedio'>"."No existen registros que cumplan con el criterio de b&uacute;squeda seleccionado"."</font></center>";
	  	    	}
		  else 
		     {  	       
		   		?>
				<table width="647" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
				  <div align="center" class="normalNegroNegrita">Activos en estado 
				    <?php 
				  $sql="SELECT * FROM sai_seleccionar_campo('sai_estado','esta_id,esta_nombre','esta_id=''$edo''','',2) 
				  resultado_set(esta_id int,esta_nombre varchar)"; 
	              $resultado=pg_query($conexion,$sql) or die("Error al mostrar");
				  $row=pg_fetch_array($resultado);
				  echo $row['esta_nombre'];
				  ?>
				  </div>
				<tr class="td_gray">
				<td width="136" align="center" class="normalNegroNegrita"><strong>Partida</strong></td>
    			<td width="112" align="center" class="normalNegroNegrita"><strong>C&oacute;digo</strong></td>
    			<td width="165" align="center" class="normalNegroNegrita"><strong>Nombre</strong></td>
				<td width="127" align="center" class="normalNegroNegrita"><strong>Estado</strong></td>
				<td width="83" align="center"  class="normalNegroNegrita"><strong>Opciones</strong></td>
  				</tr>
				<?php
		   			while($rowa=pg_fetch_array($resultado_set_tab)) 
		   			{ 
						$arti_id=trim($rowa['id']);
						$partida="";
						$sql_tabla1="SELECT * FROM sai_seleccionar_campo('sai_item','nombre, esta_id','id_tipo=2 and id='||'''$arti_id''','',2)
		   					resultado_set(nombre varchar, esta_id int4)";  
		   					$resultado_set_tabla1=pg_query($conexion,$sql_tabla1) or die("Error al mostrar"); 
		   					
							if($rowarti=pg_fetch_array($resultado_set_tabla1)) 
		   					{ 
								$arti_descripcion=$rowarti['nombre']; 
								$estado=trim($rowarti['esta_id']);
								
								//consulta a la tabla de sai_estado
								$sql_edo="select * from sai_consulta_desc_estado($estado) as descripcion"; 
								$resultado_set_edo=pg_query($conexion,$sql_edo) or die("Error al consultar estado");
								
								if($rowedo=pg_fetch_array($resultado_set_edo))
								{$nombre_edo=$rowedo['descripcion'];} 
								
								//consulta a la tabla sai_arti_part_a�o para obtener el codigo de la partida 
								$sql_tabl="SELECT * FROM sai_seleccionar_campo('sai_item_partida','part_id','id_item='||'''$arti_id''','',2) resultado_set(part_id varchar)";
								$resultado_set_tabl=pg_query($conexion,$sql_tabl) or die("Error al mostrar codigo de la partida"); 
							
								if($rowar=pg_fetch_array($resultado_set_tabl))
								{$partida=$rowar['part_id'];} 
								?>
			   					<tr class="normal">
								<td height="28" align="center"><?=$partida?></td>
    							<td align="center"><?=$arti_id?></td>
    							<td align="left"><?=$arti_descripcion?></td>
								<td align="center"><?=$nombre_edo?></td>
    							<td align="left" class="normal"><img src="../../../imagenes/vineta_azul.gif" width="11" height="7"><a href="javascript:detalle('<?=$arti_id?>')" class="normal"> Ver Detalle</a><br>
								<img src="../../../imagenes/vineta_azul.gif" width="11" height="7"><a href="bien_2.php?codigo=<?=$arti_id?>" class="normal"> Modificar</a>								</td>
 								</tr>
  <?php
		  					}  //fin de la consulta interna a sai_articulo	
						} ?> </table> 
  				<?php //fin del while
					} 	//fin sino es null el resultado de la consulta	
				}	//fin del post de codigo de la partida
			
///////////////////					
			
	 }// fin del else, que comprueba que existe una opcion de busqueda
?>
</form>
<!-- Formulario que evalua enl buscar art�culo por criterios m�ltiples-->
</body>
</html>
