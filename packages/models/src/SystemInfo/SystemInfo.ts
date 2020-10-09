// import System from './System'
import Collection from "../Collection";
import { getSystemInfo } from '@zionbuilder/rest'

export default class SystemInfo extends Collection {
	getModel() {
		return getSystemInfo
	}
	get SystemInfo() {
		return []
	}
}