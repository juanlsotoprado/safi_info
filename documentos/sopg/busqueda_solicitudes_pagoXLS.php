<?php 
  ob_start();
  session_start();
  require_once("../../includes/excel.php");
  require_once("../../includes/excel-ext.php");
  require_once("../../includes/conexion.php");
  require_once("../../includes/fechas.php");

  if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
  }
  
  $usuario = $_SESSION['login'];
  $user_perfil_id = $_SESSION['user_perfil_id'];

if ($_POST['hid_validar']==2) {
	$fecha_in=trim($_POST['txt_inicio']);
	$fecha_fi=trim($_POST['hid_hasta_itin']); 
	$fecha_ini2=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fin2=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";

	
	 $sql_fecha=" ";
     if ($_POST['txt_inicio']!=''){
	 $sql_fecha=" and to_date(to_char(sp.sopg_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini2."'
	 and to_date(to_char(sp.sopg_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin2."' ";
	}
	
    if ($_POST['estado']==15){
	 $sql_estado=" and sp.esta_id=15";
	}else{
		$sql_estado=" and sp.esta_id<>15";
	}
	
	$sql_comp="";
	if ($_POST['compromiso']<>'comp-'){
	 $sql_comp=" and sp.comp_id='".$_POST['compromiso']."'";
	}
	
	if ($_POST['tipo_solicitud']=='-') 
		$tipo_sol="%";
	else $tipo_sol=$_POST['tipo_solicitud'];
	
	if ($_POST['edo_vzla']<>0)
		$sql_edo_vzla=" and sp.edo_vzla='".$_POST['edo_vzla']."'";
	
	$proyecto="";
	if (strlen($_POST['proyac'])>8) {
	  list( $proy, $especif ) = split( ':::', $_POST['proyac'] );
	  $proyecto = " and sopg_acc_pp='".$proy."' and sopg_acc_esp='".$especif."' ";
    }
    
    if (strlen($_POST['txt_cod']) < 7) {
	  
    	$_POST['txt_cod'] = '';
    }
   
    
    
	$sql_or="SELECT sp.comp_id,sp.sopg_id,edov.edo_nombre, esta_nombre, sp.numero_reserva, initcap(em.empl_nombres)||' ' ||initcap(em.empl_apellidos) as analista, 
    sp.sopg_monto as monto_bruto,sopg_fecha, sp.sopg_observacion, sopg_bene_tp, sopg_bene_ci_rif,
    nombre_sol,depe_nombre,sopg_sub_espe,sopg_acc_esp,sopg_acc_pp,sopg_tipo_impu,spi.pres_anno,spi.sopg_monto,sopg_monto_exento,
    sc.cpat_id,initcap(e2.empl_nombres)||' ' ||initcap(e2.empl_apellidos) as beneficiario,
    CASE WHEN length(dg.perf_id_act)<2 THEN 'Finalizado' ELSE (select carg_nombre from sai_cargo where carg_fundacion=substr(dg.perf_id_act,0,3)) END as ubicacion_actual,
			spc.pgch_id,
			sch.nro_cheque,		    	
			scd.comp_id AS coda
	FROM sai_empleado em , sai_tipo_solicitud ts, sai_dependenci dp,sai_estado edo,sai_edos_venezuela edov, 
	sai_sol_pago_imputa spi,sai_convertidor sc, sai_empleado e2, sai_doc_genera dg, 
			sai_sol_pago sp
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
    WHERE 
    sp.usua_login=em.empl_cedula   AND sp.edo_vzla= edov.edo_id and sopg_tp_solicitud=ts.id_sol and dp.depe_id=depe_solicitante and 
    
    
    spi.sopg_id=sp.sopg_id and sc.part_id=sopg_sub_espe and dg.docg_id=sp.sopg_id and
    sp.sopg_bene_ci_rif=e2.empl_cedula and  (upper(e2.empl_nombres) || ' ' || upper(e2.empl_apellidos) like upper('%".trim($_POST['txt_beneficiario'])."%') or e2.empl_cedula like '%".trim($_POST['txt_beneficiario'])."%') and
    edo.esta_id=sp.esta_id $sql_fecha $sql_estado $sql_edo_vzla and sp.numero_reserva like '".$_POST['txt_reserva']."%' 
	and depe_solicitante like '".$_POST['dependencia']."%'  and sp.sopg_id like '%".$_POST['txt_cod']."'
	and sp.usua_login like '".$_POST['cmb_analista']."%' and sopg_tp_solicitud like '".$tipo_sol."' $sql_comp $proyecto
	
	union
	
	SELECT sp.comp_id,sp.sopg_id,edov.edo_nombre, esta_nombre, sp.numero_reserva, initcap(em.empl_nombres)||' ' ||initcap(em.empl_apellidos) as analista, 
    sp.sopg_monto as monto_bruto, sopg_fecha, sp.sopg_observacion, sopg_bene_tp, sopg_bene_ci_rif,
    nombre_sol,depe_nombre,sopg_sub_espe,sopg_acc_esp,sopg_acc_pp,sopg_tipo_impu,spi.pres_anno,spi.sopg_monto,sopg_monto_exento,
    sc.cpat_id,initcap(v.benvi_nombres)||' ' ||initcap(v.benvi_apellidos) as beneficiario, 
    CASE WHEN length(dg.perf_id_act)<2 THEN 'Finalizado' ELSE (select carg_nombre from sai_cargo where carg_fundacion=substr(dg.perf_id_act,0,3)) END as ubicacion_actual, 
    		spc.pgch_id,
			sch.nro_cheque,		    	
			scd.comp_id AS coda
	FROM sai_empleado em , sai_tipo_solicitud ts, sai_dependenci dp,sai_estado edo, sai_edos_venezuela edov,
	sai_sol_pago_imputa spi,sai_convertidor sc, sai_viat_benef v, sai_doc_genera dg, 
			sai_sol_pago sp
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
    WHERE 
    sp.usua_login=em.empl_cedula   AND sp.edo_vzla= edov.edo_id and sopg_tp_solicitud=ts.id_sol and dp.depe_id=depe_solicitante and 
    spi.sopg_id=sp.sopg_id and sc.part_id=sopg_sub_espe and dg.docg_id=sp.sopg_id and
    sp.sopg_bene_ci_rif=v.benvi_cedula and  (upper(v.benvi_nombres) || ' ' || upper(v.benvi_apellidos) like upper('%".$_POST['txt_beneficiario']."%') or v.benvi_cedula like '%".$_POST['txt_beneficiario']."%')
    and v.benvi_cedula not in (select empl_cedula from sai_empleado) and
    edo.esta_id=sp.esta_id $sql_fecha $sql_estado $sql_edo_vzla and sp.numero_reserva like '".$_POST['txt_reserva']."%' 
	and depe_solicitante like '".$_POST['dependencia']."%'  and sp.sopg_id like '%".$_POST['txt_cod']."'
	and sp.usua_login like '".$_POST['cmb_analista']."%' and sopg_tp_solicitud like '".$tipo_sol."' $sql_comp $proyecto 
    	
	union
	
    SELECT sp.comp_id,sp.sopg_id,edov.edo_nombre, esta_nombre, sp.numero_reserva, initcap(em.empl_nombres)||' ' ||initcap(em.empl_apellidos) as analista, 
    sp.sopg_monto, sopg_fecha, sp.sopg_observacion, sopg_bene_tp, sopg_bene_ci_rif,
    nombre_sol,depe_nombre,sopg_sub_espe,sopg_acc_esp,sopg_acc_pp,sopg_tipo_impu,spi.pres_anno,spi.sopg_monto,sopg_monto_exento,
    sc.cpat_id,initcap(p.prov_nombre) as beneficiario, 
    CASE WHEN length(dg.perf_id_act)<2 THEN 'Finalizado' ELSE (select carg_nombre from sai_cargo where carg_fundacion=substr(dg.perf_id_act,0,3)) END as ubicacion_actual,
    		spc.pgch_id,
			sch.nro_cheque,		    	
			scd.comp_id AS coda 
	FROM sai_empleado em , sai_tipo_solicitud ts, sai_dependenci dp,sai_estado edo, sai_edos_venezuela edov,
	sai_sol_pago_imputa spi,sai_convertidor sc, sai_proveedor_nuevo p, sai_doc_genera dg, 
			sai_sol_pago sp
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
    WHERE 
    sp.usua_login=em.empl_cedula   AND sp.edo_vzla= edov.edo_id and sopg_tp_solicitud=ts.id_sol and dp.depe_id=depe_solicitante and 
    spi.sopg_id=sp.sopg_id and sc.part_id=sopg_sub_espe and dg.docg_id=sp.sopg_id and 
    sp.sopg_bene_ci_rif=p.prov_id_rif and  (upper(p.prov_nombre) like upper('%".$_POST['txt_beneficiario']."%') or p.prov_id_rif like '%".$_POST['txt_beneficiario']."%') and
    edo.esta_id=sp.esta_id $sql_fecha $sql_estado $sql_edo_vzla and sp.numero_reserva like '".$_POST['txt_reserva']."%' 
	and depe_solicitante like '".$_POST['dependencia']."%'  and sp.sopg_id like '%".$_POST['txt_cod']."'
	and sp.usua_login like '".$_POST['cmb_analista']."%' and sopg_tp_solicitud like '".$tipo_sol."' $sql_comp $proyecto 
	order by 7 ASC";
	


	$resultado_set_most_pa=pg_query($conexion,$sql_or) or die("Error al consultar las solicitudes de pago");  
	$f = 0;
	$c = 0;
	
	
	$contenido[$f][0]=utf8_decode("RESULTADO DE LA BÙSQUEDA DE LAS SOLICITUDES DE PAGO");
	
	
		
	$f++;
	
	$contenido[$f][$c]=utf8_decode("Código del Documento");$c++;
	$contenido[$f][$c]=utf8_decode("Fecha de la Solicitud");$c++;
	$contenido[$f][$c]=utf8_decode("Estado");$c++;
	$contenido[$f][$c]=utf8_decode("Estado de Vnzla");$c++;
	$contenido[$f][$c]=utf8_decode("Ubicación Actual");$c++;
	$contenido[$f][$c]=utf8_decode("Fuente de Financiamiento");$c++;
	$contenido[$f][$c]=utf8_decode("Compromiso");$c++;
	$contenido[$f][$c]=utf8_decode("CI/RIF");$c++;
	$contenido[$f][$c]=utf8_decode("Beneficiario");$c++;
	$contenido[$f][$c]=utf8_decode("Detalle");$c++;
	$contenido[$f][$c]=utf8_decode("Tipo de Operación");$c++;
	$contenido[$f][$c]=utf8_decode("Gerencia Solicitante");$c++;
	$contenido[$f][$c]=utf8_decode("Centro Gestor");$c++;
	$contenido[$f][$c]=utf8_decode("Cestro de Costo");$c++;
	$contenido[$f][$c]=utf8_decode("Partida");$c++;
	$contenido[$f][$c]=utf8_decode("Cuenta Contable");$c++;
	$contenido[$f][$c]=utf8_decode("Monto Partida");$c++;
	$contenido[$f][$c]=utf8_decode("Monto Base");$c++;
	$contenido[$f][$c]=utf8_decode("% IVA");$c++;
	$contenido[$f][$c]=utf8_decode("Monto IVA");$c++;
	$contenido[$f][$c]=utf8_decode("Monto Bruto");$c++;
	$contenido[$f][$c]=utf8_decode("Retención IVA");$c++;
	$contenido[$f][$c]=utf8_decode("Retención ISLR");$c++;
	$contenido[$f][$c]=utf8_decode("Retención LTF");$c++;
	$contenido[$f][$c]=utf8_decode("Monto");$c++;
	$contenido[$f][$c]=utf8_decode("Cta. Bancaria");$c++;
	$contenido[$f][$c]=utf8_decode("Analista");$c++;
	$contenido[$f][$c]=utf8_decode("Pgch");$c++;
	$contenido[$f][$c]=utf8_decode("Nro Cheque");$c++;
	$contenido[$f][$c]=utf8_decode("Coda");
	$f++;
	$c=0;
	 while($rowpa=pg_fetch_array($resultado_set_most_pa))  
	 {
	 	
	 		
	 	
		$sopg_actual=$rowpa['sopg_id'];
	    if ($sopg_anterior<>$sopg_actual){
	    $sopg_anterior=$sopg_actual;  	
       	$monto_cheque=0;
	 	$contenido[$f][$c]=$rowpa['sopg_id'];$c++;
	 	$contenido[$f][$c]=cambia_esp(trim($rowpa['sopg_fecha']));$c++;
	 	$contenido[$f][$c]=$rowpa['esta_nombre'];$c++;
	 	$contenido[$f][$c]=$rowpa['edo_nombre'];$c++;
	 	$contenido[$f][$c]=$rowpa['ubicacion_actual'];$c++;
	 	
	    $guion=substr($rowpa['numero_reserva'],2,1);
        if ($guion!='-'){
	     $reserva=substr($rowpa['numero_reserva'],0,2);
		 $sql="SELECT * FROM sai_seleccionar_campo('sai_fuente_fin','fuef_descripcion','fuef_id like'||'''$reserva%''','',2) resultado_set(fuef_descripcion varchar)";
		 $resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar la fuente de financiamiento");
			if($row=pg_fetch_array($resultado_set_most_p)){
 			 $fuente=trim($row['fuef_descripcion']); 
			}
			
        }else{
	    	 $fuente=$rowpa['numero_reserva'];}
    
	 	$contenido[$f][$c]=$fuente;$c++;
	 	$contenido[$f][$c]=$rowpa['comp_id'];$c++;
	 	$contenido[$f][$c]=$rowpa['sopg_bene_ci_rif'];$c++;
	 	$contenido[$f][$c]=$rowpa['beneficiario'];$c++;
	 	$contenido[$f][$c]=$rowpa['sopg_observacion'];$c++;
	 	
	 	$tipo_solicitud="*";
		if ($rowpa['nombre_sol']<>"")
		$tipo_solicitud=trim($rowpa['nombre_sol']);
		
	 	$contenido[$f][$c]=$tipo_solicitud;$c++;
	 	
	 	$dep_solicitante="*";
		if ($rowpa['depe_nombre']<>"")
		$dep_solicitante=trim($rowpa['depe_nombre']);	
		 
		$contenido[$f][$c]=$dep_solicitante;$c++;
		
		$centro_gestor="*";$centro_costo="*";
		$contador=0;
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
		
		$contenido[$f][$c]=$centro_gestor;$c++;
		$contenido[$f][$c]=$centro_costo;$c++;
		$contenido[$f][$c]=$rowpa['sopg_sub_espe'];$c++;
		$contenido[$f][$c]=$rowpa['cpat_id'];$c++;
		$contenido[$f][$c]=(double)($rowpa['sopg_monto']+$rowpa['sopg_monto_exento']);$c++;
		
        $porcentaje="*";$monto_base="*";$monto_iva="*";
	    $sql_p="SELECT * FROM sai_seleccionar_campo('sai_docu_iva', 'ivap_porce,docg_monto_base,docg_monto_iva', 'docg_id='||'''".$rowpa['sopg_id']."''','',2) resultado_set(ivap_porce float4,docg_monto_base float8,docg_monto_iva float8)"; 
		$resultado=pg_query($conexion,$sql_p);
		if ($row=pg_fetch_array($resultado))
		{			
			$porcentaje=trim($row['ivap_porce']);
			$monto_base=trim($row['docg_monto_base']);				
			$monto_iva=trim($row['docg_monto_iva']);
		}
		
		
		$contenido[$f][$c]=(double)($monto_base);$c++;
		$contenido[$f][$c]=$porcentaje;$c++;
		$contenido[$f][$c]=(double)($monto_iva);$c++;
		$contenido[$f][$c]=(double)($rowpa['monto_bruto']);$c++;
		
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
		
		
		$contenido[$f][$c]=(double)($iva);$c++;
		$contenido[$f][$c]=(double)($islr);$c++;
		$contenido[$f][$c]=(double)($ltf);$c++;
		
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
		
		if ($monto_cheque==0){
		 $contenido[$f][$c]="--";$c++;
		}else{
		$contenido[$f][$c]=(double)($monto_cheque);$c++;}
						
		$contenido[$f][$c]=$cuenta;$c++;
		$contenido[$f][$c]=$rowpa['analista'];$c++;
		$contenido[$f][$c]=$rowpa['pgch_id'];$c++;
		$contenido[$f][$c]=$rowpa['nro_cheque'];$c++;
		$contenido[$f][$c]=$rowpa['coda'];$c++;
		
		$f++;
	    $c=0;
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


		$vacio='';
		
		$contenido[$f][$c]=$rowpa['sopg_id'];$c++;
	 	$contenido[$f][$c]=cambia_esp(trim($rowpa['sopg_fecha']));$c++;
	 	$contenido[$f][$c]=$rowpa['esta_nombre'];$c++;
	 	$contenido[$f][$c]=$rowpa['edo_nombre'];$c++;
	 	$contenido[$f][$c]=$rowpa['ubicacion_actual'];$c++;
	 	$contenido[$f][$c]=$fuente;$c++;
	 	$contenido[$f][$c]=$rowpa['comp_id'];$c++;
	 	$contenido[$f][$c]=$rowpa['sopg_bene_ci_rif'];$c++;
	 	$contenido[$f][$c]=$rowpa['beneficiario'];$c++;
	 	$contenido[$f][$c]=$vacio;$c++;
	 	$contenido[$f][$c]=$rowpa['nombre_sol'];$c++;
	 	$contenido[$f][$c]=$rowpa['depe_nombre'];$c++;
	 	
		$contenido[$f][$c]=$centro_gestor;$c++;
		$contenido[$f][$c]=$centro_costo;$c++;
		$contenido[$f][$c]=$rowpa['sopg_sub_espe'];$c++;
		$contenido[$f][$c]=$rowpa['cpat_id'];$c++;
		$contenido[$f][$c]=(double)($rowpa['sopg_monto']+$rowpa['sopg_monto_exento']);$c++;

		$contenido[$f][$c]=$vacio;$c++;
		$contenido[$f][$c]=$vacio;$c++;
		$contenido[$f][$c]=$vacio;$c++;
		$contenido[$f][$c]=$vacio;$c++;
		$contenido[$f][$c]=$vacio;$c++;
		$contenido[$f][$c]=$vacio;$c++;
	    $contenido[$f][$c]=$vacio;$c++;
		$contenido[$f][$c]=$vacio;$c++;
		$contenido[$f][$c]=$vacio;$c++;
		$contenido[$f][$c]=$vacio;$c++;
		$contenido[$f][$c]=$vacio;$c++;
		$contenido[$f][$c]=$vacio;$c++;
	 	$f++;
	    $c=0;
		  
		  
	     }
	 }//fin while query
}

    

createExcel("solicitudes-pago.xls",$contenido);
?>
