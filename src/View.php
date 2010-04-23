<?php

try {
    $tpl = new Opt_Class;
    $tpl->sourceDir = ROOT . DS . APP_DIR .  '/views/';
    $tpl->compileDir = ROOT. DS . APP_DIR .'/views/compiled/';
    $tpl->contentType = Opt_Output_Http::HTML;
    $tpl->charset = 'utf-8';

    $tpl->register(Opt_Class::OPT_INSTRUCTION, 'LinkTo', 'Opc_Instruction_LinkTo');

    $tpl->setup();
} catch(Opt_Exception $exception) {
    Opt_Error_Handler($exception);
}


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

    /* Name of layout to use with this View. */
	var $layout = 'default';

    /* Title HTML element of this View. */
	var $pageTitle = false;

    /* Turns on or off Cake's conventional mode of rendering views. On by default. */
	var $autoRender = true;

    /* Turns on or off Cake's conventional mode of finding layout files. On by default. */
	var $autoLayout = true;

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
        'here', 'layout', 'name', 'pageTitle', 
		'params', 'data', 'passedArgs',
	);

    /* This Output class simply returns template */
    var $renderer = null;

    /* Standard view configuration */
	function __construct(&$controller, $register = true){
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
		if ($this->hasRendered) return true;
		if ($file != null) $action = $file;
        if (is_null($action)) $action = $this->action;
        if ($layout === null) $layout = $this->layout;

        /* Render view with ginven action name */
        if ($action !== false && $viewFileName = $this->_getViewFileName($action)) {

            /* Create a view object for action */
            $view = new Opt_View($viewFileName);
            $this->_exportContext($view);
        
            if ($layout && $this->autoLayout) {
                $this->output = $this->renderLayout($view, $layout);
            } else {
                $this->output = $this->renderer->render($view);
            }

            $this->hasRendered = true;
            return $this->output;
        } else {
            throw new Opt_TemplateNotFound_Exception;
        }
	}

    /*
     * Render the layout
     *
     * To embed content of a view write:
     *
     *      <opt:include view="$content" />
     */
	function renderLayout($content, $layout = null) {
		$layoutFileName = $this->_getLayoutFileName($layout);

        /* Create view object for layout */
        $layout = new Opt_View($layoutFileName);
        $layout->content = $content;
        $this->_exportContext($layout);

        /* Render the layout */
        return $this->renderer->render($layout);
	}

    /* Returns file name for view */
	function _getViewFileName($name = null) {
        return $this->_getFileName($name, $this->action);
	}

    /* Returns file name for layout */
	function _getLayoutFileName($name = null) {
        return $this->_getFileName($name, $this->layout, 'layouts');
	}

    /* Creates filename for view or layout */
    function _getFileName($name, $default, $directory = null){

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
    function _exportContext($view){
        foreach($this->viewVars as $var => $value){
            $view->{$var} = $value;
        }
    }
}

?>