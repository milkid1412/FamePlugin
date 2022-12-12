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
        $name = strtolower($player->getName());
        if(!isset($data[0]) || strlen($data[0]) < 3){
            $this->owner->msg($player, '현재 접속하고 있는 유저의 이름을 검색해주세요. (3글자 이상)');
            return;
        }
        $target_arr = $this->resizePlayerArray($this->owner->getPlayersByPrefix($data[0]), $name);
        if(empty($target_arr)){
            $this->owner->msg($player, '" ' . $data[0] . ' "에 해당하는 검색결과가 없습니다.');
            return;
        }
        $player->sendForm(new SelectTargetForm($this->owner, $target_arr));
    }

    public function resizePlayerArray(array $players, string $pn): array
    {
        $arr = [];
        if(!empty($players))
        foreach($players as $player){
            if(strtolower($player->getName()) === $pn) continue;
            $arr[] = $player;
        }
        return $arr;
    }
}