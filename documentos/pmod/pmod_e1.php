<?php 
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
$pres_anno = $_SESSION['an_o_presupuesto'];
$pres_anno = 2014;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Documento sin t&iacute;tulo</title>
<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body>
<table width="850" border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas">
         <tr class="td_gray">
            <td><div align="center" class="normalNegroNegrita">INGRESAR MODIFICACI&Oacute;N PRESUPUESTARIA<br></div></td>          </tr>
          <tr>
	<?php

	$largo = $_POST["hid_largo"];
	$fecha_in=trim($_POST['hid_hasta_itin']);
	if($fecha_in!=''){
	 $fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	}else{
		$fecha_ini="";
	}
	//datos fijos

	for($i=0;$i<$largo;$i++){
		$a[$i]=$_POST["rb_ac_proy".$i];
		$b[$i]=$_POST["txt_id_p_ac".$i];
		$c[$i]=$_POST["txt_id_acesp".$i];
		$d[$i]=$_POST["txt_id_depe".$i];
		$e[$i]=$_POST["txt_id_pda".$i];
		$f[$i]=$_POST["txt_den_pda".$i];
		$g[$i]=$_POST["rb_ced".$i];
		$h[$i]=str_replace(',','',$_POST["txt_monto_pda".$i]);
	}

	//COMPRUEBA LA DISPONIBILIDAD POR PARTIDA
	$ind=0;
	$indice=0;
	$sw=false;


	for($i=0;$i<$largo;$i++){
		$cedente=false;
		//VERIFICA SI ES UNA PARTIDA CEDENTE
		if($g[$i]==0){
		 $sql_str="SELECT round(cast(resultado as numeric),2) as resultado FROM sai_pres_consulta_disp(".$pres_anno.",'".$a[$i]."','".$b[$i]."','".$c[$i]."','".$e[$i]."','".$d[$i]."',".$h[$i].") as resultado";
		 $res_q=pg_exec($sql_str);
		 $row=pg_fetch_array($res_q);
		 $cedente=true;

		 //METELA EN UN ARREGLO PARA GENERAR EL APARTADO
		 $aa[$indice]=$_POST["rb_ac_proy".$i];
		 $bb[$indice]=$_POST["txt_id_p_ac".$i];
		 $cc[$indice]=$_POST["txt_id_acesp".$i];
		 $dd[$indice]=$_POST["txt_id_depe".$i];
		 $ee[$indice]=$_POST["txt_id_pda".$i];
		 $ff[$indice]=$_POST["txt_den_pda".$i];
		 $gg[$indice]=$_POST["rb_ced".$i];
		 $hh[$indice]=str_replace(',','',$_POST["txt_monto_pda".$i]);
		 $jj[$indice]='1';
		 $kk[$indice]=" Modificacion Presupuestaria ".$cod_doc;


		 $str_aa=convierte_arreglo($aa);
		 $str_bb=convierte_arreglo($bb);
		 $str_cc=convierte_arreglo($cc);
		 $str_dd=convierte_arreglo($dd);
		 $str_ee=convierte_arreglo($ee);
		 $str_ff=convierte_arreglo($ff);
		 $str_gg=convierte_arreglo($gg);
		 $str_hh=convierte_arreglo($hh);
		 $str_jj=convierte_arreglo($jj);
		 $str_kk=convierte_arreglo($kk);

		 $indice = $indice +1;
		 if( $row['resultado']<0){  //Partidas sin disponibilidad
		 	$pda_sob[$ind]=$e[$i];
		 	$sw=true;
		 	$ind=$ind+1;
		 }
		}
	}
	$rb_tp=$_POST['rb_tp'];
	if(((!$sw) && ($rb_tp==3)) || ((!$sw) && ($rb_tp==5)) || (!$sw) && ($rb_tp==2)){
		$dep=$_POST['opt_depe'];

	 $tipoModificacion=$_POST['tipoModificacion'];
	 $motivo=$_POST['txt_motivos'];
	 $str_a=convierte_arreglo($a);
	 $str_b=convierte_arreglo($b);
	 $str_c=convierte_arreglo($c);
	 $str_d=convierte_arreglo($d);
	 $str_e=convierte_arreglo($e);
	 $str_f=convierte_arreglo($f);
	 $str_g=convierte_arreglo($g);
	 $str_h=convierte_arreglo($h);
	 $partidas_sin_fondo=convierte_arreglo($pda_sob);

	 if (($rb_tp==2) && ($pda_sob.length>=1)){
	  $sql_str="SELECT * FROM sai_pres_ingresa0305(".$rb_tp.",".$pres_anno.",'".$dep."','".$str_a."','".$str_b."','".$str_c."','".$str_d."','".$str_e."','".$str_h."','".$str_g."','".trim($motivo)."','".$tipoModificacion."','".$fecha_ini."','".$partidas_sin_fondo."') as resultado";
	 }else{
	 	$sql_str="SELECT * FROM sai_pres_ingresa0305(".$rb_tp.",".$pres_anno.",'".$dep."','".$str_a."','".$str_b."','".$str_c."','".$str_d."','".$str_e."','".$str_h."','".$str_g."','".trim($motivo)."','".$tipoModificacion."','".$fecha_ini."') as resultado";
	 }
	/* echo $rb_tp;
	 echo $sql_str;*/
	 $res_q=pg_exec($sql_str);
	 $msg='Modificaci&oacute;n Presupuestaria registrada con el c&oacute;digo: ';
	 if ($res_q) {

	 	$rs_forma=pg_fetch_array($res_q);
	 	$cod_doc=$rs_forma['resultado'];
	 	$por_proyecto = 0;
	 	$tipo_cadena = 0;
	 	$estado_doc=10;
	 	$prioridad_doc=1;
		$request_codigo_documento=$request_id_tipo_documento;

	 	$sql = "SELECT * FROM sai_buscar_grupos_perfil('$user_perfil_id') as resultado(grupo_id int4)";

	 	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
			while ($row = pg_fetch_array($resultado)) {
				$user_grupos_id .= ",''".trim($row["grupo_id"])."''";
			}
			//Borrar 1era coma
			$user_grupos_id = "{".substr($user_grupos_id,1)."}";

			//Buscar las opciones seg�n la cadena 	
			$sql_op = "SELECT * FROM sai_buscar_opciones_cadena('$request_id_tipo_documento',1,'$user_grupos_id',$por_proyecto,'',$tipo_cadena) as resultado(wfop_id int4, wfob_id_sig int4, wfca_id_hijo int4, wfca_id_padre int4)";
			$resultado = pg_query($conexion,$sql_op) or die("Error al mostrar");
			if ($row = pg_fetch_array($resultado)) {
				$id_objeto_sig_p = trim($row["wfob_id_sig"]);
				$id_hijo_p = trim($row["wfca_id_hijo"]);
			}

			//Buscar grupo que debe realizar la accion siguiente

			$sql = " SELECT * FROM sai_buscar_grupo_obj('$request_id_tipo_documento','$id_objeto_sig_p','$id_hijo_p') as resultado ";
			$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
			if ($row = pg_fetch_array($resultado)) {
				$grupo_general_p = $row["resultado"];
			}
			//Buscar perfiles del grupo
			$perfiles_general_p = "";
			$sql = " SELECT * FROM sai_buscar_perfil_grupo('$grupo_general_p') as resultado ";
			$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
			if ($row = pg_fetch_array($resultado)) {
				$perfiles_general_p = $row["resultado"];
			}
			$grupo_particular_p=buscar_grupo_particular_dependencia($dependencia_solicitante,$perfiles_general_p);

			//Se agrega el registro en la tabla de doc generados (sai_doc_genera)
			$sql = " SELECT * FROM sai_insert_doc_generado('$cod_doc','$id_objeto_sig_p','$id_hijo_p','$user_login','$user_perfil_id',$estado_doc,$prioridad_doc,'$grupo_particular_p','') as resultado ";

			$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
			if ($row = pg_fetch_array($resultado)) {
				$inserto_doc = $row["resultado"];
			}


			include("includes/respaldos_e1.php");
			$sql_str="select * from sai_pres_consulta_0305('".$cod_doc."',".$pres_anno.") as resultado(a varchar, b int2, c timestamp,d text, e varchar, esta_id int4)";

			$result=pg_exec($sql_str);

		 if(!$result){
		 	echo('<tr>');
		 	echo('<td colspan="4">');
		 	echo('<div align="center"><img src="../../imagenes/mano_bad.gif" width="31" height="38"></div>');
		 	echo('</td>');
		 	echo('</tr>');
		 }
		 else{

		 	$row_modif=pg_fetch_array($result);
		 	$ano=substr($row_modif['c'],0,4);
		 	$mes=substr($row_modif['c'],5,2);
		 	$dia=substr($row_modif['c'],8,2);
		 	 
		 	echo("\n");
		 	echo("<SCRIPT LANGUAGE='JavaScript'>\n");
		 	echo("var activar=".$row_modif['b'].";\n");
		 	echo("var sw=1;\n");
		 	echo("</SCRIPT>\n");
		 	echo("\n");
		 	 

	   ?>
<!-- 	<tr>
		<td width="20">
		
		</td>
		<td width="10">&nbsp;</td>
		<td width="296" colspan="2" class="normalNegroNegrita">
		<div align="left"><?php echo($msg.$cod_doc); ?></div>
		</td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4">
		<div align="center"><img src="imagenes/mano_ok.gif" width="31"
			height="38"></div>
		</td>
	</tr>
	<tr>
		<td colspan="4">
		<table width="850" border="0" class="tablaalertas">
			<tr>
				<td>
				<div align="center">
				<table width="850" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
						<div align="center" class="normalNegrita_naranja">Solicitud de
						Modificaci&oacute;n Presupuestaria</div>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
				</div>
				</td>
			</tr> -->
			<tr>
				<td>
				<table width="850" border="0" cellpadding="0" cellspacing="0"
					background="imagenes/fondo_tabla.gif" class="tablaalertas">
					<tr>
						<td colspan="2">&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">
						<table width="200" border="0" align="center" cellpadding="0"
							cellspacing="0" class="tablaalertas">
							<tr>
								<td width="100">
								<div class="normalNegroNegrita">C&oacute;digo:</div>
								</td>
								<td width="100">
								<div align="center" class="normalNegroNegrita"><?php echo($cod_doc); ?></div>
								</td>
							</tr>
							<tr>
								<td>
								<div class="normalNegroNegrita">Fecha:</div>
								</td>
								<td>
								<div align="center" class="normal"><?php echo($dia."-".$mes."-".$ano); ?></div>
								</td>
							</tr>
							<tr>
								<td>
								<div class="normalNegroNegrita">Dependencia:</div>
								</td>
								<td>
								<div align="center" class="normal"><?php echo($row_modif['a']); ?></div>
								</td>
							</tr>
						</table>
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td width="158" class="normalNegrita"><?php if(trim($tipoModificacion)=='A') {echo "Ajuste"; }  if(trim($tipoModificacion)=='M') {echo "Modificaci&oacute;n"; }?>
						</td>

						<td width="193"><span class="normal"> <input id="rb_tp"
							name="rb_tp" type="radio" value="3"> Cr&eacute;dito</span></td>
						<td width="193"><span class="normalNegroNegrita"><span
							class="normal"> <input id="rb_tp" name="rb_tp" type="radio"
							value="5"> Traspaso</span> </span></td>
						<td width="193"><span class="normal"> <input id="rb_tp"
							name="rb_tp" type="radio" value="2"> Disminuci&oacute;n </span></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td class="normalNegrita">&nbsp;</td>
						<td class="normalNegroNegrita">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td class="normalNegrita">&nbsp;</td>
						<td class="normalNegroNegrita">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td><input name="hid_largo" type="hidden" id="hid_largo"> <input
					name="hid_val" type="hidden" id="hid_val"></td>
			</tr>
			<?php
			/*$sqldt_str="select * from sai_pres_consulta_0305_detalles('".$cod_doc."',".$pres_anno.") as resultado(pda varchar,ced_rec_sw bit, depe_id varchar,ac_proy_id varchar, ac_proy_sw bit, monto float8, acesp varchar )";
			//ESTE NO --> $sqldt_str="select * from sai_pres_consulta_0305_detalles('".$cod_doc."',".$pres_anno.") as resultado(pda varchar,ced_rec_sw bit, depe_id varchar,ac_proy_id varchar, ac_proy_sw bit, monto float8, acesp varchar )";

			$result_dt=pg_exec($sqldt_str);*/
			
			$query = 	"SELECT ".
							"part_id as pda, ".
							"f0dt_tipo as ced_rec_sw, ".
							"depe_id, ".
							"f0dt_id_p_ac as ac_proy_id,".
							"f0dt_proy_ac as ac_proy_sw, ".
							"f0dt_monto as monto, ".
							"f0dt_id_acesp as acesp, ".
							"spae.centro_gestor, ".
							"spae.centro_costo ".
						"FROM sai_fo0305_det, sai_proy_a_esp spae ".
						"WHERE ".
							"f030_id = '".$cod_doc."' AND ".
							"f0dt_proy_ac = CAST(1 AS BIT) AND ".
							"f0dt_id_p_ac = spae.proy_id AND ".
							"f0dt_id_acesp = spae.paes_id ".
						"UNION ".
						"SELECT ".
							"part_id as pda, ".
							"f0dt_tipo as ced_rec_sw, ".
							"depe_id, ".
							"f0dt_id_p_ac as ac_proy_id,".
							"f0dt_proy_ac as ac_proy_sw, ".
							"f0dt_monto as monto, ".
							"f0dt_id_acesp as acesp, ".
							"sae.centro_gestor, ".
							"sae.centro_costo ".
						"FROM sai_fo0305_det, sai_acce_esp sae ".
						"WHERE ".
							"f030_id = '".$cod_doc."' AND ".
							"f0dt_proy_ac = CAST(0 AS BIT) AND ".
							"f0dt_id_p_ac = sae.acce_id AND ".
							"f0dt_id_acesp = sae.aces_id ".
						"ORDER BY centro_gestor, centro_costo";
			$result_dt=pg_exec($query);
			if(!$result_dt){
	   			echo("Error Mostrando los detalles");
			}
			?>
			<tr>
				<td>
				<table width="850" border="0" cellpadding="0" cellspacing="0"
					class="tablaalertas" id="tbl_mod">
					<tr class="td_gray">
						<td>
						<div align="center" class="normal">Centro gestor</div>
						</td>
						<td width="70" class="normal">
						<div align="center">Centro costo</div>
						</td>
						<td width="80" class="normal">
						<div align="center">Dependencia</div>
						</td>
						<td width="95">
						<div align="center" class="normal">Partida</div>
						</td>
						<td width="195">
						<div align="center" class="normal">Denominaci&oacute;n</div>
						</td>
						<td width="45" class="normal">
						<div align="center">Tipo</div>
						</td>
						<td width="170">
						<div align="center" class="normal">Monto</div>
						</td>
					</tr>
					<?php
					while($row_dt=pg_fetch_array($result_dt))
		   { //echo($row_dt['ac_proy_sw']);
		    $sql_str3="SELECT * FROM sai_consulta_desc_pda('".$row_dt['pda']."',".$pres_anno.") as detalle";
		   	 
		   	$res=pg_exec($sql_str3);
		   	$row_dt_pda=pg_fetch_array($res);

		   	if($row_dt['ced_rec_sw']==1){
		   		$clase="peqNegrita_naranja";
		   		$tipo="receptora";
		   	}
		   	else{
		   		$clase="peqNegrita_naranja";
		   		$tipo="(cedente)";
		   	}
		   	?>
					<tr>
						<td height="60">
						<!-- <div align="center" class="normalNegroNegrita"><?php echo($row_dt['ac_proy_id']); ?></div> -->
						<div align="center" class="normalNegroNegrita"><?php echo($row_dt['centro_gestor']); ?></div>
						</td>
						<td width="70">
						<!-- <div align="center" class="normalNegroNegrita"><?php echo($row_dt['acesp']); ?></div> -->
						<div align="center" class="normalNegroNegrita"><?php echo($row_dt['centro_costo']); ?></div>
						</td>
						<td width="80">
						<div align="center" class="normalNegroNegrita"><?php echo($row_dt['depe_id']); ?></div>
						</td>
						<td width="95">
						<div align="center" class="normal"><?php echo($row_dt['pda']); ?>
						</div>
						</td>
						<td width="195">
						<div align="center" class="normalNegroNegrita"><?php echo($row_dt_pda['detalle']); ?></div>
						</td>
						<td width="45">
						<div align="center" class="<?php echo($clase);?>"><?php  echo($tipo);?></div>
						</td>
						<td width="170">
						<div align="center" class="normalNegroNegrita"><?php echo($row_dt['monto']); ?></div>
						</td>
					</tr>
					<?php
		   }
		   ?>
					<tr>
						<td colspan="7">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="7">
						<table width="420" align="center">
						<?
						$cod_doc=$codigo;
						include("includes/respaldos_mostrar.php");
						?>
						</table>
						</td>
					</tr>
					<tr>
						<td colspan="7">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="7">
						<div align="center" class="normalNegrita">Exposici&oacute;n
						de Motivos</div>
						</td>
					</tr>
					<tr>
						<td colspan="7">
						<div align="center"><?php echo($motivo);  ?></div>
						</td>
					</tr>
					<tr>
						<td colspan="7">
						<div align="center">
						<!-- <table width="500" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="100">
								<div align="right" class="normalNegrita">
								<div align="center"><span class="normalNegrita">Motivo
								Anulaci&oacute;n</span>:</div>
								</div>
								</td>
								<td width="400"><span class="Estilo1"><strong> <textarea
									name="txt_memo" cols="60" rows="8" class="normal"></textarea> </strong></span></td>
							</tr>
						</table> -->
						</div>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<?php
		 }
	 }
	 else{
	 	?></td>
	</tr>
	<tr>
		<td width="20">
		<div align="right"><img src="imagenes/vineta_azul.gif" width="11"
			height="7"></div>
		</td>
		<td width="10">&nbsp;</td>
		<td width="296" colspan="2" class="normalNegroNegrita">
		<div align="left">Ha ocurrido un error al registrar los datos , <?php echo(pg_errormessage($conexion)); ?></div>
		</td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4">
		<div align="center"><img src="imagenes/mano_bad.gif" width="31"
			height="38"></div>
		</td>
	</tr>
	<?php
	 } //END IF
	}
	//else{ //IF PRINCIPAL

	echo("\n");
	echo("<SCRIPT LANGUAGE='JavaScript'>\n");
	echo("var sw=0;\n");
	echo("</SCRIPT>\n");
	echo("\n");
	?>

	<?php if (($rb_tp==5) && ($sw)){?>
		<tr>
		<td colspan="4">
		<div align="center"><img src="imagenes/mano_bad.gif" width="31"
			height="38"></div>
		</td>
	</tr>
	<tr>
		<td colspan="4">
		<div align="center">No se registr&oacute; el Traspaso por no poseer fondos suficientes</div>
		</td>
	</tr>
	<?php }
	if (count($pda_sob)>=1){?>
	<tr>
		<td colspan="4">
		<div align="center" class="normalNegrita"><?php echo('Las siguientes partidas no poseen fondos suficientes: ');  ?></div>
		</td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<?php
	$largo_arr=count($pda_sob);
	for($i=0;$i<$largo_arr;$i++){
		?>
	<tr>
		<td colspan="4">
		<div align="center" class="normalNegrita"><?php echo($pda_sob[$i]);  ?></div>
		</td>
	</tr>
	<?php
	}}
	?>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>

</table>
	<?php  // pg_close($conexion);?>
</body>
</html>
