type CallbackFunction = (...args: any[]) => void;
interface Hook {
	[key: string]: CallbackFunction[];
}

// l10n
interface translateString {
	[key: string]: string;
}

interface Window {
	wp: any;
	tinyMCEPreInit: any;
	tinyMCE: any;
	quicktags: any;
	switchEditors: any;
	ZnPbInitialData: any;
	ZnPbComponentsData: Record<string, any>;
}
