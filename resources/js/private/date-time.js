function getTimezoneName() {
	return Intl.DateTimeFormat().resolvedOptions().timeZone;
}

function getDateTime() {
	const now = new Date();
	const year = now.getFullYear();
	const month = String(now.getMonth() + 1).padStart(2, '0');
	const day = String(now.getDate()).padStart(2, '0');
	const hours = String(now.getHours()).padStart(2, '0');
	const minutes = String(now.getMinutes()).padStart(2, '0');
	const seconds = String(now.getSeconds()).padStart(2, '0');
	const amOrPm = hours >= 12 ? 'PM' : 'AM';

	const formattedDateTime = `${year}/${month}/${day} ${hours % 12}:${minutes}:${seconds} ${amOrPm}`;
	return formattedDateTime;
}

function formatTime(timeString) {
	const [hourString, minute] = timeString.split(":");
	const hour = +hourString % 24;
	return (hour % 12 || 12) + ":" + minute + (hour < 12 ? "am" : "pm");
}

function toLocalDateTime(dateTimeStr, noTime = false, other = {}) {

	if (dateTimeStr === null || dateTimeStr === false || dateTimeStr === '') return '';

	let dateTimeFormat = other.dateTimeFormat === undefined ? null : other.dateTimeFormat;

	let originalDatetime = moment.tz(dateTimeStr, SERVER_DATETIME_FORMAT, SERVER_TZ);
	let convertedDatetime = originalDatetime.clone().tz(moment.tz.guess());
	let formattedDatetime = null;

	if (dateTimeFormat === null) formattedDatetime = convertedDatetime.format(CLIENT_DATETIME_FORMAT);
	else formattedDatetime = convertedDatetime.format(dateTimeFormat);

	if (noTime === true) {
		let chunks = formattedDatetime.split(' ');
		return chunks[0];
	}

	return formattedDatetime;
}

function toMonthName(monthNumber) {
	let months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
		"Jul", "Aug", "Sept", "Oct", "Nov", "Dec"
	];
	return months[parseInt(monthNumber) - 1];
}

function dateCheck(from, to, check) {
	let chunks = check.split('/');
	check = chunks[2] + '-' + chunks[1] + '-' + chunks[0];
	const fromDate = moment(from, 'YYYY-MM-DD');
	const toDate = moment(to, 'YYYY-MM-DD');
	const checkDate = moment(check, 'YYYY-MM-DD');
	return checkDate.isSameOrAfter(fromDate) && checkDate.isSameOrBefore(toDate);
}

function yymmddDashedFormat(date) {

	let chunks = date.split('-');


	let year = chunks[0];
	let month = chunks[1];
	let day = chunks[2];

	let monthName = toMonthName(month);

	return monthName + ' ' + day + ', ' + year;
}

function ddmmyy(date) {

	if (date == null || date == false || date == '') return '';

	let chunks = date.split('-');


	let year = chunks[0];
	let month = chunks[1];
	let day = chunks[2];

	let monthName = toMonthName(month);

	return day + '/' + month + '/' + year;
}
