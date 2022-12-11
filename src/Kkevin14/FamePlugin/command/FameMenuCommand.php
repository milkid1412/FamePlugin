<?php
declare(strict_types=1);

namespace Kkevin14\FamePlugin\command;

use Kkevin14\FamePlugin\form\MainMenuForm;
use Kkevin14\FamePlugin\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class FameMenuCommand extends Command
{
    private Main $owner;

    public function __construct(Main $owner)
    {
        parent::__construct('인기도', '인기도 메뉴를 여는 명령어입니다.', '/인기도', ['fame', 'popularity']);
        $this->owner = $owner;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player) return;
        $sender->sendForm(new MainMenuForm($this->owner, $sender));
    }
}