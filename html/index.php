<?php 

require_once '../vendor/autoload.php';

?>

<html>
    <head>
        <title>BUMO</title>
        
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
        <input type="button" id="playCard" value="Karte spielen" />
        <hr>
        <script src="/scripts/jquery.min.js"></script>
        <script src="/scripts/socket.io-client/socket.io.js"></script>
        <script src="/scripts/main.js"></script>

        <textarea id="log"></textarea>
    </body>

</html>