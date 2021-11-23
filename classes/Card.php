<?php declare(strict_types=1);

class Card 
{
    private CardColor $color;
    private CardValue $value;

    public function __construct(CardColor $cardColor, CardValue $cardValue)
    {
        $this->color = $cardColor;
        $this->value = $cardValue;
    }

    public function getCardColor () : CardColor 
    {
        return $this->color;
    }

    public function getCardValue () : CardValue
    {
        return $this->value;
    }

    public function __toString() : string 
    {
        return $this->getCardColor() . ':' . $this->getCardValue();
    }

}