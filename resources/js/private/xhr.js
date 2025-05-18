let xhrState = {};

function xhrRequest(param) {
	LAST_BACKEND_INTERACTION_SINCE = 0;

	setCookie('client_timezone', getTimezoneName());
	let bt = getCookie('bt');
	bt = decodeURI(bt);

	let defaultHeaders = {
		'Content-Type': 'application/json',
		'X-Requested-With': 'XMLHttpRequest'
	};

	let {
		method = "GET",
		url = null,
		body = null,
		headers = defaultHeaders,
		withCredentials = null,
		stringify = null,
		progress = null,
		defaultAuhtorization = true, id = uid(),
		target = null
	} = param;


	let request = new XMLHttpRequest();
	request.open(method, url);

	if (withCredentials !== null) request.withCredentials = withCredentials;

	for (let key in headers) {
		request.setRequestHeader(key, headers[key]);
	}

	if (bt !== undefined && bt !== null && bt !== false && defaultAuhtorization === true) {
		request.setRequestHeader('Authorization', `Bearer ${bt}`);
	}

	return new Promise(function (resolve, reject) {

		try {
			// start
			request.upload.onloadstart = function (event) {
				setXhrState("start", true, id);
				xhrTargetOnLoadStart(target);
			}

			// progress
			request.upload.onprogress = function (event) {
				let total = event.total;
				let loaded = event.loaded;

				let progressValue = (loaded / total) * 100;

				if (progress !== null) {
					let progressElement = document.querySelector(`[data-progress="${progress}"]`).setAttribute('style', `--progress:${progressValue}%`);
				}

				setXhrState("progress", Math.ceil(progressValue), id);
			}

			// complete
			request.onload = function (event) {

				let responseText = event.target.responseText;
				let responseStatus = event.target.status;

				if (progress !== null) {
					document.querySelector(`[data-progress="${progress}"]`).setAttribute('style', `--progress:0%`);
				}

				setXhrState("complete", true, id);
				xhrTargteFinish(target);

				let responseData = responseText;

				try {
					responseData = responseText !== '' ? JSON.parse(responseText) : responseText;
					if (responseStatus === 500) throw new Error("500 server error");
				}
				catch (e) {
					//window.location.href = BASE_URL + '/error';
				}

				resolve({
					status: 'success',
					msg: 'completed',
					data: responseData
				});

			}

			// abort
			request.onabort = function () {

				setXhrState("error", true, id);
				xhrTargteFinish(target);

				resolve({
					status: 'fail',
					msg: 'abort'
				});
			}

			// error
			request.onerror = function (e) {
				setXhrState("error", true, id);
				xhrTargteFinish(target);

				resolve({
					status: 'fail',
					msg: 'error'
				});
			}

			if (stringify === undefined || stringify === null || stringify === true) {
				body = JSON.stringify(body);
			}

			if (body === null) request.send();
			else request.send(body);

		} catch (err) {
			setXhrState("error", true);
			resolve({
				status: 'fail',
				msg: 'error'
			});
		}

	});

}

function setXhrState(statusKey, status, id) {
	if (xhrState[id] === undefined) xhrState[id] = {
		start: null,
		progress: null,
		complete: null,
		error: null
	};
	xhrState[id][statusKey] = status;
	return xhrState;
}

function xhrTargetOnLoadStart(target) {
	let els = document.querySelectorAll(`[data-xhr-name="${target}"]`);
	els.forEach(el => {
		let classStr = el.getAttribute('data-xhr-loading.class');
		let attrStr = el.getAttribute('data-xhr-loading.attr');

		if (!isEmpty(classStr)) el.classList.add(classStr.split(' '));
		if (!isEmpty(attrStr)) attrStr.split(' ').forEach(attr => el.setAttribute(attr, ''));
	})
}

function xhrTargteFinish(target) {
	let els = document.querySelectorAll(`[data-xhr-name="${target}"]`);
	els.forEach(el => {
		let classStr = el.getAttribute('data-xhr-loading.class');
		let attrStr = el.getAttribute('data-xhr-loading.attr');

		if (!isEmpty(classStr)) el.classList.remove(classStr.split(' '));
		if (!isEmpty(attrStr)) attrStr.split(' ').forEach(attr => el.removeAttribute(attr));
	})
}