<?php declare(strict_types=1);

class GameController 
implements JsonSerializable
{
    public const GAME_INIT = 1;
    public const GAME_RUNNING = 2;
    public const GAME_FINISHED = 3;
    
    /** @var Player[] */
    private array $players;

    private string $gameId; 

    /** @var Card[] Abgelegte Kartenstapel */
    private array $staple = [];
    /** @var Card[] Verdeckter Kartenstapel */
    private array $cardDeck = [];

    private int $cardCount;

    private int $nextPlayer = 0;

    private int $gameStatus; 

    public function __construct()
    {
        
    }

    public function initialize(Player $currentPlayer, $gameId = null, int $cardCount = 7) 
    {
        // Neues Spiel, erster Spieler
        $this->gameId = $gameId ?? uniqid();
        $this->gameStatus = self::GAME_INIT;
        $this->cardCount = $cardCount;
        $this->joinGame($currentPlayer);
    }

    public function joinGame (Player $currentPlayer) 
    {
        $this->players[] = $currentPlayer;
    }

    public function canStartNewGame() : bool 
    {
        return count($this->players) > 1;        
    }

    public function initNewGame () : void
    {
        // 1. Kartenstapel erzeugen
        $this->cardDeck = $this->createCardDeck();

        // 2. Mischen
        $this->shuffleCardDeck();

        // 3. Karten verteilen
        for ($cardCounter = 1; $cardCounter <= $this->cardCount; $cardCounter++) {
            foreach ($this->players as $player) {
                $player->addCard($this->getTopCardFromDeck());
            }
        }
        
        // Nun hat jeder Spieler X Karten auf der Hand.
        // 4. Oberste Karte offenlegen. 
        $this->addTopCardToStaple($this->getTopCardFromDeck());
        
        // 5. Spiel starten
        $this->gameStatus = self::GAME_RUNNING;
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

    public function shuffleCardDeck() : void
    {
        shuffle($this->cardDeck);
    }

    public function getTopCardFromDeck () : Card
    {
        return array_shift ($this->cardDeck);
    }

    public function addTopCardToStaple (Card $card) : void
    {
        $this->staple[] = $card;
    }

    public function checkCardsCompatible(Card $card1, Card $card2) : bool 
    {
        if (in_array (CardColor::SPECIAL, [$card1->getCardColor()->getColor(), $card2->getCardColor()->getColor()])) {
            // Einer der beiden Karten ist ein Joker / 4+, das heißt es kann 
            // eine beliebige Karte folgen bzw. diese kann auf eine beliebige
            // Karte gelegt werden. 
            return true;
        }

        if ($card1->getCardColor()->getColor() == $card2->getCardColor()->getColor()) {
            // Karten haben die selbe Farbe
            return true;
        }

        if ($card1->getCardValue()->getValue() == $card2->getCardValue()->getValue()) {
            // Karte hat die selbe Zahl => Farbwechsel
            return true;
        }
        // Ansonsten passen die Karten nicht aufeinander
        return false;
    }

    public function makePlayerMove ($cardIndex) : bool
    {
        // Variante 1: Spieler legt Karte
        $playerCard = $this->players[$this->nextPlayer]->getCardByIndex($cardIndex);
        if ($this->checkCardsCompatible(end($this->staple), $playerCard)) {
            $this->staple[] = $playerCard;
            $this->players[$this->nextPlayer]->removeCard($cardIndex);
            
            if ($this->checkPlayerWins()) {
                $this->gameStatus = self::GAME_FINISHED;
            } else {
                $this->nextPlayer = $this->nextPlayerIndex();
            }
            return true;
        }
        return false;
    }

    private function nextPlayerIndex() : int 
    {
        if (isset($this->players[$this->nextPlayer+1]))  {
            return $this->nextPlayer+1;
        } else {
            return 0;
        }
    }

    private function checkPlayerWins () : bool 
    {
        return count($this->players[$this->nextPlayer]->getCards()) == 0;
    }

    public function addCardForPlayer() : void 
    {
        // Variante 2: Spieler zieht Karte
        $this->players[$this->nextPlayer]->addCard($this->getTopCardFromDeck());
    }

    public function getPlayers() 
    {
        return $this->players;
    }

    public function jsonSerialize() : mixed 
    {
        foreach ($this->players as $player) {
            $data['players'][$player->getPlayerId()]['cards'] = $player->getCardsAsStringArray();
        }
        $data['topCardStaple'] = (string)end($this->staple);
        $data['nextPlayerId'] = $this->getNextPlayerId();
        return $data;
    }

    public function getNextPlayerId() 
    {
        return $this->players[$this->nextPlayer]->getPlayerId();
    }
}