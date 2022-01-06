import {
	deleteTemplate,
	exportTemplateById
} from '@zb/rest'
import { LibrarySource } from './LibrarySource'
import { saveAs } from 'file-saver'

export class LibraryItem {
	public id: string = ''
	public name?: string = ''
	public category: Array = []
	public thumbnail: string = ''
	public data: string = ''
	public tags: Array = []
	public edit_url: string = ''
	public preview_url: string = ''
	public type: string = ''
	public source: string = ''
	public url: string = ''
	public pro: boolean = false
	public loadingThumbnail = false

	public loading: boolean = false
	public librarySource: LibrarySource

	constructor(item: LibraryItem, librarySource: LibrarySource) {
		Object.assign(this, item)

		this.librarySource = librarySource
	}

	delete() {
		this.loading = true

		return deleteTemplate(this.id).then((response) => {
			this.librarySource.removeItem(this)

			// Allow chaining
			return Promise.resolve(response)
		}).finally(() => {
			this.loading = false
		})
	}

	export() {
		this.loading = true

		exportTemplateById(this.id).then((response) => {
			var blob = new Blob([response.data], { type: 'application/zip' })
			saveAs(blob, `${this.name}.zip`)
		})
			.finally(() => {
				this.loading = false
			})
	}

	// 	function saveThumbnailData() {

	// }
}