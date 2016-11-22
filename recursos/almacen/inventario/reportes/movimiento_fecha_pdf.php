<?php

	require("../../../../includes/conexion.php");
	require("../../../../lib/fpdf/fpdf.php");
	require_once("../../../../includes/fechas.php");
	
	$fecha_in=trim($_GET['txt_inicio']); 
	$fecha_fi=trim($_GET['hid_hasta_itin']); 
	$fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
	$tipo_mov=trim($_GET['tp_movimiento']);
	$tp_arti=trim($_GET['tp_arti']);
	$depe=trim($_GET['depe']);
	$nombre_arti=$_GET['des_articulo'];

	$criterio1="";
	$criterio2="";
	$criterio3="";
	$criterio4="";
	$criterio5="";
	$criterio6="";

	if (strlen($fecha_ini)>2) {
		$criterio1 = " AND alm_fecha_recepcion >= '".$fecha_ini."' and alm_fecha_recepcion <= '".$fecha_fin."' ";
		$criterio2 = " AND fecha_acta >= '".$fecha_ini."' and fecha_acta <= '".$fecha_fin."' ";
	}
	
	if ($depe>0) {
		$criterio3=" and depe_solicitante='".$depe."' ";
		$criterio4=" and depe_entregada like '".substr($depe,0,2)."%' ";
	}
	  
	if ($tp_arti>'0')
		$criterio5=" and t4.tipo='".$tp_arti."' ";


	$sql_arti="SELECT * FROM sai_seleccionar_campo('sai_item','id','nombre='||'''$nombre_arti''','',2) resultado_set (id varchar)";
	$resul_arti=pg_query($conexion,$sql_arti);
	if($rowa=pg_fetch_array($resul_arti)){
		$codigo_arti=trim($rowa['id']);
	}
	if (strlen($codigo_arti)>0) {
		$criterio6=" and t2.id='".$codigo_arti."' ";
	}

	$sql_entrada = "
			SELECT
				t1.acta_id,
				alm_fecha_recepcion AS fecha,
				t1.depe_solicitante AS dependencia,
				t2.id AS arti_id,
				cantidad,
				precio,
				nombre,
				'E' AS tipo,
				prov_id_rif,
				0 AS entregado_a
			FROM
				sai_arti_inco arti_inco
				INNER JOIN sai_arti_almacen t1 ON (t1.acta_id = arti_inco.acta_id)
				INNER JOIN sai_item t2 ON (t2.id = t1.arti_id)
				INNER JOIN sai_item_articulo t4 ON (t4.id = t2.id)
			WHERE
				arti_inco.esta_id <> 15
				".$criterio5."
				".$criterio1."
				".$criterio3."
				".$criterio6."
			 
		UNION
		
			SELECT
				amat_id AS acta_id,
				fecha_acta AS fecha,
				depe_entregada AS dependencia,
				t1.arti_id,
				cantidad,
				precio,
				nombre,
				t3.tipo,
				'--' AS prov_id_rif,
				0 AS entregado_a
			FROM
				sai_arti_acta_almacen t3
				INNER JOIN sai_arti_salida t1 ON (t1.n_acta = t3.amat_id)
				INNER JOIN sai_item t2 ON (t2.id = t1.arti_id)
				INNER JOIN sai_item_articulo t4 ON (t4.id = t2.id) 
			WHERE
				t3.tipo ='D'
				AND t3.esta_id <> 15 
				".$criterio5."
				".$criterio2."
				".$criterio4."
				".$criterio6."
	";
		
	$sql_salida = "
		SELECT
			t3.amat_id AS acta_id,
			t3.fecha_acta AS fecha,
			t3.depe_entregada AS dependencia,
			t1.arti_id,
			t1.cantidad,
			t1.precio,
			t2.nombre,
			t3.tipo,
			'--' AS prov_id_rif,
			t3.entregado_a
		FROM
			sai_arti_acta_almacen t3
			INNER JOIN sai_arti_salida t1 ON (t1.n_acta = t3.amat_id)
			INNER JOIN sai_item t2 ON (t2.id = t1.arti_id)
			INNER JOIN sai_item_articulo t4 ON (t2.id = t4.id)
		WHERE
			t3.tipo='S'
			AND t3.esta_id <> 15
			".$criterio5."
			".$criterio2."
			".$criterio4."
			".$criterio6."
	";
	
	if ($tipo_mov=='2'){ //ENTRADAS
		$sql = $sql_entrada." ORDER BY nombre, fecha";
	} elseif ($tipo_mov=='3') {//SALIDAS
		$sql = $sql_salida." ORDER BY nombre, fecha";
	} else {//ENTRADAS Y SALIDAS
		$sql = $sql_entrada." UNION ".$sql_salida." ORDER BY nombre, fecha";
	}
	
	$movimientosArticulos = array();
	$idsDependenciasDestinos = array();
	$idsProveedores = array();
	$idsActasUbicacion = array();
	$dependenciasDestinos = array();
	$proveedores = array();
	$actasUbicaciones = array();
	
	$resultado = pg_query($conexion, $sql);
	
	if($resultado === false){
		echo "Error al realizar la consulta.";
		error_log(pg_last_error());
	} else {
		while ($row = pg_fetch_array($resultado)){
			$movimientosArticulos[] = $row;
			$idsDependenciasDestinos[$row['dependencia']] = $row['dependencia'];
			$idsProveedores[$row['prov_id_rif']] = $row['prov_id_rif'];
			if($row['entregado_a'] == '-1') $idsActasUbicacion[$row['acta_id']] = $row['acta_id'];
		}
		
		// Obtener las dependencias
		if(count($idsDependenciasDestinos) > 0){
			
			$sql = "
				SELECT
					depe_id AS id_dependencia,
					depe_nombre AS nombre_dependencia,
					depe_nombrecort AS nombre_corto_dependencia
				FROM
					sai_dependenci
				WHERE
					depe_id IN ('".implode("' ,'", $idsDependenciasDestinos)."')
			";
		
			$resultado = pg_query($conexion, $sql);
			
			if($resultado === false){
				echo "Error al realizar la consulta.";
				error_log(pg_last_error());
			} else {
				while ($row = pg_fetch_array($resultado)){
					$dependenciasDestinos[$row['id_dependencia']] = $row;
				}
			}
		}
		
		// Obtener las ubicaciones
		if(count($idsActasUbicacion) > 0){
			
			$sql = "
				SELECT
					bien_asbi.acta_almacen AS id_acta,
					bien_asbi.ubicacion AS id_ubicacion,
					CASE
						WHEN bien_asbi.ubicacion <> 3 THEN
							bien_ubicacion.bubica_nombre
						WHEN bien_asbi.ubicacion = 3 AND infocentro IS NOT NULL THEN
							(	SELECT
									nemotecnico || ' \: '  ||  nombre
								FROM
									safi_infocentro
								WHERE
									nemotecnico = infocentro
							)
						ELSE
							''
					END AS nombre_ubicacion
				FROM
					sai_arti_acta_almacen arti_acta_almacen
					INNER JOIN sai_bien_asbi bien_asbi ON (bien_asbi.acta_almacen = arti_acta_almacen.amat_id)
					INNER JOIN sai_bien_ubicacion bien_ubicacion ON (bien_ubicacion.bubica_id = bien_asbi.ubicacion)
				WHERE						
					bien_asbi.acta_almacen IN ('".implode("', '", $idsActasUbicacion)."')
			";
			
			$resultado = pg_query($conexion, $sql);
			
			if($resultado === false){
				echo "Error al realizar la consulta.";
				error_log(pg_last_error());
			} else {
				while ($row = pg_fetch_array($resultado)){
					$actasUbicaciones[$row['id_acta']] = $row;
				}
			}
		}
		
		// Obtener los proveedores
		if(count($idsProveedores) > 0){
			
			$sql = "
				SELECT
					prov_id_rif AS id_proveedor,
					prov_nombre AS nombre_proveedor
				FROM
					sai_proveedor_nuevo
				WHERE
					prov_id_rif IN ('".implode("' ,'", $idsProveedores)."')
			";
			
			$resultado = pg_query($conexion, $sql);
			
			if($resultado === false){
				echo "Error al realizar la consulta.";
				error_log(pg_last_error());
			} else {
				while ($row = pg_fetch_array($resultado)){
					$proveedores[$row['id_proveedor']] = $row;
				}
			}
		}
	}

	//Colocamos la imagen del ministerio del lado derecho
	class PDF extends FPDF
	{
		//Cabecera de página
		function Header() {

			$alto = 4;
			global $tipo_mov;
			global $fecha_in;
			global $fecha_fi;

			//Logo
			$this->SetX(35);
			$this->Image('../../../../imagenes/encabezado.jpg',18,20,256,12);
			$this->Ln(2);

			$this->SetFont('Arial','B',12);
			//Título
			$posy= $this->gety();
			$this->SetX(25);
			$this->SetY(31);
			$titulo="Movimientos de artículos ";
			
			if ($tipo_mov=="3") {
				$titulo= $titulo."salientes desde el ".$fecha_in." al ".$fecha_fi;
			} elseif ($tipo_mov=="2"){
				$titulo= $titulo."entrantes desde el ".$fecha_in." al ".$fecha_fi;
			} else {
				$titulo= $titulo."entrantes y salientes desde el ".$fecha_in." al ".$fecha_fi;
			}
		 	
			 $this->Cell(286,15,utf8_decode($titulo),0,1,'C');
			 $this->SetX(25);
			 $this->SetY(32);
			 $this->Cell(256,15,'',0,1,'C');
	
			 if ($tipo_mov == "3")
			 	$this->SetX(25);
			 else
			 	$this->SetX(15);
	
			 $this->SetFont('Arial','B',7);
			 if ($tipo_mov <> '3')
			 	$this->Cell(262,$alto,"",1,2,'C');
			 else
			 	$this->Cell(248,$alto,"",1,2,'C');
	
			 $this->Cell(20,$alto,"Acta",1,0,'C');
			 $this->Cell(15,$alto,"Tipo",1,0,'C');
			 $this->Cell(16,$alto,"Fecha acta",1,0,'C');
			 $this->Cell(70,$alto,utf8_decode("Artículo"),1,0,'C');
			 if ($tipo_mov <> '3'){
			 	$this->Cell(70,$alto,"Proveedor",1,0,'C');
			 	$this->Cell(24,$alto,"Dependencia",1,0,'C');
			 }else{
			 	$this->Cell(80,$alto,"Dependencia",1,0,'C');
			 }
	
			 $this->Cell(13,$alto,"Cantidad",1,0,'C');
			 $this->Cell(18,$alto,"Costo unitario",1,0,'C');
			 $this->Cell(16,$alto,"Monto total",1,2,'C');

		}

		//Pie de página
		function Footer() {
	
		 	global $user_nombre;
		 	$this->SetX(-13.5);
		 	$this->SetFont('Arial','B',7);
		 	//Número de página
		 	$this->Cell(-3,10,utf8_decode('  SAFI-Fundación Infocentro').'  '.$this->PageNo().'/{nb}',0,0,'R');
		 	$this->Cell(-3,16,utf8_decode('Detalle generado el día').'  '.date("d/m/y").' a las '.date("H:i:s"),0,0,'R');
		}
		
	}

	$pdf=new PDF('L','mm','A4');
	$pdf->AddPage();
	$pdf->AliasNbPages();
	$alto=4;
	$posy= $pdf->gety();
	$pdf->SetXY(12,$posy);
	if ($tipo_mov == "3")
		$pdf->SetX(25);
	else
		$pdf->SetX(15);
	$pdf->SetFont('Arial','',7);
	$i=0;
	$monto_total=0;
	$monto=0;
	$total_entradas=0;
	$total_salidas=0;
	foreach ($movimientosArticulos AS $rowt1)
	{
		$i++;
		if(trim($rowt1['tipo']=='E')){
			$movimiento = 'Entrada';
			$total_entradas = $total_entradas + ($rowt1['precio'] * $rowt1['cantidad']);
		}
		if(trim($rowt1['tipo']=='S')){
			$movimiento = 'Salida';
			$total_salidas = $total_salidas + ($rowt1['precio'] * $rowt1['cantidad']);
		}
		if(trim($rowt1['tipo'] == 'D')){
			$movimiento = utf8_decode('Devolución');
			$total_entradas = $total_entradas + ($rowt1['precio'] * $rowt1['cantidad']);
		}

		$fec = substr($rowt1['fecha'],8,2).'/'.substr($rowt1['fecha'],5,2).'/'.substr($rowt1['fecha'],0,4);
		$hora = substr($rowt1['fecha'],11,8);
		
		$dependencia_completa = "--";
		$dependencia = "--";
		
		// Obtener la dependencia
		if(isset($dependenciasDestinos[$rowt1['dependencia']]))
		{
			$dependencia_completa = $dependenciasDestinos[$rowt1['dependencia']]['nombre_dependencia'];
			$dependencia = $dependenciasDestinos[$rowt1['dependencia']]['nombre_corto_dependencia'];
		}
		
		if ($rowt1['entregado_a'] == '-1')
		{
			if(isset($actasUbicaciones[$rowt1['acta_id']]) && $actasUbicaciones[$rowt1['acta_id']]['id_ubicacion'] != "1")
			{
				if($actasUbicaciones[$rowt1['acta_id']]['id_ubicacion'] == "3"){
					$dependencia_completa = substr($actasUbicaciones[$rowt1['acta_id']]['nombre_ubicacion'], 0, 53);
					$explodeDependencia_completa = explode(":", $dependencia_completa);
					$dependencia = $explodeDependencia_completa[0];
				}
				else {
					$dependencia = $dependencia_completa = $actasUbicaciones[$rowt1['acta_id']]['nombre_ubicacion'];
				}
			}
		}

		// Obtener el proveedor
		$objProveedor = (isset($proveedores[$rowt1['prov_id_rif']]))
			? ($proveedores[$rowt1['prov_id_rif']]) : null;
		
		$proveedor = ($objProveedor !== null)
			? substr($objProveedor['id_proveedor'] . ":" . $objProveedor['nombre_proveedor'], 0, 46) : "--";

		$pdf->Cell(20,$alto,$rowt1['acta_id'],1,0,'C');
		$pdf->Cell(15,$alto,$movimiento,1,0,'L');
		$pdf->Cell(16,$alto,$fec,1,0,'C');
		$pdf->Cell(70,$alto,substr((strtoupper($rowt1['nombre'])),0,45),1,0,'L');
		if ($tipo_mov <> '3'){
			$pdf->Cell(70,$alto,strtoupper($proveedor),1,0,'L');
			$pdf->Cell(24,$alto,strtoupper($dependencia),1,0,'L');
		}else{
			$pdf->Cell(80,$alto,strtoupper($dependencia_completa),1,0,'L');
		}
		$pdf->Cell(13,$alto,$rowt1['cantidad'],1,0,'R');
		$pdf->Cell(18,$alto,str_replace('.',',',$rowt1['precio']),1,0,'R');
		$monto=$rowt1['precio']*$rowt1['cantidad'];
		$monto_total=$monto_total+$monto;
		$pdf->Cell(16,$alto,number_format($monto,2,',','.'),1,2,'R');
		if ($tipo_mov == "3")
			$pdf->SetX(25);
		else
			$pdf->SetX(15);

	  
	}
	$pdf->SetFont('Arial','B',7);
	if ($tipo_mov == '2'){
		$pdf->Cell(246,$alto,"Total entradas Bs.",1,0,'R');
		$pdf->Cell(16,$alto,number_format($total_entradas,2,',','.'),1,2,'R');
	}elseif ($tipo_mov=='3'){
		$pdf->Cell(232,$alto,"Total salidas Bs.",1,0,'R');
		$pdf->Cell(16,$alto,number_format($total_salidas,2,',','.'),1,2,'R');
	}else{
		$pdf->Cell(246,$alto,"Total entradas Bs.",1,0,'R');
		$pdf->Cell(16,$alto,number_format($total_entradas,2,',','.'),1,2,'R');
		$pdf->SetX(15);
		$pdf->Cell(246,$alto,"Total salidas Bs.",1,0,'R');
		$pdf->Cell(16,$alto,number_format($total_salidas,2,',','.'),1,2,'R');
	}

	$tipo_documento=substr($codigo,0,4);
	$pdf-> Output();
?>