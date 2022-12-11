<?php
declare(strict_types=1);

namespace Kkevin14\FamePlugin\form;

use Kkevin14\FamePlugin\Main;
use pocketmine\form\Form;
use pocketmine\player\Player;

class SeeRankForm implements Form
{
    private Main $owner;

    private int $page;

    public function __construct(Main $owner, int $page)
    {
        $this->owner = $owner;
        $this->page = $page;
    }

    public function jsonSerialize()
    {
        $arr = $this->owner->db['fame'];
        arsort($arr);
        $arr_k = array_keys($arr);
        $arr_v = array_values($arr);
        $i = ($this->page - 1) * 10;
        $max = $this->page * 10 - 1;
        $str = '';
        $rank = $i + 1;
        $max_page = ceil(count($arr_k) / 10);
        for(; $i <= $max; $i++){
            $str .= '§f(§b' . $rank . '위§f) ' . $arr_k[$i] . ' - §a' . $arr_v[$i] . "\n";
            if(!isset($arr_k[$rank])) break;
            $rank++;
        }
        return [
            'type' => 'form',
            'title' => $this->owner->title,
            'content' => $str . '§f*전체 §e' . $max_page . '§f페이지 중 §e' . $this->page . '§f페이지 보는 중*' . "\n\n",
            'buttons' => [
                [
                    'text' => '§b▶ §f다음 페이지로'
                ],
                [
                    'text' => '§b◀ §f이전 페이지로'
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        $max_page = ceil(count($this->owner->db['fame']) / 10);
        if($data === null) return;
        if($data === 0){
            if(++$this->page > $max_page) $this->page--;
        }else{
            if(--$this->page < 1) $this->page++;
        }
        $player->sendForm(new SeeRankForm($this->owner, $this->page));
    }
}