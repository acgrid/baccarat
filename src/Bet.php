<?php


namespace BaccaratGame;


class Bet
{
    /**
     * @var Game
     */
    protected $game;

    /**
     * Default odd configuration
     * @var array
     */
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
            case Game::RESULT_BANKER: $field = 'forBanker'; break;
            case Game::RESULT_PLAYER: $field = 'forPlayer'; break;
            case Game::RESULT_TIE: $field = 'forTie'; break;
            case Game::RESULT_PAIR: $field = 'forPair'; break;
            default: throw new \InvalidArgumentException("Unknown bet type '$for'.");
        }
        $this->$field[$gambler] = (isset($this->$field[$gambler]) ? $this->$field[$gambler] : 0) + $amount;
        if(!isset($this->gamblers[$gambler])) $this->gamblers[$gambler] = [];
        $this->gamblers[$gambler][$for] = $this->$field[$gambler];
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
            foreach($this->gamblers as $gambler => $bets){
                $container = new Result();
                foreach($bets as $bet => $amount){
                    $container->add($bet, $amount, $bet === $result ? $this->settle($amount, $this->odds[$result]) : 0);
                }
                $this->results[$gambler] = $container;
            }
        }
        return $this->results;
    }

    /**
     * @param $gambler
     * @return Result|null
     */
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