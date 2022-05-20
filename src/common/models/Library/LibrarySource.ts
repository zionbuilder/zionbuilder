import localSt from 'localstorage-ttl';
import { LibraryItem } from './LibraryItem';
import { useNotificationsStore } from '../../store';

export interface Source {
	name: string;
	url: string;
	id: string;
	last_changed: string;
}

export class LibrarySource {
	public name?: string = '';
	public id = '';
	public url = '';
	public request_headers: Array<any> = [];
	public use_cache = false;
	public items: Array<LibraryItem> = [];
	public categories = [];
	public loading = false;
	public loaded = false;
	public type = 'remote';

	constructor(librarySource: Source) {
		Object.assign(this, librarySource);
	}

	/**
	 * Fetches the data from the server or from local cache
	 *
	 * @param useCache boolean True in case you want to use the local cache or not
	 * @returns void
	 */
	getData(useCache = true) {
		if (this.loaded && useCache) {
			return;
		} else if (useCache && this.use_cache && localSt.get(`znpbLibraryCache_${this.id}`)) {
			const savedData = localSt.get(`znpbLibraryCache_${this.id}`);
			if (savedData) {
				const { items, categories } = savedData;
				this.categories = categories;
				this.setItems(items);
				this.loaded = true;
			}
		} else {
			this.loading = true;

			fetch(this.url, {
				headers: this.request_headers,
			})
				.then(response => {
					return response.json().then(data => {
						// Check if permission is ok
						if (!response.ok) {
							// Show error notification
							const { add } = useNotificationsStore();
							if (data?.message) {
								add({
									message: data.message,
									type: 'error',
									delayClose: 5000,
								});
							}

							return;
						}

						const { categories = {}, items = [] } = data;
						this.categories = Object.values(categories);

						// Add library source type so we can use it on import
						this.setItems(items);
						this.loaded = true;

						// Save library data to cache
						if (this.use_cache) {
							this.saveToCache(Object.values(categories), items);
						}
					});
				})
				.finally(() => {
					// End loading
					this.loading = false;
				});
		}
	}

	setItems(items: Array<LibraryItem>) {
		this.items = items.map(item => new LibraryItem(item, this));
	}

	removeItem(item: LibraryItem) {
		const index = this.items.indexOf(item);
		this.items.splice(index, 1);

		this.deleteCache();
	}

	/**
	 * Adds a new item to this source
	 *
	 * @param item The Object containing item data
	 * @returns {LibraryItem} The library item instance
	 */
	addItem(item: LibraryItem) {
		this.items.push(new LibraryItem(item, this));

		this.deleteCache();
	}

	saveToCache(categories: Array<any>, items: Array<LibraryItem>) {
		localSt.set(
			`znpbLibraryCache_${this.id}`,
			{
				categories,
				items,
			},
			604800000,
		);
	}

	deleteCache() {
		localStorage.removeItem(`znpbLibraryCache_${this.id}`.toString());
	}
}
