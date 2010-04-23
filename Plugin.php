<?php

/*
 * Bootstrap file for Open Power Cake plugin.
 * @author: Dawid Fatyga
 */

define('OPC_ROOT', dirname(__FILE__));
define('OPT_LIB_ROOT',  OPC_ROOT . DS . 'libs' . DS);
define('OPC_SRC_ROOT',  OPC_ROOT . DS . 'src' . DS);

/* Load and configure Open Power Template Libs */
require_once(OPT_LIB_ROOT . DS . 'Opl' . DS . 'Base.php');
Opl_Loader::setDirectory(OPT_LIB_ROOT);
Opl_Loader::mapLibrary('Opl', OPT_LIB_ROOT . 'Opl' . DS);
Opl_Loader::mapLibrary('Opt', OPT_LIB_ROOT . 'Opt' . DS);
Opl_Loader::mapLibrary('Opc', OPC_SRC_ROOT);
Opl_Loader::setHandleUnknownLibraries(false);
Opl_Loader::register();

?>
