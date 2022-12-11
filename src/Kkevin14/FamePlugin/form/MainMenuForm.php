<?php
declare(strict_types=1);

namespace Kkevin14\FamePlugin\form;

use Kkevin14\FamePlugin\Main;
use pocketmine\form\Form;
use pocketmine\player\Player;

class MainMenuForm implements Form
{
    private Main $owner;
    private Player $player;

    public function __construct(Main $owner, Player $player)
    {
        $this->owner = $owner;
        $this->player = $player;
    }

    public function jsonSerialize()
    {
        return [
            'type' => 'form',
            'title' => $this->owner->title,
            'content' => "\n" . '§f내 인기도: §b' . $this->owner->getFame($this->player) . "\n" .
                '§f남은 인기도 포인트: §e' . $this->owner->getFameLeftCount($this->player) . "\n" .
                '§f인기도 포인트 초기화까지: ' . $this->owner->getLeftTime() . "\n\n",

            'buttons' => [
                [
                    'text' => '§a⊙ §f인기도 주기'
                ],
                [
                    'text' => '§c⊙ §f인기도 순위'
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        if($data === 0){
            if($this->owner->getFameCount($player) == $this->owner->getFameLimit()){
                $this->owner->msg($player, '오늘 지급된 인기도 포인트를 모두 사용하셨습니다.');
                return;
            }
            $player->sendForm(new SearchTargetForm($this->owner));
        }elseif($data === 1){
            $player->sendForm(new SeeRankForm($this->owner, 1));
        }
    }
}