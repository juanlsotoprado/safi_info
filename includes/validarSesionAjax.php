<?php
ob_start();
session_start();
if  (!isset($_SESSION['login']))  {
	echo 'false';
}

?>