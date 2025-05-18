function isEmpty(param) {
	if (param === undefined || param === '' || param === null) return true;
	else if (isArray(param) && param.length === 0) return true;
	else return false;
}

function isArray(variable) {
	return Object.prototype.toString.call(variable) === '[object Array]';
}


// String Manupulation

function capitalize(string) {
	string = string.toLowerCase();
	return string.charAt(0).toUpperCase() + string.slice(1);
}

function capitalizeAll(string, smartCapitalize = false) {
	let ignore = ['and', 'for', 'by', 'the', 'was', 'is', 'a'];

	let output = [];
	if (string == null || string == false || string == undefined) string = '';
	let chunks = string.split(' ');

	chunks.forEach((word, wordIndex) => {
		if (ignore.includes(word.toLowerCase()) && smartCapitalize === false) output.push(capitalize(word));
		else if (wordIndex !== 0 && ignore.includes(word.toLowerCase()) && smartCapitalize === true) output.push(word.toLowerCase());
		else output.push(capitalize(word));
	});
	return output.join(' ');
}

function slugToText(slug) {
	let text = slug.replace(/-/g, ' ').replace(/([a-z])([A-Z])/g, '$1 $2');
	text = text.toString().replaceAll('_', ' ');
	return capitalize(text);
}

function slugify(str) {
	const slug = str
		.toLowerCase()
		.replace(/[^a-z0-9]+/g, '-')
		.replace(/^-+|-+$/g, '')
		.trim();

	return slug;
}

function slugToCamelCase(slug) {
	return slug.replace(/-([a-z])/g, function (match, letter) {
		return letter.toUpperCase();
	});
}

function toStr(obj, ...arr) {
	let pointer = obj;
	arr.forEach(key => {
		if (pointer !== undefined && pointer !== null) pointer = pointer[key];
	});
	return (pointer === undefined || pointer === null) ? '' : pointer;
}

function excerpt(text, maxLength) {
	if (text.length <= maxLength) {
		return text;
	} else {
		const truncatedText = text.substring(0, maxLength);

		const lastSpaceIndex = truncatedText.lastIndexOf(' ');


		if (lastSpaceIndex === -1) {
			return truncatedText + '...';
		} else {
			return truncatedText.substring(0, lastSpaceIndex) + '...';
		}
	}
}

function formatNumber(number) {
	number = parseFloat(number);
	return number.toLocaleString('en-US', {
		style: 'decimal',
		minimumFractionDigits: 2,
		maximumFractionDigits: 2
	});
}

function padWithZeros(number, numberOfZeros) {
	if (typeof number !== 'number' || typeof numberOfZeros !== 'number' || numberOfZeros < 0) return "Invalid input";
	const numberString = number.toString();
	const zerosToAdd = Math.max(0, numberOfZeros - numberString.length);
	const paddedNumber = '0'.repeat(zerosToAdd) + numberString;
	return paddedNumber;
}


// String Sanitize

function stripTags(html) {
	return html.replace(/<[^>]*>/g, "");
}

function removeLeadingTrailingSlashes(inputString) {
	return inputString.replace(/^\/|\/$/g, '');
}

// Array

function chunkArray(array, size) {
	return Array.from({
		length: Math.ceil(array.length / size)
	}, (_, index) =>
		array.slice(index * size, index * size + size)
	);
}

// Elements

function disableElement(id) {
	let element = document.getElementById(id);
	if (element !== null) element.setAttribute('disabled', true);
}

function enableElement(id) {
	let element = document.getElementById(id);
	if (element !== null) element.removeAttribute('disabled');
}

function toggleClass(element, className, checkOnly = false) {
	let hasClass = false;
	if (element !== null && element.classList.contains(className)) {
		if (checkOnly == false) element.classList.remove(className);
		hasClass = true;
	} else if (element !== null)
		if (checkOnly == false) element.classList.add(className);
	return hasClass;
}

function removeClosest(closestSelector) {
	let target = event.target;
	let element = target.closest(closestSelector);
	element.remove();
}

let toggleButtons = document.querySelectorAll('.toggle-buttons');

toggleButtons.forEach((tb) => {

	let buttons = tb.querySelectorAll('button');

	buttons.forEach((b) => {

		b.addEventListener('click', function () {

			let toggleButtonContainer = b.closest('.toggle-button-container');
			let toggleContentContainer = b.querySelector('.toggle-content-container');
			let contentContainers = toggleButtonContainer.querySelectorAll('[data-id]');

			let toggleId = b.dataset.toggle;


			buttons.forEach((siblingButton) => {
				if (siblingButton === b) siblingButton.classList.add('active');
				else siblingButton.classList.remove('active');
			});

			contentContainers.forEach((cc) => {
				if (cc.dataset.id == toggleId) cc.classList.add('active');
				else cc.classList.remove('active');
			});

		});

	});

});

// Calculations

function calculatePercentage(percentage, number) {
	if (typeof percentage !== 'number') percentage = parseFloat(percentage);
	if (typeof number !== 'number') number = parseFloat(number);
	return (percentage / 100) * number;
}


// File

function formatFileSize(bytes) {
	const KB = 1024;
	const MB = KB * 1024;
	const GB = MB * 1024;

	if (bytes < KB) {
		return bytes + " B";
	} else if (bytes < MB) {
		return (bytes / KB).toFixed(2) + " KB";
	} else if (bytes < GB) {
		return (bytes / MB).toFixed(2) + " MB";
	} else {
		return (bytes / GB).toFixed(2) + " GB";
	}
}

function fileClassification(fileName) {

	let output = {
		type: null
	};

	let classification = {
		'png': 'image',
		'jpeg': 'image',
		'jpg': 'image',
		'gif': 'image',
		'webp': 'image',
		'mp4': 'video',
		'mkv': 'video',
		'avi': 'video',
		'wmv': 'video',
		'flv': 'video'
	}

	if (fileName == null || fileName == false || fileName == undefined || fileName == '') return output;

	let arr = fileName.toString().split('.');
	let ext = arr[arr.length - 1];

	let classificationType = classification[ext.toString().toLowerCase()] !== undefined ? classification[ext.toString().toLowerCase()] : null;
	if (classificationType === null) return output;

	let nameArr = arr;
	nameArr.pop();


	output.type = classificationType;
	output.ext = ext;
	output.name = nameArr.join('.');

	return output;
}

// Cookie

function getCookie(name) {
	const value = `; ${document.cookie}`;
	const parts = value.split(`; ${name}=`);
	if (parts.length === 2) return parts.pop().split(';').shift();
	return null;
}

function setCookie(name, value, expirationSeconds = null) {
	if (name === undefined || name === '') return console.error('invalid cookie name');

	let arr = [];
	arr.push(`${name}=${value}`);

	if (expirationSeconds !== null) {
		let date = new Date();
		date.setTime(d.getTime() + (expirationSeconds * 1000));
		arr.push(`expires=${d.toUTCString()}`);
	}

	arr.push(`path=/`);
	document.cookie = arr.join(';');
}


// Encryption / Decryption

const cipher = salt => {
	const textToChars = text => text.split('').map(c => c.charCodeAt(0));
	const byteHex = n => ("0" + Number(n).toString(16)).substr(-2);
	const applySaltToChar = code => textToChars(salt).reduce((a, b) => a ^ b, code);

	return text => text.split('')
		.map(textToChars)
		.map(applySaltToChar)
		.map(byteHex)
		.join('');
}

const decipher = salt => {
	const textToChars = text => text.split('').map(c => c.charCodeAt(0));
	const applySaltToChar = code => textToChars(salt).reduce((a, b) => a ^ b, code);

	return encoded => encoded.match(/.{1,2}/g)
		.map(hex => parseInt(hex, 16))
		.map(applySaltToChar)
		.map(charCode => String.fromCharCode(charCode))
		.join('');
}

let encrypter = cipher(APP_NAME);
let decrypter = decipher(APP_NAME);

function encrypt(text) {
	let encodedText = encodeURIComponent(text);
	return encrypter(encodedText);
}

function decrypt(secret) {
	let str = decrypter(secret);
	return decodeURIComponent(str);
}


// Query Params

function searchQueryParam(param) {
	let params = (new URL(document.location)).searchParams;
	return params.get(param);
}


// Misc.

function uid(prefix = '') {
	let timestamp = Date.now().toString(36);
	let randomString = '';
	for (let i = 0; i < 8; i++) {
		randomString += Math.random().toString(36).substring(2, 10);
	}
	let randomNum = Math.floor(Math.random() * 1000);
	return prefix + timestamp + randomNum.toString() + randomString;
}

function nl2br(str) {
	return str.replace(/(?:\r\n|\r|\n)/g, '<br>');
}

function debugLog(x) {
	if (DEBUG === false) return;
	console.log(x);
}

function fullName(firstName, lastName) {
	if (firstName === null || firstName === undefined) firstName = '';
	if (lastName === null || lastName === undefined) lastName = '';

	if (lastName === '') return firstName;
	else return firstName + ' ' + lastName;
}