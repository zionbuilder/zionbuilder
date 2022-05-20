import { createHooks } from '@wordpress/hooks';

export const hooks = createHooks();
export const { applyFilters, doAction, addAction, removeAction } = hooks;

window.zb = window.zb || {};
window.zb.hooks = hooks;
