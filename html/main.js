$(function() {

  var connected = false;
  var socket = io('http://192.168.0.251:2020');
  
  // Sends a chat message
  /*function sendMessage ($inputMessage) {
    var message = $inputMessage.val();
    // Prevent markup from being injected into the message
    message = cleanInput(message);
    // if there is a non-empty message and a socket connection
    if (message && connected) {
      $inputMessage.val('');
      addChatMessage({
        username: username,
        message: message
      });
      // tell server to execute 'new message' and send along one parameter
      socket.emit('new message', message);
    }
  }*/

  // Prevents input from having injected markup
  function cleanInput (input) {
    return $('<div/>').text(input).text();
  }

  // Whenever the server emits 'new message', update the chat body
  socket.on('game id', function (data) {
    $('#root').html(data);
  });

  $('#connect').click(function() {
    socket.emit ('join', $('#gameId').val());
  });

  $('#showGames').click(function() {
    socket.emit ('getGameList');
  });

  socket.on('getGameListResponse', function(data) {
    $('#root').append ('<ul>');
    $(data).each (function(value) {
      $('#root').append('<li>'+ value + '</li>');
    });
    $('#root').append('</ul>');
  });

});
