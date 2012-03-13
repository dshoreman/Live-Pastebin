<?php
set_time_limit(0);
@date_default_timezone_set('Europe/London');
require 'class.PHPWebSocket.php';

// when a client sends data to the server
function wsOnMessage($clientID, $message, $messageLength, $binary) {
	global $Server;
	$ip = long2ip($Server->wsClients[$clientID][6]);

	if ($messageLength == 0) {
		$Server->wsClose($clientID);
		return;
	}

	//The speaker is the only person in the room. Don't let them feel lonely.
	if (sizeof($Server->wsClients) == 1)
		$Server->wsSend($clientID, 'There\'s nobody here, fool!');
	else {
		//Send the message to everyone but the person who said it
		foreach ( $Server->wsClients as $id => $client ) {
			if ($id == $clientID) continue;
			$Server->wsSend($id, 'Visitor ' . $clientID . ' (' . $ip . ') said "' . $message . '"');
		}
	}
}

// when a client connects
function wsOnOpen($clientID) {
	global $Server;
	$ip = long2ip($Server->wsClients[$clientID][6]);

	$Server->log($ip . ' (' . $clientID . ') has connected.');

	//Send a join notice to everyone but the person who joined
	foreach ( $Server->wsClients as $id => $client ) {
		if ($id == $clientID) continue;
		$Server->wsSend($id, 'Visitor ' . $clientID . ' (' . $ip . ') has joined the room.');
	}
}

// when a client closes or lost connection
function wsOnClose($clientID, $status) {
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	$Server->log($ip . ' (' . $clientID . ') has disconnected.');

	foreach ( $Server->wsClients as $id => $client ) {
		$Server->wsSend($id, 'Visitor ' . $clientID . ' (' . $ip . ') has left the room.');
	}
}

// start the server
$Server = new PHPWebSocket();
$Server->log('Starting pasted...');
$Server->bind('message', 'wsOnMessage');
$Server->bind('open', 'wsOnOpen');
$Server->bind('close', 'wsOnClose');

$Server->wsStartServer('127.0.0.1', 9300);