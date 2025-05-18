@extends('layouts.portal')

@section('main-content')

<div class="cards cards-3" id="themes"></div>

@stop

@section('page-script')
	<script>
		
		async function fetchThemes(){

			let response = await xhrRequest({
				method: 'GET',
				url: BASE_URL + '/api/themes/all'
			});

			let themes = response.data;

			let layouts = themes.map(theme => {
				return `
					<div class="card item-preview-card | ${theme.status !== 'active' ? 'no-hover' : ''}">
						<div class="item-preview-card-body">
							<div class="item-preview-card-body-info | ${theme.status !== 'active' ? 'hide' : ''}">
								<a href="${PREFIXED_URL}/appearance/themes/customize/${theme.slug}" class="button button-xs button-warning">
									Customize Theme
								</a>
							</div>
							<img class="item-preview-card-img" src="${BASE_URL}/themes/${theme.slug}/resources/thumbnail.jpg" alt="${theme.title} thumbnail">
						</div>
						<div class="item-preview-card-footer">
							<p>${theme.title}</p>
							<button onclick="activateTheme(${theme.id})" class="button button-sm | ${theme.status === 'active' ? 'active button-primary' : 'button-primary-border'}">${theme.status === 'active' ? 'Activated' : 'Active'}</button>
						</div>
					</div>
				`;
			});

			document.querySelector('#themes').innerHTML = layouts.join('');
		}

		async function activateTheme(themeId){

			let n = Notification.show({
				text: 'Activating theme, please wait...',
				time: 0
			});

			let response = await xhrRequest({
				method: 'GET',
				url: BASE_URL + '/api/themes/activate/' + themeId
			});

			Notification.hideAndShowDelayed(n.data.id, {
				text: response.data.msg,
				classes: [response.data.status]
			});

			fetchThemes();
		}


		fetchThemes();
	</script>
@stop