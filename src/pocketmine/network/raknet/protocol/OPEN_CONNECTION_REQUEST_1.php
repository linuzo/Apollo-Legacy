<?php

namespace pocketmine\network\raknet\protocol;

use pocketmine\network\raknet\RakNet;

class OPEN_CONNECTION_REQUEST_1 extends Packet{
	
    public static $ID = 0x05;

    public $protocol = RakNet::PROTOCOL;
    public $mtuSize;

    public function encode(){
        parent::encode();
        $this->put(RakNet::MAGIC);
        $this->putByte($this->protocol);
        $this->put(str_repeat(chr(0x00), $this->mtuSize - 18));
    }

    public function decode(){
        parent::decode();
        $this->offset += 16;
        $this->protocol = $this->getByte();
        $this->mtuSize = strlen($this->get(true)) + 18;
    }
}