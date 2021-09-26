<?php
namespace Bridge\cloudbridge\packets;

class RequestPacket extends Packet{
	/** @var int */
	public $type;
	/** @var string */
	public $requestid;
}