<?php

namespace Enp\Form\Element;

class Time extends \Zend_Form_Element_Xhtml
{
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formTimepicker';

	public function __construct($spec, $options = null)
    {
		$timeValidator = (isset($options['time_validator'])) ? $options['time_validator'] : true;
		
        parent::__construct($spec, $options);
        $this->setAllowEmpty(true)
             ->setAutoInsertNotEmptyValidator(false);
		
		if ($timeValidator) { 
			$this->addValidator(new \Enp\Validate\Time(), true);
		}
    }
}
