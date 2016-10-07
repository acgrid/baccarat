<?php
/**
 * Created by PhpStorm.
 * User: acgrid
 * Date: 2016/10/4
 * Time: 20:08
 */

namespace BaccaratTest;


use BaccaratGame\Bet;
use BaccaratGame\Game;

class BetTest extends \PHPUnit_Framework_TestCase
{

    public function testBets()
    {
        $pool = new Bet($game = Game::replay('KNLEFeWpyygcVFMtLIQuHyxIBvCGZxDmCQBuLOSDhdnrhiTMKwfwETAYvyhLNjosBKIQJKZPRErVFwWendRuvaTuXrOvBDPtEuhmjhsXeAORdfYiYJYZoWILHWeSxBSaqARkjkYbOPbcYnrZtpXLUPzNdzQHIifSzZOhEJVsICVEzqmGIjqboVjlAufZWAmRraBgxBXVDPsda
eJWtFlkRDgwXqJQcywHgCTcEHnfkmLJqzWpGKVeukUYsKNFBpjoMkPPOvbwhbNadAxGGMnTCdHuOUMSynvlixSNsUXUfFGcQcTgtFKmlKOigjJoCyAwQYjldMqtzprUfhDyfXbrICtUvMDAemHliaEzSRnTmrtvWxgRCJlXTepkQbzbaZMgSpNlpLqZqUsNGxasHPwoonVcicDikoFG'));
        $pool->bet('Bob', Game::RESULT_BANKER, 3000)
            ->bet('Bob', Game::RESULT_BANKER, 4000)
            ->bet('Bob', Game::RESULT_TIE, 100)
            ->bet('Mary', Game::RESULT_PAIR, 1000)
            ->bet('Sally', Game::RESULT_PLAYER, 500)
            ->bet('Mill', Game::RESULT_PLAYER, 900)
            ->bet('Bill Gates', Game::RESULT_BANKER, 50000)
            ->bet('William', Game::RESULT_TIE, 7000)
            ->bet('Rich Guy', Game::RESULT_PAIR, 300000);
        $this->assertArrayHasKey(Game::RESULT_BANKER, $pool->betOf('Bob'));
        $this->assertArrayHasKey(Game::RESULT_TIE, $pool->betOf('Bob'));
        $this->assertEquals(7000, $pool->betOf('Bob')[Game::RESULT_BANKER]);
        $this->assertSame(Game::RESULT_BANKER, $game->result());
        $this->assertEquals(13650, $pool->resultOf('Bob')->getBalance());
        $this->assertEquals(0, $pool->resultOf('Mary')->getBalance());
        $this->assertEquals(0, $pool->resultOf('Sally')->getBalance());
        $this->assertEquals(0, $pool->resultOf('Mill')->getBalance());
        $this->assertEquals(97500, $pool->resultOf('Bill Gates')->getBalance());
        $this->assertEquals(0, $pool->resultOf('William')->getBalance());
        $this->assertEquals(0, $pool->resultOf('Rich Guy')->getBalance());
        $this->assertEquals(2850, $pool->commission());
    }
}
