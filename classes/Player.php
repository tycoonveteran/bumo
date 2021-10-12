<?php declare(strict_types=1);

class Player 
{
    
    private array $playerCards;
    private string $name;
    private string $playerId;
    
    public function __construct(string $name) 
    {
        $this->name = $name;    
        $this->playerId = uniqid();    
    }

    public function getPlayerId() : string 
    {
        return $this->playerId;
    }

    public function addCard (Card $card) : Player
    {
        $this->playerCards[] = $card;
        return $this;
    }

    public function removeCard($cardIndex) : Player 
    {
        array_slice ($this->playerCards, $cardIndex, 1);
        return $this;
    }

    public function getCards() : array 
    {
        return $this->playerCards;
    }

}