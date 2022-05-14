import './scss/index.scss';
import './wordpress';
import { App } from 'vue';

import { Sortable } from '@zionbuilder/sortable';

import { Tooltip, PopperDirective } from '@zionbuilder/tooltip';
import { Modal, ModalConfirm, ModalTemplateSaveButton } from '@zionbuilder/modal';
import { getDefaultGradient } from './utils';
import * as generalComponents from './components';

export * as utils from './utils';
export * from '@composables';
export * from './components';

const components = {
	Modal,
	ModalConfirm,
	ModalTemplateSaveButton,
	Tooltip,
	Sortable,
	getDefaultGradient,
	...generalComponents,
};

const install = (app: App) => {
	Object.values(components).forEach(component => {
		app.component(component.name, component);
	});

	// Add the tooltip directive
	app.directive('znpb-tooltip', PopperDirective);
};

export { install, Modal, ModalConfirm, ModalTemplateSaveButton, Tooltip, Sortable };
