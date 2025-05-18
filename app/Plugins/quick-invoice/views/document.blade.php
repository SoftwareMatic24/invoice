@extends("layouts.document")

@section('page-style')
<style>
	:root{
		--clr-document-primary: {{ $primaryColor ?? '' }};
		--clr-document-secondary: {{ $secondaryColor ?? '' }};
	}
</style>
@stop



@section("main-content")
<div class="document-container">
	<div class="document-toolbar-container">
		<div class="document-toolbar">
			<div class="left">
				<div class="payment-container | hide"></div>
			</div>
			<div class="right">
				<button onclick="downloadDocument()" class="button button-border">
					Download
					<!-- <svg style="transform: translateY(0.1rem);" class="icon">
						<use xlink:href="{{ asset('assets/icons.svg#solid-caret') }}" />
					</svg> -->
				</button>
			</div>
		</div>
	</div>
	<div class="document document-a4 {{ $templateSlug }}">
		<div id="page-1" class="page">
			{!! $templateView !!}
		</div>
	</div>
</div>
@stop

@section("page-script")
<script>
	let documentData = '{!! addSlashes(json_encode($document)) !!}';
	let currentPage = 1;
	let doc = jspdf.jsPDF();
	documentData = JSON.parse(documentData);
	let items = documentData.items;
	
	function populateDocument() {
		let footerEl = document.querySelector(`#page-${currentPage} .footer`);
		let totalEls = document.querySelectorAll('[data-is="total"]');

		populateToolbarPayment();

		let businessPhone = null;
		let businessAddress = [];
		let businessEmail = null;
		let businessWebsite = null;

		if(documentData.business.telephone !== null) businessPhone = documentData.business.telephone;
		else if(documentData.business.phone !== null) businessPhone = documentData.business.phone;

		if(documentData.business.email !== null) businessEmail = documentData.business.email;
		if(documentData.business.website !== null) businessWebsite = documentData.business.website;

		if(documentData.business.city !== null) businessAddress.push(documentData.business.city);
		if(documentData.business.country !== null) {
			let country = countryList().find(c => c.code == documentData.business.country);
			if(country !== undefined) businessAddress.push(country.name);
		}

		let pricing = calculateItemsTotal(items, documentData.discount, documentData.discount_type);
		
		let nextPageTableHTML = `
			<div class="header"></div>
			<div class="body">
				<table>
					<thead>
						<tr>
							<th style="width: 2rem;">No.</th>
							<th>Item Description</th>
							<th style="width: 6rem;">Qty</th>
							<th style="width: 15rem;" class="${documentData.document_type === 'delivery-note' ? 'hide' : ''}">Price</th>
							<th class="${documentData.document_type === 'delivery-note' ? 'hide' : ''}">Total</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<div class="text-group" style="margin-top: 2rem;">
					<div class="left" style="width: 60%;"></div>
					<div class="right border summary" style="width: 43.8%;"></div>
				</div>
			</div>
			<div class="footer"></div>
		`;

		let footerHTML = `
			<ul>
				<li class="${businessPhone === null ? 'hide' : ''}">
					<img class="icon" src="${BASE_URL}/plugin/quick-invoice/assets/phone-grey-100x100.png" />
					${businessPhone}
				</li>
				<li class="${businessAddress.length === 0 ? 'hide' : ''}">
					<img class="icon" src="${BASE_URL}/plugin/quick-invoice/assets/location-pin-grey-100x100.png" />
					${businessAddress.join(', ')}
				</li>
				<li class="${businessEmail === null ? 'hide' : ''}">
					<img class="icon" src="${BASE_URL}/plugin/quick-invoice/assets/envelope-grey-100x100.png" />
					${businessEmail}
				</li>
				<li class="${businessWebsite === null ? 'hide' : ''}">
					<img class="icon" src="${BASE_URL}/plugin/quick-invoice/assets/globe-grey-100x100.png" />
					${businessWebsite}
				</li>
			</ul>
		`;
		let summaryHTML = `
			<table style="transform: translateY(-2rem);border-top:none;" class="${documentData.document_type === 'delivery-note' ? 'hide' : ''}">
				<tbody>
					<tr>
						<td style="background-color: #EEF0E5;font-weight:600;font-size:1.4rem;">Sub Total</td>
						<td style="width: 15rem;">${formatNumber(pricing.subtotal)} ${toStr(documentData, 'currency')}</td>
					</tr>
					<tr>
						<td style="background-color: #EEF0E5;font-weight:600;font-size:1.4rem;">Discount</td>
						<td>${pricing.discountTotal} ${toStr(documentData, 'currency')}</td>
					</tr>
					<tr>
						<td style="background-color: #EEF0E5;font-weight:600;font-size:1.4rem;">VAT</td>
						<td>${pricing.vatPercentage}%</td>
					</tr>
					<tr>
						<td style="background-color: #EEF0E5;font-weight:600;font-size:1.4rem;">Total</td>
						<td>${formatNumber(pricing.total)} ${toStr(documentData, 'currency')}</td>
					</tr>
				</tbody>
			</table>
		`;

		footerEl.innerHTML = footerHTML;
		
		totalEls.forEach(el => {
			el.innerHTML = `${formatNumber(pricing.total)} ${toStr(documentData, 'currency')}`;
		});

		for (let i = 0; i < items.length; i++) {
			let item = items[i];

			let itemPrice = parseFloat(item.unit_price) * parseFloat(item.quantity);
			let vatPrice = calculatePercentage(item.vat, itemPrice);
			let itemPriceWithVat = itemPrice + vatPrice;

			let html = `
				<tr>
					<td>${i + 1}</td>
					<td>${toStr(item, 'title')} <span class="${toStr(item,'code') === '' ? 'hide' : ''}">(${toStr(item,'code')})</span></td>
					<td>${toStr(item, 'quantity')}</td>
					<td class="${documentData.document_type === 'delivery-note' ? 'hide' : ''}">${formatNumber(toStr(item, 'unit_price'))}</td>
					<td class="${documentData.document_type === 'delivery-note' ? 'hide' : ''}">${toStr(item, 'vat')}%</td>
					<td class="${documentData.document_type === 'delivery-note' ? 'hide' : ''}">${formatNumber(itemPriceWithVat)} ${toStr(documentData, 'currency')}</td>
				</tr>
			`;

			let tbodyEl = document.querySelector(`#page-${currentPage} tbody`);		
			tbodyEl.insertAdjacentHTML('beforeend', html);

			if (documentIsFull(`page-${currentPage}`, 80)) {
				currentPage++;
				addNewDocument(['document document-a4 document-invoice-2'], `page-${currentPage}`, nextPageTableHTML);
				footerEl = document.querySelector(`#page-${currentPage} .footer`);
				footerEl.innerHTML = footerHTML;
			}

			
			if(i == items.length-1) document.querySelector(`#page-${currentPage} .summary`).innerHTML = summaryHTML;
		}
	}

	function populateToolbarPayment(){
		let paymentContainerEl = document.querySelector('.document-toolbar .payment-container');
		let pricing = calculateItemsTotal(items, documentData.discount, documentData.discount_type);
		let paymentsTotal = documentData.payments.reduce((total, payment) => {
			total += parseFloat(payment.amount);
			return total;
		}, 0);
		
		let paymentStatus = 'Unpaid';
		if(paymentsTotal === pricing.total) paymentStatus = 'Paid';
		else if(paymentsTotal > pricing.total) paymentStatus = 'Over Payment';
		else if(paymentsTotal > 0) paymentStatus = 'Partially Paid';
		
		if(documentData.document_type !== 'invoice') paymentStatus = 'Total';

		let html = `
			<div class="payment | ${documentData.document_type === 'delivery-note' ? 'hide' : ''}">
				<div>
					<p class="amount">${formatNumber(pricing.total)}</p>
					<p class="currency">${toStr(documentData, 'currency')}</p>
				</div>
				<div>
					<p class="status | ${(paymentStatus === 'Paid' || paymentStatus === 'Over Payment') ? 'clr-green-400' : 'clr-orange-400'}">${paymentStatus}</p>
				</div>
			</div>
		`;
		paymentContainerEl.innerHTML = html;
		paymentContainerEl.classList.remove('hide');
	}

	function calculateItemTotal(item, discount = 0, discountType = 'percentage'){
		let qty = item['quantity'];
		let unitPrice = item['unit_price'];
		let vatPercentage = item['vat'];

		qty = parseFloat(qty);
		unitPrice = parseFloat(unitPrice);
		vatPercentage = parseFloat(vatPercentage);

		qty = parseFloat(qty);
		unitPrice = parseFloat(unitPrice);
		vatPercentage = parseFloat(vatPercentage);

		if (isNaN(qty)) qty = 0;
		if (isNaN(unitPrice)) unitPrice = 0;
		if (isNaN(vatPercentage)) vatPercentage = 0;

		let discountPrice = 0;
		let itemPrice = unitPrice * qty;

		if(discountType === 'percentage') discountPrice = (discount / 100) * itemPrice;
		else if(discountType === 'amount') discountPrice = discount;

		let itemPriceWithDiscount = itemPrice - discountPrice;
		let vatPrice = calculatePercentage(vatPercentage, itemPriceWithDiscount);

		return {
			subtotal: itemPrice,
			total: (itemPrice - discountPrice) + vatPrice,
			discountTotal: discountPrice,
			vatTotal: vatPrice,
			vatPercentage: vatPercentage
		};
	}

	function calculateItemsTotal(items, discount = 0, discountType = 'percentage'){
		if(discountType === 'amount'){
			let subtotal = items.reduce((subtotal, item) =>{
				subtotal += ( parseFloat(item['unit_price']) * parseFloat(item['quantity']) );
				return subtotal;
			} ,0)

		
			return items.reduce((acc, item)=>{

				let qty = item['quantity'];
				let unitPrice = item['unit_price'];
				let vatPercentage = item['vat'];

				qty = parseFloat(qty);
				unitPrice = parseFloat(unitPrice);
				vatPercentage = parseFloat(vatPercentage);

				if (isNaN(qty)) qty = 0;
				if (isNaN(unitPrice)) unitPrice = 0;
				if (isNaN(vatPercentage)) vatPercentage = 0;

				let itemPrice = unitPrice * qty;
				let decimalPercentage = (itemPrice / subtotal);
				if(isNaN(decimalPercentage)) decimalPercentage = 0;
				let itemDiscount = decimalPercentage * discount;
				let itemDiscountPrice = itemPrice - itemDiscount;
				let vatPrice = (itemDiscountPrice * vatPercentage) / 100;

				acc.vatTotal += vatPrice;
				acc.discountTotal += itemDiscount;
				acc.subtotal += itemPrice;
				acc.total += (itemDiscountPrice + vatPrice);
				acc.vatPercentage += vatPercentage;

				return acc;
			}, {total:0, subtotal: 0, discountTotal: 0, vatTotal: 0, vatPercentage: 0});
		}
		return items.reduce((row, item) => {
			let obj = calculateItemTotal(item, discount, discountType);
			row.total += obj.total;
			row.subtotal += obj.subtotal;
			row.discountTotal += obj.discountTotal;
			row.vatTotal += obj.vatTotal;
			row.vatPercentage += obj.vatPercentage;

			return row;
		}, {subtotal: 0, total: 0, discountTotal: 0, vatTotal: 0, vatPercentage: 0});
	}

	function downloadDocument(){
		downloadPDF(`${documentData.document_type}-${documentData.document_number}.pdf`);
	}

	populateDocument();
</script>
@stop