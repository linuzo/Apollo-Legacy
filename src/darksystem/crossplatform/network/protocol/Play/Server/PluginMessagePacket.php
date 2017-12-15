<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class PluginMessagePacket extends OutboundPacket{

	/** @var string */
	public $channel;
	/** @var array */
	public $data = [];

	public function pid(){
		return self::PLUGIN_MESSAGE_PACKET;
	}

	protected function encode(){
		$this->putString($this->channel);
		switch($this->channel){
			case "MC|BOpen":
				$this->putVarInt($this->data[0]);
			break;
		}
	}
}
