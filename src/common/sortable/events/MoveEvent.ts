import AbstractEvent from './AbstractEvent'

export default class MoveEvent extends AbstractEvent {
	static type = 'sortable:move'
	static cancelable = true
}
