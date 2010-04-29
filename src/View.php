<?php

require "Exception.php";

function def($value, $default){
    if($value !== null) return $value;
    return $default;
}

/*
 * View class for Open Power Template library
 * @author: Dawdid Fatyga
*/

class OpenPowerTemplateView extends Object {

    /*
     * @var: Opt_Class
     */
    static private $_opt = null;
    
    /*
     * @var: Opt_Output_Return
     */
    static private $_renderer = null;

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

    /*
     * Initializes the Open Power Template library
     *
     * Used configuration variables:
     *   Opt.compileDir  -- directory to place compiled template (relative to APP/views)
     *   Opt.charser     -- charset of templates (default is "utf-8")
     */
    private static function initialize(){
        if(is_object(self::$_opt)) return;
        
        if(Opl_Registry::exists('opt'))
            self::$_opt = Opl_Registry::get('opt');
        else
            self::$_opt = new Opt_Class;

        /* configure compile path */
        $compileDir = def(Configure::read("Opt.compileDir"), 'views'. DS . 'compiled');
        $compileDir = trim($compileDir, "/\\");

        self::$_opt->sourceDir = ROOT . DS . APP_DIR . DS . 'views' . DS;
        self::$_opt->compileDir = ROOT. DS . APP_DIR . DS . $compileDir . DS;
        self::$_opt->charset = def(Configure::read("Opt.charset"), 'utf-8');
        
        /* Recompile template every request on debug */
        if(Configure::read() > 0){
            self::$_opt->compileMode = Opt_Class::CM_REBUILD;
            Opl_Registry::setState('opl_extended_errors', true);
        }

        /* Register instructions */
        self::$_opt->register(Opt_Class::OPT_NAMESPACE, "cake");
        self::$_opt->register(Opt_Class::OPT_INSTRUCTION, 'Html', 'Cake_Instruction_Html');

        self::$_opt->setup();

        self::$_renderer = new Opt_Output_Return();
    }

    /* Standard view configuration */
    function __construct(&$controller, $register = true) {
        self::initialize();
        
        if (is_object($controller)) {
            foreach($this->__passedVars as $var)
                $this->{$var} = $controller->{$var};

            /* Controller name is specific */
            $this->controller = $this->name;
        }

        parent::__construct();

        if ($register) ClassRegistry::addObject('view', $this);
    }

    /* Renders the single action */
    function render($action = null, $layout = null, $file = null) {
        try {
            if ($this->hasRendered) return true;

            if ($file != null) $action = $file;
            if (is_null($action)) $action = $this->action;
            if ($layout === null) $layout = $this->layout;

            /* Render view with given action name */
            if ($action !== false && $viewFileName = $this->_getViewFileName($action)) {

                /* Create a view object for action */
                $view = new Opt_View($viewFileName);
                $this->_exportContext($view);

                /* Inherit with given the layout */
                if ($layout)
                    $view->inherit($this->_getLayoutFileName($layout));

                /* Render the view */
                $this->output = self::$_renderer->render($view);

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