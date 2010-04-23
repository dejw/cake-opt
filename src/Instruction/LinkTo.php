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
    
    /* Renders the anchor */
    public function processNode(Opt_Xml_Node $node) {
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
        
        $url = h(Router::url($url, $optional['full']));
        $anchor = $optional['anchor'] or $url;

        $node->set('nophp', true);

        /* The content is the anchor */
        if($node->hasChildren()){
            $node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, "<a href='$url'>");
            $node->addBefore(Opt_Xml_Buffer::TAG_AFTER, "</a>");
            $this->_process($node);
        } else {
            $node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, "<a href='$url'>$anchor</a>");
        }
    }
}

?>