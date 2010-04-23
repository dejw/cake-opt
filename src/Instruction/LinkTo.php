<?php

/*
 * opt:link_to tag -- uses internal CakePHP's Router to form an anchor
 * @author: Dawid Fatyga
 */
class Opc_Instruction_LinkTo extends Opt_Compiler_Processor {
    protected $_name = 'link_to';

    /* Configures the instruction */
    public function configure(){
        $this->_addInstructions('opt:link_to');
    }
    
    /*
     * Renders the anchor
     * TODO: fix parameters escaping
     */
    public function processNode(Opt_Xml_Node $node){
        /* Parse attributes */
        $url = array(
            'controller' => array(self::REQUIRED, self::HARD_STRING),
            'action'     => array(self::REQUIRED, self::HARD_STRING)
        );
        $optional = array(
            'anchor'     => array(self::OPTIONAL, self::HARD_STRING, null),
            'full'       => array(self::OPTIONAL, self::BOOL, false),
            '__UNKNOWN__' => array(self::OPTIONAL, self::EXPRESSION, null)
        );
        $this->_extractAttributes($node, $url);
        $unknown = $this->_extractAttributes($node, $optional);

        $params = $this->_extractParams($unknown);
        
        /* ... and remove them */
		$node->removeAttribute('controller');
        $node->removeAttribute('action');
        $node->removeAttribute('anchor');
        $node->removeAttribute('full');

        foreach($unknown as $param => $value)
            $node->removeAttribute($param);

        $node->set('call:attribute-friendly', true);
        $node->set('noEntitize', true);

        /* Add anchor when it is available */
        if($node->get('single') and is_null($optional['anchor']))
            throw new Opt_AttributeNotDefined_Exception('anchor', 'single opt:link_to tag');

        $node->appendChild(new Opt_Xml_Text($optional['anchor']));

        /* Output anchor now If we have only controller and action attributes */
        if(count($params) == 0){
            /* Generate the url */
            $url = h(Router::url(array_merge($url, $params), $optional['full']));
            $node->set('nophp', true);
            
            $node->addBefore(Opt_Xml_Buffer::TAG_NAME, "a");
            $node->addAttribute(new Opt_Xml_Attribute('href', $url));
        } else {
            $node->set('dynamic', true);
            /* otherwise emit the url generator */
            $href = "<?php echo h(Router::url(array(" .
                    'controller => "' . $url['controller']. '",' .
                    'action => "' . $url['action']. '",';
            foreach($params as $param)
                $href .= $param . ",";

            $href .= "), " . ($optional['full'] ? "true" : "false") . ")); ?>";
            
            $node->addBefore(Opt_Xml_Buffer::TAG_NAME, "echo 'a';");
            $attr = new Opt_Xml_Attribute('href', $href);
            $node->addAttribute($attr);
        }
        $node->set('postprocess', true);
        $this->_compiler->set('escape', false);
        $this->_process($node);
    }

    public function postprocessNode(Opt_Xml_Node $node){
        $node->setNamespace(null);
    }

    /* Searches for parameters named param* = value */
    private function _extractParams($params){
        $return = array();
        foreach($params as $name => $value){
            if(preg_match("/^param(.*)/", $name))
                $return[$name] = $value;
        }
        ksort($return);
        return array_values($return);
    }
}

?>