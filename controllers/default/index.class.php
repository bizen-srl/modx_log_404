<?php
// Gotta do this here because we don't have a reliable event for this. 
require_once dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';
class Log404IndexManagerController extends \modExtraManagerController {

    public static $routing_key = 'page';

    /**
     * @static
     *
     * @param modX $modx A reference to the modX object.
     * @param string $className The name of the class that is being requested.
     * @param array $config A configuration array of options related to this controller's action object.
     *
     * @return The class specified by $className
     */
    public static function getInstance(\modX &$modx, $className, array $config = array()) {
        // Manual routing
        $className = (isset($_GET[self::$routing_key])) ? '\\Log404\\'.ucfirst($_GET[self::$routing_key]).'Controller': '\\Log404\\IndexController';
        unset($_GET[self::$routing_key]);
        /** @var modManagerController $controller */
        $controller = new $className($modx,$config);
        return $controller;
    }


}
/*EOF*/