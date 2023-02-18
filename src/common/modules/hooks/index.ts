import { createHooksInstance } from './hooks';

export const { addAction, removeAction, doAction, addFilter, applyFilters, on, off, trigger } = createHooksInstance();

// Export methods for user to create their own actions and filters instances
export { createHooksInstance };
