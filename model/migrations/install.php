<?php
/*------------------------------------------------------------------------------
This file is included during package building and during database migration
operations.
------------------------------------------------------------------------------*/
if (!defined('MODX_CORE_PATH')) {return;}

// Follow repoman's conventions here: this code can run in dev mode or in prod mode!
$core_path = $modx->getOption('log404.core_path','',MODX_CORE_PATH.'components/log404/');

$manager = $modx->getManager();
$modx->addPackage('log404',$core_path.'model/','modx_');
$manager->createObjectContainer('Log404Items');

/*EOF*/