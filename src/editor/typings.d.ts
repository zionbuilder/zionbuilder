type ZionElementConfig = {
	uid: string;
	content: ZionElementConfig[];
	options: Record<string, unknown>;
	element_type: string;
};

type ZionElement = {
	uid: string;
	content: string[];
	options: Record<string, unknown>;
	element_type: string;
	content: string[];
	parent: ZionElement | null;
	parentUID: string | null;
	addedTime?: number;
	isHighlighted: boolean;
	isVisible: boolean;
	name: string;
	isCut: boolean;
	isWrapper: boolean;
	indexInParent: number;
	delete(): void;
	duplicate(): ZionElement;
	move(element: ZionElement, index: number): void;
	highlight(): void;
	wrapIn(wrapperType: string = 'container'): void;
	toJSON(): ZionElementConfig;
	getClone(): ZionElementConfig;
	unHighlight(): void;
};

type ZionElementDefinition = {
	category: string;
	// TODO: change unknown to VUE type component??
	component: null | unknown;
	content_orientation: string;
	deprecated: boolean;
	element_type: string;
	icon: string;
	is_child: boolean;
	keywords: string[];
	label: string;
	name: string;
	options: Record<string, unknown>;
	scripts: Record<string, unknown>;
	show_in_ui: boolean;
	style_elements: Record<string, unknown>;
	styles: Record<string, unknown>;
	thumb: string;
	wrapper: boolean;
};

type BuilderArea = {
	id: string;
	name: string;
	element?: ZionElement;
};

type ZionPanel = {
	id: string;
};
