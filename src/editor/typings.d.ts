type ZionElementConfig = {
	uid: string;
	content: ZionElementConfig[];
	options: Record<string, unknown>;
	element_type: string;
};

type ZionElement = Omit<ZionElementConfig, 'content'> & {
	content: string[];
	parent: string | null;
	addedTime?: number;
	isHighlighted: boolean;
	delete: function;
};

type BuilderArea = {
	id: string;
	name: string;
	element: ZionElement;
};

type ZionPanel = {
	id: string;
};
