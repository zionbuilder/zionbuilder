import { LibrarySource } from './LibrarySource';
import { deleteLibraryItem, addLibraryItem, importLibraryItem } from '@common/api';
import { LibraryItem } from './LibraryItem';

export class LocalLibrary extends LibrarySource {
	importItem(templateData) {
		this.loading = true;

		return importLibraryItem(this.id, templateData)
			.then(response => {
				this.addItem(response.data);

				return Promise.resolve(response);
			})
			.finally(() => {
				this.loading = false;
			});
	}

	removeItem(item: LibraryItem) {
		return deleteLibraryItem(this.id, item.id).then(response => {
			super.removeItem(item);

			// Allow chaining
			return Promise.resolve(response);
		});
	}

	createItem(item) {
		this.loading = true;

		return addLibraryItem(this.id, item)
			.then(response => {
				if (response.data) {
					this.addItem(response.data);
				}

				// Allow chaining
				return Promise.resolve(response);
			})
			.finally(() => {
				this.loading = false;
			});
	}
}
