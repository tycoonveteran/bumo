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

        <script src="/jquery.min.js"></script>
        <script src="/socket.io-client/socket.io.js"></script>
        <script src="/main.js"></script>

        <textarea id="log"></textarea>
    </body>

</html>