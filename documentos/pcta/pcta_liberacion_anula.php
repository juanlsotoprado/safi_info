<?php
  ob_start();
  require_once("../../includes/conexion.php");
	  
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../index.php',false);
   ob_end_flush(); 
   exit;
  }

$codigo = $_REQUEST['codigo'];

$query="select t1.pcta_id, t1.pcta_id_dest, t2.empl_nombres as nbpresenta, pcta_asociado,
t2.empl_apellidos as appresenta, t3.carg_nombre, t4.depe_nombre as depesolicitante, t5.empl_nombres  as nbsolicitante, t5.empl_apellidos as apsolicitante, t7.empl_nombres as nbelabora, 
t7.empl_apellidos as apelabora, EXTRACT(DAY FROM t1.pcta_fecha)||'/'||EXTRACT(month FROM t1.pcta_fecha)||'/'||EXTRACT(Year FROM t1.pcta_fecha) as pcta_fecha,t1.pcta_justificacion, t1.pcta_monto_solicitado, t6.pcas_nombre as asunto
from sai_pcuenta t1, sai_empleado t2, sai_cargo t3, sai_dependenci t4, sai_empleado t5, sai_pcta_asunt t6, sai_empleado t7
where t1.pcta_id=trim('".$codigo."') and t5.depe_cosige=t4.depe_id and t1.pcta_id_remit=t5.empl_cedula and trim(t1.pcta_asunto)=trim(t6.pcas_id) and t1.pcta_presentado_por=t2.empl_cedula and t2.carg_fundacion=t3.carg_fundacion and t1.usua_login=t7.empl_cedula ";


$result=pg_query($conexion,$query);
if($row=pg_fetch_array($result))
{
	$remitente=$row["nbsolicitante"]." ".$row["apsolicitante"];
	$elabora=$row["nbelabora"]." ".$row["apelabora"];
	$presentado=$row["nbpresenta"]." ".$row["appresenta"];
	$depesolicitante=$row["depesolicitante"];
	$fecha=$row["pcta_fecha"];
	$justificacion=$row["pcta_justificacion"];
    $monto=$row["pcta_monto_solicitado"];
	$asunto=$row["asunto"];
    $pcta_asociado=$row['pcta_asociado'];
	
	
    //Buscar las Imputaciones
	$total_imputacion=0;
	 
	  $sql= " Select * from sai_buscar_pcta_imputacion('".trim($codigo)."') as result ";
	  $sql.= " (pcta_id varchar, pcta_acc_pp varchar, pcta_acc_esp varchar, depe_id varchar, pcta_sub_espe varchar, pcta_monto float8, tipo bit)";
	  $resultado_set= pg_exec($conexion ,$sql);
	  $valido=$resultado_set;
	  
	  if ($resultado_set)
  	  {
		$total_imputacion=pg_num_rows($resultado_set);
 	    $monto_solicitado=0;
		$i=0;
		while($row=pg_fetch_array($resultado_set))	
		{
				$matriz_imputacion[$i]=trim($row['tipo']);
				$matriz_acc_pp[$i]=trim($row['pcta_acc_pp']); 
				$matriz_acc_esp[$i]=trim($row['pcta_acc_esp']);
				$matriz_sub_esp[$i]=trim($row['pcta_sub_espe']);
				$matriz_uel[$i]=trim($row['depe_id']); 
				$matriz_monto[$i]=trim($row['pcta_monto']); 
		 		$monto_solicitado=$monto_solicitado+$row['pcta_monto'];
				$i++;
		}
	  }
?>
<script type="text/javascript">
function anular(){
document.form.opcion.value="2";
document.form.submit();
	
}
</script>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<form name="form" method="post" action="pcta_liberacion_anula.php">
<input type="hidden" name="opcion" value="1">
<input type="hidden" name="pcta_asociado" value="<?=$pcta_asociado;?>">
<table width="485" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr  class="normal"> 
	<td height="15" colspan="2" valign="midden" class="td_gray">
	  <span class="normalNegroNegrita"><strong>LIBERACION DEL PUNTO DE CUENTA</strong></span>		</td>
  </tr>
  <tr> 
	<td height="28" colspan="2" valign="midden" class="normal" align="center"> 
	  <strong> Punto de Cuenta: <?php echo $codigo; ?>
	  <input type="hidden" name="pcta_id" value="<?= $codigo;?>"></strong>			</td>
  </tr>
  <tr>
	<td width="127"><div align="left" class="normal"><strong> Fecha: </strong></div></td>
    <td width="346"><div align="left" class="normalNegro"><?	echo($fecha);	?></div></td>
  </tr>
  <tr>
	<td width="127"><div class="normal"><strong> Elaborado Por: </strong></div></td>
	<td height="12" align="left"><div align="left" class="normalNegro"><? echo($elabora);?></div>		</td>
  </tr>
  <tr>
	<td height="32" align="left"><div align="left" class="normal"><strong>Solicitado Por:</strong></div></td>
    <td height="12" align="left"><div align="left" class="normalNegro"><? echo $remitente;?></div>		</td>
  </tr>
  <tr>
    <td><div class="normal"><strong>Unidad/Dependencia:</strong></div></td>
	<td><span class="normalNegro"><? echo $depesolicitante;?></span></td></tr>
  <tr>
	<td height="35"><div align="left" class="normal"> <strong>Asunto:</strong></div></td>
	<td><span class="normalNegro">
	<?	echo $asunto;?></span></td>
  </tr>
  <tr>
    <td><div class="normal"><strong>Justificaci&oacute;n:</strong></div></td>
	<td><span class="normalNegro"><?echo $justificacion;?></span></td></tr>
  <tr>
    <td><div class="normal"><strong>Monto Solicitado:</strong></div></td>
	<td><span class="normalNegro"><?echo $monto;?></span></td></tr>
	<br>
  <tr>
	<td colspan="3"><br>
	  <table width="60%" border="0"  class="tablaalertas" align="center">
        <tr class="td_gray">
          <td  align="center" class="normalNegroNegrita">Imputaci&oacute;n presupuestaria </td>
        </tr>
	    <tr>
          <td class="normal" align="center">
            <table width="100%" border="0">
              <tr>
                <td  class="peqNegrita" align="left"><div align="center">ACC.C/PP</div></td>
                <td  class="peqNegrita" align="left"><div align="center">Acci&oacute;n espec&iacute;fica </div></td>
                <td width="10%" align="left"  class="peqNegrita"><div align="center">Dependencia</div></td>
                <td width="15%" align="left"  class="peqNegrita"><div align="center">Partida</div></td>
                <td width="39%" align="left"  class="peqNegrita"><div align="center">Monto </div></td>
              </tr>
              <tr>
              <?php
		        for ($ii=0; $ii<$total_imputacion; $ii++){?>
                  <td  class="peq" align="left" width="17%">
                    <div align="center"><input type="text" class="normalNegro" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly="true"/></div></td>
                  <td  class="peq" align="left" width="19%">
                    <div align="center"><input type="texto" class="normalNegro" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="true"/></div></td>
                  <td  class="peq" align="left" width="10%">
                    <div align="center"><input type="text" class="normalNegro" size="10" maxlength="10"   valign="right"  align="right" value="<?php echo $matriz_uel[$ii];?>" readonly="true"/></div></td>
                  <td  class="peq" align="left" width="15%">
                    <div align="center"><input type="text" class="normalNegro" size="15" maxlength="15" value="<?php echo $matriz_sub_esp[$ii];?>" /></div></td>
                  <td class="peq" align="right" width="39%">
                    <input type="text" class="normalNegro" size="25" maxlength="25" value="<?php echo  number_format($matriz_monto[$ii],2,'.',',');?>" readonly="true" /></td>
                </tr>
                <?php 
		       } ?>
            </table></td>
          </tr></table><br>
      <tr>
	<td></td></tr>
  <tr> 
   	 <td><div class="normal"><strong>Motivo Anulaci&oacute;n:</strong></div></td>
	<td><span class="normalNegro"><textarea name="contenido_memo" cols="50" rows="8" class="normalNegro"></textarea></span></td></tr>
  </tr>
  <tr> 
	<td height="18" colspan="2"><div align="center"><input type="button" value="Anular" onclick="javascript:anular();"></div></td>
  </tr>

</table><?php }?>
</form>
<form name="form1" method="post">
<?php 
if ($_REQUEST['opcion']==2){
	$cod_doc = $_REQUEST['pcta_id'];
	$id_pcta_asociado=$_REQUEST['pcta_asociado'];
	$memo_contenido=trim($_POST['contenido_memo']);
	 if ($memo_contenido==""){
 	  $memo_contenido="No Especificado";
     }
     
	$sql="update sai_pcuenta set esta_id=15 where pcta_id='".$cod_doc."'";
	$resultado_set = pg_exec($conexion ,$sql) or die("Error al anular el punto de cuenta");
	$sql="update sai_doc_genera set esta_id=15 where docg_id='".$cod_doc."'";
	$resultado_set = pg_exec($conexion ,$sql) or die("Error al anular el punto de cuenta");
	
	$sql="delete from sai_pcta_imputa_traza where pcta_id='".$cod_doc."'";
	$resultado_set = pg_exec($conexion ,$sql) or die("Error al anular la traza del punto de cuenta");
	$query_memo=utf8_decode("select * from sai_insert_memo('".$_SESSION['login']."','".$_SESSION['user_depe_id']."','".$memo_contenido."', 'Anulación Liberación','0', '0','0','',0, 0, '0', '','".$cod_doc."') as memo_id");
	$resultado_set = pg_exec($conexion ,$query_memo);
	
	//Se reintegra la disponibilidad al pcta
    $sql= " Select * from sai_buscar_pcta_imputacion('".trim($cod_doc)."') as result ";
	$sql.= " (pcta_id varchar, pcta_acc_pp varchar, pcta_acc_esp varchar, depe_id varchar, pcta_sub_espe varchar, pcta_monto float8, tipo bit)";
	$resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
	  
	if ($resultado_set)
  	{
	 while($row=pg_fetch_array($resultado_set))	
	 {
	  
	  $query="SELECT SUM(monto) as disponible FROM sai_disponibilidad_pcta 
	  WHERE pcta_asociado='".$id_pcta_asociado."' and partida='".$row['pcta_sub_espe']."'";
      $resultado_query= pg_exec($conexion ,$query);
	  if ($row_query=pg_fetch_array($resultado_query))

	   $monto_disponible=$row_query['disponible'] +($row['pcta_monto']*(-1));
	   $sql="UPDATE sai_disponibilidad_pcta set monto=0 WHERE pcta_asociado='".$id_pcta_asociado."' and partida='".$row['pcta_sub_espe']."'";
	   $resultado_sql = pg_exec($conexion ,$sql) or die("Error al actualizar disponibilidad del punto de cuenta");
       $sql="UPDATE sai_disponibilidad_pcta set monto='".$monto_disponible."' WHERE pcta_id='".$cod_doc."' and partida='".$row['pcta_sub_espe']."'";
	   $resultado_sql = pg_exec($conexion ,$sql) or die("Error al actualizar disponibilidad del punto de cuenta");

		}
	  }
	
	echo "<br><div align='center'>"."El punto de cuenta ". $cod_doc. " se ha anulado satisfactoriamente";

}	
?>

</form>
</body>
</html>
<?php pg_close($conexion);?>