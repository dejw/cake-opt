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
     * 
     * TODO: parse parametres
     */
    public function processNode(Opt_Xml_Node $node){
        /* Parse attributes */
        $url = array(
            'controller' => array(self::REQUIRED, self::HARD_STRING),
            'action'     => array(self::REQUIRED, self::HARD_STRING)
        );
        $optional = array(
            'params'     => array(self::OPTIONAL, self::HARD_STRING, ''),
            'anchor'     => array(self::OPTIONAL, self::HARD_STRING, null),
            'full'       => array(self::OPTIONAL, self::BOOL, false)
        );
        $this->_extractAttributes($node, $url);
        $this->_extractAttributes($node, $optional);

        /* ... and remove them */
		$node->removeAttribute('controller');
        $node->removeAttribute('action');
        $node->removeAttribute('params');
        $node->removeAttribute('anchor');
        $node->removeAttribute('full');

        /* Generate the url */
        $url = h(Router::url($url, $optional['full']));
			
        $node->set('call:attribute-friendly', true);
        $node->set('nophp', true);
        $node->set('noEntitize', true);

        $node->addBefore(Opt_Xml_Buffer::TAG_NAME, "a");
        $node->addAttribute(new Opt_Xml_Attribute('href', $url));

        /* Add anchor when it is available */
        if($node->get('single') and is_null($optional['anchor']))
            throw new Opt_AttributeNotDefined_Exception('anchor', 'single opt:link_to tag');
        
        $node->appendChild(new Opt_Xml_Text($optional['anchor']));

        $node->set('postprocess', true);
        $this->_process($node);
    }

    public function postprocessNode(Opt_Xml_Node $node){
        $node->setNamespace(null);
    }
}

?>