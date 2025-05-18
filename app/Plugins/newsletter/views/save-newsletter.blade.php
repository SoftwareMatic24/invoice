@extends('layouts.portal')

@section('main-content')

@inject('pluginController','App\Http\Controllers\PluginController')

<div class="grids main-sidebar-grids">
	<div class="grid">
		<form action="#" id="page-form">

			<div class="form-group">
				<div class="grids grids-2 gap-3">
					<div class="grid">
						<label class="input-style-1-label">Name</label>
						<input name="name" type="text" class="input-style-1">
					</div>

					<div class="grid"></div>
				</div>
			</div>

			<div class="form-group">
				<div class="grids grids-2 gap-3">
					<div class="grid">
						<label class="input-style-1-label">Email</label>
						<input name="email" type="text" class="input-style-1">
					</div>

					<div class="grid">
						<label class="input-style-1-label">Status</label>
						<select class="input-style-1" name="status">
							<option value="subscribed">Subscribed</option>
							<option value="unsubscribed">Unsubscribed</option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<button class="button button-sm button-primary">Save Record</button>
			</div>

		</form>
	</div>
	<div class="grid">
		<div class="grid-widget | margin-bottom-2">
			<p class="grid-widget-text status"><b>Subscription date</b>: <span data-is="subscription-date"></span></p>
			<p class="grid-widget-text url | margin-top-1"><b>Unsubscribed date</b>: <span data-is="unsubscription-date"></span></p>
		</div>
	</div>
</div>


@stop

@section('page-script')
<script>
	let newsletterId = '{{ $newsletterId ?? "" }}';
	let pageForm = document.querySelector('#page-form');

	pageForm.addEventListener('submit', async function() {
		event.preventDefault();

		let name = pageForm.querySelector('[name="name"]').value;
		let email = pageForm.querySelector('[name="email"]').value;
		let status = pageForm.querySelector('[name="status"]').value;


		let api = BASE_URL + '/api/newsletter/save';
		if(newsletterId !== '' && newsletterId !== null) api = BASE_URL + '/api/newsletter/save/' + newsletterId

		let n = Notification.show({
			text: 'Saving, please wait...',
			time: 0
		});

		let response = await xhrRequest({
			method: 'POST',
			url: api,
			body: {
				id: newsletterId,
				name: name,
				email: email,
				status: status
			}
		});


		if (response.data.status === 'success') {
			let newsletter = response.data.newsletter;

			newsletterId = newsletter.id;
			let subscriptionDate = toLocalDateTime(newsletter.create_datetime);
			let unsubscribeDate = toLocalDateTime(newsletter.unsubscribe_datetime !== undefined ? newsletter.unsubscribe_datetime : '');

			document.querySelector('[data-is="subscription-date"]').innerHTML = subscriptionDate;
			document.querySelector('[data-is="unsubscription-date"]').innerHTML = unsubscribeDate;
		}

		Notification.hideAndShowDelayed(n.data.id, {
			classes: [response.data.status],
			text: response.data.msg
		});
	});


	async function fetchNewsletter(newsletterId){
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/newsletter/one/' + newsletterId
		});

		let newsletter = response.data;
		let subscriptionDate = toLocalDateTime(newsletter.create_datetime);
		let unsubscribeDate = toLocalDateTime(newsletter.unsubscribe_datetime !== undefined ? newsletter.unsubscribe_datetime : '');

		
		document.querySelector('[name="name"]').value = newsletter.name;
		document.querySelector('[name="email"]').value = newsletter.email;
		document.querySelector('[name="status"]').value = newsletter.status;
		document.querySelector('[data-is="subscription-date"]').innerHTML = subscriptionDate;
		document.querySelector('[data-is="unsubscription-date"]').innerHTML = unsubscribeDate;

	}

	if(newsletterId !== '' && newsletterId !== null) fetchNewsletter(newsletterId);
</script>
@stop