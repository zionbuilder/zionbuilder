import AbstractEvent from './AbstractEvent'

export default class ChangeEvent extends AbstractEvent {
	static type = 'sortable:change'
	static cancelable = true
}
