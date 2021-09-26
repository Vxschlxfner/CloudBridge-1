<?php

namespace cloudbridge\utils;

use Bridge\cloudbridge\packets\ConsoleTextPacket;
use Bridge\cloudbridge\packets\DisconnectPacket;
use Bridge\cloudbridge\packets\LoginPacket;
use Bridge\cloudbridge\packets\MessagePacket;
use Bridge\cloudbridge\ServerManager;
use Bridge\cloudbridge\SocketShit;
use pocketmine\Server;

class Packets {

    /**
     * Function login
     * @return void
     */
    //NOTE: template for other packets
    public static function login(): void{
        $packet = new LoginPacket();
        $packet->uuid = ServerManager::getServerUuid();
        $packet->password = ServerManager::getCloudPassword();
        SocketShit::sendPacket($packet);
    }

    /**
     * Function broadcastMessage
     * @return void
     */
    public static function broadcastMessage(string $message): void{
        $packet = new MessagePacket();
        $packet->sender = ServerManager::getServerId();
        $packet->message = $message;
        SocketShit::sendPacket($packet);
    }

    public static function text(): void{
        $packet = new ConsoleTextPacket();
        $packet->sender = Server::getInstance()->getMotd();
        $packet->message = "§e{$packet->sender} §ahas connected to the §eCloud§7!";
        SocketShit::sendPacket($packet);
    }

    /**
     * Function disconnect
     * @return void
     * @throws \Exception
     */
    public static function disconnect(): void{
        $packet = new DisconnectPacket();
        $packet->requestId = Server::getInstance()->getMotd();
        $packet->reason = 1;
        SocketShit::sendPacket($packet);
    }

}