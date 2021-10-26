<?php 

require_once '../vendor/autoload.php';

?>

<html>
    <head>
        <title>BUMO</title>
        
    </head>
    <body>

        <div id="root"></div>

        <input type="button" id="connect" value="Connect" />
        <input type="button" id="showGames" value="Zeige Spiele" />
        <input type="text" id="gameId" />

        <script src="/jquery.min.js"></script>
        <script src="/socket.io-client/socket.io.js"></script>
        <script src="/main.js"></script>
    </body>

</html>