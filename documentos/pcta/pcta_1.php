<?php
  ob_start();
  require_once("includes/conexion.php");
  include(dirname(__FILE__).'/../../init.php');
  	 	  
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../index.php',false);
   ob_end_flush(); 
   exit;
  }
   $mostrar=true;

  $user_perfil_id = $_SESSION['user_perfil_id'];
  $sql = "SELECT * FROM sai_buscar_grupos_perfil('".$user_perfil_id."') as resultado(grupo_id int4)";
  $resultado = pg_query($conexion,$sql) or die("Error al mostrar");
  while ($row = pg_fetch_array($resultado)) {
	$lista_grupos_usuario .= ",".trim($row["grupo_id"]);
  }

  $lista_grupos_usuario = "(".substr($lista_grupos_usuario,1).")";
	
  //Buscar cuales tipo de cadena (asunto del pcta) puede iniciar segun su grupo  
  $sql_t = "select * from sai_any_tabla ('sai_wfcadena','DISTINCT wfca_tipo','docu_id=''pcta'' AND wfgr_id IN $lista_grupos_usuario') as result(wfca_tipo int2)";
  $resultado_t = pg_query($conexion,$sql_t) or die("Error al mostrar");
  while ($row_t = pg_fetch_array($resultado_t)) {
	$lista_tipos_cadena .= ",".trim($row_t["wfca_tipo"]);
  }
  $lista_tipos_cadena = "(".substr($lista_tipos_cadena,1).")";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Ingresar Punto de Cuenta</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link type="text/css" href="js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="js/lib/calendarPopup/js/dateparse.js"></script>
<script language="JavaScript" src="js/lib/actb.js"></script>
<script type="text/javascript"> g_Calendar.setDateFormat('dd/mm/yyyy');	</script>

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"
	charset="utf-8"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/ui.min.js';?>"
	charset="utf-8"></script>
	
<!-- jQuery and jQuery UI -->

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>"
	charset="utf-8"></script>
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />

<!-- elRTE -->

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/elrte.min.js';?>"
	charset="utf-8"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/elRTE.options.js';?>"
	charset="utf-8"></script>
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/elrte.min.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/elrte-inner.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />


<!-- elRTE translation messages -->

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/i18n/elrte.es.js';?>"
	charset="utf-8"></script>

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/pcta/pcuenta.js';?>"
	charset="utf-8"></script>	



	
<script type="text/javascript" charset="utf-8">

   $().ready(function() {

	   var opts = {
				doctype  :	' <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">',
				cssClass : 'el-rte',
				lang     : 'es',
				height   : 400,
				toolbar  : 'maxi',
				cssfiles : ['js/editorlr/css/elrte-inner.css']
	
			}
		

		$('#pcuenta_descripcion').elrte(opts);

  });


</script>		

	
	


<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>
<script language="javascript" src="js/func_montletra.js"></script>
<script language="javascript">

var asuntos = new Array();
partidas = new Array();
monto_tot=new Array();
arreglo= new Array();
var monto_total=new Array();


function validarRif(rif){

	campo = rif.value;
	var ubicacion = '';
	var caracteres = "abcdefghijñÑklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789áéíóúÁÉÍÓÚ-.: /";
	for (var i=0; i < campo.length; i++) {
		ubicacion = campo.substring(i, i + 1);
		if (caracteres.indexOf(ubicacion) == -1) {
			rif.value = rif.value.replace(ubicacion, "");
		}
	}

/*	var encuentra=0;
	for(j= 0; j < arreglo_rif.length; j++){
		if(rif.value==arreglo_rif[j]){
			return true;
			encuentra=1;
		}
	}
	if (encuentra==0){
	alert("Este RIF indicado no es v"+aACUTE+"lido");
	document.form1.rif_sugerido.focus();
	return		
	}*/
}

function verifica_fechas(fecha){ 
  var op=false;
  var fecha_actual = document.getElementById(fecha.id).value;
  if(fecha_actual.value!=""){
	var arreglo_f_desde = fecha_actual.split("/");
	var desde = new Date(arreglo_f_desde[2]+"/"+arreglo_f_desde[1]+"/"+arreglo_f_desde[0]);
	var hoy = new Date("<?php echo(date('Y/m/d')); ?>");
	if(desde.getTime() > hoy.getTime()){
	  alert("La Fecha no Puede ser Mayor a "+ "<?php echo(date('d/m/Y')); ?>");
	  document.getElementById(fecha.id).value="";
	  return;
	}
  }
}

function llenar_datos(campo)
{
	if(campo.value!="")
	{
		for(i=0;i<asuntos.length;i++)
		{
			if(asuntos[i][0]==campo.value)
			{
				document.form1.nom_asunto.value=asuntos[i][1];
				break;
			}
		}
		if ((campo.value=='013') || (campo.value=='039')){

			document.form1.pcta_asociado.disabled=false;
			
			}else{
				document.form1.pcta_asociado.value=0;
				document.form1.pcta_asociado.disabled=true;
				}
	}
	else
	{
		document.form1.nom_asunto.value="";
	}
}

function habilita(){

document.form1.cond_pago.disabled=true;
document.form1.txt_cod_imputa.disabled=true;
document.form1.txt_nombre_imputa.disabled=true;
document.form1.chk_tp_imputa.disabled=true;
document.form1.txt_cod_accion.disabled=true;
document.form1.txt_nombre_accion.disabled=true;
document.form1.num_reserva.disabled=true;
<?php $mostrar=true;?>
}

function inhabilita(){

document.form1.cond_pago.disabled=false;
document.form1.txt_cod_imputa.disabled=false;
document.form1.txt_nombre_imputa.disabled=false;
document.form1.chk_tp_imputa.disabled=false;
document.form1.txt_cod_accion.disabled=false;
document.form1.txt_nombre_accion.disabled=false;
document.form1.num_reserva.disabled=false;
<?php $mostrar=false;?>
}

function revisar()
{
    if(document.form1.fecha.value==""  )
	{
	  alert("Debe seleccionar la fecha del Punto de Cuenta...");
	  document.form1.fecha.focus();
	  return;
    }

    if(document.form1.pcuenta_destino.value=="")
	{
		alert("Debe seleccionar el Destino del Punto de Cuenta");
		document.form1.pcuenta_destino.focus();
		return;
	}

    if(document.form1.pcuenta_solicita.value=="")
	{
		alert("Debe indicar la persona solicitante");
		document.form1.pcuenta_solicita.focus();
		return;
	}

    if(document.form1.presentado_por.value=="")
	{
		alert("Debe indicar la persona quien va a presentar el punto de cuenta");
		document.form1.presentado_por.focus();
		return;
	}

    if(document.form1.opt_depe.value=="")
	{
		alert("Debe indicar la gerencia solicitante");
		document.form1.opt_depe.focus();
		return;
	}

	if(document.form1.pcuenta_asunto.value=="")
	{
		alert("Debe seleccionar el asunto del Punto de Cuenta");
		document.form1.pcuenta_asunto.focus();
		return;
	}else{
		 if(((document.form1.pcuenta_asunto.value=="013") && (document.form1.pcta_asociado.value=="0")) || ((document.form1.pcuenta_asunto.value=="039") && (document.form1.pcta_asociado.value=="0"))) {
             alert("Debe especificar el 1er punto de cuenta asociado al Alcance");
             document.form1.pcta_asociado.focus();
             return;
			 }
		}

	
	 descripcionlegth = $('#pcuenta_descripcion').elrte('val').length;

		if(descripcionlegth < 5 )
			
		{  	
			alert("Debe especificar la descripcion del Punto de Cuenta  y este s\u00f3lo puede tener caracteres alfanum\u00E9ricos");
			
			return;
		}





















	
	if(trim(document.form1.justificacion.value)=="")
	{
		alert("Debe indicar la justificaci\u00F3n del Punto de Cuenta");
		document.form1.justificacion.focus();
		return;
	}
	
	if(trim(document.form1.rif_sugerido.value)=="")
	{
		alert("Indique el R.I.F. del proveedor sugerido, en caso de que no aplique, colocar N/A");
		document.form1.rif_sugerido.focus();
		return;
	}
	
	if(document.form1.op_recursos[0].checked==true)
	{

		if(trim(document.form1.cond_pago.value)=="")
		{
		alert("Debe indicar las condiciones del pago");
		document.form1.cond_pago.focus();
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

	}
	
	if(confirm("Est\u00E1 seguro que desea generar este Punto de Cuenta ?"))
	{

		$('#pcuenta_descripcionVal').val($('#pcuenta_descripcion').elrte('val'));
		var texto=crear();
		document.form1.txt_arreglo_f.value=texto;
		document.form1.submit()
	}
	
}			

function elimina_pda(tipo)
{ 
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
		//agrega los elementos
		for(i=0;i<partidas.length;i++)
		{
			var row = document.createElement("tr")
			//LOS RADIO BUTTONS
			var td1 = document.createElement("td");
			td1.setAttribute("align","Center");
			td1.setAttribute("colspan","2");
			td1.className = 'normalNegro';
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
			  if(document.form1.chk_tp_imputa[0].checked==true)
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
				rad_1.setAttribute("readonly","true");
				td1.appendChild(rad_1);			
				td1.appendChild(rad_1_text);
			  
			 //CODIGO DEL PROYECTO O ACCION
			  var td2 = document.createElement("td");
			  td2.setAttribute("align","Center");
			  td2.className = 'titularMedio';
			  var txt_id_p_ac = document.createElement("INPUT");
			  txt_id_p_ac.setAttribute("type","text");
			  name="txt_id_p_ac"+i;
			  txt_id_p_ac.setAttribute("name",name);
			  txt_id_p_ac.setAttribute("readonly","true"); 
			  txt_id_p_ac.value=partidas[i][1];	 
			  txt_id_p_ac.size='15'; 
			  txt_id_p_ac.className='normalNegro';
			  td2.appendChild(txt_id_p_ac);
			  
			  //CODIGO DE LA ACCION ESPECIFICA
			  var td3 = document.createElement("td");
			  td3.setAttribute("align","Center");
			  td3.className = 'titularMedio';
			  var txt_id_acesp = document.createElement("INPUT");
			  txt_id_acesp.setAttribute("type","text");
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
	
			  row.appendChild(td1); 
			  row.appendChild(td2);
			  row.appendChild(td3); 
			  row.appendChild(td4);
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
			document.form1.hid_monto_tot.value=0;
			document.form1.txt_monto_tot.value=0;
			diner=0;
			monto_tot=new Array();
			monto_tot_exento=new Array();
			}
			else
			{
			document.form1.hid_monto_tot.value=mo;
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


function validar_pri(elem)
{
	for(i=0;i<elem;i++)
	{
		if((document.getElementById('txt_monto'+i).value=='') || (document.getElementById('txt_monto'+i).value<=0))
		{
		    alert('Revise los montos ingresados.');
		    return false;
		}	
		if(document.getElementById('txt_monto'+i).value=='') 
		{
		    alert('Debe especificar un monto, de no aplicar debe colocar cero (0) .');
		    return false;
		  }
	}	 
}

function add_opciones()
{   
		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
		var index;
		var monto_inicial =document.form1.txt_monto_tot.value

		if(document.form1.txt_cod_imputa.value=="")
		{	
			alert(" Seleccione el c\u00F3digo del Proyecto o Accion Centralizada !.");
			return;
		}
	
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
			j=eval(element_todos)+i;
			var registro = new Array(7);  	        
			registro[4]=document.getElementById('txt_codigo'+i).value;
			registro[5]=document.getElementById('txt_den'+i).value;
			var row = document.createElement("tr")
		
		   //LOS RADIO BUTTONS
			var td1 = document.createElement("td");
			td1.setAttribute("align","Center");
			td1.setAttribute("colspan","2");
			td1.className = 'normalNegro';
			var name="rb_ac_proy"+j;
			if(pos_nave>0)
			{
				 var rad_1 = document.createElement('<input type="radio" name="'+name+'">'); 
			}
			else
				{ 
					var rad_1 = document.createElement('INPUT');
					rad_1.type="radio";
					rad_1.name=name; }
				 
					rad_1.setAttribute("id",name);
					rad_1.setAttribute("readonly","true");
			  
					if(document.form1.chk_tp_imputa[0].checked==true)
					{
						registro[0]=1;
						rad_1.setAttribute("value",1);
						rad_1_text = document.createTextNode('PR');
						rad_1.defaultChecked = true;
					}
					else
						{
							registro[0]=0;		    
							rad_1.setAttribute("value",0);
							rad_1_text = document.createTextNode('AC');
							rad_1.defaultChecked = true
						}
				
			  td1.appendChild(rad_1);			
			  td1.appendChild(rad_1_text);
			 
			 //CODIGO DEL PROYECTO O ACCION
			  var td2 = document.createElement("td");
			  td2.setAttribute("align","Center");
			  td2.className = 'titularMedio';
			  var txt_id_p_ac = document.createElement("INPUT");
			  txt_id_p_ac.setAttribute("type","text");
			  name="txt_id_p_ac"+j;
			  txt_id_p_ac.setAttribute("name",name);
			  txt_id_p_ac.readOnly=true; 
			  registro[1]=document.form1.txt_cod_imputa.value;
			  txt_id_p_ac.value=registro[1];	 
			  txt_id_p_ac.size='15'; 
			  txt_id_p_ac.className='normalNegro';
			  td2.appendChild(txt_id_p_ac);
			  
			  //CODIGO DE LA ACCION ESPECIFICA
			  var td3 = document.createElement("td");
			  td3.setAttribute("align","Center");
			  td3.className = 'titularMedio';
			  var txt_id_acesp = document.createElement("INPUT");
			  txt_id_acesp.setAttribute("type","text");
			  name="txt_id_acesp"+j;
			  txt_id_acesp.setAttribute("name",name);
			  txt_id_acesp.setAttribute("readOnly","true"); 
			  registro[2]=document.form1.txt_cod_accion.value;
			  txt_id_acesp.value=registro[2];	 
			  txt_id_acesp.size='8'; 
			  txt_id_acesp.className='normalNegro';
			  td3.appendChild(txt_id_acesp);
			  
			  //CODIGO DE LA DEPENDENCIA
			  var td4 = document.createElement("td");
			  td4.setAttribute("align","Center");
			  td4.className = 'titularMedio';
			  var txt_id_depe = document.createElement("INPUT");
			  txt_id_depe.setAttribute("type","text");
			  name="txt_id_depe"+j;
			  txt_id_depe.setAttribute("name",name);
			  txt_id_depe.setAttribute("readOnly","true");
			  registro[3]=document.form1.opt_depe.value;
			  txt_id_depe.value=registro[3];	 
			  txt_id_depe.size='8'; 
			  txt_id_depe.className='normalNegro';
			  td4.appendChild(txt_id_depe);
			  
			  //CODIGO DE LA PARTIDA
			  var td5 = document.createElement("td");
			  td5.setAttribute("align","Center");
			  td5.className = 'titularMedio';
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
			 
			   monto_tot[monto_tot.length]= mon;
			   monto_inicial=parseFloat(monto_inicial) + parseFloat(mon);

			  
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
			  row.appendChild(td2);
			  row.appendChild(td3); 
			  row.appendChild(td4);
			  row.appendChild(td5);
			  row.appendChild(td6);
			  row.appendChild(td8);
			  row.appendChild(td10);
			  tbody.appendChild(row); 	
			  
			  partidas[partidas.length]=registro;
			  arreglo[arreglo.length]=registro[4];
			  document.form1.hid_partida_actual.value=arreglo;
			}
			
			element_mod = document.getElementById('tbl_mod').getElementsByTagName('tr').length;
			document.getElementById('hid_largo').value=element_mod;
			
			for(i=0;i<element_otros;i++)
			{	 
			  tbody2.deleteRow(1);
			}

	var xx1=number_format(monto_inicial,2,'.',','); 
	//document.form1.txt_monto_tot.value=monto_inicial;
	document.form1.txt_monto_tot.value=number_format(monto_inicial,2,'.',',');

    document.form1.txt_monto_tot2.value=monto_inicial;
	ver_monto_letra(monto_inicial, 'txt_monto_letras','');	
}
	

function verifica_partida()
{
 abrir_ventana('includes/arbolCategoria.php?dependencia=<?php echo $_SESSION['user_depe_id'];?>&campo_nom_supe=txt_nombre_imputa&campo_cod_supe=txt_cod_imputa&campo_nombre_accion=txt_nombre_accion&campo_cod_accion=txt_cod_accion&tipo=chk_tp_imputa&formulario=form1&tipo_docu=0&centrog=centro_gestor&centroc=centro_costo&opcion=pcta&campo_cod_supe2=txt_cod_imputa2&campo_cod_accion2=txt_cod_accion2');
}

function revisar_doc(id_tipo_documento,id_opcion,objeto_siguiente_id,objeto_siguiente_id_proy,cadena_siguiente_id,cadena_siguiente_id_proy,id_objeto_actual)
{ 
	document.form1.action="accion_ejecutar.php?tipo="+id_tipo_documento+"&accion="+id_objeto_actual+"&accion_sig=" + objeto_siguiente_id + "&hijo="+cadena_siguiente_id+"&opcion="+id_opcion;
	revisar();
}
</script>
</head>
<body>
<form name="form1" method="post" action="" id="form1" enctype="multipart/form-data" >
<?
  $id_depe = substr($_SESSION['user_perfil_id'],2,3);
  $id_depe2 = substr($_SESSION['user_perfil_id'],2,2);
?>
<table align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr  class="normal"> 
	<td height="15" valign="midden" class="td_gray"><span class="normalNegroNegrita"><strong>PUNTO DE CUENTA </strong></span></td>
  </tr>
  <tr>
	<td>
 	 <table>
	   <tr>
		 <td valign="midden"><div align="left" class="normal"><strong>Fecha: </strong></div></td>
		 <td ><input type="text" size="10" id="fecha" name="fecha" class="dateparse"
		 readonly="readonly" value="<?php echo $_POST["txt_inicio"];?>"/>
		 <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha');" title="Show popup calendar">
		 <img src="js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a></td>
  	   </tr>
	   <tr>
	     <td><div align="left" class="normal"><strong>Preparada para:</strong></div></td> 
	     <td>
		  <select name="pcuenta_destino" class="normalNegro">
		   <option selected value="">Seleccione...</option>
		   <option value="01">Presidencia</option>
		   <option value="02">Direcci&oacute;n Ejecutiva</option>
		   <option value="03">Presidencia/Direcci&oacute;n Ejecutiva</option>
		   <option value="04">Ministro(a)</option>
		  </select></TD>
	   </tr>
	   <tr>
	     <td><div align="left" class="normal"><b>Elaborado por:</b></div></td>
		 <td><input type="text" name="pcuenta_remit" class="normalNegro" value="<? echo($_SESSION['solicitante']);?>" readonly="true"></td>
	   </tr>
	   <tr>
	     <td><div align="left" class="normal"><b>Solicitado por:</b></div></td>
		 <td>
		  <select name="pcuenta_solicita" class="normalNegro">
		   <option value="">-</option>
			<?
			$presi=$_SESSION['presidente'];
			$dir_ejec=$_SESSION['director_ej'];
			
			if ($id_depe==$_SESSION['presidencia']) {
			$sql_solicitante="SELECT * FROM  sai_empleado where depe_cosige like '%$id_depe%' and carg_fundacion='".$presi."'";
			}else{
			if ($id_depe==$_SESSION['dir_ejecutiva']){
			$sql_solicitante="SELECT * FROM  sai_empleado where depe_cosige like '%$id_depe%' and carg_fundacion='".$dir_ejec."'";
			}else{
			$sql_solicitante="select se.empl_cedula, se.empl_nombres, se.empl_apellidos from sai_empleado se,sai_usuario su where se.empl_cedula=su.empl_cedula and su.usua_activo=true and (carg_fundacion='".$_SESSION['director']."' or carg_fundacion='".$_SESSION['coord_nac']."' or carg_fundacion='".$_SESSION['consultor']."' or carg_fundacion='".$_SESSION['presidente']."' or carg_fundacion='".$_SESSION['director_ej']."' or carg_fundacion='".$_SESSION['gerente']."' or carg_fundacion='".$_SESSION['adjunto']."') ORDER BY se.empl_nombres";
			}}
			
			$result=pg_query($conexion,$sql_solicitante);
			while($row=pg_fetch_array($result))
			{?>
			 <option value="<?php echo(trim($row['empl_cedula'])); ?>"><?php echo(trim($row['empl_nombres'])); echo " "; echo(trim($row['empl_apellidos']));?></option>
			<?}
			?>
		   </select></td>
	  </tr>
      <tr>
	   	<td><div align="left" class="normal"><b>Presentado por:</b></div></td>
		<td><select name="presentado_por" class="normalNegro">
		<?
		
		if ($id_depe==$_SESSION['presidencia']){
		$sql_solicitante="SELECT * FROM  sai_empleado where carg_fundacion='".$presi."'";
		}else{
		$sql_solicitante="select empl_cedula, empl_nombres, empl_apellidos 
		from sai_empleado where depe_cosige like '".$id_depe."%' and esta_id='1' and
		(carg_fundacion='".$_SESSION['gerente']."' or carg_fundacion='".$_SESSION['consultor']."' or carg_fundacion='".$_SESSION['director']."') or (carg_fundacion='".$_SESSION['director_ej']."' and esta_id='1') 
		order by depe_cosige desc";
		}
		$result=pg_query($conexion,$sql_solicitante);
		while($row=pg_fetch_array($result))
		{?>
		 <option value="<?php echo(trim($row['empl_cedula'])); ?>"><?php echo(trim($row['empl_nombres'])); echo " "; echo(trim($row['empl_apellidos']));?></option>
		<?}
		?>
		</select></td>
	 </tr>
     <tr><td class="normal" align="left"><strong>Dependencia que tramita: </strong></td>
		<td>
		<?php
		if ($id_depe==$_SESSION['presidencia']){
	      $sql_str="SELECT * FROM  sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombrecort,depe_nombre','depe_id='||'''$id_depe''','',2) resultado_set(depe_id varchar,depe_nombrecort varchar, depe_nombre varchar)";	
	 	}else{
         if(($_SESSION['user_perfil_id']==$_SESSION['perfil_aad']) || ($_SESSION['user_perfil_id']==$_SESSION['perfil_ath'])){
 		  $sql_str="SELECT * FROM  sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombrecort,depe_nombre','depe_nivel=4 or depe_id='||'''$id_depe''','',2) resultado_set(depe_id varchar,depe_nombrecort varchar, depe_nombre varchar)";         	
         }else
	 	     {
		     $sql_str="SELECT * FROM  sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombrecort,depe_nombre','depe_nivel=4 and depe_id='||'''$id_depe''','',2) resultado_set(depe_id varchar,depe_nombrecort varchar, depe_nombre varchar)";
		     }
	 	}
			$res_q=pg_exec($sql_str);		  
		   ?>
           <select name="opt_depe" class="normalNegro" id="opt_depe">
	      <?php while($depe_row=pg_fetch_array($res_q)){ 
	        if ($id_depe==$depe_row['depe_id']){?>
	        <option value="<?php echo(trim($depe_row['depe_id'])); ?>" selected="true"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
	       <?php }else{?>
             <option value="<?php echo(trim($depe_row['depe_id'])); ?>"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
             <?php }}?>
           </select>		   </td></tr>
	  <tr>
	   	<td><div align="left" class="normal"><strong> Prioridad: </strong></div></td>
	    <td><span class="normal">
	      <select name="slc_prioridad" class ="normalNegro">
            <option value="1">Baja</option>
            <option value="2">Media</option>
            <option value="3">Alta</option>
          </select></span></td>
	  </tr>
	  <tr>
	    <td><div align="left" class="normal"><strong>Asunto:</strong></div></td>
	    <td><span class="normal">
	      <select name="pcuenta_asunto" class="normalNegro" onChange="javascript: llenar_datos(this)">
            <option selected value="">Seleccione...</option>
            <?	    $sql_asu="SELECT  pcas_nombre,pcas_id FROM  sai_pcta_asunt WHERE esta_id=1 and pcas_id<>'020' ORDER BY pcas_nombre";
					$result=pg_query($conexion,$sql_asu);
					while($row=pg_fetch_array($result))
					{
						$docu_nombre=$row["pcas_nombre"]; 
						$docu_id=$row["pcas_id"]; 
						?><script language="javascript">
							var registro = new Array(2);
							registro[0]='<?php echo ($docu_id); ?>'
							registro[1]='<?php echo ($docu_nombre); ?>'
							asuntos[asuntos.length]=registro;
						</script>
						<?
 						  echo("<option value='$docu_id'>$docu_nombre</option>");
					}
				?>
          </select><input name="nom_asunto" type="hidden" id="hid_nomape"></span></td></tr>
	  <tr>
	  	<td><div align="left" class="normal"><strong>Punto de cuenta asociado:</strong></div></td>
   		<td><span class="normal">
	    <?$a_o=$_SESSION['an_o_presupuesto'];
	      $busqueda="pcta-".$_SESSION['user_depe_id'];
	      $sql_fte="SELECT * FROM  sai_seleccionar_campo('sai_doc_genera t1, sai_pcuenta t2','docg_id','docg_id=pcta_id and wfob_id_ini=99 and docg_id like '||'''$busqueda%'''||' and pcta_asunto<>'||'''013'''||' and pcta_fecha like '||'''$a_o%'''||'','pcta_id',1) resultado_set(docg_id varchar)";
	      $res_fte=pg_exec($sql_fte);		  ?>
          <select name="pcta_asociado" class="normalNegro" id="fte_fin" disabled="true">
		   <option value="0">--Seleccione--</option>
	      <?php while($fte_row=pg_fetch_array($res_fte)){ ?>
             <option value="<?php echo(trim($fte_row['docg_id'])); ?>"><?php echo(trim($fte_row['docg_id'])); ?></option>
             <?php }?>
          </select></span></td></tr>
	  <tr>
	    <td>
	    <div align="left" class="normal"><strong>Descripci&oacute;n: </strong></div></td>
	    <td>
	    
















	    
	    
	    
	    <div id="pcuenta_descripcion" class="pcuenta_descripcion"><?php echo $descripcion ?></div>
	    <input type="hidden" name="pcuenta_descripcionVal" id="pcuenta_descripcionVal" value="">
	    
	    
	    











	    
	    
	    
	    
	    </td>
  	  </tr>
	  <tr>
	    <td><div align="left" class="normal"><strong>Justificaci&oacute;n:</strong></div></td>
	    <td><textarea rows="3" name="justificacion" cols="50"></textarea></td></tr>
	  <tr>
	    <td><div align="left" class="normal"><strong>Lapso de Convenio/Contrato:</strong></div></td>
	    <td><textarea rows="2" name="convenio" cols="50"></textarea></td></tr>
	  <tr>
		<td><div align="left" class="normal"><strong>Garant&iacute;a:</strong></div></td>
		<td><textarea rows="2" name="garantia" cols="50"></textarea></td></tr>
	  <tr>
	    <td><div align="left" class="normal"><strong>Rif del Proveedor Sugerido:</strong></div></td>
		<td><input type="text" name="rif_sugerido" id="rif_sugerido" class="normalNegro" size="70" onkeyup="validarRif(rif_sugerido)">
		<?php 	
		$query = 	"SELECT prov_id_rif as id,prov_nombre as nombre ".
							"FROM ".
							"sai_proveedor_nuevo ".
							"WHERE ".
							"prov_esta_id=1 ".
							"UNION
							SELECT benvi_cedula as id, (benvi_nombres || ' ' || benvi_apellidos)  as nombre
							FROM
							sai_viat_benef WHERE benvi_esta_id=1
							UNION
							SELECT empl_cedula as id, (empl_nombres || ' ' || empl_apellidos) as nombre
							FROM sai_empleado WHERE esta_id=1
							ORDER BY 2";
		        $resultado = pg_exec($conexion, $query);
				$numeroFilas = pg_num_rows($resultado);
				$arregloProveedores = "";
				$cedulasProveedores = "";
				$nombresProveedores = "";
				$indice=0;
				while($row=pg_fetch_array($resultado)){
					$arregloProveedores .= "'".$row["id"]." : ".strtoupper(str_replace("\n"," ",$row["nombre"]))."',";
					$cedulasProveedores .= "'".$row["id"]."',";
					$nombresProveedores .= "'".str_replace("\n"," ",strtoupper($row["nombre"]))."',";
					$indice++;
				}
					$arregloProveedores = substr($arregloProveedores, 0, -1);
					$cedulasProveedores = substr($cedulasProveedores, 0, -1);
					$nombresProveedores = substr($nombresProveedores, 0, -1);
			?>
				<script>			
					var proveedor = new Array(<?= $arregloProveedores?>);
					var arreglo_rif = new Array(<?= $cedulasProveedores?>);
					var nombre_proveedor= new Array(<?= $nombresProveedores?>);
					actb(document.getElementById('rif_sugerido'),proveedor);
				</script>
		</td></tr>
	  <tr>
	    <td><div align="left" class="normal"><strong>Requiere recursos monetarios:</strong></div></td><td class="normal"> <input type="radio" name="op_recursos" value="1" checked="true" onchange="inhabilita()">SI&nbsp;&nbsp;<input type="radio" name="op_recursos" value="0" onchange="habilita()">NO&nbsp;&nbsp;</td></tr>
	  <tr>
	    <td><div align="left" class="normal"><strong>Condiciones de pago:</strong></div></td>
		<td><textarea rows="2" name="cond_pago" cols="50"></textarea></td></tr>
	  <tr>
	    <td><div align="left" class="normal"><strong>Observaciones:</strong></div></td>
	    <td><textarea rows="3" name="observaciones" cols="50"></textarea></td></tr>
</table>
</td>
</tr>
<tr>
  <td>
    <table align="center"  class="tablaalertas">
  	 <tr class="td_gray">
	   <td ><div align="center" class="peqNegrita"><a href="javascript:verifica_partida();">
		<img src="imagenes/estadistic.gif" width="24" height="24" border="0"  />Categor&iacute;a</a></div></td>
		<td ><div align="center" class="peqNegrita">C&oacute;digo</div></td>
		<td ><div align="center"><span class="peqNegrita">Denominaci&oacute;n</span></div></td>
	 </tr>
	 <tr>
		<td><div align="left">
			<input name="chk_tp_imputa" type="radio" class="peq" value="1" readonly="true"><span class="peqNegrita">Proyectos</span></div></td>
		<td rowspan="2"><div align="center">
			<input name="txt_cod_imputa" type="hidden" id="txt_cod_imputa" value="" >
		  <input name="txt_cod_imputa2" type="text" class="normalNegro" id="txt_cod_imputa2" size="15" value="" readonly="readonly"></div>		</td>
		<td rowspan="2">
			<div align="center"><input name="txt_nombre_imputa" type="text" class="normalNegro" id="txt_nombre_imputa" size="70" readonly="true" value=""></div></td>
	 </tr>
	 <tr>
	   <td valign="top"><div align="left"><input name="chk_tp_imputa" type="radio" class="peq" value="0" readonly="true"><span class="peqNegrita">Acci&oacute;n Cent. </span></div>		</td>
	 </tr>
	 <tr>
	   <td><div align="left"><p><span class="peqNegrita">&nbsp;Acci&oacute;n Espec&iacute;fica</span></p></div>		</td>
	   <td><input name="txt_cod_accion" type="hidden" id="txt_cod_accion">
		<input name="txt_cod_accion2" type="text" class="normalNegro" id="txt_cod_accion2" size="15" readonly></div>		</td>
	   <td><div align="center"><input name="txt_nombre_accion" type="text" class="normalNegro" id="txt_nombre_accion" size="70" readonly="true"></div></td>
	 </tr>
     <tr>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
	   <td class="peqNegrita" align="left">Proy/Acc: <input type="text" name="centro_gestor" id="centro_gestor" size="5" readonly="true" class="normalNegro">&nbsp;&nbsp;Acc Esp.: <input type="text" name="centro_costo" id="centro_costo" size="5" readonly="true" class="normalNegro"></td>
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
     <a href="javascript:abrir_ventana('documentos/pcta/arbol_partidas.php?tipo=1&tipo_doc=1&gerencia='+document.form1.opt_depe.value+'&id_p_c='+document.form1.txt_cod_imputa.value+'&id_ac='+document.form1.txt_cod_accion.value+'&arre='+document.form1.hid_partida_actual.value,550)"><img src="imagenes/estadistic.gif" border="0" />Partida</a></span></div>		</td>
    <td width="436" class="td_gray"><div align="center"><span class="peqNegrita">Denominaci&oacute;n</span></div></td>
    <td width="132" class="td_gray"><div align="center"><span class="peqNegrita">Monto </span></div></td>
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
	<td class="peqNegrita" colspan="2">Proyecto o Acci&oacute;n Centralizada </td>
	<td ><div align="center" class="peqNegrita">&nbsp;&nbsp;ACC.C/P.P </div></td>
	<td ><div align="center" class="peqNegrita">&nbsp;&nbsp;ACC.ESP </div></td>
	<td ><div align="center" class="peqNegrita">Dependencia</div></td>
	<td ><div align="center" class="peqNegrita"> Partida</div></td>
	<td ><div align="center" class="peqNegrita">Denominaci&oacute;n</div></td>
	<td ><div align="center" class="peqNegrita">&nbsp;&nbsp;Monto </div></td>
    <td ><div align="center" class="peqNegrita">&nbsp;&nbsp;Acci&oacute;n </div></td>
  </tr>
  <tbody id="item"></tbody>
</table><br>
  </td>
 </tr>
 <tr> 
   <td colspan="9" valign="midden" class="td_gray"><div align="left" class="normalNegroNegrita">TOTAL A SOLICITAR</div></td>
 </tr>
 <tr class="normal">
	<td colspan="9"><div align="left" class="normal">En Bs.
	  <input type="text" name="txt_monto_tot" size="15" readonly="true" class="normalNegro" value="0">
	  <input type="hidden" name="txt_monto_tot2"></div></td> 
  </tr>
  <tr class="normal">
	<td class="normal" align="left">En letras:
	 <textarea name="txt_monto_letras" id="txt_monto_letras" rows="2" cols="70" class="normalNegro" readonly></textarea><script>ver_monto_letra(0, 'txt_monto_letras','');</script></td>
  </tr>
  <tr><TD>

<table align="center" class="tablaalertas">
 <tr><td>
  <?
	include("includes/respaldos.php");
	?>
 </td></tr>
</table></td>
 </tr>
 <tr> 
      <td height="18" colspan="3">
	  <?
		include("documentos/opciones_1.php");
		?>		
		<input type="hidden" name="hid_depe_num" value="0"/>
		</td>
  </tr>
</table>
</form>
</body>
</html>
<?php pg_close($conexion);?>