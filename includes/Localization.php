<?php

namespace ZionBuilder;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Localization
 * Localization class
 *
 * Will handle all interactions with the WordPress admin area and the pagebuilder
 *
 * @package ZionBuilder
 */
class Localization {
	/**
	 * Get strings
	 *
	 * @return array<string> The list of translatable strings
	 */
	public static function get_strings() {
		return apply_filters(
			'zionbuilder/localization/strings',
			[
				// Options panel
				'no_options_found'                         => esc_html__( 'No options found', 'zionbuilder' ),
				'element_has_no_specific_options'          => esc_html__( 'Element has no specific options', 'zionbuilder' ),

				// Free vs pro
				'manage_users_permissions_free'            => esc_html__( 'Want to give control to specific users?', 'zionbuilder' ),
				'manage_users_permissions_title'           => esc_html__( 'specific users control', 'zionbuilder' ),
				'manage_users_permissions_access_free'     => esc_html__( 'Want to give access only to content, specific post type or builder feature?', 'zionbuilder' ),
				'pro_manage_global_colors_free_title'      => esc_html__( 'Meet Global Colors', 'zionbuilder' ),
				'pro_manage_global_gradients_free_title'   => esc_html__( 'Meet Global Gradients', 'zionbuilder' ),
				'pro_manage_global_colors_free'            => esc_html__( 'Global colors allows you to define a color that you can use in builder, and everytime this color changes it will be updated automatically in all locations where it was used. ', 'zionbuilder' ),
				'pro_manage_global_gradients_free'         => esc_html__( 'Global gradients allows you to define a gradient configuration that you can use in builder, and everytime this gradient configuration changes it will be updated automatically in all locations where it was used. ', 'zionbuilder' ),
				'custom_fonts_upgrade_message'             => esc_html__( 'With PRO you can upload your own sets of fonts and assign it to your page elements.', 'zionbuilder' ),
				'typekit_fonts_upgrade_message'            => esc_html__( 'With PRO you can use the Adobe fonts library to add your fonts along side Google fonts and custom fonts.', 'zionbuilder' ),

				'custom_icons_upgrade_message'             => sprintf(
					/* translators: %s is the whitelabel name; */
					_x( '%s PRO lets you upload your own icons in addition to the Font Awesome icons that everyone is using.', 'zionbuilder' ),
					Whitelabel::get_title()
				),
				'pro_features'                             => esc_html__( 'With PRO you will have additional control over your pages, create reusable sections and elements, have dynamic data, additional elements, additional options to existing elements and many more features.', 'zionbuilder' ),
				'learn_more_about_pro'                     => esc_html__( 'Click here to learn more about PRO.', 'zionbuilder' ),

				// WP related
				'ajax_error'                               => esc_html__( 'There was a problem retrieving the templates', 'zionbuilder' ),
				'templates_not_found'                      => esc_html__( 'No templates found.', 'zionbuilder' ),
				'preview'                                  => esc_html__( 'preview', 'zionbuilder' ),
				'view_post'                                => esc_html__( 'Preview post', 'zionbuilder' ),
				'upgrade_to_pro_now'                       => esc_html__( 'Upgrade to PRO now!', 'zionbuilder' ),
				'pro'                                      => esc_html__( 'pro', 'zionbuilder' ),
				'documentation'                            => esc_html__( 'Documentation', 'zionbuilder' ),
				// get pro
				'upgrade_to_pro'                           => esc_html__( 'Upgrade to PRO', 'zionbuilder' ),
				// general
				'local'                                    => esc_html__( 'Local', 'zionbuilder' ),
				'global'                                   => esc_html__( 'Global', 'zionbuilder' ),

				// Admin Menu
				'templates'                                => esc_html__( 'Templates', 'zionbuilder' ),
				'settings'                                 => esc_html__( 'Settings', 'zionbuilder' ),
				'system_info'                              => esc_html__( 'System Info', 'zionbuilder' ),
				'general_settings'                         => esc_html__( 'General Settings', 'zionbuilder' ),
				'allowed_post_types'                       => esc_html__( 'Allowed Post types', 'zionbuilder' ),
				'font_options'                             => esc_html__( 'Font Options', 'zionbuilder' ),
				'google_fonts'                             => esc_html__( 'Google Fonts', 'zionbuilder' ),
				'custom_fonts'                             => esc_html__( 'Custom Fonts', 'zionbuilder' ),
				'typekit_fonts'                            => esc_html__( 'Adobe Fonts', 'zionbuilder' ),
				'custom_icons'                             => esc_html__( 'Custom Icons', 'zionbuilder' ),
				'color_presets'                            => esc_html__( 'Color Presets', 'zionbuilder' ),
				'gradients'                                => esc_html__( 'Gradients', 'zionbuilder' ),
				'presets'                                  => __( 'Presets', 'zionbuilder' ),
				'replace_url'                              => esc_html__( 'Replace URL', 'zionbuilder' ),
				'general'                                  => esc_html__( 'General', 'zionbuilder' ),

				// Tools
				'tools'                                    => esc_html__( 'Tools', 'zionbuilder' ),
				'regenerate_files'                         => esc_html__( 'Regenerate Files', 'zionbuilder' ),
				'regenerate_css'                           => esc_html__( 'Regenerate CSS & JS', 'zionbuilder' ),
				'sync_library'                             => esc_html__( 'Sync Library', 'zionbuilder' ),
				'old_url'                                  => esc_html__( 'old Url', 'zionbuilder' ),
				'new_url'                                  => esc_html__( 'New Url', 'zionbuilder' ),

				'regenrate_info'                           => sprintf(
					/* translators: %s is the whitelabel library name; */
					_x( '%s Library automatically updates on a daily basis. You can also manually update it by clicking on the sync button.', 'zionbuilder' ),
					Whitelabel::get_title()
				),

				'tools_info'                               => sprintf(
					/* translators: %s is the whitelabel plugin name */
					_x( 'Styles set in %s are saved in CSS files in the uploads folder. Recreate those files, according to the most recent settings.', 'zionbuilder' ),
					Whitelabel::get_title()
				),
				'replace_info'                             => __( '<strong>Important:</strong> It is strongly recommended that you <a href="https://zionbuilder.io/documentation/replace-url-s/">backup your database</a> before using Replace URL.', 'zionbuilder' ),

				// System info
				'system_info_desc'                         => esc_html__( 'Scroll down to copy paste the Info shown', 'zionbuilder' ),

				// Templates
				'all'                                      => esc_html__( 'All', 'zionbuilder' ),
				'yes'                                      => esc_html__( 'Yes', 'zionbuilder' ),
				'no'                                       => esc_html__( 'No', 'zionbuilder' ),
				'published'                                => esc_html__( 'Published', 'zionbuilder' ),
				'drafts'                                   => esc_html__( 'Drafts', 'zionbuilder' ),
				'trash'                                    => esc_html__( 'Trash', 'zionbuilder' ),
				'saved_elements'                           => esc_html__( 'Saved Elements', 'zionbuilder' ),
				'smart_areas'                              => esc_html__( 'Smart Areas', 'zionbuilder' ),
				'title'                                    => esc_html__( 'Title', 'zionbuilder' ),
				'author'                                   => esc_html__( 'Author', 'zionbuilder' ),
				'shortcode'                                => esc_html__( 'Shortcode', 'zionbuilder' ),
				'add_new_template'                         => esc_html__( 'Add new template', 'zionbuilder' ),
				'no_template'                              => esc_html__( 'No template', 'zionbuilder' ),
				'template_type'                            => esc_html__( 'Template type', 'zionbuilder' ),
				'template_name'                            => esc_html__( 'Template Name', 'zionbuilder' ),
				// Template actions
				'action_edit'                              => esc_html__( 'Edit', 'zionbuilder' ),
				'action_conditions'                        => esc_html__( 'Conditions', 'zionbuilder' ),
				'action_delete'                            => esc_html__( 'Delete', 'zionbuilder' ),
				'action_export'                            => esc_html__( 'Export', 'zionbuilder' ),
				'action_preview'                           => esc_html__( 'Preview', 'zionbuilder' ),
				'template_delete_confirm'                  => esc_html__( 'Yes, delete template', 'zionbuilder' ),
				'template_delete_cancel'                   => esc_html__( 'No, keep template', 'zionbuilder' ),
				'are_you_sure_template_delete'             => esc_html__( 'Are you sure you want to delete this template?', 'zionbuilder' ),
				'template_was_added'                       => esc_html__( 'The template was successfully added to library', 'zionbuilder' ),
				'success'                                  => esc_html__( 'success', 'zionbuilder' ),
				'select_template'                          => esc_html__( 'Select a template', 'zionbuilder' ),
				'select_type'                              => esc_html__( 'Select type', 'zionbuilder' ),
				'type_name'                                => esc_html__( 'Type a name for the new template', 'zionbuilder' ),
				'enter_name_for_template'                  => esc_html__( 'Enter a name for this template', 'zionbuilder' ),

				// Conditions
				'display_conditions'                       => esc_html__( 'Display Conditions', 'zionbuilder' ),
				// General
				'no_google_fonts'                          => esc_html__( 'No Google fonts added', 'zionbuilder' ),
				'search_for_fonts'                         => esc_html__( 'Search for fonts...', 'zionbuilder' ),
				// Google fonts
				'font_name'                                => esc_html__( 'Font name', 'zionbuilder' ),
				'variants'                                 => esc_html__( 'variants', 'zionbuilder' ),
				'subsets'                                  => esc_html__( 'subsets', 'zionbuilder' ),
				'actions'                                  => esc_html__( 'actions', 'zionbuilder' ),
				'add_font'                                 => esc_html__( 'Add Font', 'zionbuilder' ),
				'are_you_sure_google_font_delete'          => esc_html__( 'Are you sure you want to delete this font?', 'zionbuilder' ),
				'click_to_delete_font'                     => esc_html__( 'Delete font?', 'zionbuilder' ),
				'font_delete_confirm'                      => esc_html__( 'Yes, delete the font', 'zionbuilder' ),
				'font_delete_cancel'                       => esc_html__( 'No, keep the font', 'zionbuilder' ),
				// Custom fonts
				'save'                                     => esc_html__( 'Save', 'zionbuilder' ),
				'font_weight'                              => esc_html__( 'Font Weight', 'zionbuilder' ),
				'woff_file'                                => esc_html__( '.woff file', 'zionbuilder' ),
				'woff2_file'                               => esc_html__( '.woff2 file', 'zionbuilder' ),
				'ttf_file'                                 => esc_html__( '.ttf file', 'zionbuilder' ),
				'svg_file'                                 => esc_html__( '.svg file', 'zionbuilder' ),
				'eot_file'                                 => esc_html__( '.eot file', 'zionbuilder' ),
				// Icon manager
				'no_icons'                                 => esc_html__( 'No icons added', 'zionbuilder' ),

				'icon_pack'                                => esc_html__( 'Icon pack', 'zionbuilder' ),
				'search_for_icons'                         => esc_html__( 'Search through', 'zionbuilder' ),
				'icons'                                    => esc_html__( 'icons', 'zionbuilder' ),
				'no_icons_in_package'                      => esc_html__( 'No icons were found in package', 'zionbuilder' ),
				'select_icon'                              => esc_html__( 'Select an icon', 'zionbuilder' ),
				'add_new_item'                             => esc_html__( 'Add new item', 'zionbuilder' ),

				// Typekit fonts

				'typekit_api_description'                  => sprintf(
					'%s <a href="https://fonts.adobe.com/account/tokens" target="_blank">%s</a>. %s',
					esc_html__( 'Please provide the token you got from the', 'zionbuilder' ),
					esc_html__( 'Adobe fonts website', 'zionbuilder' ),
					esc_html__( 'After adding a valid token, your web projects will appear bellow.', 'zionbuilder' )
				),
				'submit'                                   => esc_html__( 'submit', 'zionbuilder' ),
				'loading'                                  => esc_html__( 'Loading', 'zionbuilder' ),

				// Color presets
				'add_color'                                => esc_html__( 'Add color', 'zionbuilder' ),

				// Gradient presets
				'add_gradient'                             => esc_html__( 'Add Gradient', 'zionbuilder' ),
				'gradient_type'                            => esc_html__( 'Gradient type', 'zionbuilder' ),
				'gradient_angle'                           => esc_html__( 'Gradient angle', 'zionbuilder' ),
				'gradient_bar'                             => esc_html__( 'Gradient bar', 'zionbuilder' ),
				'click_to_add_gradient_point'              => esc_html__( 'Click to add gradient point', 'zionbuilder' ),
				'global_colors_availability'               => esc_html__( 'Global colors are available in', 'zionbuilder' ),
				'save_gradient_title'                      => __( 'name', 'zionbuilder' ),

				// System info
				'copy_paste'                               => esc_html__( 'Copy and paste info', 'zionbuilder' ),
				'copy_paste_description'                   => esc_html__( 'You can copy the below info as simple text with Ctrl+C / Ctrl+V:', 'zionbuilder' ),
				// Permissions tab
				'role_manager'                             => esc_html__( 'Role manager', 'zionbuilder' ),
				'user_specific'                            => esc_html__( 'User specific permissions', 'zionbuilder' ),
				'add_user'                                 => esc_html__( 'Add a User', 'zionbuilder' ),
				'access_to_editor'                         => esc_html__( 'Access to editor', 'zionbuilder' ),
				'post_types'                               => esc_html__( 'Post types', 'zionbuilder' ),
				'features'                                 => esc_html__( 'Features', 'zionbuilder' ),
				'content'                                  => esc_html__( 'Content', 'zionbuilder' ),
				'access_to_all_editor'                     => esc_html__( 'Access to editor', 'zionbuilder' ),
				'edit_only_content'                        => esc_html__( 'Edit only Content', 'zionbuilder' ),
				'permissions'                              => esc_html__( 'Permissions', 'zionbuilder' ),
				'no_user_added'                            => esc_html__( 'no user added yet', 'zionbuilder' ),
				'search_for_users'                         => esc_html__( 'Search for users', 'zionbuilder' ),
				'search_description'                       => esc_html__( 'Type in the search below to find an user and press enter to add it.', 'zionbuilder' ),

				/*
				 * Frontend Strings
				 */
				// General texts
				'generic_add_new'                          => __( 'Add new', 'zionbuilder' ),
				'generating_preview'                       => __( 'Generating preview...', 'zionbuilder' ),
				// element options
				'edit_element'                             => __( 'Edit Element', 'zionbuilder' ),
				'save_element'                             => __( 'Save Element ', 'zionbuilder' ),
				'rename_element'                           => __( 'Rename Element', 'zionbuilder' ),
				'visible_element'                          => __( 'Hide Element ', 'zionbuilder' ),
				'show_element'                             => __( 'Show Element ', 'zionbuilder' ),
				'hidden_element'                           => __( 'Show Element ', 'zionbuilder' ),
				'copy_element_style'                       => __( 'Copy element style', 'zionbuilder' ),
				'cut_element_style'                        => __( 'Cut Element', 'zionbuilder' ),
				'delete_element'                           => __( 'Delete Element', 'zionbuilder' ),
				'open'                                     => __( 'Open', 'zionbuilder' ),
				'close'                                    => __( 'Close', 'zionbuilder' ),
				'toolbox'                                  => __( 'Toolbox', 'zionbuilder' ),
				// library save element
				'library'                                  => __( 'Library', 'zionbuilder' ),
				'save_to_library'                          => __( 'Save to library', 'zionbuilder' ),
				'choose_title'                             => __( 'Choose a title', 'zionbuilder' ),
				'save_title_desc'                          => __( 'Write a suggestive name for your element', 'zionbuilder' ),
				'create'                                   => __( 'Create', 'zionbuilder' ),
				'download'                                 => __( 'Download', 'zionbuilder' ),
				// panel name
				'history_panel'                            => __( 'History', 'zionbuilder' ),
				'add_elements_panel'                       => __( 'Add elements', 'zionbuilder' ),
				'library_panel'                            => __( 'Library', 'zionbuilder' ),
				'tree_view_panel'                          => __( 'Tree view panel', 'zionbuilder' ),
				'global_css_classes'                       => __( 'Global CSS classes', 'zionbuilder' ),
				'global_settings_panel'                    => __( 'Options', 'zionbuilder' ),
				//elements
				'search_elements'                          => __( 'Search elements', 'zionbuilder' ),
				//tree panel subcomponent
				'tree_view'                                => __( 'Tree view', 'zionbuilder' ),
				'section_view'                             => __( 'Section view', 'zionbuilder' ),
				'wireframe_view'                           => __( 'Wireframe', 'zionbuilder' ),
				'editable_name'                            => __( 'editable name', 'zionbuilder' ),
				'collapse_all'                             => __( 'Collapse all', 'zionbuilder' ),
				'expand_all'                               => __( 'Expand all', 'zionbuilder' ),
				'remove_all'                               => __( 'Remove all', 'zionbuilder' ),
				'removed_all_elements'                     => __( 'Removed all elements', 'zionbuilder' ),

				//Save actions
				'page_saved_publish'                       => __( 'This page was successfully saved and published', 'zionbuilder' ),
				'page_saved'                               => __( 'This page was successfully saved', 'zionbuilder' ),
				// Element options
				'options'                                  => __( 'Options', 'zionbuilder' ),
				// search no elements
				'no_elements_found'                        => __( 'No elements found matching the search criteria', 'zionbuilder' ),
				'delete_class_tooltip'                     => __( 'Remove class from element.', 'zionbuilder' ),
				// Input Image
				'custom_width'                             => __( 'Custom Width', 'zionbuilder' ),
				'custom_height'                            => __( 'Custom Height', 'zionbuilder' ),
				// Input Date
				'days'                                     => __( 'Days', 'zionbuilder' ),
				'hour'                                     => __( 'Hours', 'zionbuilder' ),
				'minutes'                                  => __( 'Minutes', 'zionbuilder' ),
				'seconds'                                  => __( 'Seconds', 'zionbuilder' ),
				'next_month'                               => __( 'Next Month', 'zionbuilder' ),
				'previous_month'                           => __( 'Previous month', 'zionbuilder' ),
				'set_time'                                 => __( 'Set time', 'zionbuilder' ),
				'monday'                                   => __( 'Mon', 'zionbuilder' ),
				'tuesday'                                  => __( 'Tue', 'zionbuilder' ),
				'wednesday'                                => __( 'Wed', 'zionbuilder' ),
				'thursday'                                 => __( 'Thu', 'zionbuilder' ),
				'friday'                                   => __( 'Fri', 'zionbuilder' ),
				'saturday'                                 => __( 'Sat', 'zionbuilder' ),
				'sunday'                                   => __( 'Sun', 'zionbuilder' ),
				'jan'                                      => __( 'January', 'zionbuilder' ),
				'feb'                                      => __( 'February', 'zionbuilder' ),
				'mar'                                      => __( 'March', 'zionbuilder' ),
				'apr'                                      => __( 'April', 'zionbuilder' ),
				'may'                                      => __( 'May', 'zionbuilder' ),
				'jun'                                      => __( 'June', 'zionbuilder' ),
				'jul'                                      => __( 'July', 'zionbuilder' ),
				'aug'                                      => __( 'August', 'zionbuilder' ),
				'sep'                                      => __( 'September', 'zionbuilder' ),
				'oct'                                      => __( 'October', 'zionbuilder' ),
				'nov'                                      => __( 'November', 'zionbuilder' ),
				'dec'                                      => __( 'December', 'zionbuilder' ),

				// Library
				'search_library'                           => __( 'Search in this library', 'zionbuilder' ),
				'testimonials'                             => __( 'Testimonials', 'zionbuilder' ),
				'import'                                   => __( 'Import', 'zionbuilder' ),
				'favorites'                                => __( 'Favorites', 'zionbuilder' ),
				'my_favorites'                             => __( 'My favorites', 'zionbuilder' ),
				'sort'                                     => __( 'Sort', 'zionbuilder' ),
				'go_back'                                  => __( 'Go Back', 'zionbuilder' ),
				'pages'                                    => __( 'Pages', 'zionbuilder' ),
				'no_more_to_show'                          => __( 'No more to show :(', 'zionbuilder' ),
				'drag_drop'                                => __( 'Drag and drop your exported item here or just click to ', 'zionbuilder' ),
				'browse'                                   => __( 'browse', 'zionbuilder' ),
				'for_files'                                => __( 'for files', 'zionbuilder' ),
				'drop_files'                               => __( 'Drop your files', 'zionbuilder' ),
				'to_upload'                                => __( 'to upload', 'zionbuilder' ),
				'fancy_title'                              => __( 'My fancy title', 'zionbuilder' ),
				'enter_new_category'                       => __( 'Enter new category', 'zionbuilder' ),
				'select_category'                          => __( 'Select category', 'zionbuilder' ),
				// CssSelectorDropdown
				'no_class_found'                           => __( 'No class found. Press "Add class" to create a new class and assign it to the element.', 'zionbuilder' ),
				'add_class'                                => __( 'Add Class', 'zionbuilder' ),
				'class_name_exists'                        => __( 'This class name already exists', 'zionbuilder' ),
				'invalid_class_name'                       => __( 'Invalid class name, classes must not start with numbers and cannot contain spaces', 'zionbuilder' ),
				'no_elements'                              => __( 'No elements found', 'zionbuilder' ),
				'new_pseudo'                               => __( 'Add new pseudoselector ex: :hover::before ', 'zionbuilder' ),

				//GlobalClasses
				'no_global_class_found'                    => __( 'No class found', 'zionbuilder' ),

				//keyShortcutsModal
				'when_dragging_input_number'               => __( 'On input of type number', 'zionbuilder' ),
				'on_input_number_link'                     => __( 'On input of type number with link option available', 'zionbuilder' ),
				'save_changes'                             => __( 'Save changes', 'zionbuilder' ),
				'cut_element'                              => __( 'Cut', 'zionbuilder' ),
				'discard_element_styles'                   => __( 'Discard styles', 'zionbuilder' ),

				//About Modal
				'need_help'                                => __( 'Do you need help?', 'zionbuilder' ),
				'free'                                     => __( 'FREE', 'zionbuilder' ),
				'view_changelog'                           => __( 'View changelog', 'zionbuilder' ),
				'update_to_version'                        => __( 'Update to version', 'zionbuilder' ),
				'buy_pro'                                  => __( 'Buy Pro', 'zionbuilder' ),
				'activate_pro'                             => __( 'Activate PRO', 'zionbuilder' ),
				'not_installed'                            => __( 'Not installed!', 'zionbuilder' ),
				'you_are_upto_date'                        => __( 'You are up to date!', 'zionbuilder' ),
				'about_zion_description'                   => __( 'Producing <b>smashing design</b> is now possible with Zion Builder. <br/>Complex elements, library system, responsive building design, multilanguage adaptability, speed and performance, control not only over the actions but also over the whole website, and powerful blog options are barely few of the features for this <b> blue-chip </b> plugin. <br/><br/>Choose the version that fits your needs, as Zion Builder offers you the possibility to <b> build a website in no-time </b>even if just the free version is active.', 'zionbuilder' ),

				//Console messages
				'oops_something_wrong'                     => esc_html__( 'oops, something went wrong!', 'zionbuilder' ),

				// PanelElementOptions
				'element_options_default_message'          => __( 'Start typing in the search field and the found options will appear here', 'zionbuilder' ),
				'styles'                                   => __( 'Styles', 'zionbuilder' ),
				'advanced'                                 => __( 'Advanced', 'zionbuilder' ),

				// history
				'history_now'                              => esc_html__( 'Now', 'zionbuilder' ),
				'initial_state'                            => esc_html__( 'Initial State', 'zionbuilder' ),

				// LibraryModal

				'zion_library'                             => sprintf(
					/* translators: %s is the whitelabel library name */
					_x( '%s Library', 'zionbuilder' ),
					Whitelabel::get_title()
				),
				'library_insert'                           => __( 'Insert', 'zionbuilder' ),
				'local_library'                            => __( 'Local Library', 'zionbuilder' ),
				'library_insert_tooltip'                   => __( 'Insert this item into page', 'zionbuilder' ),
				'library_click_preview_tooltip'            => __( 'Click to preview this item', 'zionbuilder' ),
				'library_add_favorite_tooltip'             => __( 'Add this item to favorites', 'zionbuilder' ),
				'refresh_tooltip'                          => __( 'Refresh data from the server ', 'zionbuilder' ),

				// Tour Modal
				'take_tour'                                => __( 'Take the tour', 'zionbuilder' ),
				'next_step'                                => __( 'Next Step', 'zionbuilder' ),
				'previous_step'                            => __( 'Previous Step', 'zionbuilder' ),
				'end_tour'                                 => __( 'End tour', 'zionbuilder' ),
				'welcome_to_zion'                          => __( 'Welcome to Zion Builder', 'zionbuilder' ),
				'welcome_to_zion_description'              => __( 'Press the <b>"Take the Tour"</b> button to take a smooth ride through the main features of the page builder. You must follow and click on the <b>highlighted hints </b>or press <b>"Next Step"</b>. <br/><br/><br/>Press <b>"x"</b> or <b>"End tour"</b> button to close tour, and immediatly start creating. Our page builder is highly intuitive, so you will be able to build a page in no-time.', 'zionbuilder' ),
				'adding_elements'                          => __( 'Adding elements', 'zionbuilder' ),
				'adding_elements_description'              => __( 'The <b>colored circle with "+" sign </b> appears each time you hover an element. By clicking on it, a popup will appear from which you can add elements to the page.<br/> <br/>Click on the colored circle that contains <b>"+ icon"</b> to add elements or templates to page', 'zionbuilder' ),
				'elements_popup'                           => __( 'Click the highlighted tab', 'zionbuilder' ),
				'elements_popup_description'               => __( 'The "add elements" popup has 3 main Tabs: <b>"Columns Tab"</b>, <b>"Elements Tab"</b> and <b>"Library Tab"</b>. <br /><br />From the columns tab you can immediately add a predefined set of columns. The builder is inteligent enough to know if it should also insert a section or a wrapper column to contain the selected column layout.', 'zionbuilder' ),
				'elements_tab'                             => __( 'Go to library tab', 'zionbuilder' ),
				'elements_tab_description'                 => __( 'From the <b>Elements tab</b> you can add new elements in the position from which you\'ve pressed the plus sign. They can be easily searched by introducing their name in the search box, or just by scrolling through them. <br/><br/>Now, click on the highlighted Library tab to see the link to the Library.', 'zionbuilder' ),
				'columns_tab'                              => __( 'Go to columns tab', 'zionbuilder' ),
				'columns_tab_description'                  => __( 'From the Library tab you can choose from hundreds of pre-made templates.<br/> <br/>Now lets add some elements to the page. Go back to add <b>"Columns"</b> in order to add a column template to your page. </br><b>Click on the highlighted "Columns tab"</b>', 'zionbuilder' ),
				'add_template_to_page'                     => __( 'Add a template to your page', 'zionbuilder' ),
				'add_template_to_page_description'         => __( 'Choose highlighted template to add to your page.<br/> Since you are inserting this as a root element, adding this template will create a section with a column inside.', 'zionbuilder' ),
				'edit_element_options'                     => __( 'Edit Element options', 'zionbuilder' ),
				'edit_element_options_description'         => __( 'Opening the settings panel for each element, can be made through multiple actions: <br/><ul><li>- DoubleClick an element </li><li>- Rightclick on the element and choose edit </li><li>- Clicking on edit toolbar from the top right of each element </li></ul> <br/><br/> Now,<b> double click </b> on the element to open the panel settings', 'zionbuilder' ),
				'panel_element_options'                    => __( 'Click the highlighted tab', 'zionbuilder' ),
				'panel_element_options_description'        => __( 'This panel has 3 main tabs: "General", "Styling" and "Advanced".<br/>The "General Tab" is now active. Inside <b>"General Tab"</b> you can edit the content and other general settings of the element. This Tab content will varify from element to element. <br/> <br/>Click on the highlighted "Styling" Tab to continue the tour', 'zionbuilder' ),
				'styling_tab'                              => __( 'Go to Advanced Tab', 'zionbuilder' ),
				'styling_tab_description'                  => __( 'The element wrapper style rules are created within this tab. The typography, background, display and many more styling options are powerful tools to style your elements.<br/> If you click on the element id,an input will appear for entering and creating re-usable classes. The blue button gives the ability to change the state of the class and access the pseudoselectors such as hover, after, before. This tab content is the same for all the elements.<br/><br/>Now click on the highlighted "Advanced Tab" to see the tab content and get more explanations.', 'zionbuilder' ),
				'advanced_tab'                             => __( 'Close the panel', 'zionbuilder' ),
				'advanced_tab_description'                 => __( 'The active tab is the <b>"Advanced tab"</b>. Changing the name, adding a unique Id, animations, custom Css,or element visibility could be done from the advanced tab. This tab settings are also the same for all the elements. <br/><br/>Click on the top right <b>"x"</b> button to close the panel', 'zionbuilder' ),
				'choose_device'                            => __( 'Choose device', 'zionbuilder' ),
				'choose_device_description'                => __( 'Create design for the desired device. When you hover the icon, the other devices flyout will appear. If you choose laptop or mobile and set options from the panel, your webpage will look exactly as in preview.', 'zionbuilder' ),
				'actions_history'                          => __( 'Open the panels', 'zionbuilder' ),
				'actions_history_description'              => __( 'From the main bar, you can open and close treeview, history, library and general settings panels. <br/><br/> Now click on the icon to see the history of your actions', 'zionbuilder' ),
				'close_from_mainbar'                       => __( 'Close the panel from main bar', 'zionbuilder' ),
				'close_from_mainbar_description'           => __( 'When a panel is open, the background of the panel icon becomes darker.<br/><br/> CLick on the icon to close the panel.', 'zionbuilder' ),
				'open_treeview_panel'                      => __( 'Open treeview panel', 'zionbuilder' ),
				'open_treeview_panel_description'          => __( 'Click on the icon to see the treeview panel of your page', 'zionbuilder' ),
				'save_your_work'                           => __( 'Save your work', 'zionbuilder' ),
				'save_your_work_description'               => __( 'The treeview panel shows the simplified elements from the page. You cand add elements in the treeview and edit them just like in the page editor. <br/><br/>Now, click on the bottom icon to <b>save</b> the page. If you just hover the icon, a flyout will appear with multiple saving types such as: <b>"draft", "template" or "publish"</b><br/> Saving can also be done with keyshortcuts <b>"CTRL + S"</b>', 'zionbuilder' ),
				'tour_was_ended'                           => __( 'Tour was ended', 'zionbuilder' ),
				'tour_was_ended_description'               => __( 'If you missed something, you can go back anytime to the “Info Icon” > “Start Tour”. To restart tour, you must be on a new empty page. <br/><br/>Good luck in building the future!', 'zionbuilder' ),

				// Post Lock
				'post_could_not_lock'                      => __( 'Could not lock current post', 'zionbuilder' ),
				'post_preview'                             => __( 'Preview', 'zionbuilder' ),
				'post_go_back'                             => __( 'Go back', 'zionbuilder' ),
				'post_take_over'                           => __( 'Take Over', 'zionbuilder' ),

				// Background color
				'select_background_color'                  => __( 'Select background color', 'zionbuilder' ),
				// Background gradient
				'no_local_gradients'                       => __( 'No local gradients were found', 'zionbuilder' ),
				'no_global_gradients'                      => __( 'No global gradients were found', 'zionbuilder' ),

				// Background repeat
				'background_repeat'                        => __( 'Background Repeat', 'zionbuilder' ),
				'background_repeat_description'            => __( 'Description for background repeat', 'zionbuilder' ),
				'select_background_repeat'                 => __( 'Select background repeat', 'zionbuilder' ),
				'select_background_attachement'            => __( 'Select background attachment', 'zionbuilder' ),
				'background_attachement'                   => __( 'Background Attachment', 'zionbuilder' ),
				'background_attachement_description'       => __( 'Description for background attachement', 'zionbuilder' ),
				'background_position'                      => __( 'Background position', 'zionbuilder' ),
				'background_position_description'          => __( 'Description for background description', 'zionbuilder' ),
				'background_size'                          => __( 'Background Size', 'zionbuilder' ),
				'background_size_description'              => __( 'Description for background size', 'zionbuilder' ),
				'background_clip'                          => __( 'Background Clip', 'zionbuilder' ),
				'select_background_clip'                   => __( 'Select background clip', 'zionbuilder' ),
				'background_clip_description'              => __( 'Description for background clip', 'zionbuilder' ),
				'background_blend_mode'                    => __( 'Background blend mode', 'zionbuilder' ),
				'blend_mode'                               => __( 'Blend mode', 'zionbuilder' ),
				'background_blend_mode_description'        => __( 'Background blend mode description', 'zionbuilder' ),

				// Text align
				'align_left'                               => __( 'Align left', 'zionbuilder' ),
				'align_center'                             => __( 'Align center', 'zionbuilder' ),
				'align_right'                              => __( 'Align right', 'zionbuilder' ),
				'justify'                                  => __( 'Justify', 'zionbuilder' ),

				// Shape
				'select_shape'                             => __( 'Select shape divider', 'zionbuilder' ),
				'pro_masks_title'                          => __( 'More shape dividers', 'zionbuilder' ),
				'pro_masks_description'                    => __( 'The shape you were looking for is not listed above ?', 'zionbuilder' ),
				'mask_position'                            => __( 'Mask position', 'zionbuilder' ),
				'select_top_mask'                          => __( 'Selected top mask', 'zionbuilder' ),
				'select_bottom_mask'                       => __( 'Selected bottom mask', 'zionbuilder' ),
				'select_mask_height'                       => __( 'Add mask height', 'zionbuilder' ),
				'select_mask_color'                        => __( 'Add a color to mask', 'zionbuilder' ),
				'top_masks'                                => __( 'Top masks', 'zionbuilder' ),
				'bottom_masks'                             => __( 'Bottom masks', 'zionbuilder' ),
				'flip_mask'                                => __( 'Flip mask horizontally', 'zionbuilder' ),

				// Background gradient
				'select_background_gradient'               => __( 'Select from library', 'zionbuilder' ),
				'add_background_gradient'                  => __( 'Add new background gradient', 'zionbuilder' ),
				'or'                                       => __( 'or', 'zionbuilder' ),

				// Custom attributes
				'meet_custom_attributes'                   => __( 'Meet custom attributes', 'zionbuilder' ),
				'meet_custom_attributes_desc'              => __( 'Generate custom attributes to every inner parts of the element', 'zionbuilder' ),
				'meet_custom_attributes_link'              => __( 'Click here to learn more about PRO.', 'zionbuilder' ),

				//Link
				'add_an_url'                               => __( 'Add an URL', 'zionbuilder' ),
				'link_new_window'                          => __( 'New Window', 'zionbuilder' ),
				'link_blank'                               => __( 'Same Window', 'zionbuilder' ),
				'set_a_title'                              => __( 'Set a title', 'zionbuilder' ),
				'link_target'                              => __( 'Link Target', 'zionbuilder' ),
				'link_title'                               => __( 'Link Title', 'zionbuilder' ),
				'link_url'                                 => __( 'Link Url', 'zionbuilder' ),

				// Transform
				'add_transform_property'                   => __( 'ADD TRANSFORM PROPERTY', 'zionbuilder' ),

				// Not found
				'element_not_found'                        => __( 'element not found', 'zionbuilder' ),
				'add_class_assignment_not_allowed'         => __( 'Class asignments not allowed', 'zionbuilder' ),

				// Video background
				'browser_video_error'                      => __( 'Your browser does not support HTML5 video.', 'zionbuilder' ),
				'no_video_selected'                        => __( 'No video Selected', 'zionbuilder' ),
				'no_images_selected'                       => __( 'No images selected', 'zionbuilder' ),

				// Select
				'add_option'                               => __( 'Add your option', 'zionbuilder' ),
				'no_items'                                 => __( 'No items', 'zionbuilder' ),

				// Dropdown classes selector
				'enter_class_name'                         => __( 'Enter Class Name', 'zionbuilder' ),

				// ColorPicker
				'no_html_matching'                         => __( 'No HTMLElement was found matching', 'zionbuilder' ),

				// InputColorPicker
				'no_color_chosen'                          => __( 'No color chosen', 'zionbuilder' ),
				'color'                                    => __( 'Color', 'zionbuilder' ),

				// PatternContainer, PresetInput
				'add_preset_title'                         => __( 'Global color name', 'zionbuilder' ),

				//InputSelect
				'no_result'                                => __( 'No results', 'zionbuilder' ),

				//InputTextShadow
				'inset_box_shadow'                         => __( 'Inset', 'zionbuilder' ),

				//GradientColorConfig
				'location'                                 => __( 'Location', 'zionbuilder' ),
				'delete_gradient_color'                    => __( 'Delete', 'zionbuilder' ),

				//GradientGenerator
				'save_as_preset'                           => __( 'Save as preset', 'zionbuilder' ),

				// Dragable
				'draggable'                                => __( 'Draggable', 'zionbuilder' ),

				// AllowedPostTypes
				'set_allowed_types'                        => __( 'You can set from here the allowed post types', 'zionbuilder' ),

				// Colors
				'create_color_palette'                     => __( 'Create your color pallete to use locally or globally', 'zionbuilder' ),

				// CustomFonts
				'click_me_to_add_font'                     => __( 'Click Me or the Blue button to add a Font', 'zionbuilder' ),

				// GoogleFonts
				'google_fonts_1'                           => __( 'Seting up', 'zionbuilder' ),
				'google_fonts_link'                        => __( 'Google web fonts', 'zionbuilder' ),
				'google_fonts_2'                           => __( "has never been easier. Choose which ones to use for your website's stylish typography", 'zionbuilder' ),

				// GradientBox
				'delete_gradient_from_preset'              => __( 'Delete this gradient from your preset', 'zionbuilder' ),

				// ColorBox
				'delete_color_from_preset'                 => __( 'Delete this color from your preset', 'zionbuilder' ),
				'create_gradient_info'                     => __( 'Create Astonishing Gradients that you will use in all the pages of your website', 'zionbuilder' ),

				// permissions
				'manage_users_permissions'                 => __(
					'Manage the permissions by selecting which users are allowed to use the page builder. Select to edit only the content, the post types such as Post, Pages, and the main features such as the header and the footer builder.',
					'zionbuilder'
				),
				'manage_wordpress_users_permisions'        => __( 'Manage your wordpress users permissions. Adding a new user will allow the basic permissions which can be edited afterwards.', 'zionbuilder' ),

				// ModalAddNewTemplate
				'create_new_modal_template'                => __( 'Create a new template by choosing the template type and adding a name', 'zionbuilder' ),

				// ModalConditions
				'add_display_condition'                    => __( 'Add Display Condition', 'zionbuilder' ),

				// Saved elements
				'manage_smart_areas'                       => __( 'Manage the smart areas you want to use in your project', 'zionbuilder' ),

				// TemplatePage
				'templates_description'                    => __( 'Templates allow you to easily build a WordPress page', 'zionbuilder' ),

				//ToolsPage
				'update_site_address_url'                  => __( 'Update Site Address (URL)', 'zionbuilder' ),
				'update_url'                               => __( 'Update URL', 'zionbuilder' ),

				'enter_old_and_new_url'                    => sprintf(
					/* translators: %s is the whitelabel plugin name */
					_x( 'Enter your old and new URLs for your WordPress installation, to update all %s data (Relevant for domain transfers or move to "HTTPS").', 'zionbuilder' ),
					Whitelabel::get_title()
				),

				// ModalListItem
				'user_has_permissions_remove'              => __( 'This user already has permissions. Click to remove', 'zionbuilder' ),

				// UserModalContent
				'check_access_to_editor'                   => __( 'Check to have access to editor', 'zionbuilder' ),

				// UserTemplate
				'customize_permissions_for_user'           => __( 'Customize the permissions for this user', 'zionbuilder' ),
				'delete_permissions_for_user'              => __( 'Delete permissions for this user', 'zionbuilder' ),

				// single-template
				'edit_template'                            => __( 'Edit template', 'zionbuilder' ),
				'delete_template'                          => __( 'Delete template', 'zionbuilder' ),
				'export_template'                          => __( 'Export template', 'zionbuilder' ),
				'preview_template'                         => __( 'Preview template', 'zionbuilder' ),
				'copy_shortcode'                           => __( 'Copy shortcode', 'zionbuilder' ),
				'regenerate_screenshot'                    => __( 'Regenerate screenshot', 'zionbuilder' ),

				// device-element
				'discard_changes_for'                      => __( 'Discard changes for', 'zionbuilder' ),

				// icons modal
				'icon_library_title'                       => __( 'Icon Library', 'zionbuilder' ),

				// InputWrapper
				'discard_changes'                          => __( 'Discard changes', 'zionbuilder' ),

				// PseudoSelectors
				'add_pseudo_content'                       => __( 'Click to add content for pseudo selector.', 'zionbuilder' ),

				// SingleClass
				'click_to_edit_class'                      => __( 'Click to edit this class', 'zionbuilder' ),
				'click_to_delete_globally'                 => __( 'Click to globally delete this class', 'zionbuilder' ),

				// element-section-view, TreeViewListItem
				'enable_hidden_element'                    => __( 'The element is hidden. Click to enable it.', 'zionbuilder' ),
				'preview_not_available'                    => __( 'Preview not available', 'zionbuilder' ),
				// RightClickMenu
				'copy_element'                             => __( 'Copy', 'zionbuilder' ),
				'paste_element'                            => __( 'Paste', 'zionbuilder' ),
				'duplicate_element'                        => __( 'Duplicate', 'zionbuilder' ),
				'copy_element_styles'                      => __( 'Copy styles', 'zionbuilder' ),
				'paste_element_styles'                     => __( 'Paste styles', 'zionbuilder' ),
				'copy_classes'                             => __( 'Copy classes', 'zionbuilder' ),
				'paste_classes'                            => __( 'Paste classes', 'zionbuilder' ),
				'undo'                                     => __( 'Undo', 'zionbuilder' ),
				'redo'                                     => __( 'Redo', 'zionbuilder' ),
				'hide_element'                             => __( 'Hide element', 'zionbuilder' ),
				'toggle_preview'                           => __( 'Toggle preview mode', 'zionbuilder' ),
				'toggle_library'                           => __( 'Toggle Library Panel', 'zionbuilder' ),
				'toggle_tree_view_panel'                   => __( 'Toggle Tree View Panel', 'zionbuilder' ),
				'toggle_page_options'                      => __( 'Toggle page options', 'zionbuilder' ),
				'when_dragging_element'                    => __( 'When dragging element', 'zionbuilder' ),
				'when_dragging_toolbox'                    => __( 'When dragging toolbox', 'zionbuilder' ),
				'set_incremental_value'                    => __( 'Set incremental value', 'zionbuilder' ),
				'set_even_incremental_value'               => __( 'Set even incremental value', 'zionbuilder' ),
				'set_even_values'                          => __( 'Set even values', 'zionbuilder' ),
				'duplicate_element_in_place'               => __( 'Duplicate element in place', 'zionbuilder' ),

				// ZnpbPanelMain
				'help'                                     => __( 'Help', 'zionbuilder' ),
				'help_modal_title'                         => __( 'Help Center', 'zionbuilder' ),
				'tour'                                     => __( 'Start tour', 'zionbuilder' ),
				'key_shortcuts'                            => __( 'Key shortcuts', 'zionbuilder' ),

				'about_zion_builder'                       => sprintf(
					/* translators: %s is the whitelabel plugin name */
					_x( 'About %s', 'zionbuilder' ),
					Whitelabel::get_title()
				),

				'zion_builder'                             => sprintf(
					/* translators: %s is the whitelabel plugin name */
					'%s ',
					Whitelabel::get_title()
				),
				'back_to_wp_dashboard'                     => __( 'Back to WP dashboard', 'zionbuilder' ),
				'back_to_zion_dashboard'                   => sprintf(
					/* translators: %s is the whitelabel plugin name */
					_x( '%s  dashboard', 'zionbuilder' ),
					Whitelabel::get_title()
				),
				'save_template'                            => __( 'Save Template', 'zionbuilder' ),
				'save_draft'                               => __( 'Save Page', 'zionbuilder' ),
				'save_page'                                => __( 'Save & Publish Page', 'zionbuilder' ),
				'success_save'                             => __( 'This page was successfully saved', 'zionbuilder' ),
				'getting_started'                          => __( 'Getting started', 'zionbuilder' ),
				// inline editor
				'target'                                   => __( 'Target', 'zionbuilder' ),
				'family'                                   => __( 'family', 'zionbuilder' ),
				'font_size'                                => __( 'Size', 'zionbuilder' ),
				'heading'                                  => __( 'Heading', 'zionbuilder' ),
				'line_height'                              => __( 'Height', 'zionbuilder' ),
				'letter_spacing'                           => __( 'Spacing', 'zionbuilder' ),
				'add_a_link'                               => __( 'Add a link', 'zionbuilder' ),

				// CustomFontModalContent
				'custom_font_name'                         => __( 'Choose custom font name', 'zionbuilder' ),
				'custom_font_weight'                       => __( 'Set custom font weight', 'zionbuilder' ),
				'custom_font_woff'                         => __( 'Select custom font woff file', 'zionbuilder' ),
				'custom_font_woff2'                        => __( 'Select custom font woff2 file', 'zionbuilder' ),
				'custom_font_ttf'                          => __( 'Select custom font ttf file', 'zionbuilder' ),
				'custom_font_svg'                          => __( 'Select custom font svg file', 'zionbuilder' ),
				'custom_font_eot'                          => __( 'Select custom font eot file', 'zionbuilder' ),

				// znpb-editor-app
				'storage_data_available'                   => __( 'Newer data is available in local storage. Choose what data to use', 'zionbuilder' ),
				'use_local_version'                        => __( 'Use local version', 'zionbuilder' ),
				'use_server_version'                       => __( 'Use server version', 'zionbuilder' ),
				'needs_options_for_render'                 => __( 'Please edit the element options', 'zionbuilder' ),

				// Notices
				'autosave_notice'                          => __( 'This is an autosave. Edit your page and when you\'re ready press the "Save and Publish Page" button.', 'zionbuilder' ),
				'page_content_error'                       => __( 'There was an error loading the content. Please refresh and try again', 'zionbuilder' ),

				// Pricing Box
				'featured'                                 => __( 'Featured', 'zionbuilder' ),
				'moved'                                    => __( 'Moved', 'zionbuilder' ),
				'copied'                                   => __( 'Copied', 'zionbuilder' ),

				// Element
				'invalid_element'                          => __( 'Invalid Element', 'zionbuilder' ),
				'remove_additional_classes'                => __( 'Remove additional classes', 'zionbuilder' ),
				'link_attributes'                          => __( 'Link attributes', 'zionbuilder' ),
				'add_custom_link_attribute'                => __( 'Add custom link attribute', 'zionbuilder' ),
				'attribute_key'                            => __( 'Attribute key', 'zionbuilder' ),
				'attribute_value'                          => __( 'Attribute value', 'zionbuilder' ),
				'insert_after'                             => __( 'Insert after', 'zionbuilder' ),
				'insert_inside'                            => __( 'Insert inside', 'zionbuilder' ),
				'edit_link_attributes'                     => __( 'Edit link attributes', 'zionbuilder' ),

				// Maintenance mode
				'maintenance_mode'                         => __( 'Maintenance mode', 'zionbuilder' ),
				'appearance'                               => __( 'Appearance', 'zionbuilder' ),

				// Appearance page
				'builder_theme'                            => __( 'Builder theme', 'zionbuilder' ),
				'builder_theme_desc'                       => __( 'By changing the builder theme, it will be applied on all pages where the builder is active, as well as all the builder admin pages', 'zionbuilder' ),

				// Misc
				'no_items_found'                           => __( 'No items found', 'zionbuilder' ),
				'search'                                   => __( 'Search', 'zionbuilder' ),
				'search_or_add'                            => __( 'Search or add new', 'zionbuilder' ),
				'page_options'                             => __( 'Page options', 'zionbuilder' ),
				'delete_selector'                          => __( 'Delete selector', 'zionbuilder' ),
				'are_you_sure_you_want_to_delete_selector' => __( 'Are you sure you want to delete the selector?', 'zionbuilder' ),
				'cancel'                                   => __( 'Cancel', 'zionbuilder' ),
				'yes_delete_elements'                      => __( 'Yes, delete elements', 'zionbuilder' ),
				'are_you_sure_delete_elements'             => __( 'Are you sure you want to remove all elements from page?', 'zionbuilder' ),
				'copy'                                     => __( 'Copy', 'zionbuilder' ),
				'paste'                                    => __( 'Paste', 'zionbuilder' ),

				// Global classes
				'add_css_class'                            => __( 'Add CSS class', 'zionbuilder' ),
				'selector_nice_name'                       => __( 'Selector nice name', 'zionbuilder' ),
				'enter_a_name_for_this_selector'           => __( 'Enter a name for this selector', 'zionbuilder' ),
				'add_child_selector'                       => __( 'Add child selector', 'zionbuilder' ),
				'css_selector'                             => __( 'CSS selector', 'zionbuilder' ),
				'enter_the_css_selector_you_want_to_style' => __( 'Enter the css selector you want to style', 'zionbuilder' ),
				'my_selector'                              => __( '.my-selector', 'zionbuilder' ),
				'class_nice_name'                          => __( 'CSS class nice name', 'zionbuilder' ),
				'enter_the_class_nice_name'                => __( 'Enter a name that will help you recognize this CSS class', 'zionbuilder' ),
				'css_class'                                => __( 'CSS class', 'zionbuilder' ),
				'enter_class_name_without_dot'             => __( 'Enter the CSS class name without the leading dot', 'zionbuilder' ),
				'css_class_placeholder'                    => __( 'my-class-name', 'zionbuilder' ),

				// admin custom code
				'custom_code'                              => __( 'Custom code', 'zionbuilder' ),

				// Option margin-padding
				'margin'                                   => __( 'Margin', 'zionbuilder' ),
				'margin-top'                               => __( 'Margin top', 'zionbuilder' ),
				'margin-right'                             => __( 'Margin right', 'zionbuilder' ),
				'margin-bottom'                            => __( 'Margin bottom', 'zionbuilder' ),
				'margin-left'                              => __( 'Margin left', 'zionbuilder' ),
				'padding'                                  => __( 'Padding', 'zionbuilder' ),
				'padding-top'                              => __( 'Padding top', 'zionbuilder' ),
				'padding-right'                            => __( 'Padding right', 'zionbuilder' ),
				'padding-bottom'                           => __( 'Padding bottom', 'zionbuilder' ),
				'padding-left'                             => __( 'Padding left', 'zionbuilder' ),
				'link'                                     => __( 'Link', 'zionbuilder' ),
				'unlink'                                   => __( 'Unlink', 'zionbuilder' ),

				// Library share
				'library_share'                            => __( 'Library share', 'zionbuilder' ),
				'share_library_upgrade_message'            => __( 'Zion Builder PRO lets you share you templates library with multiple websites.', 'zionbuilder' ),

				// Performance admin page
				'performance'                              => __( 'Performance', 'zionbuilder' ),

				// Responsice devices
				'all_devices'                              => __( 'all devices', 'zionbuilder' ),
				'max'                                      => __( 'max', 'zionbuilder' ),
				'edit_breakpoints'                         => __( 'Edit breakpoints', 'zionbuilder' ),
				'disable_edit_breakpoints'                 => __( 'Disable edit breakpoints', 'zionbuilder' ),
				'edit_breakpoint'                          => __( 'Edit breakpoint', 'zionbuilder' ),
				'delete_breakpoint'                        => __( 'Delete breakpoint', 'zionbuilder' ),
				'disable_autoscale'                        => __( 'Disable autoscale', 'zionbuilder' ),
				'enable_autoscale'                         => __( 'Enable autoscale', 'zionbuilder' ),
				'add_breakpoint'                           => __( 'Add breakpoint', 'zionbuilder' ),
				'preview_width'                            => __( 'Preview width', 'zionbuilder' ),
				'preview_scale'                            => __( 'Preview scale', 'zionbuilder' ),
				'wrap_with_container'                      => __( 'Wrap with container', 'zionbuilder' ),
			]
		);
	}
}
