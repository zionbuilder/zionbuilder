import Collection from "../Collection";
import GoogleFont from './GoogleFont'
import { getGoogleFonts } from '@zionbuilder/rest'

export default class GoogleFonts extends Collection {
	getModel () {
		return GoogleFont
	}

	get getGoogleFonts () {
		return getGoogleFonts()
	}

	getFontData (fontFamily) {
		return font = this.find({ family: fontFamily })
	}
}