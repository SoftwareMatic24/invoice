@extends('layouts.portal')

@php
$documents = QuickInvoiceDocument::userDocumentsByType($userId, $documentType)
@endphp

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
			<a href="{{ url('/portal/quick-invoice/documents') }}/{{$documentType}}/save" class="button button-primary">{{ __('new') }} {{ __( (str_replace("-"," ", $documentType))) }}</a>
		</div>
	</div>
	<table id="page-table" class="data-table">
		<thead class="sticky">
			<tr>
				<th data-uid="true">#</th>
				<th>{{ __('document number') }}</th>
				<th>{{ __('reference number') }}</th>
				<th>{{ __('client') }}</th>
				<th>{{ __('business') }}</th>
				@if($documentType === "invoice")
				<th>{{ __('payment') }}</th>
				@endif
				<th>{{ __('date of issue') }}</th>
				<th>
					@if($documentType === "invoice")
					{{ __('due date') }}
					@else
					{{ __('delivery date') }}
					@endif
				</th>
				<th>{{ __("action") }}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<div id="share-modal" class="modal" style="width: min(55rem, 90%)">
	<div class="modal-header">
		<p class="modal-title">{{ __('share') }} {{ __( (str_replace("-"," ", $documentType))) }}</p>
		<span onclick="hideModal('share-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<div class="modal-text-group | margin-bottom-2">
			<p>
				{{ __('share-doc-description-1') }} {{ __( (str_replace("-"," ", $documentType))) }} {{ __('share-doc-description-2') }}
			</p>
		</div>
		<div class="d-flex align-items-center gap-1">
			<div class="share-link-container" style="flex-grow: 1;">
				<input type="text">
				<button>
					<svg class="icon">
						<use xlink:href="{{ asset('assets/icons.svg#outline-copy') }}" />
					</svg>
				</button>
			</div>
			<a href="#" target="_blank" class="icon-option">
				<img class="icon" src="{{ asset('assets/icons/whatsapp-64x64.png') }}" alt="whatsapp">
			</a>
		</div>
	</div>

</div>

<div id="email-modal" class="modal" style="width: min(55rem, 90%)">
	<div class="modal-header">
		<p class="modal-title">{{ __('send') }} {{ __( (str_replace("-"," ", $documentType))) }} {{ ucwords(__('via email')) }}</p>
		<span onclick="hideModal('email-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<form action="#" onsubmit="sendDocumentViaEmail()">
			<div class="form-group inline-label">
				<input name="document-id" type="hidden" disabled>
				<label>{{ __('to') }}</label>
				<input name="email-to" type="email" class="input-style-1" style="padding-left: 5rem;">
			</div>
			<div class="form-group inline-label">
				<label>{{ __('subject') }}</label>
				<input name="email-subject" type="text" class="input-style-1" style="padding-left: 9rem;">
			</div>
			<div class="form-group">
				<textarea name="email-message" class="input-style-1" rows="5" placeholder="Write your message" style="resize: none;"></textarea>
			</div>
			<div class="form-group">
				<h3 class="attachment-heading | margin-bottom-1">{{ __('attachments') }}</h3>
				<div class="attachments"></div>
			</div>
			<div class="form-group | d-flex justify-content-end">
				<button data-xhr-name="send-document-via-mail-button" data-xhr-loading.attr="disabled" class="button button-primary button-sm button-block-on-sm">{{ __('send email') }}</button>
			</div>
		</form>
	</div>
</div>

<div id="payment-modal" class="modal" style="width: min(90rem, 90%)">
	<div class="modal-header">
		<p class="modal-title">{{ __('payments') }}</p>
		<span onclick="hideModal('payment-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<form action="#" onsubmit="addPayment()">
			<input name="payment-document-id" type="hidden" disabled>
			<div class="form-group">
				<div class="grids grids-3 gap-2">
					<div class="grid">
						<div class="form-group inline-label">
							<label>{{ __('reference number') }}</label>
							<input name="payment-reference-no" type="text" class="input-style-1" style="padding-left: 17rem;">
						</div>
					</div>
					<div class="grid">
						<div class="form-group inline-label">
							<label for="payment-amount">{{ __('amount') }}</label>
							<input id="payment-amount" name="payment-amount" type="text" class="input-style-1" style="padding-left: 13.3rem;">
						</div>
					</div>
					<div class="grid">
						<div class="form-group inline-label">
							<label>{{ __('date') }}</label>
							<input name="payment-date" type="date" class="input-style-1" style="padding-left: 7rem;">
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<textarea name="note" class="input-style-1" placeholder="{{ __('note') }}" style="resize: none;"></textarea>
			</div>
			<div class="form-group d-flex align-items-center justify-content-space-between">
				<div class="modal-text-group">
					<p><b>{{ __('due') }}:</b> <span data-is="amount-due"></span></p>
					<p><b>{{ __('paid') }}:</b> <span data-is="amount-paid"></span></p>
				</div>
				<button data-xhr-name="save-payment-button" data-xhr-loading.attr="disabled" class="button button-primary button-sm button-block-on-sm">
					{{ __('add payment') }}
				</button>
			</div>
		</form>
		<div class="data-table-container | margin-top-2">
			<div class="data-table-toolbar sticky">
				<div class="data-table-toolbar-section search-section">
					<input type="text" class="search input-style-1" placeholder="{{ __('search') }}">
					<svg class="icon search-icon">
						<use xlink:href="{{ asset('assets/icons.svg#search') }}" />
					</svg>
				</div>
				<div class="data-table-toolbar-section right"></div>
			</div>
			<table id="payments-table" class="data-table">
				<thead class="sticky">
					<tr>
						<th>{{ __('reference number') }}</th>
						<th>{{ __('amount') }}</th>
						<th>{{ __('date') }}</th>
						<th>{{ __('note') }}</th>
						<th>{{ __("action") }}</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>
@stop

@section('page-script')

{!! loadPluginFile('js/document.js', 'quick-invoice') !!}

<script>
	let pageTable = dataTable('page-table');
	let paymentsTable = dataTable('payments-table');
	let documents = '{!! addSlashes(json_encode($documents)) !!}';
	documents = JSON.parse(documents);

	document.addEventListener('DOMContentLoaded', init);

	function init() {
		populateDocuments(staticDocuments());
	}

	/**
	 * Static data
	 */

	function staticDocuments() {
		return documents;
	}

	function staticDocumentType() {
		return '{!! $documentType  !!}';
	}

	/**
	 * Fetch
	 */

	async function fap() {
		documents = await fetchDocuments();
		populateDocuments(documents);
	}

	async function fetchDocuments() {
		let response = await QuickInvoiceDocument.userDocumentsByType(staticDocumentType());
		return docs = response.data;
	}

	async function deleteDocument(documentId) {
		let n = showDeletingNotification();
		let response = await QuickInvoiceDocument.deleteUserDocument(documentId, staticDocumentType());
		showResponseNotification(n, response);
		if (response.data.status === 'success') fap();
	}

	function populateDocuments(documents) {

		let tableData = documents.map((doc, docIndex) => {
			let paymentStatus = getPaymentStatus(doc);
			return [{
					type: 'text',
					value: docIndex + 1
				},
				{
					type: 'text',
					value: doc.document_number
				},
				{
					type: 'text',
					value: toStr(doc, 'reference_number')
				},
				{
					type: 'text',
					value: toStr(doc, 'client', 'name')
				},
				{
					type: 'text',
					value: toStr(doc, 'business', 'name')
				},
				{
					type: 'tag',
					value: slugToText(paymentStatus),
					itemClasses: [paymentStatus === 'paid' ? 'tag-success' : 'tag-warning'],
					classes: [staticDocumentType() !== 'invoice' ? 'hide' : '']
				},
				{
					type: 'text',
					value: moment(doc.issue_date).format(CLIENT_DATE_FORMAT)
				},
				{
					type: 'text',
					value: doc.due_date !== null ? moment(doc.due_date).format(CLIENT_DATE_FORMAT) : ''
				},
				{
					type: 'button-group-icon',
					value: [{
							icon: 'solid-document',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							attributes: ['data-popover="Document"'],
							link: BASE_URL + '/quick-invoice/documents/online/' + doc.uid + '/' + doc.document_number,
							target: '_blank'
						},
						{
							icon: 'solid-more',
							classes: ['button', 'button-icon', 'button-icon-primary', 'floating-dropdown-toggler'],
							attributes: ['data-popover="More"'],
							event: {
								click: function() {
									let targetEl = event.currentTarget;
									showMoreDropdown(doc.id, doc.document_type, targetEl)
								}
							}
						},
						{
							icon: 'solid-pencil',
							classes: ['button', 'button-icon', 'button-icon-primary'],
							link: PREFIXED_URL + '/quick-invoice/documents/' + staticDocumentType() + '/save/' + doc.id,
							attributes: ['data-popover="Edit"'],
						},
						{
							icon: 'solid-trash',
							classes: ['button', 'button-icon', 'button-icon-danger'],
							attributes: ['data-popover="Delete"'],
							event: {
								'click': function() {

									Confirmation.show({
										positiveButton: {
											function: function() {
												deleteDocument(doc.id);
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
		popover.init();
	}

	/**
	 * Copy
	 */

	async function copyDocumentAs(documentId, newDocumentType) {
		let n = showProcessingNotification();
		let response = await QuickInvoiceDocument.copyUserDocument(documentId, newDocumentType);
		showResponseNotification(n, response);
	}

	// Send Document via Mail

	function showEmailModal(documentId) {
		let modalEl = document.querySelector(`#email-modal`);

		let doc = staticDocuments().find(doc => doc.id == documentId);
		if (doc === undefined) return;

		let onlineLink = BASE_URL + '/quick-invoice/documents/online/' + doc.uid + '/' + doc.document_number;

		let attachmentsEl = modalEl.querySelector('.attachments');
		let documentIdEl = modalEl.querySelector('[name="document-id"]');
		let emailToEl = modalEl.querySelector('[name="email-to"]');
		let emailSubjectEl = modalEl.querySelector('[name="email-subject"]');
		let emailMessageEl = modalEl.querySelector('[name="email-message"]');

		documentIdEl.value = documentId;
		emailToEl.value = doc.client.email;
		emailSubjectEl.value = slugToText(staticDocumentType()) + ' - ' + doc.document_number;
		emailMessageEl.value = `I trust this message finds you well. We appreciate your partnership with [business-name], and we are committed to ensuring a seamless experience for you.\n\nKindly review the attached ${slugToText(doc.document_type)} and let us know if you have any questions or require further clarification.`;
		attachmentsEl.innerHTML = `
			<a href="${onlineLink}" target="_blank" class="attachment" style="max-width: 25rem;">
				<svg class="icon icon-pdf-red">
					<use xlink:href="${BASE_URL}/assets/icons.svg#solid-document" />
				</svg>
				<span class="attachment-name">${doc.document_number}.pdf</span>
			</a>
		`;

		showModal('email-modal');
	}

	async function sendDocumentViaEmail() {
		if (event) event.preventDefault();

		let modalEl = document.querySelector(`#email-modal`);

		let documentIdEl = modalEl.querySelector('[name="document-id"]');
		let recipientEl = modalEl.querySelector('[name="email-to"]');
		let subjectEl = modalEl.querySelector('[name="email-subject"]');
		let messageEl = modalEl.querySelector('[name="email-message"]');

		let documentId = documentIdEl.value;
		let recipient = recipientEl.value;
		let subject = subjectEl.value;
		let message = messageEl.value;

		let n = showProcessingNotification();
		let response = await QuickInvoiceDocument.sendViaEmail(documentId, recipient, subject, message, {target: 'send-document-via-mail-button'});
		showResponseNotification(n, response);

		if (response.data.status === 'success') hideModal('email-modal');
	}

	// Share Document

	function showShareModal(documentId) {
		let modalEl = document.querySelector('#share-modal');
		let linkInputEl = modalEl.querySelector('.share-link-container input[type="text"]');
		let buttonEl = modalEl.querySelector('.share-link-container button');
		let whatsappOptionEl = modalEl.querySelector('.icon-option');

		let doc = staticDocuments().find(doc => doc.id == documentId);
		if (doc === undefined) return;

		let onlineLink = BASE_URL + '/quick-invoice/documents/online/' + doc.uid + '/' + doc.document_number;

		linkInputEl.value = onlineLink;
		buttonEl.setAttribute('onclick', `copyDocumentLink('${onlineLink}')`)
		whatsappOptionEl.href = `whatsapp://send?text=${onlineLink}`;
		showModal('share-modal');
	}

	function copyDocumentLink(link) {
		copyToClipboard(link);

		Notification.show({
			heading: '{!! __("document-link-copy-notification-heading") !!}',
			description: '{!! __("document-link-copy-notification-description") !!}',
			classes: ['success']
		});
	}

	// Add Payments

	async function showPaymentModal(documentId) {
		let modalEl = document.querySelector('#payment-modal');
		let modalBodyEl = modalEl.querySelector('.modal-body');

		let amountLabelEl = modalEl.querySelector('[for="payment-amount"]');
		let documentIdEl = modalEl.querySelector('[name="payment-document-id"]');
		let referenceNumberEl = modalEl.querySelector('[name="payment-reference-no"]');
		let amountEl = modalEl.querySelector('[name="payment-amount"]');
		let dateEl = modalEl.querySelector('[name="payment-date"]');
		let noteEl = modalEl.querySelector('[name="note"]');

		let doc = staticDocuments().find(doc => doc.id == documentId);
		if (doc === undefined) return;

		let totalPayment = doc.payments.reduce((total, payment) => total += parseFloat(payment.amount), 0);
		let itemsTotal = calculateItemsTotal(doc.items, doc.discount, doc.discount_type);

		let remaining = itemsTotal.total - totalPayment;
		if (remaining < 0) remaining = 0;

		referenceNumberEl.value = '';
		amountEl.value = remaining;
		dateEl.value = moment().format('YYYY-MM-DD');
		noteEl.value = '';
		paymentsTable.init([]);


		if (doc.currency !== null) {
			amountLabelEl.innerHTML = `{{ __('amount') }} (${doc.currency})`;
			amountEl.style.paddingLeft = '13.2rem';
		} else amountEl.style.paddingLeft = '9rem';

		documentIdEl.value = documentId;

		let eloader = eLoader();
		eloader.show(modalBodyEl);
		showModal('payment-modal');
		await updatePaymentModalPayments(documentId);
		eloader.hide(modalBodyEl);
	}

	async function addPayment() {
		if (event) event.preventDefault();
		let modalEl = document.querySelector('#payment-modal');
		let documentIdEl = modalEl.querySelector('[name="payment-document-id"]');
		let referenceNumberEl = modalEl.querySelector('[name="payment-reference-no"]');
		let amountEl = modalEl.querySelector('[name="payment-amount"]');
		let dateEl = modalEl.querySelector('[name="payment-date"]');
		let noteEl = modalEl.querySelector('[name="note"]');

		let postData = {
			documentId: documentIdEl.value,
			referenceNumber: referenceNumberEl.value,
			amount: amountEl.value,
			date: dateEl.value,
			note: noteEl.value
		};

		let n = showSavingNotification();
		let response = await QuickInvoiceDocument.saveUserDocumentPayment(postData, {target: 'save-payment-button'});
		showResponseNotification(n, response);

		if (response.data.status === 'success') {
			await fap();
			updatePaymentModalPayments(documentIdEl.value);
			referenceNumberEl.value = '';
			amountEl.value = '';
			dateEl.value = '';
			noteEl.value = '';
		}
	}

	async function fetchAndPopulateDocumentPayments(documentId) {
		let payments = await fetchDocumentPayments(documentId);
		populateDocumentPayments(payments);
	}

	async function fetchDocumentPayments(documentId) {
		let response = await QuickInvoiceDocument.userDocumentPayments(documentId);
		return response.data;
	}

	function populateDocumentPayments(payments) {
		let tableData = payments.map(payment => {
			return [{
					type: 'text',
					value: toStr(payment, 'reference_number')
				},
				{
					type: 'text',
					value: toStr(payment, 'amount')
				},
				{
					type: 'text',
					value: toStr(payment, 'payment_datetime') == '' ? '' : toLocalDateTime(payment.payment_datetime, true)
				},
				{
					type: 'text',
					value: toStr(payment, 'note')
				},
				{
					type: 'button-group-icon',
					value: [{
						icon: 'solid-trash',
						classes: ['button', 'button-icon', 'button-icon-danger'],
						event: {
							'click': function() {
								Confirmation.show({
									positiveButton: {
										function: function() {
											deletePayment(payment.id, payment.document_id);
										}
									}
								});

							}
						}
					}]
				}
			]
		});
		paymentsTable.init(tableData);
	}

	async function updatePaymentModalPayments(documentId) {
		let modalEl = document.querySelector('#payment-modal');
		let amountDueEl = modalEl.querySelector('[data-is="amount-due"]');
		let amountPaidEl = modalEl.querySelector('[data-is="amount-paid"]');

		await fetchAndPopulateDocumentPayments(documentId);

		let doc = staticDocuments().find(doc => doc.id == documentId);
		if (doc === undefined) return;

		let itemsTotal = calculateItemsTotal(doc.items, doc.discount, doc.discount_type);
		let paidTotal = doc.payments.reduce((total, payment) => {
			total += parseFloat(payment.amount);
			return total;
		}, 0);

		amountDueEl.innerHTML = formatNumber(itemsTotal.total);
		amountPaidEl.innerHTML = formatNumber(paidTotal);

		if (doc.currency !== null) {
			amountDueEl.innerHTML = amountDueEl.innerHTML + ' ' + doc.currency;
			amountPaidEl.innerHTML = amountPaidEl.innerHTML + ' ' + doc.currency;
		}
	}

	async function deletePayment(paymentId, documentId) {	
		let n = showDeletingNotification();
		let response = await QuickInvoiceDocument.deleteUserDocumentPayment(paymentId);
		showResponseNotification(n, response);
		
		if (response.data.status === 'success') {
			await fap();
			updatePaymentModalPayments(documentId);
		}
	}

	// Other

	function showMoreDropdown(documentId, documentType, el) {
		let dropDownItems = [];

		let invoiceDropdownItems = [{
				text: 'Invoice Payments',
				icon: {
					url: BASE_URL + '/plugin/quick-invoice/icons/icons.svg#solid-dollar-circle'
				},
				event: {
					click: function() {
						showPaymentModal(documentId);
					}
				}
			},
			{
				text: 'Send Invoice via Email',
				icon: {
					url: BASE_URL + '/assets/icons.svg#solid-envelope'
				},
				event: {
					click: function() {
						showEmailModal(documentId);
					}
				}
			},
			{
				text: 'Generate Delivery Note',
				icon: {
					url: BASE_URL + '/plugin/quick-invoice/icons/icons.svg#solid-edit'
				},
				event: {
					click: function() {
						copyDocumentAs(documentId, 'delivery-note');
					}
				}
			},
			{
				type: 'separator'
			},
			{
				text: 'Share Invoice',
				icon: {
					url: BASE_URL + '/plugin/quick-invoice/icons/icons.svg#solid-share'
				},
				event: {
					click: function() {
						showShareModal(documentId);
					}
				}
			},
		];

		let proposalsDropdownItems = [{
				text: 'Send Proposal via Email',
				icon: {
					url: BASE_URL + '/assets/icons.svg#solid-envelope'
				},
				event: {
					click: function() {
						showEmailModal(documentId);
					}
				}
			},
			{
				text: 'Generate Invoice',
				icon: {
					url: BASE_URL + '/plugin/quick-invoice/icons/icons.svg#solid-edit'
				},
				event: {
					click: function() {
						copyDocumentAs(documentId, 'invoice');
					}
				}
			},
			{
				type: 'separator'
			},
			{
				text: 'Share Proposal',
				icon: {
					url: BASE_URL + '/plugin/quick-invoice/icons/icons.svg#solid-share'
				},
				event: {
					click: function() {
						showShareModal(documentId);
					}
				}
			},
		];

		let deliveryNoteDropdownItems = [{
			text: 'Send Delivery Note via Email',
			icon: {
				url: BASE_URL + '/assets/icons.svg#solid-envelope'
			},
			event: {
					click: function() {
						showEmailModal(documentId);
					}
				}
		}];

		if (documentType === 'invoice') dropDownItems = invoiceDropdownItems;
		else if (documentType === 'proposal') dropDownItems = proposalsDropdownItems;
		else if (documentType === 'delivery-note') dropDownItems = deliveryNoteDropdownItems;

		let position = {
			element: el,
			topOffset: 45,
			leftOffset: -90
		};

		FloatingDropdown.show(`document-more-${documentId}`, dropDownItems, position);
	}

	function getPaymentStatus(doc) {
		let total = calculateItemsTotal(doc.items, doc.discount, doc.discount_type);
		let paymentsTotal = doc.payments.reduce((total, payment) => total += parseFloat(payment.amount), 0);

		if (paymentsTotal <= 0) return 'unpaid';
		else if (paymentsTotal < total.total) return 'partially-paid';
		else if (paymentsTotal === total.total) return 'paid';
		else if (paymentsTotal > total.total) return 'over-payment';
	}
</script>

@parent
@stop