@extends('layouts.portal')
@inject('pluginController','App\Http\Controllers\PluginController')

@section('main-content')

{{ loadPluginFile('css/style.css', 'social-media') }}


<div class="grids main-sidebar-grids">
	<div class="grid">
		<div class="social-media-links-header">
			<div>
				<p>Check icon documentation for icon names: <a href="https://ionic.io/ionicons" target="_blank">read now</a></p>
			</div>
		</div>

		<div id="social-links-list" class="social-media-links-list | margin-top-4"></div>

		<div class="social-media-links-footer | margin-top-4">
			<div>
				<button onclick="addNewLink()" class="button button-primary">Add New Link</button>
			</div>
		</div>
	</div>
	<div class="grid">
		<div class="grid-widget">
			<button onclick="saveSocial()" class="button button-primary button-block">Save Social Links</button>
		</div>
	</div>
</div>


@stop

@section('page-script')

<script>
	
	function addNewLink(icon = '', url = ''){

		let view = `
			<div class="social-media-links-item">
				<div>
					<input name="icon" class="input-style-1" type="text" placeholder="Icon name" value="${icon}">
				</div>
				<div>
					<input name="url" class="input-style-1" type="text" placeholder="URL" value="${url}">
				</div>
				<div>
					<button onclick="removeSocialLink()" class="button button-sm button-danger-border">Remove</button>
				</div>
			</div>
		`;

		document.querySelector('#social-links-list').insertAdjacentHTML('beforeend', view);
	}

	function removeSocialLink(){
		let target = event.target;
		Confirmation.show({
			positiveButton: {
				function: function(){
					let container = target.closest('.social-media-links-item');
					container.remove();
				}
			}
		});
	}

	async function fetchSocialLinks(){

		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/social-media/social-links/all'
		});

		if(response.data.length <= 0) addNewLink();
	
		response.data.forEach(row => {
			addNewLink(row.icon, row.url);
		});

	}

	async function saveSocial(){

		let postData = [];
		let divs = document.querySelectorAll('#social-links-list .social-media-links-item');
		divs.forEach(div => {
			let icon = div.querySelector('input[name="icon"]').value;
			let url = div.querySelector('input[name="url"]').value;

			postData.push({icon, url});
		});

		
		let n = Notification.show({
			text: 'Saving, please wait...',
			time: 0
		});

		let response = await xhrRequest({
			method: 'POST',
			url: BASE_URL + '/api/social-media/social-links/save',
			body: postData
		});

		Notification.hideAndShowDelayed(n.data.id, {
			text: response.data.msg,
			classes: [response.data.status]
		});

	}


	fetchSocialLinks();

</script>

@parent
@stop