$(function() {

  var connected = false;
  var socket = io('http://192.168.0.251:2020');
  var playerId = "";
  var lastGameState = 1;

  // Whenever the server emits 'new message', update the chat body
  socket.on('game id', function (data) {
    log(data);
    $('#root').html(data);
  });

  socket.on('yourUserId', function (data) {
    log(data);
    playerId = data.userId;

    $('#create').parent().append('Teile folgenden Link, damit die Leute joinen können! ' +
      window.location.href + '/?gameId=' + data.gameId
    );

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
    if (gameState.currentGameState == 2) {
      if (lastGameState != 2) {
        // Spiel hat gerade begonnen!
        
        // Initialisieren der Gegner
        var playerIndex = 1;
        $.each(gameState.players, function( playerId, player ) {
          createPlayerElements (playerIndex, playerId, player);

          playerIndex++;
        });

        $('#echtesFrontend').show();
      }
      replaceCardDeck(gameState.topCardStaple);

      $.each(gameState.players, function( playerId, player ) {
        replacePlayerCards(playerId, gameState.players[playerId].cards);
      });

      $('.playerName').parent().css("background-color", "");

      var element = $(document).find(`.playerName[data-playerId='${gameState.nextPlayerId}']`).parent();
      $(element).css("background-color", "yellow");
    }

    if (gameState.currentGameState == 3) {
      $('#cardDeck').html ("Spiel vorbei!");
      $('.playerDeck').html("");
    }

    lastGameState = gameState.currentGameState;
  });

  function createPlayerElements(playerIndex, playerId, player) {
    $('#deckPlayer'+playerIndex).attr("data-playerId", playerId);
    $('#namePlayer'+playerIndex).attr("data-playerId", playerId).html(player.name);
    
    replacePlayerCards (playerId, player.cards);
  }

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

  function replacePlayerCards(playerId, cards)
  {
    var element = $(document).find(`.playerDeck[data-playerId='${playerId}']`);

    $(element).html("");
    var zIndex = 0;
    $.each(cards, function( index, card ) {
      var elementClass = 'class="playable" ';
      var tag = 'data-cardindex="'+zIndex+'" '
      var style = 'style="';
      if (zIndex > 0) {
        style += 'margin-left: -120px; ';
      }
      style += 'z-index: '+zIndex+';" ';
      var src = 'src="images/cards/'+getCardImageFile(card)+'.png"';
      $(element).append('<img '+tag+elementClass+style+src+'>');
      zIndex++;
    });
  }

  function getCardImageFile(card)
  {
    if (card == "Cover") {
      return "Cover";
    }
    // Format: FARBE:WERT
    const strArray = card.split(":");
    return strArray[0] + '_' + strArray[1];
  }

});
