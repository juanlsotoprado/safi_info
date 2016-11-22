<?php 
    ob_start();
	session_start();
	 require_once("includes/conexion.php");
	 require_once("includes/funciones.php");
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	     {
		   header('Location:../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	     }
	ob_end_flush(); 

	$partida_IVA=trim($_SESSION['part_iva']);
	$cod_doc = $request_codigo_documento;
	$codigo = $cod_doc;
	
	  $sql_pr= " Select * from sai_buscar_sopg_imputacion('".trim($codigo)."') as result ";
	  $sql_pr.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, sopg_tipo_impu bit, sopg_monto_exento float)";
	  $resultado_set_most_pr=pg_query($conexion,$sql_pr);
	  $resultado_sumar_imp=pg_query($conexion,$sql_pr);

	  if($rowp=pg_fetch_array($resultado_set_most_pr)){
	   $sopg_tp_imputacion=$rowp['sopg_tipo_impu'];
	  }
	  $monto_base=0;
	  while($rows=pg_fetch_array($resultado_sumar_imp)){
	   $cod_part=$rows['sopg_sub_espe'];
       if ($cod_part<>$_SESSION['part_iva']){
	    $monto_base=$monto_base+$rows['sopg_monto'];
	   }
	 }
	   
$sql_p="SELECT * FROM sai_sol_pago WHERE sopg_id='".$codigo."'"; 
$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al consultar solicitud de pago");
$valido=$resultado_set_most_p;
if($row=pg_fetch_array($resultado_set_most_p))
{
  require_once("includes/fechas.php");
  $depe_id=trim($row['depe_solicitante']); 
  $tp_sol=trim($row['sopg_tp_solicitud']);
  $sopg_monto=trim($row['sopg_monto']); 
  $sopg_fecha=trim($row['sopg_fecha']);
  $pres_anno=trim($row['pres_anno']);
  $esta_id=trim($row['esta_id']);
  $usua_login=trim($row['usua_login']);
  $comp_id=trim($row['comp_id']);
  $sopg_bene_ci_rif=trim($row['sopg_bene_ci_rif']);
  $sopg_bene_tp=trim($row['sopg_bene_tp']);
  $sopg_detalle=trim($row['sopg_detalle']);
  $sopg_observacion=trim($row['sopg_observacion']);
  $factura_num=trim($row['sopg_factura']);
  $factura_control=trim($row['sopg_factu_num_cont']);
  $factura_fecha=cambia_esp(trim($row['sopg_factu_fecha']));
  $numero_reserva=trim($row['numero_reserva']);
  $comp_id=trim($row['comp_id']);
  $edo_vzla=$row['edo_vzla'];
  $otro=0;

    //Datos del Solicitante
	$sql_so="select * from sai_buscar_usuario('$usua_login','')
	resultado_set(empl_email varchar, usua_login varchar, usua_activo bool,empl_cedula varchar, empl_nombres varchar,
	empl_apellidos varchar,empl_tlf_ofic varchar,carg_nombre varchar,depe_nombre varchar,depe_id varchar,carg_id varchar)";
	$resultado_set_most_so=pg_query($conexion,$sql_so) or die("Error al consultar partida");
	$valido=$resultado_set_most_so;
	if($rowso=pg_fetch_array($resultado_set_most_so))
	{
		$email=trim($rowso['empl_email']);
		$cedula=$rowso['empl_cedula'];
		$solicitante=$rowso['empl_nombres'].' '.$rowso['empl_apellidos'];
		$cargo=trim($rowso['carg_nombre']);
		$telefono=trim($rowso['empl_tlf_ofic']);
	}
	
	   //Buscar Nombre del Documento al cual se le asocia la solicitud de pago y el nombre del estado actual
	    $sql_d="select * from sai_buscar_datos_sopg('1',4,'','','$codigo','sopg','',0) 
		resultado_set(docu_nombre varchar, esta_id int4, docg_prioridad int2, esta_nombre varchar)"; 
		$resultado_set_most_d=pg_query($conexion,$sql_d) or die("Error al consultar documento");
		$valido=$resultado_set_most_d;
		if($rowd=pg_fetch_array($resultado_set_most_d))
		{
		   $prioridad=trim($rowd['docg_prioridad']);
		}
		
	//Buscar datos del benefiario segun sea el tipo (1:sai_empleado 2_sai_proveedor 3:sai_viat_benef)
	if($sopg_bene_tp==1) //Empleado
	{
	 	$sql_be="select * from sai_buscar_datos_sopg('$sopg_bene_ci_rif',1,'','','','','',0) 
		resultado_set(depe_id varchar, depe_nombre varchar,empl_nombres varchar,empl_apellidos varchar)";
		$resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar empleado");
		$valido=$resultado_set_most_be;
		if($rowbe=pg_fetch_array($resultado_set_most_be))
		{
		   $nombre_bene=$rowbe['empl_nombres'].' '.$rowbe['empl_apellidos'];
		   $depe_nombre_bene=trim($rowbe['depe_nombre']);
		   $bene_tp=1;
		}
	}
	else
	   if($sopg_bene_tp==2) //Proveedor
	   {
	       $sql_be="SELECT * FROM sai_seleccionar_campo('sai_proveedor_nuevo','prov_nombre','prov_id_rif='||'''$sopg_bene_ci_rif''','',2) 
		   resultado_set(prov_nombre varchar)"; 
		   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar proveedor");
		   $valido=$resultado_set_most_be;
		   if($rowbe=pg_fetch_array($resultado_set_most_be))
		   {
		   	  $nombre_bene=$rowbe['prov_nombre'];
			  $depe_nombre_bene=$dependencia;
			  $bene_tp=2;
		   }
	   }
	   else
	       if($sopg_bene_tp==3) //Otro beneficiario
		   {
			   $sql_be="SELECT * FROM sai_seleccionar_campo('sai_viat_benef','benvi_nombres,benvi_apellidos','benvi_cedula='||'''$sopg_bene_ci_rif''','',2) 
			   resultado_set(benvi_nombres varchar,benvi_apellidos varchar)"; 
			   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar otro beneficiario");
			   $valido= $resultado_set_most_be;
			   if($rowbe=pg_fetch_array($resultado_set_most_be))
			   {
				  $nombre_bene=$rowbe['benvi_nombres'].' '.$rowbe['benvi_apellidos'];
				  $depe_nombre_bene=$dependencia;
				  $bene_tp=3;
			   }
		   }
	
	//Verificar la categoria de la imputacion de la solicitud de pago
	if($sopg_tp_imputacion==1)//Es una solicitud para la categoria de proyecto
	{
	   $categoria="Proyecto";
	   //Buscar nombre del proyecto y de la accion especifica
	   $sql_im="select * from sai_buscar_datos_sopg('',2,'$sopg_imp_p_c','$sopg_imputa','','','',0) 
	   resultado_set(a varchar,b varchar,c varchar)";
	}
	else
	    if($sopg_tp_imputacion==0)//Es una solicitud para la categoria de accion central
		{
		   $categoria=utf8_decode("Acción Central");
		   //Buscar nombre del accion central y de la accion especifica
		   $sql_im="select * from sai_buscar_datos_sopg('',3,'$sopg_imp_p_c','$sopg_imputa','','','',0) 
	  	   resultado_set(a varchar,b varchar,c varchar)";
		}
	$resultado_set_im=pg_query($conexion,$sql_im) or die("Error al consultar accion central o proyecto");
	$valido=$resultado_set_im;
	if($rowim=pg_fetch_array($resultado_set_im))
	{
		$p_a_impu_nomb=trim($rowim['a']);
	    $a_esp_impu_nomb=trim($rowim['b']);
	    $depe_impu_id=trim($rowim['c']);
	} 
}//fin de consultar solicitud de pago

/*Consulto los impuesto  IVA*/
	$sql= "select * from sai_consulta_impuestos ('0','IVA') as resultado ";
	$sql.= "(id varchar, nombre varchar, porcetaje float4,  principal bit, tipo bit)";
	$resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
		if ($resultado_set)
  		{
			$elem_impuesto=pg_num_rows($resultado_set);
			$id_impuesto=array($elem_impuesto);
			$porce_impuesto=array($elem_impuesto);
			$impu_nombre=array($elem_impuesto);
			$impu_prici=array($elem_impuesto);
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

/*Consulta la tabla de impuestos IVA por documento*/
   $elem_imp_iva=0;
if ($valido)
  { 
    $docu_base=  $codigo;

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
			 }//Del While
			 $elem_imp_iva=$ii;
		}//Del set
		if ($iva_xx==0)
		{
		 $subtotal_xx=$monto_base;
		}
	}//Del valido
	

	//buscar los soportes
   $sql= "select * from sai_buscar_sopg_anexos ('".$codigo."') as resultado ";
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


 		?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<title>.:Modificar Solicitud de Pago:.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link type="text/css" href="js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="js/lib/calendarPopup/js/dateparse.js"></script>
<script language="JavaScript" src="js/lib/actb.js"></script>
<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');

	</script>
<script language="javascript" src="js/func_montletra.js"></script>
<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>

<script language="JavaScript" type="text/JavaScript">
<!--
function contador (campo, cuentacampo, limite) {
	if (campo.value.length > limite) campo.value = campo.value.substring(0, limite);
	else cuentacampo.value = limite - campo.value.length;
	}

//para los apendchild



var partidas = new Array();
var todas_pdas = new Array();
var monto_tot=new Array();
var monto_tot_exento=new Array();
var monto_total=new Array();
var monto_total_exento=new Array();
var arreglo= new Array();
var array_ini_part = new Array();
var array_ini_mont = new Array();
var array_disponible= new Array();
var monto_inicial;
var monto_inicial_exento;
var bandera=false;
partidas_comp = new Array();
validar_compromiso = new Array();
var listado_comp = new Array();

var _charmiles = ',';    //separador de miles
var _chardecimal = '.';    //separador de la parte decimal

function inputFloat(e,minus){

    var menos = minus || false;
    if(e==null){
        e=event;
    }
    if(e==null){
        e=window.event;
    }

    var tecla = (document.all) ? e.keyCode : e.which;
    //48=0,57=9, 45=menos

    if(tecla==0 && !document.all)return true;//solo FF en keypress de flechas
    if(tecla==8)return true;//backs
    if(tecla==_chardecimal.charCodeAt(0)) return true; //punto decimal
    if (tecla==45){
        if (!menos){
            return false;
        }
    }else if(tecla < 48 || tecla > 57){
        return false;
    }
    return true;
}

function validar_pri(elem)
{

  for(i=0;i<elem;i++)
  {

     if( (document.getElementById('txt_monto'+i).value<0) || (document.getElementById('txt_monto_exento'+i).value<0))
     {
	    alert('Revise los montos ingresados.');
	    return false;
     }	
     	if((document.getElementById('txt_monto'+i).value=='') || (document.getElementById('txt_monto_exento'+i).value==''))
        {
	    alert('Debe especificar un monto, de no aplicar debe colocar cero (0) .');
	    return false;
	}	
  }	 
    
}
function act_desact()
{
  document.form.txt_otro.disabled = !(document.form.chk_otro.checked);
  if (document.form.chk_otro.checked )
  {  
   document.form.txt_otro.value="";
   document.form.txt_otro.focus();
   
  }
  else
  {
   document.form.txt_otro.value="";
  }
}


function redondear_dos_decimal(valor) {
   float_redondeado=Math.round(valor * 100) / 100;
   return float_redondeado;
} 

function calcular_iva()
{ 

 var ivaxx=0;
 
 var subtotalx=parseFloat(MoneyToNumber(document.form.txt_monto_subtotal.value));
 var exento=parseFloat(MoneyToNumber(document.form.txt_monto_subtotal_exento.value)); 
 var tt_neto=0;
 var porce=parseFloat(MoneyToNumber( document.form.opc_por_iva.value));

 
     var IVA=redondear_dos_decimal((subtotalx*porce)/100);
     document.form.txt_monto_iva_tt.value=IVA;
	
     var objeto = document.getElementById('txt_monto_iva_tt');
     FormatCurrency(objeto);
	
   var tt_total=(subtotalx+IVA+exento);
   var xx1=number_format(tt_total,2,'.',','); 

   document.form.txt_monto_tot.value=xx1;
   document.form.hid_monto_tot.value=tt_total;
   ver_monto_letra(tt_total, 'txt_monto_letras','');
   return;
}


function add_opciones()
{   
        nave=new String(navigator.appName);
	var pos_nave=nave.indexOf("Explorer");
        var index;
		   
	if(document.form.txt_cod_imputa.value==""){	
	    alert(" Seleccione el c\u00F3digo de Proyecto y Acci\u00F3n Centralizada !.");
	    return;
	}

		element_otros = document.getElementById('tbl_part').getElementsByTagName('tr').length;
	    element_otros = element_otros -1;
		var tbody2 = document.getElementById('tbl_part');
									
		//se agregan ahora los elementos a la tabla inferior
		var tabla = document.getElementById('tbl_mod');
		element_todos = document.getElementById('tbl_mod').getElementsByTagName('tr').length;
		element_todos = element_todos -7;
		var tbody = document.getElementById('item');
		var id='item';
		
		var valido=validar_pri(element_otros);
      
	    if(valido==false){
		  return;
		}
		
		if(element_otros<1) 
		{
			alert("Este documento no posee partidas asociadas");
			return;
		}
		cg = trim(document.form.txt_cod_imputa2.value);
		cc = trim(document.form.txt_cod_accion2.value);
/*
 * VALIDAR QUE NO SUPERE EL MONTO DEL COMPROMISO
 */
	    var comp =document.form.comp_id.value;
		for(i=0;i<element_otros;i++)
		{
		  if ((document.getElementById('txt_monto_exento'+i).value>0)||(document.getElementById('txt_monto'+i).value>0))
		  {
		    var monto_compromiso=parseFloat(MoneyToNumber(document.getElementById('monto_comp'+i).value));
			var montoexento=parseFloat(MoneyToNumber(document.getElementById('txt_monto_exento'+i).value));
			var montosujeto=parseFloat(MoneyToNumber(document.getElementById('txt_monto'+i).value));
				  
			if (montosujeto+montoexento>monto_compromiso){
			  alert("El monto introducido, no puede ser superior al monto del compromiso");
			  document.getElementById('txt_monto_exento'+i).focus();
			  return;
			}		
			
		    j=partidas_comp.length;
		    var registro = new Array(7);  	        
		    registro[4]=document.getElementById('txt_id_pda'+i).value;
		    registro[5]=document.getElementById('txt_den_pda'+i).value;
		    var row = document.createElement("tr")
	
	
		    //Verificamos si esta ya registrada la partida
		    for(l=0;l<partidas.length;l++)
		    {

		     if (partidas[l][4]==registro[4])  
		    {
			alert("Partida ya seleccionada...");
			return;
		    }
		   }

		    

		    var row = document.createElement("tr")
			
		     //LOS RADIO BUTTONS
		     var td1 = document.createElement("td");
		     td1.setAttribute("align","Center");
		     td1.className = 'normalNegro';
		     //creamos una radio button
		     var name="rb_ac_proy"+j;
			if(pos_nave>0){
			   var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); }
			 else{ 
			   var rad_1 = document.createElement('INPUT');
			   rad_1.type="radio";
	           rad_1.name=name; }
			 
			rad_1.setAttribute("id",name);
	        rad_1.setAttribute("disabled","true");
		  
		  if(document.form.chk_tp_imputa[0].checked==true){
		    registro[0]=1;
		    rad_1.setAttribute("value",1);
			rad_1_text = document.createTextNode('PR');
			rad_1.defaultChecked = true
		  }
		  else{
		    registro[0]=0;		    
		    rad_1.setAttribute("value",0);
			rad_1_text = document.createTextNode('AC');
			rad_1.defaultChecked = true
		  }
			
		  td1.appendChild(rad_1);			
		  td1.appendChild(rad_1_text);

		  //CODIGO DEL PROYECTO O ACCION
		  var td22 = document.createElement("td");
		  td22.setAttribute("align","Center");
		  td22.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_p_ac = document.createElement("INPUT");
		  txt_id_p_ac.setAttribute("type","text");
		  name="txt_id_p_ac2"+i;
	      txt_id_p_ac.setAttribute("name",name);
		  txt_id_p_ac.setAttribute("readonly","true"); 
		  txt_id_p_ac.value=cg;	 
		  txt_id_p_ac.size='15'; 
		  txt_id_p_ac.className='normalNegro';
		  td22.appendChild(txt_id_p_ac);
		  
		 //CODIGO DEL PROYECTO O ACCION OCULTO
		  var td2 = document.createElement("td");
		  td2.setAttribute("align","Center");
		  td2.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_p_ac = document.createElement("INPUT");
		  txt_id_p_ac.setAttribute("type","hidden");
		  name="txt_id_p_ac"+j;
	      txt_id_p_ac.setAttribute("name",name);
		  txt_id_p_ac.readOnly=true; 
		  registro[1]=document.form.txt_cod_imputa.value;
		  txt_id_p_ac.value=registro[1];
		  	 
		  txt_id_p_ac.size='15'; 
		  txt_id_p_ac.className='normalNegro';
		  td2.appendChild(txt_id_p_ac);

		  //CODIGO DE LA ACCION ESPECIFICA
		  var td33 = document.createElement("td");
		  td33.setAttribute("align","Center");
		  td33.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_acesp = document.createElement("INPUT");
		  txt_id_acesp.setAttribute("type","text");
		  name="txt_id_acesp2"+i;
	      txt_id_acesp.setAttribute("name",name); 
		  txt_id_acesp.setAttribute("readonly","true"); 
		  txt_id_acesp.value=cc;	 
		  txt_id_acesp.size='8'; 
		  txt_id_acesp.className='normalNegro';
		  td33.appendChild(txt_id_acesp);
		  
		  //CODIGO DE LA ACCION ESPECIFICA OCULTO
		  var td3 = document.createElement("td");
		  td3.setAttribute("align","Center");
		  td3.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_acesp = document.createElement("INPUT");
		  txt_id_acesp.setAttribute("type","hidden");
		  name="txt_id_acesp"+j;
	      txt_id_acesp.setAttribute("name",name);
		  txt_id_acesp.setAttribute("readOnly","true"); 
		  registro[2]=document.form.txt_cod_accion.value;
		  txt_id_acesp.value=registro[2];	 
		  txt_id_acesp.size='8'; 
		  txt_id_acesp.className='normalNegro';
		  td3.appendChild(txt_id_acesp);
		  
		  //CODIGO DE LA DEPENDENCIA
		  var td4 = document.createElement("td");
		  td4.setAttribute("align","Center");
		  td4.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_depe = document.createElement("INPUT");
		  txt_id_depe.setAttribute("type","text");
		  name="txt_id_depe"+j;
	      txt_id_depe.setAttribute("name",name);
		  txt_id_depe.setAttribute("readOnly","true");
		  registro[3]=document.form.opt_depe.value;
		  txt_id_depe.value=registro[3];	 
		  txt_id_depe.size='8'; 
		  txt_id_depe.className='normalNegro';
		  td4.appendChild(txt_id_depe);
		  
		  //CODIGO DE LA PARTIDA
		  var td5 = document.createElement("td");
		  td5.setAttribute("align","Center");
		  td5.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_pda = document.createElement("INPUT");
		  txt_id_pda.setAttribute("type","text");
		  txt_id_pda.setAttribute("readOnly","true");
		  name="txt_id_pda"+j;
	      txt_id_pda.setAttribute("name",name);
		  txt_id_pda.value=registro[4];	 
		  txt_id_pda.size='15'; 
		  txt_id_pda.className='normalNegro';
		  td5.appendChild(txt_id_pda);
		  
		  //DENOMINACION
		  var td6 = document.createElement("td");
		  td6.setAttribute("align","Center");
		  td6.className = 'titularMedio';
		  //creamos una radio button
		  var txt_den_pda = document.createElement("INPUT");
		  txt_den_pda.setAttribute("type","text");
		  name="txt_den_pda"+j;
		  txt_den_pda.setAttribute("readOnly","true"); 
	      txt_den_pda.setAttribute("name",name);
		  txt_den_pda.value=registro[5];	 
		  txt_den_pda.size='20'; 
		  txt_den_pda.className='normalNegro';
		  td6.appendChild(txt_den_pda);
		  
		  //MONTO
		  var td8 = document.createElement("td");
		  td8.setAttribute("align","Center");
		  td8.className = 'titularMedio';
		  //creamos una radio button
		  var txt_monto = document.createElement("INPUT");
		  txt_monto.setAttribute("type","text"); 
		  name="txt_monto_pda"+j;
	      txt_monto.setAttribute("name",name);
		  txt_monto.setAttribute("readOnly","true");
		  registro[6]=document.getElementById('txt_monto'+i).value;
		  var mon=MoneyToNumber(registro[6]);
		  txt_monto.value=mon;	  
		  txt_monto.size='10'; 
		  txt_monto.className='normalNegro';
		  td8.appendChild(txt_monto);


		  /**************************************/
		   monto_tot[monto_tot.length]=mon;
		  /***************************************/

		  //MONTO EXENTO
		  var td9 = document.createElement("td");
		  td9.setAttribute("align","Center");
		  td9.className = 'titularMedio';
		  //creamos una radio button
		  var txt_monto_exento = document.createElement("INPUT");
		  txt_monto_exento.setAttribute("type","text"); 
		  name="txt_monto_pda_exento"+j;
	      txt_monto_exento.setAttribute("name",name);
		  txt_monto_exento.setAttribute("readOnly","true");
		  registro[7]=document.getElementById('txt_monto_exento'+i).value;
		  var mon2=MoneyToNumber(registro[7]);
		  txt_monto_exento.value=mon2;	  
		  txt_monto_exento.size='10'; 
		  txt_monto_exento.className='normalNegro';
		  td9.appendChild(txt_monto_exento);
		  
		  /**************************************/
		   monto_tot_exento[monto_tot_exento.length]= mon2;
		  /***************************************/
		  
		  //OPCION DE ELIMINAR
		  var td10 = document.createElement("td");				
		  td10.setAttribute("align","Center");
		  td10.className = 'normal';
		  editLink = document.createElement("a");
		  linkText = document.createTextNode("Eliminar");
		  editLink.setAttribute("href", "javascript:elimina_pda('"+(j)+"')");
		  editLink.appendChild(linkText);
		  td10.appendChild (editLink);
				  	  
		  row.appendChild(td1); 
		  row.appendChild(td22);
		  row.appendChild(td33); 
		  row.appendChild(td4);
		  row.appendChild(td5);
		  row.appendChild(td6);
		  row.appendChild(td8);
		  row.appendChild(td9);
		  row.appendChild(td10);
		  row.appendChild(td2);
		  row.appendChild(td3);		  
	      tbody.appendChild(row); 	
		  
		  partidas[partidas.length]=registro;
		  /*****************************************************/
		  arreglo[arreglo.length]=registro[4];
		  document.form.hid_partida_actual.value=arreglo;
		  /**************************************************/
        }
		  document.getElementById('txt_monto_exento'+i).value=0.0;
		  document.getElementById('txt_monto'+i).value=0.0;
	   }
		
		element_mod = document.getElementById('tbl_mod').getElementsByTagName('tr').length;
	   	element_mod = element_mod -3;		
		document.getElementById('hid_largo').value=element_mod;
	
	/*for(i=0;i<element_otros;i++){	 
		  tbody2.deleteRow(1);
	    }*/
}

function add_monto()
{
	 var m=0;
     var m2=0;
     var m3=0;

	 for(i=0;i<monto_tot.length;i++)
	 {
	  m=parseFloat(m) + parseFloat(monto_tot[i]);
	 }  

	 for(i=0;i<monto_tot_exento.length;i++)
	 {
  	  m3=parseFloat(m3) + parseFloat(monto_tot_exento[i]);
	 }  

	 m2=parseFloat(m2) + parseFloat(m) + parseFloat(m3);  

	 document.form.hid_monto_tot.value=m2;
	 document.form.txt_monto_tot.value=number_format(m2,2,'.',',');
	 document.form.txt_monto_subtotal.value=number_format(m,2,'.',',');
	 document.form.txt_monto_subtotal_exento.value=number_format(m3,2,'.',',');
	 diner= number_format(m,2,'.','');
	 diner=parseFloat(diner);

ver_monto_letra(diner, 'txt_monto_letras','');
}

function elimina_pda(tipo)
{   
    nave=new String(navigator.appName);
    var pos_nave=nave.indexOf("Explorer");
	
    var tabla = document.getElementById('tbl_mod');
    var tbody = document.getElementById('item');
		
	for(i=0;i<partidas.length;i++)
	{
	 tabla.deleteRow(1);
	}

    for(i=tipo;i<partidas.length;i++)
	{
	 partidas[i-1]=partidas[i];
	 arreglo[i-1]=partidas[i][3];
	 monto_tot[i-1]=monto_tot[i];
	 monto_tot_exento[i-1]=monto_tot_exento[i];
	}
	monto_tot[partidas.length-1]=0;
 	monto_tot_exento[partidas.length-1]=0;
	partidas.pop(); 
	arreglo.pop();
	document.form.hid_partida_actual.value=arreglo;
		
	nave=new String(navigator.appName);
	var pos_nave=nave.indexOf("Explorer");
    
	document.getElementById('hid_largo').value=partidas.length;
	cg = trim(document.form.txt_cod_imputa2.value);
	cc = trim(document.form.txt_cod_accion2.value);
	
    //agrega los elementos
	for(i=0;i<partidas.length;i++)
	{
		var row = document.createElement("tr")
		//LOS RADIO BUTTONS
		var td1 = document.createElement("td");
		td1.setAttribute("align","Center");
		td1.className = 'normalNegro';
		//creamos una radio button
		var name="rb_ac_proy"+i;
		if(pos_nave>0)
		{
		  var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); 
		}
		else
		   { 
		    var rad_1 = document.createElement('INPUT');
		    rad_1.type="radio";
	            rad_1.name=name; 
		   }
		  
		    if(partidas[i][0]==1){
		       rad_1.setAttribute("value",1);
			   rad_1_text = document.createTextNode('PR');
			   rad_1.defaultChecked = true
		    }
		    else{		    
		       rad_1.setAttribute("value",0);
			   rad_1_text = document.createTextNode('AC');
			   rad_1.defaultChecked = true
		    }
		  	
		    rad_1.setAttribute("id",name);
	            rad_1.setAttribute("disabled","true");
		  
		    td1.appendChild(rad_1);			
		    td1.appendChild(rad_1_text);
		  
		 //CODIGO DEL PROYECTO O ACCION
		  var td22 = document.createElement("td");
		  td22.setAttribute("align","Center");
		  td22.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_p_ac = document.createElement("INPUT");
		  txt_id_p_ac.setAttribute("type","text");
		  name="txt_id_p_ac2"+i;
	      txt_id_p_ac.setAttribute("name",name);
		  txt_id_p_ac.setAttribute("readonly","true"); 
		  txt_id_p_ac.value=cg;	 
		  txt_id_p_ac.size='15'; 
		  txt_id_p_ac.className='normalNegro';
		  td22.appendChild(txt_id_p_ac);

		 //CODIGO DEL PROYECTO O ACCION OCULTO
		  var td2 = document.createElement("td");
		  td2.setAttribute("align","Center");
		  td2.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_p_ac = document.createElement("INPUT");
		  txt_id_p_ac.setAttribute("type","hidden");
		  name="txt_id_p_ac"+i;
	      txt_id_p_ac.setAttribute("name",name);
		  txt_id_p_ac.setAttribute("readonly","true"); 
		  txt_id_p_ac.value=partidas[i][1];	 
		  txt_id_p_ac.size='15'; 
		  txt_id_p_ac.className='normalNegro';
		  td2.appendChild(txt_id_p_ac);
		  
		  //CODIGO DE LA ACCION ESPECIFICA
		  var td33 = document.createElement("td");
		  td33.setAttribute("align","Center");
		  td33.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_acesp = document.createElement("INPUT");
		  txt_id_acesp.setAttribute("type","text");
		  name="txt_id_acesp2"+i;
	      txt_id_acesp.setAttribute("name",name); 
		  txt_id_acesp.setAttribute("readonly","true"); 
		  txt_id_acesp.value=cc;	 
		  txt_id_acesp.size='8'; 
		  txt_id_acesp.className='normalNegro';
		  td33.appendChild(txt_id_acesp);

		  //CODIGO DE LA ACCION ESPECIFICA OCULTO
		  var td3 = document.createElement("td");
		  td3.setAttribute("align","Center");
		  td3.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_acesp = document.createElement("INPUT");
		  txt_id_acesp.setAttribute("type","hidden");
		  name="txt_id_acesp"+i;
	      txt_id_acesp.setAttribute("name",name); 
		  txt_id_acesp.setAttribute("readonly","true"); 
		  txt_id_acesp.value=partidas[i][2];	 
		  txt_id_acesp.size='8'; 
		  txt_id_acesp.className='normalNegro';
		  td3.appendChild(txt_id_acesp);
		  
		   //CODIGO DE LA DEPENDENCIA
		  var td4 = document.createElement("td");
		  td4.setAttribute("align","Center");
		  td4.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_depe = document.createElement("INPUT");
		  txt_id_depe.setAttribute("type","text");
		  txt_id_depe.setAttribute("readonly","true");
		  name="txt_id_depe"+i;
	      txt_id_depe.setAttribute("name",name); 
		  txt_id_depe.value=partidas[i][3];	 
		  txt_id_depe.size='8'; 
		  txt_id_depe.className='normalNegro';
		  td4.appendChild(txt_id_depe);
		  	    
		  //CODIGO DE LA PARTIDA
		  var td5 = document.createElement("td");
		  td5.setAttribute("align","Center");
		  td5.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_pda = document.createElement("INPUT");
		  txt_id_pda.setAttribute("type","text");
		  txt_id_pda.setAttribute("readonly","true");
		  name="txt_id_pda"+i;
	      txt_id_pda.setAttribute("name",name);
		  txt_id_pda.value=partidas[i][4];	 
		  txt_id_pda.size='15'; 
		  txt_id_pda.className='normalNegro';
		  td5.appendChild(txt_id_pda);
		  
		  //DENOMINACION
		  var td6 = document.createElement("td");
		  td6.setAttribute("align","Center");
		  td6.className = 'titularMedio';
		  //creamos una radio button
		  var txt_den_pda = document.createElement("INPUT");
		  txt_den_pda.setAttribute("type","text");
		  txt_den_pda.setAttribute("readonly","true");
		  name="txt_den_pda"+i;
	      txt_den_pda.setAttribute("name",name);
		  txt_den_pda.value=partidas[i][5];	 
		  txt_den_pda.size='20'; 
		  txt_den_pda.className='normalNegro';
		  td6.appendChild(txt_den_pda);
		  
		  //MONTO
		  var td8 = document.createElement("td");
		  td8.setAttribute("align","Center");
		  td8.className = 'titularMedio';
		  //creamos una radio button
		  var txt_monto = document.createElement("INPUT");
		  txt_monto.setAttribute("type","text");
		  name="txt_monto_pda"+i;
	      txt_monto.setAttribute("name",name);
		  txt_monto.setAttribute("readonly","true");
		  var mon=MoneyToNumber(partidas[i][6]);
		  txt_monto.value=mon;	 
		  txt_monto.size='10'; 
		  txt_monto.className='normalNegro';
		  td8.appendChild(txt_monto);	
		  /**************************************/
		   monto_total[monto_total.length]=mon;
		  /***************************************/
		  	
		  //MONTO EXENTO
		  var td9 = document.createElement("td");
		  td9.setAttribute("align","Center");
		  td9.className = 'titularMedio';
		  //creamos una radio button
		  var txt_monto_exento = document.createElement("INPUT");
		  txt_monto_exento.setAttribute("type","text");
		  name="txt_monto_pda_exento"+i;
	      txt_monto_exento.setAttribute("name",name);
		  txt_monto_exento.setAttribute("readonly","true");
		  var mon2=MoneyToNumber(partidas[i][7]);
		  txt_monto_exento.value=mon2;	 
		  txt_monto_exento.size='10'; 
		  txt_monto_exento.className='normalNegro';
		  td9.appendChild(txt_monto_exento);	
		  /**************************************/
		   monto_total_exento[monto_total_exento.length]=mon2;
		  /***************************************/

		  //OPCION DE ELIMINAR
		  var td10 = document.createElement("td");				
		  td10.setAttribute("align","Center");
		  td10.className = 'normal';
		  editLink = document.createElement("a");
		  linkText = document.createTextNode("Eliminar");
		  editLink.setAttribute("href", "javascript:elimina_pda('"+(i+1)+"')");
		  editLink.appendChild(linkText);
		  td10.appendChild (editLink);

		  row.appendChild(td1); 
		  row.appendChild(td22);
		  row.appendChild(td33);  
		  row.appendChild(td4);
		  row.appendChild(td5);
		  row.appendChild(td6);
		  row.appendChild(td8);
		  row.appendChild(td9);
		  row.appendChild(td10);
		  row.appendChild(td2);
		  row.appendChild(td3);		  		  
	      tbody.appendChild(row); 	
        }
		/****************************************/
		m=0;
        me=0;

		if(monto_tot.length==0){
			document.form.txt_monto_tot.value=0;
			document.form.hid_monto_tot.value=0;}

		 for(i=0;i<monto_total.length;i++)
		 {
			m=parseFloat(m) + parseFloat(monto_total[i]);
			document.form.hid_monto_tot.value=m;
			document.form.txt_monto_tot.value=m;
		 }  
		 
		 for(i=0;i<monto_total_exento.length;i++)
		{
			me=parseFloat(me) + parseFloat(monto_total_exento[i]);
			document.form.txt_monto_subtotal_exento.value=me;
		}  
		 
		 if (partidas.length==0)
		{
			document.form.hid_monto_tot.value=0;
			document.form.txt_monto_subtotal.value=0;
			document.form.txt_monto_subtotal_exento.value=0;
			document.form.txt_monto_tot.value=0;
			diner=0;
			monto_tot=new Array();
			monto_tot_exento=new Array();
			}
			else
			{

			document.form.txt_monto_subtotal.value=document.form.txt_monto_tot.value;
			diner= number_format(m,2,'.','');
			}
			calcular_iva();
			monto_total=new Array();
			monto_total_exento=new Array();
			diner=parseFloat(diner);
		 	ver_monto_letra(diner, 'txt_monto_letras','');
			ver_monto_letra(diner,'hid_monto_letras','');
}
/****************************************************************************************/
	function limpiarItem(){
			document.getElementById("itemCompletarTemp").value="";
			document.getElementById("itemCompletarTemp").focus();
			document.getElementById("sujeto_temp").value="0";
			document.getElementById("exento_temp").value="0";
			document.getElementById("itemCompletarTemp").value="";
		}

 function estaEnItemsTemporales(idItem){
	
	for(j = 0; j < partidas.length; j++){
		if(idItem==partidas[j][4]){
			return true;
		}
	}
	return false;
}
 
function estaEnItems(idItem,arreglop){
	
	for(j = 0; j < arreglop.length; j++){
		if(idItem==arreglop[j]){
			return j;
		}
	}
	return -1;
}

function agregarItem(objeto,montos,montoe,arreglo_partidas,arreglo_cuentas){
	nave=new String(navigator.appName);
	var pos_nave=nave.indexOf("Explorer");
	if(document.form.txt_cod_imputa.value==""){	
		alert("Seleccione el c"+oACUTE+"digo del Proyecto o Acci"+oACUTE+"n Centralizada !.");
		return;
	}
	
	if(trim(objeto.value)==""){
		//alert("Introduzca la partida o una palabra contenida en el nombre.");
		alert("Introduzca la cuenta contable o una palabra contenida en el nombre.");
		document.getElementById("itemCompletarTemp").focus();
	}else{
				tokens = objeto.value.split( ":" );
				if(tokens[0] && tokens[1]){
					idPartida = trim(tokens[0]);
					nombreItem = trim(tokens[1]);
					if (idPartida.substring(0,1)=="4")
					indiceIdItem = estaEnItems(idPartida,arreglo_partidas);
					else
					indiceIdItem = estaEnItems(idPartida,arreglo_cuentas);
					
					if(indiceIdItem>-1){
						var tbody = document.getElementById('item');
						idItem = idsPartidasItemsTemp[indiceIdItem];
						esta = estaEnItemsTemporales(idItem);
						if(esta==false){
							indiceGeneral = partidas.length;
							nombrePartida = nombresPartidasItems[indiceIdItem];
					
							monto_sujeto = (trim(montos.value));
							monto_exento = (trim(montoe.value));
							proyecto = trim(document.form.txt_cod_imputa.value);
							accion = trim(document.form.txt_cod_accion.value);
							cg = trim(document.form.txt_cod_imputa2.value);
							cc = trim(document.form.txt_cod_accion2.value);
							
							//Verificamos si esta ya registrada
							for(l=0;l<partidas.length;l++)
							{
							 if ((partidas[l][4]==idPartida) )
							 {
								alert("Partida ya seleccionada...");
								return;
							 }
							}
							
							if((montos.value=='') || (montos.value<=0)){
							       if((montoe.value=='') || (montoe.value<=0)){
										alert('Revise los montos ingresados.');
										return false;
									}}
							
							var registro = new Array(8);
							registro[1]=proyecto;
							registro[2]=accion;
							registro[4]=idPartida;
							registro[5]=nombrePartida;
							registro[6]=monto_sujeto;
							registro[7]=monto_exento;
						
							var fila = document.createElement("tr");

							//LOS RADIO BUTTONS
							var td1 = document.createElement("td");
							td1.setAttribute("align","Center");
							td1.className = 'normalNegro';
		
							//creamos una radio button
							var name="rb_ac_proy"+indiceGeneral;
							if(pos_nave>0){
						 		var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); 
							}else{
								var rad_1 = document.createElement('INPUT');
								rad_1.type="radio";
								rad_1.name=name;
							}
				 
							rad_1.setAttribute("id",name);
							rad_1.setAttribute("readOnly","true");
			  
							if(document.form.chk_tp_imputa[0].checked==true){
							registro[0]=1;
							rad_1.setAttribute("value",1);
							rad_1_text = document.createTextNode('PR');
							rad_1.defaultChecked = true;
							}else{
							registro[0]=0;		    
							rad_1.setAttribute("value",0);
							rad_1_text = document.createTextNode('AC');
							rad_1.defaultChecked = true
							}
		
							td1.appendChild(rad_1);			
							td1.appendChild(rad_1_text);
		 
							//CODIGO DEL PROYECTO O ACCION
							var td22 = document.createElement("td");
							td22.setAttribute("align","Center");
							td22.className = 'titularMedio';
							//creamos una radio button
							var txt_id_p_ac = document.createElement("INPUT");
							txt_id_p_ac.setAttribute("type","text");
							name="txt_id_p_ac2"+indiceGeneral;
							txt_id_p_ac.setAttribute("name",name);
							txt_id_p_ac.readOnly=true; 
							//registro[1]=document.form.txt_cod_imputa.value;
							txt_id_p_ac.value=cg;
			 				txt_id_p_ac.size='15'; 
							txt_id_p_ac.className='normalNegro';
							td22.appendChild(txt_id_p_ac);

							//CODIGO DEL PROYECTO O ACCION OCULTO
							var td2 = document.createElement("td");
							td2.setAttribute("align","Center");
							td2.className = 'titularMedio';
							//creamos una radio button
							var txt_id_p_ac = document.createElement("INPUT");
							txt_id_p_ac.setAttribute("type","hidden");
							name="txt_id_p_ac"+indiceGeneral;
							txt_id_p_ac.setAttribute("name",name);
							txt_id_p_ac.readOnly=true; 
							registro[1]=document.form.txt_cod_imputa.value;
							txt_id_p_ac.value=registro[1];
			 				txt_id_p_ac.size='15'; 
							txt_id_p_ac.className='normalNegro';
							td2.appendChild(txt_id_p_ac);
		  
							//CODIGO DE LA ACCION ESPECIFICA
							var td33 = document.createElement("td");
							td33.setAttribute("align","Center");
							td33.className = 'titularMedio';
							//creamos una radio button
							var txt_id_acesp = document.createElement("INPUT");
							txt_id_acesp.setAttribute("type","text");
							name="txt_id_acesp2"+indiceGeneral;
							txt_id_acesp.setAttribute("name",name);
							txt_id_acesp.setAttribute("readOnly","true"); 
							//registro[2]=document.form.txt_cod_accion.value;
							txt_id_acesp.value=cc;	 
							txt_id_acesp.size='8'; 
							txt_id_acesp.className='normalNegro';
							td33.appendChild(txt_id_acesp);

							//CODIGO DE LA ACCION ESPECIFICA OCULTA
							var td3 = document.createElement("td");
							td3.setAttribute("align","Center");
							td3.className = 'titularMedio';
							//creamos una radio button
							var txt_id_acesp = document.createElement("INPUT");
							txt_id_acesp.setAttribute("type","hidden");
							name="txt_id_acesp"+indiceGeneral;
							txt_id_acesp.setAttribute("name",name);
							txt_id_acesp.setAttribute("readOnly","true"); 
							registro[2]=document.form.txt_cod_accion.value;
							txt_id_acesp.value=registro[2];	 
							txt_id_acesp.size='8'; 
							txt_id_acesp.className='normalNegro';
							td3.appendChild(txt_id_acesp);

							//CODIGO DE LA DEPENDENCIA
							var td4 = document.createElement("td");
							td4.setAttribute("align","Center");
							td4.className = 'titularMedio';

							//creamos una radio button
							var txt_id_depe = document.createElement("INPUT");
							txt_id_depe.setAttribute("type","text");
							name="txt_id_depe"+indiceGeneral;
							txt_id_depe.setAttribute("name",name);
							txt_id_depe.setAttribute("readOnly","true");
							registro[3]=document.form.opt_depe.value;
							txt_id_depe.value=registro[3];	 
							txt_id_depe.size='8'; 
							txt_id_depe.className='normalNegro';
							td4.appendChild(txt_id_depe);
							
							//CODIGO DE LA PARTIDA
							var columna3 = document.createElement("td");
							columna3.setAttribute("align","center");
							columna3.className = 'titularMedio';
							var inputIdPartida = document.createElement("INPUT");
							inputIdPartida.setAttribute("type","text");
							inputIdPartida.setAttribute("readOnly","true");
							inputIdPartida.setAttribute("name","txt_id_pda"+indiceGeneral);
							inputIdPartida.value=registro[4];
							inputIdPartida.size='15';
							inputIdPartida.className='normalNegro';
							columna3.appendChild(inputIdPartida);
							
							//DENOMINACION DE LA PARTIDA
							var columna4 = document.createElement("td");
							columna4.setAttribute("align","center");
							columna4.className = 'titularMedio';
							var inputNombrePartida = document.createElement("INPUT");
							inputNombrePartida.setAttribute("type","text");
							inputNombrePartida.setAttribute("name","txt_den_pda"+indiceGeneral);
							inputNombrePartida.setAttribute("readOnly","true");
							inputNombrePartida.value=registro[5];
							inputNombrePartida.size='20';
							inputNombrePartida.className='normalNegro';
							columna4.appendChild(inputNombrePartida);
							
							//DESCRIPCION
							var columna5 = document.createElement("td");
							columna5.setAttribute("align","center");
							columna5.className = 'titularMedio';
							var inputEspecificaciones = document.createElement("INPUT");
							inputEspecificaciones.setAttribute("type","text");
							inputEspecificaciones.setAttribute("name","txt_monto_pda"+indiceGeneral);
							inputEspecificaciones.setAttribute("readOnly","true");
							inputEspecificaciones.value=registro[6];
							inputEspecificaciones.size='10';
							inputEspecificaciones.className='normalNegro';
							columna5.appendChild(inputEspecificaciones);
							
							//CANTIDAD
							var columna6 = document.createElement("td");
							columna6.setAttribute("align","center");
							columna6.className = 'titularMedio';
							var inputCantidad = document.createElement("INPUT");
							inputCantidad.setAttribute("type","text");
							inputCantidad.setAttribute("name","txt_monto_pda_exento"+indiceGeneral);
							inputCantidad.setAttribute("readOnly","true");
							inputCantidad.value=registro[7];
							inputCantidad.size='10';
							inputCantidad.className='normalNegro';
							columna6.appendChild(inputCantidad);
							
							monto_tot[monto_tot.length]= registro[6];
							monto_tot_exento[monto_tot_exento.length]= registro[7];

							
							//OPCION DE ELIMINAR
							var columna7 = document.createElement("td");
							columna7.setAttribute("align","center");
							columna7.className = 'normal';
							editLink = document.createElement("a");
							linkText = document.createTextNode("Eliminar");
							editLink.setAttribute("href", "javascript:elimina_pda('"+(indiceGeneral+1)+"')");
							editLink.appendChild(linkText);
							columna7.appendChild (editLink);

							fila.appendChild(td1); 
							fila.appendChild(td22);
							fila.appendChild(td33);
							fila.appendChild(td4);  
							fila.appendChild(columna3);
							fila.appendChild(columna4);
							fila.appendChild(columna5);
							fila.appendChild(columna6);
							fila.appendChild(columna7);
							fila.appendChild(td2);
							fila.appendChild(td3);
							tbody.appendChild(fila); 

							partidas[partidas.length]=registro;
							contador_partidas_temporales++;
							var temporal=registro[4];
							if (temporal.substring(0,6)!='4.11.0'){
							
							 validar_compromiso[partidas.length-1]=1;
							 
							}else{
								validar_compromiso[partidas.length-1]=0;
								}
							document.getElementById('hid_largo').value=partidas.length;
							limpiarItem();
						}else{
							alert("La partida ya se ha agregado a la solicitud.");
							document.getElementById("itemCompletarTemp").value="";
							document.getElementById("sujeto_temp").value="0";
							document.getElementById("exento_temp").value="0";
							
							
						}
					}
					else{
						alert("La partida indicada no es v"+aACUTE+"lido");
					}
				}else{
					alert("Seleccione una partida");
				}
			}	
}


var contador_partidas=0;
var contador_partidas_temporales=0;
function consulta_presupuesto(){
		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
    	var tbody = document.getElementById('ar_body');
    	var tabla = document.getElementById('tbl_mod');
	   //Lo primero que debe hacerse es borrar las partidas existentes
	    for(i=0;i<contador_partidas;i++){
			tbody.deleteRow(0);	
		}

	    for(i=0;i<contador_partidas;i++){
			tabla.deleteRow(1);
		}

	    for(i=0;i<contador_partidas_temporales;i++){
			tabla.deleteRow(1);
		}
		
		    contador_partidas=0;
		    var valor=document.form.comp_id.value;
		    
			for(i=0;i<listado_comp.length;i++)
			{
				var fila = document.createElement("tr");
				var comp = listado_comp[i][0];
				//alert(comp);
				if (valor==comp){
				contador_partidas++;
				//LOS RADIO BUTTONS
				var td1 = document.createElement("td");
				td1.setAttribute("align","Center");
				td1.className = 'normalNegro';
				//creamos una radio button
				var name="rb_ac_proy"+i;
				if(pos_nave>0){
					 var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); 
				}else{ 
					var rad_1 = document.createElement('INPUT');
					rad_1.type="radio";
					rad_1.name=name; 
				}
				
				if(listado_comp[i][3]==1){
					rad_1.setAttribute("value",1);
					rad_1_text = document.createTextNode('PR');
					rad_1.defaultChecked = true;
				}else{		    
					rad_1.setAttribute("value",0);
					rad_1_text = document.createTextNode('AC');
					rad_1.defaultChecked = true;
				}
					
				rad_1.setAttribute("id",name);
				//rad_1.setAttribute("disabled","true");
				rad_1.setAttribute("readOnly","true");
				td1.appendChild(rad_1);			
				td1.appendChild(rad_1_text);
				
				//CODIGO DE LA PARTIDA
				var columna1 = document.createElement("td");
				columna1.setAttribute("align","center");
				columna1.className = 'titularMedio';
				name="txt_id_pda"+(contador_partidas-1);
				var imp_1 = document.createElement("INPUT");
				imp_1.setAttribute("type","text");
				imp_1.setAttribute("readOnly","true");
				imp_1.setAttribute("name",name);//txt_id_pda
				imp_1.setAttribute("Id",name);
				imp_1.value=listado_comp[i][4];
				imp_1.size='15';
				imp_1.className='normalNegro';
				columna1.appendChild(imp_1);

				//NOMBRE DE LA PARTIDA
				var columna2 = window.opener.document.createElement("td");
				columna2.setAttribute("align","Center");
				columna2.className = 'titularMedio';
				var imp_2 = document.createElement("INPUT");
				imp_2.setAttribute("type","text");
		      	name="txt_den_pda"+(contador_partidas-1);
		    	imp_2.setAttribute("name",name);
		    	imp_2.setAttribute("readOnly","true");
				imp_2.className = "normalNegro";
				imp_2.setAttribute("value",listado_comp[i][7]);
				imp_2.setAttribute("id",name);
				imp_2.setAttribute("size","30");
				columna2.appendChild (imp_2);	


				//CODIGO DEL centro gestor
				var columna8 = document.createElement("td");
				columna8.setAttribute("align","center");
				columna8.className = 'titularMedio';
				name="txt_id_p_ac2"+(contador_partidas-1);
				var imp_8 = document.createElement("INPUT");
				imp_8.setAttribute("type","text");
				imp_8.setAttribute("readOnly","true");
				imp_8.setAttribute("name",name);//txt_id_pda
				imp_8.setAttribute("Id",name);
				imp_8.value=listado_comp[i][8];
				imp_8.size='15';
				imp_8.className='normalNegro';
				columna8.appendChild(imp_8);

				//CODIGO DEL PROYECTO O ACCION 
				var columna6 = document.createElement("td");
				columna6.setAttribute("align","center");
				columna6.className = 'titularMedio';
				name="txt_id_p_ac"+(contador_partidas-1);
				var imp_6 = document.createElement("INPUT");
				imp_6.setAttribute("type","hidden");
				imp_6.setAttribute("readOnly","true");
				imp_6.setAttribute("name",name);//txt_id_pda
				imp_6.setAttribute("Id",name);
				imp_6.value=listado_comp[i][2];
				imp_6.size='15';
				imp_6.className='normalNegro';
				columna6.appendChild(imp_6);


				//CODIGO DE centro costo
				var columna9 = document.createElement("td");
				columna9.setAttribute("align","center");
				columna9.className = 'titularMedio';
				name="txt_id_acesp2"+(contador_partidas-1);
				var imp_9 = document.createElement("INPUT");
				imp_9.setAttribute("type","text");
				imp_9.setAttribute("readOnly","true");
				imp_9.setAttribute("name",name);//txt_id_pda
				imp_9.setAttribute("Id",name);
				imp_9.value=listado_comp[i][9];
				imp_9.size='15';
				imp_9.className='normalNegro';
				columna9.appendChild(imp_9);

				
				//CODIGO DE LA ACCION ESPECIFICA
				var columna7 = document.createElement("td");
				columna7.setAttribute("align","center");
				columna7.className = 'titularMedio';
				name="txt_id_acesp"+(contador_partidas-1);
				var imp_7 = document.createElement("INPUT");
				imp_7.setAttribute("type","hidden");
				imp_7.setAttribute("readOnly","true");
				imp_7.setAttribute("name",name);//txt_id_pda
				imp_7.setAttribute("Id",name);
				imp_7.value=listado_comp[i][1];
				imp_7.size='15';
				imp_7.className='normalNegro';
				columna7.appendChild(imp_7);
				
				//MONTO COMPROMISO
				var columna5 = document.createElement("td");
				columna5.setAttribute("align","Center");
				columna5.className = 'titularMedio';
				name="monto_comp"+(contador_partidas-1);
				var imp_5 = document.createElement("INPUT");
				imp_5.setAttribute("type","text");
				imp_5.setAttribute("name",name);
				imp_5.setAttribute("Id",name);
				imp_5.setAttribute("readOnly","true");
				imp_5.setAttribute("value",listado_comp[i][10]);
				imp_5.size='10';
				imp_5.className='normalNegro';
				columna5.appendChild(imp_5);
				
				//MONTO SUJETO
				var columna3 = document.createElement("td");
				columna3.setAttribute("align","right");
				columna3.className = 'titularMedio';
				name="txt_monto"+(contador_partidas-1);
				var imp_3 = document.createElement("INPUT");
				imp_3.setAttribute("type","text");
				imp_3.setAttribute("name",name);
				imp_3.setAttribute("Id",name);
				imp_3.setAttribute("onkeypress","return inputFloat(event,true)");
				//imp_3.setAttribute("onKeyUp","FormatCurrency(this)");
				imp_3.value='0.0';
				imp_3.size='10';
				imp_3.className='normalNegro';
				columna3.appendChild(imp_3);
				
				//MONTO EXENTO
				var columna4 = document.createElement("td");
				columna4.setAttribute("align","right");
				columna4.className = 'titularMedio';
				name="txt_monto_exento"+(contador_partidas-1);
				var imp_4 = document.createElement("INPUT");
				imp_4.setAttribute("type","text");
				imp_4.setAttribute("name",name);
				imp_4.setAttribute("Id",name);
				imp_4.setAttribute("onkeypress","return inputFloat(event,true)");
				//imp_4.setAttribute("onKeyUp","FormatCurrency(this)");
				imp_4.value='0.0';
				imp_4.size='10';
				imp_4.className='normalNegro';
				columna4.appendChild(imp_4);

			//	fila.appendChild(td1); 
				fila.appendChild(columna1); 
				fila.appendChild(columna2);
			//	fila.appendChild(columna8);
			//	fila.appendChild(columna9);
				fila.appendChild(columna5);
				fila.appendChild(columna3);
				fila.appendChild(columna4);
				fila.appendChild(columna6);
				fila.appendChild(columna7);
				tbody.appendChild(fila); 
				
		        document.form.txt_cod_imputa.value=listado_comp[i][2];
			    document.form.txt_cod_accion.value=listado_comp[i][1];
			    //document.form.centro_gestor.value=listado_comp[i][8];
			    //document.form.centro_costo.value=listado_comp[i][9];
			    document.form.numero_reserva.value=listado_comp[i][11];
				    
				if (listado_comp[i][3]==0){
				  document.form.txt_nombre_accion.value=listado_comp[i][6];
				  document.form.txt_nombre_imputa.value=listado_comp[i][5];
				  document.form.chk_tp_imputa[0].checked=false;
				  document.form.chk_tp_imputa[1].checked=true;}
				else{
					document.form.txt_nombre_accion.value=listado_comp[i][6];
					document.form.txt_nombre_imputa.value=listado_comp[i][5];
					document.form.chk_tp_imputa[0].checked=true;
					document.form.chk_tp_imputa[1].checked=false;
					}
				}else{

					}
			   }
			if(contador_partidas==0){
				/*document.getElementById('Categoria').style.display='';
				document.getElementById('PartidasAutomaticas').style.display='none';
				document.getElementById('PartidasTemporales').style.display='';
				document.getElementById('Boton').style.display='none';*/
				document.form.txt_nombre_accion.value='';
				document.form.txt_nombre_imputa.value='';
				document.form.chk_tp_imputa[0].checked=false;
				document.form.chk_tp_imputa[1].checked=false;
				document.form.txt_cod_imputa.value='';
			    document.form.txt_cod_accion.value='';
			    document.form.centro_gestor.value='';
			    document.form.centro_costo.value='';
			    document.form.numero_reserva.value='0';
			    document.form.txt_monto_subtotal_exento.value='0';
			    document.form.txt_monto_subtotal.value='0.00';
		}
				
			    document.form.txt_monto_subtotal_exento.value='0.00';
			    document.form.txt_monto_subtotal.value='0.00';
			    document.form.txt_monto_iva_tt.value='0.00';
				<?php //$sopg_monto=0.00;?>
			    document.form.opc_por_iva.value='0';
		   
	}

function inicial()
{
	nave=new String(navigator.appName);
    var pos_nave=nave.indexOf("Explorer");
    var tbody2=document.getElementById('ar_body');
	for (i=0; i<partidas_comp.length; i++){

		 var row1 = document.createElement("tr")
     	  //CODIGO DE LA PARTIDA
		  var td01 = document.createElement("td");
		  td01.setAttribute("align","Center");
		  td01.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_pda = document.createElement("INPUT");
		  
		  txt_id_pda.setAttribute("type","text");
		  txt_id_pda.setAttribute("readonly","true");
		  name="txt_id_pda"+i;
	      txt_id_pda.setAttribute("name",name);
	      txt_id_pda.setAttribute("id",name);	   
		  txt_id_pda.value=partidas_comp[i][0];	 
		  txt_id_pda.size='15'; 
		  txt_id_pda.className='normalNegro';
		  td01.appendChild(txt_id_pda);

		  //DENOMINACION
		  var td02 = document.createElement("td");
		  td02.setAttribute("align","Center");
		  td02.className = 'titularMedio';
		  //creamos una radio button
		  var txt_den_pda = document.createElement("INPUT");
		  txt_den_pda.setAttribute("type","text");
		  txt_den_pda.setAttribute("readonly","true");
		  name="txt_den_pda"+i;
	      txt_den_pda.setAttribute("name",name);
	      txt_den_pda.setAttribute("id",name);	  
		  txt_den_pda.value=partidas_comp[i][2];	 
		  txt_den_pda.size='30'; 
		  txt_den_pda.className='normalNegro';
		  td02.appendChild(txt_den_pda);

		//MONTO COMPROMISO
		  var td03 = document.createElement("td");
		  td03.setAttribute("align","Center");
		  td03.className = 'titularMedio';
		  //creprincipal.phpamos una radio button
		  var txt_monto = document.createElement("INPUT");
		  
		  txt_monto.setAttribute("type","text");
		  name="monto_comp"+i;
	      txt_monto.setAttribute("name",name);//totalizar()
	      txt_monto.setAttribute("Id",name);
		  txt_monto.setAttribute("readonly","true");
	      txt_monto.setAttribute("onChange","javascript:totalizar()");
		  txt_monto.value=partidas_comp[i][1];	 
		  txt_monto.size='10'; 
		  txt_monto.className='normalNegro';
		  td03.appendChild(txt_monto);	
		  //monto_inicial=parseFloat(monto_inicial) + parseFloat(txt_monto.value);
		  //monto_tot[monto_tot.length]= partidas_comp[i][1];

		  //MONTO SUJETO
		  var td04 = document.createElement("td");
		  td04.setAttribute("align","right");
		  td04.className = 'titularMedio';
		  name="txt_monto"+i;
		  var monto_comp = document.createElement("INPUT");
		  monto_comp.setAttribute("type","text");
		  monto_comp.setAttribute("name",name);
		  monto_comp.setAttribute("Id",name);
		  monto_comp.setAttribute("onkeypress","return inputFloat(event,true)");
		  //imp_3.setAttribute("onKeyUp","FormatCurrency(this)");
		  monto_comp.value='0.0';
		  monto_comp.size='10';
		  monto_comp.className='normalNegro';
		  td04.appendChild(monto_comp);

			//MONTO EXENTO  columna nueva revisar ls nombres de todos los montos
			var td05 = document.createElement("td");
			td05.setAttribute("align","right");
			td05.className = 'titularMedio';
			name="txt_monto_exento"+i;
			var imp_4 = document.createElement("INPUT");
			imp_4.setAttribute("type","text");
			imp_4.setAttribute("name",name);
			imp_4.setAttribute("Id",name);
			imp_4.setAttribute("onkeypress","return inputFloat(event,true)");
			//imp_4.setAttribute("onKeyUp","FormatCurrency(this)");
			imp_4.value='0.0';
			imp_4.size='10';
			imp_4.className='normalNegro';
			td05.appendChild(imp_4);
		  
		  row1.appendChild(td01);
	      row1.appendChild(td02);
		  row1.appendChild(td03);
		  row1.appendChild(td04);
		  row1.appendChild(td05);
		  
	      tbody2.appendChild(row1); 
		  contador_partidas++;
	}
	
    var tabla = document.getElementById('tbl_mod');
    var tbody = document.getElementById('item');
    var monto_inicial=0.0;

	nave=new String(navigator.appName);
    var pos_nave=nave.indexOf("Explorer");
    var monto_base=0.0;
    var monto_exento=0.0;
    var tabla = document.getElementById('tbl_mod');
    var tbody = document.getElementById('item');
    
    document.getElementById('hid_largo').value=partidas.length;
    document.form.hid_partida_actual.value=arreglo;  
    //agrega los elementos
	for(ii=0;ii<partidas.length;ii++)
	{
	
 	     	 var row = document.createElement("tr")
		  //LOS RADIO BUTTONS
		  var td1 = document.createElement("td");
		  td1.setAttribute("align","Center");
		  td1.className = 'normalNegro';
		  //creamos una radio button
		  var name="rb_ac_proy"+ii;
		  if(pos_nave>0){
		    var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); }
		  else{ 
		    var rad_1 = document.createElement('INPUT');
		    rad_1.type="radio";
	            rad_1.name=name; }
		  
		    if(partidas[ii][0]==1){
		       rad_1.setAttribute("value",1);
			   rad_1_text = document.createTextNode('PR');
			   rad_1.defaultChecked = true
		    }
		    else{		    
		       rad_1.setAttribute("value",0);
			   rad_1_text = document.createTextNode('AC');
			   rad_1.defaultChecked = true
		    }
		  	
		    rad_1.setAttribute("id",name);
	            rad_1.setAttribute("disabled","true");
		  
		    td1.appendChild(rad_1);			
		    td1.appendChild(rad_1_text);
		  
		 //CODIGO DEL PROYECTO O ACCION
		  var td22 = document.createElement("td");
		  td22.setAttribute("align","Center");
		  td22.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_p_ac = document.createElement("INPUT");
		  txt_id_p_ac.setAttribute("type","text");
		  name="txt_id_p_ac2"+ii;
	      txt_id_p_ac.setAttribute("name",name);
		  txt_id_p_ac.setAttribute("readonly","true"); 
		  txt_id_p_ac.value=partidas[ii][8];	 
		  txt_id_p_ac.size='15'; 
		  txt_id_p_ac.className='normalNegro';
		  td22.appendChild(txt_id_p_ac);

		  //CODIGO DEL PROYECTO O ACCION OCULTO
		  var td2 = document.createElement("td");
		  td2.setAttribute("align","Center");
		  td2.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_p_ac = document.createElement("INPUT");
		  txt_id_p_ac.setAttribute("type","hidden");
		  name="txt_id_p_ac"+ii;
	      txt_id_p_ac.setAttribute("name",name);
		  txt_id_p_ac.setAttribute("readonly","true"); 
		  txt_id_p_ac.value=partidas[ii][1];	 
		  txt_id_p_ac.size='1'; 
		  txt_id_p_ac.className='normalNegro';
		  td2.appendChild(txt_id_p_ac);
		  
		  
		  //CODIGO DE LA ACCION ESPECIFICA
		  var td33 = document.createElement("td");
		  td33.setAttribute("align","Center");
		  td33.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_acesp = document.createElement("INPUT");
		  txt_id_acesp.setAttribute("type","text");
		  name="txt_id_acesp"+ii;
	      txt_id_acesp.setAttribute("name",name); 
		  txt_id_acesp.setAttribute("readonly","true"); 
		  txt_id_acesp.value=partidas[ii][9];	 
		  txt_id_acesp.size='8'; 
		  txt_id_acesp.className='normalNegro';
		  td33.appendChild(txt_id_acesp);
		  
		  //CODIGO DE LA ACCION ESPECIFICA OCULTO
		  var td3 = document.createElement("td");
		  td3.setAttribute("align","Center");
		  td3.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_acesp = document.createElement("INPUT");
		  txt_id_acesp.setAttribute("type","hidden");
		  name="txt_id_acesp"+ii;
	      txt_id_acesp.setAttribute("name",name); 
		  txt_id_acesp.setAttribute("readonly","true"); 
		  txt_id_acesp.value=partidas[ii][2];	 
		  txt_id_acesp.size='1'; 
		  txt_id_acesp.className='normalNegro';
		  td3.appendChild(txt_id_acesp);
		  
		  //CODIGO DE LA DEPENDENCIA
		  var td4 = document.createElement("td");
		  td4.setAttribute("align","Center");
		  td4.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_depe = document.createElement("INPUT");
		  txt_id_depe.setAttribute("type","text");
		  txt_id_depe.setAttribute("readonly","true");
		  name="txt_id_depe"+ii;
	      txt_id_depe.setAttribute("name",name); 
		  txt_id_depe.value=partidas[ii][3];	 
		  txt_id_depe.size='8'; 
		  txt_id_depe.className='normalNegro';
		  td4.appendChild(txt_id_depe);
		  	    
		  //CODIGO DE LA PARTIDA
		  var td55 = document.createElement("td");
		  td55.setAttribute("align","Center");
		  td55.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_pda = document.createElement("INPUT");
		  txt_id_pda.setAttribute("type","text");
		  txt_id_pda.setAttribute("readonly","true");
		  name="txt_id_pda_oculta"+ii;
	      txt_id_pda.setAttribute("name",name);
	      var temporal=partidas[ii][4];
	      if (temporal.substring(0,6)=='4.11.0'){
		   temporal=partidas[ii][10];
		  }
		  txt_id_pda.value=temporal;	 
		  txt_id_pda.size='15'; 
		  txt_id_pda.className='normalNegro';
		  td55.appendChild(txt_id_pda);

		  //CODIGO PARTIDA OCULTA
		  var td5 = document.createElement("td");
		  td5.setAttribute("align","Center");
		  td5.className = 'titularMedio';
		  //creamos una radio button
		  var txt_id_pda = document.createElement("INPUT");
		  txt_id_pda.setAttribute("type","hidden");
		  txt_id_pda.setAttribute("readonly","true");
		  name="txt_id_pda"+ii;
	      txt_id_pda.setAttribute("name",name);
		  txt_id_pda.value=partidas[ii][4];	 
		  txt_id_pda.size='15'; 
		  txt_id_pda.className='normalNegro';
		  td5.appendChild(txt_id_pda);
		  
		  
		  
		  
		  //DENOMINACION
		  var td6 = document.createElement("td");
		  td6.setAttribute("align","Center");
		  td6.className = 'titularMedio';
		  //creamos una radio button
		  var txt_den_pda = document.createElement("INPUT");
		  txt_den_pda.setAttribute("type","text");
		  txt_den_pda.setAttribute("readonly","true");
		  name="txt_den_pda"+ii;
	      txt_den_pda.setAttribute("name",name);
		  txt_den_pda.value=partidas[ii][5];	 
		  txt_den_pda.size='20'; 
		  txt_den_pda.className='normalNegro';
		  td6.appendChild(txt_den_pda);
		  
		  //MONTO
		  var td8 = document.createElement("td");
		  td8.setAttribute("align","Center");
		  td8.className = 'titularMedio';
		  //creamos una radio button
		  var txt_monto = document.createElement("INPUT");
		  txt_monto.setAttribute("type","text");
		  name="txt_monto_pda"+ii;
	      txt_monto.setAttribute("name",name);
		  txt_monto.setAttribute("readonly","true");
		  txt_monto.value=partidas[ii][6];	 
		  txt_monto.size='10'; 
		  txt_monto.className='normalNegro';
		  td8.appendChild(txt_monto);	
		
		  monto_tot[monto_tot.length]= txt_monto.value;
		  if ((partidas[ii][4]!='4.03.18.01.00')&& (partidas[ii][4]!='4.11.05.02.00')){
		  monto_base = parseFloat(monto_base) + parseFloat(txt_monto.value);
		  }
		  //MONTO EXENTO
		  var td9 = document.createElement("td");
		  td9.setAttribute("align","Center");
		  td9.className = 'titularMedio';
		  //creamos una radio button
		  var txt_monto_exento = document.createElement("INPUT");
		  txt_monto_exento.setAttribute("type","text");
		  name="txt_monto_pda_exento"+ii;
	      txt_monto_exento.setAttribute("name",name);
		  txt_monto_exento.setAttribute("readonly","true");
		  txt_monto_exento.value=partidas[ii][7];	 
		  txt_monto_exento.size='10'; 
		  txt_monto_exento.className='normalNegro';
		  td9.appendChild(txt_monto_exento);
		  
		  monto_tot_exento[monto_tot_exento.length]= txt_monto_exento.value;
		  monto_exento = parseFloat(monto_exento) + parseFloat(txt_monto_exento.value);
			  //OPCION DE ELIMINAR
			  var td10 = document.createElement("td");				
			  td10.setAttribute("align","Center");
			  td10.className = 'normal';
			  editLink = document.createElement("a");
			  linkText = document.createTextNode("Eliminar");
			  editLink.setAttribute("href", "javascript:elimina_pda('"+(ii+1)+"')");
			  editLink.appendChild(linkText);
			  td10.appendChild (editLink);
		  

		  row.appendChild(td1); 
		  row.appendChild(td22);
		  row.appendChild(td33);
		  row.appendChild(td4); 
		  row.appendChild(td55);
		  row.appendChild(td6);
		  row.appendChild(td8);
	 	  row.appendChild(td9);
		  row.appendChild(td10);
		  row.appendChild(td2);
		  row.appendChild(td3);
		  row.appendChild(td5);		  		  
	      tbody.appendChild(row); 	
	      monto_iva=parseFloat(document.getElementById('txt_monto_iva_tt').value);
	      
		document.form.txt_monto_subtotal.value=monto_base;
		monto_pagar=monto_base+monto_exento+monto_iva;
		//document.form.txt_monto_tot.value=monto_pagar;
		//document.form.hid_monto_tot.value=monto_pagar;

		diner= number_format(monto_pagar,2,'.','');
		diner=parseFloat(diner);
	 	ver_monto_letra(diner, 'txt_monto_letras','');
	 //	ver_monto_letra(diner,'hid_monto_letras','');
	//}
        }
}
//-->
</script><script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>

<script language="javascript">
function deshabilitar_combo(valor)
{

	 if(valor=='1')
	 { 
	  document.form.opt_bene1.checked=true;
	  document.form.opt_bene2.checked=false;
	  document.form.opt_bene3.checked=false;
	  
	  document.form.slc_empl_bene.disabled=false;
	  document.form.slc_prov_bene.disabled=true;
	  document.form.slc_otro_bene.disabled=true;
	 }
	 else
		 if(valor=='2') 
		 { 
			document.form.opt_bene1.checked=false;
			document.form.opt_bene2.checked=true;
			document.form.opt_bene3.checked=false;
			
			document.form.slc_empl_bene.disabled=true;
			document.form.slc_prov_bene.disabled=false;
			document.form.slc_otro_bene.disabled=true;
		 }
 		 else
 			if(valor=='3')
 			{ 
				document.form.opt_bene1.checked=false;
				document.form.opt_bene2.checked=false;
				document.form.opt_bene3.checked=true;
				
				document.form.slc_empl_bene.disabled=true;
				document.form.slc_prov_bene.disabled=true;
				document.form.slc_otro_bene.disabled=false;
 			}
			
}

function crear_txt_arreglo_esp(elemento,pos)
{
	elemento='';
	for(i=0;i<partidas.length;i++)
		{
			var mon=MoneyToNumber(partidas[i][pos]);
			elemento+=mon;
			if(i!=(partidas.length-1))
				elemento+=",";
			else
				elemento;
		}
	return elemento;
}

function verifica_partida()
{
 var partida_num= document.getElementById('item').getElementsByTagName('tr').length; 
 if (partida_num >0) 
 {
 alert("Para cambiar de categor\u00EDa Proyecto o Acci\u00F3n Centralizada \n se requiere no tener asociadas partidas");
 
 }
else
 {
 abrir_ventana('includes/arbolCategoria.php?dependencia=<?php echo $_SESSION['user_depe_id'];?>&campo_nom_supe=txt_nombre_imputa&campo_cod_supe=txt_cod_imputa&campo_nombre_accion=txt_nombre_accion&campo_cod_accion=txt_cod_accion&tipo=chk_tp_imputa&formulario=form&tipo_docu=0&centrog=centro_gestor&centroc=centro_costo&opcion=sopg&campo_cod_supe2=txt_cod_imputa2&campo_cod_accion2=txt_cod_accion2');
 }
}

//Funcion permite obtetner text de un cobo
function obtener_texto(tipo)
{
 if(tipo==1)
 {
   var emp;
   var emp1;
   var depe;
   var depe1;
   if(document.form.slc_empl_bene.value != '')
   {
	 document.form.slc_prov_bene.value=''
	 document.form.slc_otro_bene.value=''
	 document.form.hid_bene_tp.value=1;
	 document.form.hid_bene_ci_rif.value=document.form.slc_empl_bene.value;
	 document.form.txt_ci_bene.value=document.form.slc_empl_bene.value;
	 document.form.hid_beneficiario.value=document.form.slc_empl_bene.options[document.form.slc_empl_bene.selectedIndex].text;
	   
       emp=document.form.slc_empl_bene.value;  
       <?php
       $sql_em="SELECT * FROM sai_seleccionar_campo('sai_usuario','empl_cedula,depe_id','','',2) 
	   resultado_set(empl_cedula varchar,depe_id varchar)"; 
	   $resultado_set_em=pg_query($conexion,$sql_em) or die("Error al mostrar");
	   while($rowem=pg_fetch_array($resultado_set_em)) 
	   { ?>
          emp1="<?php echo trim($rowem['empl_cedula']); ?>"
	      if (emp==emp1)
          { 
	        depe="<?php echo trim($rowem['depe_id']); ?>";
	      }	<?php 
 	   }
	   $sql_de="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombre','','',2) 
	   resultado_set(depe_id varchar,depe_nombre varchar)"; 
	   $resultado_set_de=pg_query($conexion,$sql_de) or die("Error al mostrar");
	   while($rowde=pg_fetch_array($resultado_set_de)) 
	   { ?>
          depe1="<?php echo trim($rowde['depe_id']); ?>"
	      if (depe==depe1)
          { 
	        document.form.txt_depe_bene.value="<?php echo trim($rowde['depe_nombre']); ?>";
	      }	<?php 
 	   }
	   ?>
	 document.form.hid_dependencia.value=document.form.txt_depe_bene.value;
   }
 }
 if(tipo==2)
 {
   if(document.form.slc_prov_bene.value != '')
   {
	   document.form.slc_empl_bene.value=''
	   document.form.slc_otro_bene.value=''
	   document.form.hid_bene_tp.value=2;
	   document.form.txt_ci_bene.value=document.form.slc_prov_bene.value
	   document.form.hid_bene_ci_rif.value=document.form.slc_prov_bene.value
	   document.form.hid_beneficiario.value=document.form.slc_prov_bene.options[document.form.slc_prov_bene.selectedIndex].text;
	   document.form.txt_depe_bene.value='<?php echo $_SESSION['user_depe'];?>'
	   document.form.hid_dependencia.value='<?php echo $_SESSION['user_depe'];?>'
	   var codigo;
       var codigo1;
       codigo=document.form.slc_prov_bene.value;  
   }
 }
 if(tipo==3)
 {
   if(document.form.slc_otro_bene.value != '')
   {
	 document.form.slc_prov_bene.value=''
	 document.form.slc_empl_bene.value=''
	 document.form.hid_bene_tp.value=3;
	 document.form.hid_bene_ci_rif.value=document.form.slc_otro_bene.value
	 document.form.txt_ci_bene.value=document.form.slc_otro_bene.value
	 document.form.hid_beneficiario.value=document.form.slc_otro_bene.options[document.form.slc_otro_bene.selectedIndex].text;
	 document.form.txt_depe_bene.value='<?php echo $_SESSION['user_depe'];?>'
	 document.form.hid_dependencia.value='<?php echo $_SESSION['user_depe'];?>'
   }
 }

}

<!--DESCARGAR LAS PARTIDAS AGREGADAS EN UN TXT TIPO HIDDEN DEL FORMULARIO -->
function crear_txt_arreglo(elemento,pos)
{
	elemento='';
	for(i=0;i<partidas.length;i++)
		{
			elemento+=partidas[i][pos];
			if(i!=(partidas.length-1))
				elemento+=",";
			else
				elemento;
		}
	return elemento;
}
function disponer_txt_arreglo(elemento,pos)
{
	elemento='';
	for(i=0;i<array_disponible.length;i++)
		{
			elemento+=array_disponible[i][pos];
			if(i!=(array_disponible.length-1))
				elemento+=",";
			else
				elemento;
		}
	return elemento;
}


//Validar Datos
function enviar()
{

		if( (document.form.slc_empl_bene.value=="") &&(document.form.slc_prov_bene.value=="") && (document.form.slc_otro_bene.value=="") )
		{
			alert('Debe seleccionar un beneficiario');
			return;
		}

		if(document.form.txt_detalle.value=="")
		{
			alert('Debe especificar el detalle de la solicitud');
			document.form.txt_detalle.focus();
			return;
		}

		if(document.form.edo_vzla.value=="0")
		{
			alert('Debe especificar el estado para la solicitud');
			document.form.edo_vzla.focus();
			return;
		}
		
		if( (document.form.txt_cod_imputa.value=="") && (document.form.txt_cod_accion.value=="") )
		{
			alert('Debe seleccionar la categor\u00EDa para la cual desea hacer la imputaci\u00F3n');
			return;
		}
		
		if((document.form.hid_largo.value<1) || (partidas=="") )
		{
			alert("Este documento no posee partidas asociadas");
			return;
		}
			
		  var x_total=0;
		  if( (partidas.length <= array_ini_part.length) && (document.form.hid_cambio.value==0) )
		  { 
			   for(i=0;i<partidas.length;i++)
			   {
				   p=allTrim(partidas[i][4]);
				   pm=allTrim(partidas[i][6]);
				   pm=(MoneyToNumber(pm));
				   for(j=0;j<array_ini_part.length;j++)
				   {   
						/*Caso donde las partidas y montos son iguales, y tal vez se haya eliminado
						una partida o la misma este en otra posicion*/ 
						p_a=array_ini_part[j];
						pm_a=array_ini_mont[j];
						if( (p==p_a) && ( parseFloat(pm) <= parseFloat(pm_a) ) )
						{
							document.form.hid_cambio.value=0;
							x_total=x_total+1;
						}
				   }
			  } 
		  }
		  // Partidas iguales y/o diferentes
		  if (x_total != partidas.length) 
		  { 
			 for(i=0;i<partidas.length;i++)
			 {
				 var c=0;
				 var arre = new Array(2);
				 p=allTrim(partidas[i][4]);
				 pm=allTrim(partidas[i][6]);
				 pm=(MoneyToNumber(pm));
				 for(j=0;j<array_ini_part.length;j++)
				 {   
				     p_a=array_ini_part[j];
					 pm_a=array_ini_mont[j];
					 /*Caso donde las partidas son iguales y los montos diferentes*/
					 if( (p==p_a) && (parseFloat(pm) > parseFloat(pm_a)) )
					 {
						 monto_dif=parseFloat(pm)-parseFloat(pm_a);
						 //Guardar en el arreglo de array_disponible
						 arre[0]=p;
						 arre[1]=monto_dif;
						 array_disponible[array_disponible.length]=arre;
						 document.form.hid_cambio.value=1;
					 }
					  if(p != p_a) 
					  {
						 c=c+1;
					  }
				  }
				   if(c==array_ini_part.length)
				   {
						/*Guardar en el mismo arreglo de array_disponible */
						 arre[0]=p;
						 arre[1]=(MoneyToNumber(pm));
						 array_disponible[array_disponible.length]=arre;
						 document.form.hid_cambio.value=1;
				   }
			  } 
		   }
			/*********************************************************************************/
			document.form.hid_cambio_part.value=disponer_txt_arreglo('document.form.hid_cambio_part.value',0);
			document.form.hid_cambio_mont.value=disponer_txt_arreglo('document.form.hid_cambio_mont.value',1); 
	   
    document.form.txt_arreglo_part.value=crear_txt_arreglo('document.form.txt_arreglo_part.value',4);
    document.form.txt_arreglo_proy.value=crear_txt_arreglo('document.form.txt_arreglo_proy.value',1);
    document.form.txt_arreglo_acc.value=crear_txt_arreglo('document.form.txt_arreglo_acc.value',2);
    
    document.form.txt_arreglo_mont.value=crear_txt_arreglo_esp('document.form.txt_arreglo_mont.value',6);
    document.form.txt_arreglo_mont_exento.value=crear_txt_arreglo_esp('document.form.txt_arreglo_mont_exento.value',7);
	 
	document.form.hid_cambio_largo.value=array_disponible.length; 
	document.form.hid_largo_total.value=partidas.length;

	
	 
	if(confirm("Datos introducidos de manera correcta. Est\u00e1 seguro que desea continuar?."))
    {
	
		var texto=crear();
		document.form.txt_arreglo_f.value=texto;
		var texto=crear_digital();
		document.form.txt_arreglo_d.value=texto;
		document.form.chk_tp_imputa[0].disabled=false;
		document.form.chk_tp_imputa[1].disabled=false;
	    document.form.submit();
    }	
}

function revisar_doc(id_documento,id_tipo_documento,id_opcion,objeto_siguiente_id,cadena_siguiente_id,id_objeto_actual)
{ 
	document.form.action="accion_ejecutar.php?tipo="+id_tipo_documento+"&accion="+id_objeto_actual+"&accion_sig=" + objeto_siguiente_id + "&hijo="+cadena_siguiente_id+"&opcion="+id_opcion+"&id="+id_documento;

	enviar();
}

</script><style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
-->
</style>
<?php
$a_o="comp-%".substr($_SESSION['an_o_presupuesto'],2,2);
$pres_anno=$_SESSION['an_o_presupuesto'];

/* Parche para que los sopg funcionen en el año anterior */
//$a_o="comp-%".substr('2014',2,2);
//$pres_anno=2014;

$i=0;

$sql_p="	Select monto,partida,
			comp_sub_espe,t1.comp_acc_esp,t1.comp_acc_pp,comp_tipo_impu,t1.comp_id as comp_id,
			case comp_tipo_impu when CAST(1 AS BIT) then (select proy_titulo  from sai_proyecto where t1.comp_acc_pp=proy_id and pre_anno='".$_SESSION['an_o_presupuesto']."') else
			(select acce_denom from sai_ac_central where t1.comp_acc_pp=acce_id and pres_anno='".$_SESSION['an_o_presupuesto']."') end as titulo_proy,
			case comp_tipo_impu when CAST(1 AS BIT) then (select paes_nombre from sai_proy_a_esp where t1.comp_acc_pp=proy_id and paes_id=t1.comp_acc_esp and pres_anno='".$_SESSION['an_o_presupuesto']."') else
			(select aces_nombre from sai_acce_esp where t1.comp_acc_pp=acce_id and t1.comp_acc_esp=aces_id and pres_anno='".$_SESSION['an_o_presupuesto']."') end as titulo_accion,
			case comp_tipo_impu when CAST(1 AS BIT) then (select centro_gestor from sai_proy_a_esp where t1.comp_acc_pp=proy_id and paes_id=t1.comp_acc_esp and pres_anno='".$_SESSION['an_o_presupuesto']."') else
			(select centro_gestor from sai_acce_esp where t1.comp_acc_pp=acce_id and t1.comp_acc_esp=aces_id and pres_anno='".$_SESSION['an_o_presupuesto']."') end as centro_gestor,
			case comp_tipo_impu when CAST(1 AS BIT) then (select centro_costo from sai_proy_a_esp where t1.comp_acc_pp=proy_id and paes_id=t1.comp_acc_esp and pres_anno='".$_SESSION['an_o_presupuesto']."') else
			(select centro_costo from sai_acce_esp where t1.comp_acc_pp=acce_id and t1.comp_acc_esp=aces_id and pres_anno='".$_SESSION['an_o_presupuesto']."') end as centro_costo,
			part_nombre,fuente_financiamiento
		from 
			sai_comp_imputa t1,sai_partida t2,sai_disponibilidad_comp t3, sai_forma_1125 t4
		where  t4.pres_anno='".$pres_anno."' and form_id_p_ac=t1.comp_acc_pp and form_id_aesp=t1.comp_acc_esp 
		and t3.comp_acc_pp=t1.comp_acc_pp and t3.comp_acc_esp=t1.comp_acc_esp
		and t3.comp_acc_pp=t1.comp_acc_pp and t3.comp_acc_esp=t1.comp_acc_esp
		and 
		part_id=comp_sub_espe and t1.pres_anno=t2.pres_anno and 
		t3.comp_id=t1.comp_id and t3.partida=t2.part_id and 
		t1.comp_id in (select docg_id from sai_doc_genera where esta_id<>15 and docg_id like '".$a_o."' order by docg_fecha)
		order by comp_id";

$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
while($row=pg_fetch_array($resultado_set_most_p)) 
   {
	$partida = $row['comp_sub_espe'];
  	$acc_esp = $row['comp_acc_esp'];
  	$acc_pp = $row['comp_acc_pp'];
  	$imputacion = $row['comp_tipo_impu'];
  	$id_comp =  $row['comp_id'];
  	$titulo = $row['titulo_proy'];
  	$accion = $row['titulo_accion'];
  	$descripcion = $row['part_nombre'];
  	$gestor = $row['centro_gestor'];
  	$costo = $row['centro_costo'];
  	$monto_comp=$row['monto'];
  	$fuente=$row['fuente_financiamiento'];
   	echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$id_comp';
				registro[1]='$acc_esp';
				registro[2]='$acc_pp';
				registro[3]='$imputacion';
				registro[4]='$partida';					
				registro[5]='$titulo';
				registro[6]='$accion';
				registro[7]='$descripcion';
				registro[8]='$gestor';
				registro[9]='$costo';
				registro[10]='$monto_comp';
				registro[11]='$fuente';
				listado_comp[$i]=registro;
				</script>
				");
				$i++;
   }	
   



if ($comp_id<>"N/A"){//TIENE PCTA ASOCIADO, POR LO QUE SE BUSCA LAS PARTIDAS ASOCIADAS LA DISPONIBILIDAD
  $sql_pcta= " Select partida,monto,part_nombre 
  from sai_disponibilidad_comp t3,sai_partida t4 where comp_id='".$comp_id."' 
  and partida=t4.part_id and pres_anno='".$pres_anno."' and partida not in
 (select sopg_sub_espe from sai_sol_pago_imputa t2,sai_sol_pago t1 where t1.sopg_id=t2.sopg_id 
  and t1.sopg_id='".$codigo."')
 order by partida";
 // echo $sql_pcta;
  $resultado_pcta=pg_query($conexion,$sql_pcta);

  	while($row=pg_fetch_array($resultado_pcta)) 
	{    
      echo("<script language='JavaScript' type='text/JavaScript'>"); ?>
	  var registro = new Array(3);
	  registro[0] = <?php echo("'".trim($row['partida'])."';"); ?>
	  registro[1] = <?php echo("'".trim($row['monto'])."';"); ?>
	  registro[2] = <?php echo("'".trim($row['part_nombre'])."';"); ?>
	  partidas_comp[partidas_comp.length]=registro;
	 <?php	 
	  echo('</script>'); 
	}	//Del IF rowsu
}



	  //Buscar codigo y acc_especifica en sai_sol_sub_part (descargarlas en un arreglo javascript)
	  //$sql_pr= " Select * from sai_buscar_sopg_imputacion('".trim($codigo)."') as result ";
	//  $sql_pr.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, tipo bit, sopg_monto_exento float)";
	
	  $sql_pr="Select sopg_id,sopg_acc_pp, sopg_acc_esp, depe_id, sopg_sub_espe, sopg_monto, sopg_tipo_impu as tipo, 
	     	   sopg_monto_exento,case sopg_tipo_impu when CAST(1 AS BIT) then (
			   select centro_gestor from sai_proy_a_esp where sopg_acc_pp=proy_id and paes_id=sopg_acc_esp and pres_anno='".$pres_anno."') else
			  (select centro_gestor from sai_acce_esp where sopg_acc_pp=acce_id and sopg_acc_esp=aces_id and pres_anno='".$pres_anno."') end as centro_gestor,
		      case sopg_tipo_impu when CAST(1 AS BIT) then (select centro_costo from sai_proy_a_esp where sopg_acc_pp=proy_id and paes_id=sopg_acc_esp and pres_anno='".$pres_anno."') else
			  (select centro_costo from sai_acce_esp where sopg_acc_pp=acce_id and sopg_acc_esp=aces_id and pres_anno='".$pres_anno."') end as centro_costo
			  ,cpat_id
	  		  from sai_sol_pago_imputa,sai_convertidor
			  where sopg_id='".trim($codigo)."' and part_id=sopg_sub_espe";
	  
	$resultado_set_most_pr=pg_query($conexion,$sql_pr);
	$valido=$resultado_set_most_pr;
	$i=0;
	$total_exento=0;

	while($rowp=pg_fetch_array($resultado_set_most_pr))
    {
	$subpartida=trim($rowp['sopg_sub_espe']);  
//	if ($subpartida <> trim ($partida_IVA) )//No se toma en considera la partida del IVA*/
 //  {
	$submonto=trim($rowp['sopg_monto']);
	$sql_su="SELECT * FROM sai_seleccionar_campo('sai_partida','part_nombre','part_id='||'''$subpartida''','',2) 
	resultado_set(part_nombre varchar)"; 
	
	$resultado_set_most_su=pg_query($conexion,$sql_su) or die("Error al consultar partida");
	if($rowsu=pg_fetch_array($resultado_set_most_su))
	{    
			$part_nombre=trim($rowsu['part_nombre']);
			echo('<script language="JavaScript" type="text/JavaScript">'); ?>
			var registro = new Array(11);
			registro[0] = <?php echo("'".trim($rowp['tipo'])."';"); ?>
			registro[1] = <?php echo("'".trim($rowp['sopg_acc_pp'])."';"); ?>
			registro[2] = <?php echo("'".trim($rowp['sopg_acc_esp'])."';"); ?>
			registro[3] = <?php echo("'".trim($rowp['depe_id'])."';"); ?>
			registro[4] = <?php echo("'".$subpartida."';"); ?>
			registro[5] = <?php echo("'".$part_nombre."';"); ?>
			registro[6] = <?php echo("'".trim($rowp['sopg_monto'])."';"); ?>
			registro[7] = <?php echo("'".trim($rowp['sopg_monto_exento'])."';"); ?>
			registro[8] = <?php echo("'".trim($rowp['centro_gestor'])."';"); ?>
			registro[9] = <?php echo("'".trim($rowp['centro_costo'])."';"); ?>
			registro[10] = <?php echo("'".trim($rowp['cpat_id'])."';"); ?>
			
			partidas[partidas.length]=registro;
			arreglo[arreglo.length]=registro[4];
			array_ini_part[array_ini_part.length]=registro[4];
			array_ini_mont[array_ini_mont.length]=registro[6];
		   <?php	 
			echo('</script>'); 
			
		}	//Del IF rowsu
	///  } //Del if ($subpartida <> trim ($partida_IVA)
	 	 $matriz_imputacion[$i]=trim($rowp['tipo']);
		 $matriz_acc_pp[$i]=trim($rowp['sopg_acc_pp']); // proy o acc
		 $matriz_acc_esp[$i]=trim($rowp['sopg_acc_esp']); // acc esp
		 $matriz_sub_esp[$i]=trim($rowp['sopg_sub_espe']); //sub-part
		 $matriz_uel[$i]=trim($rowp['depe_id']); //depe
		 $matriz_monto[$i]=trim($rowp['sopg_monto']); //monto
		 $matriz_monto_exento[$i]=trim($rowp['sopg_monto_exento']); //monto
		 $total_exento=$total_exento+$rowp['sopg_monto_exento'];
		 
    	$sql_pcta= " Select monto,partida,part_nombre from sai_disponibilidad_comp t3,sai_partida t4 
		where comp_id='".$comp_id."' and partida=t4.part_id and pres_anno='".$pres_anno."' 
		and partida='".$subpartida."'";
	
   		$resultado_pcta=pg_query($conexion,$sql_pcta);
 		while($row=pg_fetch_array($resultado_pcta)) 
		{    
        echo("<script language='JavaScript' type='text/JavaScript'>"); ?>
	    var registro = new Array(3);
	    registro[0] = <?php echo("'".trim($row['partida'])."';"); ?>
	    registro[1] = <?php echo("'".($row['monto']+$matriz_monto[$i]+$matriz_monto_exento[$i])."';"); ?>
	    registro[2] = <?php echo("'".trim($row['part_nombre'])."';"); ?>
	    partidas_comp[partidas_comp.length]=registro;
	   <?php	 
	    echo('</script>'); 
	  }
		 
		 
		 $i++;
	} //Del While
	 
	      if ($matriz_imputacion[0]==1){//Por Proyecto
		 $query ="Select * from sai_buscar_centro_gestor_costo_proy('".trim($matriz_acc_pp[0])."','".trim($matriz_acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
		}else{//Por Accion Centralizada
		 $query ="Select * from  sai_buscar_centro_gestor_costo_acc('".trim($matriz_acc_pp[0])."','".trim($matriz_acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
		 }

		$resultado_query= pg_exec($conexion,$query);
		if ($resultado_query){
		   while($row=pg_fetch_array($resultado_query)){
		   $centrog = trim($row['centro_gestor']);
		   $centroc = trim($row['centro_costo']);
		   }
		 }

	$total_imputacion=$i;

				$sql="select * from sai_consulta_proyecto_accion('". $matriz_acc_pp[($i-1)] ."','".$matriz_acc_esp[($i-1)]."','".$matriz_imputacion[($i-1)]."',".$pres_anno.") as resultado (nombre varchar, especifica varchar, cg varchar, cc varchar)";
				$resultado_set = pg_exec($conexion ,$sql) or die("Error al visualizar datos");
				 if ($resultado_set)
						{
						  $valido=true;
						  $row_impu = pg_fetch_array($resultado_set,0); 
						   $p_a_impu_nomb=$row_impu['nombre'];
						   $a_esp_impu_nomb=$row_impu['especifica'];
						    $sopg_imp_p_c =$matriz_acc_pp[($i-1)];  
							$sopg_imputa=$matriz_acc_esp[($i-1)];
							$cg=$row_impu['cg'];
							$cc=$row_impu['cc'];
						}
			
	
	?>
</head>
<?php //if($comp_id==""){?>
<body onLoad="inicial(),deshabilitar_combo(<?=$bene_tp?>); ver_monto_letra(<?php echo  number_format($sopg_monto,2,'.','');?>, 'txt_monto_letras','');">
<form name="form" method="post" action="" enctype="multipart/form-data" id="form1">
<input type="hidden" name="codigo_sopg" value="<?php echo $codigo;?>">
<input type="hidden" name="hid_partida_actual" value="">
<input type="hidden" name="hid_comprobar" id="hid_comprobar">
<input type="hidden" name="hid_asunto_previo" value="<?php echo $documento;?>">
<input type="hidden" name="hid_otro" value="<?php echo $otro;?>">
<input type="hidden" name="hid_susti_fuente" value="<?php echo $fuente;?>">
<input type="hidden" name="hid_cambio" value="0">
<input type="hidden" name="hid_cambio_largo" value="0">
<input type="hidden" name="hid_cambio_part" value="0">
<input type="hidden" name="hid_cambio_mont" value="0">
<input type="hidden" name="hid_pres_anno" value="<?php echo $pres_anno;?>">
<table  align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
<tr>
	<td>
		<table width="100%">
					<tr class="td_gray"> 
	<td colspan="4" class="normalNegroNegrita" align="center">MODIFICAR SOLICITUD DE PAGO</td>
  </tr>
		<tr class="td_gray"> 
        <td height="21" colspan="3"><div align="left" class="normalNegroNegrita"> 
        DATOS DEL SOLICITANTE </div></td>
        </tr>
		<tr> 
		<td height="28" colspan="2"><div class="normalNegrita" >Solicitud de pago: </div></td>
		<td class="normalNegro"><?php echo $codigo;?></td>
		</tr>
		<tr> 
		<td height="28" colspan="2"><div  class="normalNegrita">Solicitante:</div></td>
		<td class="normalNegro"><?php echo $solicitante;?></td>
		</tr>
		<tr> 
		<td height="28" colspan="2"><div class="normalNegrita">C&eacute;dula de identidad:</div></td>
		<td class="normalNegro"><?php echo $cedula;?></td>
		</tr>
		<tr> 
		<td height="28" colspan="2"><div  class="normalNegrita">Correo electr&oacute;nico:</div></td>
		<td class="normalNegro"><?php echo $email;?></td>
		</tr>
		<tr> 
		<td height="28" colspan="2"><div  class="normalNegrita">Cargo:</div></td>
		<td class="normalNegro"><?php echo $cargo;?></td>
		</tr>
		<tr> 
		<td height="30" colspan="2"><div class="normalNegrita">Dependencia solicitante:</div></td>
		<?$sql="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_nombre','depe_id='||'''$depe_id''','',2)
		resultado_set(depe_nombre varchar)"; 
		$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
		$valido=$resultado_set_most_p;
		if($row=pg_fetch_array($resultado_set_most_p))
		{
 		$dependencia=trim($row['depe_nombre']); //Solicitante
		}?>
		<td class="normal">
		<select name="dependencia" class="normalNegro" id="dependencia">
 		 <option value="<?php echo($depe_id); ?>" selected><?php echo($dependencia); ?></option>
		<? $sql_str="SELECT depe_id,depe_nombrecort,depe_nombre FROM sai_dependenci WHERE (depe_nivel='4' or depe_nivel='3') and depe_id<>'$depe_id' order by depe_nombre";	
		    $res_q=pg_exec($sql_str);?>
		<?php while($depe_row=pg_fetch_array($res_q)){ ?>
	        <option value="<?php echo(trim($depe_row['depe_id'])); ?>"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
             <?php }?>
		</select>

</td>
		</tr>
<tr> 
			<td height="30" colspan="2"><div class="normalNegrita">Tipo de solicitud:</div></td>
			<?
			   $sql="SELECT * FROM sai_seleccionar_campo('sai_tipo_solicitud','nombre_sol','id_sol='||'''$tp_sol''','nombre_sol',1) 
			   resultado_set(nombre_sol varchar)"; 
		//	   echo $sql;
			   $resultado_set_most_be=pg_query($conexion,$sql) or die("Error al consultar tipo de solicitud");
			   $valido= $resultado_set_most_be;
			   if($rowbe=pg_fetch_array($resultado_set_most_be))
			   {
				$nombre_sol=$rowbe['nombre_sol'];
			   }
?>
			<td class="normal"> 
			<select name="tipo_sol" class="normalNegro" id="tipo_sol">
 		      <option value="<?php echo($tp_sol); ?>"><?php echo($nombre_sol); ?></option>
			<? $sql="SELECT * FROM sai_seleccionar_campo('sai_tipo_solicitud','id_sol,nombre_sol','esta_id=1 and id_sol<>'||'''$tp_sol''','nombre_sol',1) 
			   resultado_set(id_sol int4,nombre_sol varchar)"; 
			    $res_q=pg_exec($sql);
			    while($depe_row=pg_fetch_array($res_q)){ ?>
	        <option value="<?php echo(trim($depe_row['id_sol'])); ?>"><?php echo(trim($depe_row['nombre_sol'])); ?></option>
             <?php }?>
			</select></td>
			</tr>
		<tr> 
		<td height="30" colspan="2"><div class="normalNegrita">Tel&eacute;fono de oficina:</div></td>
		<td class="normalNegro"><?php echo $telefono;?></td>
		</tr>
		</table>	</td>
</tr>
<tr>
	<td>
		<table width="100%">
		<tr class="td_gray"> 
        <td height="21" colspan="3"><div align="left" class="normalNegroNegrita"> 
        DATOS DEL BENEFICIARIO</div></td>
        </tr>
		<tr> 
		<td height="57"><div class="normalNegrita">Beneficiario:</div></td>
		<td width="735" class="normal">

				<table>
				<tr>
				<td><input name="opt_bene" id="opt_bene1" type="radio" checked  onClick="javascript:deshabilitar_combo(1)">				</td>
				<td class="normalNegro">Empleado</td>
				<td>
					<select name="slc_empl_bene" class="normalNegro" disabled= onChange="obtener_texto(1)">
					<option value="">[Seleccione]</option>
					<?php
					#Efectuamos la consulta SQL
					if ($tp_sol==20) //Pago de Liquidaciones
					$sql_e="SELECT * FROM sai_empleado order by empl_nombres";
					else
					$sql_e="SELECT * FROM sai_empleado where esta_id=1 order by empl_nombres";
					$resultado_set_e=pg_query($conexion,$sql_e) or die("Error al mostrar");
					#Mostramos los resultados obtenidos
					while($rowe=pg_fetch_array($resultado_set_e)) 
					{ 
						  $empl_id=trim($rowe['empl_cedula']);
						  $empl_nombre=$rowe['empl_nombres'].' '.$rowe['empl_apellidos'];
						  if(($sopg_bene_tp==1) and ($empl_id==$sopg_bene_ci_rif))
						  {
						  	?>
						  	<option value="<?php echo $empl_id;?>" selected><?php echo $empl_nombre;?></option>
						  	<?php
						  }
						  else
							 {?><option value="<?php echo $empl_id;?>"><?php echo $empl_nombre;?></option> 
					   <?php }
					} ?>
					</select>				</td>
				</tr>
				<tr>
				<td><input name="opt_bene" id="opt_bene2" type="radio" checked onClick="javascript:deshabilitar_combo(2)"></td>
				<td class="normalNegro">Proveedor</td>
				<td>
					<select name="slc_prov_bene" class="normalNegro" disabled onChange="obtener_texto(2)">
					<option value="">[Seleccione]</option>
					<?php
					#Efectuamos la consulta SQL
					$sql_e="SELECT * FROM sai_seleccionar_campo('sai_proveedor_nuevo','prov_id_rif,prov_nombre','prov_esta_id=1','prov_nombre',1) 
					resultado_set(prov_id_rif varchar,prov_nombre varchar)"; 
					$resultado_set_e=pg_query($conexion,$sql_e) or die("Error al mostrar");
					#Mostramos los resultados obtenidos
					while($rowe=pg_fetch_array($resultado_set_e)) 
					{ 
						$prov_id=trim($rowe['prov_id_rif']);
						$prov_nombre=$rowe['prov_nombre'];
					//	$prov_nit=$rowe['prov_nit'];
						if($sopg_bene_tp==2)
						{
						  if($prov_id==$sopg_bene_ci_rif)
						  {?><option value="<?php echo $prov_id;?>" selected><?php echo $prov_nombre;?></option><?php
						  }
						}else
						?>
						<option value= "<?php echo $prov_id;?>"><?php echo $prov_nombre;?></option>
						<?php 
					} ?>
					</select>				</td>
				</tr>
				<tr>
				<td><input name="opt_bene" id="opt_bene3" type="radio" checked onClick="javascript:deshabilitar_combo(3)"></td>
				<td class="normalNegro">Otro</td>
				<td>
					<select name="slc_otro_bene" class="normalNegro" disabled onChange="obtener_texto(3)">
					<option value="">[Seleccione]</option>
					<?php
					#Efectuamos la consulta SQL
					$sql_e="SELECT * FROM sai_viat_benef where benvi_esta_id=1 order by benvi_nombres"; 
					$resultado_set_e=pg_query($conexion,$sql_e) or die("Error al mostrar");
					#Mostramos los resultados obtenidos
					while($rowe=pg_fetch_array($resultado_set_e)) 
					{ 
						$otro_id=trim($rowe['benvi_cedula']);
						$otro_nombre=$rowe['benvi_nombres'].' '.$rowe['benvi_apellidos'];
						if($sopg_bene_tp==3)
						{
						  if($otro_id==$sopg_bene_ci_rif)
						  {?><option value="<?php echo $otro_id;?>" selected><?php echo $otro_nombre;?></option><?php
						  }
						}else
						?>
						<option value= "<?php echo $otro_id;?>"><?php echo $otro_nombre;?></option>
						<?php 
					} ?>
					</select>				</td>
				</tr>
				</table>
	</td>
		</tr>
		<tr> 
		<td height="28"><div class="normalNegrita">C.I. o RIF:</div></td>
		<td class="normal">
		<input type="text" name="txt_ci_bene" value="<?php echo $sopg_bene_ci_rif;?>" class="normalNegro" size="20" maxlength="20" readonly/>
		<input type="hidden" name="hid_bene_tp" value="<?php echo $sopg_bene_tp;?>">
		<input type="hidden" name="hid_bene_ci_rif" id="hid_bene_ci_rif" value="<?php echo $sopg_bene_ci_rif;?>" >		</td>
		</tr>
		</table>	</td>
</tr>
<tr>
	<td colspan="4">
		<table width="100%" class="tablaalertas">
		<tr > 
        <td height="21" colspan="8" class="td_gray"><div align="left" class="normalNegroNegrita"> 
        DOCUMENTOS ANEXOS</div></td>
        </tr>
		
		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_factura" type="checkbox" id="chk_factura"  <?php if ($anexos[0]==1){echo "checked";}?> /></td>
		 <td height="21">Factura</td>
        <td height="21" ><input name="chk_ordc" type="checkbox" id="chk_ordc"  <?php if ($anexos[1]==1){echo "checked";}?>/></td>
		<td height="21" >Orden de compra</td>
        <td height="21" ><input name="chk_contrato" type="checkbox" id="chk_contrato"  <?php if ($anexos[2]==1){echo "checked";}?>/></td>
		<td height="21" >Contrato</td>
        <td height="21" ><input name="chk_certificacion" type="checkbox" id="chk_certificacion"  <?php if ($anexos[3]==1){echo "checked";}?>/></td>
        <td height="21" >Certificaci&oacute;n del control perceptivo</td>
  </tr>
		
		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_informe" type="checkbox" id="chk_informe"  <?php if ($anexos[8]==1){echo "checked";}?>/></td>
		<td height="21" >Informe o solicitud de pago a cuentas</td>
        <td height="21" ><input name="chk_ords" type="checkbox" id="chk_ords" <?php if ($anexos[5]==1){echo "checked";}?>/></td>
        <td height="21" >Orden de servicio</td>
        <td height="21" ><input name="chk_pcta" type="checkbox" id="chk_pcta"  <?php if ($anexos[6]==1){echo "checked";}?>/></td>
        <td height="21" >Punto de cuenta</td>
	    </tr>


		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_otro" type="checkbox" id="chk_otro"  <?php if ($anexos[10]==1){echo "checked";}?> onclick="javascript:act_desact()"/></td>
        <td height="21" >Otro (Especifique)</td>
		<td height="21" ></td>
        <td height="21" ><input type="text" name="txt_otro" id="txt_otro" size="25" maxlength="25" class="normalNegro" value="<?php echo $anexos_otros;?>" ></td>
        </tr>

	 </table>
	 </td>
  </tr> 
<tr>
	<td>
		<table width="100%">
		<tr class="td_gray"> 
        <td height="21" colspan="3"><div align="left" class="normalNegroNegrita"> 
        DETALLES DE LA SOLICITUD</div></td>
        </tr>

			<tr> 
				<td height="28" colspan="2"> <div class="normalNegrita">Factura N&deg;:</div>
				 </td><td> <input name="txt_factura"  type="text" class="normalNegro" id="txt_factura" size="20" maxlength="20"  align="right"  onkeyup="javascript:validar(this,2);"value="<?php echo $factura_num;?>" />
						
					<font class="normalNegrita">Fecha:</font>
					<?php if (strlen($factura_fecha>4)){?> 
					 <input type="text" size="10" id="txt_fecha_factura" name="txt_fecha_factura" class="normalNegro" value="<?php echo $factura_fecha;?>" readonly/>
					 <?php } else {?>
					 <input type="text" size="10" id="txt_fecha_factura" name="txt_fecha_factura" class="normalNegro" value="" readonly/>
					 <?php }?>					 
		<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_fecha_factura');" title="Show popup calendar">
		<img src="js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
		</a>
			<font class="normalNegrita">N&deg; de control:</font>
						<input name="txt_factura_num_control"  type="text" class="normalNegro" id="txt_factura_num_control" size="11" maxlength="10" align="right" onkeyup="javascript:validar(this,2);"  value="<?php echo $factura_control;?>"/>
					 </td>           
				</tr>
				<tr class="normal"> 
		<td height="28" class="normalNegrita" colspan="2"> 
		<div>Prioridad:</div>		</td>
		<td> 
		<select name="slc_prioridad" class ="normalNegro" >
            <option value="3" selected>Alta</option>
        </select>
		</td>
		</tr>
		<tr class="normal"> 
		<td height="33" colspan="2" class="normalNegrita">
		<div>Fuente de financiamiento:</div></td>
		<td  class="normal">
		<select name="numero_reserva" class="normalNegro" id="numero_reserva">
		<?php 
		   	$sql_ft="SELECT * FROM  sai_seleccionar_campo('sai_fuente_fin','fuef_id,fuef_descripcion','esta_id<>15 and fuef_id = '||'''$numero_reserva'''||'','fuef_descripcion',1) resultado_set(fuef_id varchar,fuef_descripcion varchar)";
   	        $res_ft=pg_exec($sql_ft);
   	        if($fte_row=pg_fetch_array($res_ft)){
		?>
        <option value="<?php echo(trim($fte_row['fuef_id'])); ?>" selected><?php echo(trim($fte_row['fuef_descripcion'])); ?></option>
         <option value="N/A">N/A</option>
   	    <?php 
   	        }
   	        $sql_ff="SELECT * FROM  sai_seleccionar_campo('sai_fuente_fin','fuef_id,fuef_descripcion','esta_id<>15 and fuef_id <> '||'''$reserva'''||'','fuef_descripcion',1) resultado_set(fuef_id varchar,fuef_descripcion varchar)";
   	        $res_ff=pg_exec($sql_ff);
  			
	        while($ftef_row=pg_fetch_array($res_ff)){ ?>
             <option value="<?php echo(trim($ftef_row['fuef_id'])); ?>"><?php echo(trim($ftef_row['fuef_descripcion'])); ?></option>
             <?php }?>
           </select>
		</td>
		</tr>
		<tr><td colspan="2"><div class="normal"><strong>N&uacute;mero del compromiso:</strong></div></td>
   <td><span class="normal">
    <select name="comp_id" class="normalNegro" id="comp_id" onChange="consulta_presupuesto()">
        <option value="<?php echo($comp_id); ?>" selected><?php echo($comp_id); ?></option>
        <option value="N/A">N/A</option>
   	    <?php 
   	        $a_o="comp-%".substr($_SESSION['an_o_presupuesto'],2,2);
  			$sql_fte="SELECT docg_id as comp_id FROM  sai_doc_genera WHERE esta_id<>15 and docg_id like '".$a_o."' and docg_id<>'".$comp_id."' order by docg_id";
    		$res_fte=pg_exec($sql_fte);	
		
	        while($fte_row=pg_fetch_array($res_fte)){ ?>
             <option value="<?php echo(trim($fte_row['comp_id'])); ?>"><?php echo(trim($fte_row['comp_id'])); ?></option>
             <?php }?>
           </select>		  
	</span>
	</td></tr>
<tr> 
		<td height="72" colspan="2"><div class="normalNegrita">Motivo del pago:</div></td>
		<td  class="normal">
			<textarea name="txt_detalle" cols="60" rows="3" class="normalNegro" onKeyDown="contador(this.form.txt_detalle,this.form.remLen,60);"
								onKeyUp="contador(this.form.txt_detalle,this.form.remLen,60);"><?php echo $sopg_detalle;?></textarea><input type="text" name="remLen" size="3" maxlength="3" value="60" readonly>		</td>
		</tr>
	<tr class="normal"> 
		<td height="33" colspan="2" class="normalNegrita">
		<div>Estado:  
		</div></td>
		<td  class="normal">
		<select name="edo_vzla" class="normalNegro" id="edo_vzla">
		<?php 
		   	$sql_ft="SELECT * FROM  safi_edos_venezuela ORDER BY nombre";
   	        $res_ft=pg_exec($sql_ft);
   	        if (($edo_vzla=="")||($edo_vzla=="0") ){
   	        	?>
             <option value="0" selected><?php echo("[Seleccione]"); ?></option>
            <?php	
   	        	}
   	        while($fte_row=pg_fetch_array($res_ft)){
			if ($edo_vzla==$fte_row['id']){?>
             <option value="<?php echo(trim($fte_row['id'])); ?>" selected><?php echo(trim($fte_row['nombre'])); ?></option>
            <?php }else{?>
             <option value="<?php echo(trim($fte_row['id'])); ?>"><?php echo(trim($fte_row['nombre'])); ?></option>
             <?php }}?>
           </select>
		</td>
		</tr>		
		<tr> 
		<td height="81" colspan="2"><div class="normalNegrita">Observaci&oacute;n:</div></td>
		<td class="normal">
			<textarea name="txt_observa" cols="60" rows="3" class="normalNegro" onBlur="javascript:LimitText(this,600)"><?php echo $sopg_observacion;?></textarea>		</td>
		</tr>
			
		
		</table>	</td>
</tr>
		<input type="hidden" name="hid_largo_total" value="<?php echo $total_imputacion;?>" >
		<input name="hid_largo" type="hidden" id="hid_largo" />
        <input name="hid_val" type="hidden" id="hid_val" />
        <input type="hidden" name="txt_arreglo_part" value="" />
        <input type="hidden" name="txt_arreglo_mont" value="" />
        <input type="hidden" name="txt_arreglo_proy" value="" />
        <input type="hidden" name="txt_arreglo_acc" value="" />
        <input type="hidden" name="txt_arreglo_mont_exento" value="" />
<tr>
	<td>
		<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="tablaalertas">
		<tr class="td_gray">
		<td >
		<div align="center" class="peqNegrita">
	<!-- <a href="javascript:verifica_partida();"><img src="imagenes/estadistic.gif" width="24" height="24" border="0"  />Categor&iacute;a</a> -->
		</div>
		</td>
		<td ><div align="center" class="normalNegroNegrita">C&oacute;digo</div></td>
		<td ><div align="center"><span class="normalNegroNegrita">Denominaci&oacute;n</span></div></td>
		</tr>
		<tr>
		<td>
		    <div align="left">
			<input name="chk_tp_imputa" type="radio" class="peqNegrita_naranja" value="1" <?php if($sopg_tp_imputacion==1) echo "checked"; ?>  disabled>
			<span class="normalNegrita">Proyectos</span></div>
      </td>
		<td rowspan="2">
		   <div align="center">
			<input name="txt_cod_imputa" type="hidden" class="normalNegro" id="txt_cod_imputa" size="15" readonly value="<?php echo $sopg_imp_p_c;?>">
			<input name="txt_cod_imputa2" type="text" class="normalNegro" id="txt_cod_imputa2" size="15" readonly value="<?php echo $cg;?>">
			</div>		</td>
		<td rowspan="2">
			<div align="center">
			<input name="txt_nombre_imputa" type="text" class="normalNegro" id="txt_nombre_imputa" size="70" readonly value="<?php echo $p_a_impu_nomb;?>">
			</div>		</td>
		</tr>
		<tr>
		<td valign="top"><div align="left">
		  <input name="chk_tp_imputa" type="radio" class="peqNegrita_naranja" value="0" <?php if($sopg_tp_imputacion==0) echo "checked";?> disabled >
		  <span class="normalNegrita">Acc. Central</span></div>
		</td>
		</tr>
		<tr>
		<td>
		<div align="left"><p><span class="normalNegrita">&nbsp;Acci&oacute;n espec&iacute;fica</span></p></div>		</td>
		<td>
			<div align="center">
			<input name="txt_cod_accion" type="hidden" class="normalNegro" id="txt_cod_accion" size="15" readonly value="<?php echo $sopg_imputa;?>">
			<input name="txt_cod_accion2" type="text" class="normalNegro" id="txt_cod_accion2" size="15" readonly value="<?php echo $cc;?>"></div>		</td>
		<td>
			<div align="center">
			<input name="txt_nombre_accion" type="text" class="normalNegro" id="txt_nombre_accion" size="70" readonly value="<?php echo $a_esp_impu_nomb;?>"></div>		</td>
		</tr>
		<tr>
		<td class="peqNegrita" align="right">Dependencia</td>
		<td>
		<?php
			 	  $id_depe=$_SESSION['user_depe_id'];
 			      $sql_str="SELECT * FROM  sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombrecort,depe_nombre','depe_id='||'''$id_depe''','',2) resultado_set(depe_id varchar,depe_nombrecort varchar, depe_nombre varchar)";
				  $res_q=pg_exec($sql_str);		  
		   ?>
           <select name="opt_depe" class="normalNegro" id="opt_depe">
             <?php while($depe_row=pg_fetch_array($res_q)){ ?>
             <option value="<?php echo(trim($depe_row['depe_id'])); ?>"><?php echo(trim($depe_row['depe_nombrecort'])); ?></option>
             <?php }?>
           </select>		   </td>
			<!--  <td class="peqNegrita" align="left">Centro Gestor: <input type="text" name="centro_gestor" id="centro_gestor" size="5" readonly="true" class="normalNegro">&nbsp;&nbsp;Centro Costo: <input type="text" name="centro_costo" id="centro_costo" size="5" readonly="true" class="normalNegro"></td>-->
		  </tr>
		</table><br>	<br></br></td>
</tr>

	<tr>
				<td colspan="2">
					<br/><br/>
					<input type="hidden" name="hid_largo" id="hid_largo"/>
					<input type="hidden" name="hid_val" id="hid_val"/>
	   <div id="PartidasTemporales" >
					<table align="center" width="700px" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
					  <tr>
							<td colspan="2" align="center">
							&nbsp;<font  class="normalNegrita">Cuentas contables</font><font class="peq_naranja">(*)</font> 
								<div id="itemContainerTemp" style="width: 560px;float: center;">
								<input autocomplete="off" size="70" type="text" id="itemCompletarTemp" name="itemCompletarTemp" value="" class="normalNegro"/></div>
								<div style="width: 700px; float: left;text-align: left;margin-top: -10px;" class="normalNegro">
								<br/><div align="center"><span class="peq_naranja">(*)</span>Introduzca la cuenta contable o una palabra contenida en el nombre</div>
								</div><br>				
								<?php
								$query_partidas_temp="SELECT t2.part_id, t2.part_nombre,t1.cpat_id,cpat_nombre 
								FROM sai_convertidor t1,  sai_partida t2,sai_cue_pat t3 
								WHERE t1.part_id=t2.part_id and t2.pres_anno='".$_SESSION['an_o_presupuesto']."'
								 and t2.part_id like '4.11.0%' and t1.cpat_id=t3.cpat_id order by 3";
								
								/*	$query_partidas_temp = 	"SELECT ".
													"part_id, ".
													"part_nombre ".
												"FROM sai_partida  ".
												"WHERE ".
													"pres_anno='".$_SESSION['an_o_presupuesto']."' and part_id not like '%.00.%' and part_id like '4.11.0%'".
												"ORDER BY part_id, part_nombre ";
								*/
									$resultado = pg_exec($conexion, $query_partidas_temp);
									$numeroFilas = pg_num_rows($resultado);
									
									$arregloItems = "";
									$idsPartidasItems = "";
									$nombresPartidasItems = "";
									$nombresItems = "";
									while($row=pg_fetch_array($resultado)){
										$arregloItems .= "'".$row["part_id"]." : ".strtoupper(str_replace("\n"," ",$row["part_nombre"]))."',";
										$idsPartidasItems .= "'".$row["part_id"]."',";
										$arregloCtas .= "'".$row["cpat_id"]." : ".strtoupper(str_replace("\n"," ",$row["cpat_nombre"]))."',";
										$idsCtasItems .= "'".$row["cpat_id"]."',";
										$nombresPartidasItems .= "'".str_replace("\n"," ",strtoupper($row["part_nombre"]))."',";
									}
									//$arregloItems = quitarAcentosMayuscula(substr($arregloItems, 0, -1));
									$arregloItems = quitarAcentosMayuscula(substr($arregloCtas, 0, -1));
									$idsPartidasItems = substr($idsPartidasItems, 0, -1);
									$idsCtasItems = substr($idsCtasItems, 0, -1);
									$nombresPartidasItems = quitarAcentosMayuscula(substr($nombresPartidasItems, 0, -1));
									?>
								<script>
									var arregloItemsTemp = new Array(<?= $arregloItems?>);
									var idsPartidasItemsTemp = new Array(<?= $idsPartidasItems?>);
									var idsCtasItems = new Array(<?= $idsCtasItems?>);
									var nombresPartidasItems = new Array(<?= $nombresPartidasItems?>);
									actb(document.getElementById('itemCompletarTemp'),arregloItemsTemp);
								</script>
							</td>
						</tr>
						

						<tr><td class="normal" width="200px" align="center"><b>Monto sujeto: </b><input type="text" id="sujeto_temp" name="sujeto_temp" size="20" class="normalNegro" value="0"  onkeypress="return inputFloat(event,true);"/></td>
						    <td class="normal" width="200px" align="center"><b>Monto exento: </b><input type="text" id="exento_temp" name="exento_temp" class="normalNegro" size="20" value="0"  onkeypress="return inputFloat(event,true);"></input>
						    <input type="button" value="Agregar" onclick="javascript:agregarItem(itemCompletarTemp,sujeto_temp,exento_temp,idsPartidasItemsTemp,idsCtasItems),add_monto(),calcular_iva();" class="normal"/>
						    </td></tr>
						    </table>
						   </div>

	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tablaalertas" id="tbl_part">
			<tr class="td_gray">
			<td width="122">
			<div align="center"><span class="normalNegrita">
			<?php if ($comp_id<>"N/A"){?>
			<img src="imagenes/estadistic.gif" border="0" />Partida			
			<?php }else{?>
			<a href="javascript:abrir_ventana('documentos/sopg/arbol_partidas.php?tipo=1&tipo_doc=1&id_p_c='+document.form.txt_cod_imputa.value+'&id_ac='+document.form.txt_cod_accion.value+'&arre='+document.form.hid_partida_actual.value,550)"><img src="imagenes/estadistic.gif" border="0" />Partida</a>
			<?php }?>
			</span></div>			</td>
			<td width="436"><div align="center"><span class="normalNegroNegrita">Denominaci&oacute;n</span></div></td>
			<td width="132"><div align="center"><span class="normalNegroNegrita">Monto compromiso</span></div></td>
			<td width="132"><div align="center"><span class="normalNegroNegrita">Monto sujeto</span></div></td>
		          <td width="132"><div align="center"><span class="normalNegroNegrita">Monto exento</span></div></td>
			</tr>
			<tbody id="ar_body">
			</tbody>
			</table>	<br>	</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>
	<div align="center">
	<input type="button" value="Confirmar" onclick="javascript:add_opciones(),add_monto(),calcular_iva()">
	</div></td>
	</tr>
<tr>
	<td >
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tablaalertas" id="tbl_mod">
		<tr class="td_gray">
		<td height="22" colspan="2"><div align="center" class="normalNegroNegrita"> ACC.C/PP</div></td>
		<td width="133" class="normalNegroNegrita"><div align="center">Acci&oacute;n espec&iacute;fica</div></td>
		<td width="113"><div align="center" class="normalNegroNegrita">Dependencia</div></td>
		<td width="96"><div align="center" class="normalNegroNegrita"> Partida/Cuenta contable</div></td>
		<td width="196"><div align="center" class="normalNegroNegrita">Denominaci&oacute;n</div></td>
		<td width="110"><div align="center" class="normalNegroNegrita">Monto sujeto</div></td>
		<td width="110"><div align="center" class="normalNegroNegrita">Monto exento</div></td>
		<td width="110"><div align="center" class="normalNegroNegrita">Acci&oacute;n</div></td>
		</tr>
		<tbody id="item">
		</tbody>
		<tr>
		<td colspan="9">&nbsp;</td>
		</tr>
   <tr>
   <td height="21" colspan="9" class="td_gray"><div align="left" class="normalNegroNegrita"> 
     	  <div align="center">DETALLE DEL IMPUESTO AL VALOR AGREGADO (IVA)</div>
 		</div>		</td>
		</tr>
		<tr >
		<td height="24" colspan="3"  align="center" valign="top" class="normalNegrita">&nbsp;&nbsp;Monto exento: 
	        	<input name="txt_monto_subtotal_exento" type="text" class="normalNegro" id="txt_monto_subtotal_exento" value="<?echo (number_format($total_exento,2,'.',','))?>" size="20" maxlength="20" readonly align="right" > </td>
			<td height="24" colspan="2"  align="center" valign="top" class="normalNegrita">Monto base: 
	        <input name="txt_monto_subtotal" type="text" class="normalNegro" id="txt_monto_subtotal" value="" size="20" maxlength="20" readonly align="right" > </td>
          <td   align="left" valign="top" class="normalNegrita" >&nbsp;&nbsp;Porcentaje : 
		 
			  <span class="normal">
			  <select name="opc_por_iva" id="opc_por_iva" class ="normalNegro"  onChange="javacript:calcular_iva()">
                <?php
		for ($ii=0; $ii <$elem_impuesto; $ii++)
		{
		?>
                <option value="<?php echo $porce_impuesto[$ii];?>"  <?php if  ($porce_impuesto[$ii]==$iva_porce[0])  {echo "selected";}?> title="<?php echo $porce_impuesto[$ii]."% ". $impu_nombre[$ii];?>"><?php echo $porce_impuesto[$ii];?></option>
                <?PHP
				
				}
				?>
              </select>
              </span> </td>
			<td colspan="4"  align="center" valign="top" class="normalNegrita"><div align="left">Monto IVA: 
                <input name="txt_monto_iva_tt" type="text" class="normalNegro" id="txt_monto_iva_tt" value="<?php echo (number_format($iva_xx,2,'.',','));?>" size="25" maxlength="25" readonly align="right"> 
            </div></td>
		  </tr>
	
		<tr > 
        <td  colspan="9" class="td_gray"><div align="left" class="normalNegroNegrita"> 
        TOTAL A PAGAR</div></td>
        </tr>
		
		<tr class="normal">
		<td colspan="9"><div align="left" class="normal">En Bs.
		  <input type="text" name="txt_monto_tot" value="<?php echo(number_format($sopg_monto,2,'.',',')); ?>"  size="15" readonly class="normalNegro">
		  </div>
        </td> 
		 </tr>
		 
		 <tr class="normal">
		<td height="18" colspan="9">
        </td> 
		 </tr>
		<tr class="normal">
		<td  colspan="9" class="normal" >En letras:
		<textarea name="txt_monto_letras" id="txt_monto_letras" rows="2" cols="85" class="normalNegro" readonly></textarea>
        <input type="hidden" name="hid_monto_tot" value="<?=$sopg_monto;?>">
		</td>
		</tr>  
		</table>	</td>
</tr>
<tr>
  <td height="18" colspan="3">&nbsp;</td>
</tr>
<tr>
  <td height="18" colspan="3">
  <table  align="center">
		<?
		include("includes/respaldos.php");
		?>
	 </table>
  </td>
</tr>
<tr>
  <td height="18" colspan="3">&nbsp;</td>
</tr>
<tr>
      <td height="18" colspan="3">
	   <?
		include("documentos/opciones_2.php");
		?>  </td>
    </tr>
</table>
    
    <input type="hidden" name="hid_imputa_tipo" value="<?php  echo implode("�",$matriz_imputacion);?>" />
</form>
</body>
</html>
<?php pg_close($conexion);?>