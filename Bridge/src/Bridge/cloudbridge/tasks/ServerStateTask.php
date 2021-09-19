<?php
/* Copyright (c) 2021 Florian H. All rights reserved. */
namespace Bridge\cloudbridge\tasks;

use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use pocketmine\utils\MainLogger;
use Bridge\cloudbridge\Main;

class ServerStateTask extends Task{

	public function onRun(): void{
		try {
			$file = new Config(Main::getInstance()->file . Main::getInstance()->name . ".json", Config::JSON);
			$list = [];
			foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player) {
				$list[] = $player->getName();
			}
			//Update files
			if (!Main::$inGame) {
					$file->setAll([
						"count"   => count(Main::getInstance()->getServer()->getOnlinePlayers()),
						"max"     => Main::getInstance()->max,
						"list"    => $list,
						"port"    => Main::getInstance()->getServer()->getPort(),
						"ingame"  => false,
						"offline" => false
					]);
					$file->save();
			} else {
				$file->setAll([
					"count"   => count(Main::getInstance()->getServer()->getOnlinePlayers()),
					"max"     => Main::getInstance()->max,
					"list"    => $list,
					"port"    => Main::getInstance()->getServer()->getPort(),
					"ingame"  => true,
					"offline" => false
				]);
				$file->save();
			}
		} catch (\Exception $exception) {
			MainLogger::getLogger()->info(Main::PREFIX . "crashed. Regenerate!");
		}
	}
}
