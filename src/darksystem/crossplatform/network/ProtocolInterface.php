<?php

namespace darksystem\crossplatform\network;

use pocketmine\network\protocol\DataPacket;
use pocketmine\network\SourceInterface;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\utils\MainLogger;
use darksystem\crossplatform\CrossPlatform;
use darksystem\crossplatform\DesktopPlayer;
use darksystem\crossplatform\network\protocol\Login\EncryptionResponsePacket;
use darksystem\crossplatform\network\protocol\Login\LoginStartPacket;
use darksystem\crossplatform\network\protocol\Play\Client\AdvancementTabPacket;
use darksystem\crossplatform\network\protocol\Play\Client\EnchantItemPacket;
use darksystem\crossplatform\network\protocol\Play\Client\TeleportConfirmPacket;
use darksystem\crossplatform\network\protocol\Play\Client\AnimatePacket;
use darksystem\crossplatform\network\protocol\Play\Client\ConfirmTransactionPacket;
use darksystem\crossplatform\network\protocol\Play\Client\CraftRecipeRequestPacket;
use darksystem\crossplatform\network\protocol\Play\Client\CraftingBookDataPacket;
use darksystem\crossplatform\network\protocol\Play\Client\ClickWindowPacket;
use darksystem\crossplatform\network\protocol\Play\Client\ClientSettingsPacket;
use darksystem\crossplatform\network\protocol\Play\Client\ClientStatusPacket;
use darksystem\crossplatform\network\protocol\Play\Client\CreativeInventoryActionPacket;
use darksystem\crossplatform\network\protocol\Play\Client\EntityActionPacket;
use darksystem\crossplatform\network\protocol\Play\Client\PlayerAbilitiesPacket;
use darksystem\crossplatform\network\protocol\Play\Client\ChatPacket;
use darksystem\crossplatform\network\protocol\Play\Client\CloseWindowPacket;
use darksystem\crossplatform\network\protocol\Play\Client\HeldItemChangePacket;
use darksystem\crossplatform\network\protocol\Play\Client\KeepAlivePacket;
use darksystem\crossplatform\network\protocol\Play\Client\PlayerBlockPlacementPacket;
use darksystem\crossplatform\network\protocol\Play\Client\PlayerDiggingPacket;
use darksystem\crossplatform\network\protocol\Play\Client\PlayerLookPacket;
use darksystem\crossplatform\network\protocol\Play\Client\PlayerPacket;
use darksystem\crossplatform\network\protocol\Play\Client\PlayerPositionAndLookPacket;
use darksystem\crossplatform\network\protocol\Play\Client\PlayerPositionPacket;
use darksystem\crossplatform\network\protocol\Play\Client\PluginMessagePacket;
use darksystem\crossplatform\network\protocol\Play\Client\TabCompletePacket;
use darksystem\crossplatform\network\protocol\Play\Client\UpdateSignPacket;
use darksystem\crossplatform\network\protocol\Play\Client\UseEntityPacket;
use darksystem\crossplatform\network\protocol\Play\Client\UseItemPacket;
use darksystem\crossplatform\utils\Binary;

class ProtocolInterface implements SourceInterface{

	/** @var BigBrother */
	protected $plugin;
	/** @var Server */
	protected $server;
	/** @var Translator */
	protected $translator;
	/** @var ServerThread */
	protected $thread;

	/** @var \SplObjectStorage<DesktopPlayer> */
	protected $sessions;

	/** @var DesktopPlayer[] */
	protected $sessionsPlayers = [];

	/** @var DesktopPlayer[] */
	protected $identifiers = [];

	/** @var int */
	protected $identifier = 0;

	/** @var int */
	private $threshold;
	/** @var CrossPlatform  */
    protected $handler;

    /**
     * @param CrossPlatform $handler
     * @param Server $server
     * @param Translator $translator
     * @param int $threshold
     */
	public function __construct(CrossPlatform $handler, Server $server, Translator $translator, $threshold){
		$this->handler = $handler;
		$this->server = $server;
		$this->translator = $translator;
		$this->threshold = $threshold;
		$this->thread = new ServerThread($server->getLogger(), $server->getLoader(), $handler->getPort(), $handler->getIp(), $handler->getMotd(), "src\darksystem\crossplatform\server-icon.png", false);
		$this->sessions = new \SplObjectStorage();
	}

	/**
	 * @override
	 */
	public function start(){
		$this->thread->start();
	}

	/**
	 * @override
	 */
	public function emergencyShutdown(){
		$this->thread->pushMainToThreadPacket(chr(ServerManager::PACKET_EMERGENCY_SHUTDOWN));
	}

	/**
	 * @override
	 */
	public function shutdown(){
		$this->thread->pushMainToThreadPacket(chr(ServerManager::PACKET_SHUTDOWN));
	}

	/**
	 * @param string $name
	 * @override
	 */
	public function setName($name){
		$info = $this->handler->server->getQueryInformation();
		$value = [
			"MaxPlayers" => $this->handler->server->getMaxPlayers(),
			"OnlinePlayers" => $this->handler->server->getQueryInformation()->getPlayerCount()
		];
		$buffer = chr(ServerManager::PACKET_SET_OPTION).chr(strlen("name"))."name".json_encode($value);
		$this->thread->pushMainToThreadPacket($buffer);
	}

	/**
	 * @param int $identifier
	 */
	public function closeSession($identifier){
		if(isset($this->sessionsPlayers[$identifier])){
			$player = $this->sessionsPlayers[$identifier];
			unset($this->sessionsPlayers[$identifier]);
			$player->close($player->getLeaveMessage(), "Connection closed");
		}
	}

	/**
	 * @param Player $player
	 * @param string $reason
	 * @override
	 */
	public function close(Player $player, $reason = "unknown reason"){
		if(isset($this->sessions[$player])){
			$identifier = $this->sessions[$player];
			$this->sessions->detach($player);
			unset($this->identifiers[$identifier]);
			$this->thread->pushMainToThreadPacket(chr(ServerManager::PACKET_CLOSE_SESSION) . Binary::writeInt($identifier));
		}else{
			return;
		}
	}

	/**
	 * @param int    $target
	 * @param Packet $packet
	 */
	protected function sendPacket($target, Packet $packet){
		if(\pocketmine\DEBUG > 3){
			$id = bin2hex(chr($packet->pid()));
			if($id !== "1f"){
				echo "[Send][Interface] 0x".bin2hex(chr($packet->pid()))."\n";
			}
		}

		$data = chr(ServerManager::PACKET_SEND_PACKET) . Binary::writeInt($target) . $packet->write();
		$this->thread->pushMainToThreadPacket($data);
	}

	/**
	 * @param DesktopPlayer $player
	 */
	public function setCompression(DesktopPlayer $player){
		if(isset($this->sessions[$player])){
			$target = $this->sessions[$player];
			$data = chr(ServerManager::PACKET_SET_COMPRESSION) . Binary::writeInt($target) . Binary::writeInt($this->threshold);
			$this->thread->pushMainToThreadPacket($data);
		}
	}

	/**
	 * @param DesktopPlayer $player
	 * @param string        $secret
	 */
	public function enableEncryption(DesktopPlayer $player, $secret){
		if(isset($this->sessions[$player])){
			$target = $this->sessions[$player];
			$data = chr(ServerManager::PACKET_ENABLE_ENCRYPTION) . Binary::writeInt($target) . $secret;
			$this->thread->pushMainToThreadPacket($data);
		}
	}

	/**
	 * @param DesktopPlayer $player
	 * @param Packet        $packet
	 */
	public function putRawPacket(DesktopPlayer $player, Packet $packet){
		if(isset($this->sessions[$player])){
			$target = $this->sessions[$player];
			$this->sendPacket($target, $packet);
		}
	}

	/**
	 * @param Player     $player
	 * @param DataPacket $packet
	 * @param bool       $needACK
	 * @param bool       $immediate
	 *
	 * @return int|null identifier if $needAck === false else null
	 * @override
	 */
	public function putPacket(Player $player, DataPacket $packet, $needACK = false, $immediate = true){
		$id = 0;
		if($needACK){
			$id = $this->identifier++;
			$this->identifiers[$id] = $player;
		}
		assert($player instanceof DesktopPlayer);
		$packets = $this->translator->serverToInterface($player, $packet);
		if($packets !== null and $this->sessions->contains($player)){
			$target = $this->sessions[$player];
			if(is_array($packets)){
				foreach($packets as $packet){
					$this->sendPacket($target, $packet);
				}
			}else{
				$this->sendPacket($target, $packets);
			}
		}

		return $id;
	}

	/**
	 * @param DesktopPlayer $player
	 * @param Packet        $packet
	 */
	protected function receivePacket(DesktopPlayer $player, Packet $packet){
		$packets = $this->translator->interfaceToServer($player, $packet);
		if($packets !== null){
			if(is_array($packets)){
				foreach($packets as $packet){
					$player->handleDataPacket($packet);
				}
			}else{
				$player->handleDataPacket($packets);
			}
		}
	}

	/**
	 * @param DesktopPlayer $player
	 * @param string        $payload
	 */
	protected function handlePacket(DesktopPlayer $player, $payload){
		if(\pocketmine\DEBUG > 3){
			$id = bin2hex(chr(ord($payload{0})));
			if($id !== "0b"){
				echo "[Receive][Interface] 0x".bin2hex(chr(ord($payload{0})))."\n";
			}
		}

		$pid = ord($payload{0});
		$offset = 1;

		$status = $player->bigBrother_getStatus();

		if($status === 1){
			switch($pid){
				case InboundPacket::TELEPORT_CONFIRM_PACKET:
					$pk = new TeleportConfirmPacket();
					break;
				case InboundPacket::TAB_COMPLETE_PACKET:
					$pk = new TabCompletePacket();
					break;
				case InboundPacket::CHAT_PACKET:
					$pk = new ChatPacket();
					break;
				case InboundPacket::CLIENT_STATUS_PACKET:
					$pk = new ClientStatusPacket();
					break;
				case InboundPacket::CLIENT_SETTINGS_PACKET:
					$pk = new ClientSettingsPacket();
					break;
				case InboundPacket::CONFIRM_TRANSACTION_PACKET:
					$pk = new ConfirmTransactionPacket();
					break;
				case InboundPacket::ENCHANT_ITEM_PACKET:
					$pk = new EnchantItemPacket();
					break;
				case InboundPacket::CLICK_WINDOW_PACKET:
					$pk = new ClickWindowPacket();
					break;
				case InboundPacket::CLOSE_WINDOW_PACKET:
					$pk = new CloseWindowPacket();
					break;
				case InboundPacket::PLUGIN_MESSAGE_PACKET:
					$pk = new PluginMessagePacket();
					break;
				case InboundPacket::USE_ENTITY_PACKET:
					$pk = new UseEntityPacket();
					break;
				case InboundPacket::KEEP_ALIVE_PACKET:
					$pk = new KeepAlivePacket();
					break;
				case InboundPacket::PLAYER_PACKET:
					$pk = new PlayerPacket();
					break;
				case InboundPacket::PLAYER_POSITION_PACKET:
					$pk = new PlayerPositionPacket();
					break;
				case InboundPacket::PLAYER_POSITION_AND_LOOK_PACKET:
					$pk = new PlayerPositionAndLookPacket();
					break;
				case InboundPacket::PLAYER_LOOK_PACKET:
					$pk = new PlayerLookPacket();
					break;
				case InboundPacket::CRAFT_RECIPE_REQUEST_PACKET:
					$pk = new CraftRecipeRequestPacket();
				break;
				case InboundPacket::PLAYER_ABILITIES_PACKET:
					$pk = new PlayerAbilitiesPacket();
					break;
				case InboundPacket::PLAYER_DIGGING_PACKET:
					$pk = new PlayerDiggingPacket();
					break;
				case InboundPacket::ENTITY_ACTION_PACKET:
					$pk = new EntityActionPacket();
					break;
				case InboundPacket::CRAFTING_BOOK_DATA_PACKET:
					$pk = new CraftingBookDataPacket();
					break;
				case InboundPacket::ADVANCEMENT_TAB_PACKET:
					$pk = new AdvancementTabPacket();
					break;
				case InboundPacket::HELD_ITEM_CHANGE_PACKET:
					$pk = new HeldItemChangePacket();
					break;
				case InboundPacket::CREATIVE_INVENTORY_ACTION_PACKET:
					$pk = new CreativeInventoryActionPacket();
					break;
				case InboundPacket::UPDATE_SIGN_PACKET:
					$pk = new UpdateSignPacket();
					break;
				case InboundPacket::ANIMATE_PACKET:
					$pk = new AnimatePacket();
					break;
				case InboundPacket::PLAYER_BLOCK_PLACEMENT_PACKET:
					$pk = new PlayerBlockPlacementPacket();
					break;
				case InboundPacket::USE_ITEM_PACKET:
					$pk = new UseItemPacket();
					break;
				default:
					if(\pocketmine\DEBUG > 3){
						echo "[Receive][Interface] 0x".bin2hex(chr($pid))." Not implemented\n"; //Debug
					}
					return;
			}

			$pk->read($payload, $offset);
			$this->receivePacket($player, $pk);
		}elseif($status === 0){
			if($pid === InboundPacket::LOGIN_START_PACKET){
				$pk = new LoginStartPacket();
				$pk->read($payload, $offset);
				$player->bigBrother_handleAuthentication($this->handler, $pk->name, $this->handler->isOnlineMode());
			}elseif($pid === InboundPacket::ENCRYPTION_RESPONSE_PACKET and $this->handler->isOnlineMode()){
				$pk = new EncryptionResponsePacket();
				$pk->read($payload, $offset);
				$player->bigBrother_processAuthentication($this->handler, $pk);
			}else{
				$player->close($player->getLeaveMessage(), "Unexpected packet $pid");
			}
		}
	}

	/**
	 * @return bool
	 */
	public function process(){
		if(count($this->identifiers) > 0){
			foreach($this->identifiers as $id => $player){
				$player->handleACK($id);
			}
		}

		while(is_string($buffer = $this->thread->readThreadToMainPacket())){
			$offset = 1;
			$pid = ord($buffer{0});

			if($pid === ServerManager::PACKET_SEND_PACKET){
				$id = Binary::readInt(substr($buffer, $offset, 4));
				$offset += 4;
				if(isset($this->sessionsPlayers[$id])){
					$payload = substr($buffer, $offset);
					try{
						$this->handlePacket($this->sessionsPlayers[$id], $payload);
					}catch(\Exception $e){
						if(\pocketmine\DEBUG > 1){
							$logger = $this->server->getLogger();
							if($logger instanceof MainLogger){
								$logger->debug("DesktopPacket 0x" . bin2hex($payload));
								$logger->logException($e);
							}
						}
					}
				}
			}elseif($pid === ServerManager::PACKET_OPEN_SESSION){
				$id = Binary::readInt(substr($buffer, $offset, 4));
				$offset += 4;
				if(isset($this->sessionsPlayers[$id])){
					continue;
				}
				$len = ord($buffer{$offset++});
				$address = substr($buffer, $offset, $len);
				$offset += $len;
				$port = Binary::readShort(substr($buffer, $offset, 2));

				$identifier = "$id:$address:$port";

				$player = new DesktopPlayer($this, $identifier, $address, $port, $this->handler);
				$this->sessions->attach($player, $id);
				$this->sessionsPlayers[$id] = $player;
				$this->handler->getServer()->addPlayer($identifier, $player);
			}elseif($pid === ServerManager::PACKET_CLOSE_SESSION){
				$id = Binary::readInt(substr($buffer, $offset, 4));
				$offset += 4;
				$flag = Binary::readInt(substr($buffer, $offset, 4));

				if(isset($this->sessionsPlayers[$id])){
					if($flag === 0){
						$this->close($this->sessionsPlayers[$id]);
					}else{
						$this->closeSession($id);
					}
				}
			}
		}

		return true;
	}
}
