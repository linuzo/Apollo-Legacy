<?php

namespace pocketmine\command\defaults;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
class ClearChatCommand extends VanillaCommand{
    public function __construct($name){
        parent::__construct(
            $name,
            "%pocketmine.command.clearchat.description",
            "%commands.clearchat.usage"
        );
        $this->setPermission("pocketmine.command.clearchat");
    }
    public function execute(CommandSender $sender, $currentAlias, array $args){
        if(!$this->testPermission($sender)){
            return true;
        }
        
        $sender->getServer()->clearChat();
        $sender->sendMessage(TextFormat::GREEN . "The chat has been cleared!");
        return true;
    }
}
