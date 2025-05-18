<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<title>License Key</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
			background-color: #f0efeb;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.box {
			background-color: #fff;
			width: 100%;
			min-height: 300px;
			max-width: min(450px, 90%);
			padding: 30px;
			border-radius: 6px;
			margin-top: 100px;
			box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 44px 0px;
		}

		.box h1 {
			font-size: 30px;
			text-align: center;
			color: #343a40;
		}

		.box form {
			margin-top: 50px;
			width: 100%;
		}

		.form-group label {
			font-size: 18px;
			font-weight: 600;
			color: #343a40;
			display: block;
			margin-bottom: 10px;
		}

		form .form-group {
			margin-bottom: 30px;
		}

		.input-style-1 {
			font-size: 16px;
			width: 100%;
			padding: 16px 20px;
			border-radius: 4px;
			border: 2px solid #EEEEFF;
		}

		.button {
			font-family: inherit;
			font-size: 18px;
			font-weight: 600;
			color: #fff;
			background-color: #445E93;
			padding: 14px 24px;
			border: 3px solid transparent;
			border-radius: 4px;
			cursor: pointer;
		}

		.button:focus {
			border-color: #274c7749;
		}

		.button-block {
			width: 100%;
		}

		.button[disabled] {
			background-color: #445e933b;
			cursor: not-allowed;
		}

		.form-msg {
			display: none;
		}

		.form-msg.active {
			display: block;
		}

		.form-msg p {
			font-size: 17px;
			font-weight: 600;
			color: #343a40;
		}

		.form-msg b {
			font-weight: 700;
			text-decoration: underline;
		}

		.form-msg .success {
			color: #2c6e49;
		}

		.form-msg .fail {
			color: #ee6055;
		}
	</style>
</head>

<body>
	<div class="box">
		<h1>License Key Required</h1>
		<form action="#" id="form">
			<div class="form-group">
				<label for="#">Enter license key</label>
				<input name="license" type="text" class="input-style-1">
			</div>
			<div class="form-group form-msg">
				<p><b>Success!</b> Your product has been activated.</p>
			</div>
			<div class="form-group">
				<button class="button button-block">Activate Product</button>
			</div>
		</form>
	</div>
	<script>
		const APP_ID = '{{ env("APP_ID") }}';
		const BASE_URL = '{{ url("/") }}'
		let API = 'https://digitalmarketers24.com/lc/api/static-license/validate';

		let form = document.querySelector('#form');
		form.addEventListener('submit', async function() {
			event.preventDefault();
			LC_showMessage('default', 'Loading, please wait...');
			let response = await LC_xhrRequest({
				method: 'post',
				url: API,
				body: {
					license: document.querySelector('[name="license"]').value,
					domain: window.location.hostname
				}
			});
			LC_hideMessage();
			LC_showMessage(response.data.status, response.data.msg);
			if (response.data.status === 'success') {
				LC_showMessage('success', 'Activating product, please wait...');

				await LC_xhrRequest({
					url: BASE_URL + '/api/rs/laravel',
					method: 'POST',
					body: {
						appId: APP_ID,
						action: 'se'
					}
				});

				setTimeout(() => {
					window.location.href = BASE_URL;
				}, 3000);
			}
		});
		var xhrState = {
			start: null,
			complete: null,
			error: null,
			progress: null
		};

		function LC_xhrRequest(param) {
			let bt = null;
			let defaultHeaders = {
				'Content-Type': 'application/json',
				'X-Requested-With': 'XMLHttpRequest'
			};
			let {
				method = "GET", url = null, body = null, headers = defaultHeaders, withCredentials = null, stringify = null, progress = null
			} = param;
			let request = new XMLHttpRequest();
			request.open(method, url);
			if (withCredentials !== null) request.withCredentials = withCredentials;
			for (let key in headers) {
				request.setRequestHeader(key, headers[key]);
			}
			if (bt !== undefined && bt !== null && bt !== false) {
				request.setRequestHeader('Authorization', `Bearer ${bt}`);
			}
			return new Promise(function(resolve, reject) {
				try {
					// start
					request.upload.onloadstart = function(event) {
						LC_setXhrState("start", true);
					}
					// progress
					request.upload.onprogress = function(event) {
						let total = event.total;
						let loaded = event.loaded;
						let progressValue = (loaded / total) * 100;
						if (progress !== null) {
							let progressElement = document.querySelector(`[data-progress="${progress}"]`).setAttribute('style', `--progress:${progressValue}%`);
						}
						LC_setXhrState("progress", Math.ceil(progressValue), false)
					}
					// complete
					request.onload = function(event) {
						let responseText = event.target.responseText;
						if (progress !== null) {
							document.querySelector(`[data-progress="${progress}"]`).setAttribute('style', `--progress:0%`);
						}
						LC_setXhrState("complete", true);
						resolve({
							status: 'success',
							msg: 'completed',
							data: responseText !== '' ? JSON.parse(responseText) : responseText
						});
					}
					// abort
					request.onabort = function() {
						LC_setXhrState("error", true);
						resolve({
							status: 'fail',
							msg: 'abort'
						});
					}
					// error
					request.onerror = function(e) {
						LC_setXhrState("error", true);
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
					LC_setXhrState("error", true);
					resolve({
						status: 'fail',
						msg: 'error'
					});
				}
			});
		}

		function LC_setXhrState(statusKey, status, resetOther = true) {
			for (key in xhrState) {
				if (key === statusKey) xhrState[key] = status;
				else if (resetOther === true) xhrState[key] = null;
			}
			return xhrState;
		}

		function LC_showMessage(type = 'default', text = '') {
			let formMsg = document.querySelector('.form-msg');
			let p = document.querySelector('.form-msg p');
			formMsg.classList.add('active');
			p.classList.remove('default');
			p.classList.remove('success');
			p.classList.remove('fail');
			p.classList.add(type);
			p.innerHTML = text;
		}

		function LC_hideMessage() {
			let formMsg = document.querySelector('.form-msg');
			let p = document.querySelector('.form-msg p');
			form.classList.remove('active');
		}
	</script>
</body>

</html>