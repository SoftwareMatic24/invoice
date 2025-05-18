@inject("invoiceDocumentCtrl", "App\Plugins\QuickInvoice\Controllers\InvoiceDocumentController")

<div>
	<div class="cards cards-4">
		<div class="card stats-card" style="min-height: 13rem;">
			<div class="stats-card-body">
				<p class="sub">All Invoices</p>
				<p class="count" data-is="all-invoice-count">0</p>
			</div>
		</div>
		<div class="card stats-card">
			<div class="stats-card-body">
				<p class="sub">Paid Invoices</p>
				<p class="count" data-is="paid-invoice-count">0</p>
			</div>
		</div>
		<div class="card stats-card">
			<div class="stats-card-body">
				<p class="sub">Partially Paid Invoices</p>
				<p class="count" data-is="partially-paid-invoice-count">0</p>
			</div>
		</div>
		<div class="card stats-card">
			<div class="stats-card-body">
				<p class="sub">Unpaid Invoices</p>
				<p class="count" data-is="unpaid-invoice-count">0</p>
			</div>
		</div>
	</div>
	<div class="margin-top-2 d-flex justify-content-end">
		<form action="#" class="form-filters">
			<div class="form-group">
				<div class="grids gap-1">
					<div class="grid">
						<div class="custom-select-container">
							<select onchange="handleFilterChange()" name="clients" class="input-style-1" style="width: 25rem;"></select>
						</div>
					</div>
					<div class="grid">
						<div class="custom-select-container">
							<select onchange="handleFilterChange()" name="currencies" class="input-style-1" style="width: 18rem;"></select>
						</div>
					</div>
					<div class="grid">
						<div class="custom-select-container">
							<select onchange="handleFilterChange()" name="years" class="input-style-1" style="width: 13rem;"></select>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div style="height: 330px;" class="margin-top-6"><canvas id="chart"></canvas></div>
</div>

<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
	let chartEl = document.querySelector('#chart');
	let chartInstance = new Chart(chartEl, {
		type: 'bar',
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				title: {
					display: false,
				},
				legend: {
					display: false
				}
			},
			scales: {
				x: {
					grid: {
						display: false
					}
				},
				y: {
					grid: {
						display: false
					},
					stacked: false
				}
			},
		},
	})
	let stats = '{!! addSlashes(json_encode($invoiceDocumentCtrl->userInvoiceStats(request()["loggedInUser"]["id"] ?? ""))) !!}';
	stats = JSON.parse(stats);
	document.addEventListener('DOMContentLoaded', function() {
		populateFilters(stats);
		populateInvoiceCounts(stats);
		updateChart();
	});

	// Filters
	function populateFilters(stats) {
		populateClientsFilter(stats.clients);
		populateCurrencyFilter(stats.currencies);
		populateYearsFilter(stats.years);
	}

	function populateClientsFilter(clients) {
		let clientsEl = document.querySelector('[name="clients"]');
		let optionsHTML = clients.map((client, clientIndex) => {
			let html = ``;
			if (clientIndex === 0) html += `<option value="">All Clients</option>`;
			html += `<option value="${client}">${client}</option>`;
			return html;
		}).join('');

		if(optionsHTML === '') optionsHTML = '<option value="">Select client</option>';
		clientsEl.innerHTML = optionsHTML;
	}

	function populateCurrencyFilter(currencies) {
		let currenciesEl = document.querySelector('[name="currencies"]');
		let optionsHTML = currencies.map(currency => {
			if(currency === null) currency = 'No Currency';
			return `<option value="${currency}">${currency}</option>`;
		}).join('');
		if(optionsHTML === '') optionsHTML = '<option value="">Select currency</option>'
		currenciesEl.innerHTML = optionsHTML;
	}

	function populateYearsFilter(years) {
		let yearsEl = document.querySelector('[name="years"]');
		let optionsHTML = years.map(year => {
			return `<option value="${year}">${year}</option>`;
		}).join('');
		if(optionsHTML === '') optionsHTML = `<option value="${moment().format('Y')}">${moment().format('Y')}</option>`
		yearsEl.innerHTML = optionsHTML;
	}

	function handleFilterChange(){
		updateChart();
	}

	// Stats

	function documentCounts(yearRecords){
		let docs = Object.values(yearRecords).reduce((acc, month) => {
			let docs = [];
			for(let monthNumber in month){
				let monthDocuments = month[monthNumber];
				docs = [...docs, ...monthDocuments];
			}
			acc = [...acc, ...docs];
			return acc;
		}, []);
		
		return docs.reduce((acc, doc) => {
			let total = calculateItemsTotal(doc.items, doc.discount, doc.discount_type);
			let paymentsTotal = doc.payments.reduce((total,payment)=>{
				total += parseFloat(payment.amount);
				return total;
			},0)
			acc.totalCount++;
			if(paymentsTotal >= total.total) acc.paidCount++;
			else if(paymentsTotal === 0) acc.unpaidCount++;
			else if(paymentsTotal > 0) acc.partiallyPaidCount++;
			return acc;
		}, {totalCount: 0, paidCount: 0, unpaidCount: 0, partiallyPaidCount: 0});
	}

	function populateInvoiceCounts(stats){
		let allInvoiceCountEl = document.querySelector('[data-is="all-invoice-count"]');
		let paidInvoiceCountEl = document.querySelector('[data-is="paid-invoice-count"]');
		let unpaidInvoiceCountEl = document.querySelector('[data-is="unpaid-invoice-count"]');
		let partiallPaidInvoiceCountEl = document.querySelector('[data-is="partially-paid-invoice-count"]');

		let counts = documentCounts(stats.documents);
		allInvoiceCountEl.innerHTML = counts.totalCount;
		paidInvoiceCountEl.innerHTML = counts.paidCount;
		unpaidInvoiceCountEl.innerHTML = counts.unpaidCount;
		partiallPaidInvoiceCountEl.innerHTML = counts.partiallyPaidCount;
	}

	// Chart

	function formatYearRecord(yearRecord) {
		let formattedMonths = {};
		for (let monthNumber in yearRecord) {
			let month = yearRecord[monthNumber];
			let obj = month.reduce((acc, row) => {
				let pricing = calculateItemsTotal(row.items, row.discount, row.discount_type);
				let paymentsTotal = row.payments.reduce((total, payment) => {
					total += parseFloat(payment.amount);
					return total;
				}, 0);

				if (paymentsTotal >= pricing.total) acc.paid += pricing.total;
				else if (paymentsTotal === 0) acc.unpaid += pricing.total;
				else if (paymentsTotal > 0) acc.partiallyPaid += paymentsTotal;

				return acc;
			}, {
				paid: 0,
				unpaid: 0,
				partiallyPaid: 0
			});
			formattedMonths[parseFloat(monthNumber)] = obj;
		}
		return formattedMonths;
	}

	function createChartDataSet(formattedMonths) {
		let paids = [];
		let unpaids = [];
		let partiallyPaids = [];

		for (let i = 1; i <= 12; i++) {
			if (formattedMonths[i] === undefined) {
				paids.push(0);
				unpaids.push(0);
				partiallyPaids.push(0);
			} else {
				paids.push(formattedMonths[i].paid);
				unpaids.push(formattedMonths[i].unpaid);
				partiallyPaids.push(formattedMonths[i].partiallyPaid);
			}
		}

		return [{
				label: 'Paid',
				data: paids,
				backgroundColor: '#00509d',
			},
			{
				label: 'Partially Paid',
				data: partiallyPaids,
				backgroundColor: '#f77f00'
			},
			{
				label: 'Unpaid',
				data: unpaids,
				backgroundColor: '#ced4da'
			}
		];
	}

	function updateChart(client = null, currency = null, year = null) {
		let clientsEl = document.querySelector('[name="clients"]');
		let currenciesEl = document.querySelector('[name="currencies"]');
		let yearsEl = document.querySelector('[name="years"]');

		if (client == null) client = clientsEl.value;
		if (currency == null) currency = currenciesEl.value;
		if (year === null) year = yearsEl.value;

		let yearRecord = stats.documents[year];
		if (yearRecord === undefined) return;

		// currency filter
		if(currency !== null && currency !== '') {
			yearRecord = Object.keys(yearRecord).reduce((acc, monthNumber) => {
				let month = yearRecord[monthNumber];
				let requiredDocuments = month.filter(m => m.currency == currency);
				if(acc[monthNumber] === undefined) acc[monthNumber] = [];
				acc[monthNumber] = requiredDocuments;
				return acc;
			}, {});
		}
		// client filter
		if(client !== null && client !== '') {
			yearRecord = Object.keys(yearRecord).reduce((acc, monthNumber) => {
				let month = yearRecord[monthNumber];
				let requiredDocuments = month.filter(m => m.client == client);
				if(acc[monthNumber] === undefined) acc[monthNumber] = [];
				acc[monthNumber] = requiredDocuments;
				return acc;
			}, {});
		}


		let formattedMonths = formatYearRecord(yearRecord);
		let dataset = createChartDataSet(formattedMonths);

	
		chartInstance.data = {
			labels: ["Jan", "Feb", "Mar", "April", "May", "June", "July", "Aug", "Sep", "Oct", "Nov", "Dec"],
			datasets: dataset
		};
		chartInstance.update();
	}
</script>