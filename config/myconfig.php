<?php
$GLOBALS['SAFI_CFG']["isSetup"] = false;
$GLOBALS['SAFI_CFG']["siteURL"] = 'http://150.188.84.43/safi0.2'; // $GLOBALS['SAFI_CFG']["siteURL"] = 'http://safi.infocentro.gob.ve'

/****************************************
***** Conexión de la base de datos ******
*****************************************/

// para todas las máquinas
$GLOBALS['SAFI_CFG']["dbType"] = 'pgsql';
$GLOBALS['SAFI_CFG']["dbEncoding"] = 'LATIN1';

//programando sopg nuevo 2
/*
$GLOBALS['SAFI_CFG']["dbServer"] = '150.188.84.32';
$GLOBALS['SAFI_CFG']["dbPort"] = '5432';
$GLOBALS['SAFI_CFG']["dbUser"] = 'sistemas';
$GLOBALS['SAFI_CFG']["dbPass"] = 'd3s4rr0ll0';
$GLOBALS['SAFI_CFG']["dbDatabase"] = 'safi_13_04_2015';
*/

//para resolver tickets con pruebas de datos actuales 19/05/2015

$GLOBALS['SAFI_CFG']["dbServer"] = '150.188.84.32';
$GLOBALS['SAFI_CFG']["dbPort"] = '5432';
$GLOBALS['SAFI_CFG']["dbUser"] = 'sistemas';
$GLOBALS['SAFI_CFG']["dbPass"] = 'd3s4rr0ll0';
$GLOBALS['SAFI_CFG']["dbDatabase"] = 'safi_edrian';

// Produccion
/*
$GLOBALS['SAFI_CFG']["dbServer"] = '150.188.85.33';
$GLOBALS['SAFI_CFG']["dbPort"] = '5432';
$GLOBALS['SAFI_CFG']["dbUser"] = 'postgres';
$GLOBALS['SAFI_CFG']["dbPass"] = '*p0stgr3s.2012';
$GLOBALS['SAFI_CFG']["dbDatabase"] = 'sai';
*/
