<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class ClientSettingsPacket extends InboundPacket{

	/** @var string */
	public $lang;
	/** @var int */
	public $view;
	/** @var int */
	public $chatMode;
	/** @var int */
	public $chatColor;
	/** @var string */
	public $skinSetting;
	/** @var int */
	public $mainHand;

	public function pid(){
		return self::CLIENT_SETTINGS_PACKET;
	}

	protected function decode(){
		$this->lang = $this->getString();
		$this->view = $this->getSignedByte();
		$this->chatMode = $this->getVarInt();
		$this->chatColor = $this->getBool();
		$this->skinSetting = $this->getByte();
		$this->mainHand = $this->getVarInt();
	}
}
