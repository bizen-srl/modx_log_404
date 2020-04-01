<?php
namespace Log404;


class DownloadController extends \modExtraManagerController {

    // One place where you can load a custom style-sheet for your manager pages is in the __construct():
    function __construct(\modX &$modx,$config = array()) {
        parent::__construct($modx,$config);
        $this->config['core_path'] = $this->modx->getOption('log404.core_path', null, MODX_CORE_PATH.'components/log404/');
        $this->config['assets_url'] = $this->modx->getOption('log404.assets_url', null, MODX_ASSETS_URL.'components/log404/');
        
        // Init the class
        $this->log404 = $this->modx->getService('log404','Log404', $this->config['core_path'] .'model/');
    }

    /**
     * Variables to use in template
     * 
     */

    public $data = array();
    public $error = array();

    /**s
     * $this->scriptProperties will contain $_GET and $_POST stuff
     */
    public function initialize()
    {
        // Do any pre-processing stuff, e.g. $this->setPlaceholder()
    }

    /**
     * Do any page-specific logic and/or processing here
     * @param array $scriptProperties
     * @return void
     */
    public function process(array $scriptProperties = array())
    {   
        $csv = ($this->modx->getOption('log404.log_path') == false) ? $this->config['core_path'].'404-log.csv' : '';
        
        // Return download file handler
        return $this->log404->download($csv);
    }


}