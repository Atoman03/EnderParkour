<?php

namespace EnderParkour;

use pocketmine\plugin\PluginBase;
use pocketmine\entity\Entity;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\tile\Tile;
use pocketmine\block\Block;
use pocketmine\tile\Sign;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase implements Listener{

    public function onEnable(){
        $this->getLogger()->info(C::BLUE. "EnderParkour has been enabled!");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->parkour = new Config($this->getDataFolder(). "parkour.yml", Config::YAML);
        $this->saveDefaultConfig();
    }

    public function onDisable(){
        $this->getServer()->info(C::BLUE. "EnderParkour has been disabled!:o");
        $this->saveDefaultConfig();
    }

    public function Checkpoints(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $x = $player->getX();
        $y = $player->getY();
        $z = $player->getZ();
        $pn = $event->getPlayer()->getName();
        if($block->getId() == 63 or 68){
            $signature = $player->getLevel()->getTile($block);
            if(!($signature instanceof Sign)) return;
            $line = $signature->getText();
            if($line[0] == $this->getConfig()->get("CheckpointTextSign")){
                $this->parkour->set($pn,array($x,$y,$z,$player->getLevel()->getName()));
                $this->parkour->save();
                $player->sendMessage($this->getConfig()->get("CheckpointMsg"));
            }
            if($line[0] == $this->getConfig()->get("FinishTextSign")){
                $player->sendMessage($this->getConfig()->get("FinishMsg"));
                $this->getServer()->dispatchCommand($player, $this->getConfig()->get("FinishPlayerCmd"));
                $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $this->getConfig()->get("FinishConsoleCmd"), str_ireplace("{PLAYER}", $pn));
                if($this->getConfig()->get("DelParkourDataWhenFinish") == "true"){
                    $this->parkour->delete($pn);
                    $this->parkour->save();
                }
            }
        }
    }
    
    #public function onCommand(CommandSender $sender,Command $cmd,$label,array $args){
        #switch($cmd->getName()){
            #case "parkour":
                #if(!(isset($args[0]))){
                #$sender->sendMessage(C::ITALIC. "Parkour Commands!\n" .C::ITAlIC. "/parkour create <world name> -> Creates a parkour world");
            #}
        #}
    #}
    
    public function noVoid(PlayerMoveEvent $event){
        $player = $event->getPlayer();
        $pn = $player->getName();
        if($player->getLevel()->getName() == $this->getConfig()->get("NoVoidWorld")){
            if($event->getTo()->getFloorY() < 0.5){
                $cppos = $this->parkour->get($pn);
                if(is_array($cppos)){
                        $player->teleport(new Vector3($cppos[0],$cppos[1],$cppos[2],$this->getServer()->getLevelByName($cppos[3])));
                        $player->sendMessage($this->getConfig()->get("TpedToCheckpointMsg"));
                }else{
                    $this->getServer()->dispatchCommand($player, $this->getConfig()->get("VoidPlayerCmd"));
                }
            }
        }
    }
}
