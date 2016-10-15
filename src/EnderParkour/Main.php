<?php
 
namespace EnderParkour;
 
use pocketmine\plugin\PluginBase;
use pocketmine\entity\Entity;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\tile\Tile;
use pocketmine\scheduler\Task;
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
        
        if(!is_dir($this->getDataFolder(). "worlds/")) mkdir($this->getDataFolder(). "worlds/");
        $this->saveDefaultConfig();
    }
 
    public function onDisable(){
        $this->getLogger()->info(C::BLUE. "EnderParkour has been disabled!:o");
        $this->saveDefaultConfig();
    }
    public function Checkpoints(PlayerInteractEvent $event){
         
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $pklevel = (new Config($this->getDataFolder(). "worlds/". $player->getLevel()->getName(). "/". $player->getLevel()->getName(). ".yml", Config::YAML))->getAll();
        if($player->getLevel()->getName() == $pklevel["world"]){
            $this->parkour = new Config($this->getDataFolder(). "worlds/". $player->getLevel()->getName()."/". "parkour.yml", Config::YAML);
            $x = $player->getX();
            $y = $player->getY();
            $z = $player->getZ();
            $pn = $event->getPlayer()->getName();
            if($block->getId() == 63 or 68){
                $signature = $this->getServer()->getLevelByName($pklevel["world"])->getTile($block);
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
                        $this->parkour->remove($pn);
                        $this->parkour->save();
                    }
                }
            }
        }
    }
     
    public function onCommand(CommandSender $sender,Command $cmd,$label,array $args){
        switch($cmd->getName()){
            case "enderparkour":
                if($sender->hasPermission("enderparkour.cmd")){
                        $sender->sendMessage(C::ITALIC. "EnderParkour Commands!\n" .C::ITALIC. "/parkour create -> Creates a Parkour world on your world!". C::ITALIC. "/parkour delete <world name> -> Deletes a Parkour world!");
                }
                 
                if(isset($args[0]) || $sender->hasPermission("enderparkour.cmd.create") || $args[0] == "create" || !file_exists($this->getDataFolder(). "worlds/". $sender->getLevel()->getName(). ".yml")){
                    $this->worlds = new Config($this->getDataFolder(). "worlds/". $sender->getLevel()->getName(). "/". $sender->getLevel()->getName(). ".yml", Config::YAML);
                    $this->worlds->set("world", $sender->getLevel()->getName());
                    $this->worlds->save();
                    $sender->sendMessage(C::ITALIC. C::GREEN. "EnderParkour >> Successfully created a Parkour world named ". $sender->getLevel()->getName(). "!");
                    return true;
                }else{
                    $sender->sendMessage(C::ITALIC. C::RED. "EnderParkour >> You don't have permission to create a Parkour world or the Parkour world already exists!");
                }
                if(isset($args[0]) || isset($args[1]) || $args[0] == "delete" || $sender->hasPermission("enderparkour.cmd.delete") || file_exists($this->getDataFolder(). "worlds/". $args[1] ."/". $args[1]. ".yml")){
                    unlink($this->getDataFolder(). "worlds/". $args[1]."/". $args[1]. ".yml");
                    $sender->sendMessage(C::ITALIC. C::GREEN. "EnderParkour >> Successfully deleted the Parkour world named". $args[0]. "!");
                    return true;
                }else{
                    $sender->sendMessage(C::ITALIC. C::RED. "EnderParkour >> Please enter a Parkour world name or You don't have permission to delete a Parkour world or the Parkour world doesn't exist!");
                    return true;
                }
        }
    }
     
    public function noVoid(PlayerMoveEvent $event){
        $player = $event->getPlayer();
        $pklevel = (new Config($this->getDataFolder(). "worlds/". $player->getLevel()->getName(). "/". $player->getLevel()->getName(). ".yml", Config::YAML))->getAll();
        if($player->getLevel()->getName() == $pklevel["world"]){
            $pn = $player->getName();
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
