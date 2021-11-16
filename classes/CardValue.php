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

    public function __toString() : string 
    {
        return match ($this->_) {
            self::V0 => '0',
            self::V1 => '1',
            self::V2 => '2',
            self::V3 => '3',
            self::V4 => '4',
            self::V5 => '5',
            self::V6 => '6',
            self::V7 => '7',
            self::V8 => '8',
            self::V9 => '9',
            self::SPECIAL_AUSSETZEN => '(AUSSETZEN)',
            self::SPECIAL_RICHTUNGSWECHSEL => '(RICHTUNGSWECHSEL)',
            self::SPECIAL_PLUS_2 => '(+2)',
            self::SPECIAL_PLUS_4 => '(+4)',
            self::SPECIAL_JOKER => '(JOKER)',
        };
    }
}
    