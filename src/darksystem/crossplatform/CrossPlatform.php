<?php

namespace darksystem\crossplatform;

use pocketmine\network\protocol\Info;
use pocketmine\network\protocol\TextPacket;
use pocketmine\utils\TextFormat;
use pocketmine\Server;
use darksystem\phpseclib\Crypt\RSA;
use darksystem\crossplatform\network\ServerManager;
use darksystem\crossplatform\network\ProtocolInterface;
use darksystem\crossplatform\network\Translator;
use darksystem\crossplatform\utils\ConvertUtils;
use darksystem\crossplatform\utils\AES;

class CrossPlatform{

	/** @var ProtocolInterface */
	private $interface;

	/** @var RSA */
	protected $rsa;

	/** @var string */
	protected $privateKey;

	/** @var string */
	protected $publicKey;

	/** @var bool */
	protected $onlineMode;

	/** @var Translator */
	protected $translator;
	
    public function __construct(Server $server){
		$this->server = $server;
		
		ConvertUtils::init();
		
		$this->onlineMode = false;

		$aes = new AES();
		switch($aes->getEngine()){
			case AES::ENGINE_OPENSSL:
				$this->server->getLogger()->info("Use openssl as AES encryption engine.");
			break;
			case AES::ENGINE_MCRYPT:
				$this->server->getLogger()->warning("Use obsolete mcrypt for AES encryption. Try to install openssl extension instead!!");
			break;
			case AES::ENGINE_INTERNAL:
				$this->server->getLogger()->warning("Use phpseclib internal engine for AES encryption, this may impact on performance. To improve them, try to install openssl extension.");
			break;
		}

		$this->rsa = new RSA();
		switch(constant("CRYPT_RSA_MODE")){
			case RSA::MODE_OPENSSL:
				$this->rsa->configFile = $this->server->getDataPath() . "openssl.cnf";
				$this->server->getLogger()->info("Use openssl as RSA encryption engine.");
			break;
			case RSA::MODE_INTERNAL:
				$this->server->getLogger()->info("Use phpseclib internal engine for RSA encryption.");
			break;
		}
		
		if(Info::CURRENT_PROTOCOL === 141){
			$this->translator = new Translator();
			
			if($this->onlineMode){
				$this->server->getLogger()->info("Server is being started in the background");
				$this->server->getLogger()->info("Generating keypair");
				$this->rsa->setPrivateKeyFormat(RSA::PRIVATE_FORMAT_PKCS1);
				$this->rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_PKCS8);
				$this->rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
				$keys = $this->rsa->createKey(1024);
				$this->privateKey = $keys["privatekey"];
				$this->publicKey = $keys["publickey"];
				$this->rsa->loadKey($this->privateKey);
			}

			$this->server->getLogger()->info("Starting Minecraft: PC server on ".($this->getIp() === "0.0.0.0" ? "*" : $this->getIp()).":".$this->getPort()." version ".ServerManager::VERSION);
			
			$this->interface = new ProtocolInterface($this, $this->server, $this->translator, 256);
			$this->server->getNetwork()->registerInterface($this->interface);
		}else{
			$this->server->getLogger()->critical("Couldn't find a protocol translator for #".Info::CURRENT_PROTOCOL .", disabling plugin");
			return true;
		}
	}

    /**
     * @return Server
     */
    public function getServer(): Server{
        return $this->server;
    }

	/**
	 * @return string ip address
	 */
	public function getIp(){
		return "0.0.0.0";
	}

	/**
	 * @return int port
	 */
	public function getPort(){
		return 22565;
	}

	/**
	 * @return string motd
	 */
	public function getMotd(){
		return "DarkSystem Server";
	}

	/**
	 * @return bool
	 */
	public function isOnlineMode(){
		return $this->onlineMode;
	}

	/**
	 * @return string ASN1 Public Key
	 */
	public function getASN1PublicKey(){
		$key = explode("\n", $this->publicKey);
		array_pop($key);
		array_shift($key);
		return base64_decode(implode(array_map("trim", $key)));
	}

	/**
	 * @param string $cipher cipher text
	 * @return string plain text
	 */
	public function decryptBinary($cipher){
		return $this->rsa->decrypt($cipher);
	}
	
	/**
	 * @param string|null $message
	 * @param string|null $source
	 * @param int         $type
	 * @param array|null  $parameters
	 * @return string
	 */
	public static function toJSON($message, $source = "", $type = 1, $parameters = []){
		$message = $source.$message;
		$result = json_decode(TextFormat::toJSON($message), true);

		switch($type){
			case TextPacket::TYPE_TRANSLATION:
				unset($result["text"]);
				$message = TextFormat::clean($message);

				if(substr($message, 0, 1) === "["){
					$result["translate"] = "chat.type.admin";

					$result["with"][] = ["text" => substr($message, 1, strpos($message, ":") - 1)];
					$result["with"][] = ["translate" => preg_replace("/[^0-9a-zA-Z.]/", "", substr($message, strpos($message, "%") === false ? 0 : strpos($message, "%")))];

					$with = &$result["with"][1];
				}else{
					$result["translate"] = str_replace("%", "", $message);

					$with = &$result;
				}

				foreach($parameters as $parameter){
					if(strpos($parameter, "%") !== false){
						$with["with"][] = ["translate" => str_replace("%", "", $parameter)];
					}else{
						$with["with"][] = ["text" => $parameter];
					}
				}
			break;
			case TextPacket::TYPE_POPUP:
			case TextPacket::TYPE_TIP:
				if(isset($result["text"])){
					$result["text"] = str_replace("\n", "", $message);
				}

				if(isset($result["extra"])){
					unset($result["extra"]);
				}
			break;
		}

		if(isset($result["extra"])){
			if(count($result["extra"]) === 0){
				unset($result["extra"]);
			}
		}

		$result = json_encode($result, JSON_UNESCAPED_SLASHES);
		return $result;
	}
}
