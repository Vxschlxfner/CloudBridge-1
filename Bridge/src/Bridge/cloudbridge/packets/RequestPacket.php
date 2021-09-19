<?php
/* Copyright (c) 2020 Florian H. All rights reserved. */
namespace Bridge\cloudbridge\packets;

class RequestPacket extends Packet{
	/** @var int */
	public $type;
	/** @var string */
	public $requestid;
}
