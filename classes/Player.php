<?php declare(strict_types=1);

class Player 
{
    
    private array $playerCards = [];
    private string $name;
    private string $playerId;
    
    public function __construct(string $name, string $playerId) 
    {
        $this->name = $name;    
        $this->playerId = $playerId ?? uniqid();    
    }

    public function getName() : string 
    {
        return $this->name;
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

    public function removeCard($cardIndex)
    {
        unset ($this->playerCards[$cardIndex]);
        $this->playerCards = array_values($this->playerCards);
    }

    public function getCardByIndex ($cardIndex) : Card 
    {
        return $this->playerCards[$cardIndex];
    }

    public function getCards() : array 
    {
        return $this->playerCards;
    }

    public function getCardsAsStringArray() : array 
    {
        $data = [];
        foreach ($this->playerCards as $card)
        {
            $data[] = (string)$card;
        }
        return $data;
    }

}