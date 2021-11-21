<?php
/* Copyright (c) 2021 Florian H. All rights reserved. */
namespace Bridge\cloudbridge\packets;

class StartServerPacket extends RequestPacket{
	public const NETWORK_ID = self::PACKET_START_SERVER;

	/** @var string */
	public $template = "";
	/** @var int */
	public $count    = 1;

	/**
	 * Function decodePayload
	 * @return void
	 */
	protected function decodePayload(): void{
		$this->type = $this->getInt();
		$this->template = $this->getString();
		$this->count = $this->getInt();
	}

	/**
	 * Function encodePayload
	 * @return void
	 */
	protected function encodePayload(): void{
		$this->putInt($this->type);
		$this->putString($this->template);
		$this->putInt($this->count);
	}
}