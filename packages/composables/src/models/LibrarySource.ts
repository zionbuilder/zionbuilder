import localSt from 'localstorage-ttl'
import { LibraryItem } from './LibraryItem'

export interface Source {
	name: string;
	url: string;
	id: string;
	last_changed: string;
}


export class LibrarySource {
	public name?: string = ''
	public id: string = ''
	public url: string = ''
	public useCache: boolean = true
	public items: Array<LibraryItem> = []
	public categories = []
	public loading: boolean = false
	public loaded: boolean = false
	public type: string = 'remote'

	constructor(librarySource: Source) {
		Object.assign(this, librarySource)

		// Check if we have cached data for this source
		if (this.useCache) {
			const savedData = localSt.get(`znpbLibraryCache_${this.id}`)

			if (savedData) {
				const { items, categories } = savedData
				this.categories = categories
				this.items = items
			}
		}
	}

	getData(useCache = true) {
		if (this.loaded && useCache) {
			return
		} else if (useCache && this.useCache && localSt.get(`znpbLibraryCache_${this.id}`)) {
			const savedData = localSt.get(`znpbLibraryCache_${this.id}`)
			if (savedData) {
				const { items, categories } = savedData
				this.categories = categories
				this.items = this.createItemInstances(items)
				this.loaded = true
			}
		} else {
			this.loading = true
			let headers = {}

			// Add proper headers based on source
			if (this.type === 'local') {
				headers = {
					'X-WP-Nonce': window.ZnRestConfig.nonce,
					'Accept': 'application/json',
					'Content-Type': 'application/json'
				}
			}

			fetch(this.url, {
				headers
			})
				.then(response => response.json())
				.then((response) => {
					const { categories = {}, items = [] } = response
					this.categories = Object.values(categories)

					// Add library source type so we can use it on import
					this.items = this.createItemInstances(items)
					this.loaded = true

					// Save library data to cache
					if (this.useCache) {
						this.saveToCache(Object.values(categories), items)
					}
				}).finally(() => {
					// End loading
					this.loading = false
				})
		}
	}

	createItemInstances(items) {
		return items.map(item => {
			item.library_type = this.type

			return new LibraryItem(item, this)
		})
	}

	removeItem(item: LibraryItem) {
		const index = this.items.indexOf(item)

		this.items.splice(index, 1)
	}

	saveToCache(categories: Array, items: Array<LibraryItem>) {
		localSt.set(`znpbLibraryCache_${this.id}`, {
			categories,
			items
		}, 604800000)
	}
}