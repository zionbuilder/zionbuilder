import { default as createActionInstance } from './actions';
import { default as createFiltersInstance } from './filters';

export const { on, off, trigger } = createActionInstance();
export const { addFilter, applyFilters } = createFiltersInstance();

// Export methods for user to create their own actions and filters instances
export { createActionInstance, createFiltersInstance };
