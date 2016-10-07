<?php
/**
 * Created by PhpStorm.
 * User: acgrid
 * Date: 2016/10/7
 * Time: 15:35
 */

use BaccaratGame\Game;

require __DIR__ . '/../vendor/autoload.php';

if(PHP_SAPI != 'cli') header('Content-Type: text/plain');

$count = isset($argv[1]) ? intval($argv[1]) : 1000;
$pair = isset($argv[2]) ? boolval($argv[2]) : true;

$stat = [Game::RESULT_BANKER => 0, Game::RESULT_PLAYER => 0, Game::RESULT_TIE => 0];
if($pair) $stat[Game::RESULT_PAIR] = 0;
for($round = 0; $round < $count; $round++){
    $b = new Game();
    if(!$pair) $b->disablePair();
    $b->deal();
    while($b->draw());
    $stat[$b->result()]++;
}

foreach ($stat as $k => $v)
{
    printf("%s\t%u\t%.2f%%\n", $k, $v, $v / $count * 100);
}