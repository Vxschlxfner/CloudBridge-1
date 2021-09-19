<?php
/* Copyright (c) 2021 Florian H. All rights reserved. */
namespace Bridge\cloudbridge\packets;

class ConsoleTextPacket extends Packet{
    public const NETWORK_ID = Packet::PACKET_LOG;
	/** @var string */
    public $sender = "";
	/** @var string */
    public $message = "";

	/**
	 * Function decodePayload
	 * @return void
	 */
    protected function decodePayload() {
        $this->sender = $this->getString();
        $this->message = $this->getString();
    }

	/**
	 * Function encodePayload
	 * @return void
	 */
    protected function encodePayload() {
		$this->putString($this->sender);
		$this->putString($this->message);
    }
}
