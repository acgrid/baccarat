<?php


namespace BaccaratTest;


use BaccaratGame\Game;

class GameTest extends \PHPUnit_Framework_TestCase
{

    public function testSequence()
    {
        $this->assertSame('AH', Game::describeSequence('A'));

    }

    public function testGame()
    {
        $this->assertSame(Game::RESULT_PAIR, Game::replay('Czci')->result());
        $this->assertSame(Game::RESULT_PAIR, Game::replay('myZe')->result());
    }
}
