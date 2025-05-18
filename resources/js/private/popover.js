function Popover() {

	function init() {
		let elemets = document.querySelectorAll('[data-popover]:not([data-popover-event-added="true"])');
		elemets.forEach(el => {
			el.setAttribute('data-popover-event-added', true);
			el.addEventListener('mouseenter', handleMouseOver);
			el.addEventListener('mouseleave', handleMouseLeave);
		});
	}

	function handleMouseOver() {
		let target = event.target;
		if (target === undefined || target === null) return;

		let rect = target.getBoundingClientRect();
		let text = target.dataset.popover;
		let direction = target.dataset.popoverDirection;
		if (!direction) direction = "top";

		let span = `
			<style>
				.popover {
					font-size: 1.25rem;
					font-weight: 400;
					line-height: 1.4;
					color: #fff;
					background-color: var(--clr-neutral-700);
					display: flex;
					align-items: center;
					justify-content: center;
					max-width: 20rem;
					padding: 0.7rem 1.6rem;
					border-radius: 1000vmax;
					z-index:9;
					pointer-events: none;
					user-select: none;
				}

				.popover-top {
					position: absolute;
					left:${rect.x}px;
					top:${rect.y}px;
					transform: translate(-30%, calc(-100% - 0.5rem));
				}

				.popover-left {
					position: absolute;
					left:${rect.x}px;
					top:${rect.y}px;
					transform: translate(-105%, 0%);
				}


			</style>
		<span class="popover popover-${direction}">${text}</span>`;
		document.querySelector('body').insertAdjacentHTML('beforeend', span);
	}

	function handleMouseLeave() {
		let popovers = document.querySelectorAll('.popover');
		popovers.forEach(p => p.remove());
	}

	return {
		init
	};

}

let popover = Popover();
popover.init();


// Info Popover

let popoverElements = document.querySelectorAll('[data-info]');

popoverElements.forEach(element => {
	element.addEventListener('mouseenter', function () {
		showPopover(event);
	})
});

function showPopover(event) {
	let element = event.target;
	let style = window.getComputedStyle(element, ':before');
	if (event.target === element && event.offsetX >= 0 && event.offsetY >= 0 &&
		event.offsetX < parseInt(style.width) && event.offsetY < parseInt(style.height)) {
	}
}