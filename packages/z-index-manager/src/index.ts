import { getManager } from './manager'

const instance = getManager()

export const { getZindex, removeZindex } = instance
export {
	getManager
}