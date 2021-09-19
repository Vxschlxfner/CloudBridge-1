<?php
/* Copyright (c) 2021 Florian H. All rights reserved. */
namespace Bridge\cloudbridge\commands;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Bridge\cloudbridge\Main;

class RunCommand extends Command{

	public function __construct(){
		parent::__construct("run");
		$this->setDescription("Run Command");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if ($sender->hasPermission("cloudbridge.command.run")){
			if (Main::$inGame === false){
				Main::$inGame = true;
				$sender->sendMessage(Main::PREFIX . "§aYou have enabled the §eRunning§8-§eMode§7!");
			} else {
				Main::$inGame = false;
				$sender->sendMessage(Main::PREFIX . "§cYou have disabled the §eRunning§8-§eMode§7!");
			}
		} else {
			$sender->sendMessage("§cYou don't have the Permissions to use this command!");
		}
	}
}
