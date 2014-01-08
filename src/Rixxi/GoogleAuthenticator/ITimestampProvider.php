<?php

namespace Rixxi\GoogleAuthenticator;


interface ITimestampProvider
{

	/** @return int */
	function getTimestamp();

}
