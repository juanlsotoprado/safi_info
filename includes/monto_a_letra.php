<?php 


//$resuldecimal=monto_letra('1271.06','Bs');
//echo $resuldecimal;


function monto_letra($monto, $moneda) {

	if (strlen($moneda)==0) {
		$moneda= "Bs";
	}
	$resultado = obtenerNombreNumero($monto);
	$monto_ori = $monto;
	if (strpos($monto_ori,".") === false) { //No se encontro decimal no tiene punto
   		
		$decimal="00";
	}
	else {
		$decimal = substr($monto_ori, strpos($monto_ori,".")+1, 2); 
		$decimal1 = substr($monto_ori, strpos($monto_ori,".")+1, 1);
		if($decimal<10 && $decimal1!='0'){			
			$decimal=$decimal."0";

		}
		
	}
	if ($monto<0){
		$resuldecimal = (' Menos '.$resultado . $moneda .' con ' . $decimal . '/100 ' . utf8_decode('céntimos'));
	}else{
	      $resuldecimal = ($resultado . $moneda .' con ' . $decimal . '/100 ' . utf8_decode('céntimos'));
	}
    return $resuldecimal;
}

function letras($c,$d,$u)
{
	//var centenas,decenas,decom
	$lc="";
	$ld="";
	$lu="";
/*	$centenas=eval($c);
	$decenas=eval($d);
	$decom=eval($u);
	*/
	$centenas= $c;
	$decenas= $d;
	$decom= $u;
	
	switch($centenas)
	{
		case 0: $lc="";break;
		case 1:
		{
		  if ($decenas==0 && $decom==0)
		    $lc="Cien";
		  else
		    $lc="Ciento ";
		}
		break;
		case 2: $lc="Doscientos ";break;
		case 3: $lc="Trescientos ";break;
		case 4: $lc="Cuatrocientos ";break;
		case 5: $lc="Quinientos ";break;
		case 6: $lc="Seiscientos ";break;
		case 7: $lc="Setecientos ";break;
		case 8: $lc="Ochocientos ";break;
		case 9: $lc="Novecientos ";break;
	}
	
	switch($decenas) 
	{
		case 0: $ld="";break;
		case 1:
		{
			switch($decom)
			{
				case 0: $ld="Diez";break;
				case 1: $ld="Once";break;
				case 2: $ld="Doce";break;
				case 3: $ld="Trece";break;
				case 4: $ld="Catorce";break;
				case 5: $ld="Quince";break;
				case 6: $ld="Dieciseis";break;
				case 7: $ld="Diecisiete";break;
				case 8: $ld="Dieciocho";break;
				case 9: $ld="Diecinueve";break;
			}
		}
		break;
		case 2: $ld="Veinte";break;
		case 3: $ld="Treinta";break;
		case 4: $ld="Cuarenta";break;
		case 5: $ld="Cincuenta";break;
		case 6: $ld="Sesenta";break;
		case 7: $ld="Setenta";break;
		case 8: $ld="Ochenta";break;
		case 9: $ld="Noventa";break;
	}
	
	switch($decom)
	{
		case 0: $lu="";break;
		case 1: $lu="Un";break;
		case 2: $lu="Dos";break;
		case 3: $lu="Tres";break;
		case 4: $lu="Cuatro";break;
		case 5: $lu="Cinco";break;
		case 6: $lu="Seis";break;
		case 7: $lu="Siete";break;
		case 8: $lu="Ocho";break;
		case 9: $lu="Nueve";break;
	}
	
	if ($decenas==1)
	{
		return $lc.$ld;
	}
	
	if ($decenas==0 || $decom==0)
	{
		return $lc." ".$ld.$lu;
	}else{
		if($decenas==2)
		{
		  $ld="Veinti";
		  return $lc . $ld . strtolower($lu);
		}else{
		  return $lc.$ld." y ".$lu;
		}
	}
}

////////////////////////////////////////////
//////////////////////////////////////////////////////////

function obtenerNombreNumero($n)
{
	//var m0,cm,dm,um,cmi,dmi,umi,ce,de,un,hlp,decimal;
	//	echo $n;	
		$m0= intval($n/1000000000000); 
		$rm0= ($n % 1000000000000);
		$m1= intval($rm0/100000000000); 
		$rm1= $rm0%100000000000;
		$m2= intval($rm1/10000000000); 
		$rm2=$rm1%10000000000;
		$m3= intval($rm2/1000000000); 
		$rm3=$rm2%1000000000;
		$cm= intval($rm3/100000000); 
		$r1= $rm3%100000000;
		$dm= intval($r1/10000000); 
		$r2= $r1% 10000000;
		$um= intval($r2/1000000); 
		$r3= $r2% 1000000;
		$cmi=intval($r3/100000); 
		$r4= $r3% 100000;
		$dmi=intval($r4/10000); 
		$r5= $r4% 10000;
		$umi=intval($r5/1000); 
		$r6= $r5% 1000;
		$ce= intval($r6/100); 
		$r7= $r6% 100;
		$de= intval($r7/10); 
		$r8= $r7% 10;
		$un= intval($r8/1);

		if (($n< 1000000000000) && ($n>=1000000000))
		{
			//tmp=n.toString();
			$tmp = "".$n."";
			$s = strlen($tmp);
			$tmp1= substr($tmp, 0, $s-9);
			$tmp2= substr($tmp, $s-9, $s);
			
			$tmpn1=obtenerNombreNumero($tmp1);
			$tmpn2=obtenerNombreNumero($tmp2);
			
			if (strpos($tmpn1,"Un") !== false)
				$pred=" Billon ";
			else
				$pred=" Billones ";
				return $tmpn1.$pred.$tmpn2;
		}
	
		if (($n<10000000000) && ($n>=1000000))
		{
			$mldata = letras($cm,$dm,$um);
			$hlp= str_replace("Un","*",$mldata); 					
			if ((strpos($hlp,"*")===false) || (strpos($hlp,"*")<0) || (strpos($hlp,"*")>3))
			{
				$mldata = str_replace("Uno","un",$mldata); 
				$mldata .= " Millones ";
			}else{
				$mldata="Un Millon ";
			}
			
			$mdata=letras($cmi,$dmi,$umi);
			$cdata=letras($ce,$de,$un);
			
			if($mdata!=" ")
			{
				if ($n == 1000000) 
				{
					$mdata = str_replace("Uno","un",$mdata) . "de";
				}else{
					$mdata = str_replace("Uno","un",$mdata) ." mil ";
				}
			}
	
			return ($mldata . $mdata . $cdata);
		}
		
		if ($n<1000000 && $n>=1000)
		{
			$mdata=letras($cmi,$dmi,$umi);
			$cdata=letras($ce,$de,$un);
			//$cdata = str_replace("Un","uno",$cdata); 
			$hlp= str_replace("Un","*",$mdata); 
				//echo "posicion".strpos($hlp,"*").' aaa '.$mdata.'dddd '.$cdata."<br>";		
			if ((strpos($hlp,"*")===false) || (strpos($hlp,"*")<0) || (strpos($hlp,"*")>3))
			{				
				$mdata = str_replace("Uno","un",$mdata);			
				return ($mdata ." Mil ".$cdata);
			}
			
		
			
		
		else
			return ("Mil ". $cdata);
		}
			
		if ($n<1000 && $n>=1)
		{
			
			return (letras($ce,$de,$un));
		}
		
		if ($n==0)
		{
			return " Cero";
		}
		
		if ($n<0)
		{  
			$n=($n*(-1));
			return(obtenerNombreNumero($n));
		}
	
	return "No disponible";
		
}

?>
