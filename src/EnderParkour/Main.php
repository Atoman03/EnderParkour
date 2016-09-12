>?php

namespace EnderParkour;

use pocketmine\plugin\PluginBase;
use pocketmine\entity\Entity;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\tile\Tile;
use pocketmine\tile\Sign;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener{

//WIP
