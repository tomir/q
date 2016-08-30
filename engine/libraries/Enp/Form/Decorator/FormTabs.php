<?php
namespace Enp\Form\Decorator; 

class FormTabs extends \Zend_Form_Decorator_Abstract
{
    public function render($content) {
		
        $element = $this->getElement();
 
        if(!$element instanceof \Enp\Form) {
            return $content;
        }
		
        if(null === $element->getView()) {
            return $content;
        }
		
        return $element->getView()->formTabs($content,
                                             $element->getName(),
                                             $element->getSubForms());
    }
}