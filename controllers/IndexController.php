<?php
namespace Log404;

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';
class IndexController extends \modExtraManagerController {

    // One place where you can load a custom style-sheet for your manager pages is in the __construct():
    function __construct(\modX &$modx,$config = array()) {
        parent::__construct($modx,$config);
        $this->config['core_path'] = $this->modx->getOption('log404.core_path', null, MODX_CORE_PATH.'components/log404/');
        $this->config['assets_url'] = $this->modx->getOption('log404.assets_url', null, MODX_ASSETS_URL.'components/log404/');
        $this->config['model_path'] = $this->modx->getOption('log404.model_path', null, MODX_CORE_PATH.'components/log404/model/');

        // Init the class
        $this->log404 = $this->modx->getService('log404','Log404', $this->config['core_path'] .'model/');

        $this->modx->addPackage('log404', $this->config['model_path'], 'modx_');

        // Register assets for the plugin
        $this->modx->regClientCSS($this->config['assets_url'] . 'css/app.css');
        $this->modx->regClientCSS($this->config['assets_url'] . 'css/datatables.min.css');
        $this->modx->regClientCSS($this->config['assets_url'] . 'bootstrap/css/bootstrap.min.css');
        $this->modx->regClientStartupScript($this->config['assets_url'] . 'js/jquery-3.4.1.min.js');
        $this->modx->regClientStartupScript($this->config['assets_url'] . 'js/datatables.min.js');
        $this->modx->regClientStartupScript($this->config['assets_url'] . 'js/app.js');
    }

    /**
     * Variables to use in view
     * 
     */

    public $data = array();
    public $error = array();



    /**
     * Defines the lexicon topics to load in our controller.
     * @return array
     */
    public function getLanguageTopics() {
        return array('log404');
    }

    /**
     * Override parent function.
     * Override Smarty. I don't wants it. But BEWARE: the loadHeader and loadFooter bits require
     * the functionality of the original fetchTemplate function.  ARRRGH.  You try to escape but you can't.
     *
     * @param string $file (relative to the views directory)
     * @return rendered string (e.g. HTML)
     */
    public function fetchTemplate($file, $vars = array()) {
        // Conditional override! Gross! 
        // If we don't give Smarty a free pass, we end up with "View file does not exist" errors because
        // MODX relies on the parent fetchTemplate function to load up its header.tpl and footer.tpl files. Ick.
        if (substr($file,-4) == '.tpl') {
            return parent::fetchTemplate($file);
        }
        $path = $this->modx->getOption('log404.core_path','', MODX_CORE_PATH.'components/log404/').'views/';

        if (!is_file($path.$file)) {
            return $this->modx->lexicon('view_not_found', array('file'=> 'views/'.$file));
        }

        ob_start();

        extract($vars);

        include $path.$file;

        $content = ob_get_clean();

        return $content;
    }

    /**
     * The page title for this controller
     * @return string The string title of the page
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('log404.cmp.title');
    }

    /**
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
        // prepare and process log records
        $header = explode(',', $this->log404->settings['header_cols']);
        $logs = $this->log404->getLogs();
        
        // Return index view
        return $this->fetchTemplate('index.php', compact('header', 'logs'));
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