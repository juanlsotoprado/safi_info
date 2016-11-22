<?php
require_once(dirname(__FILE__) . '/../../../init.php');
require_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
//require_once(SAFI_INCLUDE_PATH.'monto_a_letra.php');
$form = FormManager::GetForm(FORM_BUSCAR_TESORERIA);
$listaCheques = array();
$listaCheques = $GLOBALS['SafiRequestVars']['listaCheques'];
?>
<!DOCTYPE html>
<html> 
<head>
	<title>SAFI</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"></script>
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/funciones.js';?>"></script>
	<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>	
	<?php require_once(SAFI_INCLUDE_PATH.'/monto_a_letra.php'); ?>
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/reportesTesoreria.js'?>"></script>
</head>

<body>
 <?php foreach ($listaCheques as $lista_cheque) {?>
  <table class="tablaPequena">
    <tr>
      <td><table class="tabla">
        <tr>
          <td width="38%" class="normal"><div align="center">C&Oacute;DIGO CUENTA CLIENTE </div></td>
          <td width="15%" class="normal">&nbsp;</td>
          <td width="16%" class="normal"><div align="center">CHEQUE N&ordm; </div></td>
          <td width="31%">&nbsp;</td>
        </tr>
        <tr>
          <td class="normalNegrita"><div align="center"><?php echo $lista_cheque['nro_cuenta_bancaria'];?></div></td>
          <td>&nbsp;</td>
          <td class="normalNegrita"><div align="center"><?php echo $lista_cheque['nro_cheque'];?></div></td>
          <td><div align="center" class="normalNegrita"><?php echo number_format(trim($lista_cheque['monto_cheque']),2,',','.'); ?></div></td>
        </tr>
        <tr>
          <td class="normalNegrita">FUNDACI&Oacute;N INFOCENTRO </td>
          <td>&nbsp;</td>
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
          <td colspan="4" class="normal">P&Aacute;GUESE A LA ORDEN DE: <span class="normalNegrita"><?php echo $lista_cheque['beneficiario_cheque'];?>(<?php echo $lista_cheque['ci_rif'];?>)</span></td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
        <?php $monto_en_letras = monto_letra($lista_cheque['monto_cheque'], " BOLIVARES");?>
          <td colspan="4"><span class="normal">LA CANTIDAD DE: <label id="label_monto_letras"></label></span> <span class="normal"><span class="normalNegrita">
           <span class="normalNegrita"><?php  echo $monto_en_letras; ?></span>
          </span></span> </td>
          </tr>
        <tr>
          <td>&nbsp;</td>
		<td class="normalNegrita">Caracas, <?php echo $lista_cheque['fecha_emision'];?>&nbsp;</td>           
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        
        </tr>
        <tr>
          <td colspan="2" class="normalNegrita"> <?php echo $lista_cheque['nombre_banco'];?></td>
          <td>&nbsp;</td>
          <td class="normal"><div align="center">FIRMA AUTORIZADA </div></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>
      <table class="tabla">
      <tr>
      <td class="normal" width="25%">SOLICITUD DE PAGO:</td>
      <td class="normalNegrita" width="25%"><?php echo $lista_cheque['sopg'];?></td>
      <td class="normal" width="25%">PAGO CON CHEQUE:</td>
      <td class="normalNegrita" width="25%"><?php echo $lista_cheque['pgch_id'];?></td>
      </tr>
      <tr>
      <td colspan="4">&nbsp;</td>
      </tr>
      <tr>
      <td class="normal">CONCEPTO:</td>
      <td class="normalNegrita" colspan="3"><?php echo $lista_cheque['asunto'];?></td>
      </tr>
      <tr>
      <td colspan="4">&nbsp;</td>
      </tr>
      <tr>
      <td class="normal">OBSERVACIONES:</td>
      <td class="normalNegrita" colspan="3"><?php echo $lista_cheque['observaciones'];?></td>
      </tr>
      <tr>
      <td colspan="4">&nbsp;</td>
      </tr>
      <?php if (strlen($lista_cheque['motivo_anulacion'])>5) { ?>      
	      <tr>
	      <td colspan="2" class="normal">MOTIVO ANULACI&Oacute;N:</td>
	      <td class="normalNegrita" colspan="3" align="left"><?php echo trim($lista_cheque['motivo_anulacion']);?></td>
	      </tr>
      <?php }?> 
      <tr>
      <td colspan="4">&nbsp;</td>
      </tr>
	</table>
  </table>
  <div align="center">
  <a href="javascript:window.print()"><img src="<?=GetConfig("siteURL").'/imagenes/boton_imprimir.gif';?>" width="23" height="20" border="0" alt="Imprimir Detalle"></a>
  </div>
  <br/>
  <br/>  
  <div align="center"> 
  <?php if ($lista_cheque['estatus_cheque']==44) { ?>
    <a href="javascript:imprimir('<?php echo $lista_cheque['sopg'];?>')" class="normal" alt="Generar cheque">Generar Cheque</a>
  </div>  
  <?}
  }?>
</body>
</html>