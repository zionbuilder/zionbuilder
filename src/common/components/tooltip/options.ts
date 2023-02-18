export interface TooltipOptions {
	appendTo?: string;
	placement?: string;
}

let defaultOptions: TooltipOptions = {
	appendTo: 'body',
	placement: 'top',
};

export const getDefaultOptions = () => {
	return defaultOptions;
};

export const setDefaults = (options: TooltipOptions) => {
	defaultOptions = options;
};
