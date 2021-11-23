$(function() {

  var connected = false;
  var socket = io('http://192.168.0.251:2020');
  var playerId = "";

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
    log(data);
    $('#root').html(data);
  });

  socket.on('yourUserId', function (data) {
    log(data);
    playerId = data.userId;
  });

  $('#create').click(function() {
    socket.emit ('create', $('#username').val());
  });

  $('#join').click(function() {
    socket.emit ('join', [$('#username').val(), $('#gameId').val()]);
  });

  $('#run').click(function() {
    socket.emit ('run', [$('#username').val(), $('#gameId').val()]);
  });

  $('#playCard').click(function() {
    socket.emit ('playCard', [
      $('#username').val(), 
      $('#cardIndex').val(), 
      $('#colorWish').val()
    ]);
  });

  $('#pullCard').click(function() {
    socket.emit ('pullCard', [$('#username').val()]);
  });

  socket.on('NewGame', function(data) {
    log(data);
  });

  socket.on('gameState', function(data) {
    log(data);

    var gameState = JSON.parse(data.gameController);
    replaceCardDeck(gameState.topCardStaple);
    replacePlayerCards(gameState.players[playerId].cards);
  });

  function log (data) {
    $('#log').html($('#log').html() + "\n" + JSON.stringify(data));
  }

  function replaceCardDeck(card) 
  {
    $('#cardDeck').html ('<img src="images/cards/'+getCardImageFile(card)+'.png">');
  }

  $(document.body).on('click', '.playable' ,function(){
    socket.emit ('playCard', [
      $('#username').val(),
      $(this).data('cardindex'),
      $('#colorWish').val()
    ]);
  });

  function replacePlayerCards(cards)
  {
    $('#deckPlayer1').html("");
    var zIndex = 0;
    $.each(cards, function( index, card ) {
      var elementClass = 'class="playable" ';
      var tag = 'data-cardindex="'+zIndex+'" '
      var style = 'style="';
      if (zIndex > 0) {
        style += 'position: absolute; ';
      }
      style += 'left: '+(330+zIndex*30)+'px; z-index: '+zIndex+';" ';
      var src = 'src="images/cards/'+getCardImageFile(card)+'.png"';
      $('#deckPlayer1').append('<img '+tag+elementClass+style+src+'>');
      zIndex++;
    });
  }

  function getCardImageFile(card)
  {
    // Format: FARBE:WERT
    const strArray = card.split(":");
    return strArray[0] + '_' + strArray[1];
  }

});
