<?php declare(strict_types=1);

class CardValue 
{
    const V0 = 0;
    const V1 = 1;
    const V2 = 2;
    const V3 = 3;
    const V4 = 4;
    const V5 = 5;
    const V6 = 6;
    const V7 = 7;
    const V8 = 8;
    const V9 = 9;

    const SPECIAL_AUSSETZEN = 10;
    const SPECIAL_RICHTUNGSWECHSEL = 11;
    const SPECIAL_PLUS_2 = 12;

    const SPECIAL_PLUS_4 = 14;
    const SPECIAL_JOKER = 15;

    private $_;

    public function __construct(int $cardValue) 
    {
        $this->_ = $cardValue;
    }

    public function getValue ()
    {
        return $this->_;
    }
}
    