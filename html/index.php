<?php 

require_once '../vendor/autoload.php';

?>

<html>
    <head>
        <title>BUMO</title>
    </head>
    <body>

        <div id="root"></div>
        <script>
            function connect() {
                var gameId = document.getElementById('gameId').value;
                var host = 'ws://192.168.0.251:12345/clientSocket.php?gameId=' + gameId;
                var socket = new WebSocket(host);
                socket.onmessage = function(e) {
                    document.getElementById('root').innerHTML = e.data;
                };
            }
            
        </script>
        <input type="text" id="gameId" />
        <input type="button" onClick="javascript:connect();" />
    </body>


</html>