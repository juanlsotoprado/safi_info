<?php
class TimeCounter
{
	var $startTime;
	var $endTime;
	
	function TimeCounter()
	{
		$this->startTime=0;
		$this->endTime=0;
	}
	function microtime_float()
	{
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}
	function getTimestamp()
	{
		$timeofday = gettimeofday();
		//RETRIEVE SECONDS AND MICROSECONDS (ONE MILLIONTH OF A SECOND)
		//CONVERT MICROSECONDS TO SECONDS AND ADD TO RETRIEVED SECONDS
		//MULTIPLY BY 1000 TO GET MILLISECONDS
		 return 1000*($timeofday['sec'] + ($timeofday['usec'] / 1000000));
	}
	function startCounter()
	{
		//$this->startTime=$this->getTimestamp();
		//$this->startTime = $this->microtime_float();
		$this->startTime = microtime(true);
	}
	function stopCounter()
	{
		//$this->endTime=$this->getTimestamp();
		//$this->endTime = $this->microtime_float();
		$this->endTime = microtime(true);
	}
	function getElapsedTime()
	{
		//RETURN DIFFERECE IN MILLISECONDS
		//return number_format(($this->endTime)-($this->startTime), 2);
		return ($this->endTime)-($this->startTime);
	}
}
?>