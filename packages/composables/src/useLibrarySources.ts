import { ref, Ref } from 'vue'
import { LibrarySource, Source } from './models/LibrarySource'


const librarySources: Ref<Array<LibrarySource>> = ref([])

export const useLibrarySources = () => {
	function addSources(sources: Array<Source>) {
		sources.forEach(source => {
			addSource(source)
		});
	}

	function addSource(source: Source) {
		librarySources.value.push(new LibrarySource(source))
	}

	return {
		// Methods
		addSources,
		addSource,

		// Refs
		librarySources
	}
}