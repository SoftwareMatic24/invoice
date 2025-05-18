var StripePlugin = function () { };

StripePlugin.prototype.captureIntent = async function (publicKey, postData) {

	let response = await xhrRequest({
		method: 'POST',
		url: BASE_URL + '/api/stripe/intent/create',
		body: postData
	});

	return response;
}


var stripePlugin = new StripePlugin();