<?php
/* Copyright (c) 2021 Florian H. All rights reserved. */
namespace Bridge\cloudbridge\packets;

class LoginPacket extends RequestPacket{
    public const NETWORK_ID = self::PACKET_LOGIN;

    /** @var string */
    public $uuid = "";
    /** @var string */
    public $password = "";


	/**
	 * Function decodePayload
	 * @return void
	 */
    protected function decodePayload() {
    	$this->type = $this->getInt();
        $this->uuid = $this->getString();
        $this->password = $this->getString();
        $this->requestid = $this->getString();
    }

	/**
	 * Function encodePayload
	 * @return void
	 */
    protected function encodePayload() {
    	$this->putInt($this->type);
        $this->putString($this->uuid);
        $this->putString($this->password);
        $this->putString($this->requestid);
    }
}