<?php
//error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
//ini_set("display_errors","1");
//ini_set("display_errors","0");
ini_set('memory_limit','1024M');
if (ini_get("pcre.backtrack_limit") < 1000000) { ini_set("pcre.backtrack_limit",1000000); };
@set_time_limit(10000);

ini_set("user_agent", DEFAULT_USER_AGENT);

class MyDestinationFile extends Destination {
	var $_dest_filename;

	function MyDestinationFile($dest_filename) {
		$this->_dest_filename = $dest_filename;
	}

	function process($tmp_filename, $content_type) {
		copy($tmp_filename, $this->_dest_filename);
	}
}

class MyFetcherMemory extends Fetcher{
	var $base_path;
	var $content;

	function MyFetcherMemory($content) {
		$this->content   = $content;
		$this->base_path = '';
	}
	function get_data($url) {
		if(!$url){
			return new FetchedDataURL($this->content, array(), "");
		}else{
			if (substr($url,0,8)=='file:///') {
				$url=substr($url,8);
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
 * Genera un documento en formato PDF a partir del valor especificado 
 * en el parametro $html cuyo contenido es un documento en formato HTML.
 * El documento generado es configurable de acuerdo a un conjunto de propiedades 
 * especificadas en el parametro $properties.
 * <p>
 * @param $html una cadena que contiene un documento en formato HTML 
 * @param $properties 	un arreglo de propiedades. Las propiedades disponibles son:
 * 						-path:	es una cadena que especifica la ruta donde se escribira
 * 								el documento generado. Si no se especifica o tiene como valor '',
 * 								el documento se envia al explorador web del cliente.
 * 						-landscape:	true si la orientacion que se quiere es horizontal.
 * 									false si la orientacion que se quiere es vertical.
 * 						-marginLeft: margen izquierdo del documento generado.
 * 						-marginRight: margen derecho del documento generado.
 * 						-marginTop: margen superior del documento generado.
 * 						-marginBottom: margen inferior del documento generado.
 * 						-headerHtml: encabezado documento generado.
 * 						-footerHtml: pie de pagina del documento generado.
 * @return      No tiene valor de retorno
 * @see         
 */

function convert_to_pdf($html, $properties=null){

	$path="";
	$landscape=false;
	$marginLeft=10;
	$marginRight=10;
	$marginTop=15;
	$marginBottom=15;
	$headerHtml = "<style type='text/css'>
						@page {
					 		margin-top: 20mm;
						}
					</style>".
					"<img width='1000px' src='http://safi.infocentro.gob.ve/imagenes/encabezado.jpg'/>";
	$footerHtml = 	"<span style='align=center;font-family: arial;font-style:italic;font-weight:bold;font-size: 10pt;'>SAFI - Fundación Infocentro</span><br/>";
					
	//Se colocó fecha impresión:
	$footerHtml .= "<span style='align=center;font-family: arial;font-size: 10pt;'>Fecha impresión:".fecha()."</span>";
	
	if($properties && sizeof($properties)>0){
		if(isset($properties["path"])){
			$path=$properties["path"];
		}
		if(isset($properties["landscape"])){
			$landscape=$properties["landscape"];
		}
		if(isset($properties["marginLeft"])){
			$marginLeft=$properties["marginLeft"];
		}
		if(isset($properties["marginRight"])){
			$marginRight=$properties["marginRight"];
		}
		if(isset($properties["marginTop"])){
			$marginTop=$properties["marginTop"];
		}
		if(isset($properties["marginBottom"])){
			$marginBottom=$properties["marginBottom"];
		}
		if(isset($properties["headerHtml"])){
			$headerHtml=$properties["headerHtml"];
		}
		if(isset($properties["footerHtml"])){
			$footerHtml=$properties["footerHtml"];
		}
	}
	
	global $g_config;
	$g_config = array(
                             'compress'		=> false,
                             'cssmedia'		=> "screen",
                             'debugbox'		=> false,
                             'debugnoclip'	=> false,
                             'draw_page_border'	=> false,
                             'encoding'      => "",
                             'html2xhtml'    => true,
                             'imagequality_workaround' => false,
                             'landscape'     => $landscape,
                             'margins'       => array(
                                                      'left'    => $marginLeft,
                                                      'right'   => $marginRight,
                                                      'top'     => $marginTop,
                                                      'bottom'  => $marginBottom,
                                                      ),
                             'media'         => "Letter",
                             'method'        => "fpdf",
                             'mode'          => 'html',
                             'output'        => "0",
                             'pagewidth'     => 1024,
                             'pdfversion'    => "1.4",
                             'ps2pdf'        => false,
                             'pslevel'       => 3,
                             'renderfields'  => true,
                             'renderforms'   => false,
                             'renderimages'  => true,
                             'renderlinks'   => true,
                             'scalepoints'   => true,
                             'smartpagebreak' => true,
                             'transparency_workaround' => false
                             );
                             
	parse_config_file(HTML2PS_DIR."html2ps.config");

	$g_media = Media::predefined($g_config['media']);
	$g_media->set_landscape($g_config['landscape']);
	$g_media->set_margins($g_config['margins']);
	$g_media->set_pixels($g_config['pagewidth']);
	
	$pipeline = new Pipeline();
	$pipeline->configure($g_config);
	$pipeline->fetchers[] = new MyFetcherMemory($html);
	
	$pipeline->data_filters[] = new DataFilterDoctype();
	$pipeline->data_filters[] = new DataFilterUTF8($g_config['encoding']);
	if ($g_config['html2xhtml']) {
  		$pipeline->data_filters[] = new DataFilterHTML2XHTML();
	} else {
  		$pipeline->data_filters[] = new DataFilterXHTML2XHTML();
	}
	$pipeline->parser = new ParserXHTML();
	
	$pipeline->pre_tree_filters = array();

	$filter = new PreTreeFilterHeaderFooter($headerHtml, $footerHtml);
	$pipeline->pre_tree_filters[] = $filter;

	if ($g_config['renderfields']) {
		$pipeline->pre_tree_filters[] = new PreTreeFilterHTML2PSFields();
	}
	
	if ($g_config['method'] === 'ps') {
		$pipeline->layout_engine = new LayoutEnginePS();
	} else {
		$pipeline->layout_engine = new LayoutEngineDefault();
	}

	$pipeline->post_tree_filters = array();
	
	if ($g_config['pslevel'] == 3) {
		$image_encoder = new PSL3ImageEncoderStream();
	} else {
		$image_encoder = new PSL2ImageEncoderStream();
	}
	
	$pipeline->output_driver = new OutputDriverFPDF();
	
	if($path!=''){
		$pipeline->destination = new MyDestinationFile($path);
	}else{
		$pipeline->destination = new DestinationBrowser("");
	}
	
	$time = time();
	
	$status = $pipeline->process_batch(array(''), $g_media);
	
	//error_log(sprintf("Processing of '%s' completed in %u seconds", '', time() - $time));
	
	if ($status == null) {
		print($pipeline->error_message());
		error_log("HTML2PS ERROR - Error en la conversion del pipeline");
		die();
	}
}
?>