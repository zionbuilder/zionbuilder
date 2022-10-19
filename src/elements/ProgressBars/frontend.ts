import './scss/main.scss';

window.zbScripts = window.zbScripts || {};

class ProgressBars {
	constructor(domNode: HTMLElement) {
		const bars = domNode.querySelectorAll('li');

		let start = 0;

		bars.forEach(singleBar => {
			const barProgressElement = <HTMLElement>singleBar.querySelector('.zb-el-progressBars__barProgress');
			const percentage = barProgressElement.dataset.width;

			start += 0.2;
			barProgressElement.style.transitionDelay = start + 's';

			setTimeout(() => {
				barProgressElement.style.width = percentage + '%';
			});
		});
	}
}

// Init the script
document.querySelectorAll('.zb-el-progressBars').forEach(domNode => {
	new ProgressBars(<HTMLElement>domNode);
});

window.zbScripts.progressBars = ProgressBars;
