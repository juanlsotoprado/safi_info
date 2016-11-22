<?php 
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
  require_once("../../includes/fechas.php");
  require_once("../../includes/perfiles/constantesPerfiles.php");
	 
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush(); 	
   exit;
  }
  
  ob_end_flush(); 
	  
  $usuario = $_SESSION['login'];
  $permiso=0;
  $user_perfil_id = substr($_SESSION['user_perfil_id'],2,3);
  if (($user_perfil_id==DEPENDENCIA_OFICINA_DE_GESTION_ADMINISTRATIVA_Y_FINANCIERA) || ($user_perfil_id==DEPENDENCIA_OFICINA_DE_PLANIFICACION_PRESUPUESTO_Y_CONTROL)  || ($user_perfil_id==DEPENDENCIA_OFICINA_DE_GESTION_ADMINISTRATIVA_Y_FINANCIERA_CONTABILIDAD)  || ($user_perfil_id==DEPENDENCIA_DIRECCION_EJECUTIVA)  || ($user_perfil_id==DEPENDENCIA_PRESIDENCIA)){
	$permiso=1;
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Documentos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
	g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<script>
function detalle(codigo)
{
    url="detalle.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}

function ejecutar_varios(codigo1,codigo2,codigo3,codigo4,codigo5,codigo6,codigo7,codigo8,codigo9,codigo10) { 

	if ((codigo1=='') && (codigo2=='') && (codigo3=='') && (codigo4=='') && (codigo5=='') && (codigo6=='') && (codigo7=='') && (codigo8=='') && (codigo9=='') && (codigo10==''))
	  {
		alert ('Debe seleccionar alg\u00FAn criterio de b\u00FAsqueda');
		 
	  }else{
		  if ( ((codigo1=='') && (codigo2!='')) || ((codigo1!='') && (codigo2=='')))  {
			  alert ('Debe especificar el rango de fechas a buscar');
		  }
	      else {
		        document.form.hid_validar.value=2;
		        document.form.action="sopg_buscar.php";
	  	        document.form.submit();
	      }
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

function validar(codigo1,codigo2,codigo3,codigo4,codigo5,codigo6,codigo7,codigo8,codigo9){
	if ((codigo1=='') && (codigo2=='') && (codigo3=='') && (codigo4=='') && (codigo5=='') && (codigo6=='') && (codigo7=='') && (codigo8=='') && (codigo9==''))
	  {
		alert ('Debe seleccionar alg\u00FAn criterio de b\u00FAsqueda');
		 
	  }else{
		  if ( ((codigo1=='') && (codigo2!='')) || ((codigo1!='') && (codigo2=='')))  {
			  alert ('Debe especificar el rango de fechas a buscar');
		  }
	      else {
		        document.form.hid_validar.value=2;
		        document.form.action="busqueda_solicitudes_pagoXLS.php";
	  	        document.form.submit();
	      }
	  }
}

</script>
</head>
<body>
<form name="form" action="sopg_buscar.php" method="post">
 <div align="center">
 <input type="hidden" value="0" name="hid_validar" />
 <br /> <?php
			$sql_perf_tmp="SELECT * FROM sai_buscar_cargo_depen('".$user_perfil_id."') as carg_nombre ";
			$resultado_perf_tmp=pg_query($conexion,$sql_perf_tmp) or die("Error al mostrar");
			$row_perf_tmp=pg_fetch_array($resultado_perf_tmp);
		 ?>
  <br /></div>
<table width="515" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
    <td height="21" colspan="4" class="normalNegroNegrita" align="left">B&uacute;squeda de solicitudes de pago Criterios M&uacute;ltiples</td>
  </tr>
  <tr>
    <td height="10" colspan="3"></td>
  </tr>
  <tr>
	<td width="175" height="29" class="normalNegrita" align="left">Solicitados entre:</td>
	<td width="304" class="normalNegrita" colspan="2">
	<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly" value=""/>
	<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
	<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
	</a>
	<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly" value=""/>
	<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
	<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
	</a>	</td></td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">Fuente de Financiamiento:</td>
	<td><span class="normalNegrita">
	 <select name="txt_reserva" id="txt_reserva" class="normalNegro">
	 <?php 
	   $sql_ff="SELECT * FROM  sai_seleccionar_campo('sai_fuente_fin','fuef_id,fuef_descripcion','esta_id<>15','fuef_descripcion',1) resultado_set(fuef_id varchar,fuef_descripcion varchar)";
	   $res_ff=pg_exec($sql_ff);
	  ?>
	  <option value="">--</option>
	  <?php while($row_ff=pg_fetch_array($res_ff)){?>
	  <option value="<?php echo $row_ff['fuef_id']?>"><?php echo $row_ff['fuef_descripcion']?></option>
	  <?php }?>
	 </select>
	</span></td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">C&oacute;digo del documento:</td>
	<td><span class="normalNegro"><input name="txt_cod" type="text" class="peq" id="txt_cod" value="" size="12" /></span></td>
  </tr>	
  <tr> 
	<td height="30"><div align="left" class="normalNegrita">Dependencia Solicitante:</div></td>
	<td>
	<?php
	  $user_perfil_id = substr($_SESSION['user_perfil_id'],2,2)."0";
	  if ($permiso==0)																	   
		//$sql_str="SELECT * FROM  sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombre','depe_id='||'''$user_perfil_id''','depe_nombre',1) resultado_set(depe_id varchar,depe_nombre varchar)";
	  	$sql_str="SELECT * FROM sai_dependenci WHERE depe_id IN ('".$user_perfil_id."',".DEPENDENCIA_OFICINA_TALENTO_HUMANO.")";
	  else
   		$sql_str="SELECT * FROM  sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombre','depe_nivel=4 or depe_nivel=3','depe_nombre',1) resultado_set(depe_id varchar,depe_nombre varchar)";
   	    $res_q=pg_exec($sql_str);		  
	 ?>
      <select name="dependencia" class="normalNegro" id="dependencia">
        <?php if ($permiso==1){?>
		<option value="" selected="selected">--</option>
	      <?php
           }
	      while($depe_row=pg_fetch_array($res_q)){ ?>
	        <option value="<?php echo(trim($depe_row['depe_id'])); ?>"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
             <?php }?>
           </select></td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">Tipo Solicitud:</td>
	<td><span class="normalNegrita">
	 <select name="tipo_solicitud" class="normalNegro" id="tipo_solicitud">
		<option value="-">[Seleccione]</option>
		<?php
		  $sql_ts="SELECT * FROM  sai_seleccionar_campo('sai_tipo_solicitud','id_sol,nombre_sol','','nombre_sol',1) resultado_set(id_sol int4,nombre_sol varchar)";
          $res_ts=pg_exec($sql_ts);
  		  while($row=pg_fetch_array($res_ts)) 
		  { 
		 ?>
		<option value= "<?php echo $row['id_sol'];?>"><?php echo $row['nombre_sol'];?></option>
		<?php 
		  } ?>
	</select></span></td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">Analista:</td>
	<td><span class="normalNegrita">
	  <select name="cmb_analista" class="normalNegro" id="cmb_analista">
		<option value="">[Seleccione]</option>
		<?php
		$analista_op=$_SESSION['perfil_aop'];
		$m=$_SESSION['admin1'];
		$r=$_SESSION['admin2'];
		$a=$_SESSION['admin3'];
		$p=$_SESSION['admin4'];
		$sql_e="SELECT e.empl_cedula as ci, initcap(e.empl_nombres)||' ' ||initcap(e.empl_apellidos) as analista
		FROM sai_empleado e, sai_usua_perfil p WHERE e.empl_cedula=p.usua_login
		and p.carg_id='".$analista_op."' and e.empl_cedula not in ('".$m."') and e.empl_cedula not in ('".$a."') and e.empl_cedula not in ('".$r."')  and e.empl_cedula not in ('".$p."')"; 
		$resultado_set_e=pg_query($conexion,$sql_e) or die("Error al mostrar");
		while($rowe=pg_fetch_array($resultado_set_e)) 
		{ 
			$ci=trim($rowe['ci']);
			$nombre=$rowe['analista'];
			?>
			<option value= "<?php echo $ci;?>"><?php echo $nombre;?></option>
			<?php 
		} ?>
		</select></span></td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">Beneficiario:</td>
	<td><span class="normalNegrita"><input name="txt_beneficiario" type="text" class="normalNegro" id="txt_beneficiario" value="" size="30" /></span></td>
  </tr>
<tr>
	<td class="normalNegrita" align="left">Proyecto/Acc espec&iacute;fica:</td>
	<td><span >
	  <select name='proyac' class="normalNegro">
		<option value="0" class="normal">Todos</option>
		<?php
		// $sql = "select acce_id as proyecto, aces_id as especifica, centro_gestor, centro_costo from sai_acce_esp where pres_anno=".$press_anno." union select proy_id as proyecto, paes_id as especifica, centro_gestor, centro_costo from sai_proy_a_esp where pres_anno=".$press_anno." order by centro_gestor, centro_costo ";
		  $sql = "select pres_anno, acce_id as proyecto, aces_id as especifica, centro_gestor, centro_costo from sai_acce_esp where pres_anno>2010 union select pres_anno, proy_id as proyecto, paes_id as especifica, centro_gestor, centro_costo from sai_proy_a_esp where pres_anno>2010 order by pres_anno DESC,centro_gestor, centro_costo ";  
	      $resultado_set=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");  
	      while($rowor=pg_fetch_array($resultado_set)) {	?> 
		<!--   <option value="<?php echo $rowor['proyecto'].":::".$rowor['especifica']?>"  class="normal"><?php echo $rowor['centro_gestor'].'/'.$rowor['centro_costo']?></option> -->
		  <option value="<?php echo $rowor['proyecto'].":::".$rowor['especifica']?>"  class="normal"><?php echo ($rowor['pres_anno'].'-'.$rowor['centro_gestor'].'/'.$rowor['centro_costo']);?></option>
		  
		  <?php } ?>
	  </select></span></td>
  </tr>
  
  
  <tr>
	<td class="normalNegrita" align="left">Estado:</td>
	<td><span class="normalNegrita">
	<select name="estado" class="normalNegro" id="estado">
	 <option value="0">[Seleccione]</option>
	 <option value="1" selected> Activo</option>
	 <option value="15"> Anulado</option>
	</select></span></td>
  </tr>
    <tr>
	<td class="normalNegrita" align="left">Estado Geogr&aacute;fico:</td>
	<td><span class="normalNegrita">
	 <select name="edo_vzla" id="edo_vzla" class="normalNegro">
	 <?php 
	   $sql_ff="SELECT * FROM  safi_edos_venezuela ORDER BY nombre";
	   $res_ff=pg_exec($sql_ff);
	  ?>
	  <option value="0">[Seleccione]</option>
	  <?php while($row_ff=pg_fetch_array($res_ff)){?>
	  <option value="<?php echo $row_ff['id']?>"><?php echo $row_ff['nombre']?></option>
	  <?php }?>
	 </select>
	</span></td>
  </tr>
    <tr>
	<td class="normalNegrita" align="left">Compromiso:</td>
	<td><span class="normalNegro">
	<input name="compromiso" id="compromiso" type="text" value="comp-" size="15" maxlength="20"></input></span></td>
  </tr>
  <tr>
    <td colspan="4" class="normal"><center><input type="button" value="Buscar" onclick="ejecutar_varios(document.form.txt_inicio.value, document.form.hid_hasta_itin.value, document.form.txt_reserva.value, document.form.txt_beneficiario.value,document.form.txt_cod.value,document.form.cmb_analista.value,document.form.dependencia.value,document.form.tipo_solicitud.value,document.form.estado.value,document.form.edo_vzla.value);"/>
     <input type="button" value="Hoja de C&aacute;lculo" onclick="validar(document.form.txt_inicio.value, document.form.hid_hasta_itin.value, document.form.txt_reserva.value, document.form.txt_beneficiario.value,document.form.txt_cod.value,document.form.cmb_analista.value,document.form.dependencia.value,document.form.tipo_solicitud.value,document.form.estado.value,document.form.edo_vzla.value);"/></center></td></tr>
</table>
<br />
</form>
<br>
<form name="form3" action="" method="post">
<?php 
if (isset($_POST['check'])) {
  $checkbox = $_POST['check'];
}

if ($_POST['hid_validar']==2) {
	$fecha_in=trim($_POST['txt_inicio']);
	$fecha_fi=trim($_POST['hid_hasta_itin']); 
	$fecha_ini2=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2)." 00:00:00";
	$fecha_fin2=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
?>
    <script language="javascript">
     document.form.txt_reserva.value="<? echo $_POST['txt_reserva'] ?>";
     document.form.txt_cod.value="<? echo $_POST['txt_cod'] ?>";
     document.form.dependencia.value="<? echo $_POST['dependencia'] ?>";
     document.form.tipo_solicitud.value="<? echo $_POST['tipo_solicitud'] ?>";
     document.form.cmb_analista.value="<? echo $_POST['cmb_analista'] ?>";
     document.form.txt_beneficiario.value="<? echo $_POST['txt_beneficiario'] ?>";
     document.form.edo_vzla.value="<? echo $_POST['edo_vzla'] ?>";
    </script>
	
	<?
	 $condicion = " ";
	if (strlen($_POST['txt_inicio']) > 8)
	 	$condicion = " AND sp.sopg_fecha BETWEEN '".$fecha_ini2."' AND '".$fecha_fin2."'";

	if ($_POST['estado']==15)
		$condicion .= " AND sp.esta_id = 15";
	
	if ($_POST['estado']==1)
		$condicion .= " AND sp.esta_id != 15";
	
	if ($_POST['compromiso']<>'comp-')
		$condicion .= " AND sp.comp_id='".$_POST['compromiso']."'";
	
	if ($_POST['tipo_solicitud']!='-') 
		$condicion .= " AND sopg_tp_solicitud='".$_POST['tipo_solicitud']."'";
	
	if ($_POST['edo_vzla']!=0)
		$condicion .= " AND sp.edo_vzla='".$_POST['edo_vzla']."'";
	
	if (strlen($_POST['proyac'])>8) {
		list( $proy, $especif ) = split( ':::', $_POST['proyac'] );
		$condicion .= " AND sopg_acc_pp='".$proy."' AND sopg_acc_esp='".$especif."' ";
    }
    if ($_POST['dependencia']!='')
    	$condicion .= " AND depe_solicitante LIKE '".$_POST['dependencia']."%'";  
    
    if ($_POST['cmb_analista']!='')
    	$condicion .= " AND sp.usua_login LIKE '".$_POST['cmb_analista']."%'";
    
    if (strlen($_POST['txt_cod'])>5)
    	$condicion .= " AND sp.sopg_id = '".$_POST['txt_cod']."'";
    
    if (strlen($_POST['txt_reserva'])>2)
    	$condicion .= " AND sp.numero_reserva LIKE '".$_POST['txt_reserva']."%'";
	
	$query = "SELECT sp.comp_id,
						sp.sopg_id, 
						esta_nombre, 
						sp.numero_reserva, 
						INITCAP(em.empl_nombres)||' ' ||INITCAP(em.empl_apellidos) AS analista, 
    					sp.sopg_monto AS monto_bruto,
						sopg_fecha, 
						sp.sopg_observacion, 
						sopg_bene_tp, 
						sopg_bene_ci_rif,
    					nombre_sol,
						depe_nombre,
						sopg_sub_espe,
						sopg_acc_esp,
						sopg_acc_pp,
						sopg_tipo_impu,
						spi.pres_anno,
						spi.sopg_monto,
						sopg_monto_exento,
    					sc.cpat_id,
						INITCAP(e2.empl_nombres)||' ' ||INITCAP(e2.empl_apellidos) AS beneficiario,
    					CASE 
							WHEN LENGTH(dg.perf_id_act)<2 THEN 'Finalizado' 
							ELSE (SELECT 
									carg_nombre 
								FROM sai_cargo 
								WHERE carg_fundacion = SUBSTR(dg.perf_id_act,0,3)) 
						END AS ubicacion_actual,
						INITCAP(evzla.edo_nombre) AS nombre_estado,
						spc.pgch_id,
						sch.nro_cheque,
						scd.comp_id AS coda
			FROM sai_sol_pago sp 
				INNER JOIN sai_empleado em ON (sp.usua_login = em.empl_cedula)
				LEFT OUTER JOIN sai_tipo_solicitud ts ON (sopg_tp_solicitud = ts.id_sol)
				LEFT OUTER JOIN sai_dependenci dp ON (dp.depe_id=depe_solicitante)
				INNER JOIN sai_estado edo ON (edo.esta_id=sp.esta_id)
				INNER JOIN sai_sol_pago_imputa spi ON (spi.sopg_id=sp.sopg_id)
				INNER JOIN sai_convertidor sc ON (sc.part_id=sopg_sub_espe)
				INNER JOIN sai_empleado e2 ON (sp.sopg_bene_ci_rif=e2.empl_cedula)
				INNER JOIN sai_doc_genera dg ON (dg.docg_id=sp.sopg_id)
				LEFT OUTER JOIN
					(SELECT 
						scd.comp_doc_id,
						MAX(scd.comp_fec_emis) AS comp_fec_emis
					FROM 
						sai_comp_diario scd
					WHERE 
						scd.comp_comen LIKE 'C-%'
					GROUP BY
						scd.comp_doc_id
					) s ON (s.comp_doc_id=sp.sopg_id)
				LEFT OUTER JOIN sai_comp_diario scd ON (scd.comp_doc_id=s.comp_doc_id AND scd.comp_fec_emis=s.comp_fec_emis)
				LEFT OUTER JOIN sai_pago_cheque spc ON (spc.docg_id = sp.sopg_id)
				LEFT OUTER JOIN sai_cheque sch ON (sch.id_cheque = spc.id_nro_cheque)
				LEFT OUTER JOIN sai_edos_venezuela evzla ON (sp.edo_vzla = evzla.edo_id::INT)
			WHERE 
		       (UPPER(e2.empl_nombres) || ' ' || UPPER(e2.empl_apellidos) LIKE UPPER('%".trim($_POST['txt_beneficiario'])."%') OR e2.empl_cedula LIKE '%".trim($_POST['txt_beneficiario'])."%')" . 
     			$condicion . " 
				
				
	
			UNION
	
			SELECT 
				sp.comp_id,
				sp.sopg_id, 
				esta_nombre, 
				sp.numero_reserva, 
				INITCAP(em.empl_nombres) ||' ' ||INITCAP(em.empl_apellidos) AS analista, 
		    	sp.sopg_monto AS monto_bruto, 
		    	sopg_fecha, 
		    	sp.sopg_observacion, 
		    	sopg_bene_tp, 
		    	sopg_bene_ci_rif,
		    	nombre_sol,
		    	depe_nombre,
		    	sopg_sub_espe,
		    	sopg_acc_esp,
		    	sopg_acc_pp,
		    	sopg_tipo_impu,
		    	spi.pres_anno,
		    	spi.sopg_monto,
		    	sopg_monto_exento,
		    	sc.cpat_id,
		    	INITCAP(v.benvi_nombres)||' ' ||INITCAP(v.benvi_apellidos) AS beneficiario, 
		    	CASE 
		    		WHEN LENGTH(dg.perf_id_act)<2 THEN 'Finalizado' 
		    		ELSE (SELECT 
		    				carg_nombre 
		    			FROM sai_cargo 
		    			WHERE carg_fundacion = SUBSTR(dg.perf_id_act,0,3)) 
		    	END AS ubicacion_actual,
		    	INITCAP(evzla.edo_nombre) AS nombre_estado,
		    	spc.pgch_id,
				sch.nro_cheque,		    	
				scd.comp_id AS coda
			FROM sai_sol_pago sp 
				INNER JOIN sai_empleado em ON (sp.usua_login = em.empl_cedula) 
				LEFT OUTER JOIN sai_tipo_solicitud ts ON (sopg_tp_solicitud = ts.id_sol) 
				LEFT OUTER JOIN sai_dependenci dp ON (dp.depe_id = depe_solicitante)
				INNER JOIN sai_estado edo ON (edo.esta_id = sp.esta_id) 
				INNER JOIN sai_sol_pago_imputa spi ON (spi.sopg_id = sp.sopg_id)
				INNER JOIN sai_convertidor sc ON (sc.part_id = sopg_sub_espe) 
				INNER JOIN sai_viat_benef v ON (sp.sopg_bene_ci_rif = v.benvi_cedula) 
				INNER JOIN sai_doc_genera dg ON (dg.docg_id = sp.sopg_id)
				LEFT OUTER JOIN 
					(SELECT 
						scd.comp_doc_id,
						MAX(scd.comp_fec_emis) AS comp_fec_emis
					FROM 
						sai_comp_diario scd
					WHERE 
						scd.comp_comen LIKE 'C-%'
					GROUP BY
						scd.comp_doc_id
					) s ON (s.comp_doc_id=sp.sopg_id)
				LEFT OUTER JOIN sai_comp_diario scd ON (scd.comp_doc_id=s.comp_doc_id AND scd.comp_fec_emis=s.comp_fec_emis)
				LEFT OUTER JOIN sai_pago_cheque spc ON (spc.docg_id = sp.sopg_id)
				LEFT OUTER JOIN sai_cheque sch ON (sch.id_cheque = spc.id_nro_cheque)
				LEFT OUTER JOIN sai_edos_venezuela evzla ON (sp.edo_vzla = evzla.edo_id::INT)
		    WHERE 
		     (UPPER(v.benvi_nombres) || ' ' || UPPER(v.benvi_apellidos) LIKE UPPER('%".$_POST['txt_beneficiario']."%') OR v.benvi_cedula LIKE '%".$_POST['txt_beneficiario']."%')
		    AND v.benvi_cedula NOT IN (SELECT empl_cedula FROM sai_empleado) ".
		    $condicion . " 
    	
	UNION
	
    SELECT 
    	sp.comp_id,
    	sp.sopg_id, 
    	esta_nombre, 
    	sp.numero_reserva, 
    	INITCAP(em.empl_nombres)||' ' ||INITCAP(em.empl_apellidos) AS analista,
    	sp.sopg_monto, 
    	sopg_fecha, 
    	sp.sopg_observacion, 
    	sopg_bene_tp, 
    	sopg_bene_ci_rif,
    	nombre_sol,
    	depe_nombre,
    	sopg_sub_espe,
    	sopg_acc_esp,
    	sopg_acc_pp,
    	sopg_tipo_impu,
    	spi.pres_anno,
    	spi.sopg_monto,
    	sopg_monto_exento,
    	sc.cpat_id,
    	INITCAP(p.prov_nombre) AS beneficiario, 
    	CASE 
    		WHEN LENGTH(dg.perf_id_act)<2 THEN 'Finalizado' 
    		ELSE (SELECT 
    				carg_nombre 
    			FROM sai_cargo 
    			WHERE carg_fundacion=SUBSTR(dg.perf_id_act,0,3)) 
    		END as ubicacion_actual,
    	INITCAP(evzla.edo_nombre) AS nombre_estado,
    	spc.pgch_id,
		sch.nro_cheque,    	
		scd.comp_id AS coda
	FROM 
		sai_sol_pago sp
		INNER JOIN sai_empleado em ON (sp.usua_login = em.empl_cedula) 
		LEFT OUTER JOIN sai_tipo_solicitud ts ON (sopg_tp_solicitud = ts.id_sol) 
		LEFT OUTER JOIN sai_dependenci dp ON (dp.depe_id = depe_solicitante)
		INNER JOIN sai_estado edo ON (edo.esta_id = sp.esta_id) 
		INNER JOIN sai_sol_pago_imputa spi ON (spi.sopg_id = sp.sopg_id)
		INNER JOIN sai_convertidor sc ON (sc.part_id = sopg_sub_espe) 
		INNER JOIN sai_proveedor_nuevo p ON (sp.sopg_bene_ci_rif = p.prov_id_rif) 
		INNER JOIN sai_doc_genera dg ON (dg.docg_id = sp.sopg_id)
		LEFT OUTER JOIN
			(SELECT 
				scd.comp_doc_id,
				MAX(scd.comp_fec_emis) AS comp_fec_emis
			FROM 
				sai_comp_diario scd
			WHERE 
				scd.comp_comen LIKE 'C-%'
			GROUP BY
				scd.comp_doc_id
			) s ON (s.comp_doc_id=sp.sopg_id)
		LEFT OUTER JOIN sai_comp_diario scd ON (scd.comp_doc_id=s.comp_doc_id AND scd.comp_fec_emis=s.comp_fec_emis)
		LEFT OUTER JOIN sai_pago_cheque spc ON (spc.docg_id = sp.sopg_id)
		LEFT OUTER JOIN sai_cheque sch ON (sch.id_cheque = spc.id_nro_cheque)
		LEFT OUTER JOIN sai_edos_venezuela evzla ON (sp.edo_vzla = evzla.edo_id::INT)
    WHERE 
		(UPPER(p.prov_nombre) LIKE UPPER('%".$_POST['txt_beneficiario']."%') OR p.prov_id_rif LIKE '%".$_POST['txt_beneficiario']."%')". 
		$condicion . "
	ORDER BY sopg_fecha DESC"; 

//and sopg_sub_espe<>'".$_SESSION['part_iva']."'

	$resultado_set_most_or=pg_query($conexion,$query) or die("Error al consultar la descripcion del sopg");  
	
	if(($rowor=pg_fetch_array($resultado_set_most_or))==null)  {
	  echo "<center><font color='#FF0000' class='titular'>"."Actualmente no existen documentos generados para ese criterio de b&uacute;squeda</font></center>";
	}
	else { 
		$beneficiario_nombre= "";
		$beneficiario = "";
	  ?>
   <table width="100%" border="0" align="center">
        <tr>
		<?php

		    $ano1=substr($fecha_ini,0,4);
			$mes1=substr($fecha_ini,5,2);
			$dia1=substr($fecha_ini,8,2);
			
			$ano2=substr($fecha_fin,0,4);
			$mes2=substr($fecha_fin,5,2);
			$dia2=substr($fecha_fin,8,2);
		?>
          <td width="495" height="27" class="normalNegrita"><div align="center">Resultado de la b&uacute;squeda de Solicitudes de Pago: </div></td>
        </tr>
  </table>
 <table width="100%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
    <tr class="td_gray">
	  <td width="137" class="normalNegroNegrita" align="center">C&oacute;digo del Documento </td>
	  <td width="128" class="normalNegroNegrita" align="center">Fecha de la Solicitud </td>
	  <td width="115" class="normalNegroNegrita" align="center">Estado </td>
	  <td width="115" class="normalNegroNegrita" align="center">Ubicaci&oacute;n Actual </td>
	  <td width="102" class="normalNegroNegrita" align="center">Fuente de Financiamiento</td>
	  <td width="102" class="normalNegroNegrita" align="center">Compromiso</td>	
	  <td width="102" class="normalNegroNegrita" align="center">CI/RIF</td>
	  <td width="102" class="normalNegroNegrita" align="center">Beneficiario</td>
	  <td width="102" class="normalNegroNegrita" align="center">Detalle</td>
	  <td width="102" class="normalNegroNegrita" align="center">Tipo de Operaci&oacute;n</td>
	  <td width="102" class="normalNegroNegrita" align="center">Gerencia Solicitante</td>
	  <td width="102" class="normalNegroNegrita" align="center">Centro Gestor</td>
	  <td width="102" class="normalNegroNegrita" align="center">Centro Costo</td>
	  <td width="102" class="normalNegroNegrita" align="center">Partida</td>
	  <td width="102" class="normalNegroNegrita" align="center">Cuenta Contable</td>
	  <td width="102" class="normalNegroNegrita" align="center">Monto Partida</td>
	  <td width="102" class="normalNegroNegrita" align="center">Monto Base</td>
	  <td width="102" class="normalNegroNegrita" align="center">% IVA</td>
	  <td width="102" class="normalNegroNegrita" align="center">Monto IVA</td>
	  <td width="102" class="normalNegroNegrita" align="center">Monto Bruto</td>
	  <td width="102" class="normalNegroNegrita" align="center">Retenci&oacute;n IVA</td>
	  <td width="102" class="normalNegroNegrita" align="center">Retenci&oacute;n ISLR</td>
	  <td width="102" class="normalNegroNegrita" align="center">Retenci&oacute;n LTF</td>
	  <td width="102" class="normalNegroNegrita" align="center">Monto </td>
	  <td width="102" class="normalNegroNegrita" align="center">Estado Vzla</td>
	  <td width="102" class="normalNegroNegrita" align="center">Cta. bancaria</td>
	  <td width="102" class="normalNegroNegrita" align="center">Analista</td>
	  <td width="102" class="normalNegroNegrita" align="center">Pgch</td>
	  <td width="102" class="normalNegroNegrita" align="center">Nro Cheque</td>
	  <td width="102" class="normalNegroNegrita" align="center">Coda</td>
    </tr>
					
	<?php
	 $sopg_anterior='';
	 $resultado_set_most_pa=pg_query($conexion,$query);  
	 while($rowpa=pg_fetch_array($resultado_set_most_pa))  
	 {  
	 	$sopg_actual=$rowpa['sopg_id'];
	    if ($sopg_anterior<>$sopg_actual){
	    $sopg_anterior=$sopg_actual;  	
       	$monto_cheque=0;
		?>
    <tr class="normal">
      <td height="28" align="center"><span class="link">
       <a href="javascript:abrir_ventana('sopg_detalle.php?codigo=<?php echo trim($rowpa['sopg_id']); ?>&amp;esta_id=<?php echo($rowpa['esta_id']);?>')" class="copyright"><?php echo $rowpa['sopg_id'] ;?></a></span></td>
      <td align="center"><span class="normalNegro"><?php echo cambia_esp(trim($rowpa['sopg_fecha']));?></span></td>
	  <td align="left" class="normalNegro"><div align="center">
	  <?php echo $rowpa['esta_nombre'];?></div></td>
	  <td align="left" class="normalNegro"><div align="center">
	  <?php 
		echo $rowpa['ubicacion_actual'];?></div></td>
	 <td align="center" class="normalNegro"><?php
	   $guion=substr($rowpa['numero_reserva'],2,1);
       if ($guion!='-'){
	    $reserva=substr($rowpa['numero_reserva'],0,2);
		$sql="SELECT * FROM sai_seleccionar_campo('sai_fuente_fin','fuef_descripcion','fuef_id like'||'''$reserva%''','',2) resultado_set(fuef_descripcion varchar)";
		$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
				if($row=pg_fetch_array($resultado_set_most_p))
				{
 				 $fuente=trim($row['fuef_descripcion']); //Solicitante
				}
        }else{
        	$fuente=$rowpa['numero_reserva'];
        }
	   echo $fuente;?></td>
	<td align="center" class="normalNegro"><?php echo $rowpa['comp_id'];?> </td>
	<td align="center" class="normalNegro"><?php echo $rowpa['sopg_bene_ci_rif'];?> </td>
	<td align="center" class="normalNegro"><?php echo $rowpa['beneficiario'];?></td>
	<td align="center" class="normalNegro"><?php echo $rowpa['sopg_observacion'];?></td>
    <?
      $tipo_solicitud="*";
	  if ($rowpa['nombre_sol']<>"")
	  $tipo_solicitud=trim($rowpa['nombre_sol']);
	 ?>		
	<td align="center" class="normalNegro"><?php echo $tipo_solicitud;?></td>
	<?
	  $dep_solicitante="*";
	  if ($rowpa['depe_nombre']<>"")
	  $dep_solicitante=trim($rowpa['depe_nombre']);		
    ?>
	<td align="center" class="normalNegro"><?php echo $dep_solicitante;?></td>
    <?
	 $centro_gestor="*";$centro_costo="*";
	 $contador=0;
	 $tipo_impu=$rowpa['sopg_tipo_impu'];
	 if ($tipo_impu==0)//Acci{on Específica
	 { 
	  $sql_p="SELECT * FROM sai_seleccionar_campo('sai_acce_esp', 'centro_gestor,centro_costo', 'acce_id='||'''".$rowpa['sopg_acc_pp']."'' and aces_id='||'''".$rowpa['sopg_acc_esp']."''  and pres_anno='||'".$rowpa['pres_anno']." ','',2) resultado_set(centro_gestor varchar,centro_costo varchar)"; 
	  $resultado=pg_query($conexion,$sql_p);
	 }else{
			$sql_p="SELECT * FROM sai_seleccionar_campo('sai_proy_a_esp', 'centro_gestor,centro_costo', 'paes_id='||'''".$rowpa['sopg_acc_esp']."'' and proy_id='||'''".$rowpa['sopg_acc_pp']."''  and pres_anno='||'".$rowpa['pres_anno']."','',2) resultado_set(centro_gestor varchar,centro_costo varchar)"; 
			$resultado=pg_query($conexion,$sql_p);
		   }
	  if ($row=pg_fetch_array($resultado))
	  {
	    $centro_gestor=trim($row['centro_gestor']);
	    $centro_costo=trim($row['centro_costo']);
	  }

	    $porcentaje="*";$monto_base="*";$monto_iva="*";
	    $sql_p="SELECT * FROM sai_seleccionar_campo('sai_docu_iva', 'ivap_porce,docg_monto_base,docg_monto_iva', 'docg_id='||'''".$rowpa['sopg_id']."''','',2) resultado_set(ivap_porce float4,docg_monto_base float8,docg_monto_iva float8)"; 
		$resultado_iva=pg_query($conexion,$sql_p);
		$valido_iva=0;
		if ($row=pg_fetch_array($resultado_iva))
		{	$valido_iva=1;		
			$porcentaje=trim($row['ivap_porce']);
			$monto_base=trim($row['docg_monto_base']);				
			$monto_iva=trim($row['docg_monto_iva']);
		}
		?>
		<td align="center" class="normalNegro"><?php echo $centro_gestor;?></td>
		<td align="center" class="normalNegro"><?php echo $centro_costo;?></td>
		
		
		<?php if (substr($rowpa['sopg_sub_espe'],0,6)=="4.11.0") {
		 
		  	$convertidor="SELECT cpat_id FROM sai_convertidor WHERE part_id='".$rowpa['sopg_sub_espe']."'";
      		$res_convertidor=pg_query($conexion,$convertidor);
      		if($row_conv=pg_fetch_array($res_convertidor)){
			  $cuenta= $row_conv['cpat_id'];
      		}          	
     	  }else{
     			$cuenta=$rowpa['sopg_sub_espe'];
     			}
		?>
		<td align="center" class="normalNegro"><?php echo $cuenta;//$rowpa['sopg_sub_espe'];?></td>
		<td align="center" class="normalNegro"><?php echo $rowpa['cpat_id'];?></td>
		<?php if ($rowpa['sopg_sub_espe']==$_SESSION['part_iva']){?>
		<td align="center" class="normalNegro"><?php echo(number_format(0,2,',',''));?></td>
		<?php }else{?>
		<td align="center" class="normalNegro"><?php echo(number_format($rowpa['sopg_monto']+$rowpa['sopg_monto_exento'],2,',',''));?></td>
		<?php }?>
		<td align="center" class="normalNegro"><?php
			echo(is_numeric($monto_base) != "" ? number_format($monto_base,2,',','') : $monto_base);
		?></td>
		<td align="center" class="normalNegro"><?php echo $porcentaje;?></td>
		<td align="center" class="normalNegro"><?php
			echo is_numeric($monto_iva) ? (number_format($monto_iva,2,',','')) : $monto_iva;
		?></td>
		  <td align="center" class="normalNegro"><?php
		  $monto_bruto=$rowpa['monto_bruto'];
			echo is_numeric($monto_bruto) ? (number_format($monto_bruto,2,',','')) : $monto_bruto;
		?></td>
		<?
		  $iva="*";$islr="*";$ltf="*";
		  $sql_p="SELECT * FROM sai_seleccionar_campo('sai_sol_pago_retencion', 'sopg_ret_monto,sopg_por_rete,impu_id', 'sopg_id='||'''".$rowpa['sopg_id']."''','',2) resultado_set(sopg_ret_monto float8,sopg_por_rete float4,impu_id varchar)"; 
		  $resultado=pg_query($conexion,$sql_p);
		  while ($row=pg_fetch_array($resultado))
		  {			
			$impuesto=trim($row['impu_id']);
			if ($impuesto=='IVA'){
			$iva=trim($row['sopg_ret_monto']);}
			if ($impuesto=='ISLR'){
			$islr=trim($row['sopg_ret_monto']);}
			if ($impuesto=='LTF'){				
			$ltf=trim($row['sopg_ret_monto']);}
		 }
		?>
		<td align="center" class="normalNegro"><?php echo is_numeric($iva) ? (number_format($iva,2,',','')) : $iva; ?></td>
		<td align="center" class="normalNegro"><?php echo is_numeric($islr) ? (number_format($islr,2,',','')) : $islr;?></td>
		<td align="center" class="normalNegro"><?php echo is_numeric($ltf) ? (number_format($ltf,2,',','')) : $ltf;?></td>
				
		<?
		 $sql_p="SELECT monto_cheque as sopg_cheque,ctab_numero as cuenta_banco
				 FROM sai_cheque c1 ,sai_chequera c2
				 WHERE docg_id='".$rowpa['sopg_id']."'
				 and c1.nro_chequera=c2.nro_chequera
				 union
				 SELECT trans_monto as sopg_cheque,nro_cuenta_emisor as cuenta_banco
				 FROM sai_pago_transferencia
				 WHERE docg_id='".$rowpa['sopg_id']."'
			 ";
		$resultado=pg_query($conexion,$sql_p);
		if ($row=pg_fetch_array($resultado))
		{			
		 $monto_cheque=trim($row['sopg_cheque']);
		 $cuenta=($row['cuenta_banco']);
		}
		else{
			$cuenta="";
		}

		if ($monto_cheque==0){?>
		<td align="center" class="normalNegro">--</td>
		<?}else{?>
		<td align="center" class="normalNegro"><?php 
		echo(number_format($monto_cheque,2,',',''));
		?></td>
		<?}
		?>
		<td align="center" class="normalNegro"><?php 
		echo trim($rowpa['nombre_estado']);?></td>
		<td align="center" class="normalNegro"><?php 
		echo $cuenta;?></td>
		<td align="center" class="normalNegro"><?php echo $rowpa['analista'];?></td>
		<td align="center" class="normalNegro"><?php echo $rowpa['pgch_id'];?></td>
		<td align="center" class="normalNegro"><?php echo $rowpa['nro_cheque'];?></td>
		<td align="center" class="normalNegro"><?php echo $rowpa['coda'];?></td>
	</tr>
	<? 
     }
     else { //si son iguales los sopg
         $tipo_impu=$rowpa['sopg_tipo_impu'];
     if ($tipo_impu==0){ //Acci{on Específica
		
		 $sql_p="SELECT * FROM sai_seleccionar_campo('sai_acce_esp', 'centro_gestor,centro_costo', 'acce_id='||'''".$rowpa['sopg_acc_pp']."'' and aces_id='||'''".$rowpa['sopg_acc_esp']."''  and pres_anno='||'".$rowpa['pres_anno']." ','',2) resultado_set(centro_gestor varchar,centro_costo varchar)"; 
		 $resultado=pg_query($conexion,$sql_p);
	
		}else{
		$sql_p="SELECT * FROM sai_seleccionar_campo('sai_proy_a_esp', 'centro_gestor,centro_costo', 'paes_id='||'''".$rowpa['sopg_acc_esp']."'' and proy_id='||'''".$rowpa['sopg_acc_pp']."''  and pres_anno='||'".$rowpa['pres_anno']."','',2) resultado_set(centro_gestor varchar,centro_costo varchar)"; 
		 $resultado=pg_query($conexion,$sql_p);
	
		}
	 	  if ($row=pg_fetch_array($resultado))
		  {
		   $centro_gestor=trim($row['centro_gestor']);
		   $centro_costo=trim($row['centro_costo']);
		  }

	 ?>
	<tr>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center" class="normalNegro"><?php echo $tipo_solicitud;?></td>
	  <td align="center" class="normalNegro"><?php echo $dep_solicitante;?></td>
  	  <td align="center" class="normalNegro"><?php echo $centro_gestor;?></td>
	  <td align="center" class="normalNegro"><?php echo $centro_costo;?></td>
	  		<?php if (substr($rowpa['sopg_sub_espe'],0,6)=="4.11.0") {
		 
		  	$convertidor="SELECT cpat_id FROM sai_convertidor WHERE part_id='".$rowpa['sopg_sub_espe']."'";
      		$res_convertidor=pg_query($conexion,$convertidor);
      		if($row_conv=pg_fetch_array($res_convertidor)){
			  $cuenta= $row_conv['cpat_id'];
      		}          	
     	  }else{
     			$cuenta=$rowpa['sopg_sub_espe'];
     			}
		?>
	  <td align="center" class="normalNegro"><?php echo $cuenta;//$rowpa['sopg_sub_espe'];?></td>
	  <td align="center" class="normalNegro"><?php echo $rowpa['cpat_id'];?></td>
	  <?php if ($rowpa['sopg_sub_espe']==$_SESSION['part_iva']){?>
		<td align="center" class="normalNegro"><?php echo(number_format(0,2,',',''));?></td>
	  <?php }else{?>
	  <td align="center" class="normalNegro"><?php echo(number_format($rowpa['sopg_monto']+$rowpa['sopg_monto_exento'],2,',',''));?></td>
	  <?php }?>
	  <td align="center" ><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	  <td align="center"><span class="peq">&nbsp;</span></td>
	</tr>
    <?php 
    }?> 
	<?php 
	  }//fin while query?>
	  <? 
	}
}
?> 
 </table> 
</form>
</body>
</html>

<?php pg_close($conexion);?>