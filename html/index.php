<?php 





?>

<html>
    <head>
        <title>BUMO</title>
    </head>
    <body>

        <div id="root"></div>
        <script>
            var host = 'ws://192.168.0.251:12345/clientSocket.php';
            var socket = new WebSocket(host);
            socket.onmessage = function(e) {
                document.getElementById('root').innerHTML = e.data;
            };
        </script>

    </body>


</html>