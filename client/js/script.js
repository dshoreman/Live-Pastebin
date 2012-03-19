var Server;

function chat (msg, chat_window) {
	var chatbox = $(chat_window);
	chatbox.append((chatbox.val() ? "\n" : '') + msg);

	chatbox.scrollTop(chatbox.height());
}

function send (data, destination) {
	Server.send('message', '{"data":"' + data + '","dest":"' + destination + '"}');
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
	var code_box = '#codeBox';

	function JSONtoHTML (JSON) {
		if (typeof JSON == 'string') var data = $.parseJSON(JSON);
		else if (typeof JSON == 'object') var data = JSON;
		else return false;
		
		return '<blockquote><p>' + data.msg + '</p>\n<small>' + data.author + '</small></blockquote>';
	}

	// Make the connection
	setStatus('connecting');
	console.log('INFO', 'Connecting...');
	Server = new FancyWebSocket('ws://127.0.0.1:9300');

	// Process the user's chat messages
	$(chat_form).submit(function (e) {
		e.preventDefault();

		var msg = $(chat_input).val();
		if ($.trim(msg) != '') {
			chat(JSONtoHTML({"author":"You","msg":msg}), chat_window);
			send(msg, 'chatWindow');
		}
		$(chat_input).val('');
	});

	// Keep the code box sync'd
	$(code_box).keyup(function (e) {
		send($(code_box).val(), 'codeBox');
	});

	// Socket open - we're in!
	Server.bind('open', function () {
		setStatus('connected');
		console.log('INFO', 'Connected.');
		connected = true;

		if (retries > 0) {
			console.log('INFO', 'Resetting retry count...');
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
					console.log('INFO,' 'Reconnecting (attempt ' + retries + ' of ' + max_retries + ')...');

					setTimeout(function () {
						Server.connect();
					}, retry_time);
				}
				else {
					console.log('INFO', 'Max retries reached, aborting.');
					setStatus('disconnected');
					connected = false;
				}
			}, 500);
		}
		else {
			setTimeout(function () {
				setStatus('disconnected');
				console.log('INFO', 'Disconnected.');
			}, 1500);
		}
	});

	// Log stuff the server sends us
	Server.bind('message', function (payload) {
		// We want to parse the JSON here so we can decide what to do based on code/chat
		var data = $.parseJSON(payload)
		switch (data.dest) {
			case 'codeBox': $(code_box).val(data.data); break;
			case 'chatWindow': chat(JSONtoHTML(data), chat_window); break;
		}
	});

	Server.connect();
});