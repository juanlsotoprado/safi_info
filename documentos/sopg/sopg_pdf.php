<?php
require_once(dirname(__FILE__) . '/../../init.php');
require_once (SAFI_LIB_PATH . '/general.php');
// Modelo
require_once(SAFI_MODELO_PATH. '/empleado.php');
require_once(SAFI_MODELO_PATH. '/firma.php');

require("../../includes/conexion.php");
if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}

require("../../includes/reporteBasePdf.php"); 
require("../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../includes/html2ps/funciones.php");
require("../../includes/monto_a_letra.php");
require_once("../../includes/fechas.php");

@set_time_limit(10000);
parse_config_file(HTML2PS_DIR.'html2ps.config');

$codigo=$_GET['cod_doc']; 
$tipo=$_GET['tipo'];
$elem_existe=0;

$sql_p="SELECT * FROM sai_sol_pago WHERE sopg_id='".$codigo."'"; 
$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al consultar solicitud de pago");
$valido=$resultado_set_most_p;
if($row=pg_fetch_array($resultado_set_most_p))
{
  $depe_id=trim($row['depe_solicitante']); //Solicitante
  $tp_sol=trim($row['sopg_tp_solicitud']);
  $sopg_monto=trim($row['sopg_monto']); 
  $fecha_crea=cambia_esp(trim($row['sopg_fecha']));
  $pres_anno=trim($row['pres_anno']);
  $esta_id=trim($row['esta_id']);
  $usua_login=trim($row['usua_login']); //Solicitante
  $sopg_bene_ci_rif=trim($row['sopg_bene_ci_rif']);
  $sopg_bene_tp=trim($row['sopg_bene_tp']);
  $sopg_detalle=trim($row['sopg_detalle']);
  $obs=trim($row['sopg_observacion']);
  $factura_num=trim($row['sopg_factura']);
  $factura_control=trim($row['sopg_factu_num_cont']);
  $factura_fecha=cambia_esp(trim($row['sopg_factu_fecha']));
  $comp_id=trim($row['comp_id']);
  $reserva=$row['numero_reserva'];


   $sql="SELECT * FROM sai_seleccionar_campo('sai_tipo_solicitud','nombre_sol','id_sol='||'''$tp_sol''','',2) 
   resultado_set(nombre_sol varchar)"; 
   $resultado_set_most_be=pg_query($conexion,$sql) or die("Error al consultar tipo de solicitud");
   $valido= $resultado_set_most_be;
   if($rowbe=pg_fetch_array($resultado_set_most_be))
   {
	$nombre_sol=$rowbe['nombre_sol'];
   }else{
   	$nombre_sol="";
   }


	  $sql="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_nombre','depe_id='||'''$depe_id''','',2) 
	  resultado_set(depe_nombre varchar)"; 
	  $resultado_set_most_be=pg_query($conexion,$sql) or die("Error al consultar dependencia");
	  if($rowbe=pg_fetch_array($resultado_set_most_be))
	  {
	   $depe_nombre=$rowbe['depe_nombre'];
	  }else{
	   $depe_nombre="";
	  }

	  $sql="SELECT * FROM sai_seleccionar_campo('sai_fuente_fin','fuef_descripcion','fuef_id= '||'''$reserva''','',2) resultado_set(fuef_descripcion varchar)"; 
	  $resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
	  if($row=pg_fetch_array($resultado_set_most_p))
	  {
 	   $fuente=trim($row['fuef_descripcion']); //Solicitante
	  }
  
    //Datos del Solicitante
	$sql_so="select * from sai_buscar_usuario('$usua_login','')
	resultado_set(empl_email varchar, usua_login varchar, usua_activo bool,empl_cedula varchar, empl_nombres varchar,
	empl_apellidos varchar,empl_tlf_ofic varchar,carg_nombre varchar,depe_nombre varchar,depe_id varchar,carg_id varchar)";
	$resultado_set_most_so=pg_query($conexion,$sql_so) or die("Error al consultar partida");
	if($rowso=pg_fetch_array($resultado_set_most_so))
	{
		$email=trim($rowso['empl_email']);
		$cedula=$rowso['empl_cedula'];
		$solicitante=$rowso['empl_nombres'].' '.$rowso['empl_apellidos'];
		$cargo=trim($rowso['carg_nombre']);
		$telefono=trim($rowso['empl_tlf_ofic']);
	}
	
	    //Buscar Nombre del Documento al cual se le asocia la solicitud de pago y el nombre del estado actual
	    $sql_d="select * from sai_buscar_datos_sopg('1',4,'','','$codigo','','',0) 
		resultado_set(docu_nombre varchar, esta_id int4, docg_prioridad int2, esta_nombre varchar)"; 
		$resultado_set_most_d=pg_query($conexion,$sql_d) or die("Error al consultar documento");
		if($rowd=pg_fetch_array($resultado_set_most_d))
		{ 
		    $prioridad=$rowd['docg_prioridad'];
		}
		
	//Buscar datos del benefiario segun sea el tipo (1:sai_empleado 2_sai_proveedor 3:sai_viat_benef)
	if($sopg_bene_tp==1) //Empleado
	{
	 	$sql_be="select * from sai_buscar_datos_sopg('$sopg_bene_ci_rif',1,'','','','','',0) 
		resultado_set(depe_id varchar, depe_nombre varchar,empl_nombres varchar,empl_apellidos varchar)"; 
		$resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar empleado");
		if($rowbe=pg_fetch_array($resultado_set_most_be))
		{
 		$nombre_bene=$rowbe['empl_nombres'].' '.$rowbe['empl_apellidos'];
		}
	}
	else
	   if($sopg_bene_tp==2) //Proveedor
	   {
	       $sql_be="SELECT * FROM sai_seleccionar_campo('sai_proveedor_nuevo','prov_nombre','prov_id_rif='||'''$sopg_bene_ci_rif''','',2) 
		   resultado_set(prov_nombre varchar)"; 
		   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar proveedor");
		   if($rowbe=pg_fetch_array($resultado_set_most_be))
		   {
		   	  $nombre_bene=$rowbe['prov_nombre'];
		   }
	   }
	   else
	       if($sopg_bene_tp==3) //Otro beneficiario
		   {
			   $sql_be="SELECT * FROM sai_seleccionar_campo('sai_viat_benef','benvi_nombres,benvi_apellidos','benvi_cedula='||'''$sopg_bene_ci_rif''','',2) 
			   resultado_set(benvi_nombres varchar,benvi_apellidos varchar)"; 
			   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar otro beneficiario");
			   if($rowbe=pg_fetch_array($resultado_set_most_be))
			   {
				  $nombre_bene=$rowbe['benvi_nombres'].' '.$rowbe['benvi_apellidos'];
				  $apellido_bene=$rowbe['benvi_apellidos'];
			   }
		   }
	
}//fin de consultar solicitud de pago

 //buscar los soportes
   $sql= "select * from sai_buscar_sopg_anexos ('".$codigo ."') as resultado ";
   $sql.= "(sopg_id varchar,soan_factura bit , soan_ordc bit , soan_contrato bit, soan_certificacion bit, ";
   $sql.= " soan_recibo bit, soan_ords bit, soan_pcta bit, soan_gaceta bit , soan_informe bit, soan_estimacion bit, soan_otro bit, soan_otro_deta varchar )"; 
   $resultado_set = pg_exec($conexion ,$sql);
   $valido=$resultado_set;
		if ($resultado_set)
		{
		 $anexos=array(11);
		  for ($i=0; $i<11; $i++)
   			{		 
    	 		$anexos[$i]=0;
   		     }
		  if (pg_num_rows($resultado_set) >0)
		  {
   		 $row = pg_fetch_array($resultado_set); 
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
   /*Consulta la tabla de impuestos IVA por documento*/
if ($valido)
  {
   $docu_base=  $codigo;
   $elem_imp_iva=0;
   
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
			 } //Del While
			 $elem_imp_iva=$ii;
		} //Del set
		if ($iva_xx==0)
		{
		 $subtotal_xx=$sopg_monto;
		}
	}	//Del valido
	

	/*Busqueda del Expediente*/
	$docu_final=$codigo;
 	$doc_anno_press=$pres_anno;
   if($valido) 
   {
  $sql= " Select * from sai_pres_busca_expediente (".$doc_anno_press.",'". $docu_final."') as expediente";
  $resultado_set = pg_exec($conexion ,$sql) ;
  $valido= $resultado_set;
	if ($resultado_set)
	{
	  $row = pg_fetch_array($resultado_set,0);
	  $expediente=trim($row[0]);
	}
	}
	
	/*Busqueda del CAUSADO*/

if ($valido)
  {
  $sql= " Select * from sai_pres_busca_op_exp (".$doc_anno_press.",3,'". $expediente."') as causado";
  $resultado_set = pg_exec($conexion ,$sql) ;
  $valido= $resultado_set;
	if ($resultado_set)
	{
	  $row = pg_fetch_array($resultado_set,0);
	  $causado=trim($row[0]);
	}
	}
	
	 //Buscar las Imputaciones
	 $total_imputacion=0;
	 if ($valido){
	  $sql= " Select * from sai_buscar_sopg_imputacion('".trim($codigo)."') as result ";
	  $sql.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, tipo bit, sopg_monto_exento float8)";
	  $resultado_set= pg_exec($conexion ,$sql);
	  $valido=$resultado_set;
		if ($resultado_set)
  		{
		$total_imputacion=pg_num_rows($resultado_set);
		$i=0;
		while($row=pg_fetch_array($resultado_set))	
			 {
				$matriz_imputacion[$i]=trim($row['tipo']);
				$acc_pp[$i]=trim($row['sopg_acc_pp']); // proy o acc
				$acc_esp[$i]=trim($row['sopg_acc_esp']); // acc esp
				$sub_espe[$i]=trim($row['sopg_sub_espe']); //sub-part
				$uel_id[$i]=trim($row['depe_id']); //depe
				$monto[$i]=trim($row['sopg_monto'])+trim($row['sopg_monto_exento']); //monto
				//$monto_exento[$i]=trim($row['sopg_monto_exento']); //monto
				$i++;
			}
  	     if ($matriz_imputacion[0]==1){ //Por Proyecto
		 $query ="Select * from sai_buscar_centro_gestor_costo_proy('".trim($acc_pp[0])."','".trim($acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
		}else{ //Por Accion Centralizada
		 $query ="Select * from  sai_buscar_centro_gestor_costo_acc('".trim($acc_pp[0])."','".trim($acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
		 }

		$resultado_query= pg_exec($conexion,$query);
		if ($resultado_query){
		   while($row=pg_fetch_array($resultado_query)){
		   $centrog = trim($row['centro_gestor']);
		   $centroc = trim($row['centro_costo']);
		   }
		 }

		}
		}

		
			$tt_neto=0;
			$tt_retencion=0;
			$tt_retencion_otras=0;
			$sql= "select * from sai_buscar_sopg_reten ('".trim($codigo)."') as resultado ";
			$sql.= "(docu_id varchar, impu_id varchar, rete_monto float8,  por_rete float4,";
		    $sql.= "por_imp float4,servicio varchar,monto_base float8)";
			$resultado_set= pg_exec($conexion ,$sql);
			if ($resultado_set){
			 $num_retenciones=pg_num_rows($resultado_set);
			}
			
  		    while($row_rete_doc=pg_fetch_array($resultado_set))	
			{
				$rete_monto_doc=trim($row_rete_doc['rete_monto']);
				$tt_retencion=$rete_monto_doc+$tt_retencion;
			}
			$tt_neto=$sopg_monto-$tt_retencion;
			
			$sql= "select * from sai_buscar_sopg_otras_reten ('".trim($codigo)."') as resultado ";
			$sql.= "(sopg_id varchar, sopg_partida_rete varchar, sopg_ret_monto float8)";
			$resultado_set_otras= pg_exec($conexion ,$sql);
			if ($resultado_set_otras){
			 $num_otras_retenciones=pg_num_rows($resultado_set_otras);
  		    }
 			 while($row_rete_doc=pg_fetch_array($resultado_set_otras))	
			 {
				$monto_rete_otras=trim($row_rete_doc['sopg_ret_monto']);
				$tt_retencion_otras=$monto_rete_otras+$tt_retencion_otras;
			 }
			    $tt_neto2=$tt_neto-$tt_retencion_otras;
			    
			   $monto_en_letras = monto_letra($tt_neto2, " BOLIVARES");

/*$contenido = "<style type='text/css'>
						.Campo{
							 FONT-SIZE: 18px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
						.bordeTabla{
							border: solid 1px #000000;
						}
						.textoTabla{
							FONT-WEIGHT: normal; FONT-SIZE: 18px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
					</style>
		   

*/
$contenido = "<style type='text/css'>
						.nombreCampo{
							 FONT-SIZE: 22px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}

						.nombreCampo2{
							 FONT-SIZE: 22px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
						
						.nombreCampo3{
							 FONT-SIZE: 22px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
					
						.textoTabla{
							FONT-WEIGHT: normal; FONT-SIZE: 22px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none
						}
					</style>
					<table border='0' width='100%'>".
					"<tr><td>".
					"<table border='0' width='400'>".
					" <tr class='nombreCampo3'><td align='center'><b>ORDEN DE PAGO</b></td></tr>".
					" <tr class='nombreCampo3'><td align='center'><b>FORMGG027</b></td></tr>".
					"</table></td>".
					"<td><table border='1' width='400' align='right'>".
					" <tr><td align='left'><b>No: </b>".$codigo."</td><td align='left'><b>Fecha: </b>".$fecha_crea."</td></tr>".
					" <tr><td align='left' colspan='2'><b>No pgch: </b></td></tr>".
					" <tr><td align='left'><b>No cheque: </b></td><td align='left'><b>No. Comp: </b>".$comp_id."</td></tr>".
					" <tr><td align='left' colspan='2'><b>Fte. Financiamiento: </b>".$fuente."</td></tr>".
					"</table></td></tr></table><br>".
		    	
			    "<table border=1 width='100%'>".
				" <tr class='nombreCampo'><td colspan='8' align='center'><b>DATOS DE LA ORDEN DE PAGO</b></td></tr>".
				" <tr class='nombreCampo'><td colspan='8'><b>Direcci&oacute;n u Oficina:</b> ".$depe_nombre."</td></tr>".
				" <tr class='nombreCampo'><td colspan='7'><b>Nombre o Raz&oacute;n Social del Beneficiario:</b> </td><td width='12'><b>C.I o R.I.F.</b> </td></tr>".
				" <tr class='nombreCampo2'><td colspan='7'>".$nombre_bene."</td><td  width='12'>".$sopg_bene_ci_rif."</td></tr>".
				" <tr class='nombreCampo'><td colspan='8'><b>Tipo de Solicitud:</b>".$nombre_sol."</td></tr>".
				" <tr class='nombreCampo'><td colspan='8'><b>Motivo del Pago: </b>".$sopg_detalle." </td></tr>".
				" <tr class='nombreCampo'><td colspan='2'><b>No. Factura: </b>".$factura_num."</td><td colspan='6'><b>Fecha Factura: </b>".$factura_fecha."</td></tr></table><br>".
				" <table border=1 width='100%'><tr class='nombreCampo'><td colspan='8' align='center'><b>Documentos Anexos</b> </td></tr>".
				" <tr class='nombreCampo'><td colspan='8'>
				<table width='100%'>
				<tr>";
				if ($anexos[0]==1) {
	$contenido.="<td width='5' hight='2' bgcolor='#000000'></td>";
				}else{
	$contenido.="<td width='5' hight='2' bgcolor='#FFFFFF'></td>";
				}
	$contenido.="<td width='10'>Factura</td>"; 
				if ($anexos[1]==1) {
	$contenido.="<td width='5' hight='2' bgcolor='#000000'></td>";
				}else{
	$contenido.="<td width='5' hight='2' bgcolor='#FFFFFF'></td>";
				}				
	$contenido.="<td width='35'>Orden de Compra</td>";
				if ($anexos[2]==1) {
	$contenido.="<td width='5' hight='2' bgcolor='#000000'></td>";
				}else{
	$contenido.="<td width='5' hight='2' bgcolor='#FFFFFF'></td>";
				}				
	$contenido.="<td width='20'>Contrato</td>";
				if ($anexos[3]==1) {
	$contenido.="<td width='5' hight='2' bgcolor='#000000'></td>";
				}else{
	$contenido.="<td width='5' hight='2' bgcolor='#FFFFFF'></td>";
				}	
    $contenido.="<td width='140'>Certificaci&oacute;n Control Perceptivo</td>
				</tr>
				<tr>";
 				if ($anexos[4]==1) {
	$contenido.="<td width='5' hight='2' bgcolor='#000000'></td>";
				}else{
	$contenido.="<td width='5' hight='2' bgcolor='#FFFFFF'></td>";
				}	   
    $contenido.="<td width='10'>Informe o Solicitud de Pago a Cuentas</td>";
 				if ($anexos[9]==1) {
	$contenido.="<td width='5' hight='2' bgcolor='#000000'></td>";
				}else{
	$contenido.="<td width='5' hight='2' bgcolor='#FFFFFF'></td>";
				}	
    $contenido.="<td width='35'>Orden de Servicio</td>";
     			if ($anexos[6]==1) {
	$contenido.="<td width='5' hight='2' bgcolor='#000000'></td>";
				}else{
	$contenido.="<td width='5' hight='2' bgcolor='#FFFFFF'></td>";
				}	   
    $contenido.="<td width='20'>Punto de Cuenta</td>";
    			if ($anexos[7]==1) {
	$contenido.="<td width='5' hight='2' bgcolor='#000000'></td>";
				}else{
	$contenido.="<td width='5' hight='2' bgcolor='#FFFFFF'></td>";
				}	   
    $contenido.="</tr>
				<tr>";
	 			if ($anexos[8]==1) {
	$contenido.="<td width='5' hight='2' bgcolor='#000000'></td>";
				}else{
	$contenido.="<td width='5' hight='2' bgcolor='#FFFFFF'></td>";
				}	   
   
    $contenido.="<td width='20' colspan='3'>Otro: ".$anexos_otros."</td>
  			    </tr>
				</table></td>
				</tr>".
				" <tr class='nombreCampo'><td colspan='8' align='center'><b>Total a Pagar </b></td></tr>".
				" <tr align='center' class='nombreCampo2'>".
				" <td colspan='6'><b>".$monto_en_letras."</b></td>".
				" <td colspan='2' align='right'><b>".number_format($tt_neto2,2,',','.')."</b></td>".
				" </tr>".
				" <tr class='nombreCampo2'><td colspan='8'><b>Observaciones: </b>".ucwords($obs)."</td></tr></table><br>".
				" <table align='center' width='100%' border='1'><tr class='nombreCampo'><td colspan='4' align='center'><b>Imputaci&oacute;n Presupuestaria </b></td></tr>".
				" <tr align='center' class='nombreCampo'>".
								" <td>Proyecto/Acci&oacute;n Centralizada</td>".
								" <td>Acci&oacute;n espec&iacute;fica</td>".
								" <td>Partida/Cuenta contable</td>".
								" <td>Monto (BsF.)</td>".
				" </tr>";

$query = "SELECT ".
			"spi.sopg_monto, ".
			"spi.sopg_sub_espe, ".
			"spi.sopg_monto_exento, ".
			"sae.aces_nombre as centralespnombre, ".
			"sae.centro_gestor as centrogestorac, ".
			"sae.centro_costo as centrocostoac, ".
			"spae.paes_nombre as proyectoespnombre, ".
			"spae.centro_gestor as centrogestorpr, ".
			"spae.centro_costo as centrocostopr, ".
			"sac.acce_denom as centralprinombre, ".
			"sp.proy_titulo as proyectoprinombre ".
		"FROM ".
			"sai_sol_pago_imputa spi ".
			"left outer join sai_acce_esp sae on (spi.sopg_acc_pp=sae.acce_id and spi.sopg_acc_esp=sae.aces_id and spi.pres_anno=sae.pres_anno) ".
			"left outer join sai_ac_central sac on (sae.acce_id=sac.acce_id and spi.pres_anno=sac.pres_anno) ".
			"left outer join sai_proy_a_esp spae on (spi.sopg_acc_pp=spae.proy_id and spi.sopg_acc_esp=spae.paes_id and spi.pres_anno=spae.pres_anno) ".
			"left outer join sai_proyecto sp on (spae.proy_id=sp.proy_id and spi.pres_anno=sp.pre_anno)	".
		"WHERE spi.sopg_id='".$codigo."'";
$total_imp=0;
$result=pg_query($conexion,$query);
while($row=pg_fetch_array($result)) {
	$centralespnombre=$row["centralespnombre"];
	$centralprinombre=$row["centralprinombre"];
	$proyectoespnombre=$row["proyectoespnombre"];
	$proyectoprinombre=$row["proyectoprinombre"];

	$montosubespecifica=$row['sopg_monto'];
	$montosubespecifica2=$row['sopg_monto_exento'];
	$subespecifica=$row["sopg_sub_espe"];
	
	if (substr($subespecifica,0,6)=="4.11.0"){
      $convertidor="SELECT cpat_id FROM sai_convertidor WHERE part_id='".$subespecifica."'";
      $res_convertidor=pg_query($conexion,$convertidor);
      if($row_conv=pg_fetch_array($res_convertidor)){
		$cuenta= $row_conv['cpat_id'];
      }          	
     }else{
     $cuenta=$subespecifica;
     }
	$centrogestorac=$row["centrogestorac"];
	$centrocostoac=$row["centrocostoac"];
	$centrogestorpr=$row["centrogestorpr"];
	$centrocostopr=$row["centrocostopr"];
	$monto=$montosubespecifica+$montosubespecifica2;
	$total_imp=$total_imp+$monto;
	$contenido .= " <tr class='nombreCampo'>"
					." <td align='center'>".$centrogestorac."&nbsp;". $centrogestorpr."</td>"
					." <td align='center'>".$centrocostoac."&nbsp;".$centrocostopr."</td>"
					." <td align='center'>".$cuenta."</td>"
					." <td align='right'>".number_format($monto,2,',','.')."</td>"
				    ." </tr>";
}
	$contenido .= " <tr class='nombreCampo2'>"
					." <td colspan='4' align='right'><b>".number_format($total_imp,2,',','.')."</b></td>"
				    ." </tr></table><br>";
 if ($num_retenciones>0){
$contenido .=		"<table border='1' width='100%'> <tr class='nombreCampo'><td colspan='5' align='center'><b>Retenciones</b></td></tr>".
					" <tr align='center' class='nombreCampo'>".
					" <td>Tipo de Servicio</td>".
					" <td>Retenci&oacute;n</td>".
					" <td>% Retenci&oacute;n</td>".
					" <td>Monto Bs.</td>".
					" <td>Monto Neto del Pago</td>".
					" </tr>";
				

  	/*Consulto las retenciones previas del documento */
	$sql= "select * from sai_buscar_sopg_reten ('".trim($codigo)."') as resultado ";
	$sql.= "(docu_id varchar, impu_id varchar, rete_monto float8,  por_rete float4,";
	$sql.= "por_imp float4,servicio varchar,monto_base float8)";
	$resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
	if ($resultado_set)
  		{
			$elem_existe=pg_num_rows($resultado_set);
			$id_impuesto_doc=array($elem_existe);
			$rete_monto_doc=array($elem_existe);
			$por_rete_doc=array($elem_existe);
			$por_imp_doc=array($elem_existe);
			$servicio_doc=array($elem_existe);
			$tt_retencion=0;
			$ii=0;
			
 			while($row_rete_doc=pg_fetch_array($resultado_set))	
			 {
				$id_impuesto_doc=trim($row_rete_doc['impu_id']);
				$rete_monto_doc=trim($row_rete_doc['rete_monto']);
				$por_rete_doc=trim($row_rete_doc['por_rete']);
				$por_imp_doc=trim($row_rete_doc['por_imp']);
				$servicio_doc=trim($row_rete_doc['servicio']);
			 	$especial="";
				if  ($por_imp_doc>0)
			    {$especial= $por_imp_doc." %";}
			    $contenido .= " <tr class='nombreCampo'>"
					." <td>".$servicio_doc."</td>"
					." <td>".$id_impuesto_doc." ".$especial."</td>"
					." <td align='center'>".$por_rete_doc."</td>"
					." <td align='right'>".number_format($rete_monto_doc,2,',','.')."</td>";
					if ($ii==0){
					 $contenido .=" <td class='nombreCampo2' align='right' rowspan='".$elem_existe."'><b>".number_format($tt_neto,2,',','.')."</b></td>";
					}
				$ii++; 	
		        $contenido .=" </tr>";				    
			}
		}
		$contenido .="</table><br>";
 }
		
 if ($num_otras_retenciones>0){
 		$contenido .=	
					"<table align='center' width='100%' border='1'>".
					" <tr class='nombreCampo'><td colspan='4' align='center'><b>Otras Retenciones</b></td></tr>".
					" <tr align='center' class='nombreCampo'>".
					" <td>Tipo de Retenci&oacute;n</td>".
					" <td>Cuenta</td>".
					" <td>Monto Bs.</td>".
					" <td>Monto Neto del Pago</td>".
					" </tr>";
//cONSULTO LAS OTRAS RETENCIONES
	
 $sql="SELECT sopg_partida_rete,sopg_ret_monto,part_nombre, t3.cpat_id,cpat_nombre
 FROM sai_sol_pago_otra_retencion t1, sai_partida t2 ,sai_convertidor t3,sai_cue_pat t4
 WHERE sopg_id='".$codigo."' AND t1.sopg_partida_rete=t2.part_id and 
 t2.pres_anno='".$_SESSION['an_o_presupuesto']."' 
 AND t3.part_id=t2.part_id AND t3.cpat_id=t4.cpat_id"; 
 $resultado_set_otras= pg_exec($conexion ,$sql);
	$valido=$resultado_set_otras;
	
		if ($resultado_set_otras)
  		{
			$elem_existe_otras=pg_num_rows($resultado_set_otras);
			$monto_rete_otras=array($elem_existe_otras);
			$partida_rete_otras=array($elem_existe_otras);
			$tt_retencion_otras=0;
			$ii=0;
			
			while($row_rete_doc=pg_fetch_array($resultado_set_otras))	
			 {
				$monto_rete_otras=trim($row_rete_doc['sopg_ret_monto']);
				$partida_rete_otras=trim($row_rete_doc['sopg_partida_rete']);
				$tt_retencion_otras=$monto_rete_otras+$tt_retencion_otras;
				$id_cta=$row_rete_doc['cpat_id'];
				$nombre_part=$row_rete_doc['cpat_nombre'];
				/*$sql_be="SELECT * FROM sai_seleccionar_campo('sai_partida','part_nombre','part_id='||'''$partida_rete_otras'' and pres_anno=$pres_anno','',2) resultado_set(part_nombre varchar)"; 
				$resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar la Partida");
				if($rowbe=pg_fetch_array($resultado_set_most_be))
	 			{
				  
  				}*/
			    $contenido .= " <tr class='nombreCampo'>"
					." <td>".$nombre_part."</td>"
					." <td align='center'>".$id_cta."</td>"
					." <td align='right'>".number_format($monto_rete_otras,2,',','.')."</td>";
			 	    if ($ii==0){
					 $contenido .=" <td  class='nombreCampo2' align='right' rowspan='".$elem_existe_otras."'><b>".number_format($tt_neto2,2,',','.')."</b></td>";
					}
				$ii++; 	
			 }
			
		}
		$contenido .=" </tr></table><br>"; 
 }	
$firmas = array();
$firmasSeleccionadas = array();
$firmas[0] = '46450';
$firmas[1] = '65150';
$firmas[2] = '47350';
$firmas[3] = '62452';

$firmasSeleccionadas = SafiModeloFirma::GetFirmaByPerfiles($firmas);
$nombreCOP = "";

// Obtener los datos del coordinador de ordenación de pago
$cedulaCOP = GetConfig('cedulaCoordinadorOrdenacionDePagos');
if($cedulaCOP != null){
	$empleadoCOP = SafiModeloEmpleado::GetEmpleadoByCedula($cedulaCOP);
	if($empleadoCOP != null){
		$nombreCOP = $empleadoCOP->GetNombres()." ".$empleadoCOP->GetApellidos();
	}
}
 
if($tipo=="F"){			
$firma = "<table border='1' width='100%'>".
					"<tr ><td colspan='3' align='center'><b>Firmas</b></td></tr>".
					"<tr align='center' >".
						"<td width='33%'>".
							"<br/><br/>".
							"___________________<br />".$solicitante."<br />Analista de Ordenación de Pago</td>".
						"<td width='33%'>".
							"<br/><br/>".
							"___________________<br />".$nombreCOP."<br />Coordinador Ordenación de Pago</td>".
						"<td width='34%'>".
							"<br/><br/>".
							"___________________<br />".$firmasSeleccionadas['62452']['nombre_empleado']."<br />Jefe Ordenación de Pago y Contabilidad".
						"</td>".
					"</tr>".
					"<!-- <tr><td colspan='3' align='center'><b>Confirmación y Aprobación</b></td></tr> -->".
					"<tr valign='bottom' >".
						"<td align='center'><br/><br/>____________________ <br/>".utf8_encode($firmasSeleccionadas['46450']['nombre_empleado'])."<br/>".utf8_encode($firmasSeleccionadas['46450']['nombre_cargo'])." ".utf8_encode($firmasSeleccionadas['46450']['nombre_dependencia'])."</td>".
						"<td align='center'><br/><br/>____________________ <br/>".utf8_encode($firmasSeleccionadas['47350']['nombre_empleado'])."<br/>".utf8_encode($firmasSeleccionadas['47350']['nombre_cargo'])."</td>".
						"<td align='center'><br/><br/>____________________ <br/>".utf8_encode($firmasSeleccionadas['65150']['nombre_empleado'])."<br/>".utf8_encode($firmasSeleccionadas['65150']['nombre_cargo'])."</td>".
					"</tr>".
					"</table><br/>".

				"<table border='1' width='100%'>".
				"<tr align='center' valign='top'>".
				"<td colspan='4'>Recibí Conforme</td></tr>".
				"<tr align='center' valign='top'>".
						"<td>Apellidos y Nombres<br/><br/><br/></td>".
						"<td>Cedula<br/><br/><br/></td>".
						"<td>Firma<br/><br/><br/></td>".
						"<td>Fecha<br/><br/><br/></td>".
					"</tr>".
				"</table><br/>".
				"<table border='0' width='100%'>".
				"<tr align='left' valign='top'>".
				"<td>Distribución del Formulario</td></tr>".
				"<tr><td>Original: Expediente de Pago</td>".
				"<td> Primera copia: Correlativo Tesorería </td>".
				"<td> Segunda copia: Solicitud de Pago</td></tr></table>";
}else if($tipo=="L"){
$firma = "<table border='1' align='center' width='100%'>".
					"<tr><td align='center' colspan='8'><b>Firmas</b></td></tr>".
					"<tr align='center' >".
						"<td width='33%' colspan='3'>".
							"<!--Elaborado por: --><br /><br />".
							"___________________<br />".$solicitante."<br />Analista de Ordenaci&oacute;n de Pago</td>".
						"<td width='34%' colspan='2'>".
							"<!--Revisado por: --><br /><br />".
							"___________________<br />".$nombreCOP."<br />Coordinador Ordenaci&oacute;n de Pago</td>".
						"<td width='33%' colspan='3'>".
							"<!--Revisado por:--><br/><br/>".
							"___________________<br />".$firmasSeleccionadas['62452']['nombre_empleado']."<br />Jefe Ordenaci&oacute;n de Pago y Contabilidad".
						"</td>".
					"</tr>".
					"<!-- <tr><td colspan='8' align='center'><b>Confirmaci&oacute;n y Aprobaci&oacute;n</b></td></tr> -->".
					"<tr valign='bottom' >".
						"<td align='center' colspan='3' width='33%'><br/><br/>____________________ <br/>".$firmasSeleccionadas['46450']['nombre_empleado']."<br/>".$firmasSeleccionadas['46450']['nombre_cargo']." ".$firmasSeleccionadas['46450']['nombre_dependencia']."</td>".
						"<td align='center'  colspan='2' width='34%'><br/><br/>____________________ <br/>".$firmasSeleccionadas['47350']['nombre_empleado']."<br/>".$firmasSeleccionadas['47350']['nombre_cargo']."</td>".
						"<td align='center' colspan='3' width='33%'><br/><br/>____________________ <br/>".$firmasSeleccionadas['65150']['nombre_empleado']."<br/>".$firmasSeleccionadas['65150']['nombre_cargo']."</td>".
						
					"</tr>".
					"</table><br/>".

				"<table border='1' align='center' width='100%'>".
				"<tr align='center'>".
				"<td colspan='4'>Recib&iacute; Conforme</td></tr>".
				"<tr align='center'>".
						"<td width='40%'>Apellidos y Nombres<br/><br/><br/></td>".
						"<td width='20%' >Cedula de Identidad<br/><br/><br/></td>".
						"<td width='30%' >Firma<br/><br/><br/></td>".
						"<td width='15%' >Fecha<br/><br/><br/></td>".
						/*"<td width='40%' colspan='4'>Apellidos y Nombres<br/><br/><br/></td>".
						"<td width='20%' colspan='1'>Cedula de Identidad<br/><br/><br/></td>".
						"<td width='30%' colspan='2'>Firma<br/><br/><br/></td>".
						"<td width='10%' colspan='1'>Fecha<br/><br/><br/></td>".*/
					"</tr>".
				"</table>".
				"<table border='0' width='100%'>".
				"<tr align='left' valign='top'>".
				"<td>Distribuci&oacute;n del Formulario</td></tr>".
				"<tr><td>Original: Expediente de Pago</td>".
				"<td> Primera copia: Correlativo Tesorer&iacute;a </td>".
				"<td> Segunda copia: Solicitud de Pago</td></tr></table>";
}				
				

				

if($tipo=="F"){
	$footer = "<br/>".$firma;
				/*"<style type='text/css'>
						@page {
					 		@bottom-right {
					 			margin-top: 98mm;
					    		content: 'Página ' counter(page) ' de ' counter(pages);
					  		}
						}
					</style>";*/
	$properties = array("marginBottom" => 95, "footerHtml" => $footer);
}else if($tipo=="L"){
	/*$footer = "<style type='text/css'>
						@page {
					 		@bottom-right {
					    		content: 'Página ' counter(page) ' de ' counter(pages);
					  		}
						}
					</style>";*/
	$contenido .= $firma;
	$footer="";
	$properties = array("marginBottom" => 15, "footerHtml" => $footer);
}
convert_to_pdf($contenido, $properties);
pg_close($conexion);