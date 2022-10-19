import { exportLibraryItem, getLibraryItemBuilderConfig, saveLibraryItemThumbnail } from '../../api';
import { LibrarySource } from './LibrarySource';
import { saveAs } from 'file-saver';

export class LibraryItem {
	public id = '';
	public name?: string = '';
	public category: Array = [];
	public thumbnail = '';
	public data = '';
	public tags: Array = [];
	public urls: Object = {};
	public type = '';
	public source = '';
	public url = '';
	public pro = false;
	public loadingThumbnail = false;

	public loading = false;

	// Source related
	public librarySource: LibrarySource;

	constructor(item: LibraryItem, librarySource: LibrarySource) {
		Object.assign(this, item);

		this.librarySource = librarySource;
	}

	delete() {
		this.loading = true;

		return this.librarySource.removeItem(this).finally(() => {
			this.loading = false;
		});
	}

	export() {
		this.loading = true;

		return exportLibraryItem(this.librarySource.id, this.id)
			.then(response => {
				const blob = new Blob([response.data], { type: 'application/zip' });
				saveAs(blob, `${this.name}.zip`);
			})
			.finally(() => {
				this.loading = false;
			});
	}

	saveThumbnailData(data) {
		saveLibraryItemThumbnail(this.librarySource.id, this.id, data).finally(() => {
			this.librarySource.deleteCache();
		});
	}

	getBuilderData() {
		return getLibraryItemBuilderConfig(this.librarySource.id, this.id);
	}

	toJSON() {
		return {
			id: this.id,
			name: this.name,
			category: this.category,
			thumbnail: this.thumbnail,
			data: this.data,
			tags: this.tags,
			urls: this.urls,
			type: this.type,
			pro: this.pro,
			url: this.url,
		};
	}
}
