<?php
/**
 * Created by PhpStorm.
 * User: acgrid
 * Date: 2016/10/4
 * Time: 21:16
 */

use BaccaratGame\Game;

require __DIR__ . '/../vendor/autoload.php';

if(PHP_SAPI != 'cli') header('Content-Type: text/plain');

$game = new Game();
$game->deal();
echo "=SHUFFLE=\n";
printf("Sequence: %s\n", $game->sequence());
echo "=DEAL=\n";
printf("Banker Cards: %s [%u]\n", ShowCards($game->bankerCards()), $game->bankerPoint());
printf("Player Cards: %s [%u]\n", ShowCards($game->playerCards()), $game->playerPoint());

while($game->draw()){
    echo "=DRAW=\n";
    printf("Banker Cards: %s [%u]\n", ShowCards($game->bankerCards()), $game->bankerPoint());
    printf("Player Cards: %s [%u]\n", ShowCards($game->playerCards()), $game->playerPoint());
}

printf("=RESULT=\n%s\n", $game->result());

function ShowCards($cards){
    return implode(",", array_map(function($card){
        $card = $card[1];
        if($card == 1){
            return 'A';
        }elseif($card === 11){
            return 'J';
        }elseif($card === 12){
            return 'Q';
        }elseif($card === 13){
            return 'K';
        }else{
            return $card;
        }
    }, $cards));
}