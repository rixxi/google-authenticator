<?php

namespace Rixxi\GoogleAuthenticator;

use Nette\Utils\Strings;
use Base32\Base32;


/**
 * Basket for keeping seed binary value
 */
class Seed
{

	static public function generate($length = 16)
	{
		return new static(Base32::decode(Strings::random($length, 'A-Z2-7')));
	}


	/** @var string */
	private $value;


	/**
	 * @param string
	 * @throws \Exception
	 */
	public function __construct($value)
	{
		$this->value = $value;
	}


	public function getValue()
	{
		return $this->value;
	}


	public function __toString()
	{
		return Base32::encode($this->value);
	}

}
