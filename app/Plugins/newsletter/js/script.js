var NewsletterPlugin = function () { };

NewsletterPlugin.prototype.subscribe = async function (data) {
	let n = Notification.show({
		text: 'Subscribing to newsletter...',
		time: 0
	});

	let response = await xhrRequest({
		method: 'POST',
		url: BASE_URL + '/api/newsletter/save',
		body: data
	});

	Notification.hideAndShowDelayed(n.data.id, {
		text: response.data.msg,
		classes: [response.data.status]
	});
};

var newsletterPlugin = new NewsletterPlugin();