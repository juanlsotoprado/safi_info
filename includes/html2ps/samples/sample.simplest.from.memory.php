<?php

/**
 * Thanks for JensE for providing the code of fetcher class
 */

require_once(dirname(__FILE__).'/../config.inc.php');
require_once(HTML2PS_DIR.'pipelinefactory.class.php');

error_reporting(E_ALL);
ini_set("display_errors","1");
@set_time_limit(10000);
parse_config_file(HTML2PS_DIR.'html2ps.config');

/**
 * Handles the saving generated PDF to user-defined output file on server
 */
class MyDestinationFile extends Destination {
  /**
   * @var String result file name / path
   * @access private
   */
  var $_dest_filename;

  function MyDestinationFile($dest_filename) {
    $this->_dest_filename = $dest_filename;
  }

  function process($tmp_filename, $content_type) {
    copy($tmp_filename, $this->_dest_filename);
  }
}

class MyFetcherMemory extends Fetcher {
  var $base_path;
  var $content;

  function MyFetcherMemory($content, $base_path) {
    $this->content   = $content;
    $this->base_path = $base_path;
  }

  function get_data($url) {
    if (!$url) {
      return new FetchedDataURL($this->content, array(), "");
    } else {
      // remove the "file:///" protocol
      if (substr($url,0,8)=='file:///') {
        $url=substr($url,8);
        // remove the additional '/' that is currently inserted by utils_url.php
        if (PHP_OS == "WINNT") $url=substr($url,1);
      }
      return new FetchedDataURL(@file_get_contents($url), array(), "");
    }
  }

  function get_base_url() {
    return 'file:///'.$this->base_path.'/dummy.html';
  }
}

/**
 * Runs the HTML->PDF conversion with default settings
 *
 * Warning: if you have any files (like CSS stylesheets and/or images referenced by this file,
 * use absolute links (like http://my.host/image.gif).
 *
 * @param $path_to_html String HTML code to be converted
 * @param $path_to_pdf  String path to file to save generated PDF to.
 * @param $base_path    String base path to use when resolving relative links in HTML code.
 */
function convert_to_pdf($html, $path_to_pdf, $base_path='') {
  $pipeline = PipelineFactory::create_default_pipeline('', // Attempt to auto-detect encoding
                                                       '');

  // Override HTML source 
  // @TODO: default http fetcher will return null on incorrect images 
  // Bug submitted by 'imatronix' (tufat.com forum).
  $pipeline->fetchers[] = new MyFetcherMemory($html, $base_path);

  // Override destination to local file
  $pipeline->destination = new MyDestinationFile($path_to_pdf);

  $baseurl = '';
  $media =& Media::predefined('A4');
  $media->set_landscape(false);
  $media->set_margins(array('left'   => 0,
                            'right'  => 0,
                            'top'    => 0,
                            'bottom' => 0));
  $media->set_pixels(1024); 

  global $g_config;
  $g_config = array(
                    'cssmedia'     => 'screen',
                    'scalepoints'  => '1',
                    'renderimages' => true,
                    'renderlinks'  => true,
                    'renderfields' => true,
                    'renderforms'  => false,
                    'mode'         => 'html',
                    'encoding'     => '',
                    'debugbox'     => false,
                    'pdfversion'    => '1.4',
                    'draw_page_border' => false
                    );

  $pipeline->configure($g_config);
  $pipeline->process_batch(array($baseurl), $media);
}



$strContent='
Se somete a consideraci&oacute;n del Ing. Omar Montilla Director Ejecutivo y a la aprobaci&oacute;n de la Dra. Nancy Zambrano la solicitud de recursos para la realizaci&oacute;n&nbsp;&nbsp; del estudio ( Medici&oacute;n de Impacto social en entorno de los&nbsp; Infocentros), el cual tendr&aacute; una cobertura nacional para aplicar un instrumento de recolecci&oacute;n de informaci&oacute;n, donde&nbsp; se requiere&nbsp; movilizaci&oacute;n, alimentaci&oacute;n y compra de materiales de Oficina.
<br/><br/><br/><br/>
<table width="100%" cellspacing="1" cellpadding="1" border="1" align="" summary="">
    <tbody>
        <tr>
            <td>Concepto de Gastos</td>
            <td>Proceso de inducci&oacute;n (en Bs.F.)</td>
            <td>Proceso de aplicaci&oacute;n de la&nbsp; encuesta</td>
            <td>Total</td>
            <td>Iva</td>
            <td>Monto total:</td>
        </tr>
        <tr>
            <td>Vi&aacute;ticos</td>
            <td>6.360.00</td>
            <td>156.900.00</td>
            <td>163.260.00</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Refrigerios</td>
            <td>20.920.00</td>
            <td>&nbsp;</td>
            <td>20.920.00</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Papeleria</td>
            <td>&nbsp;</td>
            <td>1.800.00</td>
            <td>1.800.00</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Material de Oficina</td>
            <td>&nbsp;</td>
            <td>8.000.00</td>
            <td>8.000.00</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Total:</td>
            <td>27.280.00</td>
            <td>166.700.00</td>
            <td>193.980.00</td>
            <td>17.458.20</td>
            <td>211.438.20</td>
        </tr>
        <tr>

        <td colspan="6" align="center">
        <br/><br/>
        Se somete a consideraci&oacute;n del Ing. Omar Montilla Director Ejecutivo y a la aprobaci&oacute;n de la Dra. Nancy Zambrano la solicitud de recursos para la realizaci&oacute;n&nbsp;&nbsp; del estudio ( Medici&oacute;n de Impacto social en entorno de los&nbsp; Infocentros), el cual tendr&aacute; una cobertura nacional para aplicar un instrumento de recolecci&oacute;n de informaci&oacute;n, donde&nbsp; se requiere&nbsp; movilizaci&oacute;n, alimentaci&oacute;n y compra de materiales de Oficina.
        <br/><br/>        
        <table width="90%" cellspacing="1" cellpadding="1" border="1" align="" summary="">
		    <tbody>
		        <tr>
		            <td>Concepto de Gastos</td>
		            <td>Proceso de inducci&oacute;n (en Bs.F.)</td>
		            <td>Proceso de aplicaci&oacute;n de la&nbsp; encuesta</td>
		            <td>Total</td>
		            <td>Iva</td>
		            <td>Monto total:</td>
		        </tr>
		        <tr>
		            <td>Vi&aacute;ticos</td>
		            <td>6.360.00</td>
		            <td>156.900.00</td>
		            <td>163.260.00</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		        </tr>
		        <tr>
		            <td>Refrigerios</td>
		            <td>20.920.00</td>
		            <td>&nbsp;</td>
		            <td>20.920.00</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		        </tr>
		        <tr>
		            <td>Papeleria</td>
		            <td>&nbsp;</td>
		            <td>1.800.00</td>
		            <td>1.800.00</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		        </tr>
		        <tr>
		            <td>Material de Oficina</td>
		            <td>&nbsp;</td>
		            <td>8.000.00</td>
		            <td>8.000.00</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		        </tr>
		        <tr>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		        </tr>
		        <tr>
		            <td>Total:</td>
		            <td>27.280.00</td>
		            <td>166.700.00</td>
		            <td>193.980.00</td>
		            <td>17.458.20</td>
		            <td>211.438.20</td>
		        </tr>
		        <tr>
		        
		        <td colspan="6" align="center">
		        
		        
		        
		        <br/><br/>
        Se somete a consideraci&oacute;n del Ing. Omar Montilla Director Ejecutivo y a la aprobaci&oacute;n de la Dra. Nancy Zambrano la solicitud de recursos para la realizaci&oacute;n&nbsp;&nbsp; del estudio ( Medici&oacute;n de Impacto social en entorno de los&nbsp; Infocentros), el cual tendr&aacute; una cobertura nacional para aplicar un instrumento de recolecci&oacute;n de informaci&oacute;n, donde&nbsp; se requiere&nbsp; movilizaci&oacute;n, alimentaci&oacute;n y compra de materiales de Oficina.
        <br/><br/>        
        <table width="90%" cellspacing="1" cellpadding="1" border="1" align="" summary="">
		    <tbody>
		        <tr>
		            <td>Concepto de Gastos</td>
		            <td>Proceso de inducci&oacute;n (en Bs.F.)</td>
		            <td>Proceso de aplicaci&oacute;n de la&nbsp; encuesta</td>
		            <td>Total</td>
		            <td>Iva</td>
		            <td>Monto total:</td>
		        </tr>
		        <tr>
		            <td>Vi&aacute;ticos</td>
		            <td>6.360.00</td>
		            <td>156.900.00</td>
		            <td>163.260.00</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		        </tr>
		        <tr>
		            <td>Refrigerios</td>
		            <td>20.920.00</td>
		            <td>&nbsp;</td>
		            <td>20.920.00</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		        </tr>
		        <tr>
		            <td>Papeleria</td>
		            <td>&nbsp;</td>
		            <td>1.800.00</td>
		            <td>1.800.00</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		        </tr>
		        <tr>
		            <td>Material de Oficina</td>
		            <td>&nbsp;</td>
		            <td>8.000.00</td>
		            <td>8.000.00</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		        </tr>
		        <tr>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		            <td>&nbsp;</td>
		        </tr>
		        <tr>
		            <td>Total:</td>
		            <td>27.280.00</td>
		            <td>166.700.00</td>
		            <td>193.980.00</td>
		            <td>17.458.20</td>
		            <td>211.438.20</td>
		        </tr>
		    </tbody>
		</table>
        <br/><br/>
		        
		        
		        </td>
		        
		        
		        <tr>
		    </tbody>
		</table>
        <br/><br/>
        
        </td>
        
        <tr>
    </tbody>
</table>
';

convert_to_pdf(/*file_get_contents('../temp/long.html')*/$strContent, '../out/test.pdf');

?>