import localSt from 'localstorage-ttl'

export class LibrarySource {
	public name?: string = ''
	public id: string = ''
	public url: string = ''
	public useCache: boolean = true
	public items = []
	public categories = []
	public loading: boolean = false
	public loaded: boolean = false

	constructor(librarySource: LibrarySource) {
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
		if (useCache && this.useCache && localSt.get(`znpbLibraryCache_${this.id}`)) {
			const savedData = localSt.get(`znpbLibraryCache_${this.id}`)

			if (savedData) {
				const { items, categories } = savedData
				this.categories = categories
				this.items = items
			}
		} else {
			this.loading = true
			fetch(this.url)
				.then(response => response.json())
				.then((response) => {
					const { categories = {}, items = [] } = response
					this.categories = Object.values(categories)
					this.items = items

					// Save library data to cache
					this.saveToCache(Object.values(categories), items)
				}).finally(() => {
					// End loading
					this.loading = false
				})
		}
	}

	saveToCache(categories: Array, items: Array) {
		if (this.useCache) {
			localSt.set(`znpbLibraryCache_${this.id}`, {
				categories,
				items
			}, 604800000)
		}
	}
}