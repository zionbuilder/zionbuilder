import Collection from "../Collection";
import GoogleFont from './GoogleFont'

export default class GoogleFonts extends Collection {
	getModel () {
		return GoogleFont
	}

	getFontData (fontFamily) {
		return this.models.find((font) => {
			font.family === fontFamily
		})
	}
}