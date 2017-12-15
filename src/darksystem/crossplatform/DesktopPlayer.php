<?php

namespace darksystem\crossplatform;

use pocketmine\Player;
use pocketmine\event\Timings;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\inventory\CraftingGrid;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\types\WindowTypes;
use pocketmine\network\protocol\ContainerOpenPacket;
use pocketmine\network\protocol\Info;
use pocketmine\network\protocol\LoginPacket;
use pocketmine\network\protocol\RequestChunkRadiusPacket;
use pocketmine\network\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\BatchPacket;
use pocketmine\network\SourceInterface;
use pocketmine\level\Level;
use pocketmine\level\format\Chunk;
use pocketmine\tile\ItemFrame;
use pocketmine\utils\Utils;
use pocketmine\utils\TextFormat;
use darksystem\crossplatform\network\Packet;
use darksystem\crossplatform\network\protocol\Login\EncryptionRequestPacket;
use darksystem\crossplatform\network\protocol\Login\EncryptionResponsePacket;
use darksystem\crossplatform\network\protocol\Login\LoginSuccessPacket;
use darksystem\crossplatform\network\protocol\Play\Server\AdvancementsPacket;
use darksystem\crossplatform\network\protocol\Play\Server\KeepAlivePacket;
use darksystem\crossplatform\network\protocol\Play\Server\PlayerPositionAndLookPacket;
use darksystem\crossplatform\network\protocol\Play\Server\TitlePacket;
use darksystem\crossplatform\network\protocol\Play\Server\SelectAdvancementTabPacket;
use darksystem\crossplatform\network\protocol\Play\Server\UnloadChunkPacket;
use darksystem\crossplatform\network\protocol\Play\Server\UnlockRecipesPacket;
use darksystem\crossplatform\network\ProtocolInterface;
use darksystem\crossplatform\entity\ItemFrameBlockEntity;
use darksystem\crossplatform\utils\Binary;
use darksystem\crossplatform\utils\InventoryUtils;
use darksystem\crossplatform\utils\RecipeUtils;

class DesktopPlayer extends Player{

	/** @var int */
	private $crossplatform_status = 0;
	/** @var string */
	protected $crossplatform_uuid;
	/** @var string */
	protected $crossplatform_formatedUUID;
	/** @var array */
	protected $crossplatform_properties = [];
	/** @var string */
	private $crossplatform_checkToken;
	/** @var string */
	private $crossplatform_secret;
	/** @var string */
	private $crossplatform_username;
	/** @var string */
	private $crossplatform_clientId;
	/** @var int */
	private $crossplatform_dimension = 0;
	/** @var string[] */
	private $crossplatform_entitylist = [];
	/** @var InventoryUtils */
	private $inventoryUtils;
	/** @var RecipeUtils */
	private $recipeUtils;
	/** @var array */
	private $crossplatform_clientSetting = [];
	/** @var array */
	private $crossplatform_pluginMessageList = [];
	/** @var array */
	private $crossplatform_breakPosition = [];
	/** @var array */
	private $crossplatform_bossBarData = [
		"entityRuntimeId" => -1,
		"uuid" => "",
		"nameTag" => ""
	];

	/** @var ProtocolInterface */
	protected $interface;
	/** @var CrossPlatform */
	protected $plugin;

	/**
	 * @param SourceInterface $interface
	 * @param string          $clientID
	 * @param string          $address
	 * @param int             $port
	 * @param CrossPlatform      $handler
	 */
	public function __construct(SourceInterface $interface, $clientID, $address, $port, CrossPlatform $handler){
		$this->handler = $handler;
		$this->crossplatform_clientId = $clientID;
		parent::__construct($interface, $clientID, $address, $port);

		$this->crossplatform_breakPosition = [new Vector3(0, 0, 0), 0];
		$this->inventoryUtils = new InventoryUtils($this);
		$this->recipeUtils = new RecipeUtils($this);
	}

	/**
	 * @return InventoryUtils
	 */
	public function getInventoryUtils(){
		return $this->inventoryUtils;
	}

	/**
	 * @return RecipeUtils
	 */
	public function getRecipeUtils(){
		return $this->recipeUtils;
	}

	/**
	 * @return int dimension
	 */
	public function crossplatform_getDimension(){
		return $this->crossplatform_dimension;
	}

	/**
	 * @param int $level_dimension
	 * @return int dimension of pc version converted from $level_dimension
	 */
	public function crossplatform_getDimensionPEToPC($level_dimension){
		switch($level_dimension){
			case 0:
				$dimension = 0;
			break;
			case 1:
				$dimension = -1;
			break;
			case 2:
				$dimension = 1;
			break;
		}
		$this->crossplatform_dimension = $dimension;
		return $dimension;
	}

	/**
	 * @param int    $eid
	 * @param string $entitytype
	 */
	public function crossplatform_addEntityList($eid, $entitytype){
		if(!isset($this->crossplatform_entitylist[$eid])){
			$this->crossplatform_entitylist[$eid] = $entitytype;
		}
	}

	/**
	 * @param int $eid
	 * @return string
	 */
	public function crossplatform_getEntityList($eid){
		if(isset($this->crossplatform_entitylist[$eid])){
			return $this->crossplatform_entitylist[$eid];
		}
		return "generic";
	}

	/**
	 * @param int $eid
	 */
	public function crossplatform_removeEntityList($eid){
		if(isset($this->crossplatform_entitylist[$eid])){
			unset($this->crossplatform_entitylist[$eid]);
		}
	}

	/**
	 * @return array
	 */
	public function crossplatform_getClientSetting(){
		return $this->crossplatform_clientSetting;
	}

	/**
	 * @param array $clientSetting
	 */
	public function crossplatform_setClientSetting($clientSetting = []){
		$this->crossplatform_clientSetting = $clientSetting;
	}

	/**
	 * @return array
	 */
	public function crossplatform_getPluginMessageList(){
		return $this->crossplatform_pluginMessageList;
	}

	/**
	 * @param string $channel
	 * @param array  $data
	 */
	public function crossplatform_setPluginMessageList($channel = "", $data = []){
		$this->crossplatform_pluginMessageList[$channel] = $data;
	}

	/**
	 * @return array
	 */
	public function crossplatform_getBreakPosition(){
		return $this->crossplatform_breakPosition;
	}

	/**
	 * @param array $positionData
	 */
	public function crossplatform_setBreakPosition($positionData = []){
		$this->crossplatform_breakPosition = $positionData;
	}

	/**
	 * @param  string       $bossBardata
	 * @return string|array
	 */
	public function crossplatform_getBossBarData($bossBarData = ""){
		if($bossBarData === ""){
			return $this->crossplatform_bossBarData;
		}
		return $this->crossplatform_bossBarData[$bossBarData];
	}

	/**
	 * @param string $bossBardata
	 */
	public function crossplatform_setBossBarData($bossBarData, $data){
		$this->crossplatform_bossBarData[$bossBarData] = $data;
	}

	/**
	 * @return int status
	 */
	public function crossplatform_getStatus(){
		return $this->crossplatform_status;
	}

	/**
	 * @return array properties
	 */
	public function crossplatform_getProperties(){
		return $this->crossplatform_properties;
	}

	/**
	 * @return string uuid
	 */
	public function crossplatform_getUniqueId(){
		return $this->crossplatform_uuid;
	}

	/**
	 * @return string formatted uuid
	 */
	public function crossplatform_getformatedUUID(){
		return $this->crossplatform_formatedUUID;
	}

	/**
	 * @param bool $first
	 */
	public function sendAdvancements($first = false){
		$pk = new AdvancementsPacket();
		$pk->advancements = [
			[
				"pocketmine:advancements/root",
				[
					false
				],
				[
					true,
					CrossPlatform::toJSON("Welcome to DarkSystem Server!"),
					CrossPlatform::toJSON("Join to DarkSystem Server with Minecraft"),
					Item::get(Item::GRASS),
					0,
					[
						1,
						"minecraft:textures/blocks/stone.png"
					],
					0,
					0
				],
				[
					["hasjoined"],
				],
				[
					[
						"hasjoined"
					]
				]
			]
		];
		$pk->identifiers = [];
		$pk->progress = [
			[
				"pocketmine:advancements/root",
				[
					[
						"hasjoined",
						[
							true,
							time()
						]
					]
				]
			]
		];
		$this->putRawPacket($pk);

		if($first){
			$pk = new SelectAdvancementTabPacket();
			$pk->hasTab = true;
			$pk->tabId = "pocketmine:advancements/root";
			$this->putRawPacket($pk);
		}
	}

	/**
	 * @param CraftingGrid $grid
	 * @override
	 */
	public function setCraftingGrid(CraftingGrid $grid){
		parent::setCraftingGrid($grid);

		if($grid->getDefaultSize() === 9){
			$pk = new ContainerOpenPacket();
			$pk->windowId = 127;
			$pk->type = WindowTypes::WORKBENCH;
			$pk->x = 0;
			$pk->y = 0;
			$pk->z = 0;

			$this->dataPacket($pk);
		}
	}

    /**
     * @param int $chunkX
     * @param int $chunkZ
     * @param BatchPacket $payload
     * @override
     * @return bool|void
     */
	public function sendChunk($chunkX, $chunkZ, $payload){
		parent::sendChunk($chunkX, $chunkZ, $payload);
		foreach($this->usedChunks as $index => $c){
			Level::getXZ($index, $chunkX, $chunkZ);
			/** @var ItemFrame $frame */
            foreach(ItemFrameBlockEntity::getItemFramesInChunk($this->level, $chunkX, $chunkZ) as $frame){
				$frame->spawnTo($this);
			}
		}
	}

	/**
	 * @param int   $chunkX
	 * @param int   $chunkZ
	 * @param Level $level
	 * @override
	 */
	protected function unloadChunk($chunkX, $chunkZ, Level $level = null){
		parent::unloadChunk($chunkX, $chunkZ);

		$pk = new UnloadChunkPacket();
		$pk->chunkX = $chunkX;
		$pk->chunkZ = $chunkZ;
		$this->putRawPacket($pk);

		foreach(ItemFrameBlockEntity::getItemFramesInChunk($level ?? $this->level, $chunkX, $chunkZ) as $frame){
			$frame->despawnFrom($this);
		}
	}

	/**
	 * @param Chunk $chunk
	 * @override
	 */
	public function onChunkUnloaded(Chunk $chunk){
		foreach(ItemFrameBlockEntity::getItemFramesInChunk($this->level, $chunk->getX(), $chunk->getZ()) as $frame){
			$frame->despawnFromAll();
		}
	}

	/**
	 * @override
	 */
	public function onVerifyCompleted(LoginPacket $packet, $isValid, $isAuthenticated){
		parent::onVerifyCompleted($packet, true, true);

		$pk = new ResourcePackClientResponsePacket();
		$pk->status = ResourcePackClientResponsePacket::STATUS_COMPLETED;
		$this->handleDataPacket($pk);

		$pk = new RequestChunkRadiusPacket();
		$pk->radius = 8;
		$this->handleDataPacket($pk);

		$pk = new KeepAlivePacket();
		$pk->id = mt_rand();
		$this->putRawPacket($pk);

		$pk = new TitlePacket();
		$pk->actionID = TitlePacket::TYPE_SET_TITLE;
		$pk->data = TextFormat::toJSON("");
		$this->putRawPacket($pk);

		$pk = new TitlePacket();
		$pk->actionID = TitlePacket::TYPE_SET_SUB_TITLE;
		$pk->data = TextFormat::toJSON(TextFormat::YELLOW . TextFormat::BOLD . "This is a beta version of cross-platform.");
		$this->putRawPacket($pk);

		$this->sendAdvancements(true);
	}

	public function crossplatform_respawn(){
		$pk = new PlayerPositionAndLookPacket();
		$pk->x = $this->getX();
		$pk->y = $this->getY();
		$pk->z = $this->getZ();
		$pk->yaw = 0;
		$pk->pitch = 0;
		$pk->flags = 0;
		$this->putRawPacket($pk);

		foreach($this->usedChunks as $index => $d){//reset chunks
			Level::getXZ($index, $chunkX, $chunkZ);
			$this->unloadChunk($chunkX, $chunkZ);
		}

		$this->usedChunks = [];
	}

	/**
	 * @param string     $uuid
	 * @param array|null $onlineModeData
	 */
	public function crossplatform_authenticate($uuid, $onlineModeData = null){
		if($this->crossplatform_status === 0){
			$this->crossplatform_uuid = $uuid;
			$this->crossplatform_formatedUUID = Binary::UUIDtoString($this->crossplatform_uuid);

			$this->interface->setCompression($this);

			$pk = new LoginSuccessPacket();
			$pk->uuid = $this->crossplatform_formatedUUID;
			$pk->name = $this->crossplatform_username;
			$this->putRawPacket($pk);

			$this->crossplatform_status = 1;

			if($onlineModeData !== null){
				$this->crossplatform_properties = $onlineModeData;
			}

			$skin = "";
			$skindata = null;
			foreach($this->crossplatform_properties as $property){
				if($property["name"] === "textures"){
					$skindata = json_decode(base64_decode($property["value"]), true);
					if(isset($skindata["textures"]["SKIN"]["url"])){
						$skin = $this->getSkinImage($skindata["textures"]["SKIN"]["url"]);
					}
				}
			}

			$pk = new LoginPacket();
			$pk->username = $this->crossplatform_username;
			$pk->protocol = Info::CURRENT_PROTOCOL;
			$pk->clientUUID = $this->crossplatform_formatedUUID;
			$pk->clientId = crc32($this->crossplatform_clientId);
			$pk->xuid = crc32($this->crossplatform_username);
			$pk->serverAddress = "127.0.0.1:25565";
			$pk->locale = "en_US";
			$pk->clientData["SkinGeometryName"] = "";
			$pk->clientData["SkinGeometry"] = "";
			$pk->clientData["CapeData"] = "";
			if($skin === ""){
				if($this->handler->getConfig()->get("skin-slim")){
					$pk->clientData["SkinId"] = "Standard_Custom";
				}else{
					$pk->clientData["SkinId"] = "Standard_CustomSlim";
				}
				$pk->clientData["SkinData"] = base64_encode(file_get_contents($this->handler->getDataFolder().$this->handler->getConfig()->get("skin-yml")));
			}else{
				if($skindata !== null && !isset($skindata["textures"]["SKIN"]["metadata"]["model"])){
					$pk->clientData["SkinId"] = "Standard_Custom";
				}else{
					$pk->clientData["SkinId"] = "Standard_CustomSlim";
				}
				$pk->clientData["SkinData"] = base64_encode($skin);
			}
			$pk->chainData = ["chain" => []];
			$pk->clientDataJwt = "eyJ4NXUiOiJNSFl3RUFZSEtvWkl6ajBDQVFZRks0RUVBQ0lEWWdBRThFTGtpeHlMY3dsWnJ5VVFjdTFUdlBPbUkyQjd2WDgzbmRuV1JVYVhtNzR3RmZhNWZcL2x3UU5UZnJMVkhhMlBtZW5wR0k2SmhJTVVKYVdacmptTWo5ME5vS05GU05CdUtkbThyWWlYc2ZhejNLMzZ4XC8xVTI2SHBHMFp4S1wvVjFWIn0.W10.QUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFB";
			$this->handleDataPacket($pk);
		}
	}

	/**
	 * @param CrossPlatform $handler
	 * @param EncryptionResponsePacket $packet
	 */
	public function crossplatform_processAuthentication(CrossPlatform $handler, EncryptionResponsePacket $packet){
		$this->crossplatform_secret = $handler->decryptBinary($packet->sharedSecret);
		$token = $handler->decryptBinary($packet->verifyToken);
		$this->interface->enableEncryption($this, $this->crossplatform_secret);
		if($token !== $this->crossplatform_checkToken){
			$this->close("Invalid check token");
		}else{
			$this->getAuthenticateOnline($this->crossplatform_username, Binary::sha1("".$this->crossplatform_secret.$handler->getASN1PublicKey()));
		}
	}

    /**
     * @param CrossPlatform $handler
     * @param string $username
     * @param bool $onlineMode
     */
	public function crossplatform_handleAuthentication(CrossPlatform $handler, $username, $onlineMode = false){
		if($this->crossplatform_status === 0){
			$this->crossplatform_username = $username;
			if($onlineMode){
				$pk = new EncryptionRequestPacket();
				$pk->serverID = "";
				$pk->publicKey = $handler->getASN1PublicKey();
				$pk->verifyToken = $this->crossplatform_checkToken = str_repeat("\x00", 4);
				$this->putRawPacket($pk);
			}else{
				$info = $this->getProfile($username);
				if(is_array($info)){
					$this->crossplatform_authenticate($info["id"], $info["properties"]);
				}
			}
		}
	}

	/**
	 * @param string $username
	 * @return array|bool profile data if success else false
	 */
	public function getProfile($username){
		$profile = null;
		$info = null;

		$response = Utils::getURL("https://api.mojang.com/users/profiles/minecraft/".$username);
		if($response !== false){
			$profile = json_decode($response, true);
		}

		if(!is_array($profile)){
			return false;
		}

		$uuid = $profile["id"];
		$response = Utils::getURL("https://sessionserver.mojang.com/session/minecraft/profile/".$uuid, 3);
		if($response !== false){
			$info = json_decode($response, true);
		}

		if($info === null or !isset($info["id"])){
			return false;
		}

		return $info;
	}

	/**
	 * @param string $username
	 * @param string $hash
	 */
	public function getAuthenticateOnline($username, $hash){
		$result = null;

		$response = Utils::getURL("https://sessionserver.mojang.com/session/minecraft/hasJoined?username=".$username."&serverId=".$hash, 5);
		if($response !== false){
			$result = json_decode($response, true);
		}

		if(is_array($result) and isset($result["id"])){
			$this->crossplatform_authenticate($result["id"], $result["properties"]);
		}else{
			$this->close("User not premium");
		}
	}

	/**
	 * @param string $url
	 * @return string skin image
	 */
	public function getSkinImage($url){
		if(extension_loaded("gd")){
			$image = imagecreatefrompng($url);

			if($image !== false){
				$width = imagesx($image);
				$height = imagesy($image);
				$colors = [];
				for($y = 0; $y < $height; $y++){
					$y_array = [];
					for($x = 0; $x < $width; $x++){
						$rgb = imagecolorat($image, $x, $y);
						$r = ($rgb >> 16) & 0xFF;
						$g = ($rgb >> 8) & 0xFF;
						$b = $rgb & 0xFF;
						$alpha = imagecolorsforindex($image, $rgb)["alpha"];
						$x_array = [$r, $g, $b, $alpha];
						$y_array[] = $x_array;
					}
					$colors[] = $y_array;
				}
				$skin = "";
				foreach($colors as $width){
					foreach($width as $height){
						$alpha = 0;
						if($height[0] === 255 and $height[1] === 255 and $height[2] === 255){
							$height[0] = 0;
							$height[1] = 0;
							$height[2] = 0;
							if($height[3] === 127){
								$alpha = 255;
							}else{
								$alpha = 0;
							}
						}else{
							if($height[3] === 127){
								$alpha = 0;
							}else{
								$alpha = 255;
							}
						}
						$skin = $skin.chr($height[0]).chr($height[1]).chr($height[2]).chr($alpha);
					}
				}
				imagedestroy($image);
				return $skin;
			}
		}
		return "";
	}

	/**
	 * @param DataPacket $packet
	 * @override
	 */
	public function handleDataPacket(DataPacket $packet){
		if(!$this->isConnected()){
			return;
		}

		$timings = Timings::getReceiveDataPacketTimings($packet);
		$timings->startTiming();

		$this->getServer()->getPluginManager()->callEvent($ev = new DataPacketReceiveEvent($this, $packet));
		if(!$ev->isCancelled() and !$packet->handle($this->sessionAdapter)){
			$this->getServer()->getLogger()->debug("Unhandled " . $packet->getName() . " received from " . $this->getName() . ": 0x" . bin2hex($packet->buffer));
		}

		$timings->stopTiming();
	}

	/**
	 * @param Packet $packet
	 */
	public function putRawPacket(Packet $packet){
		$this->interface->putRawPacket($this, $packet);
	}
	
}
