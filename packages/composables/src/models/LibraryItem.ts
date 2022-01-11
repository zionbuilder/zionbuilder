import {
	deleteTemplate,
	exportTemplateById,
	saveThumbnailData as saveThumbnailDataRest
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
	public library_type: string = ''

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

	saveThumbnailData(data) {
		saveThumbnailDataRest(this.id, data)
	}

	toJSON() {
		return {
			id: this.id,
			name: this.name,
			category: this.category,
			thumbnail: this.thumbnail,
			data: this.data,
			tags: this.tags,
			edit_url: this.edit_url,
			preview_url: this.preview_url,
			type: this.type,
			source: this.source,
			pro: this.pro,
			url: this.url,
			library_type: this.library_type
		}
	}
}