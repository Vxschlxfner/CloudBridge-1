<?php
/* Copyright (c) 2021 Florian H. All rights reserved. */
namespace Bridge\cloudbridge;

use pocketmine\Server;
use pocketmine\utils\Config;

class ServerManager{
	/**
	 * Function getServerId
	 * @return string
	 */
	static function getServerId(): string{
		return (new Config(Server::getInstance()->getDataPath() . "server.properties"))->get("motd", "");
	}

	/**
	 * Function getServerUuid
	 * @return string
	 */
	static function getServerUuid(): string{
		return (new Config(Server::getInstance()->getDataPath() . "server.properties"))->get("server-uuid", "");
	}

	/**
	 * Function getCloudPassword
	 * @return string
	 */
	static function getCloudPassword(): string{
		return (new Config(Server::getInstance()->getDataPath() . "server.properties"))->get("cloud-password");
	}
}
