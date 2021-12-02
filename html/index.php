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

            a.button1 {
                display: inline-block;
                padding: 0.35em 1.2em;
                border: 0.1em solid #FFFFFF;
                margin: 0 0.3em 0.3em 0;
                border-radius: 0.12em;
                box-sizing: border-box;
                text-decoration: none;
                font-family: 'Roboto',sans-serif;
                font-weight: 300;
                color: #FFFFFF;
                text-align: center;
                transition: all 0.2s;
            }

            a.button1:hover{
                color: #000000;
                background-color: #FFFFFF;
            }

            @media all and (max-width:30em) {
                a.button1{
                    display: block;
                    margin: 0.4em auto;
                }
            }
        </style>
    </head>
    <body>

        <input type="text" id="username" placeholder="Dein Name" />
        <?php 
        if (!isset($_GET['gameId'])) {
            print '<input type="button" id="create" value="Neues Spiel hosten" />';
        } else {
            print '
            <input type="text" id="gameId" placeholder="Spiel-ID" readonly="true" value="'.$_GET['gameId'].'"/>
            <input type="button" id="join" value="Spiel beitreten" />';
        }
        ?>
        
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
        
        <hr>
        <script src="/scripts/jquery.min.js"></script>
        <script src="/scripts/socket.io-client/socket.io.js"></script>
        <script src="/scripts/main.js"></script>

        <textarea id="log"></textarea>
        <hr>

        <div style="display: none; background-size: cover; background-image: url(images/backgrounds/bumo_background_green_3_logo.jpg);" id="echtesFrontend">
            <table>
                <tr>
                    <td><span id="namePlayer3" class="playerName"></span>
                        <div id="deckPlayer3" class="playerDeck">
                        </div>
                    </td>
                    <td><span id="namePlayer4" class="playerName"></span>
                        <div id="deckPlayer4" class="playerDeck">
                        </div>
                    </td>
                    <td><span id="namePlayer5" class="playerName"></span>
                        <div id="deckPlayer5" class="playerDeck">
                        </div></td>
                </tr>
                <tr style="height: 300px;">
                    <td><span id="namePlayer2" class="playerName"></span>
                        <div id="deckPlayer2" class="playerDeck">
                        </div>
                    </td>
                    <td>Deck
                        <div id="cardDeck">
                            <img src="images/cards/Cover.png" />
                        </div>
                    </td>
                    <td><span id="namePlayer6" class="playerName"></span>
                        <div id="deckPlayer6" class="playerDeck">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="javascript:void();" class="button1" id="pullCard">Karte ziehen</a>
                    </td>
                    <td><span id="namePlayer1" class="playerName">Du</span>
                        <div id="deckPlayer1" class="playerDeck">
                            
                        </div>
                    </td>
                    <td></td>
                </tr>
            </table>
        </div>

    </body>

</html>