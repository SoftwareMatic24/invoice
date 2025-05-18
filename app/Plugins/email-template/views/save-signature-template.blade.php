@extends('layouts.portal')
@section('page-style')
<style>
	.ck-editor__editable {

		min-height: 300px !important;
	}
</style>
@stop

@section('main-content')

@inject('pluginController','App\Http\Controllers\PluginController')

<div class="grids main-sidebar-grids">
	<div class="grid">
		<form action="#" id="page-form" onsubmit="return false;">
			<div class="form-group">
				<label class="input-style-1-label">{{ __("title") }}</label>
				<input name="title" type="text" class="input-style-1">
			</div>
			<div class="form-group">
				<label class="input-style-1-label">Signature Template</label>
				<textarea name="content" cols="30" rows="17" class="input-style-1"></textarea>
			</div>
		</form>
	</div>
	<div class="grid | flex-column-reverse-on-md">
		<div class="grid-widget | margin-bottom-2">
			<div class="button-group">
				<button data-is="publish-button" onclick="saveTemplate()" class="button button-sm button-primary button-block">Save Signature</button>
			</div>
		</div>
	</div>
</div>

@stop

@section('page-script')
<script>
	let signatureId = '{{ $signatureId ?? "" }}';

	async function fap(){
		let signature = await fetchSignature();
		populateSignature(signature);
	}

	async function fetchSignature(){
		let response = await xhrRequest({
			method: 'GET',
			url: BASE_URL + '/api/email-template/signatures/one/' + signatureId
		});
		return response.data;
	}

	async function saveTemplate(){
		let titleEl = document.querySelector('[name="title"]');
		let contentEl = document.querySelector('[name="content"]');

		let postData = {
			title: titleEl.value,
			content: contentEl.value
		};

		let apis = {
			add: {
				method: 'POST',
				url: BASE_URL + '/api/email-template/signatures/save',
			},
			update: {
				method: 'PUT',
				url: BASE_URL + '/api/email-template/signatures/save/' + signatureId,
			}
		};

		let api = signatureId === '' ? apis.add : apis.update;
	
		let n = Notification.show({
			text: 'Saving, please wait...',
			time: 0
		});

		let response = await xhrRequest({
			method: api.method,
			url: api.url,
			body: postData
		});

		Notification.hideAndShowDelayed(n.data.id, {
			text: response.data.msg,
			classes: [response.data.status]
		});

		if(response.data.status === 'success') signatureId = response.data.signatureId;
	}

	function populateSignature(signature){
		if(signature === undefined || signature === '') return;
		let titleEl = document.querySelector('[name="title"]');
		let contentEl = document.querySelector('[name="content"]');
		titleEl.value = signature.title;
		contentEl.value = signature.content;	
	}

	function initHTMLEditor(content = null) {

		ClassicEditor.create(document.querySelector(`#html-content`), {
				licenseKey: '',
				initialData: content == null ? '' : content,
			})
			.then(editor => {
				contentEditors.push(editor);
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
				console.warn('Build id: htu0gx4ou56h-hrgzwh179cfe');
				console.error(error);
			});
	}

	if(signatureId !== '') fap();
</script>
@stop