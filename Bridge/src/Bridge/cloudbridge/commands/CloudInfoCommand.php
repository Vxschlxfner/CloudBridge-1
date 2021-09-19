<?php
/* Copyright (c) 2021 Florian H. All rights reserved. */
namespace Bridge\cloudbridge\commands;

use FormAPI\FormAPI;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Bridge\cloudbridge\Main;
use pocketmine\world\World;

class CloudInfoCommand extends Command
{
    private $main;

    public function __construct(Main $main)
    {
        parent::__construct("cloudinfo", "", "", []);
        $this->setDescription("CloudInfo Command");
        $this->main = $main;
    }

    public static function onlineServerCountForm(Player $player)
    {
        $api = FormAPI::getInstance();
        $form = $api->createSimpleForm(function (Player $player, int $data = null) {
            $result = $data;
            if ($result === null) {
                return;
            }
        });
        $form->setTitle(Main::PREFIX . "§eInfo");
        $form->setContent("§aCurrent §cOnline§7-§cTemplates§7:\n§4" . Main::countTemplates());
        $form->addButton("§4Close.");
        $form->sendToPlayer($player);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $this::onlineServerCountForm($sender);
        }
    }
}