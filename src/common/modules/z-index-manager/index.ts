import { getManager } from './manager';

const instance = getManager();

export const { getZIndex, removeZIndex } = instance;
export { getManager };
