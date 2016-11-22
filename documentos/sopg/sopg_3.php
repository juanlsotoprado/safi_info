<?php
	require_once(dirname(__FILE__) . '/../../init.php');

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
	ob_end_flush(); 


 	$cod_doc = $request_codigo_documento;
	$codigo = $cod_doc;
	$partida_IVA=trim($_SESSION['part_iva']);

//Buscar c�digo en sai_sol_pago
$sql_p="SELECT * FROM sai_sol_pago WHERE sopg_id='".$codigo."'"; 
$resultado_set_most_p=pg_query($conexion,$sql_p);
$valido=$resultado_set_most_p;
if($row=pg_fetch_array($resultado_set_most_p))
{
  require_once("includes/fechas.php");
  $depe_id=trim($row['depe_solicitante']); //Solicitante
  $tp_sol=trim($row['sopg_tp_solicitud']);
  $sopg_monto=trim($row['sopg_monto']); 
  $sopg_fecha=trim($row['sopg_fecha']);
  $pres_anno=trim($row['pres_anno']);
  $esta_id=trim($row['esta_id']);
  $usua_login=trim($row['usua_login']); //Solicitante
  $sopg_bene_ci_rif=trim($row['sopg_bene_ci_rif']);
  $sopg_bene_tp=trim($row['sopg_bene_tp']);
  $sopg_detalle=trim($row['sopg_detalle']);
  $sopg_observacion=trim($row['sopg_observacion']);
  $fecha_crea=trim($row['sopg_fecha']);
  $factura_num=trim($row['sopg_factura']);
  $factura_control=trim($row['sopg_factu_num_cont']);
  $factura_fecha=cambia_esp(trim($row['sopg_factu_fecha']));
  $fecha_sol=cambia_esp(trim($row['sopg_fecha']));
  $numero_reserva=trim($row['numero_reserva']);
  $comp_id=trim($row['comp_id']);
  $otro=0;
  
$sql="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_nombre','depe_id='||'''$depe_id''','',2)
resultado_set(depe_nombre varchar)"; 
$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
$valido=$resultado_set_most_p;
if($row=pg_fetch_array($resultado_set_most_p))
{
 $dependencia=trim($row['depe_nombre']); //Solicitante
}

    //Datos del Solicitante
	$sql_so="select * from sai_buscar_usuario('$usua_login','')
	resultado_set(empl_email varchar, usua_login varchar, usua_activo boolean,empl_cedula varchar, empl_nombres varchar,
	empl_apellidos varchar,empl_tlf_ofic varchar,carg_nombre varchar,depe_nombre varchar,depe_id varchar,carg_id varchar)";
	$resultado_set_most_so=pg_query($conexion,$sql_so) or die("Error al consultar el usuario");
	if($rowso=pg_fetch_array($resultado_set_most_so))
	{
		$email=trim($rowso['empl_email']);
		$cedula=$rowso['empl_cedula'];
		$solicitante=$rowso['empl_nombres'].' '.$rowso['empl_apellidos'];
		$cargo=trim($rowso['carg_nombre']);
		$telefono=trim($rowso['empl_tlf_ofic']);
	}
	
  
	    //Buscar Nombre del Documento al cual se le asocia la solicitud de pago y el nombre del estado actual
	    $sql_d="select * from sai_buscar_datos_sopg('',4,'','','$codigo','sopg','',0) 
		resultado_set(docu_nombre varchar, esta_id int4, docg_prioridad int2, esta_nombre varchar)"; 
		$resultado_set_most_d=pg_query($conexion,$sql_d) or die("Error al consultar documento");
		if($rowd=pg_fetch_array($resultado_set_most_d))
		{      $prioridad=$rowd['docg_prioridad'];
		}
		
	//Buscar datos del benefiario segun sea el tipo (1:sai_empleado 2:sai_proveedor 3:sai_viat_benef)
	if($sopg_bene_tp==1) //Empleado
	{
	 	$sql_be="select * from sai_buscar_datos_sopg('$sopg_bene_ci_rif',1,'','','','','',0) 
		resultado_set(depe_id varchar, depe_nombre varchar,empl_nombres varchar,empl_apellidos varchar)"; 
		$resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar empleado");
		if($rowbe=pg_fetch_array($resultado_set_most_be))
		{
		   $nombre_bene=$rowbe['empl_nombres'].' '.$rowbe['empl_apellidos'];
		   $depe_nombre_bene=trim($rowbe['depe_nombre']);
		}
	}
	else
	   if($sopg_bene_tp==2) //Proveedor
	   {
	       $sql_be="SELECT * FROM sai_seleccionar_campo('sai_proveedor_nuevo','prov_nombre','prov_id_rif='||'''$sopg_bene_ci_rif''','',2) 
		   resultado_set(prov_nombre varchar)"; 
		   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar proveedor");
		   if($rowbe=pg_fetch_array($resultado_set_most_be))
		   {
		   	  $nombre_bene=$rowbe['prov_nombre'];
		   	  $depe_nombre_bene=$dependencia;
		   }
	   }
	   else
	       if($sopg_bene_tp==3) //Otro beneficiario
		   {
			   $sql_be="SELECT * FROM sai_seleccionar_campo('sai_viat_benef','benvi_nombres,benvi_apellidos','benvi_cedula='||'''$sopg_bene_ci_rif''','',2) 
			   resultado_set(benvi_nombres varchar,benvi_apellidos varchar)"; 
			   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar otro beneficiario");
			   if($rowbe=pg_fetch_array($resultado_set_most_be))
			   {
				  $nombre_bene=$rowbe['benvi_nombres'].' '.$rowbe['benvi_apellidos'];
				  $depe_nombre_bene=$dependencia;
			   }
		   }
	
}//fin de consultar solicitud de pago

   //buscar los soportes
   $sql= "select * from sai_buscar_sopg_anexos ('".$codigo ."') as resultado ";
   $sql.= "(sopg_id varchar,soan_factura bit , soan_ordc bit , soan_contrato bit, soan_certificacion bit, ";
   $sql.= " soan_recibo bit, soan_ords bit, soan_pcta bit, soan_gaceta bit , soan_informe bit, soan_estimacion bit, soan_otro bit, soan_otro_deta varchar )"; 
   $resultado_set = pg_exec($conexion ,$sql);
   $valido=resultado_set;
		if ($resultado_set)
		{
		 $anexos=array(11);
		  for ($i=0; $i<11; $i++)
   			{		 
    	 		$anexos[$i]=0;
   		     }
		  if (pg_num_rows($resultado_set) >0)
		  {
	   		 $row = pg_fetch_array($resultado_set,0); 
			 $anexos_otros=trim($row['soan_otro_deta']);
			 $anexos[0]=trim($row['soan_factura']);
			 $anexos[1]=trim($row['soan_ordc']);
			 $anexos[2]=trim($row['soan_contrato']);
			 $anexos[3]=trim($row['soan_certificacion']);
			 $anexos[4]=trim($row['soan_recibo']);
		   	 $anexos[5]=trim($row['soan_ords']);
		     $anexos[6]=trim($row['soan_pcta']);
			 $anexos[7]=trim($row['soan_gaceta']);
			 $anexos[8]=trim($row['soan_informe']);
		     $anexos[9]=trim($row['soan_estimacion']);
		     $anexos[10]=trim($row['soan_otro']);
		 }
 		}

 /*Consulto los impuesto que aplica para la retencion*/
if ($valido)
  {
	$sql= "select * from sai_consulta_impuestos ('1','') as resultado ";
	$sql.= "(id varchar, nombre varchar, porcetaje float4,  principal bit, tipo bit)";
	$resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
		if ($resultado_set)
  		{
			$elem_retencion=pg_num_rows($resultado_set);
			$id_impuesto=array($elem_retencion);
			$porce_impuesto=array($elem_retencion);
			$impu_nombre=array($elem_retencion);
			$impu_prici=array($elem_retencion);
			$ii=0;
 			while($row_rete=pg_fetch_array($resultado_set))	
			 {
			   $id_impuesto[$ii]=strtoupper(trim($row_rete['id']));
			   $porce_impuesto[$ii]=trim($row_rete['porcetaje']);
			   $impu_prici[$ii]=trim($row_rete['principal']);
			   $impu_nombre[$ii]=trim($row_rete['nombre']);
			   $ii++; 
			 }
		} 
	}	


/*Consulta la tabla de impuestos IVA por documento*/
if ($valido)
  {
  
   $docu_base=  $codigo;
   $elem_imp_iva=0;
   $subtotal_xx=0;
   $iva_xx=0; 
   $sql=  " select * from sai_buscar_docu_iva('".trim($docu_base)."') as result  ";
   $sql.= "( docg_id varchar,ivap_porce float4, docg_monto_base float8, docg_monto_iva float8)";
   $resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
		if ($resultado_set)
  		{
			$elem_imp_iva=pg_num_rows($resultado_set);
			$iva_porce=array($elem_imp_iva);
			$iva_monto=array($elem_imp_iva);

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
			 }//Del While
			 $elem_imp_iva=$ii;
		}//Del set

	}//Del valido

/*Consulto las retenciones previas del documento */
$tt_retencion=0;
if ($valido)
  {
	$sql= "select * from sai_buscar_sopg_reten ('".trim($codigo)."') as resultado ";
	$sql.= "(docu_id varchar, impu_id varchar, rete_monto float8,  por_rete float4,";
	$sql.= "por_imp float4,servicio varchar,monto_base float8)";
	$resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
		if ($resultado_set)
  		{
			$elem_existe=pg_num_rows($resultado_set);
			$id_impuesto_doc=array($elem_existe);
			$rete_monto_doc=array($elem_existe);
			$por_rete_doc=array($elem_existe);
			$por_imp_doc=array($elem_existe);
			$servicio_doc=array($elem_existe);
			
			$tt_neto=0;
			$ii=0;
 			while($row_rete_doc=pg_fetch_array($resultado_set))	
			 {
				$id_impuesto_doc[$ii]=trim($row_rete_doc['impu_id']);
				$rete_monto_doc[$ii]=trim($row_rete_doc['rete_monto']);
				$por_rete_doc[$ii]=trim($row_rete_doc['por_rete']);
				$por_imp_doc[$ii]=trim($row_rete_doc['por_imp']);
				$servicio_doc[$ii]=trim($row_rete_doc['servicio']);
				$tt_retencion=$rete_monto_doc[$ii]+$tt_retencion;
		        $ii++; 
			 }
			 $tt_neto=$sopg_monto-$tt_retencion;
		} 
	}	

    /*Consulto las OTRAS retenciones del documento */
    $sql_be="SELECT sopg_partida_rete,sopg_ret_monto,part_nombre FROM sai_sol_pago_otra_retencion t1, sai_partida t2 WHERE sopg_id='".$codigo."' AND t1.sopg_partida_rete=t2.part_id and t2.pres_anno='".$_SESSION['an_o_presupuesto']."'"; 
	$resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar partida");
	
	if ($resultado_set_most_be)
	{
	  $elem_otras_rete=pg_num_rows($resultado_set_most_be);
	  $nombre_part=array($elem_otras_rete);
	  $id_partida=array($elem_otras_rete);
	  $monto_rete=array($elem_otras_rete);
	  $tt_neto=0;
	  $ii=0;
	  $total_otras_rete=0;
	  while($rowbe=pg_fetch_array($resultado_set_most_be))
	  {
	   	  $nombre_part[$ii]=$rowbe['part_nombre'];
	      $id_partida[$ii]=$rowbe['sopg_partida_rete'];
		  $monto_rete[$ii]=$rowbe['sopg_ret_monto'];
		  $total_otras_rete=$total_otras_rete+$monto_rete[$ii];
		  $ii++;
	   }
 		$tt_neto=$sopg_monto-$tt_retencion-$total_otras_rete;
	}



/* Se debe buscar en los parametros anuales el % de IVA*/
if ($valido)
	{$unidad_tributaria=0;
		$an_o_presupuesto= $_SESSION['an_o_presupuesto']; // A�o Presupuestario
	    $sql="select * from sai_panual(".$an_o_presupuesto.",1,'','') as resultado (dolar float4, unidad_tri float4,  pres_anno int2, obs varchar, imp_salnac float4, imp_salint float4, fecha timestamp )";
		
		$resultado_set_anual = pg_exec($conexion ,$sql) ;
		$valido=resultado_set_anual;
		if ($resultado_set_anual)
			{ 
			  $row_anual = pg_fetch_array($resultado_set_anual); 
			  $unidad_tributaria=$row_anual['unidad_tri'];
			}
		}


	//Buscar las Imputaciones
	 $total_imputacion=0;
	 if ($valido){
	  $sql= " Select * from sai_buscar_sopg_imputacion('".trim($codigo)."') as result ";
	  $sql.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, tipo bit, sopg_monto_exento float)";
	  $resultado_set= pg_exec($conexion ,$sql);
	  $valido=$resultado_set;
	
		if ($resultado_set)
  		{
		$total_imputacion=pg_num_rows($resultado_set);
		$i=0;
		$monto_base=0;
		while($row=pg_fetch_array($resultado_set))	
			 {
				$matriz_imputacion[$i]=trim($row['tipo']);
				$matriz_acc_pp[$i]=trim($row['sopg_acc_pp']); // proy o acc
				$matriz_acc_esp[$i]=trim($row['sopg_acc_esp']); // acc esp
				$matriz_sub_esp[$i]=trim($row['sopg_sub_espe']); //sub-part
				$matriz_uel[$i]=trim($row['depe_id']); //depe
				$matriz_monto[$i]=trim($row['sopg_monto']); //monto
		 		$matriz_monto_exento[$i]=trim($row['sopg_monto_exento']); //monto
				$monto_base=$monto_base+$matriz_monto[$i];
				$i++;
			}

		if ($iva_xx==0)
		{
		 $subtotal_xx=$monto_base;
		}
		}
		}	
require_once("includes/funciones.php");
?>
<script>
var listado_islr = new Array();
</script>
<?php
$sql_comp="SELECT pcta_id FROM sai_comp WHERE comp_id='".$comp_id."'";
$result_comp=pg_exec($conexion,$sql_comp);
if ($row_comp=pg_fetch_array($result_comp)){
  $pcta_actual=$row_comp['pcta_id'];
} 
		
/*$sql_islr="SELECT * FROM sai_retenciones_islr WHERE cedula='".$sopg_bene_ci_rif."'";
$result_islr=pg_exec($conexion,$sql_islr);
if ($row_islr=pg_fetch_array($result_islr)){
	$monto_pagado=$row_islr['monto_pagado'];
	$validar_islr=$row_islr['validar'];
	$pcta=$row_islr['pcta_id'];
    $monto_pagar=$row_islr['monto_pagar'];
	  echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$monto_pagado';
				registro[1]='$tp_sol';
				registro[2]='$validar_islr';
				registro[3]='$pcta';
				registro[4]='$monto_pagar';
				listado_islr[0]=registro;
				</script>
				"); 	
	 	
}else{*/
	  echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]=0;
				registro[1]='$tp_sol';
				registro[2]=1;
				registro[3]='$pcta_actual';
				registro[4]=0;
				listado_islr[0]=registro;
				</script>
				");
	
//}
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Revisar SOPG</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>
<script language="javascript" src="js/func_montletra.js"></script>
<script LANGUAGE="JavaScript">
var submonto_js=0;
var iva_monto_js=0;
compromiso=new Array();
var opcion=0;

//DESCARGAR LAS PARTIDAS AGREGADAS EN UN TXT TIPO HIDDEN DEL FORMULARIO
function crear_txt_arreglo(elemento,pos)
{
	elemento='';
	for(i=0;i<compromiso.length;i++)
		{
			elemento+=compromiso[i][pos];
			if(i!=(compromiso.length-1))
				elemento+=",";
			else
				elemento;
		}
	return elemento;
}
</script><script language="javascript">

//Funcion que revisa si los datos de la pagina estan bien
function enviar()    
{
    var num_impu=document.getElementById('tbl_retenciones').getElementsByTagName('tr').length-4;
    for (t=0;  t<num_impu; t++)
	{
	 var servicio = trim (document.getElementById('txt_servicio'+t).value);
	 var impuest= trim(document.getElementById('txt_impuesto'+t).value);
	 var porce=document.getElementById('txt_iva_'+t).value;
	 var monto=parseFloat(MoneyToNumber (document.getElementById('txt_monto_impu'+t).value));
	 if (porce=="s/n")
	 {
		 porce="";
	 }
	 else
	 {
		 porce=" "+porce + "%"
	 }
	  if (servicio=="" && monto >0 )
	  	{
		     alert("Debe colocar tipo de servicio para el impuesto "+ impuest+ porce);
			 document.getElementById('txt_servicio'+t).focus();
			 return;
		}
	
	}
   

    for (t=0;  t<10; t++)
	{
	 var part_rete=document.getElementById('part_'+t).value;
	 var monto=parseFloat(MoneyToNumber (document.getElementById('monto_rete'+t).value));

	  if (part_rete==0 && monto >0 )
	  	{
		 alert("Debe colocar la partida relacionada con la retenci\u00F3n ");
		 document.getElementById('part'+t).focus();
		 return;
		}
		//Validar que no se dupliquen las otras retenciones
	    for (k=0;  k<10; k++)
		{
	    	var partida=document.getElementById('part_'+k).value;
	    	if (part_rete!=0 && monto >0 )
		  	{
			if ((k!=t)&& (part_rete==partida)){
			 alert("No debe seleccionar dos veces la misma retenci\u00F3n, en Otras retenciones")
			 return;
			}
			}
		}
	  
	}
   
	document.form.array_depe_id.value=crear_txt_arreglo(document.form.array_depe_id.value,0);
	document.form.array_code_tipo.value=crear_txt_arreglo(document.form.array_code_tipo.value,1);
	document.form.array_p_ac.value=crear_txt_arreglo(document.form.array_p_ac.value,2);
	document.form.array_ac_esp.value=crear_txt_arreglo(document.form.array_ac_esp.value,3);
	document.form.array_part.value=crear_txt_arreglo(document.form.array_part.value,4);
	document.form.array_monto.value=crear_txt_arreglo(document.form.array_monto.value,5);
	document.form.array_abono.value=crear_txt_arreglo(document.form.array_abono.value,6);
	document.form.array_code_desc.value=crear_txt_arreglo(document.form.array_code_desc.value,7);
	
	document.form.txt_monto_retenciones_tt.value=MoneyToNumber(document.form.txt_monto_retenciones_tt.value);
	document.form.txt_monto_neto.value=MoneyToNumber(document.form.txt_monto_neto.value);
	document.form.submit();
}


function revisar_doc(id_documento,id_tipo_documento,id_opcion,objeto_siguiente_id,cadena_siguiente_id,id_objeto_actual,nombre_opcion)
{ 
	if (confirm(" Est\u00e1 seguro que desea "+nombre_opcion+"? ")) {
		
		//si la opcion es firmar, verificar que este la firma 
		if (id_opcion==3) {
		
			var firmaTextField = document.getElementById('firma');
			confirmado=0;
			confirmado=confirmar_firma(firmaTextField);			
			if (confirmado==0) {			
				return;			
			}
		
		}
		
		document.form.action="accion_ejecutar.php?tipo="+id_tipo_documento+"&accion="+id_objeto_actual+"&accion_sig=" + objeto_siguiente_id + "&hijo="+cadena_siguiente_id+"&opcion="+id_opcion+"&id="+id_documento;
		
		if (id_opcion != 5)
		{
		  enviar();
		}
		else
		{
		contenido=prompt("Indique el motivo de la devoluci\u00F3n: ","");
  		 while (contenido==null){
    		  contenido=prompt("Debe especificar el motivo de la devoluci\u00F3n: ","");
    		 }
    		 if (contenido!=null){
     		   document.getElementById('contenido_memo').value=contenido;
     		   document.form.submit();
    		}
		}
	  
	}

}


function actualizar_neto()
{ 
// Esta funcion es usada para actualizar las retenciones
 var subtotalx=parseFloat(MoneyToNumber(document.form.txt_monto_pagar.value));
 var num_impu=document.getElementById('tbl_retenciones').getElementsByTagName('tr').length-4;
 var num_otros_impu=document.getElementById('tbl_otras_retenciones').getElementsByTagName('tr').length-4;
 var i=0;
 var tt_retencion=0;
 for (i=0; i<num_impu;i++)
   {
     var monto_rete=parseFloat(MoneyToNumber(document.getElementById('txt_monto_impu'+i).value));
     tt_retencion=tt_retencion+monto_rete;
   }


 var j=0;
 var tt_otra_retencion=0;
 for (j=0; j<num__otros_impu;j++)
   {
     var monto_rete=parseFloat(MoneyToNumber(document.getElementById('monto_rete'+j).value));
     tt_otra_retencion=tt_otra_retencion+monto_rete;
   }

   var tt_neto=(subtotalx-tt_retencion-tt_otra_retencion);
   document.form.txt_monto_retenciones_tt.value=tt_retencion;
   var objeto = document.form.txt_monto_retenciones_tt;
   FormatCurrency(objeto);
   
   document.form.txt_monto_neto.value=tt_neto;
   var objeto = document.form.txt_monto_neto;
   FormatCurrency(objeto);
   //ver_monto_letra(tt_neto, 'txt_monto_letras_neto','');
}

function redondear_dos_decimal(valor) {
   float_redondeado=Math.round(valor * 100) / 100;
   return float_redondeado;
} 

function sumar_rete()
{
  var num_otras_reten=0;
  var valor_rete=0;
  var total_gene=parseFloat(MoneyToNumber(document.form.txt_monto_pagar.value));
  var max_rete=parseInt(document.form.hid_max_otra_retencion.value);
  var total_reten=parseFloat(MoneyToNumber(document.form.txt_monto_retenciones_tt.value));
  if (isNaN(total_reten)){
	total_reten=0;
  }
  
  for (i=0; i<max_rete; i++)
  {
   var monto_rete=parseFloat(MoneyToNumber(document.getElementById('monto_rete'+i).value));
   valor_rete=valor_rete+monto_rete;
  
   if (monto_rete>0){
    num_otras_reten=num_otras_reten+1;
   }
  }

   document.form.monto_rete_part.value=valor_rete;

   var tt_neto=redondear_dos_decimal(total_gene-valor_rete-total_reten);	
   document.form.txt_monto_neto.value=tt_neto;
   document.form.hid_elem_otra_retencion.value=num_otras_reten;
   ver_monto_letra(tt_neto, 'txt_monto_letras_neto','');
}

function calcular_retencion(tp_retencion,campo,tipo)
{
	var tt_retencion = 0;
	var campo_id = trim(campo.name);
	var xx = parseInt(campo_id.substring( 4, (campo_id.length)  ));
	var ivaxx = 0;
	var subtotalx = parseFloat(MoneyToNumber(document.form.txt_monto_subtotal.value));
	var total_gene = parseFloat(MoneyToNumber(document.form.txt_monto_pagar.value));
	var tt_neto = 0;
	var porce = campo.value;
	
	var unidadTributaria = <?php echo GetConfig("unidadTributaria"); ?>;
	
	if (tp_retencion=='ISLR')
	{
		var rif = "<?echo substr($sopg_bene_ci_rif,0,1); ?>";
		var factorISLR = <?php echo GetConfig("factorISLR"); ?>;
		var rete_ISR = redondear_dos_decimal(subtotalx * porce / 100);
		
		
		if(rif!="J" && rif!="G" && (subtotalx > (factorISLR * unidadTributaria)) && (porce == 1 || porce == 3))
		{
				var sustraendo = redondear_dos_decimal(factorISLR * unidadTributaria * porce / 100);
				if(rete_ISR >= sustraendo) rete_ISR -= sustraendo;
		}

		if(rif!="J" && rif!="G") {
			if(subtotalx > (factorISLR * unidadTributaria)){
				document.getElementById('txt_monto_impu'+xx).value = rete_ISR;
			} else {
				document.getElementById('txt_monto_impu'+xx).value = 0;
			}
		} else {
			document.getElementById('txt_monto_impu'+xx).value = rete_ISR;
		}
	}
	
	if (tp_retencion=='FZA')
	{
		var rete_fza = redondear_dos_decimal(total_gene * porce / 100);
		document.getElementById('txt_monto_impu'+xx).value = rete_fza;
	}
	//nuevo tipo de retencion fianza laborar 21/05/2015
	if (tp_retencion=='FZAL')
	{
		var rete_fzal = redondear_dos_decimal(subtotalx * porce / 100);
		document.getElementById('txt_monto_impu'+xx).value = rete_fzal;
	}
	//FIN nuevo tipo de retencion fianza laborar 21/05/2015
	//nuevo tipo de retencion fianza de anticipo 21/05/2015
	if (tp_retencion=='FZAA')
	{
		var rete_fzal = redondear_dos_decimal(subtotalx * porce / 100);
		document.getElementById('txt_monto_impu'+xx).value = rete_fzal;
	}
	//FIN nuevo tipo de retencion fianza de anticipo 21/05/2015
	if (tp_retencion=='LTF')
	{
		var rete_TF = redondear_dos_decimal(subtotalx * porce);
		
		if (porce >= 1) {
	  		rete_TF = (rete_TF / 100);
		}
	
		/* Si el monto es mayor de 50 unidades tributarias se aplica retención de LTF */
		if(porce>0 && subtotalx >= (unidadTributaria * 50)){
			document.getElementById('txt_monto_impu'+xx).value = rete_TF;
		}else{
			document.getElementById('txt_monto_impu'+xx).value = 0;
		}
	}
	
	if (tp_retencion=='IVA')
	{
		var tipo_iva = parseFloat(MoneyToNumber(document.getElementById('txt_iva_'+xx).value)); // Porcentaje de IVA a cobrar
		var num_tipo = parseInt(<?php echo $elem_imp_iva;?>);
		
		for (var xt = 0; xt < num_tipo; xt++)
		{
			var tipo_list = parseFloat(MoneyToNumber(document.getElementById('txt_iva%'+xt).value));
			if (tipo_iva == tipo_list)
			{
				ivaxx = parseFloat(MoneyToNumber(document.getElementById('txt_iva_monto%'+xt).value));
				xt = num_tipo;
			}
		}
		
		var rete_IVA = redondear_dos_decimal( ivaxx * porce / 100);
		document.getElementById('txt_monto_impu'+xx).value = rete_IVA;
	}
	
	var objeto = document.getElementById('txt_monto_impu'+xx);
	FormatCurrency(objeto);
	
	var num_impu=document.getElementById('tbl_retenciones').getElementsByTagName('tr').length-4;
	for (var i=0; i<num_impu;i++)
	{
		var monto_rete=parseFloat(MoneyToNumber(document.getElementById('txt_monto_impu'+i).value));
		tt_retencion=tt_retencion+monto_rete;
	}
    
	tt_neto=(total_gene-tt_retencion-document.form.monto_rete_part.value);
	if (tipo==0)
	{
		document.form.txt_monto_retenciones_tt.value=tt_retencion;
		var objeto = document.form.txt_monto_retenciones_tt;
		FormatCurrency(objeto);
		
		document.form.txt_monto_neto.value=tt_neto;
		var objeto = document.form.txt_monto_neto;
		FormatCurrency(objeto);
		ver_monto_letra(tt_neto, 'txt_monto_letras_neto','');
	}
	return;
}

</script>
</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body onload="ver_monto_letra(<?php echo  number_format($sopg_monto,2,'.','');?>, 'txt_monto_letras','');ver_monto_letra(<?php echo  number_format($tt_neto,2,'.','');?>, 'txt_monto_letras_neto','');"> 
<form name="form" action="sopg_e3.php" method="post">
<input type="hidden" name="hid_codigo_sopg" value="<?php echo $codigo;?>">
<input type="hidden" name="hid_pres_ann" value="<?php echo $pres_anno;?>">
<input type="hidden" name="hid_validar_accion" value="<?php echo $validar;?>">
<input type="hidden" name="hid_valor" value="<?php echo $valor;?>">
<input type="hidden" name="hid_documento" value="<?php echo $documento;?>">

<table  align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
<?php if  ($valido){?>
	<tr>
		<td colspan="4">
			<table width="100%">
					  <tr class="td_gray"> 
	<td colspan="3" class="normalNegroNegrita" align="center">REVISAR SOLICITUD DE PAGO</td>
  </tr>
			<tr class="td_gray"> 
			<td height="21" colspan="3" valign="midden"><div align="left" class="normalNegroNegrita"> 
			DATOS DEL SOLICITANTE </div></td>
			</tr>
			<tr> 
			<td height="28" colspan="2"><div class="normalNegrita" >Solicitud de Pago: </div></td>
			<td   class="normalNegro"> <?php echo $codigo;?></div></td>
			</tr>
			<tr> 
			<td height="28"  colspan="2"><div  class="normalNegrita">Solicitante:</div></td>
			<td class="normalNegro"> <?php echo $solicitante;?></td>
			</tr>
			<tr> 
			<td height="28"  colspan="2"><div  class="normalNegrita">C&eacute;dula de Identidad:</div></td>
			<td class="normalNegro"> <?php echo $cedula;?></td>
			</tr>
			<tr> 
		    <td height="28" colspan="2"><div  class="normalNegrita">Email:</div></td>
		    <td class="normalNegro"><?php echo $email;?></td>
		    </tr>
			<tr> 
			<td height="28"><div  class="normalNegrita">Cargo:</div></td>
			<td class="normalNegro"  colspan="2"> <?php echo $cargo;?></td>
			</tr>
			<tr> 
			<td height="30" colspan="2"><div class="normalNegrita">Dependencia Solicitante:</div></td>
			<td class="normalNegro"><?php echo $dependencia;?></td>
			</tr>
			<tr> 
			<td height="30" colspan="2"><div  class="normalNegrita">Tipo de Solicitud: </div></td>
			<?
			   $sql="SELECT * FROM sai_seleccionar_campo('sai_tipo_solicitud','nombre_sol','id_sol='||'''$tp_sol''','',2) 
			   resultado_set(nombre_sol varchar)"; 
			   $resultado_set_most_be=pg_query($conexion,$sql) or die("Error al consultar el tipo de solicitud");
			   $valido= $resultado_set_most_be;
			   if($rowbe=pg_fetch_array($resultado_set_most_be))
			   {
				$nombre_sol=$rowbe['nombre_sol'];
			   }
			?>
			<td class="normalNegro" width="540"><?echo $nombre_sol;?></td>
			</tr>
			<tr> 
			<td height="30" colspan="2"><div class="normalNegrita">Tel&eacute;fono de Ofic.:</div></td>
			<td class="normalNegro"> <?php echo $telefono;?></td>
			</tr>
			</table>		</td>
	       </tr>
	       <tr>
		<td colspan="2">
			<table width="100%">
			<tr class="td_gray"> 
			<td height="21" colspan="3" valign="midden"><div align="left" class="normalNegroNegrita"> 
			DATOS DEL BENEFICIARIO</div></td>
			</tr>
			<tr> 
			<td height="29" colspan="2"><div class="normalNegrita">Beneficiario:</div></td>
			<td width="540" class="normalNegro"><?php echo $nombre_bene;?></td>
			</tr>
			<tr> 
			<td height="28" colspan="2"><div  class="normalNegrita">C.I. o RIF:</div></td>
			<td class="normalNegro"><?php echo $sopg_bene_ci_rif;?></td>
			</tr>
			
			</table>		</td>
	</tr>
	<tr>
	<td colspan="4">
		<table width="100%" class="tablaalertas">
		<tr > 
        <td height="21" colspan="8" valign="midden" class="td_gray"><div align="left" class="normalNegroNegrita"> 
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
        <td height="21">Certificaci&oacute;n del Control Perceptivo</td>
       </tr>
		
		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_informe" type="checkbox" id="chk_informe" disabled="true" <?php if ($anexos[8]==1){echo "checked";}?>/></td>
		<td height="21" >Informe o Solicitud de Pago a Cuentas</td>
        <td height="21" ><input name="chk_ords" type="checkbox" id="chk_ords" disabled="true" <?php if ($anexos[5]==1){echo "checked";}?>/></td>
        <td height="21" >Orden de Servicio</td>
        <td height="21" ><input name="chk_pcta" type="checkbox" id="chk_pcta" disabled="true" <?php if ($anexos[6]==1){echo "checked";}?>/></td>
        <td height="21" >Punto de Cuenta</td>
	    </tr>

		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_otro" type="checkbox" id="chk_otro" disabled="true" <?php if ($anexos[10]==1){echo "checked";}?>/></td>
        <td height="21" >Otro (Especifique)</td>
		<td height="21" ></td>
        <td height="21" ><input type="text" name="txt_otro" id="txt_otro" class="normal" size="25" maxlength="25" value="<?php echo $anexos_otros;?>"   disabled="true"></td>
        </tr>

	 </table>
	 </td>
  </tr> 
	<tr>
		<td height="176" colspan="2">
			<table width="100%">
			<tr class="td_gray"> 
			<td height="21" colspan="3" valign="midden"><div align="left" class="normalNegroNegrita"> 
			DETALLES DE LA SOLICITUD </div></td>
			</tr>
			<?php
				if (trim( $factura_num<>"")){ ?>
				<tr > 
				 <td height="28" valign="midden" colspan="3"> <div class="normalNegrita">Factura N&deg;
				<input name="txt_factura"  type="text" class="class="normalNegro" id="txt_factura" size="20" maxlength="20"   valign="right"  align="right"  readonly="true" value="<?php echo $factura_num;?>"/>
				  Fecha:
				  <input name="txt_fecha_factura" type="text" class="normalNegro" id="txt_fecha_factura" size="12" readonly="true"  value="<?php echo $factura_fecha;?>" />
				  N&deg; de Control:
				  <input name="txt_factura_num_control"  type="text" class="normalNegro" id="txt_factura_num_control" size="11" maxlength="10"   valign="right"  align="right"  readonly="true"  value="<?php echo $factura_control;?>"/>
				  </div></td>
					  </tr>
	  		<?php } ?>
			<tr class="normal"> 
			<td height="28" class="normalNegrita" colspan="2"> 
			<div >Prioridad:</div>			</td>
			<td class="normal"><select name="slc_prioridad" class="normalNegro">
			<option value="3" selected>Alta</option>

				 </select></td>
			</tr>
		<tr class="normal"> 
		<td height="33" colspan="2" class="normalNegrita">
		<div >Fuente de Financiamiento:  
		</div></td>
		<td  class="normalNegro"><?
		$sql="SELECT * FROM sai_seleccionar_campo('sai_fuente_fin','fuef_descripcion','fuef_id ='||'''$numero_reserva''','',2) resultado_set(fuef_descripcion varchar)";
		$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
				if($row=pg_fetch_array($resultado_set_most_p))
				{
 				 $fuente=trim($row['fuef_descripcion']); //Solicitante
				}
	
	echo $fuente;
		?></td>
		</tr>
		<tr class="normal"><td height="28" colspan="2"  class="normalNegrita">N&uacute;mero del Compromiso:</td>
			<td class="normalNegro" width="540"><?echo $comp_id;?></td></tr>
				<tr> 
			<tr> 
			<td height="27" colspan="2"><div  class="normalNegrita">Motivo del Pago:</div></td>
			<td class="normalNegro"><?php echo $sopg_detalle;?></td>
			</tr>
			<tr> 
			<td height="21" colspan="2"><div  class="normalNegrita">Fecha de Solicitud:</div></td>
			<td class="normalNegro"><?php echo $fecha_sol;?>			</td>
			</tr>
			<tr> 
			<td height="21" colspan="2"><div  class="normalNegrita">Observaci&oacute;n:</div></td>
			<td class="normalNegro">
			<textarea name="txt_observa" rows="3" cols="60" ><?php echo  $sopg_observacion;?></textarea>			</td>
			</tr>
		</table>		</td>
	</tr>
	<tr>
		<td colspan="4">
		<table width="60%" border="0"  class="tablaalertas" align="center">
          <tr class="td_gray">
            <td  align="center"  class="normalNegroNegrita">Imputaci&oacute;n Presupuestaria </td>
          </tr>
		  
          <tr>
            <td  class="normal" align="center" >
              <table width="100%" border="0" >
                <tr>
                  <td  class="peqNegrita" align="left"><div align="center">Centro Gestor</div></td>
                  <td  class="peqNegrita" align="left"><div align="center">Centro Costo </div></td>
                  <td width="10%" align="left"  class="peqNegrita"><div align="center">Dependencia</div></td>
                  <td width="15%" align="left"  class="peqNegrita"><div align="center">Partida/Cuenta contable</div></td>
                  <td width="39%" align="left"  class="peqNegrita"><div align="center">Monto Sujeto</div></td>
		 		  <td width="39%" align="left"  class="peqNegrita"><div align="center">Monto Exento</div></td>
                </tr>
                <tr >
                  <?php
				  for ($ii=0; $ii<$total_imputacion; $ii++)
    			  {
    			  	if ($matriz_imputacion[$ii]==1){//Por Proyecto
		 $query ="Select * from sai_buscar_centro_gestor_costo_proy('".trim($matriz_acc_pp[$ii])."','".trim($matriz_acc_esp[$ii])."') as result (centro_gestor varchar, centro_costo varchar)";
		}else{//Por Accion Centralizada
		 $query ="Select * from  sai_buscar_centro_gestor_costo_acc('".trim($matriz_acc_pp[$ii])."','".trim($matriz_acc_esp[$ii])."') as result (centro_gestor varchar, centro_costo varchar)";
		 }

		$resultado_query= pg_exec($conexion,$query);
		if ($resultado_query){
		   while($row=pg_fetch_array($resultado_query)){
		   $centrog = trim($row['centro_gestor']);
		   $centroc = trim($row['centro_costo']);
		   }
		 }	
    		
				  ?>
                  <td  class="peq" align="left" width="17%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_proyecto_accion".$ii;?>  type="hidden" class="normalNegro" id=<?php echo "txt_imputa_proyecto_accion".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly="true"/>
				<input name=<?php echo "centro_gestor".$ii;?> type="text" class="normalNegro" id=<?php echo "centro_gestor".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $centrog;?>" readonly="true"/>
                    </div></td>
                  <td  class="peq" align="left" width="19%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_accion_esp".$ii;?>  type="hidden" class="normalNegro" id=<?php echo "txt_imputa_accion_esp".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="true"/>
 				<input name=<?php echo "centro_costo".$ii;?>  type="texto" class="normalNegro" id=<?php echo "centro_costo".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $centroc;?>" readonly="true"/>
                    </div></td>
                  <td  class="peq" align="left" width="10%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_unidad".$ii;?>  type="text" class="normalNegro" id=<?php echo "txt_imputa_unidad".$ii;?> size="10" maxlength="10"   valign="right"  align="right" value="<?php echo $matriz_uel[$ii];?>" readonly="true"/>
                    </div></td>
                  <td  class="peq" align="left" width="15%">
                    <div align="center">
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
                      <input name="<?php echo "txt_imputa_sub_esp".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_sub_esp".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $cuenta;?>" readonly="true" title="<?php if (trim( $matriz_sub_esp[$ii])== $partida_IVA){echo "Impuesto al Valor Agregado";}?>"/>
                    </div></td>
                  <td  class="peq" align="left" width="39%">
                    <div align="center">
                      <input name="<?php echo "txt_imputa_monto".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_monto".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($matriz_monto[$ii],2,'.',',');?>" readonly="true" />
                    </div></td>
                  <td  class="peq" align="left" width="39%">
                    <div align="center">
                      <input name="<?php echo "txt_imputa_monto_exento".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_monto_exento".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($matriz_monto_exento[$ii],2,'.',',');?>" readonly="true" />
                    </div></td>
                </tr>
                <?php 
		 }
		 ?>
            </table></td>
          </tr>
		
		
		<tr > 
		</table></td>
		</tr>
		  
		  <?if ($elem_imp_iva>0){?>
	 <tr>
		<td class="normal" colspan="5" align="center" >
			<table width="100%" class="tablaalertas" align="center" id="tbl_detalle_iva" border="0" >
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
			<td class="normalNegrita" align="center">Impuesto %</td>
			<td class="normalNegrita" align="center">Monto (Bs.)</td>
			</tr>
			<?php 
			for ($xt=0; $xt<$elem_imp_iva; $xt++)
			{
			?> 
			<tr >
			<td width="50%" class="normal" align="center"><input name="<?php echo "txt_iva%".$xt;?>" type="text" class="normalNegro" id=<?php echo "txt_iva%".$xt;?> value="<?php echo (number_format($iva_porce[$xt],2,'.',','));?>" size="6" maxlength="6" readonly="" align="right"></td>
			<td width="50%" class="normal" align="center"><input name="<?php echo "txt_iva_monto%".$xt;?>" type="text" class="normalNegro" id="<?php echo "txt_iva_monto%".$xt;?>" value="<?php echo  (number_format($iva_monto[$xt],2,'.',',')); ?>"  size="25" maxlength="25" readonly="" align="right"></td>
			</tr>
			<?php
			} //Del For
			} //Del If
			?>
		</table>
		</td>	 
	</tr>	
<?}else{?>
  <input name="txt_monto_subtotal" type="hidden" class="peq" id="txt_monto_subtotal" value="<?php echo (number_format($subtotal_xx,2,'.',','));?>" size="25" maxlength="25" readonly="" align="right">
<?}?>
	<tr> 
	<td height="21" colspan="2" valign="midden" class="td_gray" align="left">
	<div align="left"class="normalNegroNegrita">TOTAL A PAGAR</div></td>
	</tr>
	<tr> 
	<td height="21" valign="midden" align="left" class="normal">
	Bs.:
	  <input type="text" name="txt_monto_pagar" size="20" class="normalNegro" readonly="" value="<?php echo(number_format($sopg_monto,2,'.',',')); ?>">
	<input type="hidden" value="<?php echo $sopg_monto;?>" name="hid_monto_original">	
	<input type="hidden"  name="hid_elem_imp_iva" value="<?php echo $elem_imp_iva;?>">
	<input type="hidden"  name="hid_elem_retencion" value="<?php echo $elem_retencion;?>">
	</td>
	</tr>
	<tr> 
	<td height="21" colspan="2" valign="midden" align="left" class="normalNegro">
	En Letras:<textarea name="txt_monto_letras" id="txt_monto_letras" rows="2" cols="65" class="normalNegro" readonly></textarea></td>
	</tr>
	
		<tr><td colspan="2"></td></tr>
		<tr class="normal">
	    <td height="32" colspan="2" align="center">
		<table width="100%"  align="center" id="tbl_renta" border="0" class="tablaalertas">
			
			<tr>
			<td class="normalNegrita" align="center"></td>
             </tr>
			<tr>
			<td class="normalNegrita" colspan="4" align="left" >
			</td>
			</tr>
			<tr>
			
		  </table>
		 <?php
		if (trim($factura_num<>""))  { ?>

		<tr>
		<td class="normal" colspan="5" align="center" >
			<table width="100%" class="tablaalertas" align="center" id="tbl_retenciones" border="0">
			<tr class="td_gray">
			<td height="19" colspan="5" align="center" class="normalNegroNegrita">
             RETENCIONES <a href="javascript:abrir_ventana('http://www.seniat.gov.ve',700)">www.seniat.gov.ve</a></td>
			</tr>
			
			<tr>
			<tr class="td_gray">
			<td width="63%" class="normalNegroNegrita" align="center">Tipo de Servicio</td>
			<td width="10%" class="normalNegroNegrita" align="center">Tipo de Retenci&oacute;n</td>
			<td width="10%" class="normalNegroNegrita" align="center">Impuesto %</td>
			<td width="12%" class="normalNegroNegrita" align="center">% de Retenci&oacute;n</td>
			<td width="15%" class="normalNegroNegrita" align="center">Monto Retenido(Bs.)</td>
			</tr>
			<?php
                          
			  $ii=0;
			  $jj=0;
			  while($ii<$elem_retencion) 
			  {
			  $servicio="";
			  $impu_iva_es="s/n";
			  $porcetaje_rete=0;
			  $monto_retenido=0;
			  

	  	 if ( $id_impuesto[$ii]<>"IVA")
		  {
				   
				   for ($ee=0; $ee<$elem_existe; $ee++)
			 	 { 
			   		if  ( ($id_impuesto_doc[$ee]== $id_impuesto[$ii]) && $id_impuesto_doc[$ee]<>"IVA")
			   		{
					 $servicio=$servicio_doc[$ee];
			      	 $monto_retenido=$rete_monto_doc[$ee];
					 $porcetaje_rete=$por_rete_doc[$ee];
					}
			  	}
			 ?>
			<tr>
			<td width="64%" class="normal" align="center">
			<input name="<?php echo "txt_servicio".$jj?>" id="<?php echo "txt_servicio".$jj?>" type="text"  class="normalNegro" value="<?php echo $servicio;?>" size="60" maxlength="60"></td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_impuesto".$jj?>" id= "<?php echo "txt_impuesto".$jj?>" value="<?php echo $id_impuesto[$ii];?>" class="normalNegro" size="10" readonly="true"></td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_iva_".$jj?>" id= "<?php echo "txt_iva_".$jj?>" value="s/n" class="normalNegro" size="6" readonly="true"></td>
			<td width="12%" class="normal" align="center">
              <select name="<?php echo "opc_".$jj?>" id="<?php echo "opc_".$jj?>" class ="normalNegro"  onClick="javacript:calcular_retencion('<?php echo $id_impuesto[$ii];?>',this,0)">
			 <?php
			    $id_inicial=$id_impuesto[$ii];
			  	while($id_inicial==$id_impuesto[$ii]) 
				{
				?>
                <option value="<?php echo $porce_impuesto[$ii];?>"  <?php if (($porce_impuesto[$ii]==$porcetaje_rete) || ($porce_impuesto[$ii]==0) ) {echo "selected";}?> title="<?php echo $porce_impuesto[$ii]."% ". $impu_nombre[$ii];?>"><?php echo $porce_impuesto[$ii];?></option>
				<?PHP
				$ii++;
				}
				?>
              </select></td> 
			<td width="15%" align="center" class="normal">
			<input name="<?php echo "txt_monto_impu".$jj?>" type="text"  class="normalNegro" value="<?php echo  number_format($monto_retenido,2,'.',',');?>" size="25" maxlength="25" readonly="true" align="right" id="<?php echo "txt_monto_impu".$jj?>">
			</td>
			</tr>
			<?php 
				if (number_format($monto_retenido,2,'.',',') > 0)
				{  //Verifico la retencion?><script language="JavaScript" type="text/JavaScript"  id="java<?php echo $jj;?>" >
				 javascript:calcular_retencion('<?php echo  $id_inicial;?>',<?php echo "opc_".$jj?>,1);
				  </script> 
				<?php
				}
			?>
			<?php
			$jj++;
			$id_inicial="";
		} //Del  fin de Diferente IVA 
		?>
		
		<?php
		if ( $id_impuesto[$ii]=="IVA") 
		{
		for ($xt=0; $xt <$elem_imp_iva;$xt++)  
			{
			  $servicio="";
			  $impu_iva_es="0";
			  $porcetaje_rete=0;
			  $monto_retenido=0;
			for ($ee=0; $ee<$elem_existe; $ee++)
			 	 { 
				 
			   		if  ( ($por_imp_doc[$ee]== $iva_porce[$xt]) && $id_impuesto_doc[$ee]=="IVA")
			   		{
					 $servicio=$servicio_doc[$ee];
			         $monto_retenido=$rete_monto_doc[$ee];
					 $porcetaje_rete=$por_rete_doc[$ee];
					}
			  	}
				
			?>
			<tr>
			<td width="64%" class="normal" align="center">
			<input name="<?php echo "txt_servicio".$jj?>" id="<?php echo "txt_servicio".$jj?>" type="text"  class="normalNegro" value="<?php echo $servicio;?>" size="60" maxlength="60"></td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_impuesto".$jj?>" id= "<?php echo "txt_impuesto".$jj?>" value="<?php echo $id_impuesto[$ii];?>" class="normalNegro" size="10" readonly="true"></td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_iva_".$jj?>" id= "<?php echo "txt_iva_".$jj?>" value="<?php echo trim($iva_porce[$xt]);?>" class="normalNegro" size="6" readonly="true"></td>
			<td width="12%" class="normal" align="center">
              <select name="<?php echo "opc_".$jj?>" id="<?php echo "opc_".$jj?>" class ="normalNegro"  onClick="javacript:calcular_retencion('<?php echo $id_impuesto[$ii];?>',this,0)">
			 <?php
			    $dd=0;
			  	for ( $xy=0; $xy <$elem_retencion; $xy++ )
				{
				 if ($id_impuesto[$xy]=="IVA") 
				 {
				?>
                <option value="<?php echo $porce_impuesto[$xy];?>"  <?php if ( ($porce_impuesto[$xy]==$porcetaje_rete)|| ($porce_impuesto[$xy]==0 )) {echo "selected";}?> title="<?php echo $porce_impuesto[$xy]."% ". $impu_nombre[$xy];?>"><?php echo $porce_impuesto[$xy];?></option>
				<?PHP
				  $dd++;
				  }
				}
				?>
              </select></td> 
			<td width="15%" align="center" class="normal">
			<input name="<?php echo "txt_monto_impu".$jj?>" type="text"  class="normalNegro" value="<?php echo  number_format($monto_retenido,2,'.',',');?>" size="25" maxlength="25" readonly="true" align="right" id="<?php echo "txt_monto_impu".$jj?>">			</td>
			</tr>
			<?php 
				if (number_format($monto_retenido,2,'.',',') > 0)
				{  //Verifico la retencion?>
				<script language="JavaScript" type="text/JavaScript"  id="java<?php echo $jj;?>" >
				 javascript:calcular_retencion('IVA',<?php echo "opc_".$jj?>,1);
				  </script> 
				<?php
				}
			?>
			<?php
			$jj++;
			$id_inicial="";
			} //-->Del For para los tipos de IVA
		$ii=$ii+$dd;
		if  ( $elem_imp_iva==0)
		 {
		   $ii++;
		 }
		} //Del sino -> "es igual a IVA"
		
	} //Del While $elem_retencion
		?>
		
			<tr >
			<td height="19" colspan="1" align="left" class="normalNegroNegrita">TOTAL RETENCIONES :		      </td>
			<td height="19" colspan="4" align="right" class="normalNegrita"><input name="txt_monto_retenciones_tt" type="text" class="normalNegro" id="txt_monto_retenciones_tt" value="<?php echo(number_format($tt_retencion,2,'.',',')); ?>" size="25" maxlength="25" readonly="true" align="right"> </td>
			</tr>
			
			</table>		</td>
		</tr>
		<?php
		}else{?>
		      <tr>
		         <td class="normal" colspan="5" align="center" >
			  <table width="0" align="center" id="tbl_retenciones" border="0">
			   <tr width="0">
			    <td width="0"><input name="txt_monto_retenciones_tt" type="hidden"  id="txt_monto_retenciones_tt" value=""></td>
			</tr>
			 </table></td></tr>
		      <?
                }
		?>

		<tr>
		<td class="normal" colspan="5" align="center" ><BR>
			<table width="100%" class="tablaalertas" align="center" id="tbl_otras_retenciones" border="0">
			<tr class="td_gray">
			<td height="19" colspan="5" align="center" class="normalNegroNegrita">
             OTRAS RETENCIONES </td>
			</tr>
			<tr class="td_gray">
			<td width="12%" class="normalNegroNegrita" align="center">Cuenta</td>
			<td width="15%" class="normalNegroNegrita" align="center">Monto Retenido(Bs.)</td>
			<input type="hidden" name="hid_elem_otra_retencion" value="<?php echo $elem_otras_rete;?>">
			</tr>
			<?
              	
			  $k=0;
			  $monto_retenido=0;
			  $maxima_long=20;
			  
			  for ($i=0; $i<$elem_otras_rete; $i++)
			  {
			     	 $id_parti=$id_partida[$i];
			      	 $monto_ret=$monto_rete[$i];
					 $nombre_part=$nombre_part[$i];?>
				
			  <tr>
			   <td width="12%" class="normal" align="center">
               <select name="<?php echo "part_".$k?>" id="<?php echo "part_".$k?>" class ="normalNegro">
			  <option value="0">[Seleccione]</option>
			 <?php
 			  $sql_partidas="SELECT t2.part_id, t2.part_nombre,t1.cpat_id,cpat_nombre FROM sai_convertidor t1,  sai_partida t2,sai_cue_pat t3 WHERE t1.part_id=t2.part_id and t2.pres_anno='".$_SESSION['an_o_presupuesto']."' and t2.part_id like '4.11.0%' and t1.cpat_id=t3.cpat_id order by 3";
 			  
			   $resultado_partidas=pg_exec($conexion,$sql_partidas) or die("Error al consultar otro beneficiario");
			   while($rowp=pg_fetch_array($resultado_partidas))
			   {
			   	$id_part=$rowp['part_id'];
				$nombre_part=$rowp['part_nombre'];
				if ($id_part==$id_parti){?>
				   <option value="<?php echo $id_parti;?>" selected="true"><?php echo $rowp['cpat_id']." : ".$rowp['cpat_nombre'];?></option>	
				<?php }
			    ?>
			    <option value="<?php echo $id_part;?>"><?php echo $rowp['cpat_id']." : ".$rowp['cpat_nombre'];?></option>
			<?}?>
			           		</select></td> 
			<td width="15%" align="center" class="normal">
			<input name="<?php echo "monto_rete".$k?>" type="text"  class="normalNegro" value="<?php echo  number_format($monto_ret,2,'.',',');?>" size="25" maxlength="25" align="right" id="<?php echo "monto_rete".$k?>"  onchange="sumar_rete();"></td>
			</tr>	 
				<?
				$k++;
			  	}
			  	
			  while($k<$maxima_long) 
			  {
			  
			  	?>

			<tr>
			<td width="12%" class="normal" align="center">
             <select name="<?php echo "part_".$k?>" id="<?php echo "part_".$k?>" class ="normalNegro">
			<option value="0">[Seleccione]</option>
			 <?php
 			  $sql_partidas="SELECT t2.part_id, t2.part_nombre,t1.cpat_id,cpat_nombre FROM sai_convertidor t1,  sai_partida t2,sai_cue_pat t3 WHERE t1.part_id=t2.part_id and t2.pres_anno='".$_SESSION['an_o_presupuesto']."'  and t2.part_id like '4.11.0%' and t1.cpat_id=t3.cpat_id order by 3";
			 
 			  $resultado_partidas=pg_exec($conexion,$sql_partidas) or die("Error al consultar otro beneficiario");
			   while($rowp=pg_fetch_array($resultado_partidas))
			   {
			   	$id_part=$rowp['part_id'];
				$nombre_part=$rowp['part_nombre'];
				
			    ?>
			    <option value="<?php echo $id_part;?>"><?php echo $rowp['cpat_id']." : ".$rowp['cpat_nombre']?></option>
			<?}?>
             		</select></td> 
			<td width="15%" align="center" class="normal">
			<input name="<?php echo "monto_rete".$k?>" type="text"  class="normalNegro" value="<?php echo  number_format($monto_retenido,2,'.',',');?>" size="25" maxlength="25" align="right" id="<?php echo "monto_rete".$k?>" onchange="sumar_rete();"></td>
			</tr>
			<?$k++;
			}?>
	
			<tr >
			<td height="19" colspan="1" align="left" class="normalNegroNegrita">TOTAL OTRAS RETENCIONES </td>
			<td height="19" width="15%" align="center" class="normalNegrita"><input name="monto_rete_part" type="text" class="normalNegro" id="monto_rete_part" value="<?php echo(number_format($total_otras_rete,2,'.',',')); ?>" size="25" maxlength="25" readonly="true" align="right">
			<input type="hidden" name="hid_max_otra_retencion" value="<?php echo $maxima_long?>">
			</td>
			</tr>
			</table>
		</td></tr>



	<tr><td height="21" colspan="2" valign="midden">&nbsp;</td></tr>
	<tr  > 
	<td height="21" colspan="2" valign="midden" class="td_gray" align="left">
	<div align="left"class="normalNegroNegrita">MONTO NETO DEL PAGO</div></td>
	</tr>
	<tr> 
	<td height="21" colspan="2" valign="midden" align="left" class="normalNegrita">	En Bs.
	  <input type="text" name="txt_monto_neto" size="20" class="normalNegro" readonly="true" value="<?php echo(number_format($tt_neto,2,'.',',')); ?>"></td>
	</tr>
	<tr> 
	<td height="21" colspan="2" valign="midden" align="left" class="normalNegrita">
	En Letras:<textarea name="txt_monto_letras_neto" id="txt_monto_letras_neto" rows="2" cols="65" class="normalNegro" readonly></textarea></td>
	</tr>
	<?php 
				if (number_format($tt_neto,2,'.',',') > 0)
				{  //Verifico la retencion?><script language="JavaScript" type="text/JavaScript"  id="javaactualiza" >
				  </script> 
				<?php
				}
			?>
	<tr>
		  <td colspan="2" class="normal" align="center">&nbsp;</td>
		  </tr>
		<tr>
		  <td colspan="2" class="normal" align="center">
		  <table width="420" align="center">
			<?  		   
           include("includes/respaldos_mostrar.php");
			?>
		</table>
		  
		  </td>
		  </tr>
	
    <tr>
      <td height="18" colspan="2">&nbsp;</td>
    </tr>
    <tr>
		  <td height="18" colspan="2">
		   <?
		include("documentos/opciones_3y4.php");
		?>	  </td>
    </tr>
	
	<input type="hidden" name="array_depe_id" value="">
	<input type="hidden" name="array_code_tipo" value="">
	<input type="hidden" name="array_p_ac" value="">
	<input type="hidden" name="array_ac_esp" value="">
	<input type="hidden" name="array_part" value="">
	<input type="hidden" name="array_monto" value="">
	<input type="hidden" name="array_abono" value="">
	<input type="hidden" name="array_code_desc" value="">
	
    <input type="hidden" name="contenido_memo" id="contenido_memo">
    
	    <?php
	} /*Del Valido*/
	 if ($valido==false){
	     ?>
    <tr>
      <td height="18" colspan="4" class="normal"><div align="center"><img src="imagenes/vineta_azul.gif" width="11" height="7" />Ha ocurrido un error al registrar los datos , <?php echo(pg_errormessage($conexion)); ?></div></td>
    </tr>
    <tr>
      <td height="40" colspan="4"><div align="center"><img src="imagenes/mano_bad.gif" width="31" height="38" />
	  <br><br>
		<a href="documentos.php?tipo=<? echo $request_id_tipo_documento; ?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('regresar','','imagenes/boton_reg_blk.gif',1)"><img src="imagenes/boton_reg.gif" name="regresar" width="90" height="31" border="0"></a>	</div></td>
    </tr>
    <?php
	 } 
	?>
</table>
</form>
</body>
</html>

