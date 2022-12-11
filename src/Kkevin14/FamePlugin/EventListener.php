<?php
declare(strict_types=1);

namespace Kkevin14\FamePlugin;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener
{
    private Main $owner;

    public function __construct(Main $owner)
    {
        $this->owner = $owner;
    }

    public function onPlayerJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        if(!$this->owner->hasDB($player)){
            $this->owner->register($player);
        }
    }
}