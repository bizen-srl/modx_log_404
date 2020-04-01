<?php
namespace Log404;


class ClearController extends \modExtraManagerController {

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
        $this->log404->clearLogs();

        // Redirect to index page
        $this->modx->sendRedirect($this->page('index'));

    }

    /**
     * Gotta look up the URL of our CMP and its actions
     *
     * @param string $page default: index
     * @param array any optional arguments, e.g. array('action'=>'children','parent'=>123)
     * @return string
     */
    public static function page($page='index',$args=array()) {
        $url = MODX_MANAGER_URL;
        $url .= '?a=index&namespace=log404&page='.$page;
        if ($args) {
            foreach ($args as $k=>$v) {
                $url.='&'.$k.'='.$v;
            }
        }

        return $url;
    }


}