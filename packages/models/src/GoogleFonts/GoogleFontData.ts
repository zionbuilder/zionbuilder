import Model from '../Model'

export default class GoogleFontData extends Model {

	defaults() {
		return {
			font_family: '',
			font_subset: [],
			font_variants: []
		}
	}

}