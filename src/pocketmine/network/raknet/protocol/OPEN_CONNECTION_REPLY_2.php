<?php

namespace pocketmine\network\raknet\protocol;

use pocketmine\network\raknet\RakNet;

class OPEN_CONNECTION_REPLY_2 extends Packet{
	
    public static $ID = 0x08;

    public $serverID;
    public $clientAddress;
    public $clientPort;
    public $mtuSize;

    public function encode(){
        parent::encode();
        $this->put(RakNet::MAGIC);
        $this->putLong($this->serverID);
        $this->putAddress($this->clientAddress, $this->clientPort, 4);
        $this->putShort($this->mtuSize);
        $this->putByte(0);
    }

    public function decode(){
        parent::decode();
        $this->offset += 16;
        $this->serverID = $this->getLong();
		$this->getAddress($this->clientAddress, $this->clientPort);
        $this->mtuSize = $this->getShort();
    }
}
