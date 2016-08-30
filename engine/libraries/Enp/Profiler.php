<?php

namespace Enp;

/**
 * @example  
 * 		$profiler = new \Enp\Profiler() 
 * 		...do something...
 * 		$result = $profiler->stop(); 
 */
class Profiler
{
	/**
	 * @var float
	 */
	private $_timeStart;

	public function __construct()
	{
		$this->start();
	}

	public function start()
	{
		$this->_timeStart = microtime(true);
	}

	/**
	 * @return array time / memory 
	 */
	public function stop()
	{
		return array(
			'time' => (microtime(true) - $this->_timeStart),
			'memory' => $this->getMemoryUsage()
		);
	}

	public function getMemoryUsage()
	{
		$size = memory_get_usage(true);
		$unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
		return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
	}

	public function printStop()
	{
		print_r($this->stop());
	}
}