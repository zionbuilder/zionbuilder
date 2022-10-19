import AbstractEvent from './AbstractEvent'

export default class Start extends AbstractEvent {
	static type = 'sortable:start'
	static cancelable = true
}
