<?php
ob_start();
session_start();
require_once("../../../includes/conexion.php");
require_once("../../../includes/perfiles/constantesPerfiles.php");
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../../../index.php',false);
	ob_end_flush();
	exit;
}	ob_end_flush();
 
//Login del usuario
$usuario = $_SESSION['login'];
//Perfil del usuario
$user_perfil_id = $_SESSION['user_perfil_id'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Actas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script languaje="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
<script type="text/javascript" 	src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" 	src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">	g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script>
var codis=new Array();
var codisIndice = -1;

function verificarCheckboxControl(){
	inputs = document.getElementsByTagName("input");
	todosMarcados = true;
	totalCheckboxs = 0;
	for(var i = 0; i < inputs.length; i++) {
		if(inputs[i].getAttribute("type")=="checkbox"){
			totalCheckboxs++;
			if(	strStartsWith(inputs[i].getAttribute("name"),"codis")==true
				&& inputs[i].checked == false){
				todosMarcados = false;
			}
		}
	}
	if(totalCheckboxs>1){
		checkboxControl = document.getElementById("controlCodis");
		checkboxControl.checked = todosMarcados;
	}
}

function marcarTodosNinguno(){
	checkbox = document.getElementById("controlCodis");
	inputs = document.getElementsByTagName("input");
	for(var i = 0; i < inputs.length; i++) {
		if(inputs[i].getAttribute("type")=="checkbox"
			&& strStartsWith(inputs[i].getAttribute("name"),"codis")==true){
			inputs[i].checked = checkbox.checked;
			agregarQuitarCodi(inputs[i]);
		}
	}
	verificarCheckboxControl();
}

function agregarQuitarCodi(elemento, manual){
	if(elemento.checked==true){
		if(existeCodi(elemento.value+"")==-1){
			codisIndice++;
			codis[codisIndice] = new String(elemento.value+'');
		}
	}else{
		codis[existeCodi(elemento.value+"")] = null;
		codisIndice--;
	}
	if(manual && manual==true){
		verificarCheckboxControl();
	}
}

function existeCodi(codi){
	i = 0;
	while(i<codis.length){
		if(codis[i]==codi){
			return i;	
		}
		i++;
	}
	return -1;
}

function imprimir(){
	cadenaCodis = "";
	for(i=0; i<codis.length; i++){
		if(codis[i]!=null){
			cadenaCodis += codis[i]+",";
		}
	}
	if(cadenaCodis!=""){
		cadenaCodis = cadenaCodis.substring(0,cadenaCodis.length - 1);
		//location.href="salida_varios_pdf.php?codis="+cadenaCodis;
		location.href="salida_multiple_pdf.php?codis="+cadenaCodis;
		return;
	}else{
		alert("Debe marcar al menos un (1) acta para imprimir.");
		return;
	}
}

function detalle(codigo)
{
    url="anula_1.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}
function deshabilitar_combo(valor)
{
 if (valor=='1') 
 { 
   document.form.txt_inicio.disabled=false;
   document.form.hid_hasta_itin.disabled=false;
   document.form.txt_cod.value="";
   document.form.txt_cod.disabled=true;
   document.form.tipo_acta.disabled=false;
   }

 if (valor=='3') 
 { 
   document.form.txt_inicio.disabled=true;
   document.form.hid_hasta_itin.disabled=true;
   document.form.txt_inicio.value="";
   document.form.hid_hasta_itin.value="";
   document.form.txt_cod.disabled=false;
   document.form.tipo_acta.disabled=true;
 }

}
function comparar_fechas(fecha_inicial,fecha_final) //Formato dd/mm/yyyy
{ 	
	var fecha_inicial=document.form.txt_inicio.value;
	var fecha_final=document.form.hid_hasta_itin.value;
		
	var dia1 =fecha_inicial.substring(0,2);
	var mes1 =fecha_inicial.substring(3,5);
	var anio1=fecha_inicial.substring(6,10);
	
	var dia2 =fecha_final.substring(0,2);
	var mes2 =fecha_final.substring(3,5);
	var anio2=fecha_final.substring(6,10);

	dia1 = parseInt(dia1,10);
	mes1 = parseInt(mes1,10);
	anio1= parseInt(anio1,10);

	dia2 = parseInt(dia2,10);
	mes2 = parseInt(mes2,10);
	anio2= parseInt(anio2,10); 
		
	if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || 
	 ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) )
	{
	  alert("La fecha inicial no debe ser mayor a la fecha final"); 
	  document.form.hid_hasta_itin.value='';
	  return;
	}
}

</script>
<script language="javascript">
function ejecutar_varios(codigo,codigo1,codigo2,codigo3)
{ 
	if ((document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value=='') 
			&& (document.form.txt_cod.value=='')  && (document.form.tipo_acta.value=='0') )	 
   {
   	 document.form.hid_validar.value=1;
   }
   else {document.form.hid_validar.value=2;
  
   }
   document.form.submit();
 //window.location="actas.php?&codigo="+codigo+"&txt_inicio="+codigo1+"&hid_hasta_itin="+codigo2+"&tipo_acta="+codigo3

 
}

function anular(codigo)
{ 
	document.form.action='anular_acta_entrada.php?codigo='+codigo;
   document.form.submit();
 //window.location="actas.php?&codigo="+codigo+"&txt_inicio="+codigo1+"&hid_hasta_itin="+codigo2+"&tipo_acta="+codigo3

 
}
</script>

</head>
<body>
	<form name="form" action="actas.php" method="post">
		<div align="center">
			<input type="hidden" value="0" name="hid_validar" /> <br />
		</div>
		<table width="600" align="center"
			background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td height="21" colspan="4" class="normalNegroNegrita" align="left">Buscar</td>
			</tr>
			<tr>
				<td height="10" colspan="3"></td>
			</tr>
			<tr>
				<td width="20" align="center"><input name="opt_fecha" type="radio"
					value="1" onClick="javascript:deshabilitar_combo(1)" class="normal" />
				</td>
				<td width="175" height="29" class="normalNegrita" align="left">Elaborados
					entre:</td>
				<td width="304" class="normalNegrita" colspan="2"><input type="text"
					size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
					onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?php echo date('d/m/Y')?>"/>
					<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar"> <img
						src="../../../js/lib/calendarPopup/img/calendar.gif"
						class="cp_img" alt="Open popup calendar" /> </a> <input
					type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin"
					class="dateparse" onfocus="javascript: comparar_fechas(this);"
					readonly="readonly" value="<?php echo date('d/m/Y')?>"/> <a href="javascript:void(0);"
					onclick="g_Calendar.show(event, 'hid_hasta_itin');"
					title="Show popup calendar"> <img
						src="../../../js/lib/calendarPopup/img/calendar.gif"
						class="cp_img" alt="Open popup calendar" /> </a>
				</td>
			</tr>
			<tr>
				<td height="30" align="center" class="normal"><input
					name="opt_fecha" type="radio" value="3" class="normal"
					onClick="javascript:deshabilitar_combo(3)" />
				</td>
				<td class="normalNegrita" align="left">C&oacute;digo del documento:
				</td>
				<td><span class="normalNegrita"> <input name="txt_cod" type="text"
						class="peq" id="txt_cod" value="" size="10" disabled="disabled" />
				</span></td>
			</tr>
			<tr>
				<td height="30" align="center" class="normal"></td>
				<td class="normalNegrita" align="left">Tipo de Acta:</td>
				<td><span class="normalNegrita"> <select name="tipo_acta" class="normalNegro">
							<option value="0" selected>[Seleccione]</option>
							<option value="1">Asignaci&oacute;n</option>
							<option value="2">Custodia</option>
							<option value="3">Entrada</option>
							<option value="4">Salida</option>
					</select> </span></td>
			</tr>
			<tr>
				<td height="10" colspan="3"></td>
			</tr>
			<tr>
				<td colspan="3"><div align="center">

						<input type="button" class="normalNegro" value="Buscar"
							onclick="javascript:ejecutar_varios(document.form.txt_cod.value,document.form.txt_inicio.value,document.form.hid_hasta_itin.value,document.form.tipo_acta.value)">
					
					</div></td>
			</tr>
		</table>
	</form>
	<br> <?php 
	if ($_POST['hid_validar']==1)
	{
		echo "<SCRIPT LANGUAGE='JavaScript'>"."alert ('Debe especificar al menos un criterio de b\u00FAsqueda');"."</SCRIPT>";
	}

	if ( ( (($_POST['txt_inicio'])=='') and (($_POST['hid_hasta_itin'])!='') ) or ( (($_POST['txt_inicio'])!='') and (($_POST['hid_hasta_itin'])=='') ) )
	{
		echo "<SCRIPT LANGUAGE='JavaScript'>";
		echo "alert ('Debe especificar el rango de fechas a buscar');"."</SCRIPT>";
	}

	if ( (($_POST['txt_inicio'])!='') and (($_POST['hid_hasta_itin'])!='') and ($_POST['tipo_acta']==0) )
	{

		$fecha_in=trim($_POST['txt_inicio']);
		$fecha_fi=trim($_POST['hid_hasta_itin']);
		$fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
		$fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";

		$condicion_acta=trim($_POST['cond_acta']);
		$edo=" ";
		$from="";
		$cond1="";
	/*	if ($condicion_acta>0){

			$cond1="  ";
			if ($condicion_acta==1)
			$edo=" and t1.esta_id=13";

			if ($condicion_acta==2)
			$edo=" and t1.esta_id=7";

			$sql_or="
				SELECT to_char(asbi_fecha,'DD/MM/YYYY') as fecha_acta,asbi_id as acta,revi_fecha,infocentro as id_info,
				  	case infocentro when null then '' else 
				  	(select nombre from safi_infocentro where nemotecnico=infocentro)  end as info,bubica_nombre as id_info,t1.esta_id 
			    FROM sai_bien_asbi t1,sai_revisiones_doc t2,sai_bien_ubicacion
			    WHERE t2.revi_doc=asbi_id  and revi_fecha >= '".$fecha_ini."' and revi_fecha <= '".$fecha_fin."'".$edo. 
			    " and bubica_id=ubicacion
				 order by revi_fecha desc";

		}else{*/
		

		$sql_or="SELECT to_char(fecha_acta,'DD/MM/YYYY') as fecha_acta,acta_id as acta,
		case ubicacion when 1 then 'Galp&oacute;n' else 'Torre' end as id_info,'' as info,esta_id
		FROM sai_bien_custodia
		WHERE fecha_acta >= '".$fecha_ini."' and fecha_acta <= '".$fecha_fin."'
		UNION 
		SELECT to_char(asbi_fecha,'DD/MM/YYYY') as fecha_acta,asbi_id as acta,infocentro as id_info,
		case infocentro when '' then bubica_nombre else 
		(select nombre from safi_infocentro where nemotecnico=infocentro)  end as info,t1.esta_id 
		FROM sai_bien_asbi t1,sai_bien_ubicacion".$from."
		WHERE ".$cond1.$edo." asbi_fecha >= '".$fecha_ini."' and asbi_fecha <= '".$fecha_fin."' and bubica_id=ubicacion
			   UNION
			   	SELECT to_char(fecha_registro,'DD/MM/YYYY') as fecha_acta,acta_id as acta,'' as id_info,'' as info,esta_id
			   	 FROM sai_bien_inco
			   	 WHERE  fecha_registro >= '".$fecha_ini."' and fecha_registro <= '".$fecha_fin."'";
		
		//echo $sql_or;
		$resultado_set_most_or=pg_query($conexion,$sql_or) or die("Error al consultar");
		if(($rowor=pg_fetch_array($resultado_set_most_or))==null)
		{
			echo "<center><font color='#FF0000' class='titular'>"."Actualmente no existen actas generadas en este periodo"."</font></center>";
		}
		else
		{

			$ano1=substr($fecha_ini,0,4);
			$mes1=substr($fecha_ini,5,2);
			$dia1=substr($fecha_ini,8,2);

			$ano2=substr($fecha_fin,0,4);
			$mes2=substr($fecha_fin,5,2);
			$dia2=substr($fecha_fin,8,2);
			?>
		<div class="normalNegroNegrita" align="center">
			Acta(s) elaboradas en el rango de fecha:
			<?php echo $dia1."-".$mes1."-".$ano1;?>
			y
			<?php echo $dia2."-".$mes2."-".$ano2;?>
		</div>
		<table width="502" align="center" background="../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
			    <td width="10" class="normalNegroNegrita" align="center">#</td>
				<td width="137" class="normalNegroNegrita" align="center">C&oacute;digo</td>
				<td width="128" class="normalNegroNegrita" align="center">Fecha</td>
				<td width="128" class="normalNegroNegrita" align="center">Estado del Acta</td>
				<td width="128" class="normalNegroNegrita" align="center">Destino de los Activos</td>
				<?php  if ($condicion_acta<>0){ ?>
				<td width="128" class="normalNegroNegrita" align="center">Fecha de Aprobaci&oacute;n</td>
					<?php }?>
				<td width="115" class="normalNegroNegrita" align="center">Tipo</td>
				<td width="102" class="normalNegroNegrita" align="center">Opciones</td>
			</tr>

			<?php
			$i=0;

			$resultado_set_most_pa=pg_query($conexion,$sql_or);
			while($rowpa=pg_fetch_array($resultado_set_most_pa))
			{
			
			$query_estado="Select esta_nombre from sai_estado where esta_id='".$rowpa['esta_id']."'";
			$resultado_estado=pg_query($conexion,$query_estado);
			if ($row_estado=pg_fetch_array($resultado_estado)){
				$nombre_estado=$row_estado['esta_nombre'];
			}
				
			if (strlen($rowpa['id_info'])<2) 
			 $nombre_destino=$rowpa['info'];
			else 
			 $nombre_destino=$rowpa['id_info'].":".$rowpa['info'];
				if (substr($rowpa['acta'],0,1)=="c")
				{ $i=$i+1;	
				//$nombre_destino="Torre";	?>
			<tr class="normal">
			<td align="center"><span class="peq"><?php echo $i;?></span></td>
				<td height="28" align="center"><span class="link"><a href="javascript:abrir_ventana('../custodia_pdf.php?codigo=<?php echo trim($rowpa['acta']); ?>')" class="copyright"><?php echo $rowpa['acta'];?></a></span></td>
				<td align="center"><span class="peq"><?php echo $rowpa['fecha_acta'];?></span></td>
				<td align="left"><span class="peq"><?php echo $nombre_estado;?></span></td>
				<td align="left"><span class="peq"><?php echo $nombre_destino;?></span></td>
				<td align="left" class="normal"><div align="center"><span class="peq"><?php echo "Custodia";?> </span></div></td>
				<td align="left" class="normal"><div align="center">
					<span class="peqNegrita">
						<a href="javascript:abrir_ventana('../custodia_pdf.php?codigo=<?php echo trim($rowpa['acta']); ?>')"
							class="copyright"><?php echo "Ver detalle"; ?>
						</a>
					</span> <br />
					
					</div> <?php
						if (
							$_SESSION['user_perfil_id'] != PERFIL_ANALISTA_I_PASANTE_BIENES &&
							( $_SESSION['user_perfil_id'] ==PERFIL_ALMACENISTA) && ($rowpa['esta_id']<>15)
						){
					?>
					<div align="center">
						<span class="peqNegrita"><a
							href="../anular_custodia.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=a&opcion=a"
							class="copyright"><?php echo "Anular"; ?> </a> </span><br>
					
					</div> <?php }?>
				</td>
			</tr>
			<?php }else{
				if (substr($rowpa['acta'],0,1)=="e")
				{	$i=$i+1;	?>
			<tr class="normal">
			    <td align="center"><span class="peq"><?php echo $i;?></span></td>
				<td height="28" align="center"><span class="link">
				<a href="javascript:abrir_ventana('../inco_pdf.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=i')" class="copyright"><?php echo $rowpa['acta'];?></a>
				</span>
				</td>
				<td align="center"><span class="peq"><?php echo $rowpa['fecha_acta'];?></span></td>
				<td align="left"><span class="peq"><?php echo $nombre_estado;?></span></td>
				<td align="left"><span class="peq"><?php echo $nombre_destino;?></span></td>
				<td align="left" class="normal"><div align="center"><span class="peq"><?php echo "Incorporaci&oacute;n";?> </span></div></td>
				<td align="left" class="normal">
				<div align="center">
				<span class="peqNegrita"> 
				<a href="javascript:abrir_ventana('../inco_pdf.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=i')" class="copyright"><?php echo "Ver detalle"; ?> </a>
				</span><br></div></td>
			</tr>
			<?php
				}else{
				$i=$i+1;?>
			<tr class="normal">
			    <td align="center"><span class="peq"><?php echo $i;?></span></td>
				<td height="28" align="center"><span class="link"><a
				href="javascript:abrir_ventana('../salida_activos_pdf.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=a')"
				class="copyright"><?php echo $rowpa['acta'];?></a></span></td>
				<td align="center"><span class="peq"><?php echo $rowpa['fecha_acta'];?></span></td>
				<td align="left"><span class="peq"><?php echo $nombre_estado;?></span></td>
				<td align="left"><span class="peq"><?php echo $nombre_destino;?></span></td>
				<?php if ($condicion_acta<>0){ ?>
				<td align="center"><span class="peq"><?php echo substr($rowpa['revi_fecha'],0,19);?></span></td>
				<?php }?>
				<td align="left" class="normal"><div align="center"><span class="peq"><?php echo "Asignaci&oacute;n";?> </span></div></td>
				<td align="left" class="normal"><div align="center"><span class="peqNegrita"> <a
				href="javascript:abrir_ventana('../salida_activos_pdf.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=a')"
				class="copyright"><?php echo "Ver detalle"; ?> </a> </span></div></td>
			</tr>

			<tr class="normal">
			    <td align="center"><span class="peq"><?php echo $i;?></span></td>
				<td height="28" align="center"><span class="link"><a href="javascript:abrir_ventana('../salida_activos_pdf.php?codigo=<?= trim($rowpa['acta']); ?>&tipo=s')"
					class="copyright"><?php echo "s".substr($rowpa['acta'],1);?></a></span></td>
				<td align="center"><span class="peq"><?php echo $rowpa['fecha_acta'];?></span></td>
				<td align="left"><span class="peq"><?php echo $nombre_estado;?></span></td>
				<td align="left"><span class="peq"><?php echo $nombre_destino;?></span></td>
				<?php if ($condicion_acta<>0){ ?>
				<td align="center"><span class="peq"><?php echo substr($rowpa['revi_fecha'],0,19);?></span></td>
				<?php }?>

				<td align="left" class="normal"><div align="center"><span class="peq"><?php echo "Salida";?> </span></div></td>
				<td align="left" class="normal"><div align="center"><span class="peqNegrita"> 
				  <a href="javascript:abrir_ventana('../salida_activos_pdf.php?codigo=<?= trim($rowpa['acta']); ?>&tipo=s')"
					class="copyright"><?php echo "Ver detalle"; ?> </a><br> <?php 
					
					if (($rowpa['esta_id']<>15) && ($_SESSION['user_perfil_id'] <> PERFIL_ALMACENISTA) && ($rowpa['esta_id']==12)){?>
					<a href="../modificar_salida_activos.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=a&accion=Modificar"
					   class="copyright"><?php echo "Agregar"; ?> </a><?php }
//&& ($rowpa['esta_id']<>9)
					if (($_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_BIENES  || $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_BIENES) && ($rowpa['esta_id']<>15) ){?>
					<a href="../modificar_salida_activos.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=a&opcion=a&accion=Anular"
					  class="copyright"><?php echo "Anular"; ?> </a><?php }?></span></div></td>
			</tr>
			<?php }
			}//fin del while que obtiene los datos de la consulta

			}	//fin del else que comprueba que si se tiene resultados para mostrar
			?>
		</table> <?php	
		}//fin del if que evalua el isset del seleccionar fecha
	}

	if (($_POST['txt_cod'])!='')
	{
		$cod=$_POST['txt_cod'];
		if (substr($cod,0,1)=="c")
		{
			$sql_acta="SELECT to_char(fecha_acta,'DD/MM/YYYY') as fecha_acta,acta_id as acta,case ubicacion when 1 then 'Galp&oacute;n' else 'Torre' end as id_info,'' as info,esta_id
			        FROM sai_bien_custodia
			        WHERE acta_id='".$cod."'";
			$pagina="custodia";
			$tipo="c";
		}else{
			if (substr($cod,0,1)=="e")
			{
				$sql_acta="SELECT to_char(fecha_registro,'DD/MM/YYYY') as fecha_acta,acta_id as acta,'' as id_info,'' as info,esta_id,proveedor
			   		 FROM sai_bien_inco
			   		 WHERE acta_id='".$cod."'"; 	
				$pagina="inco";
				$tipo="e";
			}
			if ((substr($cod,0,1)=="a") || (substr($cod,0,1)=="s") ) {
				if (substr($cod,0,1)=="s"){
					$cod="a".substr($cod,1);
					$tipo="s";
					$modificar=1;
				}
				else{
					$tipo="a";
				}
					
				$sql_acta="SELECT to_char(asbi_fecha,'DD/MM/YYYY') as fecha_acta,asbi_id as acta,infocentro as id_info,
				  	case infocentro when '' then bubica_nombre else 
				  	(select nombre from safi_infocentro where nemotecnico=infocentro)  end as info,t1.esta_id
			    	FROM sai_bien_asbi t1,sai_bien_ubicacion
			   		WHERE asbi_id='".$cod."' and bubica_id=ubicacion";
				$pagina="salida_activos";
			}
		}

		//$resultado_acta=pg_query($conexion,$sql_acta) or die("Error al consultar");
		
		if($sql_acta == ""){
			echo "Tipo de documento desconocido.";
			exit;
		} else { 
		
			$resultado_acta = pg_query($conexion, $sql_acta);
			
			if($resultado_acta === false){
				echo "Error al consultar.";
				error_log("Error al realizar la consulta de búsqueda de entradas/salidas de bienes. Detalles: " . pg_last_error());
				exit;
			}
		}
		
		
		if(($rowor=pg_fetch_array($resultado_acta))==null)
		{
			echo "<center><font color='#FF0000' class='titular'>"."Ud. no ha generado un acta con el codigo ingresado
					 </font></center>" ?> <?php }
			else
			{
				?>

		<table width="502" align="center"
			background="../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
			    <td width="10" class="normalNegroNegrita" align="center">#</td>
				<td width="137" class="normalNegroNegrita" align="center">C&oacute;digo</td>
				<td width="128" class="normalNegroNegrita" align="center">Fecha</td>
				<td width="128" class="normalNegroNegrita" align="center">Estado del Acta</td>
				<?php if (substr($cod,0,1)=="e"){?>
				<td width="128" class="normalNegroNegrita" align="center">Proveedor al cual se adquiri&oacute; el activo</td>
				<?php }else{?>
				<td width="128" class="normalNegroNegrita" align="center">Destino de los Activos</td>
				<?php }?>
				<td width="102" class="normalNegroNegrita" align="center">Opciones</td>
			</tr>
			<?php
			$i=0;
			$resultado_acta=pg_query($conexion,$sql_acta);
			while($rowpa=pg_fetch_array($resultado_acta))
			{$i++;
			
			$query_estado="Select esta_nombre from sai_estado where esta_id='".$rowpa['esta_id']."'";
			$resultado_estado=pg_query($conexion,$query_estado);
			if ($row_estado=pg_fetch_array($resultado_estado)){
				$nombre_estado=$row_estado['esta_nombre'];
			}
			if (strlen($rowpa['id_info'])<2) 
			 $nombre_destino=$rowpa['info'];
			else 
			 $nombre_destino=$rowpa['id_info'].":".$rowpa['info'];
				?>
			<tr class="normal">
			<td align="center"><span class="peq"><?php echo $i;?></span></td>
				<td height="28" align="center"><span class="link"><a
				href="javascript:abrir_ventana('../<?=$pagina?>_pdf.php?codigo=<?php echo trim($cod); ?>&tipo=<? echo $tipo;?>')"
				class="copyright"><?php echo $tipo.substr($rowpa['acta'],1);?></a>
				</span></td>
				<td align="center" class="normal"><?php echo $rowpa['fecha_acta'];?></td>
				<td align="left" class="normal"><?php echo $nombre_estado;?></td>
				<?php if (substr($cod,0,1)=="e"){
					$nombreproveedor="--";
					$query_proveedor="Select prov_nombre From sai_proveedor_nuevo where prov_id_rif='".$rowpa['proveedor']."'";
					$resultado_proveedor=pg_query($conexion,$query_proveedor);
					if($rowpro=pg_fetch_array($resultado_proveedor)){
						$nombreproveedor=$rowpa['proveedor'].":".$rowpro['prov_nombre'];
					}
					?>
				<td align="left"><span class="peq"><?php echo $nombreproveedor;?>
				</span></td>
				<?php }else{?>
				<td align="left"><span class="peq"><?php echo $nombre_destino;?>
				</span></td>
				<?php }?>
				<td align="left" class="normal">
				<div align="center"><span class="peqNegrita"> 
				<a
				href="javascript:abrir_ventana('../<?=$pagina?>_pdf.php?codigo=<?php echo trim($cod); ?>&tipo=<? echo $tipo;?>')"
				class="normal">Ver detalle</a>
				<?php if (substr($cod,0,1)=="e"){?>
				<a 	href="javascript:anular('<?php echo trim($cod); ?>')" class="normal">Anular</a>
				<?php }?>
				
				 
				<?php if (($modificar==1) && ($rowpa['esta_id']<>15) && ($_SESSION['user_perfil_id'] <> PERFIL_ALMACENISTA) && ($rowpa['esta_id']==12)){?>
					<br/><a href="../modificar_salida_activos.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=a&accion=Modificar"
								class="copyright">
								<?php echo "Agregar"; ?> </a> <?php }
								if ((substr($cod,0,1)=="c") && ( $_SESSION['user_perfil_id'] ==PERFIL_ALMACENISTA) && ($rowpa['esta_id']<>15)){?>
								<div align="center">
									<span class="peqNegrita"><a
										href="../anular_custodia.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=a&opcion=a"
										class="copyright"><?php echo "Anular"; ?> </a> </span><br>
								
								</div> <?php }

									if ((($tipo==a)||($tipo==s)) && ($_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_BIENES) && ($rowpa['esta_id']<>15)){?>
									<a
									href="../modificar_salida_activos.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=a&opcion=a&accion=Anular"
									class="copyright"><?php echo "Anular"; ?> </a><?php }
										
									?>
						
						</span>
					</div></td>
			</tr>
			<?php	 }//fin del while que obtiene los datos de la consulta
			?>
		</table> <?php
			}	//fin del else que comprueba que si se tiene resultados para mostrar
	}//fin de if que evalua el isset del seleccionar el estado
	if ($_POST['tipo_acta']>0) {?>
		<table width="501" border="0" align="center">
			<tr>
			<?php
			$condicion1=" ";
			$condicion2=" ";
			$condicion3=" ";
			if ( (($_POST['txt_inicio'])!='') and (($_POST['hid_hasta_itin'])!='') ){
				
			 $fecha_in=trim($_POST['txt_inicio']);
			 $fecha_fi=trim($_POST['hid_hasta_itin']);
		     $fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
			 $fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
			//FROM sai_bien_asbi			
			 $condicion1=" and asbi_fecha >= '".$fecha_ini."' and asbi_fecha <= '".$fecha_fin."' ";
			 $condicion2=" WHERE fecha_acta >= '".$fecha_ini."' and fecha_acta <= '".$fecha_fin."' ";
			 //FROM sai_bien_custodia
			 
			 $condicion3=" WHERE fecha_registro >= '".$fecha_ini."' and fecha_registro <= '".$fecha_fin."'";
			   
			   
//			    FROM sai_bien_inco
	
			}   	 
			if($_POST['tipo_acta']=="1"){
				$titulo="Asignaci&oacute;n";
				$consulta="SELECT to_char(asbi_fecha,'DD/MM/YYYY') as fecha_acta,asbi_id as acta,infocentro as id_info,
				  	   case infocentro when '' then bubica_nombre else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info, t1.esta_id
			    				  FROM sai_bien_asbi t1,sai_bien_ubicacion
			    				  WHERE bubica_id=ubicacion".$condicion1."
			   					  ORDER BY asbi_fecha";
				$ruta="salida_activos_pdf.php";
				$tipo="a";
				$modificar=1;
				$lote=0;//$lote=1;
			}
			if($_POST['tipo_acta']=='2'){
				$titulo="Custodia";
				$consulta="SELECT to_char(fecha_acta,'DD/MM/YYYY') as fecha_acta,acta_id as acta,case ubicacion when 1 then 'Galp&oacute;n' else 'Torre' end as id_info,'' as info,esta_id
			   					  FROM sai_bien_custodia".$condicion2."
			   					  ORDER BY fecha_acta";
				$ruta="custodia_pdf";
				$tipo="c";
				$modificar=0;
				$lote=0;
			}
			if($_POST['tipo_acta']=='3'){
				$titulo="Incorporaci&oacute;n";
				$consulta="SELECT to_char(fecha_registro,'DD/MM/YYYY') as fecha_acta,acta_id as acta,'' as id_info,'' as info,esta_id,proveedor
			   					  FROM sai_bien_inco".$condicion3."
			   					  ORDER BY fecha_registro";
				$ruta="inco_pdf";
				$tipo="i";
				$modificar=0;
				$lote=0;
			}
			if($_POST['tipo_acta']=='4'){
				$titulo="Salida";
				$consulta="SELECT to_char(asbi_fecha,'DD/MM/YYYY') as fecha_acta,asbi_id as acta,infocentro as id_info,
				  	   case infocentro when '' then bubica_nombre else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info,t1.esta_id
			    				  FROM sai_bien_asbi t1,sai_bien_ubicacion
			    				  WHERE bubica_id=ubicacion".$condicion1."
			   					  ORDER BY asbi_fecha";
				$ruta="salida_activos_pdf.php";
				$tipo="s";
				$lote=0;//$lote=1;
				$modificar=1;
			}

			?>
				<td width="495" height="27" class="normalNegroNegrita"><div
						align="center">
						Actas de
						<?php echo $titulo;?>
						(elaboradas hasta el  <?=date('d/m/Y')?>)
					</div></td>
					<?php if ($lote==1){?>
				<td align="center"><a
					title="Generar archivo en formato pdf con las actas seleccionadas"
					href="javascript: imprimir();"> <img border="0"
						src="../../../imagenes/pdf_ico.jpg" /> </a>
				</td>
				<?php }?>
			</tr>
		</table>
		<table width="502" align="center"
			background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
			<?php if ($lote==1){?>
				<td align="center" width="50"><input type="checkbox"
					id="controlCodis" name="controlCodis"
					onclick="marcarTodosNinguno();" />
				</td>
				<?php }?>
				<td width="10" class="normalNegroNegrita" align="center">#</td>
				<td width="137" class="normalNegroNegrita" align="center">C&oacute;digo</td>
				<td width="128" class="normalNegroNegrita" align="center">Fecha</td>
				<td width="128" class="normalNegroNegrita" align="center">Estado del Acta</td>
				<?php  if($_POST['tipo_acta']=='3'){?>
				<td width="128" class="normalNegroNegrita" align="center">Proveedor al cual se adquiri&oacute; el activo</td>
				<?php }else{?>
				<td width="128" class="normalNegroNegrita" align="center">Destino de los Activos</td>
				<?php }?>
				<td width="102" class="normalNegroNegrita" align="center">Opciones</td>
			</tr>
			<?php
		    $i=0;
			$resultado_consulta=pg_query($conexion,$consulta);
			while($rowpa=pg_fetch_array($resultado_consulta))
			{ $i++;
			
			$query_estado="Select esta_nombre from sai_estado where esta_id='".$rowpa['esta_id']."'";
			$resultado_estado=pg_query($conexion,$query_estado);
			if ($row_estado=pg_fetch_array($resultado_estado)){
				$nombre_estado=$row_estado['esta_nombre'];
			}
			
			if (strlen($rowpa['id_info'])<2) 
			 $nombre_destino=$rowpa['info'];
			else 
			 $nombre_destino=$rowpa['id_info'].":".$rowpa['info'];?>
			<tr class="normal">
			<td align="center"><span class="peq"><?php echo $i;?></span></td>
			<?php if ($lote==1){?>
				<td align="center" width="50"><input type="checkbox"
					id="codis<?= $rowpa['acta']?>" name="codis<?= $rowpa['acta']?>"
					onclick='agregarQuitarCodi(this, true);'
					value="<?= $rowpa['acta']?>" />
				</td>
				<?php }?>
				<td height="28" align="center"><span class="link"> <a
							href="javascript:abrir_ventana('../<?php echo $ruta;?>?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=<?php echo $tipo;?>')"
							class="copyright"><?php 
				if ($tipo=="s"){echo "s".substr($rowpa['acta'],1);}
				else{echo $rowpa['acta'];}?></a>
				</span></td>
				<td align="center"><span class="peq"><?php echo $rowpa['fecha_acta'];?></span></td>
				<td align="left"><span class="peq"><?php echo $nombre_estado;?></span></td>
				
				<?php  if($_POST['tipo_acta']=='3'){
					$nombreproveedor="--";
					$query_proveedor="Select prov_nombre From sai_proveedor_nuevo where prov_id_rif='".$rowpa['proveedor']."'";
					$resultado_proveedor=pg_query($conexion,$query_proveedor);
					if($rowpro=pg_fetch_array($resultado_proveedor)){
						$nombreproveedor=$rowpa['proveedor'].":".$rowpro['prov_nombre'];
					}
					?>
				<td align="left"><span class="peq"><?php echo $nombreproveedor;?>
				</span></td>
				<?php }else{?>
				<td align="left"><span class="peq"><?php echo $nombre_destino;?>
				</span></td>
				<?php }?>

				<td align="left" class="normal">
					<div align="center">
						<span class="peqNegrita"> <a
							href="javascript:abrir_ventana('../<?php echo $ruta;?>?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=<?php echo $tipo;?>')"
							class="copyright"><?php echo "Ver detalle"; ?> </a> 
							<?php if (($modificar==1) && ($rowpa['esta_id']<>15) && ($_SESSION['user_perfil_id'] <> PERFIL_ALMACENISTA) && ($rowpa['esta_id']==12)){?>
							<br><a
								href="../modificar_salida_activos.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=a&accion=Modificar"
								class="copyright"><?php echo "Agregar"; ?> </a> <?php }
									if (($_POST['tipo_acta']=='2') && ( $_SESSION['user_perfil_id'] ==PERFIL_ALMACENISTA ) && ($rowpa['esta_id']<>15)){?>
									<div align="center">
										<span class="peqNegrita"><a
											href="../anular_custodia.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=a&opcion=a"
											class="copyright"><?php echo "Anular"; ?> </a> </span><br>
									
									</div> <?php }

						   if ((($tipo=="a") ||($tipo=="s")) && ($modificar==1) && ($_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_BIENES) && ($rowpa['esta_id']<>15)){?>
									<a
									href="../modificar_salida_activos.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=a&opcion=a&accion=Anular"
									class="copyright"><?php echo "Anular"; ?> </a><?php }
										
									?>
					
						</span>
					</div></td>
			</tr>

			<?php	 }//fin del while que obtiene los datos de la consulta

	}	//fin del else que comprueba que si se tiene resultados para mostrar
	?>
		</table>

</body>
</html>
	<?php pg_close($conexion);?>

