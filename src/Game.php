<?php


namespace BaccaratGame;


class Game
{
    const SUITS = 8;

    const HEART = 'H';
    const SPADE = 'S';
    const DIAMOND = 'D';
    const CLUB = 'C';

    const PLAYING = false;
    const RESULT_BANKER = 'BANKER';
    const RESULT_PLAYER = 'PLAYER';
    const RESULT_TIE = 'TIE';
    const RESULT_PAIR = 'PAIR';

    const PLAYER = 'P';
    const BANKER = 'B';

    protected $sequence;
    /**
     * @var array
     */
    protected $deals = [];
    /**
     * @var array
     */
    protected $cardsOfBanker = [];
    /**
     * @var array
     */
    protected $cardsOfPlayer = [];

    /**
     * @var string
     */
    protected $result;
    /**
     * @var bool
     */
    protected $pairRule = true;

    public function __construct($suits = self::SUITS)
    {
        if(is_int($suits) && $suits > 0) $this->prepare($suits);
    }

    public function enablePair()
    {
        $this->pairRule = true;
        return $this;
    }

    public function disablePair()
    {
        $this->pairRule = false;
        return $this;
    }

    public static function replay($sequence, $pairRule = true)
    {
        $game = new self(0);
        $game->pairRule = $pairRule;
        $game->sequence = $sequence;
        $game->deals = str_split($sequence);
        $game->deal();
        while($game->draw()) ;
        return $game;
    }

    public function prepare($suits)
    {
        for($i = 0; $i < $suits; $i++){
            $this->deals = array_merge($this->deals, self::makeSuit());
            shuffle($this->deals);
        }
        usort($this->deals, function () {
            return random_int(-1, 1);
        });
        $this->sequence = join($this->deals);
        unset($this->result);
    }

    public function deal()
    {
        if(empty($this->cardsOfBanker) && empty($this->cardsOfPlayer)){
            $this->cardsOfPlayer[] = array_shift($this->deals);
            $this->cardsOfBanker[] = array_shift($this->deals);
            $this->cardsOfPlayer[] = array_shift($this->deals);
            $this->cardsOfBanker[] = array_shift($this->deals);
        }
        return $this;
    }

    public static function makeSuit()
    {
        return array_merge(range('A', 'Z'), range('a', 'z'));
    }

    public static function describeCard($card)
    {
        if($card <= 'M'){
            return [self::HEART, ord($card) - 64];
        }elseif($card <= 'Z'){
            return [self::SPADE, ord($card) - 77];
        }elseif($card <= 'm'){
            return [self::DIAMOND, ord($card) - 96];
        }elseif($card <= 'z'){
            return [self::CLUB, ord($card) - 109];
        }else{
            throw new \Exception("Unknown card char: $card.");
        }
    }

    public static function getCardValue($readableValue)
    {
        if(($int = intval($readableValue)) > 0) return $int;
        switch($readableValue){
            case 'A': return 1;
            case '0': return 10;
            case 'J': return 11;
            case 'Q': return 12;
            case 'K': return 13;
            default: throw new \InvalidArgumentException("Invalid human readable card value '$readableValue'");
        }
    }

    public static function makeSequence($decks)
    {
        if(!is_string($decks) || ($len = strlen($decks)) % 2 !== 0) throw new \InvalidArgumentException('Bad human readable sequence, example "AH" (ace of hearts), "3S" (three of spades), "0D" (ten of diamonds), "KC" (king of clubs).');
        $sequence = '';
        for($i = 0; $i < $len; $i++){
            $cardValue = $decks[$i];
            switch($decks[++$i]){
                case self::HEART: $sequence .= 64 + self::getCardValue($cardValue); break;
                case self::SPADE: $sequence .= 77 + self::getCardValue($cardValue); break;
                case self::DIAMOND: $sequence .= 96 + self::getCardValue($cardValue); break;
                case self::CLUB: $sequence .= 109 + self::getCardValue($cardValue); break;
                default: throw new \InvalidArgumentException("Invalid human readable card color.");
            }
        }
    }

    public static function describeSequence($sequence)
    {
        $decks = '';
        foreach(str_split($sequence) as $card){
            list($color, $value) = self::describeCard($card);
            switch($value){
                case 1: $decks .= 'A'; break;
                case 10: $decks .= '0'; break;
                case 11: $decks .= 'J'; break;
                case 12: $decks .= 'Q'; break;
                case 13: $decks .= 'K'; break;
                default: $decks .= strval($value);
            }
            $decks .= $color;
        }
        return $decks;
    }

    public static function valueOfCard($card)
    {
        if($card <= 'M'){
            return ord($card) - 64;
        }elseif($card <= 'Z'){
            return ord($card) - 77;
        }elseif($card <= 'm'){
            return ord($card) - 96;
        }elseif($card <= 'z'){
            return ord($card) - 109;
        }else{
            throw new \Exception("Unknown card char: $card.");
        }
    }

    public static function pointOfCard($card)
    {
        $value = self::valueOfCard($card);
        return $value < 10 ? $value : 0;
    }

    public static function pointOfCards(array $cards)
    {
        return array_reduce($cards, function($result, $item){
            return $result + self::pointOfCard($item);
        }, 0) % 10;
    }

    public function pair()
    {
        if(isset($this->result)) return $this->result === self::RESULT_PAIR;
        if($this->pairRule && count($this->cardsOfPlayer) == 2 && count($this->cardsOfBanker) == 2){
            $player = array_slice($this->cardsOfPlayer, 0, 2);
            if(self::valueOfCard($player[0]) == self::valueOfCard($player[1])) return $this->result = self::RESULT_PAIR;
            $banker = array_slice($this->cardsOfBanker, 0, 2);
            if(self::valueOfCard($banker[0]) == self::valueOfCard($banker[1])) return $this->result = self::RESULT_PAIR;
        }
        return false;
    }

    public function draw()
    {
        if(isset($this->result) || $this->pair()) return false;
        $drawn = [];
        if(count($this->cardsOfBanker) < 3 && count($this->cardsOfPlayer) < 3){
            if(self::pointOfCards($this->cardsOfPlayer) <= 5){
                $drawn[self::PLAYER] = $playerDrawn = self::pointOfCard($this->cardsOfPlayer[] = array_shift($this->deals));
            }
            switch(self::pointOfCards($this->cardsOfBanker)){
                case 0:
                case 1:
                case 2:
                    $drawn[self::BANKER] = $this->cardsOfBanker[] = array_shift($this->deals);
                    break;
                case 3:
                    if(!(isset($playerDrawn) && $playerDrawn === 8)){
                        $drawn[self::BANKER] = $this->cardsOfBanker[] = array_shift($this->deals);
                    }
                    break;
                case 4:
                    if(!(isset($playerDrawn) && in_array($playerDrawn, [0, 1, 8, 9]))){
                        $drawn[self::BANKER] = $this->cardsOfBanker[] = array_shift($this->deals);
                    }
                    break;
                case 5:
                    if(!(isset($playerDrawn) && in_array($playerDrawn, [0, 1, 2, 3, 8, 9]))){
                        $drawn[self::BANKER] = $this->cardsOfBanker[] = array_shift($this->deals);
                    }
                    break;
                case 6:
                    if(isset($playerDrawn) && in_array($playerDrawn, [6, 7])){
                        $drawn[self::BANKER] = $this->cardsOfBanker[] = array_shift($this->deals);
                    }
                    break;
                default:
            }
        }
        if(empty($drawn)){ // natural or end of round
            $pointsBanker = self::pointOfCards($this->cardsOfBanker);
            $pointsPlayer = self::pointOfCards($this->cardsOfPlayer);
            if($pointsBanker > $pointsPlayer){
                $this->result = self::RESULT_BANKER;
            }elseif($pointsBanker < $pointsPlayer){
                $this->result = self::RESULT_PLAYER;
            }else{
                $this->result = self::RESULT_TIE;
            }
        }
        return $drawn ?: false;
    }

    public function bankerCards()
    {
        return array_map([self::class, 'describeCard'], $this->cardsOfBanker);
    }

    public function bankerPoint()
    {
        return self::pointOfCards($this->cardsOfBanker);
    }

    public function playerCards()
    {
        return array_map([self::class, 'describeCard'], $this->cardsOfPlayer);
    }

    public function playerPoint()
    {
        return self::pointOfCards($this->cardsOfPlayer);
    }

    /**
     * @return string
     */
    public function result()
    {
        return isset($this->result) ? $this->result : self::PLAYING;
    }

    public function heap()
    {
        return $this->deals;
    }

    public function sequence()
    {
        return $this->sequence;
    }
}