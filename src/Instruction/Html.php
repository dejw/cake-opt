<?php

/*
 * Set ot html helper instructions. Includes:
 *
 * opt:link_to -- uses internal CakePHP's Router to form an anchor
 * opt:url     -- renders only the url
 * 
 * @author: Dawid Fatyga
 */
class Opc_Instruction_Html extends Opt_Compiler_Processor {
    protected $_name = 'html';

    /* Configures the instruction */
    public function configure(){
        $this->_addInstructions(array('opt:link_to', 'opt:url'));
    }
    
    /*
     * Renders the anchor
     */
    public function _processLink_to(Opt_Xml_Node $node){
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
        $optional['attribute'] = 'href';
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
            /* otherwise emit the url generator */
            $node->addBefore(Opt_Xml_Buffer::TAG_NAME, "echo 'a';");
            $this->_buildCode($node, array_merge($url, $params), $optional);
        }
        $node->set('postprocess', true);
        $this->_compiler->set('escape', false);
        $this->_process($node);
    }

    public function _postprocessLink_to(Opt_Xml_Node $node){
        $node->setNamespace(null);
    }

    /*
     * Adds generated url to the parent node
     * Taken from: http://wiki.invenzzia.org/wiki/Opt_Instruction_Url
     */
    public function _processUrl(Opt_Xml_Node $node){
        /* Prevent from adding an attribute to OPT tags */
        if(!$node->getParent() instanceof Opt_Xml_Element)
            throw new Opt_InstructionInvalidParent_Exception($node->getXmlName(), 'any non-OPT tag');

        if($this->_compiler->isNamespace($node->getParent()->getNamespace()))
            throw new Opt_InstructionInvalidParent_Exception($node->getXmlName(), 'any non-OPT tag');

        /* Parse attributes */
        $url = array(
            'controller' => array(self::REQUIRED, self::HARD_STRING),
            'action'     => array(self::REQUIRED, self::HARD_STRING)
        );
        $optional = array(
            'attribute'     => array(self::OPTIONAL, self::HARD_STRING, "href"),
            'full'       => array(self::OPTIONAL, self::BOOL, false),
            '__UNKNOWN__' => array(self::OPTIONAL, self::EXPRESSION, null)
        );
        $this->_extractAttributes($node, $url);
        $unknown = $this->_extractAttributes($node, $optional);
        $params = $this->_extractParams($unknown);
        $url = array_merge($url, $params);
        
        if($node->hasChildren()){
            $attributes = $node->getElementsByTagNameNS('opt', 'attribute', false);

            if(sizeof($attributes) == 0){
                $this->_buildCode($node, $url, $optional, true);
                return;
            }

            foreach($attributes as $attr)
                $attr->set('attributeValueStyle', Opt_Instruction_Attribute::ATTR_RAW);

            $node->set('priv:url', $url);
            $node->set('priv:params', $optional);
            $node->set('postprocess', true);
            $this->_process($node);
        } else {
            $this->_buildCode($node, $url, $optional, true);
        }
    }

    public function _postprocessUrl(Opt_Xml_Node $node) {
        $attribute = $node->get('call:attribute');
        $url = $node->get('priv:url');
        $params = $node->get('priv:params');
        if(is_null($attribute)){
            $this->_buildCode($node, $url, $params, true);
            return;
        }

        /* Ok, we have the opt:attribute instructions right there */
        $code = ' $_routing_'.(self::$_cnt).' = array(';
        foreach($url as $name => $value){
            if(is_int($name)){
                $code .= $value . ",";
            } else {
                $code .= '"'. $name .'" => "' . $value  . '",';
            }
        }
        $code .= ")";

        foreach($attribute as $attr) {
            $attr->set('nophp', true);
            $code .= $attr->buildCode(Opt_Xml_Buffer::ATTRIBUTE_BEGIN);
            $code .= ' $_routing_'.(self::$_cnt).'['.$attr->buildCode(Opt_Xml_Buffer::ATTRIBUTE_NAME).'] = '.$attr->buildCode(Opt_Xml_Buffer::ATTRIBUTE_VALUE).'; '.$attr->buildCode(Opt_Xml_Buffer::ATTRIBUTE_END);
        }
        $node->getParent()->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $code);

        /* Create an attribute for the parent. */
        $attribute = new Opt_Xml_Attribute($params['attribute'], null);
        $attribute->addAfter(Opt_Xml_Buffer::ATTRIBUTE_VALUE, ' echo '.self::ROUTING_FUNCTION.'($_routing_'.(self::$_cnt).'); unset($_routing_'.(self::$_cnt).');');

        $node->getParent()->addAttribute($attribute);
        self::$_cnt++;

        $node->set('call:attribute', null);
        $node->set('priv:url', null);
        $node->set('priv:params', null);
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

    /* Builds the url string and injects the element */
    private function _buildCode(Opt_Xml_Node $node, Array $url, Array $params, $parent = false){
        $full = "false";
        if(isset($params['full'])){
            $full = ($params['full'] ? "true" : "false");
        }

        /* Build the code */
        $code = "echo h(Router::url(array(";
        foreach($url as $name => $value){
            if(is_int($name)){
                $code .= $value . ",";
            } else {
                $code .= '"'. $name .'" => "' . $value  . '",';
            }
        }      
        $code .= "), " . $full ."))";

        /* inject the attribute */
        $attr = new Opt_Xml_Attribute($params['attribute'], null);
        $attr->addAfter(Opt_Xml_Buffer::ATTRIBUTE_VALUE, $code);
        if($parent) $node->getParent()->addAttribute($attr);
        else $node->addAttribute($attr);
    }
}

?>