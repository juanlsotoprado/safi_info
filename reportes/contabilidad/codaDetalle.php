<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado"))  {
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Asientos Autom&aacute;ticos</title>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </SCRIPT>
</script>
<?php	
$cont=trim($_GET['cont']);
$codigo=$_REQUEST['codigo']; 
$usuario = $_SESSION['login'];
		  
$sql="SELECT * FROM  sai_seleccionar_campo ('sai_comp_diario','comp_id,comp_fec,comp_tipo,comp_comen,comp_fec_emis,esta_id,comp_doc_id','comp_id=''$codigo''','',2) resultado_set(comp_id varchar, comp_fec date,comp_tipo varchar,comp_comen text,comp_fec_emis timestamp,esta_id int4, comp_doc_id varchar)"; 

$resultado=pg_query($conexion,$sql) or die("Error al mostrar"); 
while($row=pg_fetch_array($resultado))
			{ 
				$id_comp=trim($row['comp_id']);
				$fecha_comp=$row['comp_fec'];
				$tipo=$row['comp_tipo'];
				$comentario=$row['comp_comen'];
				$fecha_emis=$row['comp_fec_emis'];
				$documento=$row['comp_doc_id'];
			}

$total_imputacion=0;
$sql_presupuesto="SELECT * FROM sai_buscar_datos_causado('".$documento."','coda') as resultado (categoria varchar, aesp varchar, anno int2, apde_tipo bit,part_id varchar, apde_monto float8)";  
$resultado_pre=pg_query($conexion,$sql_presupuesto) or die ("Error al mostrar datos presupuestarios");

$i=0;
$total_imputacion=pg_num_rows($resultado_pre);
while ($row_pre=pg_fetch_array($resultado_pre)){
 $categoria=$row_pre['categoria'];
 $aesp =$row_pre['aesp'];
 $anno =$row_pre['anno'];
 $apde_tipo =$row_pre['apde_tipo'];
 $apde_partida[$i]=$row_pre['part_id'];

  	   /*  if ($apde_tipo==1){ //Por Proyecto
		 $query ="Select * from sai_buscar_centro_gestor_costo_proy('".trim($categoria)."','".trim($aesp)."') as result (centro_gestor varchar, centro_costo varchar)";
		}else{ //Por Accion Centralizada
		 $query ="Select * from  sai_buscar_centro_gestor_costo_acc('".trim($categoria)."','".trim($aesp)."') as result (centro_gestor varchar, centro_costo varchar)";
		 }

		$resultado_query= pg_exec($conexion,$query);
		if ($resultado_query){
		   if($row=pg_fetch_array($resultado_query)){
		   $centrog[$i] = trim($row['centro_gestor']);
		   $centroc[$i] = trim($row['centro_costo']);
		   }
		 }*/
$sql_nombre_presu="SELECT * FROM sai_consulta_proyecto_accion('".$categoria."','".$aesp."','$apde_tipo','$anno') as resultado (nombre_categ varchar, nombre_esp varchar,cg varchar, cc varchar)";
//echo $sql_nombre_presu."<br>";
$resultado_nomb_pre=pg_query($conexion,$sql_nombre_presu) or die ("Error al buscar datos presupuestarios");
if ($row_pre=pg_fetch_array($resultado_nomb_pre)){
 $nom_categoria=$row_pre['nombre_categ'];
 $nom_aesp =$row_pre['nombre_esp'];
 $centrog[$i] = trim($row_pre['centro_gestor']);
 $centroc[$i] = trim($row_pre['centro_costo']);
$sql_documento="SELECT * FROM sai_seleccionar_campo('sai_sol_pago','numero_reserva,comp_id','sopg_id=''$documento''','',2) resultado_set(numero_reserva varchar,comp_id varchar)";  
$resultado_doc=pg_query($conexion,$sql_documento) or die ("Error al mostrar datos del documento");
if ($row_doc=pg_fetch_array($resultado_doc)){
 $reserva=$row_doc['numero_reserva'];
 $num_com=$row_doc['comp_id'];
} 
}
 $i++;
}
?>
</head>
<body>
  <span class="normal">  </span>
  <table width="764" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
    <tr class="td_gray">
      <td colspan="4" class="normalNegroNegrita">COMPROBANTE CONTABLE</td>
    </tr>
    <tr>
      <td colspan="5" class="normalNegrita"><div align="center">Comprobante N&uacute;mero: <?php echo $codigo;?></div></td>
    </tr>
    <tr>
      <td width="81" class="normalNegrita"><strong>Fecha:</strong></td>
      <td width="671" colspan="3" class="normal"><?php echo $fecha_comp?></td>
    </tr>
    <tr>
      <td class="normalNegrita">Tipo:</td>
      <td colspan="3" class="normal"><?php echo $tipo?></td>
    </tr>
    <tr>
      <td class="normalNegrita"><strong>Comentario:</strong></td>
      <td colspan="2" class="normal"><div align="left"><?php echo $comentario?></div></td>
    </tr>
    <tr>
      <td colspan="3" align="right" class="normalNegrita">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4" class="normalNegrita">
		<div align="center">
	    <?if ($_REQUEST['anulado']=="1") {
			$anulado=1?>
		 <font color="Red"><img src="../../imagenes/anulado.gif"><?}?>
		</div>


       <table width="747" height="87" align="center" background="../Imagenes/fondo_tabla.PNG" id="servicios">
        <tr valign="middle" class="Estilo4">
          <td width="39" class="normalNegroNegrita"><div align="center">Reng</div></td>
          <td width="127" class="normalNegroNegrita"><div align="center">Cuenta</div></td>
          <td class="normalNegroNegrita"><p align="center" class="normalNegroNegrita"><strong>Descripci&oacute;n</strong></p></td>
          <td width="116" class="normalNegroNegrita"><strong>Debe</strong></td>
          <td width="103" class="normalNegroNegrita"><strong>Haber</strong></td>
        </tr>
        <?php
	  $sql_reng="SELECT * FROM  sai_seleccionar_campo ('sai_reng_comp','comp_id,reng_comp,cpat_id,cpat_nombre,rcomp_debe,rcomp_haber,rcomp_tot_db,rcomp_tot_hab','comp_id=''$codigo''','',2) resultado_set(comp_id varchar, reng_comp int8,cpat_id varchar,cpat_nombre varchar,rcomp_debe float8,rcomp_haber float8,rcomp_tot_db float8,rcomp_tot_hab float8)"; 
	  $resultado=pg_query($conexion,$sql_reng) or die("Error al mostrar"); 
	  $total_db=0;
	  $total_haber=0;
	  while($row=pg_fetch_array($resultado))
			{ 
				$id_comp=trim($row['comp_id']);
				$comp_reng=$row['reng_comp'];
				$id_cta=$row['cpat_id'];
				$nom_cta=$row['cpat_nombre'];
				$debe=$row['rcomp_debe'];	
				$haber=$row['rcomp_haber'];		

  	  $sql= " Select * from sai_buscar_sopg_imputacion('".trim($documento)."') as result ";
	  $sql.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, tipo bit,sopg_monto_exento float8)";
	  $resultado_set= pg_exec($conexion ,$sql);
	  $valido=$resultado_set;
		if ($resultado_set)
  		{
		$total_imputacion=pg_num_rows($resultado_set);
		$i=0;
		while($row=pg_fetch_array($resultado_set))	
		{
			$matriz_imputacion[$i]=trim($row['tipo']);
			$matriz_acc_pp[$i]=trim($row['sopg_acc_pp']); // proy o acc
			$matriz_acc_esp[$i]=trim($row['sopg_acc_esp']); // acc esp
			$matriz_sub_esp[$i]=trim($row['sopg_sub_espe']); //sub-part
			$matriz_monto[$i]=trim($row['sopg_monto']+$row['sopg_monto_exento']); //monto
			$i++;
			}}

  	

 	for ($ii=0; $ii<$p; $ii++)
        {
 	 $query_partida="SELECT t3.part_id as partida FROM  sai_apartado t2, sai_apar_det t3 WHERE t2.apar_id=t3.apar_id and t2.apar_docu_id='".$documento."' and t3.part_id='".trim($arreglo_partida[$ii])."' and (t3.apde_monto='".$debe."' or t3.apde_monto='".$haber."')"; 

	 $resultado_part=pg_query($conexion,$query_partida) or die("Error al consultar el convertidor"); 
	 if ($row=pg_fetch_array($resultado_part)){
 		$partida=$row['partida'];
	 }

        }
	?>
        <tr class="normal">
          <td class="normal"><?php echo $comp_reng?></td>
          <td class="normal"><?php echo $id_cta?></td>
          <td width="338" class="normal"><div align="justify"><?php echo $nom_cta?></div>            </td>
          <td ><?php echo number_format($debe,2,'.',',')?></td>
          <td height="34" colspan="7"><?php echo number_format($haber,2,'.',',')?></td>
          </tr>
        <?php 
			$total_haber=$total_haber+$haber;  
			$total_db=$total_db+$debe;
			} 
			?>
        <tbody id="body">
        </tbody>


      </table>
      <table width="379" height="65" align="right" background="../Imagenes/fondo_tabla.PNG" id="totales">

          <tr valign="top" class="normal">
            <td class="normal">&nbsp;</td>
            <td height="17" colspan="2" class="normal"><div align="right"></div></td>
          </tr>
          <tr valign="top" class="normal">
            <td class="normal"><div align="right"><strong>Total:</strong></div></td>
            <td align="right" ><?php echo number_format($total_db,2,'.',',');?></td>
            <td width="108" height="15" align="right"><?php echo number_format($total_haber,2,'.',',')?></td>
          </tr>
          <tr valign="top" class="normal">
            <td width="141" class="normal">&nbsp;</td>
            <td width="114" class="normal">&nbsp;</td>
            <td height="15" class="normal"><label id="existepart"></label></td>
          </tr>

          <tbody id="body">
          </tbody>

        </table>      </td>

    </tr>

    <tr>
      <td colspan="4" align="center" class="normalNegrita">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4" align="center" class="normalNegrita">&nbsp;</td>
    </tr>
    <tr>
<TD colspan="5">
 <table>

      <td class="normalNegrita">Fte. Financiamiento:</td>
      <td class="normal"><?php 
      //$reserva=substr($reserva,0,2);
		$sql="SELECT * FROM sai_seleccionar_campo('sai_fuente_fin','fuef_descripcion','fuef_id ='||'''$reserva''','',2) resultado_set(fuef_descripcion varchar)";
		$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
				if($row=pg_fetch_array($resultado_set_most_p))
				{
 				 $fuente=trim($row['fuef_descripcion']); //Solicitante
				}
	
	   echo $fuente;
      ?></td>
    </tr>
    <tr><td align="right" class="normalNegrita">N&uacute;mero Compromiso:</td>
      <td align="left" class="normal"><div align="left"><?php  if ($num_com<>''){echo $num_com;}else{echo "N/A";}?></tr>

    <tr>
      <td colspan="4" align="center" class="normalNegrita"><a href="javascript:window.print()" class="normal"></a></td>
    </tr>
</table>
</TD></tr>
    <tr class="normal">
      <td colspan="4" align="center" class="style2">Este Comprobante fue generado el d&iacute;a: <?=date("d/m/y");?> a las <?=date("h:i:s");?></td>
    </tr>
    <tr>
      <td colspan="4" align="center" class="normalNegrita">&nbsp;</td>
    </tr>
    <tr>
      <td height="46" colspan="4"><?php 
	
		//Incluir la lista de las revisiones
		echo "<br>";
		$request_codigo_documento = $codigo;
		$directorio_imagenes_2 = "../../imagenes/";
		include("../../includes/revisiones_mostrar.php");
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
			include('../../includes/funciones.php');
			//Buscar el nombre del cargo y dependencia actual
			$cargo_depen_actual="";
			$sql = " SELECT * FROM sai_buscar_cargo_depen('".$perfil_actual."') as resultado ";			
			$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
			if ($row = pg_fetch_array($resultado)) {
				$cargo_depen_actual = $row["resultado"];
			}			
			$mensaje=  " Instancia actual: ".$cargo_depen_actual ;		
		}
//		echo "<div align='center'><span class='normalNegrita_naranja'> ".$mensaje." </span></div><br>";
?></td>
    </tr>

<tr>
	<td colspan="3">
	<table width="50%" border="0" class="tablaalertas" align="center">
          <tr class="td_gray">
            <td  align="center"  class="normalNegroNegrita">Imputaci&oacute;n Presupuestaria </td>
          </tr>
		  
          <tr>
            <td  class="normal" align="center" >
              <table width="100%" border="0" >
                <tr>
                  <td width="20%" class="normal"><div align="center">Partida</div></td>
		  <td width="15%" class="normal"><div align="center">Cuenta</div></td>
                  <td width="20%" class="normal"><div align="center">Monto</div></td>
                </tr>
                <tr>
                  <?php
		for ($ii=0; $ii<$total_imputacion; $ii++)
    	{
		?>
                  <td  class="normal" align="left" width="15%">
                    <div align="center">
                      <?php $id_part=$matriz_sub_esp[$ii];
                            echo $id_part;?>
                    </div></td>
		   <?$convertidor="SELECT * FROM  sai_seleccionar_campo ('sai_convertidor','cpat_id','part_id=''$id_part''','',2) resultado_set(cpat_id varchar)"; 
          	   $resultado_conv=pg_query($conexion,$convertidor) or die("Error al consultar el convertidor"); 
		  if($row=pg_fetch_array($resultado_conv))
		  { 
		   $cuenta=trim($row['cpat_id']);
		  }?>
                  <td  class="normal" align="left" width="17%">
                    <div align="center">
                      <?php echo  $cuenta;?>
                    </div></td>
              <!--    <td  class="normal" align="left" width="19%">
                    <div align="center">
                      <?php echo $categoria?>
                    </div></td>
		    <td  class="normal" align="left" width="15%">
                    <div align="center"><?php echo $aesp?></div></td>-->
                  <td  class="normal" align="left" width="20%">
                    <div align="center">
                     <?php echo  number_format($matriz_monto[$ii],2,'.',',');?>
                    </div></td>
                </tr>
                <?php 
		 }
		 ?>
            </table></td>
          </tr></table></td></tr>

    <tr>
	<td height="46" colspan="4">
      <div align="center"><a href="codaPDF.php?anulado=<?php echo $anulado;?>&cod_doc=<?php echo(trim($codigo)); ?>"><img src="../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a></div></td>
</tr>
    <tr>
      <td colspan="4" align="center" class="normalNegrita"><a href="javascript:window.print()" class="normal"><img src="../../imagenes/bot_imprimir.gif" alt="impresora" width="23" height="20" border="0" /> </a></td>
    </tr>
  </table>
</body>
</html>