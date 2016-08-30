<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Observer
 *
 * @author tomaszcisowski
 */
class Otomoto_Observer implements SplSubject {
	
	private $_observers = array();

	
	/**
	* Attach an observer
	*
	* @param SplObserver $observer
	*/
	public function attach(SplObserver $observer)
	{
		array_push($this->_observers, $observer);
	}

	/**
	* Detach an observer
	*
	* @param SplObserver $observer
	*/
	public function detach(SplObserver $observer)
	{
		foreach ($this->_observers as $key => $item)
		{
			if ($observer == $item) {
				unset($this->_observers[$key]);
			}
		}
	}
	
	/**
	* Send notification to all observers
	*/
	public function notify()
	{
		foreach ($this->_observers as $key => $item) {
			$item->update($this);
		}
	}

}

?>
