<?php
/* Copyright (c) 2021 Florian H. All rights reserved. */
namespace Bridge\cloudbridge\event\cloud;

use pocketmine\event\Event;
use Bridge\cloudbridge\packets\DataPacket;

class CloudPacketReceive extends Event{
	/** @var DataPacket */
	protected $packet;


	/**
	 * CloudPacketReceive constructor.
	 * @param DataPacket $packet
	 */
	public function __construct(DataPacket $packet){
		$this->packet = $packet;
	}

	/**
	 * Function getPacket
	 * @return DataPacket
	 */
	public function getPacket(): DataPacket{
		return $this->packet;
	}
}
