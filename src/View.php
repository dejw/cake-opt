<?php

/*
 * View class for Open Power Template library
 * @author: Dawdid Fatyga
 */

class Opc_View extends View {
    
	function __construct(&$controller, $register = true){
		parent::__construct($controller, $register);

		$this->ext = ".tpl";
	}

	function render($action = null, $layout = null, $file = null) {
		if ($this->hasRendered) {
			return true;
		}
		$out = null;

		if ($file != null) {
			$action = $file;
		}

        $renderer = new Opt_Output_Return();
        if ($action !== false && $viewFileName = $this->_getViewFileName($action)) {
            $view = new Opt_View($viewFileName);

            if ($layout === null) {
                $layout = $this->layout;
            }

            if ($layout && $this->autoLayout) {
                $out = $this->renderLayout($view, $layout);
            } else {
                $out = $renderer->render($view);
            }

            $this->hasRendered = true;
            return $out;
        } else {
            throw new Opt_Exception;
        }
	}

	function renderLayout($content_for_layout, $layout = null) {
        
        $renderer = new Opt_Output_Return();
		$layoutFileName = $this->_getLayoutFileName($layout);
        $layout = new Opt_View($layoutFileName);
        $layout->content = $content_for_layout;

        return $renderer->render($layout);
	}

	function renderCache($filename, $timeStart) {
        throw new Opt_Exception;
	}

	function _getViewFileName($name = null) {
		$subDir = null;

		if (!is_null($this->subDir)) {
			$subDir = $this->subDir . DS;
		}

		if ($name === null) {
			$name = $this->action;
		}
		$name = str_replace('/', DS, $name);

		if (strpos($name, DS) === false && $name[0] !== '.') {
			$name = $this->viewPath . DS . $subDir . Inflector::underscore($name);
		} elseif (strpos($name, DS) !== false) {
            $name = $this->viewPath . DS . $subDir . $name;
    	}
        
        if(substr($name, -3) !== $this->ext)
            $name .= $this->ext;

        return $name;
	}

	function _getLayoutFileName($name = null) {
		if ($name === null) {
			$name = $this->layout;
		}
		$subDir = null;

		if (!is_null($this->layoutPath)) {
			$subDir = $this->layoutPath . DS;
		}

		$name = 'layouts' . DS . $subDir . $name;

        if(substr($name, -3) !== $this->ext)
            $name .= $this->ext;

        return $name;
	}
}

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

?>