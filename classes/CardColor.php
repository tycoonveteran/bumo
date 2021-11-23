<?php declare(strict_types=1);

class CardColor 
{
    const RED = 1;
    const BLUE = 2;
    const GREEN = 3;
    const YELLOW = 4;
    const SPECIAL = 5;

    private int $_;
    
    public function __construct($cardColor) 
    {
        $this->_ = $cardColor;
    }

    public function getColor () : int
    {
        return $this->_;
    }

    public function __toString() : string 
    {
        return match ($this->_) {
            self::RED => 'Rot',
            self::BLUE => 'Blau',
            self::GREEN => 'Grun',
            self::YELLOW => 'Gelb',
            self::SPECIAL => 'Special',
        };
    }
}