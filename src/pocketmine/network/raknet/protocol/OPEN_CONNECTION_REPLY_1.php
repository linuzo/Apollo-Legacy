<?php

namespace pocketmine\network\raknet\protocol;

use pocketmine\network\raknet\RakNet;

class OPEN_CONNECTION_REPLY_1 extends Packet{
	
    public static $ID = 0x06;

    public $serverID;
    public $mtuSize;

    public function encode(){
        parent::encode();
        $this->put(RakNet::MAGIC);
        $this->putLong($this->serverID);
        $this->putByte(0);
        $this->putShort($this->mtuSize);
    }

    public function decode(){
        parent::decode();
        $this->offset += 16;
        $this->serverID = $this->getLong();
        $this->getByte();
        $this->mtuSize = $this->getShort();
    }
}