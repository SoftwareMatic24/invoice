@extends('layouts.portal')
@section('main-content')

<div class="data-table-container">

	<div class="data-table-toolbar sticky">
		<div class="data-table-toolbar-section">
			<ul class="data-table-tabs">
				<li class="active"><a href="#">Email Templates</a></li>
				<li><a href="{{ url('/portal/email-template/signatures') }}">Email Signature Templates</a></li>
			</ul>
		</div>
		<div class="data-table-toolbar-section search-section right">
			<input type="text" class="search input-style-1" placeholder="{{ __('search') }}">
			<svg class="icon search-icon">
				<use xlink:href="{{ asset('assets/icons.svg#search') }}" />
			</svg>
		</div>
	</div>

	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
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
	var pageTable = dataTable('page-table');

	async function fetchEmailTemplates() {
		let response = await xhrRequest({
			url: BASE_URL + '/api/email-template/all',
			method: 'GET'
		});

		let templates = response.data;

		let templateData = templates.map((template, templateIndex) => {

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
					value: [
						{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/email-template/save/' + template.id
						},
						
					]
				}
			];
		});

		pageTable.init(templateData);	
	}

	fetchEmailTemplates();
</script>
@parent
@stop