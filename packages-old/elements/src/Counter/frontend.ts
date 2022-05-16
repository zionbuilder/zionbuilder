import './scss/main.scss';

window.zbScripts = window.zbScripts || {};

class Counter {
	domNode: HTMLElement;
	numberContainer?: HTMLElement;

	constructor(domNode: HTMLElement) {
		this.domNode = domNode;
		this.numberContainer = <HTMLElement>this.domNode.querySelector('.zb-el-counter__number');

		// Load if in view
		const observer = new IntersectionObserver(entries => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					this.init();
					observer.unobserve(this.domNode);
				}
			});
		});

		observer.observe(this.domNode);
	}

	init() {
		const start = this.domNode.dataset.start || '0';
		const end = this.domNode.dataset.end || '100';
		const duration = this.domNode.dataset.duration || '2000';

		this.animate(
			newValue => {
				if (this.numberContainer) {
					this.numberContainer.innerHTML = '' + Math.round(newValue);
				}
			},
			parseInt(start),
			parseInt(end),
			parseInt(duration),
		);
	}

	animate(render: (x: number) => void, from: number, to: number, duration: number) {
		const startTime = performance.now();
		requestAnimationFrame(function step(time) {
			let pTime = (time - startTime) / duration;
			if (pTime > 1) pTime = 1;
			render(from + (to - from) * pTime);
			if (pTime < 1) {
				requestAnimationFrame(step);
			}
		});
	}
}

// Init the script
document.querySelectorAll('.zb-el-counter').forEach(domNode => {
	new Counter(<HTMLElement>domNode);
});

window.zbScripts.counter = Counter;
