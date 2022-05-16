import AbstractEvent from './AbstractEvent'

export default class Drop extends AbstractEvent {
	static type = 'sortable:drop'
}
