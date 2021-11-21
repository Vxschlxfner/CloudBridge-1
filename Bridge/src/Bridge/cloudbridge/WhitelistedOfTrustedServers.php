<?php
/* Copyright (c) 2021 Florian H. All rights reserved. */
namespace Bridge\cloudbridge;

use Bridge\cloudbridge\utils\InternetAddress;

class WhitelistedOfTrustedServers{
	/** @var InternetAddress */
	protected static $list = [];

	/**
	 * Function registerServer
	 * @param InternetAddress $address
	 * @return void
	 * @throws \Exception
	 */
	static function registerServer(InternetAddress $address): void{
		if (isset(self::$list[$address->getIp()][$address->getPort()])) {
			throw new \Exception("Server {$address->getIp()}:{$address->getPort()} is already added in our whitelist.");
		}
		if (!isset(self::$list[$address->getIp()])) {
			self::$list[$address->getIp()] = [];
		}
		self::$list[$address->getIp()][] = $address->getPort();
	}

	/**
	 * Function unregisterServer
	 * @param InternetAddress $address
	 * @return void
	 * @throws \Exception
	 */
	static function unregisterServer(InternetAddress $address): void{
		if (isset(self::$list[$address->getIp()]) && isset(self::$list[$address->getIp()][$address->getPort()])) {
			unset(self::$list[$address->getIp()][$address->getPort()]);
		} else {
			throw new \Exception("Server {$address->getIp()}:{$address->getPort()} not found in our whitelist.");
		}
	}

	/**
	 * Function isWhitelisted
	 * @param InternetAddress $address
	 * @return bool
	 */
	static function isWhitelisted(InternetAddress $address): bool{
		return isset(self::$list[$address->getIp()][$address->getPort()]);
	}
}
