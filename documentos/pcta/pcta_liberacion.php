<?php 
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
  include("../../lib/FCKeditor/fckeditor.php") ;
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
	header('Location:../../index.php',false);
   	ob_end_flush(); 
	exit;
  }
   ob_end_flush();
   
   $anno_pres = $_SESSION['an_o_presupuesto'];
   //$anno_pres = 2013;
   
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::LIBERACI&Oacute;N PUNTO DE CUENTA</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../../lib/FCKeditor/fckeditor.js"></script>
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<script language="javascript" src="../../js/func_montletra.js"></script><script language="javascript">

partidas = new Array();
monto_tot=new Array();
arreglo= new Array();

var monto_total=new Array();
var gerentes_depe= new Array();
var num_select;
var pcta_gerencia = new Array();

function buscar_pcta()
{
   var m=document.getElementById('pcta_asociado');  
   var num=document.form1.pcta_asociado.options.length;

   if (num>1){

   var contador=1;
   while(contador<num_select){
	document.form1.pcta_asociado.options[1] = null;
	contador++;
   }
   }
	   
	var valor=document.form1.opt_depe.value;
	var info_id = 'pcta_asociado';
	unidad = document.getElementById(info_id); 
	
	for(i=0;i<pcta_gerencia.length;i++)
	{
		var crear_opcion=document.createElement('option');
		crear_opcion.text=pcta_gerencia[i][0];
		crear_opcion.value=pcta_gerencia[i][0];
		var depe_pcta=pcta_gerencia[i][1];
		if (depe_pcta==valor){
		var vieja_opcion = unidad.options[unidad.selectedIndex];
		try {
			unidad.add(crear_opcion, null); // standards compliant; doesn't work in IE
		}
		catch(ex) {
			unidad.add(crear_opcion); // IE only
		 	  }
		}
	   }
	num_select=document.form1.pcta_asociado.options.length;
}

function llenar_datos(campo)
{
	var tipo_id = 'opt_depe';
	tipo = document.getElementById(tipo_id);

	var n = tipo.length; // Numero de líneas del segundo combo.
	
	for (var i = 0; i < n; ++i)
		tipo.remove(tipo.options[i]); // Eliminamos todas las líneas del segundo combo.
	
	for (i=0;i<gerentes_depe.length;i++)
	{
		if(gerentes_depe[i][0]==document.form1.pcuenta_solicita.value)
		{	
			tipo[tipo.length] = new Option(gerentes_depe[i][3], gerentes_depe[i][2]);
		}
	}
}
var _chardecimal = '.';    //separador de la parte decimal
function inputFloat(e,minus){

    var menos = minus || false;
    if(e==null){
        e=event;
    }
    if(e==null){
        e=window.event;
    }

    var tecla = (document.all) ? e.keyCode : e.which;//48=0,57=9, 45=menos
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


function revisar()
{
    if(document.form1.fecha.value==""  )
	{
	  alert("Debe seleccionar la fecha del Punto de Cuenta...");
	  document.form1.fecha.focus();
	  return;
    }

      if(document.form1.pcuenta_solicita.value=="")
	{
		alert("Debe indicar el gerente solicitante");
		document.form1.pcuenta_solicita.focus();
		return;
	}

	if(trim(document.form1.justificacion.value)=="")
	{
		alert("Debe indicar la justificaci\u00F3n del Punto de Cuenta");
		document.form1.justificacion.focus();
		return;
	}

    if(trim(document.form1.pcta_asociado.value)=="0")
	{
	   alert("Debe especificar el punto de cuenta asociado a la liberaci\u00F3n");
	   document.form1.pcta_asociado.focus();
	   return;
	}

	if( (document.form1.txt_cod_imputa.value=="") && (document.form1.txt_cod_accion.value=="") )
	{
	   alert('Debe seleccionar la categor\u00EDa para la cual desea hacer la imputaci\u00F3n presupuestaria');
	   return;
	}
	
	if((document.form1.hid_largo.value<1) || (partidas=="") )
	{
	   alert("Este documento no posee partidas asociadas");
	   return;
	}
		
	if(confirm("Est\u00E1 seguro que desea generar este Punto de Cuenta ?"))
	{
		document.form1.action="pcta_eliberacion.php";
		document.form1.submit()
	}
	
}			
var listado_partidas = new Array();
var contador_partidas=0;

function buscar_presupuesto(){

	var tbody = document.getElementById('ar_body');
    for(i=0;i<contador_partidas;i++){
		tbody.deleteRow(0);	
	}

    contador_partidas=0;
	for(i=0;i<listado_partidas.length;i++)
	{
		var fila = document.createElement("tr");
		contador_partidas++;

		//CODIGO DE LA PARTIDA
		var columna1 = document.createElement("td");
		columna1.setAttribute("align","center");
		columna1.className = 'titularMedio';
		name="txt_partida"+(contador_partidas-1);
		var imp_1 = document.createElement("INPUT");
		imp_1.setAttribute("type","text");
		imp_1.setAttribute("readOnly","true");
		imp_1.setAttribute("name",name);
		imp_1.setAttribute("Id",name);
		imp_1.value=listado_partidas[i][3];
		imp_1.size='10';//15
		imp_1.className='normalNegro';
		columna1.appendChild(imp_1);

		//NOMBRE DE LA PARTIDA
		var columna2 = window.opener.document.createElement("td");
		columna2.setAttribute("align","Center");
		columna2.className = 'normalNegro';
		var imp_2 = document.createElement("INPUT");
		imp_2.setAttribute("type","text");
      	name="txt_desc"+(contador_partidas-1);
    	imp_2.setAttribute("name",name);
    	imp_2.setAttribute("readOnly","true");
		imp_2.className = "normalNegro";
		imp_2.setAttribute("value",listado_partidas[i][6]);
		imp_2.setAttribute("id",name);
		imp_2.setAttribute("size","30");
		columna2.appendChild (imp_2);	

		//MONTO PCTA
		var columna3 = document.createElement("td");
		columna3.setAttribute("align","right");
		columna3.className = 'titularMedio';
		name="monto_pcta"+(contador_partidas-1);
		var imp_3 = document.createElement("INPUT");
		imp_3.setAttribute("type","text");
		imp_3.setAttribute("name",name);
		imp_3.setAttribute("Id",name);
		imp_3.setAttribute("readOnly","true");
		imp_3.setAttribute("value",listado_partidas[i][7]);
		imp_3.size='10';
		imp_3.className='normalNegro';
		columna3.appendChild(imp_3);
			
		//MONTO LIBERAR
		var columna4 = document.createElement("td");
		columna4.setAttribute("align","right");
		columna4.className = 'titularMedio';
		name="txt_monto"+(contador_partidas-1);
		var imp_4 = document.createElement("INPUT");
		imp_4.setAttribute("type","text");
		imp_4.setAttribute("name",name);
		imp_4.setAttribute("Id",name);
		imp_4.setAttribute("onkeypress","return inputFloat(event,true)");
		imp_4.value='0.0';
		imp_4.size='10';
		imp_4.className='normalNegro';
		columna4.appendChild(imp_4);

		fila.appendChild(columna1);
		fila.appendChild(columna2);
		fila.appendChild(columna3);
		fila.appendChild(columna4);
		tbody.appendChild(fila); 

		
		document.form1.txt_cod_imputa.value=listado_partidas[i][0];
	    document.form1.txt_cod_accion.value=listado_partidas[i][1];
	    document.form1.centro_gestor.value=listado_partidas[i][8];
	    document.form1.centro_costo.value=listado_partidas[i][9];
		
		if (listado_partidas[i][2]==0){
		  document.form1.txt_nombre_accion.value=listado_partidas[i][5];
		  document.form1.txt_nombre_imputa.value=listado_partidas[i][4];
		  document.form1.chk_tp_imputa[0].checked=false;
		  document.form1.chk_tp_imputa[1].checked=true;}
		else{
			document.form1.txt_nombre_accion.value=listado_partidas[i][5];
			document.form1.txt_nombre_imputa.value=listado_partidas[i][4];
			document.form1.chk_tp_imputa[0].checked=true;
			document.form1.chk_tp_imputa[1].checked=false;
			}

	   }
	if(contador_partidas==0){
	

		document.form1.txt_nombre_accion.value='';
		document.form1.txt_nombre_imputa.value='';
		document.form1.txt_cod_imputa.value='';
	    document.form1.txt_cod_accion.value='';
	}

	tbody2=document.getElementById('servicios');
}

function validar_pri(elem)
{
	montos_pdas = new Array();
	for(i=0;i<elem;i++)
	{
		if((document.getElementById('txt_monto'+i).value=='') || (document.getElementById('txt_monto'+i).value<=0))
		  {
		      montos_pdas[i]=1;
		  }else{
			    montos_pdas[i]=0;
			  }
	}
		cont=0;
		for(i=0;i<elem;i++)
		{
		 if (montos_pdas[i]==1){
		  cont++;
		 }
		}

		if (cont==elem){
		  alert('Revise los montos ingresados, debe especificar un monto en alguna partida');
			   return false;
		  } 
}

function add_opciones()
{   
		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
		var index;
		var monto_inicial =document.form1.txt_monto_tot.value

		element_otros = document.getElementById('tbl_part').getElementsByTagName('tr').length;
		element_otros = element_otros -1;
		var tbody2 = document.getElementById('tbl_part');
										
		//se agregan ahora los elementos a la tabla inferior
		var tabla = document.getElementById('tbl_mod');
		element_todos = document.getElementById('tbl_mod').getElementsByTagName('tr').length;
		
		var tbody = document.getElementById('item');
		var id='item';
		var valido=validar_pri(element_otros);
		  
		if(valido==false){return;}
		
		if(element_otros<1) 
		{
			alert("Este documento no posee partidas asociadas");
			return;
		}
		
		for(i=0;i<element_otros;i++)
		{
		  if (document.getElementById('txt_monto'+i).value>0)
			{

			  var monto_pcta=parseFloat(MoneyToNumber(document.getElementById('monto_pcta'+i).value));
			  var monto_liberar=parseFloat(MoneyToNumber(document.getElementById('txt_monto'+i).value));
			  
			  if (monto_liberar>monto_pcta){
				  alert("El monto introducido, no puede ser superior al monto del punto de cuenta");
				  document.getElementById('txt_monto'+i).focus();
				  return;
			  }			

			var registro = new Array(7);
			registro[1]=document.getElementById('txt_partida'+i).value;
			registro[2]=document.getElementById('txt_desc'+i).value;
			registro[3]=document.getElementById('monto_pcta'+i).value;
			var row = document.createElement("tr")
		
			//Verificamos si esta ya registrada
			for(l=0;l<partidas.length;l++)
			{
			 if (partidas[l][1]==registro[1])  
			 {
				alert("Partida ya seleccionada...");
				return;
			 }
			}

			j=partidas.length;
			var row = document.createElement("tr")
		
			  //CODIGO DE LA PARTIDA
			  var td5 = document.createElement("td");
			  td5.setAttribute("align","Center");
			  td5.className = 'titularMedio';
			  var txt_id_pda = document.createElement("INPUT");
			  txt_id_pda.setAttribute("type","text");
			  txt_id_pda.setAttribute("readOnly","true");
			  name="txt_id_pda"+j;
			  txt_id_pda.setAttribute("name",name);
			  txt_id_pda.value=registro[1];	 
			  txt_id_pda.size='15'; 
			  txt_id_pda.className='normalNegro';
			  td5.appendChild(txt_id_pda);
			  
			  //DENOMINACION
			  var td6 = document.createElement("td");
			  td6.setAttribute("align","Center");
			  td6.className = 'titularMedio';
			  var txt_den_pda = document.createElement("INPUT");
			  txt_den_pda.setAttribute("type","text");
			  name="txt_den_pda"+j;
			  txt_den_pda.setAttribute("readOnly","true"); 
			  txt_den_pda.setAttribute("name",name);
			  txt_den_pda.value=registro[2];	 
			  txt_den_pda.size='30'; 
			  txt_den_pda.className='normalNegro';
			  td6.appendChild(txt_den_pda);
			  
			  //MONTO
			  var td8 = document.createElement("td");
			  td8.setAttribute("align","Center");
			  td8.className = 'titularMedio';
			  var txt_monto = document.createElement("INPUT");
			  txt_monto.setAttribute("type","text"); 
			  name="txt_monto_pda"+j;
			  txt_monto.setAttribute("name",name);
			  txt_monto.setAttribute("readOnly","true");
			  registro[3]=document.getElementById('txt_monto'+i).value;
			  var mon=MoneyToNumber(registro[3]);
              txt_monto.value=mon;	 
			  txt_monto.size='10'; 
			  txt_monto.className='normalNegro';
			  td8.appendChild(txt_monto);
			 
			   monto_tot[monto_tot.length]= mon;
			   monto_inicial=parseFloat(monto_inicial) + parseFloat(mon);
			  
			  //OPCION DE ELIMINAR
			  var td10 = document.createElement("td");				
			  td10.setAttribute("align","Center");
			  td10.className = 'normal';
			  editLink = document.createElement("a");
			  linkText = document.createTextNode("Eliminar");
 			  editLink.setAttribute("href", "javascript:elimina_pda('"+(j+1)+"')");
			  editLink.appendChild(linkText);
			  td10.appendChild (editLink);
						  
			  row.appendChild(td5);
			  row.appendChild(td6);
			  row.appendChild(td8);
			  row.appendChild(td10);
			  tbody.appendChild(row); 	
			  
			  partidas[partidas.length]=registro;
			  document.getElementById('txt_monto'+i).value=0.0;
			}
		 }
		
			document.getElementById('hid_largo').value=partidas.length;
	

	var xx1=number_format(monto_inicial,2,'.',','); 
	document.form1.txt_monto_tot.value=monto_inicial;
	document.form1.txt_monto_tot2.value=monto_inicial;
	ver_monto_letra(monto_inicial, 'txt_monto_letras','');	
	}

function elimina_pda(tipo){ 
	nave=new String(navigator.appName);
	var pos_nave=nave.indexOf("Explorer");
	var monto_inicial =document.form1.txt_monto_tot.value

	var tabla = document.getElementById('tbl_mod');
	var tbody = document.getElementById('item');
    monto_inicial=parseFloat(monto_inicial) - parseFloat(monto_tot[tipo-1]);
	document.form1.txt_monto_tot.value=monto_inicial;
	ver_monto_letra(monto_inicial, 'txt_monto_letras','');	

	for(i=0;i<partidas.length;i++)
	{
	 tabla.deleteRow(1);
	}

	for(i=tipo;i<partidas.length;i++)
	{
		partidas[i-1]=partidas[i];
		arreglo[i-1]=partidas[i][3];
		monto_tot[i-1]=monto_tot[i];
	}
	monto_tot[partidas.length-1]=0;
	partidas.pop(); 
	arreglo.pop();
	document.form1.hid_partida_actual.value=arreglo;

	nave=new String(navigator.appName);
	var pos_nave=nave.indexOf("Explorer");
	
	document.getElementById('hid_largo').value=partidas.length;

	
	
		for(i=0;i<partidas.length;i++){
		var row = document.createElement("tr");

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
		txt_id_pda.value=partidas[i][1];	 
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
		txt_den_pda.value=partidas[i][2];	 
		txt_den_pda.size='30'; 
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
		var mon=MoneyToNumber(partidas[i][3]);
		txt_monto.value=mon;	 
		txt_monto.size='10'; 
		txt_monto.className='normalNegro';
		td8.appendChild(txt_monto);	
		  
		monto_total[monto_total.length]=mon;
	
		
		//OPCION DE ELIMINAR
		var td10 = document.createElement("td");				
		td10.setAttribute("align","Center");
		td10.className = 'normal';
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href", "javascript:elimina_pda('"+(i+1)+"')");
		editLink.appendChild(linkText);
		td10.appendChild (editLink);


		row.appendChild(td5);
		row.appendChild(td6);
		row.appendChild(td8);

	    row.appendChild(td10);
		tbody.appendChild(row); 	
	}

	mo=0;
	me=0;

	if(monto_tot.length==0){document.form1.txt_monto_tot.value=0;}

	mo= monto_inicial;
	if (partidas.length==0)
	{
	document.form1.txt_monto_tot.value=0;
	diner=0;
	monto_tot=new Array();
	monto_tot_exento=new Array();
	}
	else
	{
	document.form1.txt_monto_subtotal.value=mo;
	diner= number_format(mo,2,'.','');
	}
	
	monto_total=new Array();
	diner=parseFloat(diner);
	ver_monto_letra(diner, 'txt_monto_letras','');
	ver_monto_letra(diner,'hid_monto_letras','');
	var xx1=number_format(monto_inicial,2,'.',','); 
	
	document.form1.txt_monto_tot2.value=monto_inicial;
    document.form1.txt_monto_tot.value=monto_inicial;
	ver_monto_letra(monto_inicial, 'txt_monto_letras','');	
}

</script>
</head>
<?php 
      $a_o="pcta-%".substr($anno_pres,2,2);
      $ultimos_dig_ao=substr($anno_pres,2,2);
  	  $sql = "SELECT pcta_id,pcta_gerencia, cast(substr(pcta_id,6) as integer)  from sai_pcuenta t1, sai_doc_genera t2 where pcta_id=docg_id and wfob_id_ini=99 and perf_id_act='' and  t2.esta_id<>15 and docg_id like '".$a_o."' and pcta_asunto<>'013' and pcta_asunto<>'020' and pcta_asunto<>'039' order by 3";
  	  $resultado_set=pg_query($conexion,$sql) or die("Error al consultar los puntos de cuentas");
	  $j=0;  
	while($rowor=pg_fetch_array($resultado_set)) {
		 	$gerencia_id=$rowor['pcta_gerencia'];
		 	$id_pcta =  $rowor['pcta_id'];
		   	echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$id_pcta';
				registro[1]='$gerencia_id';
				pcta_gerencia[$j]=registro;
				</script>
				");
				$j++;
	}


$i=0;
$sql_p="Select  sum(monto) as monto, b.partida, acc_esp, acc_pp, acc_tipo, pcta_id, gerencia,asunto,rif,asunto_nombre, depe_nombre, titulo_accion, centro_gestor, centro_costo
from (Select t1.pcta_sub_espe as partida,t1.pcta_acc_esp as acc_esp,t1.pcta_acc_pp as acc_pp,t1.pcta_tipo_impu as acc_tipo, t6.pcta_asociado as pcta_id,t6.monto as monto,pcta_gerencia as gerencia,pcta_asunto as asunto,rif_sugerido as rif,pcas_nombre as asunto_nombre,depe_nombre as depe_nombre, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select proy_titulo from sai_proyecto where pcta_acc_pp=proy_id and pre_anno='".$anno_pres."') 
else (select acce_denom from sai_ac_central where pcta_acc_pp=acce_id and pres_anno='".$anno_pres."') end as titulo_proy, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select paes_nombre from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$anno_pres."') 
else (select aces_nombre from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$anno_pres."') end as titulo_accion, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select centro_gestor from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$anno_pres."') 
else (select centro_gestor from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$anno_pres."') end as centro_gestor, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select centro_costo from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$anno_pres."') 
else (select centro_costo from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$anno_pres."') end as centro_costo, part_nombre 
from sai_pcta_imputa t1, sai_partida t2,sai_pcuenta t3,sai_pcta_asunt,sai_dependenci t5,sai_disponibilidad_pcta t6
where t1.pcta_id='".$_REQUEST['pcta_asociado']."' and t2.pres_anno='".$anno_pres."' and t1.pcta_id=t6.pcta_id and t1.pcta_sub_espe=t2.part_id and t6.pcta_asociado=t3.pcta_id and t2.part_id=t6.partida and t5.depe_id=pcta_gerencia and pcas_id=pcta_asunto and t3.pcta_id in (select docg_id from sai_doc_genera where esta_id<>15 and docg_id like '%".$ultimos_dig_ao."' order by docg_fecha) 
group by t1.pcta_sub_espe,t1.pcta_acc_esp,t1.pcta_acc_pp,t1.pcta_tipo_impu, t6.pcta_asociado,partida,t6.monto,pcta_gerencia,pcta_asunto,rif_sugerido,pcas_nombre,depe_nombre,part_nombre 

union
(Select  t1.pcta_sub_espe as partida,t1.pcta_acc_esp as acc_esp,t1.pcta_acc_pp as acc_pp,t1.pcta_tipo_impu as acc_tipo, t6.pcta_asociado as pcta_id,t6.monto as monto,pcta_gerencia as gerencia,pcta_asunto as asunto,rif_sugerido as rif,pcas_nombre as asunto_nombre,depe_nombre as depe_nombre, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select proy_titulo from sai_proyecto where pcta_acc_pp=proy_id and pre_anno='".$anno_pres."') 
else (select acce_denom from sai_ac_central where pcta_acc_pp=acce_id and pres_anno='".$anno_pres."') end as titulo_proy, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select paes_nombre from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$anno_pres."') 
else (select aces_nombre from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$anno_pres."') end as titulo_accion, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select centro_gestor from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$anno_pres."') 
else (select centro_gestor from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$anno_pres."') end as centro_gestor,
case pcta_tipo_impu when CAST(1 AS BIT) then (select centro_costo from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$anno_pres."') 
else (select centro_costo from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$anno_pres."') end as centro_costo, 
part_nombre from sai_pcta_imputa t1, sai_partida t2,sai_pcuenta t3,sai_pcta_asunt,sai_dependenci t5,sai_disponibilidad_pcta t6 where t1.pcta_id='".$_REQUEST['pcta_asociado']."' and t2.pres_anno='".$anno_pres."' and t1.pcta_id=t6.pcta_asociado and t1.pcta_sub_espe=t2.part_id and t6.pcta_asociado=t3.pcta_id and t2.part_id=t6.partida and t5.depe_id=pcta_gerencia and pcas_id=pcta_asunto and t3.pcta_id in (select docg_id from sai_doc_genera where esta_id<>15 and docg_id like '%".$ultimos_dig_ao."' order by docg_fecha) 
group by t1.pcta_sub_espe,t1.pcta_acc_esp,t1.pcta_acc_pp,t1.pcta_tipo_impu, t6.pcta_asociado,partida,t6.monto,pcta_gerencia,pcta_asunto,rif_sugerido,pcas_nombre,depe_nombre,part_nombre order by t6.pcta_asociado )
) as b
group by pcta_id, b.partida, acc_esp, acc_pp, acc_tipo, pcta_id, gerencia,asunto,rif,asunto_nombre, depe_nombre, titulo_accion, centro_gestor, centro_costo";
$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
while($row=pg_fetch_array($resultado_set_most_p)) 
   {
	$partida = $row['partida'];
  	$acc_esp = $row['acc_esp'];
  	$acc_pp = $row['acc_pp'];
  	$imputacion = $row['acc_tipo'];
  	$titulo = $row['titulo_proy'];
  	$accion = $row['titulo_accion'];
  	$descripcion = $row['part_nombre'];
  	$monto_pcta=$row['monto'];
  	$gestor = $row['centro_gestor'];
  	$costo = $row['centro_costo'];
  	
   $sql_partidas="select part_nombre from sai_partida where pres_anno='".$anno_pres."' and part_id='".$partida."'";
   $resultado_partidas=pg_exec($conexion,$sql_partidas);
   if($row=pg_fetch_array($resultado_partidas)){
   	$part_nombre=$row['part_nombre'];
   }
  	echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$acc_pp';
				registro[1]='$acc_esp';
				registro[2]='$imputacion';
				registro[3]='$partida';
				registro[4]='$titulo';					
				registro[5]='$accion';
				registro[6]='$part_nombre';
				registro[7]='$monto_pcta';
				registro[8]='$gestor';
				registro[9]='$costo';
				listado_partidas[$i]=registro;
				</script>
				");
				$i++;
   }	

?>
 
<body onLoad="buscar_presupuesto();">
<form name="form1" method="post" action="pcta_liberacion.php?tipo=recarga" id="form1" enctype="multipart/form-data" >
<?
	$id_depe = substr($_SESSION['user_perfil_id'],2,3);
	$id_depe2 = substr($_SESSION['user_perfil_id'],2,2);
?>
<table align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr  class="normal"> 
	<td height="15" valign="midden" class="td_gray"><span class="normalNegroNegrita"><strong>LIBERACI&Oacute;N PUNTO DE CUENTA </strong></span></td>
  </tr>
  <tr>
	<td>
 	 <table>
	   <tr>
		<td valign="midden"><div align="left" class="normal"><strong>Fecha: </strong></div></td><td>
		<div align="left">
        <input type="text" size="10" id="fecha" name="fecha" class="dateparse"
        	value="<? /*echo trim($_REQUEST['fecha']) == "" ? "31/12/2013" : $_REQUEST['fecha'];*/echo $_REQUEST['fecha'];?>"
        	readonly
        />
		<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha');" title="Show popup calendar">
		<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a></div></TD></tr>
	   <tr>
	   	<td><div align="left" class="normal"><b>Elaborado Por:</b></div></td>
		<td><input type="text" name="pcuenta_remit" class="normalNegro" value="<? echo($_SESSION['solicitante']);?>" readonly="true">
		<input type="hidden" name="pcuenta_destino" value="01"></td>
	  </tr>
	  
	  <tr>
<td class="normal" align="left"><strong>Unidad/Dependencia:</strong></td>
<td>
		<?php
		    $sql_str="SELECT depe_id,depe_nombrecort,depe_nombre FROM sai_dependenci WHERE depe_nivel='4' order by depe_nombre";
		    $res_q=pg_exec($sql_str);		  
	   ?>
           <select name="opt_depe" class="normalNegro" id="opt_depe" onchange="javascript:buscar_pcta()">
           <?php if ($_REQUEST['opt_depe']<>""){
           	$sql_str="SELECT depe_id,depe_nombrecort,depe_nombre FROM sai_dependenci WHERE depe_id='".$_REQUEST['opt_depe']."'";
		    $res_q=pg_exec($sql_str);
		    if ($depe_row=pg_fetch_array($res_q)){
           ?>
           <option value="<?php echo($_REQUEST['opt_depe']); ?>"><?php echo(trim($depe_row['depe_nombre']));?></option>
           <?php }}?>
           <option value="">Seleccione...</option>
	    <?php while($depe_row=pg_fetch_array($res_q)){ ?>
             <option value="<?php echo(trim($depe_row['depe_id'])); ?>"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
        <?php }?>
           </select>		   
</td>
</tr>
	  <tr>
	<td class="normal" align="left">
       <strong>Punto cuenta asociado:</strong>
    </td>
	<td class="normalNegro">
<select name="pcta_asociado" id="pcta_asociado" class="normalNegro" onchange="javascript:document.form1.submit()">
   <?php if (substr($_REQUEST['pcta_asociado'],0,4)=="pcta"){?>
   <option value="<?php echo $_REQUEST['pcta_asociado'];?>"><?php echo $_REQUEST['pcta_asociado'];?></option>
   <?php }?>
    <option value="-1">Seleccione</option>
	<option value="<?php echo $_REQUEST['pcta_asociado'];?>"><?php echo $_REQUEST['pcta_asociado'];?></option>
	<option value="0">N/A</option>
</select>
</td>
</tr>
	<!--   <tr><td><div align="left" class="normal"><strong>Punto de Cuenta Asociado:</strong></div></td>
        <td>
	     <?$pc_aso="pcta-%".substr($anno_pres,2,2);
	       $sql_fte="SELECT docg_id FROM  sai_doc_genera,sai_pcuenta WHERE wfob_id_ini=99 and docg_id=pcta_id and recursos=1 and docg_id like '".$pc_aso."' and pcta_asunto<> '020' order by docg_id";
	       $res_fte=pg_exec($sql_fte);		  
	     ?>
         <select name="pcta_asociado" class="normalNegro" id="fte_fin" onchange="javascript:document.form1.submit()">
         <?php if ($_REQUEST['tipo']=="recarga") {?>
		 <option value="<?php echo $_REQUEST['pcta_asociado']?>" selected="true"><?php echo $_REQUEST['pcta_asociado']?></option>
		 <?php } else {?>
	     <option value="0">--Seleccione--</option>
	   
	     <?php } while($fte_row=pg_fetch_array($res_fte)){ ?>
         <option value="<?php echo(trim($fte_row['docg_id'])); ?>"><?php echo(trim($fte_row['docg_id'])); ?></option>
         <?php }?>
         </select>		  
        </td></tr>  -->
	 <tr>
	   	<td><div align="left" class="normal"><b>Solicitado Por:</b></div></td>
		<td><select name="pcuenta_solicita" class="normalNegro" ><!-- onchange="javascript: llenar_datos(this)" -->
		<option value="">-</option>
		<?
		$sql_solicitante="SELECT t2.empl_cedula,depe_nombre,t1.depe_id,empl_nombres,empl_apellidos 
		FROM sai_dependenci t1,sai_empleado t2,sai_usuario su
		WHERE t2.depe_cosige=t1.depe_id and t2.esta_id<>15 and (carg_fundacion=46 or carg_fundacion=47 or carg_fundacion=41 or carg_fundacion=60)
		and t2.empl_cedula=su.empl_cedula and su.usua_activo=true 
		ORDER BY empl_nombres";
		
		$result=pg_query($conexion,$sql_solicitante);
		$i=0;
	    while($row=pg_fetch_array($result)) 
	    { 
 		 $ci=$row['empl_cedula'];
		 $nombre=$row['empl_nombres']." ".$row['empl_apellidos'];
		 $depe_id=$row['depe_id'];
		 $depe_nombre=$row['depe_nombre'];
		 echo("
				<script language='javascript'>
				var info = new Array(); 
				info[0]='$ci';
				info[1]='$nombre';
				info[2]='$depe_id';
				info[3]='$depe_nombre';
				gerentes_depe[$i]=info;
				</script>
			");
			$i++;
		  ?>
              <option value="<?=$ci?>"><?=$nombre?></option>
          <?php } ?>
		 </select></td>
	 </tr>
	<!--   <tr><td class="normal" align="left"><strong>Unidad/Dependencia:</strong></td>
		<td>
        <select name="opt_depe" class="normalNegro" id="opt_depe" disable="true">
        </select>
         <input type="hidden" name="presentado_por" value="<?php echo $_SESSION['jefe_presu'];?>"></td></tr>
    --> <tr><Td><div align="left" class="normal"><strong>Justificaci&oacute;n:</strong></div></Td>
	    <td><textarea rows="3" name="justificacion" cols="50"></textarea></td></tr>
	 <tr><TD><div align="left" class="normal"><strong>Observaciones:</strong></div></TD>
	    <td><textarea rows="3" name="observaciones" cols="50"></textarea></td></tr>
</table>
</td>
</tr>
<tr><td colspan="3" height="10"></td></tr>
<tr><td>
     <table align="center"  class="tablaalertas">
	   <tr class="td_gray">
		 <td><div align="center" class="peqNegrita">
		 <img src="../../imagenes/estadistic.gif" width="24" height="24" border="0"  />Categor&iacute;a</div></td>
		 <td><div align="center" class="peqNegrita">C&oacute;digo</div></td>
		 <td><div align="center"><span class="peqNegrita">Denominaci&oacute;n</span></div></td>
	   </tr>
	   <tr>
		 <td><div align="left"><input name="chk_tp_imputa" type="radio" class="peq" value="1" readonly="true">
		 <span class="peqNegrita">Proyectos</span></div>		</td>
		 <td rowspan="2"><div align="center"><input name="txt_cod_imputa" type="text" class="normalNegro" id="txt_cod_imputa" size="15" readonly="true" value=""></div>		</td>
		 <td rowspan="2"><div align="center"><input name="txt_nombre_imputa" type="text" class="normalNegro" id="txt_nombre_imputa" size="70" readonly="true" value=""></div></td>
	   </tr>
	   <tr>
	     <td valign="top"><div align="left"><input name="chk_tp_imputa" type="radio" class="peq" value="0" readonly="true">
	     <span class="peqNegrita">Acci&oacute;n Cent. </span></div>		</td>
		</tr>
		<tr>
		  <td><div align="left"><p><span class="peqNegrita">&nbsp;Acci&oacute;n Espec&iacute;fica</span></p></div>		</td>
		  <td><div align="center"><input name="txt_cod_accion" type="text" class="normalNegro" id="txt_cod_accion" size="15" readonly="true"></div>		</td>
		  <td><div align="center"><input name="txt_nombre_accion" type="text" class="normalNegro" id="txt_nombre_accion" size="70" readonly="true"></div>		</td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
	      <td class="peqNegrita" align="left">Centro Gestor: <input type="text" name="centro_gestor" id="centro_gestor" size="5" readonly="true" class="normalNegro">&nbsp;&nbsp;Centro Costo: <input type="text" name="centro_costo" id="centro_costo" size="5" readonly="true" class="normalNegro"></td>
		</tr>
	 </table>	
<br>

<table  align="center" class="tablaalertas" id="tbl_part"> 
  <tr class="td_gray">
    <td width="122" class="td_gray"><div align="center"><span class="normalNegrita">
      <input type="hidden" name="hid_partida_actual" value="">
      <input name="hid_largo" type="hidden" id="hid_largo">
      <input name="hid_val" type="hidden" id="hid_val">		
	  <input type="hidden" name="hid_acc_pp" value="">
	  <input type="hidden" name="hid_acc_esp" value="">
      <a href="javascript:abrir_ventana('../../documentos/pcta/arbol_partidas.php?tipo=1&tipo_doc=1&gerencia='+document.form1.opt_depe.value+'&id_p_c='+document.form1.txt_cod_imputa.value+'&id_ac='+document.form1.txt_cod_accion.value+'&arre='+document.form1.hid_partida_actual.value,550)"><img src="imagenes/estadistic.gif" border="0" />Partida</a></span></div>		</td>
    <td width="436" class="td_gray"><div align="center"><span class="peqNegrita">Denominaci&oacute;n</span></div></td>
    <td width="132" class="td_gray"><div align="center"><span class="peqNegrita">Monto PCTA</span></div></td>
    <td ><div align="center" class="peqNegrita">&nbsp;&nbsp;Monto</div></td>
  </tr>
  <tbody id="ar_body"></tbody>
</table>
<br>
<div align="center">
<input type="button" value="Confirmar" onclick="javascript:add_opciones()"> 
</div>
   <br>
	 <table width="722" align="center" border="0" cellpadding="0" cellspacing="3" class="tablaalertas" id="tbl_mod">
	   <tr class="td_gray">
		<td ><div align="center" class="peqNegrita"> Partida</div></td>
		<td ><div align="center" class="peqNegrita">Denominaci&oacute;n</div></td>
		<td ><div align="center" class="peqNegrita">&nbsp;&nbsp;Monto </div></td>
        <td ><div align="center" class="peqNegrita">&nbsp;&nbsp;Accion </div></td>
	   </tr>
	   <tbody id="item"></tbody>
	 </table><br></TD></tr>
	<tr> 
      <td  colspan="9" valign="midden" class="td_gray"><div align="left" class="normalNegroNegrita">  TOTAL A SOLICITAR</div></td>
    </tr>
	<tr class="normal">
	  <td colspan="9"><div align="left" class="normal">En Bs.
	  <input type="text" name="txt_monto_tot" size="15" readonly="true" class="normalNegro" value="0">
	  <input type="hidden" name="txt_monto_tot2">
      </td> 
	</tr>
	</tr>
	<tr class="normal">
	 <td class="normal" align="left">En Letras:
	 <textarea name="txt_monto_letras" id="txt_monto_letras" rows="2" cols="70" class="normalNegro" readonly></textarea><script>ver_monto_letra(0, 'txt_monto_letras','');</script></td>
	</tr>
    <tr>  
      <td height="18" colspan="3"><div align="center"><input type="button" value="Aceptar" onclick="javascript:revisar()"></div></td>
    </tr>
 	<input type="hidden" name="hid_depe_num" value="<?php echo $total_depe;?>"/>
</form>
</body>
</html>
<?php pg_close($conexion);?>