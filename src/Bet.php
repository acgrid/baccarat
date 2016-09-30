<?php


namespace BaccaratGame;


class Bet
{
    /**
     * @var Game
     */
    protected $game;

    protected $odds = [
        Game::RESULT_BANKER => 0.95,
        Game::RESULT_PLAYER => 1,
        Game::RESULT_TIE => 8,
        Game::RESULT_PAIR => 11,
    ];

    /**
     * @var array
     */
    protected $gamblers = [];
    /**
     * @var array
     */
    protected $results = [];
    protected $commission = 0;

    protected $forBanker = [];
    protected $forPlayer = [];
    protected $forTie = [];
    protected $forPair = [];

    public function __construct(Game $game, array $odds = null)
    {
        $this->game = $game;
        if(isset($odds)) $this->odds = $odds;
    }

    public function bet($gambler, $for, $amount)
    {
        if(intval($amount) <= 0) throw new \InvalidArgumentException("Bad amount '$amount'.");
        switch($for){
            case Game::RESULT_BANKER: $this->forBanker[$gambler] = $amount; break;
            case Game::RESULT_PLAYER: $this->forPlayer[$gambler] = $amount; break;
            case Game::RESULT_TIE: $this->forTie[$gambler] = $amount; break;
            case Game::RESULT_PAIR: $this->forPair[$gambler] = $amount; break;
            default: throw new \InvalidArgumentException("Unknown bet type '$for'.");
        }
        $this->gamblers[$gambler] = [$for, $amount];
        $this->results = [];
        $this->commission = 0;
        return $this;
    }

    public function betOf($gambler)
    {
        return isset($this->gamblers[$gambler]) ? $this->gamblers[$gambler] : null;
    }

    protected function settle($amount, $odd)
    {
        if($odd < 1) $this->commission += $amount * (1 - $odd);
        return $amount * (1 + $odd);
    }

    public function results()
    {
        if(empty($this->results)){
            $result = $this->game->result();
            foreach($this->gamblers as $gambler => $bet){
                list($for, $amount) = $bet;
                if($for === $result){ // win
                    $this->results[$gambler] = $this->settle($amount, $this->odds[$result]);
                }else{ // lose
                    $this->results[$gambler] = 0;
                }
            }
        }
        return $this->results;
    }

    public function resultOf($gambler)
    {
        $results = $this->results();
        return isset($results[$gambler]) ? $results[$gambler] : null;
    }

    public function commission()
    {
        return $this->commission;
    }

}