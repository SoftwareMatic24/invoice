@extends('layouts.portal')
@section('main-content')
<div class="data-table-container">
	<div class="data-table-toolbar sticky">
		<div class="data-table-toolbar-section search-section">
			<input type="text" class="search input-style-1" placeholder="{{ __('search') }}">
			<svg class="icon search-icon">
				<use xlink:href="{{ asset('assets/icons.svg#search') }}" />
			</svg>
		</div>
		<div class="data-table-toolbar-section right">
			<div class="data-table-toolbar-section-option d-flex align-items-center gap-1">
				<label for="status-filter" class="input-style-1-label">{{ __('status') }}</label>
				<div class="custom-select-container">
					<select id="status-filter" class="filter-by-search input-style-1">
						<option value="all">{{ __('all') }}</option>
						<option value="Read:Read">{{ __('read') }}</option>
						<option value="Read:Unread">{{ __('unread') }}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th>#</th>
				<th>{{ __('name') }}</th>
				<th>{{ __('email') }}</th>
				<th>{{ __('read') }}</th>
				<th>{{ __('message') }}</th>
				<th>{{ __('date') }}</th>
				<th>{{ __('action') }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<div id="page-modal" class="modal" style="max-width: 55rem;">
	<div class="modal-header">
		<p class="modal-title"></p>
		<span onclick="hideModal('page-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<div class="modal-text-group"></div>
	</div>
</div>

<div id="reply-modal" class="modal" style="max-width: 55rem;">
	<div class="modal-header">
		<p class="modal-title">{{ __('reply message') }}</p>
		<span onclick="hideModal()">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<form action="#" onsubmit="submitReply()">
			<div class="form-group">
				<label class="input-style-1-label">{{ __('name') }}</label>
				<input name="recipient-name" type="text" class="input-style-1" disabled>
			</div>
			<div class="form-group">
				<label class="input-style-1-label">{{ __('email') }}</label>
				<input name="recipient-email" type="email" class="input-style-1" disabled>
			</div>
			<div class="form-group">
				<label class="input-style-1-label">{{ __('reply') }}</label>
				<textarea name="reply" class="input-style-1"></textarea>
			</div>
			<div class="form-group">
				<button data-xhr-name="reply-button" data-xhr-loading.attr="disabled" type="submit" class="button button-primary button-sm">{{ __('send reply') }}</button>
			</div>
		</form>
	</div>
</div>

@stop

@section('page-script')

{{ loadPluginFile('js/script.js', 'contact-message') }}

<script>
	let pageTable = dataTable('page-table');
	let messages = staticContactMessages();

	document.addEventListener('DOMContentLoaded', init);

	function init(){
		populateContactMessages(staticContactMessages());
	}

	/**
	 * Static data
	 */

	function staticContactMessages() {
		let messages = '{!! addSlashes(json_encode(ContactMessage::messages())) !!}';
		return JSON.parse(messages);
	}

	/**
	 * Fetch
	 */

	async function fap() {
		let contactMessages = await fetchContactMessages();
		populateContactMessages(contactMessages);
	}

	async function fetchContactMessages() {
		let response = await ContactMessage.messages();
		messages = response.data;
		return messages;
	}

	/**
	 * Save
	 */

	async function submitReply() {

		if (event) event.preventDefault();

		let modalEl = document.querySelector("#reply-modal");
		let nameEl = modalEl.querySelector("[name='recipient-name']");
		let emailEl = modalEl.querySelector("[name='recipient-email']");
		let replyEl = modalEl.querySelector("[name='reply']");

		let postData = {
			name: nameEl.value,
			email: emailEl.value,
			reply: replyEl.value
		};

		let n = showProcessingNotification();
		let response = await ContactMessage.reply(postData, {target: 'reply-button'});
		showResponseNotification(n ,response);

		hideModal();
	}

	async function markMessageAsRead(messageId) {
		await ContactMessage.markAsRead(messageId);
		fetchPortalGenericData();
		fap();
	}

	/**
	 * Delete
	 */

	async function deleteMessage(id) {
		let n = showDeletingNotification();
		let response = await ContactMessage.deleteMessage(id);
		showResponseNotification(n, response);
		if (response.data.status === 'success') {
			fetchPortalGenericData();
			fap();
		}
	}

	/**
	 * Populate
	 */

	function populateContactMessages(messages) {
		let tableData = messages.map((message, messageIndex) => {

			let nameObj = message.detail.find(row => row.column_name === 'name');
			let emailObj = message.detail.find(row => row.column_name === 'email');
			let msgObj = message.detail.find(row => row.column_name === 'msg');

			return [{
					type: 'text',
					value: (messageIndex + 1)
				},
				{
					type: 'text',
					value: nameObj !== undefined ? nameObj.column_value : ''
				},
				{
					type: 'text',
					value: emailObj !== undefined ? emailObj.column_value : ''
				},
				{
					type: 'tag',
					itemClasses: [message.read === 0 ? 'tag-warning' : 'tag-success'],
					value: message.read === 0 ? 'Unread' : 'Read'
				},

				{
					type: 'excerpt',
					value: msgObj !== undefined ? msgObj.column_value : ''
				},
				{
					type: 'text',
					value: toLocalDateTime(message.create_datetime)
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-reply',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							event: {
								'click': function() {
									showReplyModal(message.id);
								}
							}
						},
						{
							icon: 'solid-eye',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							event: {
								'click': function() {
									showMessage(message);
								}
							}
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteMessage(message.id);
											}
										}
									});

								}
							}
						}
					]
				}
			];
		});

		pageTable.init(tableData);
	}

	/**
	 * Other
	 */

	function showMessage(message) {
		let modal = document.querySelector('#page-modal');
		modal.querySelector('.modal-title').innerHTML = '{{ __("message") }}';

		let layouts = message.detail.map(row => `<p><b>${capitalize(row.column_name)}:</b> ${row.column_value}</p>`);
		layouts.push(`<p><b>Message Date:</b> ${toLocalDateTime(message.create_datetime)}</p>`);

		modal.querySelector('.modal-text-group').innerHTML = layouts.join('');
		showModal('page-modal');

		if (message.read === 0) markMessageAsRead(message.id);

	}

	function showReplyModal(messageId) {
		
		let message = messages.find(m => m.id == messageId);
		if (!message) return;
		
		let modalEl = document.querySelector("#reply-modal");
		let nameEl = modalEl.querySelector("[name='recipient-name']");
		let emailEl = modalEl.querySelector("[name='recipient-email']");
		let replyEl = modalEl.querySelector("[name='reply']");

		let nameObj = message.detail.find(row => row.column_name == "name");
		let emailObj = message.detail.find(row => row.column_name == "email");
		
		if(nameObj) nameEl.value = nameObj.column_value;
		if(emailObj) emailEl.value = emailObj.column_value;

		replyEl.value = "";

		showModal("reply-modal");
	}

</script>
@stop