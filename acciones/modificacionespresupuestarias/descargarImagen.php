<?php

require_once(dirname(__FILE__) . '/../../init.php');

$enlace = $_REQUEST['file'];
header ("Content-Disposition: attachment; filename=$enlace ");
header ("Content-Type: application/force-download");
header ("Content-Length: ".filesize(SAFI_UPLOADS_PATH."/mpresupuestarias/".$enlace));
readfile(SAFI_UPLOADS_PATH."/mpresupuestarias/".$enlace);?>