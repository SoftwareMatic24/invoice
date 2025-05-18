var Confirmation = function () {

	let show = function (params = {}) {
		if (containerExists() === false) appendContainer();
		if (contentExists() === true) removeContent();
		appendContent(params);
	}

	let hide = function () {
		document.querySelector('.confirmation-container').remove();
	}

	let containerExists = function () {
		let container = document.querySelector('.confirmation-container');
		if (container === null) return false;
		return true;
	}

	let appendContainer = function () {
		let styelTagCode = css();
		let container = `<div class="confirmation-container container">${styelTagCode}</div>`;
		document.querySelector('body').insertAdjacentHTML('afterbegin', container);
	}

	let contentExists = function () {
		let content = document.querySelector('.confirmation-container .confirmation-content');
		if (content === null) return false;
		return true;
	}

	let removeContent = function () {
		document.querySelector('.confirmation-container .confiration-content').remove();
	}

	let appendContent = function (params) {
		let defaultPositiveButton = {
			text: CONFIRMATION_TEXTS.delete.positiveButtonText,
			classes: ['button button-danger button-sm'],
			function: function () { }
		}

		let defaultNegativeButton = {
			text: CONFIRMATION_TEXTS.delete.negativeButtonText,
			classes: ['button button-default button-sm'],
			function: function () { }
		}

		let {
			title = CONFIRMATION_TEXTS.delete.title,
			description = CONFIRMATION_TEXTS.delete.description,
			positiveButton = defaultPositiveButton,
			negativeButton = defaultNegativeButton
		} = params;


		if (positiveButton.text === undefined) positiveButton.text = defaultPositiveButton.text;
		if (positiveButton.classes === undefined) positiveButton.classes = defaultPositiveButton.classes;
		if (positiveButton.function === undefined) positiveButton.function = defaultPositiveButton.function;

		if (negativeButton.text === undefined) negativeButton.text = defaultNegativeButton.text;
		if (negativeButton.classes === undefined) negativeButton.classes = defaultNegativeButton.classes;
		if (negativeButton.function === undefined) negativeButton.function = defaultNegativeButton.function;


		let contentLayout = `
			<div class="confirmation-content">
				<p class="confirmation-title">${title}</p>
				<p class="confirmation-description">${description}</p>
				<div class="button-group">
					<button data-button="positive" class="confirmation-button ${positiveButton.classes.join(' ')}">${positiveButton.text}</button>
					<button data-button="negative" class="confirmation-button ${negativeButton.classes.join(' ')}">${negativeButton.text}</button>
				</div>
			</div>`;

		document.querySelector('.confirmation-container').insertAdjacentHTML('afterbegin', contentLayout);

		document.querySelector('.confirmation-container [data-button="positive"]').addEventListener('click', function () {
			positiveButton.function(positiveButton.params);
			hide();
		});
		document.querySelector('.confirmation-container [data-button="negative"]').addEventListener('click', function () {
			negativeButton.function(negativeButton.params);
			hide();
		});

	}

	let css = function () {
		return `
			<style>
				:where(.confirmation-button) {
					font-size: 15px;
					font-weight: 500;
					text-decoration: none;
					background-color: hsl(210, 14%, 89%);
					display: inline-flex;
					justify-content: center;
					align-items: center;
					border-radius: 4px;
					padding: 14px 28px;
					outline: none;
					border: 1px solid transparent !important;
					cursor: pointer;
					transition: all 250ms;
				}
				.confirmation-button.button-danger {
					color:#fff;
					background-color:#dc3545;
				}
				.confirmation-container{
					text-align:center;
					background-color: hsl(210, 11%, 15%);
					display:flex;
					justify-content:center;
					align-items:center;
					width:100%;
					height:100%;
					position:fixed;
					left:0;
					right:0;
					top:0;
					bottom:0;
					z-index:92;
				}
				.confirmation-content {
					width: 90%;
				}
				.confirmation-content .button-group{
					display:flex;
					gap: 10px;
					justify-content:center;
					margin-top: 40px;
				}
				.confirmation-title,
				.confirmation-description
				{
					color: hsl(210, 16%, 93%);
				}
				.confirmation-title{
					font-size: 25px;
				}
				.confirmation-description{
					font-size: 17px;
					font-weight:300;
					margin-top: 15px;
				}
			</style>
		`;
	}

	return {
		show: show,
		hide: hide
	};

}();