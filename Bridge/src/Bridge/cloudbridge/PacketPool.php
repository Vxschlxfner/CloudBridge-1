<?php
/* Copyright (c) 2021 Florian H. All rights reserved. */
namespace Bridge\cloudbridge;

use Bridge\cloudbridge\packets\MessagePacket;
use Bridge\cloudbridge\packets\SendMessagePacket;
use pocketmine\utils\Binary;
use Bridge\cloudbridge\packets\AcceptConnectionPacket;
use Bridge\cloudbridge\packets\ConsoleTextPacket;
use Bridge\cloudbridge\packets\DataPacket;
use Bridge\cloudbridge\packets\DisconnectPacket;
use Bridge\cloudbridge\packets\LoginPacket;
use Bridge\cloudbridge\packets\StartServerPacket;
use Bridge\cloudbridge\packets\StopServerGroupPacket;

class PacketPool{
	/** @var \SplFixedArray<DataPacket> */
	protected static $pool = null;

	public static function init() {
		static::$pool = new \SplFixedArray(256);

		self::registerPacket(new LoginPacket());
		self::registerPacket(new AcceptConnectionPacket());
		self::registerPacket(new ConsoleTextPacket());
		self::registerPacket(new DisconnectPacket());
		self::registerPacket(new StartServerPacket());
		self::registerPacket(new StopServerGroupPacket());
        self::registerPacket(new SendMessagePacket());
        self::registerPacket(new MessagePacket());
	}

    /**
     * Function registerPacket
     * @param DataPacket $packet
     * @return void
     */
    public static function registerPacket(DataPacket $packet): void{
        static::$pool[$packet->pid()] = clone $packet;
    }

    /**
     * Function getPacketById
     * @param int $pid
     * @return null|DataPacket
     */
    public static function getPacketById(int $pid): ?DataPacket{
        return isset(static::$pool[$pid]) ? clone static::$pool[$pid] : null;
    }

    /**
     * Function getPacket
     * @param string $buffer
     * @return null|DataPacket
     */
    public static function getPacket(string $buffer): ?DataPacket{
        $offset = 0;
        $d = Binary::readUnsignedVarInt($buffer, $offset) & DataPacket::PID_MASK;
        $pk = static::getPacketById($d);

        if (!is_null($pk)) {
            $pk->put($buffer);
            $pk->setOffset($offset);
            return $pk;
        }
        return null;
    }
}
