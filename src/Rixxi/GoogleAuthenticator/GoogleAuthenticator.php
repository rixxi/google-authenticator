<?php

namespace Rixxi\GoogleAuthenticator;

use Base32\Base32;


class GoogleAuthenticator
{

	const
		DIGITS = 6,
		POW_10_DIGITS = 1000000;


	/** @var Seed */
	private $seed;

	/** @var ITimestampProvider */
	private $timestampProvider;

	/** @var int */
	private $period;


	/**
	 * @param Seed|string|null
	 * @param ITimestampProvider|int|null
	 * @param int
	 * @throws \Exception
	 */
	public function __construct($seed = NULL, $timestamp = NULL, $period = 30)
	{
		if ($seed === NULL) {
			$this->seed = Seed::generate();

		} elseif (is_string($seed) && ($decoded = Base32::decode($seed)) !== '') {
			$this->seed = new Seed($decoded);

		} elseif ($seed instanceof Seed) {
			$this->seed = $seed;

		} else {
			throw new \Exception('Seed must be valid base32 encoded string or instance of Seed.');
		}

		if ($timestamp === NULL) {
			$this->timestampProvider = SystemTimestampProvider::getInstance();

		} elseif (is_int($timestamp) || ctype_digit($timestamp)) {
			$this->timestampProvider = new ConstantTimestampProvider((int) $timestamp);

		} elseif ($timestamp instanceof ITimestampProvider) {
			$this->timestampProvider = $timestamp;

		} else {
			throw new \Exception('Timestamp must be integer or instance of ITimestampProvider.');
		}

		if ((is_int($period) || ctype_digit($period)) && $period > 0) {
			$this->period = (int) $period;

		} else {
			throw new \Exception('Period must be integer greater then 0.');
		}
	}


	/**
	 * @return Seed
	 */
	public function getSeed()
	{
		return $this->seed;
	}


	/**
	 * Verify authenticator code
	 *
	 * @param string
	 * @param int
	 * @return bool
	 */
	public function verify($code, $window = 0)
	{
		$counter = $this->computeCounter() - $window;
		$max = $counter + $window * 2;
		do {
			if ($this->computeOneTimePassword($counter) === $code) {
				return TRUE;
			}
		} while (++$counter <= $max);

		return FALSE;
	}


	/** @return string */
	public function computeCode()
	{
		return $this->computeOneTimePassword($this->computeCounter());
	}

	/**
	 * @param int
	 * @return string[]
	 */
	public function computeCodes($window)
	{
		$counter = $this->computeCounter() - $window;
		$max = $counter + $window * 2;
		do {
			$codes[] = $this->computeOneTimePassword($counter);
		} while (++$counter <= $max);

		return $codes;
	}


	private function computeOneTimePassword($counter)
	{
		$hash = hash_hmac('sha1', pack('NN', 0, $counter), $this->seed->getValue(), TRUE);
		return str_pad(self::computeNumber($hash), self::DIGITS, '0', STR_PAD_LEFT);
	}


	private function computeCounter()
	{
		return floor($this->timestampProvider->getTimestamp() / $this->period);
	}


	static private function computeNumber($hash)
	{
		$offset = ord($hash[19]) & 0xf;

		return (
			(ord($hash[$offset    ]) & 0x7f) << 24 |
			(ord($hash[$offset + 1]) & 0xff) << 16 |
			(ord($hash[$offset + 2]) & 0xff) <<  8 |
			ord($hash[$offset + 3]) & 0xff
		) % self::POW_10_DIGITS;
	}

}
