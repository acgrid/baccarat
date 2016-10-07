<?php
/**
 * Created by PhpStorm.
 * User: acgrid
 * Date: 2016/10/4
 * Time: 20:51
 */

namespace BaccaratGame;



class Result implements \ArrayAccess, \Countable, \IteratorAggregate
{
    protected $balance = 0;
    protected $bets = [];

    public function getIterator()
    {
        return new \ArrayIterator($this->bets);
    }

    public function offsetExists($offset)
    {
        return isset($this->bets[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->bets[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Result item is read-only.');
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Result item is read-only.');
    }

    public function count()
    {
        return count($this->bets);
    }

    /**
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param string $bet
     * @param float $amount
     * @param float $balance
     * @return $this
     */
    public function add($bet, $amount, $balance)
    {
        if(isset($this->bets[$bet])){
            $this->bets[$bet] = [$this->bets[$bet][0] + $amount, $this->bets[$bet][1] + $balance];
        }else{
            $this->bets[$bet] = [$amount, $balance];
        }
        $this->balance += $balance;
        return $this;
    }

}