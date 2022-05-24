import { default as createHooksInstance } from './hooks';

export const hooks = createHooksInstance();
export const { addAction, removeAction, doAction, addFilter, applyFilters } = hooks;

// Export methods for user to create their own actions and filters instances
export { createHooksInstance };

window.zb = window.zb || {};
window.zb.hooks = hooks;
