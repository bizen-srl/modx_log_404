<?php

use League\Csv\Writer;

/**
 * This file is the main class file for Log404.
 *
 *
 * @author Manuel Barbiero <manuel@bizen.it>
 *
 * @package log404
 */
class Log404
{
    /**
     * namespace
     * 
     * @var string
     */
    protected $namespace = 'log404';


    public function __construct(modX &$modx, array $config = array(), array $settings = array())
    {
        $this->modx =& $modx;
        
        $this->config['core_path'] = $this->modx->getOption('log404.core_path', null, MODX_CORE_PATH.'components/log404/');
        $this->config['model_path'] = $this->modx->getOption('log404.core_path', null, MODX_CORE_PATH.'components/log404/') . 'model/';
        $this->config['assets_url'] = $this->modx->getOption('log404.assets_url', null, MODX_ASSETS_URL.'components/log404/');

        if(!$this->modx->addPackage('log404', $this->config['model_path'])) {
            $this->modx->log(modx::LOG_LEVEL_ERROR, '[Log404]: There was a problem adding the package.');
        }

        /* load log404 lexicon */
        if ($this->modx->lexicon) {
            $this->modx->lexicon->load('log404:default');
        }

        $this->settings = array(
            'log_path' => ($this->modx->getOption('log404.log_path') == false) ? $this->config['core_path'].'404-log.csv' : '',
            'ignore_ips' => $this->modx->getOption('log404.ignore_ips'),
            'log_max_lines' => $this->modx->getOption('log404.log_max_lines'),
            'header_cols' => $this->modx->getOption('log404.header_cols'),
            'useragents' => $this->modx->getOption('log404.useragents'),
        );
    }

    /**
    * Checks if the useragent needs to be ignored (useful to ignore seo-crawlers)
    * @var $customer_user_agent
    */
    public function checkUseragent($customer_user_agent)
    {
        $useragents = array_map('trim', explode(',', $this->settings['useragents']));
        
        if ($customer_user_agent && is_array($useragents)) {
            foreach ($useragents as $user_agent_to_ignore) {
                if (stripos($customer_user_agent, $user_agent_to_ignore) !== false) {
                    return true;
                }
                return false;
            }
        }
    }

    /**
    * Csv row insert handler
    * @var $file, array $records
    */
    public function generateCSV($file)
    {
            // Load Csv file and insert records
            $csv = Writer::createFromPath($file, 'w');

            // Init records array
            $records = $this->getLogs($this->settings['log_max_lines']);

            // Insert header array at the beginnig of the records array
            array_unshift($records, explode(',', $this->settings['header_cols']));

            //insert all the records
            return $csv->insertAll($records);
    }

   /**
    * function to set log parameters programmatically
    * @var $_SERVER, array $data
    */
    public function setParams($request, $data = array()) {
        $data['url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        //$data['date'] = date('d/m/y H:i'); // H:i
        
        if(!empty($request['HTTP_CLIENT_IP'])){
            //ip from shared internet
            $data['ip'] = $request['HTTP_CLIENT_IP'];
        }elseif(!empty($request['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $data['ip'] = $request['HTTP_X_FORWARDED_FOR'];
        }else{
            $data['ip'] = $request['REMOTE_ADDR'];
        }
      
        $data['userAgent'] = isset($request['HTTP_USER_AGENT'])
                ? $request['HTTP_USER_AGENT']
                : '<unknown user agent>';        
        
        return $data;
   }

   /**
    * Function to log records to the database
    */
    public function log($data)
    {
        // Set data to log as an array
        $data = array(
            'url' => $data['url'],
            'ip' => $data['ip'],
            'user_agent' => $data['userAgent'],
            'hit' => 1
        );

        // Log data to the db
        $log = $this->modx->newObject('Log404Items', $data);
        return $log->save();

    }

    /**
     * Function to get list of logs
     */
    public function getLogs($limit = null)
    {
        $q = $this->modx->newQuery('Log404Items');
        $q->sortby('date','ASC');
        if ($limit !== null) $q->limit($limit);
        $logs = $this->modx->getCollection('Log404Items', $q);

        $logsArray = array();

        foreach ($logs as $log) {
            $logsArray[] = array(
                'url' => $log->get('url'),
                'date' => date('d/m/Y H:i', strtotime($log->get('date'))),
                'ip' => $log->get('ip'),
                'user_agent' => $log->get('user_agent'),
                'hit' => $log->get('hit')
            );
        }

        return $logsArray;

    }

    /**
     * Download CSV
     * @var $file
     */
    public function download($file) {
        // Generate and download CSV file
        if($this->generateCSV($file)) {
            // Get parameters
            $file = urldecode($file); // Decode URL-encoded string
            
            // Process download
            if(file_exists($file)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($file).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                flush(); // Flush system output buffer
                readfile($file);
                exit;
            }
        }
    }

    /**
     * Clear Logs from the db
     */
    public function clearLogs() {
        // Clear all the logs in the database
        return $this->modx->removeCollection('Log404Items', array());
    }

     /**
     * Clear single log from the db
     */
    public function clearLog($url) {
        // Get log object from a url
        $log = $this->modx->getObject('Log404Items', array('url', $url));

        if ($log !== null) {
            return $log->remove();
        } else {
            header('HTTP/1.1 500 Internal Server');
            header('Content-Type: application/json; charset=UTF-8');
            return json_encode(array('message' => 'ERROR: Log record with url ' .$url. ' not found', 'code' => 404));
        }
    }

    /**
     * Handle hit functionality for every duplicated log record
     * @var $url
     */
    public function hit($url)
    {
   
        $log = $this->modx->getObject('Log404Items', array('url' => $url));

        // Check if url has already been logged and increase hit count
        if ($log !== null) {
            $hitCount = intval($log->get('hit'));
            $log->set('hit', $hitCount + 1);
            $log->save();
            return true;
        }

        return false;

    }
}