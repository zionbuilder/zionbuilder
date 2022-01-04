import { ref, Ref } from 'vue'
import { LibrarySource } from './models/LibrarySource'

interface Source {
	name: string;
	url: string;
	id: string;
	last_changed: string;
}

const sources: Array<Source> = [
	{
		name: 'Local library',
		url: 'https://zionbuilder.local/wp-json/zionbuilder/v1/templates/items-and-categories',
		id: 'local_library',
		useCache: false,
		type: 'local'
	},
	{
		name: 'Zion Builder library',
		url: 'https://library.zionbuilder.io/wp-json/zionbuilder-library/v1/items-and-categories',
		id: 'zion_builder',
		type: 'zion_library'
	}
].map(source => new LibrarySource(source))

const librarySources = ref(sources)

export const useLibrarySources = () => {
	return {
		librarySources
	}
}