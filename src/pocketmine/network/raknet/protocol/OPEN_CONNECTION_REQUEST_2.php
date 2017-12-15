<?php

namespace pocketmine\network\raknet\protocol;

use pocketmine\network\raknet\RakNet;

class OPEN_CONNECTION_REQUEST_2 extends Packet{
	
    public static $ID = 0x07;

    public $clientID;
	public $serverAddress;
    public $serverPort;
    public $mtuSize;

    public function encode(){
        parent::encode();
        $this->put(RakNet::MAGIC);
		$this->putAddress($this->serverAddress, $this->serverPort, 4);
        $this->putShort($this->mtuSize);
        $this->putLong($this->clientID);
    }

    public function decode(){
        parent::decode();
        $this->offset += 16;
		$this->getAddress($this->serverAddress, $this->serverPort);
        $this->mtuSize = $this->getShort();
        $this->clientID = $this->getLong();
    }
}
