@extends('layouts.portal')

@inject('pluginController','App\Http\Controllers\PluginController')
@inject('notification','App\Models\Notification')

@section('main-content')

<ul class="list notification-list" id="notifications"></ul>

@stop

@section("page-script")

<script>
	async function fetchNotifications(){
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/notifications/my'
		});

		let notifications = response.data;
		
		let allNotifications = notifications.userNotifications.concat(notifications.roleNotifications);
		let data = notificationsLayout(allNotifications);

		let layouts = data.layouts;

		document.querySelector('#notifications').innerHTML = layouts.join('');

	}

	fetchNotifications();
</script>

@parent
@stop