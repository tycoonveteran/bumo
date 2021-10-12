<?php declare(strict_types=1);

class GameController 
{
    
    private array $players;
    private string $gameId; 

    private array $staple;

    public function __construct(array $players)
    {
        $this->players = $players;
        $this->gameId = uniqid();
    }

    public function initNewGame () 
    {
        // 1. Kartenstapel erzeugen
        $cards = $this->createCardDeck();

        // 2. Mischen
        $cards = $this->shuffleCardDeck($cards);

        
    }




    public function getGameId() 
    {
        return $this->gameId;
    }

    public function createCardDeck (
        int $deckMultiplikator = 1,
        int $specialCardMultiplikator = 2
    ) : array 
    {
        for ($decks = 1; $decks <= $deckMultiplikator; $decks++)
        {
            // Wir möchten ein neues Kartendeck erzeugen.
            for ($color = CardColor::RED; $color <= CardColor::YELLOW; $color++)
            {
                for ($value = CardValue::V0; $value <= CardValue::SPECIAL_PLUS_2; $value++)
                {
                    $cardDeck[] = new Card(new CardColor($color), new CardValue($value));
                }
            }
            
            for ($specialM = 1; $specialM <= $specialCardMultiplikator; $specialM++)
            {
                // Nun die Sonderkarten beifügen
                $cardDeck[] = new Card(
                    new CardColor(CardColor::SPECIAL),
                    new CardValue(CardValue::SPECIAL_PLUS_4)
                );

                $cardDeck[] = new Card(
                    new CardColor(CardColor::SPECIAL),
                    new CardValue(CardValue::SPECIAL_JOKER)
                );
            }
        }
        
        return $cardDeck;
    }

    public function shuffleCardDeck(array $cardDeck) : array 
    {
        shuffle($cardDeck);
        return $cardDeck;
    }
}