import Model from '../Model'

export default class Template extends Model {
	defaults() {
		return {
			ID: '',
			post_author: 1,
			post_date: '',
			post_date_gmt: '',
			post_content: '',
			post_title: '',
			post_excerpt: '',
			post_status: '',
			comment_status: '',
			ping_status: '',
			post_password: '',
			post_name: '',
			to_ping: '',
			pinged: '',
			post_modified: '',
			post_modified_gmt: '',
			post_content_filtered: '',
			post_parent: 0,
			guid: '',
			menu_order: 0,
			post_type: '',
			post_mime_type: '',
			comment_count: 0,
			filter: '',
			edit_url: '',
			preview_url: '',
			shortcode: '',
			author_name: '',
			template_type: 'template',
			template_category: []
		}
	}

	remove() {
		this.collection.remove(this)
	}

}