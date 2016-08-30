<?php

namespace Enp\Cycle;

/**
 * @category Enp
 * @package  Enp_Cycle
 * @author	 Artur Świerc
 */
class InputFilter extends \Zend\InputFilter\InputFilter
{
	public function __construct() 
	{
		$this->add(array(
			'name' => 'cycle_type', 
			'required' => true, 
			'validators' => array(
				array('name' => 'not_empty')
			)
		));
		
		$this->add(array(
			'name' => 'start_hour', 
			'required' => true, 
			'validators' => array(
				array('name' => 'not_empty'), 
				array(
					'name' => 'regex', 
					'options' => array(
						'pattern' => "/^\d{2}\:\d{2}(:\d{2})*$/"
					)
				)
			)
		));
		
		$this->add(array(
			'name' => 'stop_hour', 
			'required' => true, 
			'validators' => array(
				array('name' => 'not_empty'), 
				array(
					'name' => 'regex', 
					'options' => array(
						'pattern' => "/^\d{2}\:\d{2}(:\d{2})*$/"
					)
				)
			)
		));
		
		$this->add(array(
			'name' => 'start_date', 
			'required' => true, 
			'validators' => array(
				array('name' => 'not_empty'), 
				array(
					'name' => 'date'
				)
			)
		));
		
		$this->add(array(
			'name' => 'end_condition', 
			'required' => true, 
			'validators' => array(
				array('name' => 'not_empty')
			)
		));
	}
}
