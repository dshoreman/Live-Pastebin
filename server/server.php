<?php
set_time_limit(0);
@date_default_timezone_set('Europe/London');

$server_host = '127.0.0.1';
$server_port = 9300;

require 'class.PHPWebSocket.php';

// when a client sends data to the server
function wsOnMessage($clientID, $message, $messageLength, $binary) {
	global $Server;
	$ip = long2ip($Server->wsClients[$clientID][6]);

	if ($messageLength == 0) {
		$Server->wsClose($clientID);
		return;
	}

	$message_o = json_decode($message);
	switch ($message_o->dest) {
		case 'codeBox': sendForAllButMe($clientID, $message); break;
		case 'chatWindow': {
			//The speaker is the only person in the room. Don't let them feel lonely.
			if (sizeof($Server->wsClients) == 1) {
				if ($Server->wsClients[$clientID]['data']['seenLonelyMsg'] === false) {
					$Server->wsSend($clientID, json_encode(array(
						'dest' => $message_o->chatWindow,
						'author' => 'The Autobots',
						'msg' => 'There\'s nobody here, fool!'
					)));
					$Server->wsClients[$clientID]['data']['seenLonelyMsg'] = true;
				}
			}
			else {
				sendForAllButMe($clientID, json_encode(array(
					'dest' => $message_o->dest,
					'author' => 'User ' . $clientID,
					'msg' => $message_o->data
				)));
			}
			break;
		}
	}
}

// when a client connects
function wsOnOpen($clientID) {
	global $Server;
	$ip = long2ip($Server->wsClients[$clientID][6]);

	$Server->log($ip . ' (' . $clientID . ') has connected.');
	$Server->wsClients[$clientID]['data'] = array('seenLonelyMsg' => false);

	//Send a join notice to everyone but the person who joined
	foreach ( $Server->wsClients as $id => $client ) {
		if ($id == $clientID) continue;
		$Server->wsSend($id, json_encode(array(
			'dest' => 'chatWindow',
			'author' => 'The Autobots',
			'msg' => 'User ' . $clientID . ' (' . $ip . ') has joined the room.'
		)));
	}
}

// when a client closes or lost connection
function wsOnClose($clientID, $status) {
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	$Server->log($ip . ' (' . $clientID . ') has disconnected.');

	foreach ( $Server->wsClients as $id => $client ) {
		$Server->wsSend($id, json_encode(array(
			'dest' => 'chatWindow',
			'author' => 'The Autobots',
			'msg' => 'User ' . $clientID . ' (' . $ip . ') has left the room.'
		)));
	}
}

// Send a message to everyone except the one sending it
function sendForAllButMe ($me, $data) {
	global $Server;

	if (sizeof($Server->wsClients) > 1) {
		foreach ($Server->wsClients as $id => $client) {
			if ($id == $me) continue;
			$Server->wsSend($id, $data);
		}
	}
}

// start the server
$Server = new PHPWebSocket();
$Server->log('Starting pasted...');
$Server->bind('message', 'wsOnMessage');
$Server->bind('open', 'wsOnOpen');
$Server->bind('close', 'wsOnClose');

$Server->wsStartServer($server_host, $server_port);