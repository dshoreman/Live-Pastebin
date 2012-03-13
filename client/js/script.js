var Server;

function log (text) {
	console.log(text);
}

function chat (msg, chat_window) {
	var chatbox = $(chat_window);
	chatbox.append((chatbox.val() ? "\n" : '') + msg);

	chatbox.scrollTop(chatbox.height());
}

function send (text) {
	Server.send('message', text);
}

function setStatus (status, msg) {
	switch (status) {
		case 'connecting': {
			var cls = 'info';
			break;
		}
		case 'connected': {
			var cls = 'success';
			break;
		}
		case 'connection lost': {
			var cls = 'warning';
			break;
		}
		case 'disconnected': {
			var cls = 'danger';
			break;
		}
	}

	var btn = $('#socket_status');
	btn.removeClass('btn-info btn-success btn-danger');

	if (cls != undefined) {
		if (msg != undefined) status = msg;
		btn.text(status).addClass('btn-' + cls);
	}
}

$(document).ready(function () {

	// Define some variables
	var retry_time = 2500;
	var retries = 0;
	var max_retries = 3;
	var connected = false;
	var chat_window = '#chatWindow';
	var chat_form = '#chatForm';
	var chat_input = '#chatForm input';

	function JSONtoHTML (JSON) {
		if (typeof JSON == 'string') var data = $.parseJSON(JSON);
		else if (typeof JSON == 'object') var data = JSON;
		else return false;
		
		return '<blockquote><p>' + data.msg + '</p>\n<small>' + data.author + '</small></blockquote>';
	}

	// Make the connection
	setStatus('connecting');
	log('Connecting...');
	Server = new FancyWebSocket('ws://127.0.0.1:9300');

	// Process the user's chat messages
	$(chat_form).submit(function (e) {
		e.preventDefault();

		var msg = $(chat_input).val();

		chat(JSONtoHTML({"author":"You","msg":msg}), chat_window);
		send(msg);

		$(chat_input).val('');
	});

	// Socket open - we're in!
	Server.bind('open', function () {
		setStatus('connected');
		log('Connected.');
		connected = true;

		if (retries > 0) {
			log('Resetting retry count...');
			retries = 0;
		}

		chat(JSONtoHTML({author: 'The Autobots', msg: 'Enter a message below to chat.'}), chat_window);
	});

	// Aww they broke it :(
	Server.bind('close', function (data) {
		if (connected) {
			setStatus('connection lost');
			setTimeout(function () {
				retries++;
				if (retries <= max_retries) {
					setStatus('connecting', 'reconnecting (attempt ' + retries + ' of ' + max_retries + ')...');
					log('reconnecting (attempt ' + retries + ' of ' + max_retries + ')...');

					setTimeout(function () {
						Server.connect();
					}, retry_time);
				}
				else {
					log('max retries reached, aborting.');
					setStatus('disconnected');
					connected = false;
				}
			}, 500);
		}
		else {
			setTimeout(function () {
				setStatus('disconnected');
				log('Disconnected.');
			}, 1500);
		}
	});

	// Log stuff the server sends us
	Server.bind('message', function (payload) {
		chat(JSONtoHTML(payload), chat_window);
	});

	Server.connect();
});