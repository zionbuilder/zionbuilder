import { getSystemInfo } from '@zionbuilder/rest'
import Model from '../Model'

export default class System extends Model {

	fetch() {
		getSystemInfo.then((response) => {
			this.collection.add(response.data)
		})
	}
}