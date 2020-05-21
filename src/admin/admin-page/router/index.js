import Routes from './Routes'
import L10n from '../L10n'

// Components
import SettingsPage from '../components/SettingsPage.vue'
import SystemInfo from '../components/SystemInfo.vue'
import Colors from '../components/colors/Colors.vue'
import Permissions from '../components/permissions/Permissions.vue'
import TemplatePage from '../components/templates/TemplatePage.vue'
import GoogleFonts from '../components/google-fonts/GoogleFonts.vue'
import AllowedPostTypes from '../components/allowed-post/AllowedPostTypes.vue'
import PageContent from '../components/PageContent.vue'
import Gradients from '../components/gradients/Gradients.vue'
import GetPro from '../components/GetPro.vue'
import ToolsPage from '../components/tools/ToolsPage.vue'
import ReplaceUrl from '../components/tools/ReplaceUrl.vue'

const getTemplateChildrens = () => {
	const templateChilds = {}
	window.ZnPbAdminPageData.template_types.forEach(templateType => {
		templateChilds[templateType.id] = {
			path: templateType.id,
			name: templateType.id,
			props: {
				templateType: templateType.id,
				templateName: templateType.name
			},
			title: templateType.name,
			component: TemplatePage
		}
	})

	// Add template routes
	return templateChilds
}

const routes = new Routes()
routes.addRoute('home', {
	path: '/',
	component: PageContent,
	name: 'home',
	redirect: {
		name: 'settings'
	}
})

routes.addRoute('get-pro', 	{
	path: '/get-pro',
	name: 'get-pro',
	component: GetPro
})

const SettingsRoute = routes.addRoute('settings', 	{
	path: '/settings',
	name: 'settings',
	redirect: {
		name: 'general-settings'
	},
	component: PageContent,
	title: L10n.translate('settings')
})

const GeneralSettingsRoute = SettingsRoute.addRoute('general-settings', {
	path: 'general-settings',
	redirect: {
		name: 'allowed-post-types'
	},
	component: AllowedPostTypes,
	title: L10n.translate('general_settings'),
	name: 'general-settings'
})

GeneralSettingsRoute.addRoute('allowed-post-types', {
	path: 'allowed-post-types',
	name: 'allowed-post-types',
	title: L10n.translate('allowed_post_types'),
	component: AllowedPostTypes
})

const FontOptionsRoute = SettingsRoute.addRoute('font-options', {
	path: 'font-options',
	redirect: {
		name: 'google_fonts'
	},
	component: SettingsPage,
	title: L10n.translate('font_options'),
	name: 'font_options'
})

FontOptionsRoute.addRoute('google-fonts', {
	path: 'google-fonts',
	name: 'google_fonts',
	title: L10n.translate('google_fonts'),
	component: GoogleFonts
})

FontOptionsRoute.addRoute('custom-fonts', {
	path: 'custom-fonts',
	name: 'custom_fonts',
	title: L10n.translate('custom_fonts'),
	props: { message: L10n.translate('custom_fonts_upgrade_message') },
	component: GetPro,
	label: {
		type: 'warning',
		text: L10n.translate('pro')
	}
})

FontOptionsRoute.addRoute('adobe-fonts', {
	path: 'adobe-fonts',
	name: 'adobe_fonts',
	title: L10n.translate('typekit_fonts'),
	props: { message: L10n.translate('typekit_fonts_upgrade_message') },
	component: GetPro,
	label: {
		type: 'warning',
		text: L10n.translate('pro')
	}
})

SettingsRoute.addRoute('custom-icons', {
	path: 'custom-icons',
	title: L10n.translate('custom_icons'),
	name: 'icons',
	props: { message: L10n.translate('custom_icons_upgrade_message') },
	component: GetPro,
	label: {
		type: 'warning',
		text: L10n.translate('pro')
	}
})

const PresetsRoute = SettingsRoute.addRoute('presets', {
	path: 'presets',
	component: SettingsPage,
	redirect: {
		name: 'color_presets'
	},
	title: L10n.translate('presets'),
	name: 'presets'
})

PresetsRoute.addRoute('color-presets', {
	path: 'color-presets',
	name: 'color_presets',
	title: L10n.translate('color_presets'),
	component: Colors
})

PresetsRoute.addRoute('gradients-presets', {
	path: 'gradients-presets',
	name: 'gradients_presets',
	title: L10n.translate('gradients'),
	component: Gradients
})

routes.addRoute('permissions', {
	path: '/permissions',
	component: Permissions,
	title: L10n.translate('permissions')
})

routes.addRoute('templates', {
	path: '/templates',
	component: PageContent,
	title: L10n.translate('templates'),
	name: 'all_templates',
	redirect: {
		name: 'template'
	},
	children: getTemplateChildrens()
})

const ToolsRoute = routes.addRoute('tools-page', {
	path: '/tools-page',
	component: PageContent,
	title: L10n.translate('tools'),
	redirect: {
		name: 'tools-page'
	}
})

ToolsRoute.addRoute('tools-page', {
	path: 'tools-page',
	name: 'tools-page',
	props: { templateType: 'tools' },
	title: L10n.translate('general'),
	component: ToolsPage
})

ToolsRoute.addRoute('replace-url', {
	path: 'replace-url',
	name: 'replace-url',
	props: { templateType: 'replace-url' },
	title: L10n.translate('replace_url'),
	component: ReplaceUrl
})

routes.addRoute('system-info', {
	path: '/system-info',
	component: SystemInfo,
	title: L10n.translate('system_info')
})

export default routes
