# bumo

TODO:
- Oberfläche 30.11.

  -> Anzahl Gegner und gegnerische Karten verdeckt anzeigen
  -> Info, dass Du am Zug bist, Info wer überhaupt am Zug ist.
  -> Wunsch Button am besten als Popup, wenn Joker/+4 gelegt wird!
  -> neues Spiel hosten: Sauber Game-ID und JOIN-Link generieren
  -> Spiel verlassen - Socket und Game ggf. beenden, unnötige FSMB Beenden!
  -> Während dem Spiel joinen -> Ablehnen 
  -> Kartendeck leer? Deck neu mischen! 
  -> Spiel beenden - GameState liefern, Animation => Zurück auf Startseite
  
  -> Animationen
    -> Beim Hovern Karte vergrößert in den Vordergrund - Usability checken
    -> Karte legen, 
    -> Karten bekommen, 
    -> Gegner zieht Karte, 
    -> Gegner spielt Karte

- TODOS im Code 7.12.
- startup.sh entfernen, über docker-compose lauffähig kriegen
- Deployment vServer oder AWS? 


  -> Sonderregeln: +4 darf auf +4 gelegt werden, ergibt +8
  ->               +2          +2                       +4
  ->               Joker       Joker        

  -> (Spieler kicken (Votekick-System)?)