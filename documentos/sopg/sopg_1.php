<?php
ob_start();
session_start();
require_once("includes/conexion.php");
require("includes/funciones.php");
include(dirname(__FILE__) . '/../../init.php');
//require("documentos/sopggenera-select.php");
if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();



$unificar=trim($_REQUEST['hid_seleccion']);
$documento=trim($_REQUEST['codigo']);
$otro=0;
$total_definitivo=0;
$partida_IVA=trim($_SESSION['part_iva']);

/*Consulto los impuesto IVA*/
$sql= "select * from sai_consulta_impuestos ('0','IVA') as resultado ";
$sql.= "(id varchar, nombre varchar, porcetaje float4,  principal bit, tipo bit)";
$resultado_set= pg_exec($conexion ,$sql);
$valido=$resultado_set;
if ($resultado_set){
	$elem_impuesto=pg_num_rows($resultado_set);
	$id_impuesto=array($elem_impuesto);
	$porce_impuesto=array($elem_impuesto);
	$impu_nombre=array($elem_impuesto);
	$impu_prici=array($elem_impuesto);
	$ii=0;
	while($row_rete=pg_fetch_array($resultado_set)){
		$id_impuesto[$ii]=strtoupper(trim($row_rete['id']));
		$porce_impuesto[$ii]=trim($row_rete['porcetaje']);
		$impu_prici[$ii]=trim($row_rete['principal']);
		$impu_nombre[$ii]=trim($row_rete['nombre']);
		$ii++;
	}
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Ingresar Solicitud de Pago</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
	
	<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"
	charset="utf-8"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/ui.min.js';?>"
	charset="utf-8"></script>
	
	<script language="javascript" src="js/func_montletra.js"></script>
	<script language="JavaScript" src="js/lib/actb.js"></script>
	<script language="JavaScript" src="js/funciones.js"></script>
	<link type="text/css" href="js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
	<script type="text/javascript" src="js/lib/calendarPopup/js/events.js"></script>
	<script type="text/javascript" src="js/lib/calendarPopup/js/calpopup.js"></script>
	<script type="text/javascript" src="js/lib/calendarPopup/js/dateparse.js"></script>
	<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
	</script>
	

	<script language="JavaScript">

	function contador (campo, cuentacampo, limite) {
		if (campo.value.length > limite) campo.value = campo.value.substring(0, limite);
		else cuentacampo.value = limite - campo.value.length;
		}
	
    function validar_digito(objeto){
		var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		var checkStr = objeto.value;
		var allValid = true;
		for (i = 0;  i < checkStr.length;  i++){
			ch = checkStr.charAt(i);
			for (j = 0;  j < checkOK.length;  j++){
				if (ch == checkOK.charAt(j))
					break;
				if (j == checkOK.length){
					var cambio=checkStr.substring(-1,i) 
					objeto.value=cambio;
					alert("Escriba solo caracteres o n\u00FAmeros, adem\u00E1s no debe contener caracteres especiales");
					break;
				}
			}
		}
	}

	function redondear_dos_decimal(valor) {
		float_redondeado=Math.round(valor * 100) / 100;
		return float_redondeado;
	}

	function calcular_iva(){ 
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
		ver_monto_letra(tt_total, 'txt_monto_letras','');
		ver_monto_letra(tt_total,'hid_monto_letras','');

		$("#item2").children('tr').each(function(){


			if('4.03.18.01.00' ==  $(this).children('td').eq(1).find('input').val()){


				var monto = parseFloat(MoneyToNumber($(this).children('td').eq(5).find('input').val()));

				
     
				if( monto < IVA){
					
					 if(confirm("El monto del iva es menor al del compromiso desea actualizarlo")){

					  tt_total=(subtotalx + monto + exento);

					// alert(subtotalx  + ' +' + monto + exento);


					  document.form.txt_monto_tot.value=tt_total;

					  ver_monto_letra(tt_total, 'txt_monto_letras','');
					  ver_monto_letra(tt_total,'hid_monto_letras','');

						
						 document.form.txt_monto_iva_tt.value=monto;

						 

						 }else{

							 tt_total=(subtotalx+0+exento);
							 $('#opc_por_iva').val('0');
							 
							 
							  document.form.txt_monto_tot.value=tt_total;
								 

							 document.form.txt_monto_iva_tt.value=0;
	                                    
							 

							  ver_monto_letra(tt_total, 'txt_monto_letras','');
							  ver_monto_letra(tt_total,'hid_monto_letras','');

							  
						 
						 }      
		               
				}
	
			
			} 

		});

		
		return
	}
	</script>
	<script language="JavaScript" type="text/JavaScript">
	beneficiarios = new Array();

	//para los apendchild
	partidas = new Array();
	validar_compromiso = new Array();
	todas_pdas = new Array();
	monto_tot=new Array();
	monto_tot_exento=new Array();
	monto_total=new Array();
	monto_total_exento=new Array();
	arreglo= new Array();
	partidas_orden=new Array();
	arreglo_partidas=new Array();
	var listado_comp = new Array();
	listado_estados = new Array();
</script>
<?php 
   $i=0;
   $sql_estados="select * from sai_edos_venezuela order by edo_nombre";
   $resultado_estados=pg_exec($conexion,$sql_estados);
   while($row=pg_fetch_array($resultado_estados)){
   	$edo_nombre=$row['edo_nombre'];
   	$edo_id=$row['edo_id'];
   
   	echo("
				<script language='javascript'>
				var registro = new Array(); 
				registro[0]='$edo_id';
				registro[1]='$edo_nombre';
				listado_estados[$i]=registro;
				</script>
				");
				$i++;
   }


$a_o="comp-%".substr($_SESSION['an_o_presupuesto'],2,2);
$pres_anno=$_SESSION['an_o_presupuesto'];

/* Parche para que sopg se genere con el año anterior */
//$a_o="comp-%".substr('2014',2,2);
//$pres_anno=2014;
$i=0;

$sql_p="	Select monto,partida,
			comp_sub_espe,t1.comp_acc_esp,t1.comp_acc_pp,comp_tipo_impu,substr(t1.comp_id,6) as comp_id,
			case comp_tipo_impu when CAST(1 AS BIT) then (select proy_titulo  from sai_proyecto where t1.comp_acc_pp=proy_id and pre_anno='".$pres_anno."') else
			(select acce_denom from sai_ac_central where t1.comp_acc_pp=acce_id and pres_anno='".$pres_anno."') end as titulo_proy,
			case comp_tipo_impu when CAST(1 AS BIT) then (select paes_nombre from sai_proy_a_esp where t1.comp_acc_pp=proy_id and paes_id=t1.comp_acc_esp and pres_anno='".$pres_anno."') else
			(select aces_nombre from sai_acce_esp where t1.comp_acc_pp=acce_id and t1.comp_acc_esp=aces_id and pres_anno='".$pres_anno."') end as titulo_accion,
			case comp_tipo_impu when CAST(1 AS BIT) then (select centro_gestor from sai_proy_a_esp where t1.comp_acc_pp=proy_id and paes_id=t1.comp_acc_esp and pres_anno='".$pres_anno."') else
			(select centro_gestor from sai_acce_esp where t1.comp_acc_pp=acce_id and t1.comp_acc_esp=aces_id and pres_anno='".$pres_anno."') end as centro_gestor,
			case comp_tipo_impu when CAST(1 AS BIT) then (select centro_costo from sai_proy_a_esp where t1.comp_acc_pp=proy_id and paes_id=t1.comp_acc_esp and pres_anno='".$pres_anno."') else
			(select centro_costo from sai_acce_esp where t1.comp_acc_pp=acce_id and t1.comp_acc_esp=aces_id and pres_anno='".$pres_anno."') end as centro_costo,
			part_nombre,fuente_financiamiento
		from 
			sai_comp_imputa t1,sai_partida t2,sai_disponibilidad_comp t3, sai_forma_1125 t4
		where  t4.pres_anno='".$pres_anno."' and form_id_p_ac=t1.comp_acc_pp and form_id_aesp=t1.comp_acc_esp 
		and t3.comp_acc_pp=t1.comp_acc_pp and t3.comp_acc_esp=t1.comp_acc_esp
		and t3.comp_acc_pp=t1.comp_acc_pp and t3.comp_acc_esp=t1.comp_acc_esp
		and 
		part_id=comp_sub_espe and t1.pres_anno=t2.pres_anno and 
		t3.comp_id=t1.comp_id and t3.partida=t2.part_id and t4.esta_id=1 and 
		t1.comp_id in (select docg_id from sai_doc_genera where esta_id<>15 and docg_id like '".$a_o."' order by docg_fecha)
		order by partida";
//echo $sql_p;
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
   
   $inicio=0;
   
   ?>
<script  language="JavaScript" type="text/JavaScript">	


var contador_partidas=0;

	function consulta_presupuesto(){
		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
		document.form.opt_bene[3].checked=false;
		document.getElementById('Categoria').style.display='none';
		document.getElementById('itemContainerTemp').style.display='none';
		document.getElementById('PartidasTemporales').style.display='';
		document.getElementById('PartidasAutomaticas').style.display='';
    	document.getElementById('Boton').style.display='';
    	var tbody = document.getElementById('item2');
	   //Lo primero que debe hacerse es borrar las partidas existentes
	    for(i=0;i<contador_partidas;i++){
			tbody.deleteRow(0);	
		}
		    contador_partidas=0;
		    var valor=document.form.comp_id.value;
			
			for(i=0;i<listado_comp.length;i++)
			{
				var fila = document.createElement("tr");
				var comp = listado_comp[i][0];
				if (valor==comp){
				contador_partidas++;

				//LOS RADIO BUTTONS
				var td1 = document.createElement("td");
				td1.setAttribute("align","Center");
				td1.className = 'normalNegro';
				//creamos una radio button
				var name="rb_ac_proy"+(contador_partidas-1);
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
				name="txt_codigo"+(contador_partidas-1);
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
		      	name="txt_den"+(contador_partidas-1);
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
				columna5.setAttribute("align","right");
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

				fila.appendChild(td1); 
				fila.appendChild(columna1); 
				fila.appendChild(columna2);
				fila.appendChild(columna8);
				fila.appendChild(columna9);
				fila.appendChild(columna5);
				fila.appendChild(columna3);
				fila.appendChild(columna4);
				fila.appendChild(columna6);
				fila.appendChild(columna7);
				tbody.appendChild(fila); 
				
		        document.form.txt_cod_imputa.value=listado_comp[i][2];
			    document.form.txt_cod_accion.value=listado_comp[i][1];
			    document.form.centro_gestor.value=listado_comp[i][8];
			    document.form.centro_costo.value=listado_comp[i][9];
			    document.form.numero_reserva.value=listado_comp[i][11];
			    document.form.txt_cod_accion2.value=listado_comp[i][9];
			    document.form.txt_cod_imputa2.value=listado_comp[i][8];
				    
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
				document.getElementById('Categoria').style.display='';
				document.getElementById('PartidasAutomaticas').style.display='none';
				document.getElementById('PartidasTemporales').style.display='';
				document.getElementById('Boton').style.display='none';
				document.form.txt_nombre_accion.value='';
				document.form.txt_nombre_imputa.value='';
				document.form.chk_tp_imputa[0].checked=false;
				document.form.chk_tp_imputa[1].checked=false;
				document.form.txt_cod_imputa.value='';
			    document.form.txt_cod_accion.value='';
			    document.form.centro_gestor.value='';
			    document.form.centro_costo.value='';
			    document.form.numero_reserva.value='0';
				document.form.txt_cod_imputa2.value='';
			    document.form.txt_cod_accion2.value='';
	
				
		}
		
		   
	}

	
	//funcion que elimina las partidas
	function elimina_pda(tipo){ 
		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
		
		var tabla = document.getElementById('tbl_mod');
		var tbody = document.getElementById('item');
		
		for(i=0;i<partidas.length;i++){
			tabla.deleteRow(1);
		}

		for(i=tipo;i<partidas.length;i++){
			partidas[i-1]=partidas[i];
			validar_compromiso[i-1]=validar_compromiso[i];
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
 		for(i=0;i<partidas.length;i++){
			var row = document.createElement("tr");
			//LOS RADIO BUTTONS
			var td1 = document.createElement("td");
			td1.setAttribute("align","Center");
			td1.setAttribute("colspan","2");
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
			  
			if(partidas[i][0]==1){
				rad_1.setAttribute("value",1);
				rad_1_text = document.createTextNode('PR');
				rad_1.defaultChecked = true;
			}else{		    
				rad_1.setAttribute("value",0);
				rad_1_text = document.createTextNode('AC');
				rad_1.defaultChecked = true;
			}
				
			rad_1.setAttribute("id",name);
			rad_1.setAttribute("readOnly","true");
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
			txt_id_p_ac.size='8'; 
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
			txt_den_pda.size='25'; 
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
			  
			monto_total[monto_total.length]=mon;
			
			//MONTO EXENTO
			var td9 = document.createElement("td");
			td9.setAttribute("align","Center");
			td9.className = 'normalNegro';
		
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
		mo=0;
		me=0;

		if(monto_tot.length==0){document.form.txt_monto_tot.value=0;}

		for(i=0;i<monto_total.length;i++){
			mo=parseFloat(mo) + parseFloat(monto_total[i]);
			document.form.txt_monto_tot.value=mo;
		}  

		for(i=0;i<monto_total_exento.length;i++){
			me=parseFloat(me) + parseFloat(monto_total_exento[i]);
			document.form.txt_monto_subtotal_exento.value=me;
		}  

		if (partidas.length==0){
			document.form.hid_monto_tot.value=0;
			document.form.txt_monto_subtotal.value=0;
			document.form.txt_monto_subtotal_exento.value=0;
			document.form.txt_monto_tot.value=0;
			diner=0;
			monto_tot=new Array();
			monto_tot_exento=new Array();
		}else{
			document.form.hid_monto_tot.value=mo;
			document.form.txt_monto_subtotal.value=document.form.txt_monto_tot.value;
			diner= number_format(mo,2,'.','');
		}
		calcular_iva();
		monto_total=new Array();
		monto_total_exento=new Array();
		diner=parseFloat(diner);
		ver_monto_letra(diner, 'txt_monto_letras','');
		ver_monto_letra(diner,'hid_monto_letras','');
	}
	
//*****************Funcion agregar monto con solicitud desde cero***********************************************
	function add_monto(){
		var m=0;
		var m2=0;
		var m3=0;

		for(i=0;i<monto_tot.length;i++){
			m=parseFloat(m) + parseFloat(monto_tot[i]);
		}

		for(i=0;i<monto_tot_exento.length;i++){
			m3=parseFloat(m3) + parseFloat(monto_tot_exento[i]);
		}
	 
     	m2=parseFloat(m2) + parseFloat(m) + parseFloat(m3);

		document.form.txt_monto_tot.value=number_format(m2,2,'.',',');
		document.form.txt_monto_subtotal.value=number_format(m,2,'.',',');
		document.form.txt_monto_subtotal_exento.value=number_format(m3,2,'.',',');
	 
		diner= number_format(m,2,'.','');
		diner=parseFloat(diner);
	 
		ver_monto_letra(diner, 'txt_monto_letras','');
	}

	function verifica_partida(){
		var partida_num= document.getElementById('item').getElementsByTagName('tr').length; 
		if (partida_num >0){
			alert("Para cambiar de Categor"+iACUTE+"a Proyecto o Acci"+oACUTE+"n Centralizada \n se requiere no tener asociadas partidas");
 		}else{
			abrir_ventana('includes/arbolCategoria.php?dependencia=<?= $_SESSION['user_depe_id'];?>&campo_nom_supe=txt_nombre_imputa&campo_cod_supe=txt_cod_imputa&campo_nombre_accion=txt_nombre_accion&campo_cod_accion=txt_cod_accion&tipo=chk_tp_imputa&formulario=form&tipo_docu=0&centrog=centro_gestor&centroc=centro_costo&opcion=sopg&campo_cod_supe2=txt_cod_imputa2&campo_cod_accion2=txt_cod_accion2');
		}
	}

	function act_desact(){
		document.form.txt_otro.disabled = !(document.form.chk_otro.checked);
		if(document.form.chk_otro.checked){
			document.form.txt_otro.value="";
			document.form.txt_otro.focus();
		}else{
			document.form.txt_otro.value="";
		}
	}
	function mostrarBeneficiarios(valor){
		if(valor=='1'){
			div = document.getElementById("empleadoInputContainer");
			if(div.hasChildNodes()){
			    while(div.childNodes.length >= 1 ){
			    	div.removeChild(div.firstChild);       
			    }
			}
			input = document.createElement('INPUT');
			input.setAttribute("autocomplete","off");
			input.setAttribute("size","70");
			input.setAttribute("type","text");
			input.setAttribute("id","beneficiarioEmpleado");
			input.setAttribute("name","beneficiarioEmpleado");
			input.setAttribute("value","");
			input.setAttribute("class","normal");
			div.appendChild(input);
			actb(document.getElementById('beneficiarioEmpleado'),empleadosAMostrar);
			document.getElementById('contenedorEmpleados').style.display='block';
			document.getElementById('contenedorProveedores').style.display='none';
			document.getElementById('contenedorOtros').style.display='none';
			document.getElementById('beneficiarioEmpleado').focus();
		}else if(valor=='2'){
			div = document.getElementById('proveedorInputContainer');
			if(div.hasChildNodes()){
			    while(div.childNodes.length >= 1 ){
			    	div.removeChild(div.firstChild);       
			    }
			}
			input = document.createElement('INPUT');
			input.setAttribute("autocomplete","off");
			input.setAttribute("size","70");
			input.setAttribute("type","text");
			input.setAttribute("id","beneficiarioProveedor");
			input.setAttribute("name","beneficiarioProveedor");
			input.setAttribute("value","");
			input.setAttribute("class","normal");
			div.appendChild(input);
			actb(document.getElementById('beneficiarioProveedor'),proveedoresAMostrar);
			document.getElementById('contenedorProveedores').style.display='block';
			document.getElementById('contenedorEmpleados').style.display='none';
			document.getElementById('contenedorOtros').style.display='none';
			document.getElementById('beneficiarioProveedor').focus();
		}else if(valor=='3'){
			div = document.getElementById('otroInputContainer');
			if(div.hasChildNodes()){
			    while(div.childNodes.length >= 1 ){
			    	div.removeChild(div.firstChild);       
			    }
			}
			input = document.createElement('INPUT');
			input.setAttribute("autocomplete","off");
			input.setAttribute("size","70");
			input.setAttribute("type","text");
			input.setAttribute("id","beneficiarioOtro");
			input.setAttribute("name","beneficiarioOtro");
			input.setAttribute("value","");
			input.setAttribute("class","normal");
			div.appendChild(input);
			actb(document.getElementById('beneficiarioOtro'),otrosAMostrar);
			document.getElementById('contenedorOtros').style.display='block';
			document.getElementById('contenedorEmpleados').style.display='none';
			document.getElementById('contenedorProveedores').style.display='none';
			document.getElementById('beneficiarioOtro').focus();
		}else if(valor=='4'){
			div = document.getElementById('itemContainerTemp');
			if(div.hasChildNodes()){
			    while(div.childNodes.length >= 1 ){
			    	div.removeChild(div.firstChild);       
			    }
			}
			input = document.createElement('INPUT');
			input.setAttribute("autocomplete","off");
			input.setAttribute("size","70");
			input.setAttribute("type","text");
			input.setAttribute("id","itemCompletarTemp");
			input.setAttribute("name","itemCompletarTemp");
			input.setAttribute("value","");
			input.setAttribute("class","normal");
			div.appendChild(input);
			actb(document.getElementById('itemCompletarTemp'),arregloItemsTemp);  
			document.getElementById('itemContainerTemp').style.display='block';
			document.getElementById('contenedorOtros').style.display='none';
			document.getElementById('contenedorEmpleados').style.display='none';
			document.getElementById('contenedorProveedores').style.display='none';
			document.getElementById('itemCompletarTemp').focus();
		}
	
	}

	function limpiarBeneficiario(tipo){
		if(tipo=='1'){
			document.getElementById("beneficiarioEmpleado").value="";
			document.getElementById("beneficiarioEmpleado").focus();
		}else if(tipo=='2'){
			document.getElementById("beneficiarioProveedor").value="";
			document.getElementById("beneficiarioEmpleado").focus();
		}else if(tipo=='3'){
			document.getElementById("beneficiarioOtro").value="";
			document.getElementById("beneficiarioOtro").focus();
		}
	}

	function estaEnBeneficiarios(tipo, cedula){
		if(tipo=='1'){
			for(j = 0; j < cedulasEmpleados.length; j++){
				if(cedula==cedulasEmpleados[j]){
					return nombresEmpleados[j];
				}
			}
		}else if(tipo=='2'){
			for(j = 0; j < cedulasProveedores.length; j++){
				if(cedula==cedulasProveedores[j]){
					return nombresProveedores[j];
				}
			}
		}else if(tipo=='3'){
			for(j = 0; j < cedulasOtros.length; j++){
				if(cedula==cedulasOtros[j]){
					return nombresOtros[j];
				}
			}
		}
		return "";
	}

	function estaEnBeneficiariosTemporales(cedula){
		for(j = 0; j < beneficiarios.length; j++){
			if(cedula==beneficiarios[j][0]){
				return true;
			}
		}
		return false;
	}
	
	function accionBeneficiario(id, tipo, cedula, nombre, edo, obs){
		if(id==0){
			var registro = new Array(6);			
			registro[0] = cedula;
			registro[1] = nombre;
			if(tipo=='1'){
				registro[2] = "Empleado";
			}else if(tipo=='2'){
				registro[2] = "Proveedor";
			}else if(tipo=='3'){
				registro[2] = "Otro";
			}
			registro[3] = tipo;
			registro[4]=edo;
			registro[5]=obs;
			beneficiarios[beneficiarios.length]=registro;
			
		}
		var tbody = document.getElementById('beneficiariosBody');
		var table = document.getElementById('beneficiariosTable');
		for(i=0;i<beneficiarios.length-1;i++){
			table.deleteRow(1);

			if (beneficiarios[i][4]==""){
			beneficiarios[i][4]=beneficiarios[i+1][4];
			beneficiarios[i][5]=beneficiarios[i+1][5];
			}
		}
		beneficiarios[beneficiarios.length-1][4]="";
		beneficiarios[beneficiarios.length-1][5]="";

		if(id!=0){
			table.deleteRow(1);
			for(i=id;i<beneficiarios.length;i++){
				beneficiarios[i-1]=beneficiarios[i];
			}
			beneficiarios.pop();
		}
		
		for(i=0;i<beneficiarios.length;i++){
	    	var row = document.createElement("tr");
			row.setAttribute("class","normalNegro");
	    	var td0=document.createElement("td");
			td0.setAttribute("align","justify");
			td0.appendChild(document.createTextNode(i+1));
	    	
			var td1=document.createElement("td");
			td1.setAttribute("align","justify");
			td1.appendChild(document.createTextNode(beneficiarios[i][0]));
			
			var td2=document.createElement("td");
			td2.setAttribute("align","justify");
			td2.appendChild(document.createTextNode(beneficiarios[i][1]));
			
			var td3=document.createElement("td");
			td3.setAttribute("align","left");
			td3.appendChild(document.createTextNode(beneficiarios[i][2]));

			var td5 = document.createElement("td");
			td5.setAttribute("align","Center");
			td5.className = 'titularMedio';
			var edo = document.createElement('select');
			name="estado"+i;
			edo.setAttribute("name",name);
			edo.setAttribute("id",name);

			for (h=0; h<listado_estados.length; h++){
			 opt=document.createElement('option');
			 opt.setAttribute("class","normalNegro");
			 opt.value=listado_estados[h][0];
			 nombre=listado_estados[h][1];
			 opt.innerHTML = nombre;
			 //(beneficiarios.length>1)&&
			 if ( (beneficiarios[i][4]==listado_estados[h][0])){
			 opt.setAttribute("selected", "selected")
			 }
			 edo.appendChild(opt); 
			}
			  td5.appendChild(edo);

			 var td6 = document.createElement("td");
			 td6.setAttribute("align","Center");
			 td6.className = 'titularMedio';
			 var obs_extra = document.createElement('INPUT');
			 obs_extra.setAttribute("type","text");
			 name="obs_extra"+i;
			 obs_extra.setAttribute("name",name);
			 obs_extra.setAttribute("id",name);

			// if (beneficiarios.length>1){
				// h=i+1;
			 obs_extra.value=beneficiarios[i][5];
			 //}
			 obs_extra.size='15';
			 obs_extra.className='normalNegro';
			 td6.appendChild(obs_extra);
			 
			
			var td4 = document.createElement("td");
			td4.setAttribute("align","center");
	        td4.className = 'link';
			editLink = document.createElement("a");
			linkText = document.createTextNode("Eliminar");
			editLink.setAttribute("href", "javascript:accionBeneficiario('"+(i+1)+"')");
			editLink.appendChild(linkText);
			td4.appendChild (editLink);

			row.appendChild(td0);
			row.appendChild(td1);
			row.appendChild(td2);
			row.appendChild(td3);
			row.appendChild(td6);
			row.appendChild(td5);
			row.appendChild(td4);
			tbody.appendChild(row);
		}
	}

	function obtenerPrimerBeneficiario(){
		if(beneficiarios.length>0){
			return beneficiarios[0][3];
		}
		return "";
	}

		function limpiarItem(){
			document.getElementById("itemCompletarTemp").value="";
			document.getElementById("itemCompletarTemp").focus();
			document.getElementById("sujeto_temp").value="0";
			document.getElementById("exento_temp").value="0";
			document.getElementById("itemCompletarTemp").value="";
		}

function estaEnItemsTemporales(idItem){
	
	for(j = 0; j < arreglo_partidas.length; j++){
		if(idItem==arreglo_partidas[j][4]){
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
							//	alert("Partida ya seleccionada...");
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
							td1.setAttribute("colspan","2");
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
							 
							//CODIGO DEL PROYECTO O ACCION OCULTA
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
							txt_id_p_ac.size='8'; 
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
							inputNombrePartida.size='25';
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
						
							var temporal=registro[4];
							if ((temporal.substring(0,6)!='4.11.0') && (temporal.substring(0,1)=='4')) {
							
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
	montos_pdas = new Array();
	for(i=0;i<elem;i++)
	{
		if( ((document.getElementById('txt_monto'+i).value=='') || (document.getElementById('txt_monto'+i).value<=0))
		&& ((document.getElementById('txt_monto_exento'+i).value=='') || (document.getElementById('txt_monto_exento'+i).value<=0)))
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
		    alert('Revise los montos ingresados, debe especificar un monto sujeto o exento en alguna partida');
		    return false;
		  }
		  
}

function add_opciones()
{   
		nave=new String(navigator.appName);
		var pos_nave=nave.indexOf("Explorer");
		var index;

		element_otros = document.getElementById('tbl_part').getElementsByTagName('tr').length;
		element_otros = element_otros -1;
		var tbody2 = document.getElementById('tbl_part');
										
		//se agregan ahora los elementos a la tabla inferior
		var tabla = document.getElementById('tbl_mod');
		element_todos = document.getElementById('tbl_mod').getElementsByTagName('tr').length;
		element_todos = element_todos -3;
		
		var tbody = document.getElementById('item');
		var id='item';
		var valido=validar_pri(element_otros);
				  
		if(valido==false){return;}
		
		if(element_otros<1) 
		{
			alert("Este documento no posee partidas asociadas");
			return;
		}
		cg = trim(document.form.txt_cod_imputa2.value);
		cc = trim(document.form.txt_cod_accion2.value);
		
		for(i=0;i<element_otros;i++)
		{
		  //la partida que tenga algun valor <>0 en sujeto o exento, si se pued agregar
		  if ((document.getElementById('txt_monto_exento'+i).value>0)||(document.getElementById('txt_monto'+i).value>0))
			{
			  var numero_pagos=document.getElementById('beneficiariosTable').rows.length-1;
			  var monto_compromiso=parseFloat(MoneyToNumber(document.getElementById('monto_comp'+i).value));
			  var montoexento=parseFloat(MoneyToNumber(document.getElementById('txt_monto_exento'+i).value))*numero_pagos;
			  var montosujeto=parseFloat(MoneyToNumber(document.getElementById('txt_monto'+i).value))*numero_pagos;
			  
			  if (montosujeto+montoexento>monto_compromiso){
				  alert("El monto introducido, no puede ser superior al monto del compromiso");
				  document.getElementById('txt_monto_exento'+i).focus();
				  return;
			  }			
			var registro = new Array(7); 
			registro[0]=document.getElementById('rb_ac_proy'+i).value;//form.txt_cod_imputa.value;
			registro[1]=document.getElementById('txt_id_p_ac'+i).value;//form.txt_cod_imputa.value; 	      
			registro[2]=document.getElementById('txt_id_acesp'+i).value;//form.txt_cod_accion.value; 
			registro[3]=document.form.opt_depe.value; 
			registro[4]=document.getElementById('txt_codigo'+i).value;
			registro[5]=document.getElementById('txt_den'+i).value;
			registro[6]=document.getElementById('txt_monto'+i).value;
			registro[7]=document.getElementById('txt_monto_exento'+i).value;
			var row = document.createElement("tr")
		
			//Verificamos si esta ya registrada
			for(l=0;l<partidas.length;l++)
			{
			 if ((partidas[l][4]==registro[4]) && (partidas[l][1]==registro[1]) && (partidas[l][2]==registro[2]) ) 
			 {
				alert("Partida ya seleccionada...");
				return;
			 }
			}
		    j=partidas.length;
		   //LOS RADIO BUTTONS
			var td1 = document.createElement("td");
			td1.setAttribute("align","Center");
			td1.setAttribute("colspan","2");
			td1.className = 'normalNegro';
			//creamos una radio button
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
					rad_1.setAttribute("readOnly","true");
			  
				//	if(document.form.chk_tp_imputa[0].checked==true)
					if(registro[0]==1)
					{
						//registro[0]=1;
						rad_1.setAttribute("value",1);
						rad_1_text = document.createTextNode('PR');
						rad_1.defaultChecked = true;
					}
					else
						{
							//registro[0]=0;		    
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
			  txt_id_p_ac.value=registro[1];
			  txt_id_p_ac.size='8'; 
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
			  txt_den_pda.size='25'; 
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
			  txt_monto.setAttribute("id",name);
			  txt_monto.setAttribute("readOnly","true");
			  var mon=MoneyToNumber(registro[6]);
              txt_monto.value=mon;	 
			  txt_monto.size='10'; 
			  txt_monto.className='normalNegro';
			  td8.appendChild(txt_monto);
			  
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
			  var mon2=MoneyToNumber(registro[7]);
			  txt_monto_exento.value=mon2;	 
			  txt_monto_exento.size='10'; 
			  txt_monto_exento.className='normalNegro';
			  td9.appendChild(txt_monto_exento);

			  /**************************************/
			   monto_tot[monto_tot.length]= mon;
			   monto_tot_exento[monto_tot_exento.length]= mon2;
			  /***************************************/
			  
			  //OPCION DE ELIMINAR
			  var td10 = document.createElement("td");				
			  td10.setAttribute("align","Center");
			  td10.className = 'normal';
			  editLink = document.createElement("a");
			  linkText = document.createTextNode("Eliminar");
 			  editLink.setAttribute("href", "javascript:elimina_pda('"+(j+1)+"')");
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
			  document.getElementById('txt_monto_exento'+i).value=0.0;
			  document.getElementById('txt_monto'+i).value=0.0;
			}
		  }
			document.getElementById('hid_largo').value=partidas.length;
		
	}

	function agregarBeneficiario(tipo){
		if(tipo=='1'){
			if(trim(document.getElementById("beneficiarioEmpleado").value)==""){
				alert("Introduzca el n"+uACUTE+"mero de c"+eACUTE+"dula o una palabra contenida en el nombre del empleado.");
				document.getElementById("beneficiarioEmpleado").focus();
			}else{
				if(obtenerPrimerBeneficiario()=='2'){
					alert("Ya usted indic"+oACUTE+" un proveedor como beneficiario, no se puede combinar empleados con proveedores en una misma Solicitud de Pago.");
				}else{
					tokens = document.getElementById("beneficiarioEmpleado").value.split( ":" );
					cedula = (tokens[0])?trim(tokens[0]):"";
	
					nombre = estaEnBeneficiarios(tipo, cedula);
					if(nombre!=""){
						esta = estaEnBeneficiariosTemporales(cedula);
						indice=beneficiarios.length;
						if (indice>0){
						 indice=indice-1;
						 edo_beneficiario=document.getElementById('estado'+indice).value;
						 obs_beneficiario=document.getElementById("obs_extra"+indice).value;
						}else{edo_beneficiario="";obs_beneficiario="";}
						if(esta==false){
							accionBeneficiario(0, tipo, cedula, nombre,edo_beneficiario,obs_beneficiario);
						}else{
							alert("El empleado "+nombre+" ya est"+aACUTE+" agregado como beneficiario.");
						}	
					}else{
						alert("La c"+eACUTE+"dula o el nombre indicado no es v"+aACUTE+"lido");
					}
				}
			}
			limpiarBeneficiario(tipo);
		}else if(tipo=='2'){
			if(trim(document.getElementById("beneficiarioProveedor").value)==""){
				alert("Introduzca el RIF o una palabra contenida en el nombre del proveedor.");
				document.getElementById("beneficiarioProveedor").focus();
			}else{
				/*if(obtenerPrimerBeneficiario()=='2'){
					alert("Ya usted indic"+oACUTE+" un proveedor como beneficiario, no se puede agregar varios proveedores como beneficiarios de una misma Solicitud de Pago.");
				}else*/
				 if(obtenerPrimerBeneficiario()=='1' || obtenerPrimerBeneficiario()=='3'){
					alert("Ya usted indic"+oACUTE+" personas naturales como beneficiarios, no se puede combinar personas naturales con proveedores en una misma Solicitud de Pago.");
				}else{				
					tokens = document.getElementById("beneficiarioProveedor").value.split( ":" );
					cedula = (tokens[0])?trim(tokens[0]):"";
	
					nombre = estaEnBeneficiarios(tipo, cedula);
					if(nombre!=""){
						esta = estaEnBeneficiariosTemporales(cedula);
						indice=beneficiarios.length;
						if (indice>0){
						 indice=indice-1;
						 edo_beneficiario=document.getElementById('estado'+indice).value;
						 obs_beneficiario=document.getElementById("obs_extra"+indice).value;
						}else{edo_beneficiario="";obs_beneficiario="";}
						if(esta==false){
							//accionBeneficiario(0, tipo, cedula, nombre);	
							accionBeneficiario(0, tipo, cedula, nombre,edo_beneficiario,obs_beneficiario);
						}else{
							alert("El proveedor "+nombre+" ya est"+aACUTE+" agregado como beneficiario.");
						}
					}else{
						alert("El RIF o nombre indicado no es v"+aACUTE+"lido");
					}
				}
			}
			limpiarBeneficiario(tipo);
		}else if(tipo=='3'){
			if(trim(document.getElementById("beneficiarioOtro").value)==""){
				alert("Introduzca la c"+eACUTE+"dula o una palabra contenida en el nombre de la persona.");
				document.getElementById("beneficiarioOtro").focus();
			}else{
				if(obtenerPrimerBeneficiario()=='2'){
					alert("Ya usted indic"+oACUTE+" un proveedor como beneficiario, no se puede combinar personas del renglon \"Otros\" con proveedores en una misma Solicitud de Pago.");
				}else{			
					tokens = document.getElementById("beneficiarioOtro").value.split( ":" );
					cedula = (tokens[0])?trim(tokens[0]):"";
	
					nombre = estaEnBeneficiarios(tipo, cedula);
					if(nombre!=""){
						esta = estaEnBeneficiariosTemporales(cedula);
						indice=beneficiarios.length;
						if (indice>0){
							 indice=indice-1;
							 edo_beneficiario=document.getElementById('estado'+indice).value;
							 obs_beneficiario=document.getElementById("obs_extra"+indice).value;
							}else{edo_beneficiario="";obs_beneficiario="";}
						if(esta==false){
							//accionBeneficiario(0, tipo, cedula, nombre);
							accionBeneficiario(0, tipo, cedula, nombre,edo_beneficiario,obs_beneficiario);	
						}else{
							alert("La persona "+nombre+" ya est"+aACUTE+" agregado como beneficiario.");
						}
					}else{
						alert("La c"+eACUTE+"dula o el nombre indicado no es v"+aACUTE+"lido");
					}
				}
			}
			limpiarBeneficiario(tipo);
		}
	}


	/************************** Validar Datos *******************************/
	function enviar(){   
		if(document.form.dependencia.value==0){
			alert("Debe seleccionar la Dependencia Solicitante...");
			document.form.dependencia.focus();
			return;
		}

		if(document.form.tipo_sol.value==0){
			alert("Debe seleccionar el Tipo de la Solicitud...");
			document.form.tipo_sol.focus();
			return;
		}

		if(beneficiarios.length==0){
			alert('Debe seleccionar un beneficiario');
			return;
		}
		
		cedulasBeneficiariosCadena = "";
		nombresBeneficiariosCadena = "";
		tiposBeneficiariosCadena = "";
		for(i=0;i<beneficiarios.length;i++){
			cedulasBeneficiariosCadena += beneficiarios[i][0]+",";
			nombresBeneficiariosCadena += beneficiarios[i][1]+",";
			tiposBeneficiariosCadena += beneficiarios[i][3]+",";
		}
		cedulasBeneficiariosCadena = cedulasBeneficiariosCadena.substring(0,cedulasBeneficiariosCadena.length-1);
		nombresBeneficiariosCadena = nombresBeneficiariosCadena.substring(0,nombresBeneficiariosCadena.length-1);
		tiposBeneficiariosCadena = tiposBeneficiariosCadena.substring(0,tiposBeneficiariosCadena.length-1);
		document.getElementById("hid_bene_ci_rif").value=cedulasBeneficiariosCadena;
		document.getElementById("hid_beneficiario").value=nombresBeneficiariosCadena;
		document.getElementById("hid_bene_tp").value=tiposBeneficiariosCadena;
		document.getElementById("hid_contador").value=beneficiarios.length;

		if((document.form.txt_factura.value!="") && (document.form.txt_fecha_factura.value=="")){
			alert("Debe indicar la fecha de la factura.");
			document.form.txt_fecha_factura.focus();
			return;
		}
		
		if(trim(document.form.numero_reserva.value)=="0"){
			alert('Debe especificar la fuente de financiamiento para la solicitud, de no tener colocar N/A');
			document.form.numero_reserva.focus();
			return;
		}
			
		if(document.form.comp_id.value==0){
			alert("Debe indicar el n"+uACUTE+"mero del compromiso asociado a la solicitud de pago.");
			document.form.comp_id.focus();
			return;
		}


		for(i=0;i<partidas.length;i++){
			if ((document.form.comp_id.value=='N/A') && (validar_compromiso[i]==1)){
				alert("Debe indicar el n"+uACUTE+"mero del compromiso asociado a la solicitud de pago, no puede ser N/A ya que contiene partidas que no son temporales.");
				document.form.comp_id.focus();
				return;
			}
		}


		if(trim(document.form.txt_detalle.value)==""){
			alert('Debe especificar el Motivo del Pago');
			document.form.txt_detalle.focus();
			return;
		}
		
		if((document.form.txt_cod_imputa.value=="") && (document.form.txt_cod_accion.value=="")){
			alert('Debe seleccionar la categor'+iACUTE+'a para la cual desea hacer la imputaci'+oACUTE+'n');
			return;
		}
			
		if((document.form.hid_largo.value<1) || (partidas=="")){
			alert("Este documento no posee partidas asociadas");
			return;
		}
		
		document.form.hid_monto_tot.value=MoneyToNumber(document.form.txt_monto_tot.value);
	
        if(document.form.txt_observa.length>220){
			alert("Las observaciones no deben exceder de 220 caracteres");
			return;
		}
		
		if(confirm("Datos introducidos de manera correcta. "+pACUTE+"Est"+aACUTE+" seguro que desea continuar?.")){
			var texto=crear();
			document.form.txt_arreglo_f.value=texto;
			document.form.chk_tp_imputa[0].disabled=false;
			document.form.chk_tp_imputa[1].disabled=false;
			document.form.submit();		
    	}
	}

	function verifica_fechas(fecha){ 
		var op=false;
		var fecha_actual = document.getElementById(fecha.id).value;
		if(fecha_actual.value!=""){
			var arreglo_f_desde = fecha_actual.split("/");
			var desde = new Date(arreglo_f_desde[2]+"/"+arreglo_f_desde[1]+"/"+arreglo_f_desde[0]);
			var hoy = new Date("<?=(date('Y/m/d'))?>");
			if(desde.getTime() > hoy.getTime()){
				alert("La Fecha no Puede ser Mayor a "+ "<?=(date('d/m/Y'))?>");
				document.getElementById(fecha.id).value="";
				return;
			}
		}
	}

	function colocar(){
		document.form.hid_cod_imputa.value=document.form.txt_cod_imputa.value;
	}

	function revisar_doc(id_tipo_documento,id_opcion,objeto_siguiente_id,objeto_siguiente_id_proy,cadena_siguiente_id,cadena_siguiente_id_proy,id_objeto_actual){ 
		document.form.action="accion_ejecutar.php?tipo="+id_tipo_documento+"&accion="+id_objeto_actual+"&accion_sig=" + objeto_siguiente_id + "&hijo="+cadena_siguiente_id+"&opcion="+id_opcion;
		enviar();
	}
	</script>
	
	
	<script language="JavaScript1.2">
var digitos=15 //cantidad de digitos buscados
var puntero=0
var buffer=new Array(digitos) //declaración del array Buffer
var cadena=""

function buscar_op(obj,objfoco){

	var nav4 = window.Event ? true : false;
	var key = nav4 ? obj.which : obj.keyCode;	
	
   var letra = String.fromCharCode(key)
   if(puntero >= digitos){
       cadena="";
       puntero=0;
    }
   //si se presiona la tecla ENTER, borro el array de teclas presionadas y salto a otro objeto...

	   if (key == 13){
       borrar_buffer();
       if(objfoco!=0) objfoco.focus(); //evita foco a otro objeto si objfoco=0
    }
   //sino busco la cadena tipeada dentro del combo...
   else{
       buffer[puntero]=letra;
       //guardo en la posicion puntero la letra tipeada
       cadena=cadena+buffer[puntero]; //armo una cadena con los datos que van ingresando al array
       puntero++;

       //barro todas las opciones que contiene el combo y las comparo la cadena...
       for (var opcombo=0;opcombo < obj.length;opcombo++){
          if(obj[opcombo].text.substr(0,puntero).toLowerCase()==cadena.toLowerCase()){
          obj.selectedIndex=opcombo;
          }
       }
    }
   
	   obj.returnValue = false; //invalida la acción de pulsado de tecla para evitar busqueda del primer caracter
}

function borrar_buffer(){
   //inicializa la cadena buscada
    cadena="";
    puntero=0;
}
</script> 

	<link rel="stylesheet" href="css/plantilla.css" type="text/css"	media="all" />
</head>
<body>
	<form name="form" method="post" action="" enctype="multipart/form-data"	id="form1">
		<input type="hidden" name="hid_monto_letras" id="hid_monto_letras">
		<input type="hidden" name="hid_tp_imp" id="hid_tp_imp" value="">
		<input type="hidden" name="hid_nombre_imp" value="">
		<input type="hidden" name="hid_monto_tot" value="<?= $or_orde_total_general?>">
		<table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		  <tr class="td_gray"> 
	<td colspan="3" class="normalNegroNegrita">.:Registrar solicitud de pago:.</td>
  </tr>
			<tr>
				<td height="223">
					<table width="100%">
						<tr>
							<td height="21" colspan="3" valign="middle" class="td_gray">
								<span class="normalNegroNegrita"><strong>DATOS DEL SOLICITANTE</strong></span>
							</td>
						</tr>
						<tr>
							<td height="28" colspan="2">
								<div  class="normalNegrita">Solicitante:</div>
							</td>
							<td width="465" class="normalNegro"><?= $_SESSION['solicitante']?></td>
						</tr>
						<tr>
							<td height="28" colspan="2">
								<div  class="normalNegrita">C&eacute;dula de identidad:</div>
							</td>
							<td class="normalNegro"><?= $_SESSION['cedula']?></td>
						</tr>
						<tr>
							<td height="28" colspan="2">
								<div  class="normalNegrita">Email:</div>
							</td>
							<td class="normalNegro"><?= $_SESSION['email']?></td>
						</tr>
						<tr>
							<td height="28" colspan="2">
								<div  class="normalNegrita">Cargo:</div>
							</td>
							<td class="normalNegro"><?= $_SESSION['cargo']?></td>
						</tr>
						<tr>
							<td height="30" colspan="2">
								<div class="normalNegrita">Dependencia solicitante:</div>
							</td>
							<td>
							<?php
								$sql_str="SELECT depe_id,depe_nombrecort,depe_nombre FROM sai_dependenci WHERE depe_nivel='4' or depe_nivel='3' order by depe_nombre";
								$res_q=pg_exec($sql_str);
							?>
								<select name="dependencia" class="normalNegro" id="dependencia">
									<option value="0" selected="selected">--</option>
									<?php
										while($depe_row=pg_fetch_array($res_q)){
									?>
										<option value="<?=(trim($depe_row['depe_id']))?>"><?=(trim($depe_row['depe_nombre']))?></option>
									<?php
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td height="30" colspan="2">
								<div class="normalNegrita">Tipo de solicitud:</div>
							</td>
							<td>
								<?php
									$sql_str="SELECT * FROM  sai_seleccionar_campo('sai_tipo_solicitud','id_sol,nombre_sol','esta_id=1','nombre_sol',1) resultado_set(id_sol int4,nombre_sol varchar)";
									$res_q=pg_exec($sql_str);
								?>
								<select name="tipo_sol" class="normalNegro" id="tipo_sol">
									<option value="0" selected="selected">--</option>
									<?php
										while($depe_row=pg_fetch_array($res_q)){
									?>
										<option value="<?= (trim($depe_row['id_sol']))?>"><?=(trim($depe_row['nombre_sol']))?></option>
									<?php
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td height="30" colspan="2">
								<div  class="normalNegrita">Tel&eacute;fono de oficina:</div>
							</td>
							<td class="normalNegro"><?= $_SESSION['tlf_ofic']?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table width="100%">
						<tr class="td_gray">
							<td height="21" colspan="3" valign="middle" class="td_gray">
								<div align="left" class="normalNegroNegrita">DATOS DEL BENEFICIARIO</div>
							</td>
						</tr>
						<tr>
							<td colspan="3">
							 	<table class="normal">
							 		<tr>
							 			<td width="120">
							 				<input name="opt_bene" type="radio" value="" onClick="javascript:mostrarBeneficiarios(1)">
							 				Empleado
							 			</td>
							 			<td width="120">
						 					<input name="opt_bene" type="radio" value="" onClick="javascript:mostrarBeneficiarios(2)">
											Proveedor
							 			</td>
							 			<td width="120">
						 					<input name="opt_bene" type="radio" value="" onClick="javascript:mostrarBeneficiarios(3)">
											Otro
										</td>
							 		</tr>
							 	</table>
							 </td>
						</tr>
						<tr>
							<td colspan="3">
								<div id="contenedorEmpleados" style="display: none;">
									<div id="empleadoInputContainer" style="width: 504px;float: left;">
										<input autocomplete="off" size="70" type="text" id="beneficiarioEmpleado" name="beneficiarioEmpleado" value="" class="normalNegro"/>
									</div>
									<div style="float: left;">
										<input type="button" value="Agregar" onclick="javascript:agregarBeneficiario('1');" class="normalNegrita"/>
									</div>
									<div style="width: 500px">
										<br/>&nbsp;<span class="normalNegrita">(*)</span><span class="normalNegro">Introduzca el n&uacute;mero de c&eacute;dula o una palabra contenida en el nombre del empleado.</span>
									</div>
									<?php
										$query = 	"SELECT * from sai_empleado where esta_id=1";
										$resultado = pg_exec($conexion, $query);
										$numeroFilas = pg_num_rows($resultado);
										$arregloEmpleados = "";
										$cedulasEmpleados = "";
										$nombresEmpleados = "";
										while($row=pg_fetch_array($resultado)){
											$arregloEmpleados .= "'".$row["empl_cedula"]." : ".strtoupper(str_replace("\n"," ",$row["empl_nombres"]." ".$row["empl_apellidos"]))."',";
											$cedulasEmpleados .= "'".$row["empl_cedula"]."',";
											$nombresEmpleados .= "'".str_replace("\n"," ",strtoupper($row["empl_nombres"]." ".$row["empl_apellidos"]))."',";
										}
										$arregloEmpleados = substr($arregloEmpleados, 0, -1);
										$cedulasEmpleados = substr($cedulasEmpleados, 0, -1);
										$nombresEmpleados = substr($nombresEmpleados, 0, -1);
									?>
									<script>
										var cedulasEmpleados = new Array(<?= $cedulasEmpleados?>);
										var nombresEmpleados = new Array(<?= $nombresEmpleados?>);
										var empleadosAMostrar = new Array(<?= $arregloEmpleados?>);
									</script>
								</div>
								<div id="contenedorProveedores" style="display: none;">
									<div id="proveedorInputContainer" style="width: 504px;float: left;">
										<input autocomplete="off" size="70" type="text" id="beneficiarioProveedor" name="beneficiarioProveedor" value="" class="normalNegro"/>
									</div>
									<div style="float: left;">
										<input type="button" value="Agregar" onclick="javascript:agregarBeneficiario('2');" class="normal"/>
									</div>
									<div style="width: 500px">
										<br/>&nbsp;<span class="normalNegrita">(*)</span><span class="normalNegro">Introduzca el RIF o una palabra contenida en el nombre del proveedor</span>
									</div>
									<?php
										$query = 	"SELECT prov_id_rif,prov_nombre ".
													"FROM ".
														"sai_proveedor_nuevo ".
													"WHERE ".
														"prov_esta_id=1 ".
													"ORDER BY prov_nombre";
										$resultado = pg_exec($conexion, $query);
										$numeroFilas = pg_num_rows($resultado);
										$arregloProveedores = "";
										$cedulasProveedores = "";
										$nombresProveedores = "";
										while($row=pg_fetch_array($resultado)){
											$arregloProveedores .= "'".$row["prov_id_rif"]." : ".strtoupper(str_replace("\n"," ",$row["prov_nombre"]))."',";
											$cedulasProveedores .= "'".$row["prov_id_rif"]."',";
											$nombresProveedores .= "'".str_replace("\n"," ",strtoupper($row["prov_nombre"]))."',";
										}
										$arregloProveedores = substr($arregloProveedores, 0, -1);
										$cedulasProveedores = substr($cedulasProveedores, 0, -1);
										$nombresProveedores = substr($nombresProveedores, 0, -1);
									?>
									<script>
										var cedulasProveedores = new Array(<?= $cedulasProveedores?>);
										var nombresProveedores = new Array(<?= $nombresProveedores?>);
										var proveedoresAMostrar = new Array(<?= $arregloProveedores?>);
									</script>
								</div>
								<div id="contenedorOtros" style="display: none;">
									<div id="otroInputContainer" style="width: 504px;float: left;">
										<input autocomplete="off" size="70" type="text" id="beneficiarioOtro" name="beneficiarioOtro" value="" class="normalNegro"/>
									</div>
									<div style="float: left;">
										<input type="button" value="Agregar" onclick="javascript:agregarBeneficiario('3');" class="normal"/>
									</div>
									<div style="width: 500px">
										<br/>&nbsp;<span class="normalNegrita">(*)</span><span class="normalNegro">Introduzca la c&eacute;dula o una palabra contenida en el nombre de la persona.</span>
									</div>
									<?php
										$query = 	"SELECT * from sai_viat_benef where benvi_esta_id=1";
										$resultado = pg_exec($conexion, $query);
										$numeroFilas = pg_num_rows($resultado);
										$arregloOtros = "";
										$cedulasOtros = "";
										$nombresOtros = "";
										while($row=pg_fetch_array($resultado)){
											$arregloOtros .= "'".$row["benvi_cedula"]." : ".strtoupper(str_replace("\n"," ",$row["benvi_nombres"]." ".$row["benvi_apellidos"]))."',";
											$cedulasOtros .= "'".$row["benvi_cedula"]."',";
											$nombresOtros .= "'".str_replace("\n"," ",strtoupper($row["benvi_nombres"]." ".$row["benvi_apellidos"]))."',";
										}
										$arregloOtros = substr($arregloOtros, 0, -1);
										$cedulasOtros = substr($cedulasOtros, 0, -1);
										$nombresOtros = substr($nombresOtros, 0, -1);
									?>
									<script>
										var cedulasOtros = new Array(<?= $cedulasOtros?>);
										var nombresOtros = new Array(<?= $nombresOtros?>);
										var otrosAMostrar = new Array(<?= $arregloOtros?>);
									</script>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<br/>
								<table id="beneficiariosTable">
									<tr valign="middle">
										<td width="20" class="normalNegrita">
											N&deg;
										</td>
										<td width="60" class="normalNegrita">
											C&eacute;dula/RIF
										</td>
										<td width="400" class="normalNegrita">
											Nombre
										</td>
										<td width="60" class="normalNegrita">
											<div align="center">Tipo</div>
										</td>
										<td width="60" class="normalNegrita">
											Observaci&oacute;n
										</td>
										<td width="60" class="normalNegrita">
											Estado
										</td>										
										<td width="60" class="normalNegrita">
											<div align="center">Opci&oacute;n</div>
										</td>
									</tr>
									<tbody id="beneficiariosBody" class="normal">
									</tbody>
								</table>
							</td>
						</tr>
					</table>
					<table>
						<tr>
							<td><p>&nbsp;</p></td>
						</tr>
					</table>
					<input type="hidden" id="hid_bene_tp" name="hid_bene_tp" value="">
					<input type="hidden" id="hid_beneficiario" name="hid_beneficiario" value="">
					<input type="hidden" id="hid_bene_ci_rif" name="hid_bene_ci_rif" value="">
					<input type="hidden" id="hid_contador" name="hid_contador" value="">
	
				</td>
			</tr>
			<tr>
				<td>
					<table width="100%" class="tablaalertas">
						<tr>
							<td height="21" colspan="8" valign="middle" class="td_gray">
								<div align="left" class="normalNegroNegrita">DOCUMENTOS ANEXOS</div>
							</td>
						</tr>
						<tr class="normalNegro">
							<td height="21" >
								<input name="chk_factura" type="checkbox" id="chk_factura"/>
							</td>
							<td height="21" >Factura</td>
							<td height="21" >
								<input name="chk_ordc" type="checkbox" id="chk_ordc"/>
							</td>
							<td height="21">Orden de compra</td>
							<td height="21" >
								<input name="chk_contrato" type="checkbox" id="chk_contrato"/>
							</td>
							<td height="21" >Contrato</td>
							<td height="21" >
								<input name="chk_certificacion"	type="checkbox" id="chk_certificacion"/>
							</td>
							<td height="21" class="peq">Certificaci&oacute;n del control perceptivo</td>
						</tr>
						<tr class="normalNegro">
							<td height="21" >
							<input name="chk_informe" type="checkbox" id="chk_informe"/>
							</td>
							<td height="21" class="normalNegro">Informe o solicitud de pago a cuentas</td>
							<td height="21" >
								<input name="chk_ords" type="checkbox" id="chk_ords"/>
							</td>
							<td height="21" >Orden de servicio</td>
							<td height="21" class="peq">
								<input name="chk_pcta" type="checkbox" id="chk_pcta"/>
							</td>
							<td height="21" >Punto de cuenta</td>
						</tr>
						<tr class="normalNegro">

							<td height="21" >
								<input name="chk_otro" type="checkbox" id="chk_otro" onclick="javascript:act_desact()"/>
							</td>
							<td height="21" >Otro (especifique)</td>
							<td height="21" ></td>
							<td height="21">
								<input type="text" name="txt_otro" id="txt_otro" size="25" maxlength="25" value="" disabled="disabled">
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table width="100%">
						<tr class="td_gray">
							<td height="21" colspan="3" valign="middle" class="td_gray">
								<div align="left" class="normalNegroNegrita">DETALLES DE LA SOLICITUD</div>
							</td>
						</tr>
						<tr>
							<td height="28" valign="middle" colspan="2" class="normalNegrita">
								Factura N&deg;:
							</td>
							<td >
								<input name="txt_factura" type="text" class="normalNegro" id="txt_factura" size="20" maxlength="20" align="right" onkeyup="validar_digito(txt_factura)"/>
								<font class="normalNegrita">Fecha:</font>
								<input type="text" size="10" id="txt_fecha_factura"	name="txt_fecha_factura" class="dateparse" readonly />
								<a href="javascript:void(0);" onclick="g_Calendar.show(event,'txt_fecha_factura');" title="Show popup calendar">
									<img src="js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
								</a>
								<font class="normalNegrita">N&deg; de control:</font>
								<input name="txt_factura_num_control" type="text" class="normalNegro" id="txt_factura_num_control" size="11" maxlength="10" align="right" onkeyup="validar_digito(txt_factura_num_control);"/>
							</td>
						</tr>
						<tr >
							<td height="28" colspan="2" valign="middle" class="normalNegrita">Prioridad:</td>
							<td valign="middle">
								<select name="slc_prioridad" class="normalNegro">
									<option value="3" selected>Alta</option>
								</select>
						<tr class="normal">
							<td height="33" colspan="2" class="normalNegrita">
								N&uacute;mero del compromiso:
							</td>
							<td>
							<?php
								$sql_str="SELECT distinct(docg_id) as comp_id ,docg_fecha FROM sai_doc_genera where esta_id<>15 and docg_id like '".$a_o."' order by docg_fecha";
								$res_q=pg_exec($sql_str);
							?>
								<select name="comp_id" class="normalNegro" id="comp_id" onKeypress="buscar_op(this,text2)" onblur="borrar_buffer()" onclick="borrar_buffer()" onChange="consulta_presupuesto()">
									<option value="0" selected="selected">--</option>
									<option value="N/A">N/A</option>
									<?php
										while($depe_row=pg_fetch_array($res_q)){
										$comp_id=substr($depe_row['comp_id'],5);
									?>
									<option value="<?=(trim($comp_id))?>"><?= (trim($comp_id))?></option>
									<?php
										}
									?>
								</select><input type="hidden"  name="text2"> 
							</td>
						</tr>
												<tr class="normal">
							<td height="33" colspan="2" class="normalNegrita">
								Fuente de financiamiento:
							</td>
							<td>
								<select name="numero_reserva" class="normalNegro">
								<?php 
								$sql_ff="SELECT * FROM  sai_seleccionar_campo('sai_fuente_fin','fuef_id,fuef_descripcion','esta_id<>15','fuef_descripcion',1) resultado_set(fuef_id varchar,fuef_descripcion varchar)";
								$res_ff=pg_exec($sql_ff);
								?>
								 <option value="0">--</option>
								 <option value="N/A">N/A</option>
								<?php while($row_ff=pg_fetch_array($res_ff)){
								?>
								 <option value="<?php echo $row_ff['fuef_id']?>"><?php echo $row_ff['fuef_descripcion']?></option>
								<?php }?>
								</select>
							</td>
						</tr>
						<tr>
							<td height="75" colspan="2">
								<div  class="normalNegrita">Motivo del pago:</div>
							</td>
							<td class="normal">
								<textarea name="txt_detalle" cols="70" rows="3" class="normalNegro" 
								onKeyDown="contador(this.form.txt_detalle,this.form.remLen,60);"
								onKeyUp="contador(this.form.txt_detalle,this.form.remLen,60);"
								"><?=$ordm_motivo?></textarea><input type="text" name="remLen" size="3" maxlength="3" value="60" readonly>
							</td>
						</tr>
						<tr>
							<td height="78" colspan="2">
								<div  class="normalNegrita">Observaci&oacute;n:</div>
							</td>
							<td class="normal">
								<textarea name="txt_observa" cols="70" rows="3"	class="normalNegro" onBlur="javascript:LimitText(this,600)"></textarea>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					
					<input name="hid_val" type="hidden" id="hid_val"/>
					<!--<input type="hidden" name="txt_arreglo_part" value=""/>
					<input type="hidden" name="txt_arreglo_mont" value=""/>-->
					<input type="hidden" name="hid_partida_actual" value=""/>
					<input type="hidden" name="hid_comprobar"/>
				</td>
			</tr>
			<tr>
		<td>
			<table align="center" class="tablaalertas">
				<tr class="td_gray">
					<td>
						<div align="center" class="peqNegrita" id="Categoria"  style="display:none">
							<a href="javascript:verifica_partida();">
								<img src="imagenes/estadistic.gif" width="24" height="24" border="0"/>
								Categor&iacute;a
							</a>
						</div>
					</td>
					<td>
						<div align="center" class="normalNegro">C&oacute;digo</div>
					</td>
					<td>
						<div align="center"><span class="normalNegro">Denominaci&oacute;n</span></div>
					</td>
				</tr>
				<tr>
					<td>
						<div align="left">
							<input name="chk_tp_imputa" type="radio" class="normalNegro" value="1" disabled="disabled">
							<span class="peqNegrita">Proyectos</span>
						</div>
					</td>
					<td rowspan="2">
						<div align="center">
						<input name="txt_cod_imputa" type="hidden" id="txt_cod_imputa" value="" >
		 				<input name="txt_cod_imputa2" type="text" class="normalNegro" id="txt_cod_imputa2" size="15" value="" readonly="readonly">
						</div>
					</td>
					<td rowspan="2">
						<div align="center">
							<input name="txt_nombre_imputa" type="text" class="normalNegro" id="txt_nombre_imputa" size="70" readonly="readonly"	value="">
						</div>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<div align="left">
							<input name="chk_tp_imputa" type="radio" class="normalNegro" value="0" disabled="disabled"/>
							<span class="peqNegrita">Acci&oacute;n Cent.</span>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div align="left">
							<p><span class="peqNegrita">&nbsp;Acci&oacute;n espec&iacute;fica</span></p>
						</div>
					</td>
					<td>
						<div align="center">
							<input name="txt_cod_accion" type="hidden" id="txt_cod_accion">
						    <input name="txt_cod_accion2" type="text" class="normalNegro" id="txt_cod_accion2" size="15" readonly>
						</div>
					</td>
					<td>
						<div align="center">
							<input name="txt_nombre_accion" type="text"	class="normalNegro" id="txt_nombre_accion" size="70" readonly="readonly"/>
						</div>
					</td>
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
						<?php
							while($depe_row=pg_fetch_array($res_q)){
						?>
							<option value="<?= (trim($depe_row['depe_id']))?>"><?= (trim($depe_row['depe_nombrecort']))?></option>
						<?php 
							}
						?>
						</select>
					</td>
					<td class="peqNegrita" align="left">
						Centro Gestor:
						<input type="text" name="centro_gestor" id="centro_gestor" size="5"	readonly="readonly" class="normalNegro"/>
						&nbsp;&nbsp;Centro Costo:
						<input type="text" name="centro_costo" id="centro_costo" size="5" readonly="readonly" class="normalNegro">
					</td>
				</tr>
			</table>
		</td>
	</tr>
			<tr>
				<td colspan="2">
					<br/><br/>
					<input type="hidden" name="hid_largo" id="hid_largo"/>
					<input type="hidden" name="hid_val" id="hid_val"/>
	   <div id="PartidasTemporales" style="display:none">
					<table align="center" width="700px" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
					  <tr>
							<td colspan="2" align="center">
							<input name="opt_bene" type="radio" value="" onClick="javascript:mostrarBeneficiarios(4)">
							&nbsp;<font  class="normalNegrita">Cuentas contables</font><font class="peq_naranja">(*)</font> 
								<div id="itemContainerTemp" style="width: 560px;float: center;">
								<input autocomplete="off" size="70" type="text" id="itemCompletarTemp" name="itemCompletarTemp" value="" class="normalNegro"/></div>
								<div style="width: 700px; float: left;text-align: left;margin-top: -10px;" class="normalNegro">
								<br/><div align="center"><span class="peq_naranja">(*)</span>Introduzca la cuenta contable o una palabra contenida en el nombre.</div>
								</div><br></div>					
								<?php
								
								$query_partidas_temp="SELECT t2.part_id, t2.part_nombre,t1.cpat_id,cpat_nombre 
								FROM sai_convertidor t1,  sai_partida t2,sai_cue_pat t3 
								WHERE t1.part_id=t2.part_id and t2.pres_anno='".$pres_anno."'
								 and t2.part_id like '4.11.0%' and t1.cpat_id=t3.cpat_id order by 3";
								
								/*	$query_partidas_temp = 	"SELECT ".
													"part_id, ".
													"part_nombre ".
												"FROM sai_partida  ".
												"WHERE ".
													"pres_anno='".$pres_anno."' and part_id not like '%.00.%' and part_id like '4.11.0%'".
												"ORDER BY part_id, part_nombre ";*/
								
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
						

						<tr><td class="normal" width="200px" align="center"><b>Monto sujeto:</b><input type="text" id="sujeto_temp" name="sujeto_temp" size="20" class="normalNegro" value="0"  onkeypress="return inputFloat(event,true);"/></td>
						    <td class="normal" width="200px" align="center"><b>Monto exento:</b><input type="text" id="exento_temp" name="exento_temp" class="normalNegro" size="20" value="0"  onkeypress="return inputFloat(event,true);"></input>
						    <input type="button" value="Agregar" onclick="javascript:agregarItem(itemCompletarTemp,sujeto_temp,exento_temp,idsPartidasItemsTemp,idsCtasItems),add_monto(),calcular_iva();" class="normal"/>
						    </td></tr>
						    </table>
						   </div>
						   
						   
						   
						   <br>
						   <div id="PartidasAutomaticas" style="display: none">
						   <table align="center" width="700px" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" id="tbl_part">
						   <tr>
						    <td class="normal" align="center"><b></b></td>
							<td class="normal" align="center"><b>Partida</b></td>
							<td class="normal" align="center"><b>Nombre partida</b></td>
							<td class="normal" align="center"><b>Proy/ACC</b></td>
							<td class="normal" align="center"><b>Acc. Esp.</b></td>
							<td class="normal" align="center"><b>Monto compromiso</b></td>
							<td class="normal" align="center"><b>Monto sujeto</b></td>
							<td class="normal" align="center"><b>Monto exento</b></td>
							</tr>
							 <tbody id="item2"></tbody>
							</table>
							</div>
				</td>	
			</tr>
		<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>
			 <div align="center" id="Boton" style="display: none">
			    <input type="button" value="Confirmar" onclick="javascript:add_opciones(),add_monto(),calcular_iva()">
				
			</div>
		</td>
	</tr>
	<tr>
		<td height="133">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tablaalertas" id="tbl_mod">
				<!--DWLayoutTable-->
				<tr class="td_gray">
					<td class="peqNegrita" colspan="2">Proyecto o Acci&oacute;n	Centralizada</td>
					<td>
						<div align="center" class="peqNegrita">&nbsp;&nbsp;ACC.C/P.P</div>
					</td>
					<td>
						<div align="center" class="peqNegrita">&nbsp;&nbsp;ACC.ESP</div>
					</td>
					<td>
						<div align="center" class="peqNegrita">Dependencia</div>
					</td>
					<td>
						<div align="center" class="peqNegrita">Partida/Cuenta contable</div>
					</td>
					<td>
						<div align="center" class="peqNegrita">Denominaci&oacute;n</div>
					</td>
					<td>
						<div align="center" class="peqNegrita">&nbsp;&nbsp;Monto sujeto</div>
					</td>
					<td>
						<div align="center" class="peqNegrita">&nbsp;&nbsp;&nbsp;&nbsp;Monto exento</div>
					</td>
					<td>
						<div align="center" class="peqNegrita">&nbsp;&nbsp;Acci&oacute;n</div>
					</td>
				</tr>
				<tbody id="item"></tbody>
				<tr>
					<td height="19" colspan="5">&nbsp;</td>
					<td width="57">&nbsp;</td>
					<td width="52"><!--DWLayoutEmptyCell-->&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td height="21" colspan="10" valign="middle" class="td_gray">
						<div align="left" class="normalNegroNegrita">
							<div align="center">DETALLE DEL IMPUESTO AL VALOR AGREGADO (IVA)</div>
						</div>
					</td>
				</tr>
				<tr>
				<td height="24" colspan="3" align="center" valign="top"	class="normalNegrita">
					Monto base:
					<input name="txt_monto_subtotal" type="text" class="normalNegro" id="txt_monto_subtotal" value="0.00" size="20" maxlength="20" readonly="readonly" align="right">
				</td>
				<td height="24" colspan="3" align="center" valign="top"	class="normalNegrita">
					Monto exento:
					<input name="txt_monto_subtotal_exento" type="text" class="normalNegro" id="txt_monto_subtotal_exento" value="0.00" size="20" maxlength="20" readonly="readonly" align="right">
				</td>
				<td align="left" valign="top" class="normalNegrita">
					&nbsp;&nbsp;Porcentaje:
					<span class="normal">
					<select name="opc_por_iva" id="opc_por_iva"	class="normalNegro" onChange="javacript:calcular_iva()">
						<?php
						for ($ii=0; $ii <$elem_impuesto; $ii++){
						?>
							<option value="<?= $porce_impuesto[$ii]?>" <?php if($porce_impuesto[$ii]==0){echo "selected";}?> title="<?php echo $porce_impuesto[$ii]."% ". $impu_nombre[$ii];?>"><?= $porce_impuesto[$ii]?></option>
						<?php
						}
						?>
					</select>
					</span>
				</td>
				<td colspan="3" align="center" valign="top" class="normalNegrita">
					<div align="left">
						Monto IVA:
						<input name="txt_monto_iva_tt" type="text" class="normalNegro" id="txt_monto_iva_tt" value="0.00" size="25" maxlength="25" readonly="readonly" align="right">
					</div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="21" colspan="9" valign="middle" class="td_gray">
			<div align="left" class="normalNegroNegrita">TOTAL A PAGAR</div>
		</td>
	</tr>
	<tr class="normal">
		<td class="normal" align="left">
			En Bs.
			<input type="text" name="txt_monto_tot" id="txt_monto_tot" value="<?= (number_format($total_definitivo,2,'.',','))?>" size="15" readonly="readonly" class="normalNegro"/>
		</td>
	</tr>
	<tr class="normal">
		<td class="normal" align="left">
			En letras:
			<textarea name="txt_monto_letras" id="txt_monto_letras" rows="2" cols="70" class="normalNegro" readonly><?= $total_definitivo?></textarea>
		</td>
	</tr>
	<script language="JavaScript" type="text/JavaScript" id="js1x1">
	    ver_monto_letra(<?= str_replace(",","", $total_definitivo)?>, 'txt_monto_letras','');
	    ver_monto_letra(<?= str_replace(",","", $total_definitivo)?>,'hid_monto_letras','');
	</script>
	<tr>
		<td height="18" colspan="3">
		<table width="420" align="center">
			<?php
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
				include("documentos/opciones_1.php");
			?>
		</td>
	</tr>
</table>
</form>
</body>
</html>
<?php pg_close($conexion);?>
