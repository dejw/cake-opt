<?php

/*
 * Bootstrap file for Open Power Cake plugin.
 * @author: Dawid Fatyga
 */

define('PLUGIN_ROOT', dirname(__FILE__));
define('OPT_LIB_ROOT',  PLUGIN_ROOT . DS . 'libs' . DS);
define('PLUGIN_SRC',  PLUGIN_ROOT . DS . 'src' . DS);

/* Load and configure Open Power Template Libs */
require_once(OPT_LIB_ROOT . DS . 'Opl' . DS . 'Base.php');
Opl_Loader::setDirectory(OPT_LIB_ROOT);
Opl_Loader::mapLibrary('Opl', OPT_LIB_ROOT . 'Opl' . DS);
Opl_Loader::mapLibrary('Opt', OPT_LIB_ROOT . 'Opt' . DS);

Opl_Loader::addLibrary('Cake', array('directory' => PLUGIN_SRC, 'handler' => null));
Opl_Loader::setHandleUnknownLibraries(false);
Opl_Loader::register();

require_once(PLUGIN_SRC . "View.php");

?>
