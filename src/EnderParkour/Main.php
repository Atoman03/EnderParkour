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
        $this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML);
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
            $signature = $this->getLevel()->getTile(block);
            if(!($signature instanceof Sign)) return;
            $line = $sign->getText();
            if($signature[0] == $this->config->get("CheckpointTextSign")){
                $this->parkour->set($pn,array($x,$y,$z,$player->getLevel()->getName()));
                $this->parkour->save();
                $player->sendMessage($this->config->get("CheckpointMsg"));
            }
            if($signature[0] == $this->config->get("FinishTextSign")){
                $player->sendMessage($this->config->get("FinishMsg"));
                $this->getServer()->dispatchCommand($player, $this->config->get("FinishPlayerCmd"));
                $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $this->config->get("FinishConsoleCmd"), str_ireplace("{PLAYER}", $pn));
            }
        }
    }
    
    public function noVoid(PlayerMoveEvent $event){
        $player = $event->getPlayer();
        $pn = $player->getName();
        if($player->getLevel()->getName() == $this->config->get("NoVoidWorld")){
            if($event->getTo()->getFloorY() < 0.5){
                $cppos = $this->parkour->get($pn);
		$player->teleport(new Vector3($cppos[0],$cppos[1],$cppos[2],$this->getServer()->getLevelByName($cppos[3])));
                if(empty($this->parkour->get($player))){
                    $this->getServer()->dispatchCommand($player, $this->parkour->get("VoidPlayerCmd"));
                }
            }
        }
    }
}
