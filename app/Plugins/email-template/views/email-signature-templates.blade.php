@extends('layouts.portal')

@section('main-content')

@inject('pluginController','App\Http\Controllers\PluginController')
@inject('util','App\Classes\Util')
<div class="data-table-container">
	<div class="data-table-toolbar sticky">
		<div class="data-table-toolbar-section">
			<ul class="data-table-tabs">
				<li><a href="{{ $util->prefixedURL($pluginConfig['slug']) }}">Email Templates</a></li>
				<li class="active"><a href="#">Email Signature Templates</a></li>
			</ul>
		</div>
		<div class="data-table-toolbar-section search-section right">
			<div class="">
				<input type="text" class="search input-style-1" placeholder="Search">
				<svg class="icon search-icon">
					<use xlink:href="{{ asset('assets/icons.svg#search') }}" />
				</svg>
				<svg class="icon cross-icon">
					<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
				</svg>
			</div>
			<div class="data-table-toolbar-section-option">
				<a style="min-width: 23rem;" href="{{ $util->prefixedURL($pluginConfig['slug'].'/signatures/save') }}" class="button button-primary with-plus-icon">
					<span>New Signature Template</span>
				</a>
			</div>

		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>{{ __("title") }}</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
@stop

@section('page-script')

<script>
	let pageTable = dataTable('page-table');

	async function fap() {
		let signatures = await fetchSignatures();
		populateSignatures(signatures);
	}

	async function fetchSignatures() {
		let response = await xhrRequest({
			url: BASE_URL + '/api/email-template/signatures/all',
			method: 'GET'
		});

		return response.data;
	}

	async function deleteSignature(signatureId) {

		async function proceed() {

			let n = Notification.show({
				text: 'Deleting, please wait...',
				time: 0
			});

			let response = await xhrRequest({
				method: 'DELETE',
				url: BASE_URL + '/api/email-template/signatures/delete/' + signatureId
			});

			Notification.hideAndShowDelayed(n.data.id, {
				text: response.data.msg,
				classes: [response.data.status]
			});

			if(response.data.status === 'success') fap();
		}

		Confirmation.show({
			positiveButton: {
				function: function() {
					proceed();
				}
			}
		});
	}

	function populateSignatures(signatures) {

		if (signatures === undefined || signatures === '') return;

		let templateData = signatures.map((template, templateIndex) => {

			return [{
					type: 'text',
					value: (templateIndex + 1)
				},
				{
					type: 'text',
					value: template.title
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/email-template/signatures/save/' + template.id
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								click: function() {
									deleteSignature(template.id);
								}
							}
						},

					]
				}
			];
		});

		pageTable.init(templateData, {
			pageSize: 8,
			rowCountStats: true
		});

		if(pageTable.getOriginalData().length === 0){
			pageTable.init([
				[
					{
						type:'text',
						value: ''
					},
					{
						type:'text',
						value: ''
					},
					{
						type:'text',
						value: ''
					}
				]
			]);
		}

	}


	fap();
</script>
@parent
@stop