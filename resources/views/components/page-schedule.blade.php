<p class="grid-widget-text icon-text schedule | cursor-pointer" onclick="openScheduleModal()">
	<svg class="grid-widget-icon">
		<use xlink:href="{{ asset('assets/icons.svg#solid-schedule') }}" />
	</svg>
	<b>{{ $buttonText ?? "Schedule" }}</b>
</p>

<!-- Modal -->
<div id="schedule-modal" class="modal" style="max-width: 50%;">
	<div class="modal-header">
		<p class="modal-title">{{ __("schedule") }}</p>
		<span onclick="hideModal('schedule-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body" style="height:40vh;">
		<div class="grids grids-2 | gap-5">
			<div class="grid">
				<form action="#" onsubmit="return false;">
					<div class="form-group">
						<label class="input-style-1-label">{{ __("date") }}</label>
						<input name="schedule-date" type="date" class="input-style-1">
					</div>
				</form>
			</div>
			<div class="grid">
				<form action="#" onsubmit="return false;">
					<div class="form-group">
						<label class="input-style-1-label">{{ __("time") }}</label>
						<input name="schedule-time" type="time" class="input-style-1">
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal-footer | d-flex justify-content-end">
		<div class="button-group">
			<button data-is="remove-schedule-button" class="button button-danger-border | hide" onclick="removeSchedule()">{{ ucwords(__("remove schedule")) }}</button>
			<button data-is="set-schedule-button" class="button button-primary" onclick="commitSchedule()">{{ ucwords(__("set schedule")) }}</button>
		</div>
	</div>
</div>


<script>
	let schedule = null;

	function openScheduleModal() {
		let modal = document.querySelector('#schedule-modal');

		if (schedule !== null) {
			modal.querySelector('[name="schedule-date"]').value = schedule.date;
			modal.querySelector('[name="schedule-time"]').value = schedule.time;
			modal.querySelector('[data-is="remove-schedule-button"]').classList.remove('hide');
		} else {
			modal.querySelector('[name="schedule-date"]').value = '';
			modal.querySelector('[name="schedule-time"]').value = '';
			modal.querySelector('[data-is="remove-schedule-button"]').classList.add('hide');
		}

		showModal('schedule-modal');
	}

	function commitSchedule() {

		let modal = document.querySelector('#schedule-modal');
		let scheduleDate = modal.querySelector('[name="schedule-date"]').value;
		let scheduleTime = modal.querySelector('[name="schedule-time"]').value;

		schedule = {
			date: scheduleDate,
			time: scheduleTime
		};

		if (scheduleDate == '') {
			Notification.show({
				text: '{{ __("select-schedule-date-notification") }}',
				classes: ['fail']
			});
			return;
		} else if (scheduleTime == '') {
			Notification.show({
				text: '{{ __("select-schedule-time-notification") }}',
				classes: ['fail']
			});
			return;
		}

		document.querySelector('[data-is="publish-button"]').classList.add('hide');
		document.querySelector('[data-is="drafts-button"]').classList.add('hide');
		document.querySelector('[data-is="schedule-button"]').classList.remove('hide');
		document.querySelector('.grid-widget-text.icon-text.schedule').classList.add('success');

		hideModal('schedule-modal');
	}

	function removeSchedule() {

		let modal = document.querySelector('#schedule-modal');
		modal.querySelector('[name="schedule-date"]').value = '';
		modal.querySelector('[name="schedule-time"]').value = '';

		schedule = null;
		document.querySelector('[data-is="publish-button"]').classList.remove('hide');
		document.querySelector('[data-is="drafts-button"]').classList.remove('hide');
		document.querySelector('[data-is="schedule-button"]').classList.add('hide');
		document.querySelector('.grid-widget-text.icon-text.schedule').classList.remove('success');
		hideModal('schedule-modal');
	}
</script>