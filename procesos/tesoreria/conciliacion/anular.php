<?php 
 ob_start();
session_start();
require_once("../../../includes/conexion.php");
require_once("../../../includes/arreglos_pg.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado"))  {
	header('Location:../../../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ANULAR CONCILIACI&Oacute;N BANCARIA</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../js/funciones.js"> </script>
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
	g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<script language="javascript">
function ejecutar_varios(form,op) { 
	   if ((document.form.txt_inicio.value!='') && (document.form.hid_hasta_itin.value!='') && (document.form.txt_fondo.value!=0)) {
			 document.form.hid_validar.value=4;
	   }
	   
	   if ((document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value=='') && (document.form.txt_fondo.value==0)) {
			 alert("Debe seleccionar los parametros de busqueda"); 
			 return false;
	   }
		   
	   if (((document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value=='') && (document.form.txt_fondo.value!=0))  || ((document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value!='') && (document.form.txt_fondo.value==0)) || ((document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value!='') && (document.form.txt_fondo.value!=0)) || ((document.form.txt_inicio.value!='') && (document.form.hid_hasta_itin.value=='') && (document.form.txt_fondo.value==0)) || ((document.form.txt_inicio.value!='') && (document.form.hid_hasta_itin.value=='') && (document.form.txt_fondo.value!=0)) || ((document.form.txt_inicio.value!='') && (document.form.hid_hasta_itin.value!='') && (document.form.txt_fondo.value==0))) {
			 alert("Debe ingresar todos los parametros de busqueda...\n verifique por favor... ");
			  return false;
	   }
		   
	   if (document.form.txt_cuenta.selectedIndex==0) {
			 alert("Debe seleccionar una cuenta");
			 return false;
		}
		   
		if(op==1) {
			document.form.action="anularAccion.php";	
			document.form.submit();
			return true;	
		}
		else{
			document.form.action="libro_bancoPDF.php";
			document.form.submit();
			return true;
		}
			return true;
	}

	function anular() {
		document.form3.action="anularAccion.php";	
		document.form3.submit();
	}
</script>
</head>
<body>
<form name="form"  method="post">
  <div align="center">
    <p>
      <input type="hidden" value="0" name="hid_validar" />
      <input type="hidden" value="0" name="opt_validar" />
  </div>
  <table width="60%" align="center" style="background-image:url('../../../imagenes/fondo_tabla.gif')" class="tablaalertas">
<tr class="td_gray"> 
  <td colspan="3" class="normalNegroNegrita" align="left">ANULAR CONCILIACI&Oacute;N BANCARIA </td>
</tr>
<tr>
	<td class="normalNegrita">Fecha del Movimiento:</td>
	<td>
<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?php echo $_POST["txt_inicio"];?>"/>
					<a href="javascript:void(0);" 

onclick="g_Calendar.show(event, 'txt_inicio');" 
						title="Show popup calendar">
						<img src="../../../js/lib/calendarPopup/img/calendar.gif" 
							class="cp_img" 
							alt="Open popup calendar"/>
					</a>
					<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse"
onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?php echo $_POST["hid_hasta_itin"];?>"/>
					<a href="javascript:void(0);" 

onclick="g_Calendar.show(event, 'hid_hasta_itin');" 
						title="Show popup calendar">
						<img src="../../../js/lib/calendarPopup/img/calendar.gif" 
							class="cp_img" 
							alt="Open popup calendar"/>
					</a>	
	</td>
</tr>
  <tr>
    <td class="normalNegrita">N&uacute;mero de Cuenta:</td>
    <td><select name="txt_cuenta" class="normal" id="txt_cuenta">
     <option value="0">::: Seleccione :::</option>
     <option value="">Todos</option>
      <?php 
	        //busqueda de las cuentas 
			$sql="SELECT ctab_numero, ctab_descripcion FROM  sai_ctabanco"; 
			$resultado=pg_query($conexion,$sql) or die("Error al mostrar"); 
			while($row=pg_fetch_array($resultado)) { 
				$tifo=trim($row['ctab_numero']);
				$nombre_tifo=$row['ctab_descripcion'];
			?>
      <option value="<?php echo $tifo?>"><?php echo $tifo?>-<?php echo $nombre_tifo?></option>
      <?php	} ?>
    </select>
 </td>
  </tr>
  <tr>
  <td height="44" colspan="2" align="center">
  <input type="hidden" value="1" name="validar"/> 
  <input type="submit" value="Buscar" onclick="return ejecutar_varios(this.form,1)"/> 
 </td>
</tr>
</table>
</form>
<br/>
<form name="form3" action="" method="post">
<?php 
     if ( ( (($_POST['txt_inicio'])=='') and (($_POST['hid_hasta_itin'])!='') ) or ( (($_POST['txt_inicio'])!='') and (($_POST['hid_hasta_itin'])=='') ) )   {
		     echo "<SCRIPT LANGUAGE='JavaScript'>";
			 echo "alert ('Solo selecciono una fecha, la busqueda es entre las dos, verifique por favor...');"."</SCRIPT>";
	}
		   if (strcmp($_POST['validar'],1)==0) {   
		       $cuenta=trim($_POST['txt_cuenta']);
			   $fecha_in=trim($_POST['txt_inicio']);  
			   $fecha_fi=trim($_POST['hid_hasta_itin']);
			   $ano = substr($fecha_in,6,4);			    
		  ?>
	  <table width="551" border="0" align="center">
        <tr>
		<?php
				//fecha inicial
				$ano1=substr($fecha_ini,0,4);
				$mes1=substr($fecha_ini,5,2);
				$dia1=substr($fecha_ini,8,2);
				//fecha final
				$ano2=substr($fecha_fin,0,4);
				$mes2=substr($fecha_fin,5,2);
				$dia2=substr($fecha_fin,8,2);
		?>
          <td width="545" height="27" class="normalNegroNegrita"><div align="center">
            <p>Libro Banco<br/>Movimientos Generados del: <?php echo $fecha_in;?> al <?php echo $fecha_fi;?></p>
         <?php if($cuenta<>''){ ?>  <p class="titular">Para la Cuenta Nro: <?echo $cuenta;?></p> <?php } ?>
          </div></td>
        </tr>
  </table>
	  <table width="100%" align="center" style="background-image:url('../../../imagenes/fondo_tabla.gif')"; class="tablaalertas">
					<tr class="td_gray">
					  <td width="115" class="normalNegroNegrita" align="center">sopg/codi </td>
					  <td width="115" class="normalNegroNegrita" align="center">Pgch/tran </td>
					  <td width="110" class="normalNegroNegrita" align="center">N&uacute;mero cuenta </td>
					  				      
					  <td width="90" class="normalNegroNegrita" align="center">Fecha </td>
					  <td width="110" class="normalNegroNegrita" align="center">N&uacute;mero del Cheque </td>
					  <td width="110" class="normalNegroNegrita" align="center">Beneficiario </td>
					  <td width="110" class="normalNegroNegrita" align="center">Concepto </td>
					  <td width="115" class="normalNegroNegrita" align="center">Saldo inicial </td>
					  <td width="115" class="normalNegroNegrita" align="center">Cargos (Debe) </td>
					  <td width="98" class="normalNegroNegrita" align="center">Abonos (Haber) </td>
					  <td width="90" class="normalNegroNegrita" align="center">Saldo Final</td>
					  
					
    </tr>
<?php
	$fecha_inicio_antes = date("d/m/Y",mktime(0,0,0,substr($fecha_in, 3, 2),(01 - 1),substr($fecha_in, 6)));
	$fecha_mes=substr($fecha_in,6,4)."-".substr($fecha_in,3,2);
	$fecha_mes2=substr($fecha_in,3,2)."/".substr($fecha_in,6,4);
	$fecha_in2=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fi2=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2);	
	
	/*Cálculo de saldos finales contabilidad*/
	$fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-01";
	$login=$_SESSION['login'];		

	$sql= "select monto_haber from sai_ctabanco_saldo
		   where docg_id like 'sb-".$ano."' and ctab_numero='".$cuenta."'";
	$resultado=pg_query($conexion,$sql) or die("Error al consultar saldo inicial en cuenta banco");
	if ($row=pg_fetch_array($resultado))  $saldo_banco=$row["monto_haber"] ; 

	$sql= "select sum(monto_debe) as suma_debe from sai_ctabanco_saldo
		   where ctab_numero='".$cuenta."' and fecha_saldo like '".$ano."%' and to_date(to_char(fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')< to_date('".$fecha_ini."', 'YYYY-MM-DD') and docg_id not like 'sb%' and docg_id not like 'si%' and docg_id not in (select docg_id from sai_doc_genera where esta_id=15)";
	$resultado=pg_query($conexion,$sql) or die("Error al consultar saldo de los debe en cuenta banco");
	if ($row=pg_fetch_array($resultado))  $saldo_banco=$saldo_banco-$row["suma_debe"] ; 

	$sql="select sum(monto_haber) as suma_haber from sai_ctabanco_saldo
		  where ctab_numero='".$cuenta."' and fecha_saldo like '".$ano."%' and fecha_saldo like '".$ano."%' and to_date(to_char(fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')< to_date('".$fecha_ini."', 'YYYY-MM-DD') and docg_id not like 'sb%' and docg_id not like 'si%' and docg_id not in (select docg_id from sai_doc_genera where esta_id=15)";
	$resultado=pg_query($conexion,$sql) or die("Error al consultar saldo de los haber en cuenta banco");
	if ($row=pg_fetch_array($resultado))  $saldo_banco=$saldo_banco+$row["suma_haber"] ; 
	
	$saldo_inicial = 0;
	$saldo_final = 0;
	/*Fin de Cálculo de saldos finales*/


$sql="select sp.sopg_id as sopg_id, pch.pgch_id as pago_id, cq.ctab_numero as numero_cuenta, to_char(p.paga_fecha, 'DD/MM/YYYY') as fecha_pagado,  upper(coalesce(em.empl_nombres,''))||' '||upper(coalesce(em.empl_apellidos,'')) as beneficiario, coalesce(ch.nro_cheque,'') as referencia,CASE ch.estatus_cheque WHEN 15 THEN 'A' WHEN 45 THEN case when p.paga_docu_id in (select docg_id from sai_ctabanco_saldo where docg_id like 'pgch%') then 'C' else 'N' end end as condicion, coalesce(ch.monto_cheque,0)  as monto , coalesce(sp.sopg_detalle, '') as comentario
from sai_pagado p, sai_cheque ch, sai_chequera cq, sai_sol_pago sp, sai_pago_cheque pch, sai_empleado em, sai_ctabanco_saldo ctb
where p.paga_docu_id=ctb.docg_id and ctb.docg_id like 'pgch%' and to_date(to_char(ctb.fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fi2."' and trim(sp.sopg_bene_ci_rif)=trim(em.empl_cedula)  and  p.paga_docu_id=pch.pgch_id and pch.docg_id=sp.sopg_id and ch.docg_id = sp.sopg_id and ch.nro_chequera=cq.nro_chequera ".$condicionq." and p.pres_anno<>2008 and to_date(to_char(ctb.fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fecha_in2."' and p.esta_id<>15 and p.esta_id<>2 

union (
select sp.sopg_id as sopg_id, pch.pgch_id as pago_id, cq.ctab_numero as numero_cuenta, to_char(p.paga_fecha, 'DD/MM/YYYY') as fecha_pagado,  upper(coalesce(v.benvi_nombres,'')) ||' '|| upper(coalesce(v.benvi_apellidos,'')) as beneficiario, coalesce(ch.nro_cheque,'') as referencia,CASE ch.estatus_cheque WHEN 15 THEN 'A' WHEN 45 THEN case when p.paga_docu_id in (select docg_id from sai_ctabanco_saldo where docg_id like 'pgch%') then 'C' else 'N' end end as condicion, coalesce(ch.monto_cheque,0)  as monto , coalesce(sp.sopg_detalle, '') as comentario
from sai_pagado p, sai_cheque ch, sai_chequera cq, sai_sol_pago sp, sai_pago_cheque pch, sai_viat_benef v, sai_ctabanco_saldo ctb
where p.paga_docu_id=ctb.docg_id and ctb.docg_id like 'pgch%' and to_date(to_char(fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fi2."' and trim(sp.sopg_bene_ci_rif)=trim(v.benvi_cedula) and sp.sopg_bene_ci_rif not in (select empl_cedula from sai_empleado) and  p.paga_docu_id=pch.pgch_id and pch.docg_id=sp.sopg_id and ch.docg_id = sp.sopg_id and ch.nro_chequera=cq.nro_chequera ".$condicionq." and p.pres_anno<>2008 and to_date(to_char(ctb.fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fecha_in2."' and p.esta_id<>15 and p.esta_id<>2 

union (
select sp.sopg_id as sopg_id, pch.pgch_id as pago_id, cq.ctab_numero as numero_cuenta, to_char(p.paga_fecha, 'DD/MM/YYYY') as fecha_pagado, upper(coalesce(pr.prov_nombre,'')) as beneficiario, coalesce(ch.nro_cheque,'') as referencia,CASE ch.estatus_cheque WHEN 15 THEN 'A' WHEN 45 THEN case when p.paga_docu_id in (select docg_id from sai_ctabanco_saldo where docg_id like 'pgch%') then 'C' else 'N' end end as condicion, coalesce(ch.monto_cheque,0)  as monto , coalesce(sp.sopg_detalle, '') as comentario
from sai_pagado p, sai_cheque ch, sai_chequera cq, sai_sol_pago sp, sai_pago_cheque pch, sai_proveedor_nuevo pr, sai_ctabanco_saldo ctb
where p.paga_docu_id=ctb.docg_id and ctb.docg_id like 'pgch%' and to_date(to_char(fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fi2."' and trim(sp.sopg_bene_ci_rif)=trim(pr.prov_id_rif)  and  p.paga_docu_id=pch.pgch_id and pch.docg_id=sp.sopg_id and ch.docg_id = sp.sopg_id and ch.nro_chequera=cq.nro_chequera ".$condicionq." and p.pres_anno<>2008 and to_date(to_char(ctb.fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fecha_in2."' and p.esta_id<>15 and p.esta_id<>2 

union (
select sp.sopg_id as sopg_id, ptr.trans_id as pago_id, cb.ctab_numero as numero_cuenta, to_char(p.paga_fecha, 'DD/MM/YYYY') as fecha_pagado, upper(coalesce(em.empl_nombres,''))||' '||upper(coalesce(em.empl_apellidos,'')) as beneficiario, coalesce(ptr.nro_referencia,'') as referencia,CASE ptr.esta_id WHEN 15 THEN 'A' WHEN 10 THEN case when p.paga_docu_id in (select docg_id from sai_ctabanco_saldo where docg_id like 'tran%') then 'C' else 'N' end end as condicion, coalesce(ptr.trans_monto,0)  as monto , coalesce(sp.sopg_detalle, '') as comentario
from sai_pagado p, sai_pago_transferencia ptr, sai_ctabanco cb, sai_sol_pago sp, sai_empleado em, sai_ctabanco_saldo ctb
where (cb.ctab_numero=ptr.nro_cuenta_emisor or cb.ctab_numero=ptr.nro_cuenta_receptor) and p.paga_docu_id=ctb.docg_id and ctb.docg_id like 'tran%' and to_date(to_char(fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fi2."' and trim(sp.sopg_bene_ci_rif)=trim(em.empl_cedula)  and p.paga_docu_id=ptr.trans_id and ptr.docg_id=sp.sopg_id ".$condicionb." and p.pres_anno<>2008 and to_date(to_char(ctb.fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fecha_in2."' and p.esta_id<>15 and p.esta_id<>2 

union (
select sp.sopg_id as sopg_id, ptr.trans_id as pago_id, cb.ctab_numero as numero_cuenta, to_char(p.paga_fecha, 'DD/MM/YYYY') as fecha_pagado,  upper(coalesce(v.benvi_nombres,'')) ||' '|| upper(coalesce(v.benvi_apellidos,'')) as beneficiario, coalesce(ptr.nro_referencia,'') as referencia,CASE ptr.esta_id WHEN 15 THEN 'A' WHEN 10 THEN case when p.paga_docu_id in (select docg_id from sai_ctabanco_saldo where docg_id like 'tran%') then 'C' else 'N' end end as condicion, coalesce(ptr.trans_monto,0)  as monto , coalesce(sp.sopg_detalle, '') as comentario
from sai_pagado p, sai_pago_transferencia ptr, sai_ctabanco cb, sai_sol_pago sp, sai_viat_benef v, sai_ctabanco_saldo ctb
where (cb.ctab_numero=ptr.nro_cuenta_emisor or cb.ctab_numero=ptr.nro_cuenta_receptor) and p.paga_docu_id=ctb.docg_id and ctb.docg_id like 'tran%' and to_date(to_char(fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fi2."' and trim(sp.sopg_bene_ci_rif)=trim(v.benvi_cedula) and sp.sopg_bene_ci_rif not in (select empl_cedula from sai_empleado)  and p.paga_docu_id=ptr.trans_id and ptr.docg_id=sp.sopg_id ".$condicionb." and p.pres_anno<>2008 and to_date(to_char(ctb.fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fecha_in2."' and p.esta_id<>15 and p.esta_id<>2 

union (
select sp.sopg_id as sopg_id, ptr.trans_id as pago_id,  cb.ctab_numero as numero_cuenta, to_char(p.paga_fecha, 'DD/MM/YYYY') as fecha_pagado,  upper(coalesce(pr.prov_nombre,'')) as beneficiario, coalesce(ptr.nro_referencia,'') as referencia,CASE ptr.esta_id WHEN 15 THEN 'A' WHEN 10 THEN case when p.paga_docu_id in (select docg_id from sai_ctabanco_saldo where docg_id like 'tran%') then 'C' else 'N' end end as condicion, coalesce(ptr.trans_monto,0)  as monto , coalesce(sp.sopg_detalle, '') as comentario
from sai_pagado p, sai_pago_transferencia ptr, sai_ctabanco cb, sai_sol_pago sp, sai_proveedor_nuevo pr, sai_ctabanco_saldo ctb
where (cb.ctab_numero=ptr.nro_cuenta_emisor or cb.ctab_numero=ptr.nro_cuenta_receptor) and p.paga_docu_id=ctb.docg_id and ctb.docg_id like 'tran%' and to_date(to_char(fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fi2."' and trim(sp.sopg_bene_ci_rif)=trim(pr.prov_id_rif)  and p.paga_docu_id=ptr.trans_id and ptr.docg_id=sp.sopg_id ".$condicionb." and p.pres_anno<>2008 and to_date(to_char(ctb.fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fecha_in2."' and p.esta_id<>15 and p.esta_id<>2   

union (
select cdi.comp_id as sopg_id, cdi.comp_id as pago_id, cb.ctab_numero as numero_cuenta, to_char(ctb.fecha_saldo, 'DD/MM/YYYY') as fecha_pagado,  '-', coalesce(cdi.nro_referencia,'') as referencia, case when cdi.comp_id in (select docg_id from sai_ctabanco_saldo where docg_id like 'codi%') then 'C' else 'N' end as condicion, coalesce(reg.rcomp_haber,0)-coalesce(reg.rcomp_debe,0)  as monto , coalesce(cdi.comp_comen, '') as comentario
from sai_comp_diario cdi, sai_reng_comp reg, sai_ctabanco cb, sai_ctabanco_saldo ctb
where ctb.docg_id=cdi.comp_id and ctb.docg_id like 'codi%' and to_date(to_char(fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fi2."' and cdi.comp_id=reg.comp_id and reg.cpat_id=cb.cpat_id ".$condicionb." and cdi.esta_id<>15 and to_date(to_char(ctb.fecha_saldo, 'YYYY-MM-DD'), 'YYYY-MM-DD')>='".$fecha_in2."' and cdi.comp_id like 'codi%' )))))) order by 3,4";

$resultado_set=pg_query($conexion,$sql) or die("Error al consultar");  
while($row=pg_fetch_array($resultado_set)) {

?>
<tr class="normal">
  <td height="28" align="center"><span class="link">
<?if (strcmp($i,"0")!=0) {?> 
<input type="checkbox" name="solicitud[]" value="<?php echo $row['sopg_id'];?>" />
<?}?><?php echo $documento ;?></span></td>
						  <td height="28" align="center"><span class="link"><?php echo $row['pago_id'];?></span></td>
						  <td height="28" align="center"><span class="link"><?php echo $row["numero_cuenta"].' '.substr($rowpa['ctab_numero'], 10) ;?></span></td>
						<td align="center"><span class="peq"><?php echo $row['fecha_pagado'];?></span></td>
						<td align="left" class="normal"><div align="center"><span class="link"><?php echo $row['referencia'] ;?></span></div></td>
						<td align="left" class="normal"><div align="right"><span class="link"><?php echo  $row['beneficiario']?></span></div></td>
						<td align="left" class="normal"><div align="right"><span class="link"><?php echo  $row['comentario']	 ?></span></div></td>					
						<td align="left" class="normal"><div align="right"><span class="link"><?php echo number_format($saldoInicial,2,'.',','); ?></span></div></td>
<?php
	 if ($row["monto"]>0) {
	 	$monto=$row["monto"]*-1;
		$saldo_inicial = $saldo_inicial + $monto; 	
		$saldo_final = $saldo_final + $monto; 	
	 }
	 else {
	 	$monto="";
		$saldo_inicial = $saldo_inicial - $row["monto"];	 	
		$saldo_final = $saldo_final - $row["monto"];	 	
	 }
	 ?>
	<td><span class="peq"><?php echo number_format($monto*-1,2,',','.');?></span></td>
	<?php
	 if ($row["monto"]<0) $monto=$row["monto"];
	 else $monto="";
	 ?>	
	<td><span class="peq"><?php echo number_format($monto*-1,2,',','.');?></span></td>
	<td><span class="peq"><?php echo number_format($saldo_final,2,',','.');?></span></td>
	</tr>	
<?php $i=$i+1;}?>	
</table> 
<div align="center">
<input type="button" value="Anular" onclick="anular();"/>
</div>
<?php } ?>
</form>
</body>
</html>
<?php pg_close($conexion);?>