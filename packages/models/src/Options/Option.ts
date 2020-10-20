import Model from '../Model'

export default class Option extends Model {
	defaults() {
		return {
			allowed_post_types: ['post', 'page'],
			google_fonts: [],
			custom_fonts: [],
			typekit_token: '',
			typekit_fonts: [],
			local_colors: [],
			global_colors: [],
			local_gradients: [],
			global_gradients: [],
			user_roles_permissions: {},
			users_permissions: {}
		}
	}

}