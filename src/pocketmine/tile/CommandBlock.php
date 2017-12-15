<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\tile;

use pocketmine\block\Block;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\protocol\ContainerOpenPacket;
use pocketmine\network\protocol\types\WindowTypes;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionAttachment;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\Player;

class CommandBlock extends Spawnable implements Nameable, CommandSender{

    const NORMAL = 0;
    const REPEATING = 1;
    const CHAIN = 2;

    private $permission;

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);

        if(!isset($nbt->Command)){
            $nbt->Command = new StringTag("Command", "");
        }
        if(!isset($nbt->blockType)){
            $nbt->blockType = new IntTag("blockType", self::NORMAL);
        }
        if(!isset($nbt->SuccessCount)){
            $nbt->SuccessCount = new IntTag("SuccessCount", 0);
        }
        if(!isset($nbt->LastOutput)){
            $nbt->LastOutput = new StringTag("LastOutput", "");
        }
        if(!isset($nbt->TrackOutput)){
            $nbt->TrackOutput = new ByteTag("TrackOutput", 0);
        }
        if(!isset($nbt->powered)){
            $nbt->powered = new ByteTag("powered", 0);
        }
        if(!isset($nbt->conditionMet)){
            $nbt->conditionMet = new ByteTag("conditionMet", 0);
        }
        if(!isset($nbt->UpdateLastExecution)){
            $nbt->UpdateLastExecution = new ByteTag("UpdateLastExecution", 0);
        }
        if(!isset($nbt->LastExecution)){
            $nbt->LastExecution = new LongTag("LastExecution", 0);
        }
        if(!isset($nbt->auto)){
            $nbt->auto = new IntTag("auto", 0);
        }

        $this->permission = new PermissibleBase($this);

        $this->scheduleUpdate();
    }
    
    public function setName($str){
        $this->namedtag->CustomName = new StringTag("CustomName", $str);
    }
    
    public function hasName(){
        return isset($this->namedtag->CustomName);
    }

    public function getName(){
        return isset($this->namedtag->CustomName) ? $this->namedtag->CustomName->getValue() : "CommandBlock";
    }

    public function getCommand(){
        return isset($this->namedtag->Command) ? $this->namedtag->Command->getValue() : "";
    }

    public function setCommand($command){
        $this->namedtag->Command = new StringTag("Command", $command);
    }

    public function getSuccessCount(){
        return isset($this->namedtag->SuccessCount) ? $this->namedtag->SuccessCount->getValue() : "";
    }

    public function runCommand(){
        $this->server->dispatchCommand($this, $this->getCommand());
    }

    public function getSpawnCompound(){
        $nbt = new CompoundTag("", [
            new StringTag("id", Tile::COMMAND_BLOCK),
            new IntTag("x", (int) $this->x),
            new IntTag("y", (int) $this->y),
            new IntTag("z", (int) $this->z),
            new StringTag("Command", $this->getCommand()),
            new StringTag("blockType", $this->getBlockType()),
            new StringTag("LastOutput", $this->getLastOutput()),
            new ByteTag("TrackOutput", $this->getTrackOutput()),
            new IntTag("SuccessCount", $this->getSuccessCount()),
            new ByteTag("auto", $this->getAuto()),
            new ByteTag("powered", $this->getPowered()),
            new ByteTag("conditionalMode", $this->isConditional()),
        ]);
        return $nbt;
    }

    public function isNormal(){
        return $this->getBlockType() == self::NORMAL;
    }

    public function isRepeating(){
        return $this->getBlockType() === self::REPEATING;
    }

    public function isChain(){
        return $this->getBlockType() === self::CHAIN;
    }

    public function getBlockType(){
        return isset($this->namedtag->blockType) ? $this->namedtag->blockType->getValue() : self::NORMAL;
    }

    public function setBlockType($blockType){
        return $this->namedtag->blockType = new IntTag("blockType", $blockType > 2 or $blockType < 0 ? self::NORMAL : $blockType);
    }

    public function isConditional(){
        return boolval(isset($this->namedtag->conditionalMode) ? $this->namedtag->conditionalMode->getValue() : 0);
    }

    public function getPowered(){
        return boolval(isset($this->namedtag->powered) ? $this->namedtag->powered->getValue() : 0);
    }

    public function getAuto(){
        return boolval(isset($this->namedtag->auto) ? $this->namedtag->auto->getValue() : 0);
    }

    public function setConditional($condition){
        $this->namedtag->conditionMet = new IntTag("conditionMet", +$condition);
    }

    public function setPowered($powered){
        if($this->getPowered() == $powered){
            return;
        }
        $this->namedtag->powered = new IntTag("powered", +$powered);
        if($this->isNormal() && $powered && !$this->getAuto()){
            $this->runCommand();
        }
    }

    public function setAuto($auto){
        $this->namedtag->auto = new IntTag("auto", +$auto);
    }

    public function setLastOutput($lastOutput){
        $this->namedtag->LastOutput = new StringTag("LastOutput", $lastOutput);
    }

    public function getTrackOutput(){
        return boolval(isset($this->namedtag->TrackOutput) ? $this->namedtag->TrackOutput->getValue() : 0);
    }

    public function setTrackOutput($trackOutput) {
        return $this->namedtag->TrackOutput = new IntTag("TrackOutput", $trackOutput);
    }

    public function getLastOutput(){
        return isset($this->namedtag->LastOutput) ? $this->namedtag->LastOutput->getValue() : "";
    }

    public function show(Player $player){
        $pk = new ContainerOpenPacket();
    	$pk->type = WindowTypes::COMMAND_BLOCK;
    	$pk->windowId = 64;
    	$pk->x = $this->getFloorX();
    	$pk->y = $this->getFloorY();
    	$pk->z = $this->getFloorZ();
    	$player->dataPacket($pk);
    }

    public function chainUpdate(){
        if($this->getAuto() || $this->getPowered()){
            $this->runCommand();
        }
    }

    public function onUpdate(){
        if($this->closed){
            return false;
        }
        if(!$this->isRepeating()){
            return true;
        }
        $this->chainUpdate();
        return true;
    }
    
    public function sendMessage($message){
        $this->setLastOutput($message);
    }
    
    public function getServer(){
        return Server::getInstance();
    }
    
    public function isPermissionSet($name){
        return $this->permission->isPermissionSet($name);
    }
    
    public function hasPermission($name){
        return $this->permission->hasPermission($name);
    }
    
    public function addAttachment(Plugin $plugin, $name = null, $value = null){
        return $this->permission->addAttachment($plugin, $name, $value);
    }
    
    public function removeAttachment(PermissionAttachment $attachment){
        $this->permission->removeAttachment($attachment);
    }
    
    public function recalculatePermissions(){
        $this->permission->recalculatePermissions();
    }

    public function getEffectivePermissions(){
        return $this->permission->getEffectivePermissions();
    }
    
    public function isOp(){
        return true;
    }
    
    public function setOp($value){
    	
    }

    public function getIdByBlockType($type){
        $id = [
            self::NORMAL => Block::COMMAND_BLOCK,
            self::REPEATING => Block::REPEATING_COMMAND_BLOCK,
            self::CHAIN => Block::CHAIN_COMMAND_BLOCK
        ];
        return isset($id[$type]) ? $id[$type] : Block::COMMAND_BLOCK;
    }
}
