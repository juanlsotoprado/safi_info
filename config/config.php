<?php
$GLOBALS['SAFI_CFG']["isSetup"] = false;
$GLOBALS['SAFI_CFG']["siteURL"] = '<URL completo del safi>';

/****************************************
***** Conexión de la base de datos ******
*****************************************/
$GLOBALS['SAFI_CFG']["dbType"] = '<Tipo de manejador de base de datos>'; // $GLOBALS['SAFI_CFG']["dbType"] = 'pgsql' 
$GLOBALS['SAFI_CFG']["dbEncoding"] = '<Codificación de caracteres de base de datos>'; // $GLOBALS['SAFI_CFG']["dbEncoding"] = 'LATIN1' 
$GLOBALS['SAFI_CFG']["dbServer"] = '<Servidor de base de datos>'; // $GLOBALS['SAFI_CFG']["dbServer"] = 'localhost';
$GLOBALS['SAFI_CFG']["dbPort"] = '<Puerto del servidor de base de datos>'; // Por ejemplo: $GLOBALS['SAFI_CFG']["dbPort"] = '5432';
$GLOBALS['SAFI_CFG']["dbUser"] = '<Usuario de la base de datos>'; // $GLOBALS['SAFI_CFG']["dbUser"] = 'postgres';
$GLOBALS['SAFI_CFG']["dbPass"] = '<Password de la base de datos>'; // $GLOBALS['SAFI_CFG']["dbPass"] = '12345';
$GLOBALS['SAFI_CFG']["dbDatabase"] = '<Nombre de la base de datos>'; // $GLOBALS['SAFI_CFG']["dbDatabase"] = 'safi';
$GLOBALS['SAFI_CFG']["tablePrefix"] = '<Prefijo de las tablas de la base de datos>'; // $GLOBALS['SAFI_CFG']["tablePrefix"] = '';

/**************************************************
 ********* Prefijo de Códigos de documentos *******
 **************************************************/
$GLOBALS['SAFI_CFG']["delimitadorPreCodigoDocumento"] = '-';
$GLOBALS['SAFI_CFG']["preCodigoRendicionViaticoNacional"] = 'rvna';
$GLOBALS['SAFI_CFG']["preCodigoAvance"] = 'avan';
$GLOBALS['SAFI_CFG']["preCodigoRendicionAvance"] = 'rava';

$GLOBALS['SAFI_CFG']["idCuentaResultadoDelEjercicio"] = '3.2.5.02.01.01.01';
$GLOBALS['SAFI_CFG']["nombreCuentaResultadoDelEjercicio"] = 'Resultados del Ejercicio';

$GLOBALS['SAFI_CFG']["idCuentaResultadoAcumulado"] = '3.2.5.01.01.01.01';
$GLOBALS['SAFI_CFG']["nombreCuentaResultadoAcumulado"] = 'Resultados acumulados';

$GLOBALS['SAFI_CFG']["numeroItemsDefault"] = 20;

// Definición manual de personas en cargos de infocentro.
$GLOBALS['SAFI_CFG']['cedulaCoordinadorOrdenacionDePagos'] = '12740944'; // Leyda Campos

// Unidad Tributaria
$GLOBALS['SAFI_CFG']['unidadTributaria'] = '150';
// Factor para calcular el impuesto sobre la renta (ISLR)
$GLOBALS['SAFI_CFG']['factorISLR'] = '83.3334';

require_once ("myconfig.php");
?>