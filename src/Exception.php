<?php
/* 
 * Defines exceptions used in plugin
 * @author: Dawid Fatyga
 */

/* Thrown when action name is not given to a view */
class Opc_NoActionGiven_Exception extends Opt_API_Exception {
    protected $_message = 'no action was given to rendering process.';
}


?>
