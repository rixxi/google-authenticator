<?php

namespace Rixxi\GoogleAuthenticator;


class ConstantTimestampProvider implements ITimestampProvider
{

	/** @var int */
	private $timestamp;


	/**
	 * @param int
	 */
	public function __construct($timestamp)
	{
		$this->timestamp = $timestamp;
	}


	public function getTimestamp()
	{
		return $this->timestamp;
	}

}
