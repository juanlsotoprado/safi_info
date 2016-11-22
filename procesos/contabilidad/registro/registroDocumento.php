
<?php 
	ob_start();
	require_once("../../../includes/conexion.php");
	if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
		header('Location:../../../index.php',false);
		ob_end_flush();
		exit;
	}
	ob_end_flush();	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Registro Documento</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<link href="../../../css/safi0.2.css" rel="stylesheet" type="text/css" />
<link href="../../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd-mm-yyyy');</script>

<script language="JavaScript" src="../../../js/lib/actb.js"></script>
<script language="JavaScript" src="../../../js/funciones.js"> </script>
<script type="text/javascript" src="../../../js/lib/jquery/plugins/jquery.min.js"></script>
<script type="text/javascript" src="../../../js/lib/jquery/plugins/ui.min.js"></script>
<script language="JavaScript" src="../../../js/registrarDocumento.js"></script>
<script language="JavaScript">
beneficiarios = new Array();


function estaEnBeneficiarios(cedula){
	for(j = 0; j < arreglo_rif.length; j++){
		if(cedula==arreglo_rif[j]){
			return nombre_proveedor[j];
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

function accionBeneficiario(id, cedula, nombre){
	if(id==0){
		var registro = new Array(6);			
		registro[0] = cedula;
		registro[1] = nombre;
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
		
		var td3 = document.createElement("td");
		td3.setAttribute("align","center");
        td3.className = 'link';
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href", "javascript:accionBeneficiario('"+(i+1)+"')");
		editLink.appendChild(linkText);
		td3.appendChild (editLink);

		row.appendChild(td0);
		row.appendChild(td1);
		row.appendChild(td2);
		row.appendChild(td3);
		tbody.appendChild(row);
	}
}

function limpiarBeneficiario(tipo){
	document.getElementById("beneficiario").value="";
	document.getElementById("beneficiario").focus();
}

function agregarBeneficiario(){
	if(trim(document.getElementById("beneficiario").value)==""){
		alert("Introduzca el n"+uACUTE+"mero de C"+eACUTE+"dula/RIF o una palabra contenida en el nombre del beneficiario");
		document.getElementById("beneficiario").focus();
	}else{
		tokens = document.getElementById("beneficiario").value.split( ":" );
		cedula = (tokens[0])?trim(tokens[0]):"";

		nombre = estaEnBeneficiarios(cedula);
		if(nombre!=""){
			esta = estaEnBeneficiariosTemporales(cedula);
			indice=beneficiarios.length;
			if (indice>0){
				indice=indice-1;
			}
			if(esta==false){
				accionBeneficiario(0, cedula, nombre);
			}else{
				alert("El beneficiario "+nombre+" ya est"+aACUTE+" agregado");
			}	
		}else{
			alert("La C"+eACUTE+"dula/RIF o el nombre indicado no es v"+aACUTE+"lido");
		}
	}
	limpiarBeneficiario();
}
</script>
</head>
<body>
<br/>
<form name="form1" method="post" action="registroDocumentoAccion.php" id="form1" enctype="multipart/form-data" >
<table align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
		<td colspan="2" class="normalNegroNegrita">Registro de documentos</td>
	</tr>
<tr>
	<td><div align="left" class="normal"><b>Elaborado Por:</b></div></td>
	<td class="normalNegro"><?php echo $_SESSION['solicitante'];?></td>
</tr>
<tr>
<td class="normal" align="left"><strong>Dependencia:</strong></td>
<td>
	<?php
	    $sql_str="SELECT depe_id,
	    				depe_nombrecort,
	    				depe_nombre
	    				FROM sai_dependenci
	    				WHERE depe_nivel = '4' or depe_nivel='3' 
	    				ORDER BY depe_nombre";
	    $res_q=pg_exec($sql_str);		  
	?>
	<!-- onChange="buscarDocumento(document.form1.tipoDocumento, this.value, document.form1.idDocumento);"> -->
        <select name="dependencia" class="normalNegro" id="dependencia" onchange="buscarDocumento(document.form1.tipoDocumento.value, this.value, document.form1.idDocumento.value);">
    	    <option value="-1">Seleccione</option>
    	 <!--      <option value="-0">Seleccione...</option>-->
	    	<?php while($depe_row=pg_fetch_array($res_q)){ ?>
             <option value="<?php echo(trim($depe_row['depe_id'])); ?>"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
        	<?php }?>
        </select>		   
</td>
</tr>
<tr>
	<td class="normal" align="left"><strong>Tipo de documento: </strong></td>
	<td class="normalNegro">
		<select name="tipoDocumento" id="tipoDocumento" class="normalNegro" onchange="javascript:buscarDocumento(this.value, document.form1.dependencia.value, document.form1.idDocumento.value);" >
			<option value="-1">Seleccione</option>
			<option value="pcta">Punto de cuenta (pcta)</option>
			<option value="rqui">Requisici&oacute;n (rqui)</option>			
			<option value="memr">Memorando (memr)</option>
			<option value="vnac">Vi&aacute;tico Nacional (vnac)</option>
			<option value="rvna">Rendici&oacute;n Vi&aacute;tico Nacional (rvna)</option>			
			<option value="avan">Avance (avan)</option>
			<option value="rava">Rendici&oacute;n Avance (rava)</option>			
			<option value="ordc">Orden de compra (ordc)</option>
			<option value="otro">Otro</option>
		</select>
	</td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>C&oacute;digo Documento:</b></div></td>
	<td width="80%" class="normalNegro">
		<input id="idDocumento" class="normalNegro" size="50" maxlength="200" name="idDocumento"/>
		<div style="width: 340px; color: red; float: right;margin-top: 4px;" id="errorAsunto"></div>
	</td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>Fecha Documento:</b></div></td>
	<td width="80%" class="normalNegro">
	
								<input type="text" size="10" id="fechaDocumento" name="fechaDocumento" class="dateparse" readonly="readonly"/>
							<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaDocumento');" title="Show popup calendar">
								<img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
							</a>
	
	<!-- 	<input id="fechaDocumento" class="normalNegro" size="50" maxlength="200" name="fechaDocumento"/> -->
	</td>
</tr>
<tr>
	<td><div align="left" class="normal"><strong>Beneficiario:</strong></div></td>
	<td><input type="text" name="beneficiario" id="beneficiario" class="normalNegro" size="70"/>
		<?php 	
		$query = 	"SELECT prov_id_rif AS id,
							prov_nombre AS nombre 
					FROM sai_proveedor_nuevo 
					WHERE prov_esta_id=1
					UNION
					SELECT benvi_cedula AS id,
						 benvi_nombres || ' ' || benvi_apellidos AS nombre
					FROM
						sai_viat_benef 
					UNION
					SELECT empl_cedula AS id,
						empl_nombres || ' ' || empl_apellidos AS nombre
					FROM sai_empleado
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
					actb(document.getElementById('beneficiario'),proveedor);
				</script>
			
	<a href="javascript:agregarBeneficiario();">Agregar</a></td></tr>
	<tr>
	<td colspan="2">
		<table id="beneficiariosTable">
			<tr valign="middle">
				<td width="20" class="normalNegrita">N&deg;	</td>
				<td width="60" class="normalNegrita">C&eacute;dula/RIF</td>
				<td width="400" class="normalNegrita">Nombre</td>
				<td width="60" class="normalNegrita"><div align="center">Opci&oacute;n</div></td>
			</tr>
			<tbody id="beneficiariosBody" class="normal">
			</tbody>
		</table>	

		<input type="hidden" id="hid_beneficiario" name="hid_beneficiario" value=""/>
		<input type="hidden" id="hid_bene_ci_rif" name="hid_bene_ci_rif" value=""/>
		<input type="hidden" id="hid_contador" name="hid_contador" value=""/>		
	</td>
	</tr>
<tr>
   	<td><div align="left" class="normal"><b>Monto:</b></div></td>
	<td><input type="text" name="monto" class="normalNegro" value="" id="monto"/></td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>Nro Compromiso:</b></div></td>
	<td><input type="text" name="compromiso" class="normalNegro" value="comp-400" id="compromiso"/></td>
</tr>	
<tr>
   	<td><div align="left" class="normal"><b>Observaciones:</b></div></td>
	<td><textarea cols="80" rows="2" name="observaciones" id="observaciones"></textarea> </td>
</tr>	
<tr>
   	<td><div align="left" class="normal"><b>Firmas:</b></div></td>
	<td class="normalNegro"><input type="checkbox" name="firma_de" id="firma_de"/>Director de Administraci&oacute;n<input type="checkbox" name="firma_presidencia" id="firma_presidencia"/>Presidencia</td>
</tr>
	<tr align="center">
		<td colspan="2" class="normal" align="center"><br></br>
		<input type="button" value="Registrar" onclick="javascript:revisar();" /></td>
</tr>	
</table>
</form>
</body>
</html>