<?php
require_once(dirname(__FILE__).'/lib/init.php');

// Initialise our session
if (!isset($_SESSION)) {
	session_start();
}