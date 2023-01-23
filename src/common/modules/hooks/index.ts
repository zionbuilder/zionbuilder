import { createHooksInstance } from './hooks';

export const { addAction, removeAction, doAction, addFilter, applyFilters } = createHooksInstance();

// Export methods for user to create their own actions and filters instances
export { createHooksInstance };
