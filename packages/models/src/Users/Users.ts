import Collection from "../Collection";
import User from './User'

export default class Users extends Collection {
	getModel() {
		return User
	}

}