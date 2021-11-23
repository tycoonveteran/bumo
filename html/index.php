<?php 

require_once '../vendor/autoload.php';

?>

<html>
    <head>
        <title>BUMO</title>
        <style>
            img {
                width: 150px;
            }
            tr, table {
                width: 100%;
            }
            td {
                width: 33%;
                height: 205px;
            }
            .playable {
                cursor: hand;
            }
        </style>
    </head>
    <body>

        <input type="text" id="username" placeholder="Dein Name" />
        <input type="button" id="create" value="Neues Spiel hosten" />
        <hr>        
        <input type="text" id="gameId" placeholder="Spiel-ID"/>
        <input type="button" id="join" value="Spiel beitreten" />
        <hr>
        <input type="button" id="run" value="Spiel starten" />
        <hr>
        <input type="text" id="cardIndex" placeholder="Karten-Index"/>
        Wunsch-Farbe: 
        <select id="colorWish">
            <option value="1">Rot</option>
            <option value="2">Blau</option>
            <option value="3">Grün</option>
            <option value="4">Gelb</option>
        </select>
        <input type="button" id="playCard" value="Karte spielen" />
        <input type="button" id="pullCard" value="Karte ziehen" />
        <hr>
        <script src="/scripts/jquery.min.js"></script>
        <script src="/scripts/socket.io-client/socket.io.js"></script>
        <script src="/scripts/main.js"></script>

        <textarea id="log"></textarea>
        <hr>

        <div style="display: block; background-color: #00aa00;" id="echtesFronted">
            <table>
                <tr>
                    <td></td>
                    <td>Gegner
                        <div id="deckPlayer2">
                            <img style="left: 330px; z-index: 0;" src="images/cards/Cover.png" />
                            <img style="position: absolute; left: 360px; z-index: 1;" src="images/cards/Cover.png" />
                            <img style="position: absolute; left: 390px; z-index: 2;" src="images/cards/Cover.png" />
                        </div>
                    </td>
                    <td></td>
                </tr>
                <tr style="height: 300px;">
                    <td></td>
                    <td>Deck
                        <div id="cardDeck">
                            <img src="images/cards/Cover.png" />
                        </div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Du
                        <div id="deckPlayer1">
                            <img style="left: 330px; z-index: 0;" src="images/cards/Gelb_+2.png" />
                            <img style="position: absolute; left: 360px; z-index: 1;" src="images/cards/Blau_7.png" />
                            <img style="position: absolute; left: 390px; z-index: 2;" src="images/cards/Blau_5.png" />
                        </div>
                    </td>
                    <td></td>
                </tr>
            </table>
        </div>

    </body>

</html>