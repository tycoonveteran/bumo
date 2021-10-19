<?php declare(strict_types=1);

class Player 
{
    
    private array $playerCards;
    private string $name;
    private string $playerId;
    
    public function __construct(string $name, string $playerId) 
    {
        $this->name = $name;    
        $this->playerId = $playerId ?? uniqid();    
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

    public function removeCard($cardIndex) : Card 
    {
        return array_slice ($this->playerCards, $cardIndex, 1)[0];
    }

    public function getCardByIndex ($cardIndex) : Card 
    {
        return $this->playerCards[$cardIndex];
    }

    public function getCards() : array 
    {
        return $this->playerCards;
    }

}