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
};

type BuilderArea = {
	id: string;
	name: string;
};

type ZionPanel = {
	id: string;
};
