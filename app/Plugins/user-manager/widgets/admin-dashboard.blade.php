@php
$usersGroup = User::usersGroupByRoleTitle();
@endphp

<div class="cards cards-1">
	<div class="card stats-card shadow">
		<div class="stats-card-body">
			<div class="grids grids-2">
				<div class="grid | d-flex flex-direction-column justify-content-center gap-2 border-right border-clr-200">
					@foreach($usersGroup as $role=>$group)
					<div>
						<h3 class="heading heading-xs">{{ ucwords($role) }}</h3>
						<p class="text text-xs clr-neutral-500 fw-500 | margin-top-0-5 padding-left-1">Total {{ count($group) }} {{ count($group) == 1 ? 'account' : 'accounts' }}</p>
					</div>
					@endforeach
				</div>
				<div class="grid">
					<div class="d-flex justify-content-center">
						<div style="max-width: 16.5rem;">
							<canvas id="users-doughnut-chart"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@section('page-script')
@parent
<script>
	document.addEventListener('DOMContentLoaded', function() {
		initUsersDoughnutChart();
	})

	function initUsersDoughnutChart() {
		let usersDoughnutChartCTX = document.getElementById('users-doughnut-chart');
		return new Chart(usersDoughnutChartCTX, {
			type: 'doughnut',
			data: {
				labels: userDoughnutChartLabels(),
				datasets: [{
					label: 'Accounts',
					data: userDoughnutChartDataset(),
					backgroundColor: [
						'#4682B4',
						'#708090',	
						'#DAA520',
						'#5F9EA0',
						'#A0522D',
						'#BC8F8F',
						'#9ACD32',
						'#B8860B',
						'#6B8E23',
					],
					borderWidth: 1
				}]
			},
			options: {
				cutout: '60%',
				responsive: true,
				plugins: {
					legend: {
						display: false,
						position: 'bottom',
						labels: {
							font: {
								size: 10
							}
						},
						padding: {
							top: 5
						}
					},
					title: {
						display: true,
						text: 'Users by Roles',
						color: getComputedStyle(document.documentElement).getPropertyValue('--clr-neutral-500').trim(),
					}
				}
			},
		});
	}

	function userDoughnutChartLabels() {
		let groups = staticUsersGroup();
		return Object.keys(groups).map(role => capitalize(role));
	}

	function userDoughnutChartDataset() {
		let groups = staticUsersGroup();
		return Object.values(groups).map(users => users.length);
	}

	function staticUsersGroup() {
		let groups = '{!! addSlashes(json_encode($usersGroup)) !!}';
		return JSON.parse(groups);
	}
</script>
@stop