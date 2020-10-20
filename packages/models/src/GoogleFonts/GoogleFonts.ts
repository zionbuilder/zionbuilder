import Collection from "../Collection";
import GoogleFont from './GoogleFont'

export default class GoogleFonts extends Collection {
	getModel() {
		return GoogleFont
	}
	getFontData(family: String) {
		let fontData = []

		this.models.forEach(function (font) {
			if (font['family'] == family) {
				fontData = font
			}
		});
		return fontData

	}
}