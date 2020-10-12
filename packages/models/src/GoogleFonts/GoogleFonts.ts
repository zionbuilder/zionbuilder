import Collection from "../Collection";
import GoogleFont from './GoogleFont'

export default class GoogleFonts extends Collection {
	getModel() {
		return GoogleFont
	}
	getFontData(family: String) {
		console.log('family', family)
		return this.models.find((font) => {
			font['family'] == family
		})
	}
}