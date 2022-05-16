import './scss/main.scss';

class Tabs {
	tabLinks: Array<HTMLElement> = [];
	tabContents: Array<HTMLElement> = [];
	tabFocus = 0;

	constructor(domNode: HTMLElement) {
		this.tabLinks = Array.from(domNode.querySelectorAll('.zb-el-tabs-nav-title'));
		this.tabContents = Array.from(domNode.querySelectorAll('.zb-el-tabsItem'));

		domNode.addEventListener('click', event => this.onTabClick(event));
		domNode.addEventListener('keydown', event => this.onKeyDown(event));
	}

	onKeyDown(event: KeyboardEvent): void {
		if (event.code === 'ArrowRight') {
			this.tabLinks[this.tabFocus].tabIndex = -1;
			this.tabFocus++;

			if (this.tabFocus >= this.tabLinks.length) {
				this.tabFocus = 0;
			}

			this.tabLinks[this.tabFocus].focus();
		} else if (event.code === 'ArrowLeft') {
			this.tabFocus--;
			// If we're at the start, move to the end
			if (this.tabFocus < 0) {
				this.tabFocus = this.tabLinks.length - 1;
			}
			this.tabLinks[this.tabFocus].focus();
		} else if (event.code === 'Space' || event.code === 'Enter') {
			this.activateTab(this.tabLinks[this.tabFocus]);
		}
	}

	onTabClick(event: MouseEvent) {
		const domNode = <HTMLElement>event.target;

		if (domNode && domNode.classList.contains('zb-el-tabs-nav-title')) {
			this.activateTab(domNode);
		}
	}

	deActivateTabs() {
		[...this.tabLinks, ...this.tabContents].forEach(item => {
			item.classList.remove('zb-el-tabs-nav--active');
		});
	}

	activateTab(tab: HTMLElement) {
		this.deActivateTabs();
		tab.classList.add('zb-el-tabs-nav--active');
		// activate the tab content
		const tabIndex = this.tabLinks.indexOf(tab);

		if (tabIndex !== -1 && this.tabContents[tabIndex]) {
			this.tabContents[tabIndex].classList.add('zb-el-tabs-nav--active');
		}
	}
}

document.querySelectorAll('.zb-el-tabs').forEach(domNode => {
	new Tabs(<HTMLElement>domNode);
});
