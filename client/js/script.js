function log (text) {
	console.log(text);
}

function setStatus (status) {
	switch (status) {
		case 'connecting': {
			var cls = 'info';
			break;
		}
		case 'connected': {
			var cls = 'success';
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
		btn.text(status).addClass('btn-' + cls);
	}
}

$(document).ready(function () {
	setStatus('connecting');
	log('Connecting...');
	var Server = new FancyWebSocket('ws://127.0.0.1:9300');

	// Socket open - we're in!
	Server.bind('open', function () {
		setStatus('connected');
		log('Connected.');
	});

	// Aww they broke it :(
	Server.bind('close', function (data) {
		setStatus('disconnected');
		log('Disconnected.');
	});

	// Log stuff the server sends us
	Server.bind('message', function (payload) {
		log(payload);
	});

	Server.connect();
});