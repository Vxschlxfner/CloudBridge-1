<?php
/* Copyright (c) 2021 Florian H. All rights reserved. */

namespace Bridge\cloudbridge\packets;

class StopServerGroupPacket extends RequestPacket{
	public const NETWORK_ID = self::PACKET_STOP_GROUP;

	/** @var string */
	public $template = "";



	/**
	 * Function decodePayload
	 * @return void
	 */
	protected function decodePayload(): void{
		$this->type = $this->getInt();
		$this->template = $this->getString();
	}

	/**
	 * Function encodePayload
	 * @return void
	 */
	protected function encodePayload(): void{
		$this->putInt($this->type);
		$this->putString($this->template);
	}
}
