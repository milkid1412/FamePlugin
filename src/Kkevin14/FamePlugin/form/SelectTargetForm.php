<?php
declare(strict_types=1);

namespace Kkevin14\FamePlugin\form;

use Kkevin14\FamePlugin\Main;
use pocketmine\form\Form;
use pocketmine\player\Player;

class SelectTargetForm implements Form
{
    private Main $owner;

    private array $target;

    public function __construct(Main $owner, array $target)
    {
        $this->owner = $owner;
        $this->target = $target;
    }

    public function jsonSerialize()
    {
        $buttons = [];
        /* @var Player $player */
        foreach($this->target as $player){
            $buttons[] = [
                'text' => $player->getName()
            ];
        }
        return [
            'type' => 'form',
            'title' => $this->owner->title,
            'content' => '§b▲ §f인기도를 지급할 대상을 선택해주세요.',
            'buttons' => $buttons
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        if(strtolower($this->target[$data]->getName()) === strtolower($player->getName())){
            $this->owner->msg($player, '자신에 대한 사랑이 여기까지 느껴지네요!');
            return;
        }
        $target = $this->target[$data];
        $res = $this->owner->giveFame($player, $target);
        if($res){
            $this->owner->msg($player, $target->getName() . '님에게 인기도를 지급했습니다. §7(남은 인기도 포인트: §b' . $this->owner->getFameLeftCount($player) . 'P§7)');
            $this->owner->msg($target, $player->getName() . '님께서 인기도를 올려주셨습니다!');
            $this->owner->msg($target, '현재 인기도: §b' . $this->owner->getFame($target) . '§7(현재 §b' . $this->owner->getRank($target) . '위§7)');
        }else{
            $this->owner->msg($player, '오늘은 더이상 ' . $target->getName() . '님에게 인기도를 지급할 수 없습니다.');
        }
    }
}