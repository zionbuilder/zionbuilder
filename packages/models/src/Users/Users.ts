import Collection from "../Collection";
import User from './User'
import { getUsers, getUsersById } from '@zionbuilder/rest'

export default class Users extends Collection {
	getModel() {
		return User
	}

	get getUsers() {
		return getUsers || []
	}

	getUsersById(id) {
		return getUsersById(id)
	}

}