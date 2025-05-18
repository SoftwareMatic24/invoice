@extends('layouts.portal')

@section('page-style')
<style>
	.ck-editor__editable {

		min-height: 300px !important;
	}
</style>
@parent
@stop

@section('main-content')

@inject('pluginController','App\Http\Controllers\PluginController')

<div class="grids main-sidebar-grids">
	<div class="grid">
		<form action="#" id="page-form" onsubmit="return false;">
			<div class="form-group">
				<div class="grids grids-2 gap-2">
					<div class="grid">
						<label class="input-style-1-label">{{ __("title") }}</label>
						<input name="title" type="text" class="input-style-1" disabled>
					</div>
					<div class="grid">
						<label class="input-style-1-label">{{ __("subject") }}</label>
						<input name="subject" type="text" class="input-style-1">
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="input-style-1-label">Email Content</label>
				<textarea name="content" cols="30" rows="17" class="input-style-1"></textarea>
			</div>
			<div class="form-group">
				<div class="grids grids-2 gap-3">
					<div class="grid">
						<div class="select-container chevron">
							<select name="signature" class="input-style-1">
								<option value="">Choose Signature</option>
								@foreach($signatures as $signature)
								<option value="{{ $signature['id'] }}">{{ $signature["title"] }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="grid"></div>
				</div>
			</div>
		</form>
	</div>
	<div class="grid | flex-column-reverse-on-md">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button data-is="publish-button" onclick="saveTemplate()" class="button button-sm button-primary button-block">{{ __("save template") }}</button>
			</div>
		</div>
		<div class="grid-widget | margin-bottom-2">
			<p class="grid-widget-text"><b>Website name:</b> [app-name]</p>
			<p class="grid-widget-text | margin-top-1"><b>Website link:</b> [website-link]</p>
			<p class="grid-widget-text | margin-top-1"><b>Website link with text:</b> [website-link:Text]</p>
			<p class="grid-widget-text | margin-top-1"><b>Custom link:</b> [link:Text->your-link]</p>
			<div id="instructions"></div>
		</div>
	</div>
</div>

@stop

@section('page-script')
<script src="{{ asset('js/ckeditor.js') }}"></script>
<script>
	let emailTemplateId = '{{ $emailTemplateId ?? "" }}';
	

	async function saveTemplate() {
		let subjectEl = document.querySelector('[name="subject"]');
		let signatureEl = document.querySelector('[name="signature"]');
		let contentEl = document.querySelector('[name="content"]');

		let n = Notification.show({
			text: notificationTexts.saving,
			time: 0
		});

		let response = await xhrRequest({
			method: 'PUT',
			url: BASE_URL + '/api/email-template/save/' + emailTemplateId,
			body: {
				subject: subjectEl.value,
				signature:signatureEl.value,
				data: contentEl.value
			}
		});

		Notification.hideAndShowDelayed(n.data.id, {
			text: response.data.msg,
			classes: [response.data.status]
		});
	}

	async function fetchTemplate(emailTemplateId) {
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/email-template/one/' + emailTemplateId
		});

		if (response.data === '') return;
		populateTemplate(response.data);
	}

	function populateTemplate(template) {
		let titleEl = document.querySelector('[name="title"]');
		let subjectEl = document.querySelector('[name="subject"]');
		let contentEl = document.querySelector('[name="content"]');
		let signatureEl = document.querySelector('[name="signature"]');

		titleEl.value = template.title;
		subjectEl.value = template.subject;
		contentEl.value = template.content;
		populateInstructions(template.instructions);

		if(template.email_signature_id !== null) signatureEl.value = template.email_signature_id;
	}

	function populateInstructions(instructions) {
		if (instructions === undefined || instructions === null || instructions === '') return;
		instructions = JSON.parse(instructions);
		let instructionsViews = instructions.map(i => {
			let chunks = i.toString().split(': [');
			if (chunks.length > 0) return `<p class="grid-widget-text | margin-top-1"><b>${chunks[0]}:</b> [${chunks[1]}</p>`;
			else return `<p class="grid-widget-text | margin-top-1">${i}</p>`;
		});

		document.querySelector('#instructions').innerHTML = instructionsViews.join('');
	}

	if (emailTemplateId !== '') fetchTemplate(emailTemplateId);

</script>

@stop