var NotificationGlobalConfig = null;

var Notification = function () {
	let PUBLIC = {};

	let PRIVATE = {
		style: 'default',
		position: 'bottom center',
		time: 6000,
		speed: 500,
		revealDirection: 'bottom',
		revealOffset: '20px'
	};

	// set
	function setStyle(style) {
		PRIVATE.style = style;
	}

	function setPosition(position) {
		PRIVATE.position = position;
	}

	function setSpeed(speed) {
		PRIVATE.speed = speed;
	}

	function setRevealOffset(offset) {
		PRIVATE.revealOffset = offset;
	}

	function setRevealDirection(direction) {
		PRIVATE.direction = direction;
	}

	// get
	function getStyle() {
		if (NotificationGlobalConfig !== null && NotificationGlobalConfig.style !== undefined) return NotificationGlobalConfig.style;
		return PRIVATE.style;
	}

	function getPosition() {
		if (NotificationGlobalConfig !== null && NotificationGlobalConfig.position !== undefined) return NotificationGlobalConfig.position;
		return PRIVATE.position;
	}

	function getSpeed() {
		if (NotificationGlobalConfig !== null && NotificationGlobalConfig.speed !== undefined) return NotificationGlobalConfig.speed;
		return PRIVATE.speed;
	}

	function getRevealDirection() {
		if (NotificationGlobalConfig !== null && NotificationGlobalConfig.revealDirection !== undefined) return NotificationGlobalConfig.revealDirection;
		return PRIVATE.revealDirection;
	}

	function getRevealOffset() {
		if (NotificationGlobalConfig !== null && NotificationGlobalConfig.revealOffset !== undefined) return NotificationGlobalConfig.revealOffset;
		return PRIVATE.revealOffset;
	}

	function getTime() {
		return PRIVATE.time;
	}

	function getIcon(type) {
		let styleName = this.getStyle();
		let defaultIcon = BASE_URL + '/assets/icons.svg#solid-info';
		let defaultCloseIcon = BASE_URL + '/assets/icons.svg#cross';
		let successIcon = defaultIcon;
		let failIcon = defaultIcon;
		let closeIcon = defaultCloseIcon;

		if (NotificationGlobalConfig !== null && NotificationGlobalConfig[styleName] !== undefined && NotificationGlobalConfig[styleName].icon !== undefined) {
			if (NotificationGlobalConfig[styleName].icon.default !== undefined) defaultIcon = NotificationGlobalConfig[styleName].icon.default;
			if (NotificationGlobalConfig[styleName].icon.success !== undefined) successIcon = NotificationGlobalConfig[styleName].icon.success;
			if (NotificationGlobalConfig[styleName].icon.fail !== undefined) failIcon = NotificationGlobalConfig[styleName].icon.fail;
			if (NotificationGlobalConfig[styleName].icon.close !== undefined) closeIcon = NotificationGlobalConfig[styleName].icon.close;
		}

		if (type === 'default') return defaultIcon;
		else if (type === 'success') return successIcon;
		else if (type === 'fail') return failIcon;
		else if (type === 'close') return closeIcon;

		return defaultIcon;
	}

	function hasCloseIcon() {
		if (NotificationGlobalConfig === null) return false;
		return NotificationGlobalConfig.closeIcon == true;
	}

	// show / hide

	function show(obj = {}) {
		let CONTEXT = this;
		let { position = 'bottom center', style = 'default' } = obj;

		this.setStyle(style);
		this.setPosition(position);
		let notification = this.build(obj);
		let containerExists = this.containerExists();
		if (containerExists === true) document.querySelector('.notification-container').insertAdjacentHTML('afterbegin', notification.template);
		else {
			this.addContainer();
			document.querySelector('.notification-container').insertAdjacentHTML('afterbegin', notification.template);
		}

		if (notification.data.time !== 0) {
			setTimeout(function () {
				CONTEXT.hide(notification.data.id);
			}, notification.data.time);
		}

		return notification;
	}

	function hide(notificationId) {
		let notificationSpeed = this.getSpeed();
		let notificationElement = document.getElementById(notificationId);
		notificationElement.classList.remove('active');
		notificationElement.classList.add('inactive');

		setTimeout(function () {
			notificationElement.remove();
			let isContainerEmpty = document.querySelectorAll('.notification-container .notification').length <= 0;
			if (isContainerEmpty === true) document.querySelector('.notification-container').remove();
		}, notificationSpeed);
	}

	function hideDelayed(notificationId) {
		setTimeout(() => this.hide(notificationId), 1000);
	}

	function hideAndShowDelayed(notificationId, obj) {
		setTimeout(() => {
			this.hide(notificationId);
			setTimeout(() => {
				this.show(obj);
			}, 500);
		}, 1000);
	}

	// style

	function style() {
		let css = this.colorStyle();
		css += this.revealStyle();
		if (this.getStyle() === 'default') css += this.defaultStyle();
		else if (this.getStyle() === 'style-1') css += this.style1();
		return css;
	}

	function colorStyle() {
		let activeStyle = this.getStyle();
		let obj = {
			'--notification-clr-default-bg': '#ffffff',
			'--notification-clr-default-border': '#eeeeee',
			'--notification-clr-default-icon': '#ffffff',
			'--notification-clr-default-icon-around': '#0077b6',
			'--notification-clr-default-heading': '#212529',
			'--notification-clr-default-description': '#343a40',
			'--notification-clr-success-bg': '#ffffff',
			'--notification-clr-success-border': '#eeeeee',
			'--notification-clr-success-icon': '#ffffff',
			'--notification-clr-success-icon-around': '#40916c',
			'--notification-clr-success-heading': '#212529',
			'--notification-clr-success-description': '#343a40',
			'--notification-clr-fail-bg': '#ffffff',
			'--notification-clr-fail-border': '#eeeeee',
			'--notification-clr-fail-icon': '#ffffff',
			'--notification-clr-fail-icon-around': '#ef233c',
			'--notification-clr-fail-heading': '#212529',
			'--notification-clr-fail-description': '#343a40',
			'--notification-shadow-opacity': '0.1',
			'--notification-clr-close-icon': '#adb5bd',
			'--notification-clr-close-icon-hover': '#343a40',
			'--notification-clr-close-icon-around-hover': '#ced4da',
		};
		let style = ``;

		if (NotificationGlobalConfig !== null && NotificationGlobalConfig[activeStyle] !== undefined && NotificationGlobalConfig[activeStyle].color !== undefined) {
			for (key in NotificationGlobalConfig[activeStyle].color) {
				let value = NotificationGlobalConfig[activeStyle].color[key];
				obj[key] = value;
			}
		}

		for (let key in obj) {
			style += `${key}:${obj[key]};`
		}

		return `:root{${style}}`;
	}

	function positionStyle() {
		let offsetX = '20px';
		let offsetY = '20px';
		let positionStr = this.getPosition().toLowerCase();
		if (positionStr === 'top left') return `position:fixed;top:${offsetY};left:${offsetX};`;
		else if (positionStr === 'top center') return `position:fixed;top:${offsetY};left:50%;transform:translateX(-50%);`;
		else if (positionStr === 'top right') return `position:fixed;top:${offsetY};right:${offsetX};`;
		else if (positionStr === 'bottom left') return `position:fixed;bottom:${offsetY};left:${offsetX};`;
		else if (positionStr === 'bottom center') return `position:fixed;bottom:${offsetY};left:50%;transform:translateX(-50%);`;
		else if (positionStr === 'bottom right') return `position:fixed;bottom:${offsetY};right:${offsetX};`;
		else return `position:fixed;bottom:${offsetY};left:50%;transform:translateX(-50%);`;
	}

	function revealStyle() {
		let style = ``;
		let offset = this.getRevealOffset();

		if (this.getRevealDirection() === 'top') {
			style = `
						@keyframes notification-reveal {
							0% {
								transform: translateY(calc(100% + ${offset * -1}));
							}
							100% {
								transform: translateY(0px);
							}

						}
						@keyframes notification-hide {
							0% {
								transform: translateY(0px);
							}
							100% {
								transform: translateY(calc(100% + ${offset * -1}));
							}
						}
					`;
		}
		else if (this.getRevealDirection() === 'bottom') {
			style = `
						@keyframes notification-reveal {
							0% {
								transform: translateY(calc(100% + ${offset}));
								opacity: 0;
							}
							100% {
								transform: translateY(0px);
								opacity:1;
							}

						}
						@keyframes notification-hide {
							0% {
								transform: translateY(0px);
								opacity: 1;
							}
							100% {
								transform: translateY(calc(100% + ${offset}));
								opacity: 0;
							}
						}
					`;
		}
		else if (this.getRevealDirection() === 'left') {

		}
		else if (this.revealDirection() === 'right') {

		}
		return style;
	}

	function defaultStyle() {
		let notificationSpeed = this.getSpeed();
		let positionStyle = this.positionStyle();
		let revealOffset = this.getRevealOffset();
		return `
					.notification-container {
						display: flex;
						gap: 10px;
						flex-direction: column;
						justify-content: center;
						align-items: center;
						min-width: 350px;
						z-index: 99;
						${positionStyle}
					}
					.notification {
						background-color: var(--notification-clr-default-bg);
						display: flex;
						align-items: flex-start;
						gap: 20px;
						max-width: 400px;
						width: 100%;
						padding: 10px 50px 10px 12px;
						border: 1px solid var(--notification-clr-default-border);
						border-radius: 0.6rem;
						transform: translateY(calc(100% + ${revealOffset}));
						box-shadow: rgba(99, 99, 99, var(--notification-shadow-opacity)) 0px 2px 8px 0px;
					}
					.notification.active {
						animation-name: notification-reveal;
						animation-duration: ${notificationSpeed}ms;
						animation-fill-mode: forwards;
						animation-iteration-count: 1;
					}
					.notification.inactive {
						animation-name: notification-hide;
						animation-duration: ${notificationSpeed}ms;
						animation-fill-mode: forwards;
						animation-iteration-count: 1;
					}
					.notification-icon-container {
						background-color: var(--notification-clr-default-icon-around);
						display: flex;
						align-items: center;
						justify-content: center;
						flex-shrink: 0;
						width: 42px;
						height: 42px;
						border-radius: 6px;
						transform:translateY(2px);
					}
					.notification-icon {
						fill: var(--notification-clr-default-icon);
						width: 18px;
						height: 18px;
					}
					.notification-text-container {
						font-size: 16px;
						line-height: 1.5;
						display: flex;
						flex-direction:column;
						font-weight:500;
						align-items: center;
						color: var(--notification-clr-default-text);
						user-select:none;
					}
					.notification-text-container p {
						display:block;
						width:100%;
						margin-bottom:0;
					}
					.notification-text-container p.heading {
						font-size: 16px;
						line-height: 1.5;
						font-weight: 600;
						color: var(--notification-clr-default-heading);
					}
					.notification-text-container p.description {
						font-size: 15px;
						line-height: 1.5;
						font-weight: 500;
						color: var(--notification-clr-default-description);
					}

					.notification.success{
						background-color: var(--notification-clr-success-bg);
						border-color: var(--notification-clr-success-border);
					}
					.notification.success .notification-icon-container {
						background-color: var(--notification-clr-success-icon-around);
					}
					.notification.success .notification-icon {
						fill: var(--notification-clr-success-icon);
					}
					.notification.success p.heading {
						color: var(--notification-clr-success-heading);
					}
					.notification.success p.description {
						color: var(--notification-clr-success-description);
					}
					.notification.fail {
						background-color: var(--notification-clr-fail-bg);
						border-color: var(--notification-clr-fail-border);
					}
					.notification.fail .notification-icon-container {
						background-color: var(--notification-clr-fail-icon-around);
					}
					.notification.fail .notification-icon {
						fill: var(--notification-clr-fail-icon);
					}
					.notification.fail p.heading {
						color: var(--notification-clr-fail-heading);
					}
					.notification.fail p.description {
						color: var(--notification-clr-fail-description);
					}

					.notification .close-button {
						 background-color:transparent;
						 padding:8px;
						 border:0px solid transparent;
						 border-radius:4px;
						 position:absolute;
						 top:50%;
						 right:10px;
						 transform:translateY(-50%);
					}

					.notification .close-button svg {
						fill:var(--notification-clr-close-icon);
						width:15px;
						height:15px;
					}

					.notification .close-button:hover {
						background-color:var(--notification-clr-close-icon-around-hover);
					}

					.notification .close-button:hover svg {
						fill:var(--notification-clr-close-icon-hover);
					}

					@media (max-width: 576px) {
						.notification-container {
							width: 100%
						}
					}
				`;
	}

	function style1() {
		let notificationSpeed = this.getSpeed();
		let positionStyle = this.positionStyle();
		let revealOffset = this.getRevealOffset();
		return `
					.notification-container {
						display: flex;
						gap: 10px;
						flex-direction: column;
						justify-content: center;
						align-items: center;
						width:100%;
						max-width:320px;
						z-index: 99;
						${positionStyle}
					}
					.notification {
						background-color: var(--notification-clr-default-bg);
						display: flex;
						align-items: center;
						gap: 20px;
						max-width: 450px;
						width: 100%;
						padding: 15px 25px 15px 12px;
						border-left: 4px solid var(--notification-clr-default-border);
						border-radius: 0.6rem;
						transform: translateY(calc(100% + ${revealOffset}));
					}
					.notification.active {
						animation-name: notification-reveal;
						animation-duration: ${notificationSpeed}ms;
						animation-fill-mode: forwards;
						animation-iteration-count: 1;
					}
					.notification.inactive {
						animation-name: notification-hide;
						animation-duration: ${notificationSpeed}ms;
						animation-fill-mode: forwards;
						animation-iteration-count: 1;
					}
					.notification-icon-container {
						display: flex;
						align-items: center;
						justify-content: center;
						flex-shrink: 0;
						border-radius: 1000vmax;
						margin-left: 10px;
						margin-right: 4px;
					}
					.notification-icon {
						fill: var(--notification-clr-default-icon);
						width: 20px;
						height: 20px;
					}
					.notification-text-container {
						display: flex;
						flex-direction:column;
						align-items: center;
						user-select:none;
					}
					.notification-text-container p {
						display:block;
						width:100%;
						margin-bottom:0;
					}
					.notification-text-container p.heading {
						font-size: 16px;
						line-height: 1.5;
						font-weight: 600;
						color: var(--notification-clr-default-heading);
					}
					.notification-text-container p.description {
						font-size: 15px;
						line-height: 1.5;
						font-weight: 500;
						color: var(--notification-clr-default-description);
					}
					.notification.success{
						background-color: var(--notification-clr-success-bg);
						border-color: var(--notification-clr-success-border);
					}
					.notification.success .notification-icon {
						fill: var(--notification-clr-success-icon);
					}
					.notification.success .notification-text-container p.heading {
						color: var(--notification-clr-success-heading);
					}
					.notification.success .notification-text-container p.description {
						color: var(--notification-clr-success-description);
					}

					.notification.fail {
						color: var(--notification-clr-fail-text);
						background-color: var(--notification-clr-fail-bg);
						border-color: var(--notification-clr-fail-border);
					}
					.notification.fail .notification-text-container p.heading {
						color: var(--notification-clr-fail-heading);
					}
					.notification.fail .notification-text-container p.description {
						color: var(--notification-clr-fail-description);
					}
					.notification.fail .notification-icon {
						fill: var(--notification-clr-fail-icon);
					}


					.notification .close-button {
						 background-color:transparent;
						 padding:8px;
						 border:0px solid transparent;
						 border-radius:4px;
						 position:absolute;
						 top:50%;
						 right:10px;
						 transform:translateY(-50%);
					}

					.notification .close-button svg {
						fill:var(--notification-clr-close-icon);
						width:15px;
						height:15px;
					}

					.notification .close-button:hover {
						background-color:var(--notification-clr-close-icon-around-hover);
					}

					.notification .close-button:hover svg {
						fill:var(--notification-clr-close-icon-hover);
					}

					@media (max-width: 576px) {
						.notification-container {
							width: 100%
						}
					}
				`;
	}

	// other

	function build(obj = {}) {

		let { heading = null, text = null, description = null, time = this.getTime(), classes = [] } = obj;
		let id = this.generateId();
		let iconURL = this.getIcon(classes.length > 0 ? classes[0] : 'default');
		let closeIcon = this.getIcon('close');

		if (text !== null) heading = text;

		let html = `
					<div id="${id}" class="notification active | cursor-pointer ${classes.join(' ')}">
						<div class="notification-icon-container">
							<svg class="notification-icon">
								<use xlink:href="${iconURL}" />
							</svg>
						</div>
						<div class="notification-text-container">
							<p class="heading" style="${heading === null ? 'display:none;' : ''}">${heading}</p>
							<p class="description" style="${description === null ? 'display:none;' : ''}">${description}</p>
						</div>
						<button onclick="Notification.hide('${id}')" class="close-button | ${!this.hasCloseIcon() ? 'hide' : ''}">
							<svg>
								<use xlink:href="${closeIcon}" />
							</svg>
						</button>
					</div>
				`;

		return {
			template: html,
			data: {
				id: id,
				text: heading,
				time: time
			}
		}

	}

	function addContainer() {
		let styleName = this.getStyle();
		let container = `<div class="notification-container ${styleName}"><style>${this.style()}</style></div>`;
		document.querySelector('body').insertAdjacentHTML('afterbegin', container);

	}

	function containerExists() {
		let notificationContainer = document.querySelector('.notification-container');
		return notificationContainer !== null;
	}

	function generateId() {
		return (new Date()).getTime() + '-' + Math.floor(Math.random() * 500);
	}

	PUBLIC.show = show;
	PUBLIC.hide = hide;
	PUBLIC.hideAndShowDelayed = hideAndShowDelayed;
	PUBLIC.build = build;
	PUBLIC.getTime = getTime;
	PUBLIC.generateId = generateId;
	PUBLIC.containerExists = containerExists;
	PUBLIC.addContainer = addContainer;
	PUBLIC.style = style;
	PUBLIC.colorStyle = colorStyle;
	PUBLIC.getStyle = getStyle;
	PUBLIC.defaultStyle = defaultStyle;
	PUBLIC.style1 = style1;
	PUBLIC.revealStyle = revealStyle;
	PUBLIC.positionStyle = positionStyle;
	PUBLIC.getRevealOffset = getRevealOffset;
	PUBLIC.getRevealDirection = getRevealDirection;
	PUBLIC.getSpeed = getSpeed;
	PUBLIC.getPosition = getPosition;
	PUBLIC.setPosition = setPosition;
	PUBLIC.setStyle = setStyle;
	PUBLIC.getIcon = getIcon;
	PUBLIC.hasCloseIcon = hasCloseIcon;

	return PUBLIC;
}();