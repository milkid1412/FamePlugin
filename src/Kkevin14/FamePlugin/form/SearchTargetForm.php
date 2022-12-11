<?php
declare(strict_types=1);

namespace Kkevin14\FamePlugin\form;

use Kkevin14\FamePlugin\Main;
use pocketmine\form\Form;
use pocketmine\player\Player;

class SearchTargetForm implements Form
{
    private Main $owner;

    public function __construct(Main $owner)
    {
        $this->owner = $owner;
    }

    public function jsonSerialize()
    {
        return [
            'type' => 'custom_form',
            'title' => $this->owner->title,
            'content' => [
                [
                    'type' => 'input',
                    'text' => '§b▲ §f인기도를 지급할 대상의 닉네임을 입력해주세요.'
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if(!isset($data[0]) || strlen($data[0]) < 3 || empty($this->owner->getPlayerByPrefix($data[0]))){
            $this->owner->msg($player, '현재 접속하고 있는 유저의 이름을 검색해주세요. (3글자 이상)');
            return;
        }
        $player->sendForm(new SelectTargetForm($this->owner, $this->owner->getPlayerByPrefix($data[0])));
    }
}