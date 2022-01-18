import { LibrarySource } from "./LibrarySource"
import {
	getTemplates,
	addTemplate as addTemplateToDB,
	deleteTemplate as deleteTemplateToDB,
	importTemplateLibrary
} from '@zb/rest'

import { remove } from 'lodash-es'

export class LocalLibrary extends LibrarySource {
	addTemplate(templateConfig) {
		this.loading = true

		return addTemplateToDB(templateConfig).then((response) => {
			if (response.data) {
				this.addItem(response.data)
			}

			// Allow chaining
			return Promise.resolve(response)
		}).finally(() => {
			this.loading = false
		})
	}

	importTemplate(templateData) {
		this.loading = true

		return importTemplateLibrary(templateData)
			.then((response) => {
				this.items.push(response.data)

				return Promise.resolve(response)
			})
			.finally(() => {
				this.loading = false
			})
	}

	deleteTemplate(templateID) {
		this.loading = true

		return deleteTemplateToDB(templateID).then((response) => {
			remove(this.items, (template) => template.id === templateID)

			// Allow chaining
			return Promise.resolve(response)
		}).finally(() => {
			this.loading = false
		})
	}
}