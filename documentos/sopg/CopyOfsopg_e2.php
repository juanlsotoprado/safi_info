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

	
require_once("includes/fechas.php");
$partida_IVA=trim($_SESSION['part_iva']);//4.03.18.01.00 IVA PRESUPUESTARIO
$partida_IVA_ano_anterior=$_SESSION['part_iva_ano_anterior'];//4.11.05.02.00 IVA CONTABLE

// Leer los anexos
 $anexos=array(11);
 $anexos_otros=trim($_POST['txt_otro']);

 for ($i=0; $i<11; $i++)
   { 
     $anexos[$i]=0;
   }
 if (trim($_POST['chk_factura'])=="on")
	{
		$anexos[0]=1; //Factura
    }
if (trim($_POST['chk_ordc'])=="on")
	{
		$anexos[1]=1; //Orden de Compra
    }
if (trim($_POST['chk_contrato'])=="on")
	{
		$anexos[2]=1; //Contrato
    }
if (trim($_POST['chk_certificacion'])=="on")
	{
		$anexos[3]=1; //Certidicacion del control perceptivo
    }
if (trim($_POST['chk_recibo'])=="on")
	{
		$anexos[4]=1; //Recibo
    }
if (trim($_POST['chk_ords'])=="on")
	{
		$anexos[5]=1; //Orden de Servicio
    }
if (trim($_POST['chk_pcta'])=="on")
	{
		$anexos[6]=1; //Punto de Cuenta
    }	
if (trim($_POST['chk_gaceta'])=="on")
	{
		$anexos[7]=1; //Gaceta Oficial
    }	
if (trim($_POST['chk_informe'])=="on")
	{
		$anexos[8]=1; //Informe o solicitud de pago a cuentas
    }		
if (trim($_POST['chk_estimacion'])=="on")
	{
		$anexos[9]=1; //estimacion o calculo segun RAC
    }	
if (trim($_POST['chk_otro'])=="on")
	{
		$anexos[10]=1; //Otros(especifique)
    }			
		require_once("includes/arreglos_pg.php");
	    $arreglo_anexos=convierte_arreglo($anexos);  //anexos 

  $valido=true;
  $cod_doc = $request_codigo_documento;
  $codigo= $cod_doc;
 require_once("includes/arreglos_pg.php");

  $compromiso=$_POST['comp_id'];
  $sopg_monto=trim($_POST['hid_monto_tot']);  
  $sopg_bene_ci_rif=trim($_POST['hid_bene_ci_rif']); 
  $sopg_bene_tp=trim($_POST['hid_bene_tp']);    
  $sopg_detalle=trim($_POST['txt_detalle']);     
  $numero_reserva=trim($_POST['numero_reserva']);     
  $depe_solicitante=$_POST['dependencia'];
  $tipo_solicitud=$_POST['tipo_sol'];
  $tipo_solicitud=(int)$tipo_solicitud;
       

  if(trim($_POST['hid_otro'])==1)
  {
	$sopg_sustitucion=trim($_POST['hid_susti_fuente']);
	$fuente=trim($_POST['hid_susti_fuente']);
  }
  else
  {$sopg_sustitucion=trim($_POST['slc_sustitucion']);}  
  $sopg_observacion=trim($_POST['txt_observa']);   

	$factura_num=trim($_POST['txt_factura']);
	$factura_fecha=trim($_POST['txt_fecha_factura']);
	$factura_control=trim($_POST['txt_factura_num_control']);
	if ($factura_fecha<>''){
	 $factura_fecha=cambia_ing($factura_fecha);
	}
 //Insertar en tabla de prioridades
  $prioridad=trim($_POST['slc_prioridad']);
   
  
   $disponibilidad=true;
   $pres_anno=trim($_POST['hid_pres_anno']);
   $valido=true;

//Para las imputaciones
 $largo=trim($_POST['hid_largo_total']);
 $total_imputacion=$largo;
  	
	  $j=0;
	  $tp_impu=explode ("*" , trim($_POST['hid_imputa_tipo'])); //tipo imp
	  $tp_imputacion=$tp_impu[$j];
	  $matriz_sub_esp=explode(",",trim($_POST['txt_arreglo_part']));
	  /*$cantidad_partidas=count($matriz_sub_esp_temporal);
	  $h=0; $g=0;$indice=0;
	  while($h<$cantidad_partidas){
	  	if(($matriz_sub_esp_temporal[$h]!=$partida_IVA) && ($matriz_sub_esp_temporal[$h]!='4.11.05.02.00')){
	  	  $matriz_sub_esp[$g]=$matriz_sub_esp_temporal[$h];
	  	  	$g++;$indice=$h;
	  	}
	  	$h++;
	  }*/
	  
	  $matriz_monto= explode(",",trim($_POST['txt_arreglo_mont']));
	  $matriz_monto_exento=explode(",",trim($_POST['txt_arreglo_mont_exento']));
	  $matriz_acc_pp=explode(",",trim($_POST['txt_arreglo_proy']));
	  $matriz_acc_esp=explode(",",trim($_POST['txt_arreglo_acc']));
	  
	for($j=0; $j<$largo; $j++)
	{  
 		$matriz_imputacion[$j]=trim($_POST['chk_tp_imputa']);
		//$matriz_acc_pp[$j]=trim($_POST['txt_cod_imputa']); // proy o acc
		//$matriz_acc_esp[$j]=trim($_POST['txt_cod_accion']); // acc esp
		$matriz_uel[$j]=$_REQUEST['opt_depe']; //depe
	}

	$monto_iva_impu=str_replace(",","",$_POST['txt_monto_iva_tt']);
	
	if ($monto_iva_impu >0)
	{//aki esta el problem si el IVa viene de diferentes Proyectos, Caso Caja Chica
		//echo "TOTAL IMPUTACION ".$total_imputacion;
	  for($j=0; $j<$total_imputacion; $j++)
      {
	  	$valor=$partida_IVA;
	  	$clave = array_search($valor, $matriz_sub_esp);
	 	if (!is_numeric($clave))
	 	{
	 	 $valor=$partida_IVA_ano_anterior;
	  	 $clave = array_search($valor, $matriz_sub_esp);
         if (is_numeric($clave)){
          $matriz_monto[$clave]=str_replace(",","",$monto_iva_impu); //monto del IVA
         }else{
       		   $matriz_acc_pp[$total_imputacion]=$matriz_acc_pp[($total_imputacion-1)]; // proy o acc
			   $matriz_acc_esp[$total_imputacion]=$matriz_acc_esp[($total_imputacion-1)]; // acc esp
			   $matriz_sub_esp[$total_imputacion]=$partida_IVA; //sub-part del IVA
			   $matriz_monto_exento[$total_imputacion]=0;
			   if (($matriz_sub_esp[$total_imputacion]==$partida_IVA) && ($_POST['comp_id']=='N/A')){
		 	    $matriz_sub_esp[$total_imputacion]=$partida_IVA_ano_anterior;	
			   }
		$matriz_uel[$total_imputacion]=$matriz_uel[($total_imputacion-1)]; //depe
		$matriz_monto[$total_imputacion]=str_replace(",","",$monto_iva_impu); //monto del IVA
 		$matriz_imputacion[$total_imputacion]=trim($_POST['chk_tp_imputa']);
		$j++;
		$largo=$j;
         }
	  	}else{

	  	      $matriz_monto[$clave]=str_replace(",","",$monto_iva_impu); //monto del IVA
              if ($_POST['comp_id']=='N/A'){
		       $matriz_sub_esp[$clave]=$partida_IVA_ano_anterior;	
		      }
	  	}
	  }
		
/*		$matriz_acc_pp[$j]=$matriz_acc_pp[($j-1)]; // proy o acc
		$matriz_acc_esp[$j]=$matriz_acc_esp[($j-1)]; // acc esp
		$matriz_sub_esp[$j]=$partida_IVA; //sub-part del IVA
		if (($matriz_sub_esp[$j]==$partida_IVA) && ($_POST['comp_id']=='N/A')){
		 $matriz_sub_esp[$j]=$partida_IVA_ano_anterior;	
		}
		$matriz_uel[$j]=$matriz_uel[($j-1)]; //depe
		$matriz_monto[$j]=str_replace(",","",$monto_iva_impu); //monto del IVA
 		$matriz_imputacion[$j]=trim($_POST['chk_tp_imputa']);
		$j++;
		$largo=$j;*/
		
	}
//	$total_imputacion=$j;


if ($_POST['hid_imputa_tipo']==1){ //Por Proyecto
		 $query ="Select * from sai_buscar_centro_gestor_costo_proy('".trim($matriz_acc_pp[0])."','".trim($matriz_acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
		}else{ //Por Accion Centralizada
		 $query ="Select * from  sai_buscar_centro_gestor_costo_acc('".trim($matriz_acc_pp[0])."','".trim($matriz_acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
		 }

		$resultado_query= pg_exec($conexion,$query);
		if ($resultado_query){
		   while($row=pg_fetch_array($resultado_query)){
		   $centrog = trim($row['centro_gestor']);
		   $centroc = trim($row['centro_costo']);
		   }
		 }

	$arreglo_acc_pp=convierte_arreglo($matriz_acc_pp);  //acc o proy
	$arreglo_acc_esp=convierte_arreglo($matriz_acc_esp); //acc esp
	$arreglo_sub_esp=convierte_arreglo($matriz_sub_esp); //sub part
	$arreglo_monto=convierte_arreglo($matriz_monto); //mont por part
    $arreglo_monto_exento=convierte_arreglo($matriz_monto_exento); //mont por part
	$arreglo_uel=convierte_arreglo($matriz_uel); //depe
	$arreglo_tipo_impu=convierte_arreglo($matriz_imputacion); //tipo imp
	

/*Verifico la disponibilidad */
 $satisfactorio=true;
 $largo_partidas=count($matriz_sub_esp);
 $cont=0;

 while ($cont<$largo_partidas)
   {
   	$j=0;
	$partida=$matriz_sub_esp[$cont];
	$codigo_convertidor='';
	$condicion="";
    if (substr($partida,0,1)=="4")
     $condicion=" part_id= '".$partida."' ";
    else
	 $condicion=" cpat_id= '".$partida."' ";
  
   $sql_e="SELECT part_id,cpat_id, cpat_pasivo_id FROM sai_convertidor WHERE ".$condicion;   
  //      echo $sql_e."<br>";
   $resultado_set=pg_query($conexion,$sql_e) or die("Error al mostrar");
   if($row=pg_fetch_array($resultado_set))
   {
      $codigo_convertidor=trim($row['cpat_id']).$row['cpat_pasivo_id'];
      $codigo_partida[$cont]=trim($row['part_id']);
   }
     if (strlen($codigo_convertidor)<30)
      {      
	   $satisfactorio=false;
	   $sin_convertidor=$partida;
	   $disponibilidad=false;
      }
     $cont=$cont+1;
   }	
   
   $arreglo_partida_temporal=convierte_arreglo($codigo_partida); 
	 $sql_pr= " Select * from sai_buscar_sopg_imputacion('".trim($codigo)."') as result ";
	 $sql_pr.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, sopg_tipo_impu bit, sopg_monto_exento float)";
	 $resultado_set=pg_query($conexion,$sql_pr);
	 if ($resultado_set) 
	  $total_partidas=pg_num_rows($resultado_set);
	  $i=0;
	  $partida=array($total_partidas);
   	  while($row=pg_fetch_array($resultado_set))	
	  {
	   $partida[$i]=trim($row['sopg_sub_espe']); 
	   $monto[$i]=$row['sopg_monto']+$row['sopg_monto_exento']; 
	   $i++;
	  }
 
   if ($_POST['comp_id']<>'N/A')
   { //SI TIENE COMPROMISO ASOCIADO, SE ACTUALIZA LA DISPONIBILIDAD DEL COMPROMISO

	  //PRIMERO SE LIBERA LAS PARTIDAS DEL SOPG ANTES DE LA MODIFICACION,XQ AL MODIFICAR PUEDEN SER MENOS PARTIDAS Y A SU VEZ DIFERENTES
   	  for($j=0; $j<$total_partidas; $j++)
	  {  
	   $query_disponible="SELECT monto as disponible FROM sai_disponibilidad_comp WHERE comp_id='".$compromiso."' and partida='".$partida[$j]."'";
	   $resultado_query = pg_exec($conexion,$query_disponible);
	   if ($row=pg_fetch_array($resultado_query)){
	  	 $disponible=$row['disponible']+$monto[$j];
	  	 $query = "UPDATE sai_disponibilidad_comp set monto='".$disponible."' WHERE comp_id='".$compromiso."' and partida='".$partida[$j]."'";
	  	 $resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al aumentar disponibilidad del Compromiso"));
	    } 
	   }//fin for
	   
	 
	   //SEGUNDO SE VALIDA LA DISPONIBILIDAD DE LAS NUEVAS PARTIDAS DEL SOPG DESPUES DE LA MODIFICACION
	   $disponibilidad_comp=1;
	   $disponible_comp=array($largo_partidas);
	   $montos_disponibles=array($largo_partidas);
	   
	   for($k=0; $k<$largo_partidas; $k++) {
	   	$part_id=$matriz_sub_esp[$k];
	   	$monto_bs=$matriz_monto[$k]+$matriz_monto_exento[$k];
	   	$query_disponible="SELECT monto as disponible FROM sai_disponibilidad_comp WHERE comp_id='".$compromiso."' and partida='".$part_id."'";
	   	$resultado_query = pg_exec($conexion,$query_disponible);

	   	if ($row=pg_fetch_array($resultado_query)){
	 	  $disponible=$row['disponible'];
	 	  $resta=$disponible-$monto_bs;
	 	  if ($resta<0){
	  			$disponibilidad_comp=0;
	  		}else{
	  			$montos_disponibles[$k]=$disponible-$monto_bs;
	  			
	  		}
	     }else{
	     	if ((substr($part_id,0,6)=="4.11.0") || (substr($part_id,0,1)!="4")){//Caso partidas temporales o cuentas contables, no se actualiza nada en sai_disponibilidad_comp
	     		$montos_disponibles[$k]=0.0;
	     	}else{
	     	?>
		  	<script>
			  document.location.href = "mensaje.php?pag=principal.php&msj=LA PARTIDA +<?php echo $part_id." SUBSTRING ".substr($part_id,0,1);?>+ NO SE ENCUENTRA DISPONIBLE EN EL COMPROMISO";
			</script>
	     	
	     	<?php 
	     		
	     	}
	     	
	     }
	   }
	     if ($disponibilidad_comp==0){//SI NO HAY DISPONIBILIDAD, SE REVERSA LOS MONTOS QUE YA FUERON ACTUALIZADOS
	       for($j=0; $j<$total_partidas; $j++)
	  	   {  
	   		 $query_disponible="SELECT monto as disponible FROM sai_disponibilidad_comp WHERE comp_id='".$compromiso."' and partida='".$partida[$j]."'";
	   		 $resultado_query = pg_exec($conexion,$query_disponible);
	   		 if ($row=pg_fetch_array($resultado_query)){
	  	 	   $disponible=$row['disponible']-$monto[$j];
	  	       $query = "UPDATE sai_disponibilidad_comp set monto='".$disponible."' WHERE comp_id='".$compromiso."' and partida='".$partida[$j]."'";
	  	       $resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al aumentar disponibilidad del Compromiso"));
	         } 
	        }
	     }
	   
	    if ($disponibilidad_comp==1){//SI HAY DISPONIBILIDAD, SE ACTUALIZA CON LOS MONTOS DE LA MODIFICACION
	  	 for($k=0; $k<$largo_partidas; $k++) {
	  	  $part_id=$matriz_sub_esp[$k];	
 		  $query = "UPDATE sai_disponibilidad_comp set monto='".$montos_disponibles[$k]."' WHERE comp_id='".$compromiso."' and partida='".$part_id."'";
 		  $resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al disminuir disponibilidad del Compromiso"));
	  	 }
	  	}else{
	  			$disponibilidad=false;
	  		//REDIRECCIONO A UN MENSAJE DE ERROR POR DISPONIBILIDAD?>
	  	<script>
		  document.location.href = "mensaje.php?pag=principal.php&msj=Usted no tiene disponibilidad presupuestaria para realizar esta modificaci\u00F3n";
		</script>
	  	<?php }
	
	
	  
   }//fin COMP asociado al SOPG
   
if ($satisfactorio)
  { 
   
	$sql="select * from sai_modificar_sopg('".$codigo."',".$sopg_monto.",'".$sopg_bene_ci_rif."',
	".$sopg_bene_tp.",'".$sopg_detalle."','".$sopg_observacion."','". $arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_tipo_impu."','".$arreglo_sub_esp;
	$sql.= "','".$arreglo_monto."',".$prioridad.",'".$factura_fecha ."','".$arreglo_uel ."','". $factura_num."','".$factura_control."','".$numero_reserva."','".$pres_anno."','".$arreglo_monto_exento."','".$depe_solicitante."',".$tipo_solicitud.",'".$_POST['comp_id']."',".$_POST['edo_vzla'].",'".$arreglo_partida_temporal."') as resultado_set(int4)";
	//echo $sql;
	$resultado_modif = pg_query($conexion,$sql) or die("Error al modificar la solicitud".$sql);
	$valido=$resultado_modif;
	
	if($resultado_modif)  //VOLVER A DESCOMENTAR MIENTRAS SE CAMBIO DE CONDICION
	{
		$sql = " SELECT * FROM sai_modificar_reserva('$codigo','$numero_reserva') as resultado ";
		$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
			
		$row = pg_fetch_array($resultado_modif); 
		if ($row[0] <> null)
		{
			$valido=true;
			$sql_anexo="Select * from sai_insert_sopg_anexos('".$codigo."','". $arreglo_anexos ."','". $anexos_otros."') as retorno ";
			$resultado_anexo = pg_exec($conexion ,$sql_anexo);
			 $valido=$resultado_anexo;
			 if($valido && $disponibilidad) //Si el documento viene de cero.. se registra el iva por documento
 				 {  
 				 $porcentaje="{".str_replace(",","",$_POST['opc_por_iva'])."}";
  				 $monto_base="{".str_replace(",","",$_POST['txt_monto_subtotal'])."}";
 				 $monto_iva="{".str_replace(",","",$_POST['txt_monto_iva_tt'])."}";
					
 				 $sql ="select * from sai_insert_documento_iva('".trim($codigo)."','";
 				 $sql.=$monto_iva."','". $monto_base."','". $porcentaje."','1') as reto";
 				 $resultado_docu_iva = pg_exec($conexion ,$sql);
 				 $valido=$resultado_docu_iva;
  				 if ($resultado_docu_iva)
				{	
					$row = pg_fetch_array($resultado_docu_iva,0); 
					if ($row['reto']==0) 
					{
					    $valido=false;
					}
				}
			  }
			include("includes/respaldos_e1.php");

			//Buscar datos
			$sql_p="SELECT * FROM sai_sol_pago WHERE sopg_id='".$codigo."'";  
			$resultado_set_most_p=pg_query($conexion,$sql_p);
			$valido=$resultado_set_most_p;
			if($row=pg_fetch_array($resultado_set_most_p))
			{
			  $depe_id=trim($row['depe_solicitante']); //Solicitante
			  $tp_sol=trim($row['sopg_tp_solicitud']);
			  $sopg_monto=trim($row['sopg_monto']); 
			  $sopg_fecha=trim($row['sopg_fecha']);
			  $pres_anno=trim($row['pres_anno']);
			  $esta_id=trim($row['esta_id']);
			  $usua_login=trim($row['usua_login']); //Solicitante
			  $sopg_bene_tp=trim($row['sopg_bene_tp']);

$sql="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_nombre','depe_id='||'''$depe_id''','',2)
resultado_set(depe_nombre varchar)"; 
$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
$valido=$resultado_set_most_p;
if($row=pg_fetch_array($resultado_set_most_p))
{
 $dependencia=trim($row['depe_nombre']); //Solicitante
}
				//Datos del Solicitante
				$sql_so="select * from sai_buscar_usuario('$usua_login','')
				resultado_set(empl_email varchar, usua_login varchar, usua_activo bool,empl_cedula varchar, empl_nombres varchar,
				empl_apellidos varchar,empl_tlf_ofic varchar,carg_nombre varchar,depe_nombre varchar,depe_id varchar,carg_id varchar)";
				$resultado_set_most_so=pg_query($conexion,$sql_so) or die("Error al consultar partida");
				$valido=$resultado_set_most_so;
				if($rowso=pg_fetch_array($resultado_set_most_so))
				{
					$email=trim($rowso['empl_email']);
					$cedula=$rowso['empl_cedula'];
					$solicitante=$rowso['empl_nombres'].' '.$rowso['empl_apellidos'];
					$cargo=trim($rowso['carg_nombre']);
					//$dependencia=trim($rowso['depe_nombre']);
					$telefono=trim($rowso['empl_tlf_ofic']);
				}
			
				//Buscar Nombre del Documento al cual se le asocia la solicitud de pago y el nombre del estado actual
				$sql_d="select * from sai_buscar_datos_sopg('1',4,'','','$codigo','','',0) 
				resultado_set(docu_nombre varchar, esta_id int4, docg_prioridad int2, esta_nombre varchar)"; 
				$resultado_set_most_d=pg_query($conexion,$sql_d);
				$valido=$resultado_set_most_d;
				if($rowd=pg_fetch_array($resultado_set_most_d))
				{
				   $asunto=$rowd['docu_nombre'];
				   $estado_docu=trim($rowd['esta_id']);
				   $estado_nomb=trim($rowd['esta_nombre']);
				   $prioridad=trim($rowd['docg_prioridad']);
				   
				}
			
				//Buscar datos del benefiario segun sea el tipo (1:sai_empleado 2_sai_proveedor 3:sai_viat_benef)
				if($sopg_bene_tp==1) //Empleado
				{
					$sql_bem="select * from sai_buscar_datos_sopg('$sopg_bene_ci_rif',1,'','','','','',0) 
					resultado_set(depe_id varchar, depe_nombre varchar,empl_nombres varchar,empl_apellidos varchar)"; 
					$resultado_set_most_bem=pg_query($conexion,$sql_bem);
					$valido=$resultado_set_most_bem;
					if($rowbem=pg_fetch_array($resultado_set_most_bem))
					{
					   $nombre_bene=$rowbem['empl_nombres'].' '.$rowbem['empl_apellidos'];
					   $depe_nombre_bene=trim($rowbem['depe_nombre']);
					}
				}
				else
				   if($sopg_bene_tp==2) //Proveedor
				   {
					   $sql_be="SELECT * FROM sai_seleccionar_campo('sai_proveedor','prov_nombre','prov_id_rif='||'''$sopg_bene_ci_rif''','',2) 
					   resultado_set(prov_nombre varchar)"; 
					   $resultado_set_most_be=pg_query($conexion,$sql_be);
					   $valido= $resultado_set_most_be;
					   if($rowbe=pg_fetch_array($resultado_set_most_be))
					   {
						  $nombre_bene=$rowbe['prov_nombre'];
						 //La dependencia es la del solicitante (buscarla por el usua_login registrado)
						 $depe_nombre_bene=$dependencia;
					   }
				   }
			   else
				   if($sopg_bene_tp==3) //Otro beneficiario
				   {
					   $sql_be="SELECT * FROM sai_seleccionar_campo('sai_viat_benef','benvi_nombres,benvi_apellidos','benvi_cedula='||'''$sopg_bene_ci_rif''','',2) 
					   resultado_set(benvi_nombres varchar,benvi_apellidos varchar)"; 
					   $resultado_set_most_be=pg_query($conexion,$sql_be);
					   $valido=$resultado_set_most_be;
					   if($rowbe=pg_fetch_array($resultado_set_most_be))
					   {
						  $nombre_bene=$rowbe['benvi_nombres'].' '.$rowbe['benvi_apellidos'];
						 //La dependencia es la del solicitante (buscarla por el usua_login registrado)
						  $depe_nombre_bene=$dependencia;
					   }
				   }
			
				
			}//fin de consultar solicitud de pago
			}
			}
//} //Del if valido y la disponibilidad		COLOCAR AL FINAL	

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
		 $subtotal_xx=trim(str_replace(",","",$_POST['txt_monto_tot']));
		}
		
	
?>
 <?php
 //if($valido && $disponibilidad) //Si el documento viene de cero.. se registra el iva por documento
 //// {  
  $porcentaje="{".str_replace(",","",$_POST['opc_por_iva'])."}";
  $monto_base="{".str_replace(",","",$_POST['txt_monto_subtotal'])."}";
  $monto_iva="{".str_replace(",","",$_POST['txt_monto_iva_tt'])."}";
  
  $sql ="select * from sai_insert_documento_iva('".trim($codigo)."','";
  $sql.=$monto_iva."','". $monto_base."','". $porcentaje."') as reto";
  $resultado_docu_iva = pg_exec($conexion ,$sql);
  $valido=$resultado_docu_iva;
  if ($resultado_docu_iva)
	{	
		$row = pg_fetch_array($resultado_docu_iva,0); 
					if ($row['reto']==0) 
					{
					    $valido=false;
					}
	}
 // }
 
	// La tabla sai_sol_pago_retencion es actualizada en el store procedure sai_insert_documento_iva
	
	$query = "
		SELECT
			sopg_id,
			impu_id,
			sopg_ret_monto,
			sopg_por_rete,
			sopg_por_imp,
			sopg_servicio,
			sopg_monto_base
		FROM
			sai_sol_pago_retencion retenciones
		WHERE
			sopg_id = '".trim($codigo)."'
	";
	
	// Borrar
	error_log("-----------------");
	
	if (($resultado = pg_query($conexion, $query))===false)
	{
		error_log("Error al consultar las rentenciones del sopg = '".trim($codigo)."'");
	} else if (pg_num_rows($resultado) > 0)
	{
		error_log("Hay");
		// Validar si existe factura
		if (trim( $factura_num <> "" ))
		{
			while($row = pg_fetch_array($resultado))
			{
				switch ($row['impu_id']) {
					case 'IVA':
						;
					break;
					
					default:
						$query = "
							UPDATE
								sai_sol_pago_retencion
							SET
								sopg_ret_monto = ".($row['sopg_por_rete']*$_POST['txt_monto_subtotal']/100.0).",
								sopg_monto_base = ".$_POST['txt_monto_subtotal']."
							WHERE
								sopg_id = '".trim($codigo)."'
								AND impu_id = '".$row['impu_id']."'
							";
							error_log("\n\n".$query."\n");
						;
					break;
				}
			}
		} else { // Si no existe factura
			error_log("no hay");
			// Borrar todas las retenciones
			$query = "DELETE FROM sai_sol_pago_retencion WHERE sopg_id = '".trim($codigo)."'";
			error_log("\n\n".$query."\n\n");
		}
	}
	
	
	exit;

?>		
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<title>.:Modificar Solicitud de Pago:.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>
<script language="javascript" src="js/func_montletra.js"></script>

</head>
<body >
<?php 
//if ($disponibilidad && $valido)
//{ 
?>
			<table  align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr>
				<td colspan="3">
					<table width="100%">
					<tr class="td_gray"> 
					<td height="21" colspan="3" valign="midden"><div align="left" class="normalNegroNegrita"> 
					DATOS DEL SOLICITANTE</div></td>
					</tr>
					<tr> 
					<td height="28" colspan="2"><div class="normalNegrita" >Solicitud de Pago N&uacute;mero:</div></td> 
					<td class="normalNegro" width="581"><?php echo $codigo;?></td>
					</tr>
					<tr> 
					<td height="28" colspan="2"><div class="normalNegrita">Solicitante:</div></td>
					<td class="normalNegro"><?php echo $solicitante;?></td>
					</tr>
					<tr> 
					<td height="28" colspan="2"><div  class="normalNegrita">C&eacute;dula de Identidad:</div></td>
					<td class="normalNegro"><?php echo $cedula;?></td>
					</tr>
					<tr> 
				<td height="28" colspan="2"><div class="normalNegrita">Email:</div></td>
				<td class="normalNegro"><?php echo $email;?></td>
				</tr>
					<tr> 
					<td height="28" colspan="2"><div class="normalNegrita">Cargo:</div></td>
					<td class="normalNegro"><?php echo $cargo;?></td>
					</tr>
					<tr> 
					<td height="30" colspan="2"><div class="normalNegrita">Dependencia Solicitante:</div></td>
					<td class="normalNegro"><?php echo $dependencia;?></td>
					</tr>
			<tr> 
			<td height="30" colspan="2"><div class="normalNegrita">Tipo de Solicitud: </div></td>
			<?
			   $sql="SELECT * FROM sai_seleccionar_campo('sai_tipo_solicitud','nombre_sol','id_sol='||'''$tp_sol''','',2) 
			   resultado_set(nombre_sol varchar)"; 
			   $resultado_set_most_be=pg_query($conexion,$sql) or die("Error al consultar tipo de solicitud");
			   $valido= $resultado_set_most_be;
			   if($rowbe=pg_fetch_array($resultado_set_most_be))
			   {
				$nombre_sol=$rowbe['nombre_sol'];
			   }
             ?>
			<td class="normalNegro"><?echo $nombre_sol;?></td>
			</tr>
					<tr> 
					<td height="30" colspan="2"><div class="normalNegrita">Tel&eacute;fono de Ofic.:</div></td>
					<td class="normalNegro"><?php echo $telefono;?></td>
					</tr>
					</table>				</td>
			</tr>
			<tr>
				<td colspan="3">
					<table width="100%">
					<tr class="td_gray"> 
					<td height="21" colspan="3" valign="midden"><div align="left" class="normalNegroNegrita"> 
					DATOS DEL BENEFICIARIO</div></td>
					</tr>
					<tr> 
					<td height="29" colspan="2"><div  class="normalNegrita">Beneficiario:</div></td>
					<td  width="581" class="normalNegro"><?php echo $nombre_bene;?></td>
					</tr>
					<tr> 
					<td height="28" colspan="2"><div class="normalNegrita">C.I. o RIF:</div></td>
					<td class="normalNegro"><?php echo $sopg_bene_ci_rif;?></td>
					</tr>
					</table>				</td>
			</tr>
			<tr>
	    <td>
		<table width="100%" class="tablaalertas">
		<tr > 
        <td height="21" colspan="8" valign="midden" class="td_gray"><div align="left" class="normalNegroNegrita"> 
        DOCUMENTOS ANEXOS</div></td>
        </tr>
		
		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_factura" type="checkbox" id="chk_factura"  disabled="true" <?php if ($anexos[0]==1){echo "checked";}?> /></td>
		 <td height="21">Factura</td>
        <td height="21" ><input name="chk_ordc" type="checkbox" id="chk_ordc" disabled="true" <?php if ($anexos[1]==1){echo "checked";}?>/></td>
		<td height="21" >Orden de Compra</td>
        <td height="21" ><input name="chk_contrato" type="checkbox" id="chk_contrato" disabled="true" <?php if ($anexos[2]==1){echo "checked";}?>/></td>
		<td height="21" >Contrato</td>
        <td height="21" ><input name="chk_certificacion" type="checkbox" id="chk_certificacion" disabled="true" <?php if ($anexos[3]==1){echo "checked";}?>/></td>
        <td height="21" >Certificaci&oacute;n del Control Perceptivo</td>
        </tr>
		
		<tr  class="normalNegro"> 
        <td height="21" ><input name="chk_informe" type="checkbox" id="chk_informe" disabled="true" <?php if ($anexos[8]==1){echo "checked";}?>/></td>
		<td height="21">Informe o Solicitud de Pago a Cuentas</td>
        <td height="21" ><input name="chk_ords" type="checkbox" id="chk_ords" disabled="true" <?php if ($anexos[5]==1){echo "checked";}?>/></td>
        <td height="21" >Orden de Servicio</td>
        <td height="21" ><input name="chk_pcta" type="checkbox" id="chk_pcta" disabled="true" <?php if ($anexos[6]==1){echo "checked";}?>/></td>
        <td height="21" >Punto de Cuenta</td>
	    </tr>

		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_otro" type="checkbox" id="chk_otro" disabled="true" <?php if ($anexos[10]==1){echo "checked";}?>/></td>
        <td height="21" >Otro (Especifique)</td>
		<td height="21"  ></td>
        <td height="21" ><input type="text" name="txt_otro" id="txt_otro"  size="25" maxlength="25" value="<?php echo $anexos_otros;?>"   disabled="true"></td>
        </tr>

	 </table>
	 </td>
	 </tr> 
			<tr>
				<td height="176" colspan="3">
					<table width="100%">
					<tr class="td_gray"> 
					<td height="21" colspan="3" valign="midden"><div align="left" class="normalNegroNegrita"> 
					DETALLES DE LA SOLICITUD</div></td>
					
					</tr>
					<?php
				if (trim( $factura_num<>"")){ ?>

				<tr class="normal"> 
				<td height="28" valign="midden" colspan="2"> <div class="normalNegrita">Factura N&deg;
				<input name="txt_factura"  type="text" class="normalNegro" id="txt_factura" size="20" maxlength="20"   valign="right"  align="right"  readonly="true" value="<?php echo $factura_num;?>"/>
				  Fecha:
				  <input name="txt_fecha_factura" type="text" class="normalNegro" id="txt_fecha_factura" size="12" readonly="true"  value="<?php echo $factura_fecha;?>" />
				  N&deg; de Control:
				  <input name="txt_factura_num_control"  type="text" class="normalNegro" id="txt_factura_num_control" size="11" maxlength="10"   valign="right"  align="right"  readonly="true"  value="<?php echo $factura_control;?>"/>
				  </div></td>
				 </tr>
	  		<?php } ?>

		<tr class="normal"> 
		<td height="28" class="normalNegrita"> 
		<div >Prioridad:</div>		</td>
		<td> 
		<select name="slc_prioridad" class ="normalNegro" disabled >
            <option value="1" <?php if ($prioridad==1) { echo "selected"; } ?> >Baja</option>
            <option value="2" <?php if ($prioridad==2) { echo "selected"; } ?>>Media</option>
            <option value="3" <?php if ($prioridad==3) { echo "selected"; } ?>>Alta</option>
        </select>
		</td>
		</tr>
          <tr class="normal"> 
		   <td height="28" class="normalNegrita"><div> Fuente de Financiamiento: </td>
		   <td class="normalNegro"><?echo $numero_reserva?></td>
		   </tr>
		  
		  	<tr class="normal"><td height="28" valign="midden" class="normalNegrita">N&uacute;mero del Compromiso:</td>
			<td class="normalNegro"><?echo $_POST['comp_id'];?></td></tr>
					<tr> 
					<td height="27"><div class="normalNegrita">Motivo del Pago:</div></td>
					<td class="normalNegro"><?php echo $sopg_detalle;?></td>
					</tr>
					<tr> 
					<td height="27"><div class="normalNegrita">Estado:</div></td>
					<td class="normalNegro"><?php
					$sql_ft="SELECT * FROM  safi_edos_venezuela WHERE id='".$_POST['edo_vzla']."'";
   	        		$res_ft=pg_exec($sql_ft);
					if ($row=pg_fetch_array($res_ft))
					echo $row['nombre'];?></td>
					</tr>
					<tr> 
					<td height="21"><div class="normalNegrita">Observaci&oacute;n:</div></td>
					<td class="normalNegro" width="555"><?php echo $sopg_observacion;?>					</td>
					</tr>
					<tr>
	  <td colspan="2">
		<table width="65%" border="0"  class="tablaalertas" align="center">
          <tr class="td_gray">
            <td  align="center"  class="normalNegroNegrita">Imputaci&oacute;n Presupuestaria </td>
          </tr>
		 
          <tr>
            <td  class="normal" align="center" >
              <table width="100%" border="0" >
                <tr>
                  <td  class="peqNegrita" align="left"><div align="center">ACC.C/PP</div></td>
                  <td  class="PeqNegrita" align="left"><div align="center">Acci&oacute;n Especifica </div></td>
                  <td width="10%"align="left"  class="peqNegrita"><div align="center">Dependencia</div></td>
                  <td width="15%"align="left"  class="peqNegrita"><div align="center">Partida/Cuenta contable</div></td>
                  <td width="39%"align="left"  class="peqNegrita"><div align="center">Monto Sujeto</div></td>
		  		  <td width="39%"align="left"  class="peqNegrita"><div align="center">Monto Exento</div></td>
                </tr>
              
                  <?php
				  
		for ($ii=0; $ii<$total_imputacion; $ii++)
    	{
    		
    	 $sql="select * from sai_consulta_proyecto_accion('". $matriz_acc_pp[($ii)] ."','".$matriz_acc_esp[($ii)]."','".$matriz_imputacion[($ii)]."',".$pres_anno.") as resultado (nombre varchar, especifica varchar, cg varchar, cc varchar)";
		 $resultado_set = pg_exec($conexion ,$sql) or die("Error al visualizar datos");
		 if ($resultado_set)
		 {
		  $row_impu = pg_fetch_array($resultado_set,0); 
		  $cg=$row_impu['cg'];
		  $cc=$row_impu['cc'];
		 }
		?> 		 <tr>
                  <td  class="peq" align="left" width="17%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_proyecto_accion".$ii;?>  type="text" class="normalNegro" id=<?php echo "txt_imputa_proyecto_accion".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $cg;?>" readonly="true"/>
                    </div></td>
                  <td  class="peq" align="left" width="19%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_accion_esp".$ii;?>  type="text" class="normalNegro" id=<?php echo "txt_imputa_accion_esp".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $cc;?>" readonly="true"/>
                    </div></td>
                  <td  class="peq" align="left" width="10%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_unidad".$ii;?>  type="text" class="normalNegro" id=<?php echo "txt_imputa_unidad".$ii;?> size="10" maxlength="10"   valign="right"  align="right" value="<?php echo $matriz_uel[$ii];?>" readonly="true"/>
                    </div></td>
                  <td  class="peq" align="left" width="15%">
                    <div align="center">
                   <?php 	
                    if (substr($matriz_sub_esp[$ii],0,6)=="4.11.0"){
      				 $convertidor="SELECT cpat_id FROM sai_convertidor WHERE part_id='".$matriz_sub_esp[$ii]."'";
      				 $res_convertidor=pg_query($conexion,$convertidor);
      				 if($row_conv=pg_fetch_array($res_convertidor)){
					  $cuenta= $row_conv['cpat_id'];
      				}          	
     			  }else{
     				$cuenta=$matriz_sub_esp[$ii];
     				}?>
                      <input name="<?php echo "txt_imputa_sub_esp".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_sub_esp".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $cuenta;?>" readonly="true" title="<?php if (trim( $matriz_sub_esp[$ii])== $partida_IVA){echo "Impuesto al Valor Agregado";}?>"/>
                    </div></td>
                  <td  class="peq" align="left" width="39%">
                    <div align="center">
                      <input name="<?php echo "txt_imputa_monto".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_monto".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($matriz_monto[$ii],2,'.',',');?>" readonly="true" />
                    </div></td>
		    <td  class="peq" align="left" width="39%">
                    <div align="center">
                      <input name="<?php echo "txt_imputa_monto_exento".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_monto_exento".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($matriz_monto_exento[$ii],2,'.',',');?>" readonly="true" />
                    </div></td>
                </tr>
                <?php 
		 }
		 
		 ?>
            </table></td>
          </tr>
		
			<tr>
				<td colspan="2">
									</td>
			</tr>
			<tr>
		<td   align="center" class="normal"  colspan="2">
			<table width="100%" class="tablaalertas" align="left" id="tbl_detalle_iva" border="0" background="imagenes/fondo_tabla.gif">
			<tr class="td_gray">
			<td height="19" colspan="3" align="center" class="normalNegroNegrita">
			DETALLE DEL IMPUESTO AL VALOR AGREGADO (IVA) 
			</td>
			</tr>
			<tr>
			<td height="19"  align="center" class="normalNegrita">Sub Total a Pagar Sin IVA: 
			  <input name="txt_monto_subtotal" type="text" class="normalNegro" id="txt_monto_subtotal" value="<?php echo (number_format($subtotal_xx,2,'.',','));?>" size="25" maxlength="25" readonly="" align="right"> </td>
			<td height="19"  align="center" class="normalNegrita">Monto IVA: 
			  <input name="txt_monto_iva_tt" type="text" class="normalNegro" id="txt_monto_iva_tt" value="<?php echo (number_format($iva_xx,2,'.',','));?>" size="25" maxlength="25" readonly="" align="right"> </td>
			</tr>
			<?php 
			if ($elem_imp_iva>0) 
			{
			?>
			<tr >
			<td width="50%" class="normal" align="center">Impuesto %</td>
			<td width="50%" class="normal" align="center">Monto (Bs.)</td>
			</tr>
			<?php 
			for ($xt=0; $xt<$elem_imp_iva; $xt++)
			{
			?> 
			<tr >
			<td width="50%" class="normal" align="center"><input name="<?php echo "txt_iva%".$xt;?>" type="text" class="normalNegro" id=<?php echo "txt_iva%".$xt;?> value="<?php echo (number_format($iva_porce[$xt],2,'.',','));?>" size="6" maxlength="6" readonly="" align="right"></td>
			<td width="50%" class="normal" align="center"><input name="<?php echo "txt_iva_monto%".$xt;?>" type="text" class="normalNegro" id="<?php echo "txt_iva_monto%".$xt;?>" value="<?php echo  (number_format($iva_monto[$xt],2,'.',',')); ?>"  size="25" maxlength="25" readonly="" align="right"></td>
			</tr>
			<?php
			} //Del For
			} //Del If
			?>
		</table>
		</td>	 
	</tr>		
			
			<tr class="td_gray"> 
		<td height="21" colspan="2" valign="midden"><div align="left" class="normalNegroNegrita"> 
		TOTAL A PAGAR</div></td>
		</tr>
		<tr>
		<td height="31" colspan="2">
		<div align="left"><span class="normalNegrita">
		En Bs.
		    <input type="text" name="txt_monto_tot" value="<?php echo trim($_POST['txt_monto_tot']); ?>" size="20" readonly="true" class="normalNegro">
		</span></div>		</td>
		</tr>
		<tr>
		<td height="31" colspan="2">
		<div align="left"><span class="normalNegrita">
		En Letras:<textarea name="txt_monto_letras" id="txt_monto_letras" rows="2" cols="70" class="normalNegro" readonly><?php echo trim($_POST['txt_monto_letras']);?></textarea>
		</span></div>		</td>
		</tr>
			<tr>
			  <td colspan="2" class="normal">&nbsp;</td>
			  </tr>
			<tr>
			  <td colspan="2" class="normal">
			  <table width="420" align="center">
				<?
				   include("includes/respaldos_mostrar.php");
				?>
			</table>
			  
			  </td>
			  </tr>
			<tr>
				<td height="117" colspan="2" class="normal"><br>
					<div align="center">
					Esta Solicitud de Pago Modificada el d&iacute;a: 
					<?php   echo date ("d/m/Y ") ." a las ". date ("h:i:s a ");?>
					<br>
					<br>
					<a href="javascript:window.print()" class="normal"><img src="imagenes/boton_imprimir.gif" width="23" height="20" border="0"></a>
					<br><br>
					<!-- <a href="documentos.php?tipo=<? echo $request_id_tipo_documento; ?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('regresar','','imagenes/boton_reg_blk.gif',1)"><img src="imagenes/boton_reg.gif" name="regresar" width="90" height="31" border="0"></a>		 -->			</div>					</td>
			</tr>
			</table>
			</table>
			<?php
		}
	

if ($satisfactorio==false)
  {?>
 <table width="500" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
   <td height="21" colspan="3" valign="midden"><div align="left" class="normalNegrita"> 
    SOLICITUD DE PAGO</div></td></tr>
  <tr>
    <td  colspan="3" class="normalNegrita">
		  <div align="center">
   		  <img src="imagenes/vineta_azul.gif" width="11" height="7">
		  La partida <?php echo $sin_convertidor;?> no se encuentra en el convertidor<br>
		 <?php echo(pg_errormessage($conexion)); ?><br><br>
		 <img src="imagenes/mano_bad.gif" width="31" height="38">
		 <br><br>
		</div></tr></table>
		<?php
  }
  ?>

</body>
</html>

