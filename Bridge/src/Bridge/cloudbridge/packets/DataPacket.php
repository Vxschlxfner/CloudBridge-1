<?php
declare(strict_types=1);
namespace Bridge\cloudbridge\packets;
use pocketmine\utils\BinaryStream;
use pocketmine\utils\Utils;


/**
 * Class DataPacket
 * @package cloudbridge\cloudbridge\network
 * @author Florian H.
 * @date 18.06.2020 - 21:14
 * @project CloudBridge
 */
abstract class DataPacket extends BinaryStream{

    public const NETWORK_ID = 0;
    public const PID_MASK   = 0x3ff; //10 bits
    /** @var bool */
    public $isEncoded = false;
    /** @var int */
    public $senderSubId = 0;
    /** @var int */
    public $recipientSubId = 0;

    public function pid(){
        return static::NETWORK_ID;
    }

    public function getName(): string{
        return (new \ReflectionClass($this))->getShortName();
    }

    public function canBeBatched(): bool{
        return true;
    }

    public function canBeSentBeforeLogin(): bool{
        return false;
    }

    /**
     * Returns whether the packet may legally have unread bytes left in the buffer.
     * @return bool
     */
    public function mayHaveUnreadBytes(): bool{
        return false;
    }

    /**
     * @throws \OutOfBoundsException
     * @throws \UnexpectedValueException
     */
    public function decode(){
        $this->offset = 0;
        $this->decodeHeader();
        $this->decodePayload();
    }

    /**
     * @throws \OutOfBoundsException
     * @throws \UnexpectedValueException
     */
    protected function decodeHeader(){
        $pid = $this->getUnsignedVarInt();
        if ($pid !== static::NETWORK_ID) {
            throw new \UnexpectedValueException("Expected " . static::NETWORK_ID . " for packet ID, got $pid");
        }
    }

    /**
     * Note for plugin developers: If you're adding your own packets, you should perform decoding in here.
     *
     * @throws \OutOfBoundsException
     * @throws \UnexpectedValueException
     */
    protected function decodePayload(){
    }

    public function encode(){
        $this->encodeHeader();
        $this->encodePayload();
        $this->isEncoded = true;
    }

    protected function encodeHeader(){
        $this->putUnsignedVarInt(static::NETWORK_ID);
    }

    /**
     * Note for plugin developers: If you're adding your own packets, you should perform encoding in here.
     */
    protected function encodePayload(){
    }

    public function clean(){
        $this->buffer = null;
        $this->isEncoded = false;
        $this->offset = 0;
        return $this;
    }

    public function __debugInfo(){
        $data = [];
        foreach ($this as $k => $v) {
            if ($k === "buffer" and is_string($v)) {
                $data[$k] = bin2hex($v);
            } else if (is_string($v) or (is_object($v) and method_exists($v, "__toString"))) {
                $data[$k] = Utils::printable((string)$v);
            } else {
                $data[$k] = $v;
            }
        }
        return $data;
    }

    public function __get($name){
        throw new \Error("Undefined property: " . get_class($this) . "::\$" . $name);
    }

    public function __set($name, $value){
        throw new \Error("Undefined property: " . get_class($this) . "::\$" . $name);
    }

    public function getString(): string{
        return $this->get($this->getUnsignedVarInt());
    }

    public function putString(string $v): void{
        $this->putUnsignedVarInt(strlen($v));
        $this->put($v);
    }
}