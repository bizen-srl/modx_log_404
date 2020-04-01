<?php
/**
 * @name Log404
 * @description This plugin is part of the Log404 package that keeps track of any url with a 404 error in a clear and structured log
 * @PluginEvents OnPageNotFound
 */

// Your core_path will change depending on whether your code is running on your development environment
// or on a production environment (deployed via a Transport Package).  Make sure you follow the pattern
// outlined here. See https://github.com/craftsmancoding/repoman/wiki/Conventions for more info

$core_path = $modx->getOption('log404.core_path', null, MODX_CORE_PATH.'components/log404/');
include_once $core_path .'vendor/autoload.php';

$log404 = $modx->getService('log404','Log404', $core_path .'model/');

// Make sure the class is loaded
if (!($log404 instanceof Log404)) return;

/* Don't execute in Manager */
/** @var $modx modX */
/** @var $scriptProperties array */
if ($modx->user->hasSessionContext('mgr')) return;

// Check if the useragent needs to be ignored
if($log404->checkUseragent()) return;

// Let's start the magic
switch ($modx->event->name) {
    case 'OnPageNotFound':
        // Get the response
        $response = $modx->event->params['error_type'];
        $file = $log404->settings['log_path'];
        $ignore_ips = explode(',', $log404->settings['ignore_ips']);

        // Init records array
        $records = array();
        
        // Log only on 404 error response
        if ($response == "404") {
            // Set records parameters
            $records = $log404->setParams($_SERVER);

            // Check for ips to skip
            if(in_array($records['ip'], $ignore_ips)) return;

            // Check if url has already been logged and increase hit count
            if($log404->hit($records['url'])) return;

            // Log records on csv
            if ($log404->log($records)) {
                $modx->log(modx::LOG_LEVEL_INFO, '[Log404]: ' . $records['url'] . ' returned a 404 error and has been logged.');
            } else {
                $modx->log(modx::LOG_LEVEL_ERROR, '[Log404]: ' . $records['url'] . ' returned a 404 error but something went wrong while logging it.');
            }
        }
        
    break;
}