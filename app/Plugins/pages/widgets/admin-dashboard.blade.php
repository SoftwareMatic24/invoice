<div>
	<div>
		<div class="grids grids-2 gap-2 grids-1-lg">
			<div class="grid">
				<div class="d-flex flex-direction-column gap-2  height-100">
					<div class="cards cards-2 gap-2 height-100">
						<div class="card stats-card shadow">
							<div class="stats-card-body">
								<p class="sub">Total Pages</p>
								<p class="count">{{ count(Page::allPages()) }}</p>
							</div>
						</div>
						<div class="card stats-card shadow">
							<div class="stats-card-body">
								<p class="sub">Published Pages</p>
								<p class="count">{{ count(Page::pages()) }}</p>
							</div>
						</div>
					</div>
					<div class="cards cards-2 gap-2 height-100">
						<div class="card stats-card shadow">
							<div class="stats-card-body">
								<p class="sub">All Time Visitors</p>
								<p class="count">0</p>
							</div>
						</div>
						<div class="card stats-card shadow">
							<div class="stats-card-body">
								<p class="sub">Monthly Visitors</p>
								<p class="count">0</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="grid">
				<div class="stats-card shadow hide" style="width: 100%;">
					<div class="stats-card-header">
						<h3 class="heading heading-sm">September 2024 Visitors</h3>
					</div>
					<div class="stats-card-body">
						<canvas id="pages-line-chart"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@section('page-script')
@parent
<script>
	let pagesLineChart = document.getElementById('pages-line-chart');

	new Chart(pagesLineChart, {
		type: 'line',
		data: {
			labels: [
				"D1", "D2", "D3", "D4", "D5", "D6", "D7", "D8", "D9", "D10",
				"D11", "D12", "D13", "D14", "D15", "D16", "D17", "D18", "D19", "D20",
				"D21", "D22", "D23", "D24", "D25", "D26", "D27", "D28", "D29", "D30"
			],
			datasets: [{
				label: 'Visitors',
				data: [0],
				borderColor: getComputedStyle(document.documentElement).getPropertyValue('--sidebarColor').trim(),
				backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--sidebarColor').trim(),
				pointRadius: 4,
				fill: false,
				tension: 0.1,
			}]
		},
		options: {
			responsive: true,
			plugins: {
				legend: {
					display: false,
					position: 'top',
				},
				tooltip: {
					callbacks: {
						label: function(context) {
							return context.dataset.label + ': ' + context.raw;
						}
					}
				},
			},
			scales: {
				x: {
					title: {
						display: false,
						text: 'Days',
					},
					grid: {
						display: true
					}
				},
				y: {
					title: {
						display: false,
						text: 'Visitors',
					},
					grid: {
						display: true
					},
					beginAtZero: true
				}
			}
		}
	});
</script>
@stop