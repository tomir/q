<?php

namespace Enp; 

/**
 * @package Enp
 * @author  Artur Świerc
 */
class Form extends \Zend_Form 
{
	/**
	 * @param array|null $options
	 */
	public function __construct($options = null) 
	{			
		$this->addPrefixPath('\Enp\Form\Element\\', __DIR__ . '/Form/Element/', \Zend_Form::ELEMENT);
		$this->addPrefixPath('\Enp\Form\Decorator\\', __DIR__ . '/Form/Decorator/', \Zend_Form::DECORATOR);
		$this->addElementPrefixPath('\Enp\Filter\\', __DIR__ . '/Filter/', 'filter');
		$this->addElementPrefixPath('\Enp\Validate\\', __DIR__ . '/Validate/', 'validate');
		
		$this->setView(new \Zend_View());
		$this->setDisableLoadDefaultDecorators(true);
		
		// form decorators 
		$this->addDecorator('FormElements')
				->addDecorator('htmlTag')
				->addDecorator('Form');	
		// element decorators 
		$this->setElementDecorators(array('ViewHelper', 'Errors', 'Label'));
		
		parent::__construct($options);
	}
	
	/**
     * Add a new element
     *
     * $element may be either a string element type, or an object of type
     * Zend_Form_Element. If a string element type is provided, $name must be
     * provided, and $options may be optionally provided for configuring the
     * element.
     *
     * If a Zend_Form_Element is provided, $name may be optionally provided,
     * and any provided $options will be ignored.
     *
     * @param  string|Zend_Form_Element $element
     * @param  string $name
     * @param  array|Zend_Config $options
     * @throws Zend_Form_Exception on invalid element
     * @return Zend_Form
     */
	public function addElement($element, $name = null, $options = null) 
	{
		parent::addElement($element, $name, $options);
		
		$elementName = $this->getElement($name);
		$elementName->setView($this->getView());	
		return $elementName;
	}
}
