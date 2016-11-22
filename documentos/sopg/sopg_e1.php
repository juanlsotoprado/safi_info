<?php 
  ob_start();
  session_start();
  require_once("includes/conexion.php");
  include('includes/arreglos_pg.php');
 
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush(); 
   exit;
  }
  ob_end_flush(); ?>
	
<?php // Leer los anexos
 
 $anexos=array(11);
 $anexos_otros=trim($_POST['txt_otro']);

 for ($i=0; $i<11; $i++)
   { 
     $anexos[$i]=0;
   }
 if (trim($_POST['chk_factura'])=="on")
	{
		$anexos[0]=1; 
    }
if (trim($_POST['chk_ordc'])=="on")
	{
		$anexos[1]=1; 
    }
if (trim($_POST['chk_contrato'])=="on")
	{
		$anexos[2]=1; 
    }
if (trim($_POST['chk_certificacion'])=="on")
	{
		$anexos[3]=1; 
    }
if (trim($_POST['chk_recibo'])=="on")
	{
		$anexos[4]=1; 
    }
if (trim($_POST['chk_ords'])=="on")
	{
		$anexos[5]=1; 
    }
if (trim($_POST['chk_pcta'])=="on")
	{
		$anexos[6]=1; 
    }	
if (trim($_POST['chk_gaceta'])=="on")
	{
		$anexos[7]=1; 
    }	
if (trim($_POST['chk_informe'])=="on")
	{
		$anexos[8]=1; 
    }		
if (trim($_POST['chk_estimacion'])=="on")
	{
		$anexos[9]=1; 
    }	
if (trim($_POST['chk_otro'])=="on")
	{
		$anexos[10]=1; 
    }			
		require_once("includes/arreglos_pg.php");
	    $arreglo_anexos=convierte_arreglo($anexos);  

		
?>	
<?php
require_once("includes/fechas.php");
$partida_IVA=trim($_SESSION['part_iva']);
$partida_IVA_ano_anterior=$_SESSION['part_iva_ano_anterior'];
$factura_num=trim($_REQUEST['txt_factura']);
$factura_fecha=trim($_POST['txt_fecha_factura']);
$factura_control=trim($_POST['txt_factura_num_control']);

if ($factura_fecha<>''){
	$factura_fecha=cambia_ing($factura_fecha);
}
if ($_POST['comp_id']<>'N/A'){
$compromiso="comp-".$_POST['comp_id'];

}else{
$compromiso="N/A";
$sopg_tp_imputacion=trim($_POST['chk_tp_imputa']);
}
//Datos provinientes de sopg_1.php
  $sopg_imp_p_c=trim($_POST['txt_cod_imputa']);  //Cod. del Proyecto o de la Accion Central
  $sopg_imputa=trim($_POST['txt_cod_accion']);   //Cod. de la Accion Especifica
  $sopg_tp_imputacion=trim($_POST['chk_tp_imputa']); //Tipo de Imputacion 
  $nomb_proy_acce=$_POST['txt_nombre_imputa'];  
  $nomb_acci_espe=$_POST['txt_nombre_accion'];  
  $pres_anno=$_SESSION['an_o_presupuesto'];
  /* Parche para que se emitan sopg cpn el año anterior */
  //$pres_anno=2014; //Año anteiror
  $usua_login=$_SESSION['login']; 
  $depe_id=$_POST['dependencia']; 	
  $depe_user=$_SESSION['user_depe_id'];		
  $tp_solicitud=$_POST['tipo_sol'];
  $sopg_bene_ci_rif=trim($_POST['hid_bene_ci_rif']); 
  $sbeneficiario=explode(",",$_POST['hid_bene_ci_rif']);
  //$sopg_bene_tp=trim($_POST['hid_bene_tp']);
  $sopg_bene_tp=explode(",",$_POST['hid_bene_tp']); 
  $sopg_detalle=trim($_POST['txt_detalle']);
  //Insertar en tabla de prioridades
  $prioridad=trim($_POST['slc_prioridad']); 
  $numero_reserva=trim($_POST['numero_reserva']);
  $sopg_observacion=trim($_POST['txt_observa']);   
  
  $sopg_contador=trim($_POST['hid_contador']); 
  $disponibilidad=true; 
  $valido=true; 

 //Para las imputaciones Arreglo de Partidas
  $largo=trim($_POST['hid_largo']);
  $total_imputacion=$largo;

	$j=0;
	for($i=0; $i<$largo; $i++)
	{  
		$matriz_imputacion[$j]=$_REQUEST['rb_ac_proy'.$i];//$sopg_tp_imputacion; 
		$matriz_acc_pp[$j]=$_REQUEST['txt_id_p_ac'.$i];//$sopg_imp_p_c; 
		$matriz_acc_esp[$j]=$_REQUEST['txt_id_acesp'.$i];//$sopg_imputa; 
		$matriz_sub_esp[$j]=$_REQUEST['txt_id_pda'.$i]; 
		if (($matriz_sub_esp[$j]==$partida_IVA) && ($_POST['comp_id']=='N/A')){
		 $matriz_sub_esp[$j]=$partida_IVA_ano_anterior;	
		}
		$matriz_uel[$j]=$_REQUEST['txt_id_depe'.$i]; 
		$monto_partida=$_REQUEST['txt_monto_pda'.$i]+$_REQUEST['txt_monto_pda_exento'.$i];
		$matriz_monto_partida[$j]=str_replace(",","",$monto_partida);
		$matriz_monto[$j]=str_replace(",","",$_REQUEST['txt_monto_pda'.$i]);
        $matriz_monto_base[$j]=str_replace(",","",$_REQUEST['txt_monto_pda'.$i]);
	    $matriz_monto_exento[$j]=str_replace(",","",$_REQUEST['txt_monto_pda_exento'.$i]);
		$j++;
	}

	$monto_iva_impu=str_replace(",","",$_POST['txt_monto_iva_tt']);
	
	if ($monto_iva_impu >0)
	{
	    $matriz_imputacion[$j]=$sopg_tp_imputacion;
		$matriz_acc_pp[$j]=$sopg_imp_p_c; 
		$matriz_acc_esp[$j]=$sopg_imputa; 
		$matriz_sub_esp[$j]=$partida_IVA;
		if (($matriz_sub_esp[$j]==$partida_IVA) && ($_POST['comp_id']=='N/A')){
		 $matriz_sub_esp[$j]=$partida_IVA_ano_anterior;	
		}
		$matriz_uel[$j]=$matriz_uel[($j-1)]; 
		$matriz_monto[$j]=str_replace(",","",$monto_iva_impu);
		$matriz_monto_base[$j]=str_replace(",","",$monto_iva_impu);
		$matriz_monto_partida[$j]=str_replace(",","",$monto_iva_impu);
		$matriz_monto_exento[$j]=0;
		$j++;
		$largo=$j;
		
	}
	$total_imputacion=$j;

	$arreglo_acc_pp=convierte_arreglo($matriz_acc_pp);  
	$arreglo_acc_esp=convierte_arreglo($matriz_acc_esp); 
	$arreglo_sub_esp=convierte_arreglo($matriz_sub_esp); 
	$arreglo_monto=convierte_arreglo($matriz_monto);
	$arreglo_monto_partidas=convierte_arreglo($matriz_monto_partida);
	$arreglo_monto_exento=convierte_arreglo($matriz_monto_exento);
	$arreglo_uel=convierte_arreglo($matriz_uel); 
	$arreglo_tipo_impu=convierte_arreglo($matriz_imputacion); 


/*Verifico la disponibilidad en el convertidor*/
 $satisfactorio=true;
 $largo_partidas=count($matriz_sub_esp);
 $cont=0;

 while ($cont<$largo_partidas)
 {  $codigo_convertidor='';
	$partida=$matriz_sub_esp[$cont];
	$condicion="";
    if (substr($partida,0,1)=="4")
     $condicion=" part_id= '".$partida."' ";
    else
	 $condicion=" cpat_id= '".$partida."' ";

	 $sql_e="SELECT part_id,cpat_id, cpat_pasivo_id FROM sai_convertidor WHERE ".$condicion;
   //$sql_e="SELECT * FROM sai_seleccionar_campo('sai_convertidor','cpat_id, cpat_pasivo_id','part_id='||'''$partida''','',2) resultado_set(cpat_id varchar,cpat_pasivo_id varchar)"; 
   $resultado_set=pg_query($conexion,$sql_e) or die("Error al mostrar");
   if($row=pg_fetch_array($resultado_set))
   {
	  $codigo_convertidor=trim($row['cpat_id']).$row['cpat_pasivo_id'];
	  $codigo_partida[$cont]=trim($row['part_id']);
	}

     if (strlen($codigo_convertidor)<30)
      {      
		$satisfactorio=false;
		$sin_convertidor=$partida;
		$disponibilidad=false;
      }
     $cont=$cont+1;
   }

   $arreglo_partida_temporal=convierte_arreglo($codigo_partida); 

	if ($satisfactorio)
	{
	$disponible=array($largo);
	for ($i=0; $i<$largo-1; $i++)
	{   
	if ( $matriz_sub_esp[$i] ==$partida_IVA )
	 {$disponible[$i]=true; }
	 else
	 {
		$monto_total = $matriz_monto_partida[$i] * $sopg_contador;
        $disponible[$i]=true;
		$sqla="select * from sai_pres_consulta_disp(".$pres_anno.",'". $matriz_imputacion[$i]."','".$matriz_acc_pp[$i]."','".$matriz_acc_esp[$i]."','".$matriz_sub_esp[$i]."','". $matriz_uel[$i]."',".$monto_total.") as monto_dispo ";
		
		$resultado_dispo = pg_exec($conexion ,$sqla);
		$valido=$resultado_dispo;
		
		if ($valido)
		{
			
			$row = pg_fetch_array($resultado_dispo,0); 
			$disponible_monto=$row[0];
			
			if ($disponible_monto<0)
			{
				$disponible[$i]=true;
				$disponibilidad=true;
			}
			else
				{
				   $disponible[$i]=true;
				}
		}
	 }
	} 
    }else{
	  $valido =true;
	  }

?>



<?php 
 if($disponibilidad && $valido)
{
	
	$sopg_monto=str_replace(",","",$_POST['txt_monto_tot']);
	$codigo2="";
    for ($k=0;$k<count($sbeneficiario);$k++) { //ciclo por beneficiario
    	$obs_extra=$_REQUEST['obs_extra'.$k];
    	$edo_vzla=$_REQUEST['estado'.$k];
    	$nombre_edo="";
    	$query_edo="SELECT edo_nombre FROM sai_edos_venezuela WHERE edo_id='".$edo_vzla."'";
    	$result_query=pg_exec($conexion,$query_edo);
		if ($row=pg_fetch_array($result_query)){
			$nombre_edo=$row['edo_nombre'];
		}
    	$sopg_observacion=$_POST['txt_observa']." ".$obs_extra." ESTADO ".$nombre_edo.".";
    	$sopg_observacion=mb_strtoupper($sopg_observacion,"ISO-8859-1");

    $sql="select * from sai_insert_sol_pago('".$depe_id."', ".$sopg_monto.", ".$pres_anno.", '".$usua_login."', '";
	$sql .= $sbeneficiario[$k]."',".$sopg_bene_tp[$k].", '".$sopg_detalle."', '";
	$sql .= $sopg_observacion."', '".$arreglo_acc_pp."', '";
	$sql .= $arreglo_acc_esp."', '".$arreglo_tipo_impu."','".$arreglo_sub_esp."','".$arreglo_monto."',".$prioridad.",'";
	$sql .= $arreglo_monto_exento."','".$factura_num."', '".$factura_fecha."', '".$factura_control."','".$arreglo_uel."',
	'".$numero_reserva."',".$tp_solicitud.",'".$depe_user."','".$compromiso."',".$edo_vzla.",'".$arreglo_partida_temporal."') as resultado_set(text)";
	//echo $sql;
	$resultado_insert = pg_exec($conexion ,$sql);
	$valido=$resultado_insert;
	if($resultado_insert)
	{
		$rowa = pg_fetch_array($resultado_insert,0); 
		if ($rowa[0] <> null)
		{
			
			$sql_comp="SELECT sc.pcta_id,pcta_monto_solicitado FROM sai_comp sc,sai_pcuenta sp WHERE comp_id='".$compromiso."' and sc.pcta_id=sp.pcta_id";
		    $result_comp=pg_exec($conexion,$sql_comp);
		    if ($row_comp=pg_fetch_array($result_comp)){
			  $pcta_actual=$row_comp['pcta_id'];
			  $monto_pagar=$row_comp['pcta_monto_solicitado'];
		    } else{
		    	$monto_pagar=1;
		    }
			if ($tp_solicitud=="19"){
			
			$sql_islr="SELECT * FROM sai_retenciones_islr WHERE cedula='".$sbeneficiario[$k]."'";
			$result_islr=pg_exec($conexion,$sql_islr);
			if ($row_islr=pg_fetch_array($result_islr)){
			 $pcta=$row_islr['pcta_id'];
			 if ($pcta!=$pcta_actual){
			 	   $sql="UPDATE sai_retenciones_islr SET monto_pagado=0, validar=1,pcta_id='".$pcta_actual."',monto_pagar=".$monto_pagar." WHERE cedula='".$sopg_bene_ci_rif."'";
			 	   $result=pg_query($conexion,$sql);
			      }
			}else{
			$sql="INSERT INTO sai_retenciones_islr VALUES ('".$sbeneficiario[$k]."',0,1,'".$pcta_actual."',".$monto_pagar.")";
			$result=pg_query($conexion,$sql);
			
			}
		  }
		    
		    
			$codigo=trim($rowa[0]);
			$sql_anexo="Select * from sai_insert_sopg_anexos('".$codigo."','". $arreglo_anexos ."','". $anexos_otros."') as retorno ";
			$resultado_anexo = pg_exec($conexion ,$sql_anexo);
			$valido=resultado_anexo;
			$cod_doc=$codigo;
			$estado_doc=10;
			$prioridad_doc=$prioridad;
			if ($valido)
			{
			//Se agrega el registro en la tabla de doc generados (sai_doc_genera) 
			$sql = " SELECT * FROM sai_insert_doc_generado('$cod_doc','$request_id_objeto_sig','$request_id_hijo','$user_login','$user_perfil_id',$estado_doc,$prioridad_doc,'$grupo_particular','$numero_reserva') as resultado ";			

			$resultado = pg_query($conexion,$sql) ;
			$valido=$resultado;
			if ($row = pg_fetch_array($resultado)) {
				$inserto_doc = $row["resultado"];
				include("includes/respaldos_e1.php");
			 }	
			$sql = " SELECT * FROM sai_modificar_estado_doc_pres('$cod_doc',55) as resultado ";
			$resultado = pg_query($conexion,$sql) or die("Error al modificar el estatus presupuestario");
			}	
		}
		
  $porcentaje="{".str_replace(",","",$_POST['opc_por_iva'])."}";
  $monto_base="{".str_replace(",","",$_POST['txt_monto_subtotal'])."}";
  $monto_iva="{".str_replace(",","",$_POST['txt_monto_iva_tt'])."}";
  
  $sql ="select * from sai_insert_documento_iva('".trim($codigo)."','";
  $sql.=$monto_iva."','". $monto_base."','". $porcentaje."','0') as reto";
  $resultado_docu_iva = pg_exec($conexion ,$sql);
  $valido=$resultado_docu_iva;
  if ($resultado_docu_iva)
	{	
		$row = pg_fetch_array($resultado_docu_iva,0); 
					if ($row['reto']==0) 
					{
					    $valido=false;
					}
	}
 
		  //*********QUITAR VALIDACION DE DISPONIBILIDAD PORQUE NO DEBERIA VALIDARCE

	if ($_POST['comp_id']<>'N/A'){ 
	//ACTUALIZAR MONTO DISPONIBLE DEL COMP, SIEMPRE QUE TENGA COMPROMISO ASOCIADO
	for($j=0; $j<$total_imputacion; $j++)
	  {  
	  $query_disponible="SELECT monto as disponible FROM sai_disponibilidad_comp WHERE comp_id='".$compromiso."' and partida='".$matriz_sub_esp[$j]."' and comp_acc_pp='".$matriz_acc_pp[$j]."' and comp_acc_esp='".$matriz_acc_esp[$j]."'";
	  $resultado_query = pg_exec($conexion,$query_disponible);
	  if ($row=pg_fetch_array($resultado_query)){
		$disponible=$row['disponible']-$matriz_monto_partida[$j];
		$query = "UPDATE sai_disponibilidad_comp set monto='".$disponible."' WHERE comp_id='".$compromiso."' and partida='".$matriz_sub_esp[$j]."' and comp_acc_pp='".$matriz_acc_pp[$j]."' and comp_acc_esp='".$matriz_acc_esp[$j]."'";
	    $resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al actualizar disponibilidad del Compromiso"));
	    $cod_pcta=$pcta_id;
 	  }
			
	 }
	}
	
	}
if ($k==0) $codigo2 = $codigo;
else $codigo2 = $codigo2 .",". $codigo;

}// fin del ciclo
}	

if($disponibilidad && $valido)
{
	//Datos para mostrar
  //  $arr_subpart=trim($_POST['txt_arreglo_part']);
  //  $arr_monto=trim($_POST['txt_arreglo_mont']);
    $nomb_benef=$_POST['hid_beneficiario'];  
    $nomb_depe_bene=$_POST['hid_dependencia'];  
    $nomb_documento=$_POST['txt_documento']; 
	}


/*Consulta la tabla de impuestos IVA por documento*/
if ($valido &&  $disponibilidad)
  {
   $docu_base=  $codigo;
   $elem_imp_iva=0;
   $sql=  " select * from sai_buscar_docu_iva('".trim($docu_base)."') as result  ";
   $sql.= "( docg_id varchar,ivap_porce float4, docg_monto_base float8, docg_monto_iva float8)";
   $resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
		if ($resultado_set)
  		{
			$elem_imp_iva=pg_num_rows($resultado_set);
			$iva_porce=array($elem_imp_iva);
			$iva_monto=array($elem_imp_iva);
			$subtotal_xx=0;
			$iva_xx=0;
			$ii=0;
 			while($row_iva=pg_fetch_array($resultado_set))	
			 {
			  if  ( $row_iva['ivap_porce']> 0)
			   {
			   $iva_monto[$ii]=trim($row_iva['docg_monto_iva']);
			   $iva_porce[$ii]=trim($row_iva['ivap_porce']);
			   $subtotal_xx=trim($row_iva['docg_monto_base']);
			   $iva_xx=$iva_xx + $iva_monto[$ii];
			   
			   $ii++;
			  }//Del if del porcentaje
			 } //Del While
			 $elem_imp_iva=$ii;
		} //Del set
		
	}	//Del valido


?>		

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<title>SAFI.:Ingresar Solicitud de Pago:.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>
<script language="javascript" src="js/func_montletra.js"></script>

</head>
 <body onload=" codigo_validacion(); ">

	<table  align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
	<?php if ($valido && $disponibilidad)
	{?>
	<tr>
		<td colspan="3">
			<table width="100%">
			<tr bgcolor="#000099"> 
		    <tr class="td_gray"> 
			 <td colspan="3" class="normalNegroNegrita" align="center">INGRESAR SOLICITUD DE PAGO</td>
  		    </tr>
			<tr> 
			<td height="21" colspan="3" valign="middle" class="td_gray"><div align="left" class="normalNegroNegrita"> 
			DATOS DEL SOLICITANTE</div></td>
			</tr>
			
				<tr> 
				<td height="28" colspan="2"><div class="normalNegrita" >Solicitud de pago n&uacute;mero: </td>
				<td width="421" class="normalNegro"> <?php echo $codigo2;?></td>
				</tr>
				<tr> 
				<td height="28" colspan="2" class="normalNegrita">Solicitante:</td>
				<td width="421" class="normalNegro"> <?php echo $_SESSION['solicitante'];?></td>
				</tr>
				<tr> 
				<td height="28" colspan="2" class="normalNegrita">C&eacute;dula de identidad:</td>
				<td class="normalNegro"> <?php echo $_SESSION['cedula']?></td>
				</tr>
				<tr> 
				<td height="28" colspan="2" class="normalNegrita">Email:</td>
				<td class="normalNegro"><?php echo $_SESSION['email']?></td>
				</tr>
				<tr> 
				<td height="28" colspan="2" class="normalNegrita">Cargo:</td>
				<td class="normalNegro"> <?php echo $_SESSION['cargo']?></td>
				</tr>
				<tr> 
				<td height="30" colspan="2" class="normalNegrita">Dependencia Solicitante:</td>
				<td class="normalNegro">
				<?$sql="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_nombre','depe_id='||'''$depe_id''','',2) resultado_set(depe_nombre varchar)"; 
				$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
				if($row=pg_fetch_array($resultado_set_most_p))
				{
 				 $dependencia=trim($row['depe_nombre']); //Solicitante
				}

				echo ($dependencia);?></td>
				</tr>
			<tr> 
			<td height="30" colspan="2" class="normalNegrita">Tipo de Solicitud: </td>
			<?
			   $sql="SELECT * FROM sai_seleccionar_campo('sai_tipo_solicitud','nombre_sol','id_sol='||'''$tp_solicitud''','',2) 
			   resultado_set(nombre_sol varchar)"; 
			   $resultado_set_most_be=pg_query($conexion,$sql) or die("Error al consultar tipo de solicitud");
			   $valido= $resultado_set_most_be;
			   if($rowbe=pg_fetch_array($resultado_set_most_be))
			   {
				$nombre_sol=$rowbe['nombre_sol'];
				 
			   }
?>
			<td class="normalNegro"><?echo $nombre_sol;?></td>
			</tr>
				<tr> 
				<td height="30" colspan="2" class="normalNegrita">Tel&eacute;fono de ofic.:</td>
				<td class="normalNegro"><?php echo $_SESSION['tlf_ofic']?></td>
				</tr>
				</table>			</td>
		</tr>
		<tr>
			<td colspan="3">
				<table width="100%">
				<tr class="td_gray"> 
				<td height="21" colspan="3" valign="middle"  class="td_gray"><div align="left" class="normalNegroNegrita"> 
				DATOS DEL BENEFICIARIO</div></td>
				</tr>
				<tr> 
				<td height="29" colspan="2" class="normalNegrita">Beneficiario:</td>
				<td width="419" class="normalNegro"><?php echo $nomb_benef;?></td>
				</tr>
				<tr> 
				<td height="28" colspan="2" class="normalNegrita">C.I. o RIF:</td>
				<td class="normalNegro"><?php echo $sopg_bene_ci_rif;?></td>
				</tr>
				</table>			</td>
		</tr>
		<tr>
	<td>
		<table width="100%" class="tablaalertas">
		<tr > 
        <td height="21" colspan="8" valign="middle" class="td_gray"><div align="left" class="normalNegroNegrita"> 
        DOCUMENTOS ANEXOS</div></td>
        </tr>
		
		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_factura" type="checkbox" id="chk_factura"  disabled="true" <?php if ($anexos[0]==1){echo "checked";}?> /></td>
		 <td height="21" >Factura</td>
        <td height="21" ><input name="chk_ordc" type="checkbox" id="chk_ordc" disabled="true" <?php if ($anexos[1]==1){echo "checked";}?>/></td>
		<td height="21" >Orden de Compra</td>
        <td height="21" ><input name="chk_contrato" type="checkbox" id="chk_contrato" disabled="true" <?php if ($anexos[2]==1){echo "checked";}?>/></td>
		<td height="21" >Contrato</td>
        <td height="21" ><input name="chk_certificacion" type="checkbox" id="chk_certificacion" disabled="true" <?php if ($anexos[3]==1){echo "checked";}?>/></td>
        <td height="21" >Certificaci&oacute;n del control perceptivo</td>
  </tr>
		
		<tr class="normalNegro"> 
        <td height="21"><input name="chk_informe" type="checkbox" id="chk_informe" disabled="true" <?php if ($anexos[8]==1){echo "checked";}?>/></td>
        <td height="21" >Informe o solicitud de pago a cuentas</td>
        <td height="21"><input name="chk_ords" type="checkbox" id="chk_ords" disabled="true" <?php if ($anexos[5]==1){echo "checked";}?>/></td>
        <td height="21">Orden de servicio</td>
        <td height="21"><input name="chk_pcta" type="checkbox" id="chk_pcta" disabled="true" <?php if ($anexos[6]==1){echo "checked";}?>/></td>
        <td height="21">Punto de cuenta</td>
	    </tr>
		<tr class="normalNegro"> 
        <td height="21"><input name="chk_otro" type="checkbox" id="chk_otro" disabled="true" <?php if ($anexos[10]==1){echo "checked";}?>/></td>
        <td height="21">Otro (especifique)</td>
		<td height="21"></td>
        <td height="21"><input type="text" name="txt_otro" id="txt_otro" class="normal" size="25" maxlength="25" value="<?php echo $anexos_otros;?>"   disabled="true"></td>
        </tr>

	 </table>
	 </td>
	 </tr> 
		<td >
				<table width="100%">
				<tr class="td_gray"> 
				<td height="21" colspan="3" valign="middle"><div align="left" class="normalNegroNegrita"> 
				DETALLES DE LA SOLICITUD</div></td>
				</tr>

				<tr> 
     <td height="28"  colspan="2"  class="normalNegrita">Factura N&deg;</td>
     <td valign="middle">
            <input name="txt_factura"  type="text" class="normalNegro" id="txt_factura" size="20" maxlength="20"   valign="right"  align="right"  readonly="readonly" value="<?php echo $factura_num;?>"/>
  <font class="normalNegrita"> Fecha:</font>
  <input name="txt_fecha_factura" type="text" class="normalNegro" id="txt_fecha_factura" size="12" readonly="readonly"  value="<?php echo $factura_fecha;?>" />
  <font class="normalNegrita"> N&deg; de control:</font>
  <input name="txt_factura_num_control"  type="text" class="normalNegro" id="txt_factura_num_control" size="11" maxlength="10"   valign="right"  align="right"  readonly="readonly"  value="<?php echo $factura_control;?>"/>
 </td>
      </tr>
	<tr> 
      <td height="28" colspan="2" class="normalNegrita">Prioridad:</td>
      <td valign="middle"> 
        <select name="slc_prioridad" class ="normalNegro" disabled>
         <option value="1" <?php if ($prioridad==1) { echo "selected"; } ?> >Baja</option>
          <option value="2" <?php if ($prioridad==2) { echo "selected"; } ?>>Media</option>
          <option value="3" <?php if ($prioridad==3) { echo "selected"; } ?>>Alta</option>
        </select>
        </td>
    </tr>
	<tr><td height="28" colspan="2"  class="normalNegrita">Fuente de Financiamiento</td>
	<td class="normalNegro"><?
	$sql="SELECT * FROM sai_seleccionar_campo('sai_fuente_fin','fuef_descripcion','fuef_id='||'''$numero_reserva''','',2) resultado_set(fuef_descripcion varchar)"; 
				$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
				if($row=pg_fetch_array($resultado_set_most_p))
				{
 				 $fuente=trim($row['fuef_descripcion']); //Solicitante
				}
	
	echo $fuente;?></td></tr>
		
			<tr><td height="28" colspan="2"  class="normalNegrita">N&uacute;mero del Compromiso</td>
			<td class="normalNegro"><?echo $_POST['comp_id'];?></td></tr>
				<tr> 
				<td height="27" colspan="2" class="normalNegrita">Motivo del pago:</td>
				<td class="normalNegro"><?php echo $sopg_detalle;?></td>
				</tr> 
				<tr> 
				<td height="21" colspan="2" class="normalNegrita">Observaci&oacute;n:</td>
				<td class="normalNegro">
				<?php if($_POST['txt_observa']==""){echo "No posee";}else{echo $_POST['txt_observa'];} ?>				</td>
				</tr>
			
				
        <tr>
	  <td colspan="3">
	   <table width="60%" border="0"  class="tablaalertas" align="center">
            <tr class="td_gray">
              <td  align="center" class="normalNegroNegrita">Imputaci&oacute;n presupuestaria </td>
             </tr>
	     <tr>
              <td class="normal" align="center">
               <table width="100%" border="0">
                <tr class="normalNegro">
                  <td align="left"><div align="center">ACC.C/PP</div></td>
                  <td align="left"><div align="center">Acci&oacute;n espec&iacute;fica </div></td>
                  <td width="10%" align="left" ><div align="center">Dependencia</div></td>
                  <td width="15%" align="left"  ><div align="center">Partida/Cuenta contable</div></td>
                  <td width="39%" align="left" ><div align="center">Monto Sujeto</div></td>
	          <td width="39%" align="left" ><div align="center">Monto Exento</div></td>
                </tr>
                <tr>
                  <?php
		   		  for ($ii=0; $ii<$total_imputacion; $ii++)
    	            {
    	             $sql="select * from sai_consulta_proyecto_accion('". $matriz_acc_pp[($ii)] ."','".$matriz_acc_esp[($ii)]."','".$matriz_imputacion[($ii)]."',".$pres_anno.") as resultado (nombre varchar, especifica varchar, cg varchar, cc varchar)";
					 $resultado_set = pg_exec($conexion ,$sql) or die("Error al visualizar datos");
					 if ($resultado_set)
					 {
					  $row_impu = pg_fetch_array($resultado_set,0); 
					  $cg=$row_impu['cg'];
					  $cc=$row_impu['cc'];
					 }
				  ?>
                  <td  class="peq" align="left" width="17%"><div align="center">
                      <input name="<?php echo "txt_imputa_proyecto_accion".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_proyecto_accion".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $cg;?>" readonly="readonly"/>
                    </div></td>
                  <td  class="peq" align="left" width="19%"><div align="center">
                      <input name="<?php echo "txt_imputa_accion_esp".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_accion_esp".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $cc;?>" readonly="readonly"/>
                    </div></td>
                  <td  class="peq" align="left" width="10%"><div align="center">
                      <input name="<?php echo "txt_imputa_unidad".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_unidad".$ii;?>" size="10" maxlength="10"   valign="right"  align="right" value="<?php echo $matriz_uel[$ii];?>" readonly="readonly"/>
                    </div></td>
                  <td  class="peq" align="left" width="15%"><div align="center">
                  <?php 	
                    if (substr($matriz_sub_esp[$ii],0,6)=="4.11.0"){
      				 $convertidor="SELECT cpat_id FROM sai_convertidor WHERE part_id='".$matriz_sub_esp[$ii]."'";
      				 $res_convertidor=pg_query($conexion,$convertidor);
      				 if($row_conv=pg_fetch_array($res_convertidor)){
					  $cuenta= $row_conv['cpat_id'];
      				}          	
     			  }else{
     				$cuenta=$matriz_sub_esp[$ii];
     				}?>
                      <input name="<?php echo "txt_imputa_sub_esp".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_sub_esp".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $cuenta;?>" readonly="readonly" title="<?php if (trim( $matriz_sub_esp[$ii])== $partida_IVA){echo "Impuesto al valor agregado";}?>"/>
                    </div></td>
                  <td  class="peq" align="left" width="39%"><div align="center">
                      <input name="<?php echo "txt_imputa_monto".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_monto".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($matriz_monto_base[$ii],2,'.',',');?>" readonly="readonly" />
                    </div></td>
		  <td  class="peq" align="left" width="39%"><div align="center">
                      <input name="<?php echo "txt_imputa_monto_exento".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_monto_exento".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($matriz_monto_exento[$ii],2,'.',',');?>" readonly="readonly" />
                    </div></td>
                </tr>
                <?php 
		 }
		 ?>
            </table><br></td>
          </tr>
			</table>			</td>
		</tr>
		
		<tr>
		<td class="normal"    colspan="3">
			<table width="95%" class="tablaalertas" align="center" id="tbl_detalle_iva" border="0" background="imagenes/fondo_tabla.gif">
			<tr class="td_gray">
			<td height="19" colspan="2" align="center" class="normalNegroNegrita">
			DETALLE DEL IMPUESTO AL VALOR AGREGADO (IVA) 
			</td>
			</tr>
			<tr>
			<td height="19"  align="center" class="normalNegrita">Monto Base: 
			  <input name="txt_monto_subtotal" type="text" class="normalNegro" id="txt_monto_subtotal" value="<?php echo (number_format($subtotal_xx,2,'.',','));?>" size="25" maxlength="25" readonly="" align="right"> </td>
			<td height="19"  align="center" class="normalNegrita">Monto IVA: 
			  <input name="txt_monto_iva_tt" type="text" class="normalNegro" id="txt_monto_iva_tt" value="<?php echo (number_format($iva_xx,2,'.',','));?>" size="25" maxlength="25" readonly="" align="right"> </td>
			</tr>
			<?php 
			if ($elem_imp_iva>0) 
			{
			?>
			<tr >
			<td width="50%" class="normal" align="center">Impuesto %</td>
			<td width="50%" class="normal" align="center">Monto (Bs.)</td>
			</tr>
			<?php 
			for ($xt=0; $xt<$elem_imp_iva; $xt++)
			{
			?> 
			<tr >
			<td width="50%"  align="center"><input name="<?php echo "txt_iva%".$xt;?>" type="text" class="normalNegro" id=<?php echo "txt_iva%".$xt;?> value="<?php echo (number_format($iva_porce[$xt],2,'.',','));?>" size="6" maxlength="6" readonly="" align="right"></td>
			<td width="50%"  align="center"><input name="<?php echo "txt_iva_monto%".$xt;?>" type="text" class="normalNegro" id="<?php echo "txt_iva_monto%".$xt;?>" value="<?php echo  (number_format($iva_monto[$xt],2,'.',',')); ?>"  size="25" maxlength="25" readonly="" align="right"></td>
			</tr>
			<?php
			} //Del For
			} //Del If
			?>
		</table><br>
		</td>	 
	</tr>		
		<tr class="td_gray"> 
		<td height="21" colspan="3" valign="middle"><div align="left" class="normalNegroNegrita"> 
		TOTAL A PAGAR</div></td>
		</tr>
		<tr>
		<td height="31" colspan="3">
		<div align="left"><span class="normalNegrita">
		En Bs.
		    <input type="text" name="txt_monto_tot" value="<?php echo trim($_POST['txt_monto_tot']); ?>" size="20" readonly="readonly" class="normalNegro">
		</span></div>		</td>
		</tr>
		<tr>
		<td height="31" colspan="3">
		<div align="left"><span class="normalNegrita">
		En Letras:<textarea name="txt_monto_letras" id="txt_monto_letras" rows="2" cols="70" class="normalNegro" readonly><?php echo trim($_POST['txt_monto_letras']);?></textarea>
		</span></div>		</td>
		</tr>
		<tr>
		  <td colspan="3" class="normal">
		  
		  <table width="420" align="center">
			<?
          	 include("includes/respaldos_mostrar.php");
			?>
		</table>
		
		  </td>
		<tr>
		<td  colspan="3" class="normal">		<div align="center">
			Esta solicitud de pago fue registrada el d&iacute;a: 
			<?php   echo date ("d/m/Y ") ." a las ". date ("h:i:s a ");?>
			<br>
		 <!-- <a href="javascript:window.print()" class="normal"><img src="imagenes/bot_imprimir.gif" width="23" height="20" border="0"></a>-->
<div align="center"><a href="documentos/sopg/sopg_pdf.php?cod_doc=<?php echo(trim($codigo)); ?>&tipo=L">Formato 1 (lineal)<img src="imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a>		   <br>
				   <a href="documentos/sopg/sopg_pdf.php?cod_doc=<?php echo(trim($codigo)); ?>&tipo=F">Formato 2 (firmas x hoja)<img src="imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a>		   </br></div>		
	</div></td>
		</tr>
		
		

		
		
		<tr>
		  <td colspan="3" class="normal">&nbsp;</td>
	  </tr>
		
		<tr>
		  <td colspan="3" class="normal">&nbsp;</td>
	  </tr>
<?php } 

	if ($satisfactorio==false)
	 {
			?>
		  <tr>
		    <td  colspan="3" class="normalNegrita">
		  <div align="center">
   		  <img src="imagenes/vineta_azul.gif" width="11" height="7">
		  La partida <?php echo $sin_convertidor;?> no se encuentra en el convertidor<br>
		 <?php echo(pg_errormessage($conexion)); ?><br><br>
		 <img src="imagenes/mano_bad.gif" width="31" height="38">
		 <br><br>
	 	<a href="javascript:window.print()" class="normal"><img src="imagenes/boton_imprimir.gif" width="23" height="20" border="0"></a>
		<br><br>
		<!-- <a href="documentos.php?tipo=<? echo $request_id_tipo_documento; ?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('regresar','','imagenes/boton_reg_blk.gif',1)"><img src="imagenes/boton_reg.gif" name="regresar" width="90" height="31" border="0"></a>	 -->				</div>					</td>
		 </div></tr>
		 
		<?php
		}?>
		</table>
		</table>

</body>
</html>
