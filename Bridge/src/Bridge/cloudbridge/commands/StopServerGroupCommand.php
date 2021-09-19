<?php
/* Copyright (c) 2021 Florian H. All rights reserved. */
namespace Bridge\cloudbridge\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Bridge\cloudbridge\Main;
use Bridge\cloudbridge\packets\StopServerGroupPacket;
use Bridge\cloudbridge\SocketShit;

class StopServerGroupCommand extends Command{

	public function __construct(){
		parent::__construct("stopgroup");
		$this->setDescription("StopGroup Command");
	}

	/**
	 * Function execute
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 * @return mixed|void
	 * @throws \Exception
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            return;
        }
        if (!$sender->hasPermission("cloudbridge.stopgroup")) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage($this->getUsage());
            return;
        }

        if (Main::getInstance()->isTemplate($args[0])) {
            $packet = new StopServerGroupPacket();
            $packet->template = $args[0];
            SocketShit::sendPacket($packet);
            var_dump($packet);
            $sender->sendMessage(Main::PREFIX . "§cYou have stopped the §eServer§8-§eGroup§a of the §eTemplate {$args[0]}§7!");
        } else {
            $sender->sendMessage(Main::PREFIX . "§cThis §eTemplate§c don't exists§7!");
        }
    }

}
