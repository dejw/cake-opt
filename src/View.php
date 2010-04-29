<?php

try {
    $tpl = new Opt_Class;

    /* TODO: make this configurable */
    $tpl->sourceDir = ROOT . DS . APP_DIR .  '/views/';
    $tpl->compileDir = ROOT. DS . APP_DIR .'/views/compiled/';
    $tpl->contentType = Opt_Output_Http::HTML;
    $tpl->charset = 'utf-8';
    
    /* Recompile template every request on debug */
    if(Configure::read() > 0){
        $tpl->compileMode = Opt_Class::CM_REBUILD;
        Opl_Registry::setState('opl_extended_errors', true);
    }

    /* TODO: This should be done automatically */
    $tpl->register(Opt_Class::OPT_INSTRUCTION, 'Html', 'Opc_Instruction_Html');
    $tpl->register(Opt_Class::OPT_NAMESPACE, "cake");
    
    $tpl->setup();
} catch(Opt_Exception $exception) {
    Opt_Error_Handler($exception);
}

class Opc_NoActionGiven_Exception extends Opt_API_Exception
{
    protected $_message = 'no action was given to rendering process.';
} // end Opt_TemplateNotFound_Exception;

/*
 * View class for Open Power Template library
 * @author: Dawdid Fatyga
*/

class Opc_View extends Object {

    /* Name of the controller. */
    var $controller = null;

    /* Action to be performed. */
    var $action = null;

    /* Array of parameter data */
    var $params = array();

    /* Current passed params */
    var $passedArgs = array();

    /* Array of data */
    var $data = array();

    /* Variables for the view */
    var $viewVars = array();

    /* Path for the view */
    var $viewPath = null;
    
    /* Name of layout to use with this View. */
    var $layout = 'default';

    /* Title HTML element of this View. */
    var $pageTitle = false;

    /* File extension. Defaults to Cake's template ".tpl". */
    var $ext = '.tpl';

    /* Sub-directory for this view file. */
    var $subDir = null;

    /* True when the view has been rendered. */
    var $hasRendered = false;

    /* Holds View output. */
    var $output = false;

    /* List of variables to collect from the associated controller */
    var $__passedVars = array(
            'viewVars', 'action', 'autoLayout', 'autoRender',
            'here', 'layout', 'name', 'pageTitle', 'viewPath',
            'params', 'data', 'passedArgs',
    );

    /* This Output class simply returns template */
    var $renderer = null;

    /* Standard view configuration */
    function __construct(&$controller, $register = true) {
        if (is_object($controller)) {
            foreach($this->__passedVars as $var)
                $this->{$var} = $controller->{$var};

            /* Controller name is specific */
            $this->controller = $this->name;
        }

        parent::__construct();

        if ($register) ClassRegistry::addObject('view', $this);
        $this->renderer = new Opt_Output_Return();
    }

    /* Renders the single action */
    function render($action = null, $layout = null, $file = null) {
        try {
            if ($this->hasRendered) return true;

            if ($file != null) $action = $file;
            if (is_null($action)) $action = $this->action;
            if ($layout === null) $layout = $this->layout;

            /* Render view with ginven action name */
            if ($action !== false && $viewFileName = $this->_getViewFileName($action)) {

                /* Create a view object for action */
                $view = new Opt_View($viewFileName);
                $this->_exportContext($view);

                /* Inherit with given the layout */
                if ($layout)
                    $view->inherit($this->_getLayoutFileName($layout));

                /* Render the view */
                $this->output = $this->renderer->render($view);

                $this->hasRendered = true;
                return $this->output;
            } else {
                throw new Opc_NoActionGiven_Exception;
            }
        } catch(Opt_Exception $e){
            Opt_Error_Handler($e);
        }

    }

    /* Returns file name for view */
    function _getViewFileName($name = null) {
        return $this->_getFileName($name, $this->action, $this->viewPath);
    }

    /* Returns file name for layout */
    function _getLayoutFileName($name = null) {
        return $this->_getFileName($name, $this->layout, 'layouts');
    }

    /* Creates filename for view or layout */
    function _getFileName($name, $default, $directory = null) {
        /* Set subdir if available */
        $subDir = null;
        if (!is_null($this->subDir)) $subDir = $this->subDir . DS;

        /* Fallback to default name */
        if ($name === null) $name = $default;
        $name = str_replace('/', DS, $name);

        /* Inflect the filename */
        if (strpos($name, DS) === false && $name[0] !== '.') {
            $name = $subDir . Inflector::underscore($name);
        } elseif (strpos($name, DS) !== false) {
            $name = $subDir . $name;
        }

        /* Prepend directory */
        if(!is_null($directory)) $subDir = $directory . DS . $subDir;

        $filename = $subDir . $name;

        if(substr($filename, -3) !== $this->ext) $filename .= $this->ext;
        return $filename;
    }

    /* Sets variables to the given view */
    function _exportContext($view) {
        foreach($this->viewVars as $var => $value) {
            $view->{$var} = $value;
        }
    }
}

?>