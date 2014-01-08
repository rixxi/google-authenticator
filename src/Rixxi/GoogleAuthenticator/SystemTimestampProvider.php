<?php

namespace Rixxi\GoogleAuthenticator;


class SystemTimestampProvider implements ITimestampProvider
{

	static public function getInstance()
	{
		static $instance = NULL;
		if ($instance === NULL) {
			$instance = new static;
		}
		return $instance;
	}


	public function getTimestamp()
	{
		return time();
	}

}
