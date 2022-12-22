type App = import('vue').App;

interface PluginInfo {
	is_active: boolean;
	version: string | null;
	update_version: string | null;
	is_installed?: boolean;
}

// Library interfaces
interface LibraryItem {
	id: number;
	name: string;
	status: string;
	author: string;
	shortcode: string;
	category: number[];
	thumbnail: string;
	thumbnail_failed: boolean;
	date: string;
	tags: string[];
	urls: {
		edit_url: string;
		preview_url: string;
		screenshot_generation_url: string;
	};
	type: string;
	loadingThumbnail: boolean;
	pro: boolean;
	loading: boolean;
	librarySource: LibrarySource;
	delete: () => Promise<void>;
	export: () => Promise<void>;
}

interface LibrarySource {
	name: string;
	id: string;
	type: string;
	use_cache: boolean;
	url: string;
	request_headers: Record<string, string>;
}

interface LibraryCategory {
	name: string;
	slug: string;
	term_id: number;
	term_id: number;
	isActive: boolean;
	parent?: number;
	subcategories?: LibraryCategory[];
	count: number;
}
