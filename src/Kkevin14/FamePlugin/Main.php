<?php
declare(strict_types=1);

namespace Kkevin14\FamePlugin;

use JsonException;
use Kkevin14\FamePlugin\command\FameMenuCommand;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase
{
    private Config $database;

    public array $db;

    public string $title = '§l§7[ §f인기도 §7]';

    private static ?self $instance = null;

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public static function getInstance(): ?self
    {
        return self::$instance;
    }

    protected function onEnable(): void
    {
        $this->database = new Config($this->getDataFolder() . 'data.yml', Config::YAML, [
            'fame' => [],
            'fame_count' => [],
            'fame_limit' => 2,
            'date' => (int) date("d")
        ]);
        $this->db = $this->database->getAll();

        if($this->db['date'] !== (int) date("d")){
            $this->db['date'] = (int)date("d");
            $this->db['fame_count'] = [];
        }

        $this->getServer()->getCommandMap()->register('Kkevin14', new FameMenuCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function hasDB(Player|string $player): bool
    {
        $name = strtolower($player instanceof Player ? $player->getName() : $player);
        if(isset($this->db['fame'][$name])) return true;
        return false;
    }

    public function register(Player|string $player): void
    {
        $name = strtolower($player instanceof Player ? $player->getName() : $player);
        $this->db['fame'][$name] = 0;
    }

    public function getFame(Player|string $player): int
    {
        $name = strtolower($player instanceof Player ? $player->getName() : $player);
        return $this->db['fame'][$name];
    }

    /**
     * to dev: this method is getting "used_fame_count", not "usable_fame_count".
     * if you want to get "left fame count", use 'getFameLeftCount()' method.
     */
    public function getFameCount(Player|string $player): int
    {
        $name = strtolower($player instanceof Player ? $player->getName() : $player);
        if(!isset($this->db['fame_count'][$name])){
            $this->db['fame_count'][$name] = [];
        }
        return count($this->db['fame_count'][$name]);
    }

    public function getFameLimit(): int
    {
        return $this->db['fame_limit'];
    }

    public function getFameLeftCount(Player|string $player): int
    {
        return $this->getFameLimit() - $this->getFameCount($player);
    }

    public function giveFame(Player $giver, Player $target): bool
    {
        $name_g = strtolower($giver->getName());
        $name_t = strtolower($target->getName());

        if(in_array($name_t, $this->db['fame_count'][$name_g])) return false;

        $this->db['fame_count'][$name_g][] = $name_t;
        $this->db['fame'][$name_t] += 1;
        return true;
    }

    public function getLeftTime(): string
    {
        $end_time = strtotime(date("Y-m-d", strtotime("+1 day")) . " 00:00:00 ");
        $now_time = strtotime("now");
        $calc_date = $end_time - $now_time;
        $hour = substr("0".floor($calc_date / 3600),-2);
        $minute =  substr("0".(floor($calc_date / 60) - ($hour*60)),-2) ;
        $second = substr("0".($calc_date % 60),-2);

        return '§r§a' . $hour . '§f시간 §a' . $minute . '§f분 §a' . $second . '§f초';
    }

    public function msg(Player $player, string $msg)
    {
        if(!$player->isOnline()) return;
        $player->sendMessage('§b◈ §f' . $msg);
    }

    public function getPlayersByPrefix(string $key): array
    {
        $players = [];
        foreach($this->getServer()->getOnlinePlayers() as $player){
            if(str_contains(strtolower($player->getName()), strtolower($key))) $players[] = $player;
        }
        return $players;
    }

    public function getRank(Player|string $player): int
    {
        $name = strtolower($player instanceof Player ? $player->getName() : $player);
        $arr = $this->db['fame'];
        arsort($arr);
        $rank = 1;
        foreach($arr as $key => $value){
            if($key === $name) break;
            $rank++;
        }
        return $rank;
    }

    /**
     * @throws JsonException
     */
    public function onDisable(): void
    {
        $this->database->setAll($this->db);
        $this->database->save();
    }
}