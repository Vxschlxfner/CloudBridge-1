<?php
/* Copyright (c) 2021 Florian H. All rights reserved. */
namespace Bridge\cloudbridge;

use Bridge\cloudbridge\commands\ShutdownCommand;
use cloudbridge\utils\InternetAddress;
use FormAPI\FormAPI;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\MainLogger;
use Bridge\cloudbridge\commands\CloudInfoCommand;
use Bridge\cloudbridge\commands\RunCommand;
use Bridge\cloudbridge\commands\StartServerCommand;
use Bridge\cloudbridge\commands\StopServerGroupCommand;
use Bridge\cloudbridge\event\cloud\CloudPacketReceive;
use Bridge\cloudbridge\event\server\ServerLoginEvent;
use Bridge\cloudbridge\packets\AcceptConnectionPacket;
use Bridge\cloudbridge\packets\ConsoleTextPacket;
use Bridge\cloudbridge\packets\DisconnectPacket;
use Bridge\cloudbridge\packets\LoginPacket;
use Bridge\cloudbridge\tasks\ServerStateTask;

class Main extends PluginBase implements Listener{
	/** @var Main */
	private static $instance = null;
	/** @var bool */
	private static $connected = false;
	/** @var SocketShit */
	private static $socketShit = null;

	/** @var boolean */
	public static $inGame = false;

	public $name;
	public $max;
	public $ing;
	public $file = "/home/mcpe/CloudDatabase/temp/";

	const PREFIX = "§bCloud §8» §r";
	const SERVER_PREFIX = "§eCloud §8» §7";
	private $prefix = self::PREFIX;

	public function onLoad(): void{
		self::$instance = $this;
	}

	/**
	 * Function onEnable
	 * @return void
	 * @throws \Exception
	 */
	public function onEnable(): void{
		self::$inGame = false;
        @mkdir("/home/mcpe/CloudDatabase/");

		FormAPI::enable($this);

		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		$cloud = new InternetAddress("127.0.0.1", 13337, 4);
		$server = new InternetAddress("127.0.0.1", Server::getInstance()->getPort()+1, 4);
		self::$socketShit = new SocketShit($cloud, $server);

		if (!SocketShit::connectToCloud()) {
			throw new \Exception("Cannot connect to Cloud.");
		} else {
			self::$connected = true;
			$this->login();
			$this->text();
			$this->getLogger()->info("Connected to Cloud successful.");
			$this->getServer()->getCommandMap()->registerAll(strtoupper($this->getName()), [
				new StartServerCommand($this),
				new StopServerGroupCommand($this),
				new RunCommand($this),
                new CloudInfoCommand($this),
                new ShutdownCommand("shutdown"),
			]);
		}

		$this->intiConfig();
		$f = new Config($this->getDataFolder() . 'config.yml', Config::YAML);
		$this->name = Server::getInstance()->getMotd();
		$this->max = Server::getInstance()->getMaxPlayers();
		$this->getScheduler()->scheduleRepeatingTask(new ServerStateTask(), 1 * 5);
		$this->getLogger()->info($this->prefix . "§aRegistered as " . $this->name);

	}

	/**
	 * Function onJoin
	 * @param PlayerJoinEvent $event
	 * @return void
	 */
	public function onJoin(PlayerJoinEvent $event): void{
		if (($player = $event->getPlayer())->hasPermission("cloudbridge.info")) {
			if (self::$connected) {
				$player->sendMessage(Main::PREFIX . "§aThis server is connected to Cloud.");
			} else {
				$player->sendMessage(Main::PREFIX . "§cThis server isn't connected to Cloud.");
			}
		}
	}

	private function text(): void{
		$packet = new ConsoleTextPacket();
		$packet->sender = Server::getInstance()->getMotd();
		$packet->message = "§e{$packet->sender} §ahas connected to the §eCloud§7!";
		SocketShit::sendPacket($packet);
	}

	/**
	 * Function login
	 * @return void
	 */
	private function login(): void{//INFO: template for other packets.
		$packet = new LoginPacket();
		$packet->uuid = ServerManager::getServerUuid();
		$packet->password = ServerManager::getCloudPassword();
		SocketShit::sendPacket($packet);
	}

	/**
	 * Function disconnect
	 * @return void
	 * @throws \Exception
	 */
	private function disconnect(): void{
		$packet = new DisconnectPacket();
		$packet->requestId = Server::getInstance()->getMotd();
		$packet->reason = 1;
		SocketShit::sendPacket($packet);
	}

	/**
	 * Function onDisable
	 * @return void
	 * @throws \Exception
	 */
	public function onDisable(): void{
		try {
			$this->disconnect();
            SocketShit::$cloudSocket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            $socket = socket_connect(SocketShit::$cloudSocket, SocketShit::$cloudAddress->getIp(), SocketShit::$cloudAddress->getPort());
            if ($socket) {
                SocketShit::$serverSocket->close();
            }
		} catch (\ErrorException $exception) {
			$this->getLogger()->logException($exception);
		}

		try {
			$file = new Config($this->file.$this->name.".json", Config::JSON);
			$file->setAll([
				"offline" => true,
				"ingame" => false
			]);
			$file->save();
		}catch (\Exception $exception){
			Server::getInstance()->getLogger()->info($this->prefix . "crashed. Regenerate!");
		}

		Server::getInstance()->forceShutdown();

	}

	public function intiConfig(){
		$file = new Config($this->getDataFolder() . 'config.yml', Config::YAML);
		$file->set("name", Server::getInstance()->getMotd());
		$file->set("maxplayers", Server::getInstance()->getMaxPlayers());
		$file->set("offline", false);
		$file->save();
	}

	/**
	 * Function getPrefix
	 * @return string
	 */
	public function getPrefix(): string{
		return $this->prefix;
	}

	/**
	 * Function getInstance
	 * @return static
	 */
	public static function getInstance(): self{
		return self::$instance;
	}

	/**
	 * Function getSocketShit
	 * @return SocketShit
	 */
	public static function getSocketShit(): SocketShit{
		return self::$socketShit;
	}

	/**
	 * Function getConnected
	 * @return bool
	 */
	public static function isConnected(): bool{
		return self::$connected;
	}

	/**
	 * Function onReceiveCloudPacket
	 * @param CloudPacketReceive $event
	 * @return void
	 */
	public function onReceiveCloudPacket(CloudPacketReceive $event): void{
		$packet = $event->getPacket();

		if ($packet instanceof AcceptConnectionPacket) {
			try {
				$ev = new ServerLoginEvent();
				$ev->call();
			} catch (\ErrorException $exception) {
				$this->getLogger()->logException($exception);
			}
		}
	}

    public static function getLobbys(): ?array{
        $cfg = new Config("/home/mcpe/CloudDatabase/server_groups.json", Config::JSON);
        if ($cfg->getAll()["Lobby"] !== []) {
            return $cfg->getAll()["Lobby"];
        } else {
            return null;
        }
    }

    public static function getRandomLobby(): ?string
    {

        if (self::getLobbys() === null) {
            return "null";
        }

        $cfg = new Config("/home/mcpe/CloudDatabase/server_groups.json", Config::JSON);
        $a = self::getLobbys();
        $lb = ($a != null) ? $a[array_rand($a)] : null;
        return $lb;
    }

	public function isTemplate(string $template){
	    if (is_dir("/home/mcpe/CloudServer/templates/{$template}")){
	        return true;
        } else {
	        return false;
        }
    }

    public static function countTemplates(): int{
        $cfg = new Config("/home/mcpe/CloudDatabase/server_groups.json", Config::JSON);
        return count($cfg->getAll());
    }
}
