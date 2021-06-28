import Routes from './Routes'

// Components
import SettingsPage from '../components/SettingsPage.vue'
import SystemInfo from '../components/SystemInfo.vue'
import Colors from '../components/colors/Colors.vue'
import Permissions from '../components/permissions/Permissions.vue'
import TemplatePage from '../components/templates/TemplatePage.vue'
import GoogleFonts from '../components/google-fonts/GoogleFonts.vue'
import PageAllowedPostTypes from '../components/PageAllowedPostTypes.vue'
import PageContent from '../components/PageContent.vue'
import Gradients from '../components/gradients/Gradients.vue'
import GetPro from '../components/GetPro.vue'
import ToolsPage from '../components/tools/ToolsPage.vue'
import ReplaceUrl from '../components/tools/ReplaceUrl.vue'
import MaintenanceMode from '../components/MaintenanceMode.vue'
import Appearance from '../components/Appearance.vue'

import { translate } from '@zb/i18n'

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

export const initRoutes = function () {
	routes.addRoute('home', {
		path: '/',
		component: PageContent,
		name: 'home',
		redirect: {
			name: 'settings'
		}
	})

	routes.addRoute('get-pro', {
		path: '/get-pro',
		name: 'get-pro',
		component: GetPro
	})

	const SettingsRoute = routes.addRoute('settings', {
		path: '/settings',
		name: 'settings',
		redirect: {
			name: 'general-settings'
		},
		component: PageContent,
		title: translate('settings')
	})

	const GeneralSettingsRoute = SettingsRoute.addRoute('general-settings', {
		path: 'general-settings',
		redirect: {
			name: 'allowed-post-types'
		},
		component: SettingsPage,
		title: translate('general_settings'),
		name: 'general-settings'
	})

	GeneralSettingsRoute.addRoute('allowed-post-types', {
		path: 'allowed-post-types',
		name: 'allowed-post-types',
		title: translate('allowed_post_types'),
		component: PageAllowedPostTypes
	})

	GeneralSettingsRoute.addRoute('maintenance-mode', {
		path: 'maintenance-mode',
		name: 'maintenance-mode',
		title: translate('maintenance_mode'),
		component: MaintenanceMode
	})

	GeneralSettingsRoute.addRoute('appearance', {
		path: 'appearance',
		name: 'appearance',
		title: translate('appearance'),
		component: Appearance
	})

	const FontOptionsRoute = SettingsRoute.addRoute('font-options', {
		path: 'font-options',
		name: 'font_options',
		redirect: {
			name: 'google_fonts'
		},
		title: translate('font_options'),
		component: SettingsPage
	})

	FontOptionsRoute.addRoute('google-fonts', {
		path: 'google-fonts',
		name: 'google_fonts',
		title: translate('google_fonts'),
		component: GoogleFonts
	})

	FontOptionsRoute.addRoute('custom-fonts', {
		path: 'custom-fonts',
		name: 'custom_fonts',
		title: translate('custom_fonts'),
		props: { message: translate('custom_fonts_upgrade_message') },
		component: GetPro,
		label: {
			type: 'warning',
			text: translate('pro')
		}
	})

	FontOptionsRoute.addRoute('adobe-fonts', {
		path: 'adobe-fonts',
		name: 'adobe_fonts',
		title: translate('typekit_fonts'),
		props: { message: translate('typekit_fonts_upgrade_message') },
		component: GetPro,
		label: {
			type: 'warning',
			text: translate('pro')
		}
	})

	SettingsRoute.addRoute('custom-icons', {
		path: 'custom-icons',
		title: translate('custom_icons'),
		name: 'icons',
		props: { message: translate('custom_icons_upgrade_message') },
		component: GetPro,
		label: {
			type: 'warning',
			text: translate('pro')
		}
	})

	const PresetsRoute = SettingsRoute.addRoute('presets', {
		path: 'presets',
		component: SettingsPage,
		redirect: {
			name: 'color_presets'
		},
		title: translate('presets'),
		name: 'presets'
	})

	PresetsRoute.addRoute('color-presets', {
		path: 'color-presets',
		name: 'color_presets',
		title: translate('color_presets'),
		component: Colors
	})

	PresetsRoute.addRoute('gradients-presets', {
		path: 'gradients-presets',
		name: 'gradients_presets',
		title: translate('gradients'),
		component: Gradients
	})

	routes.addRoute('permissions', {
		path: '/permissions',
		component: Permissions,
		title: translate('permissions')
	})

	routes.addRoute('templates', {
		path: '/templates',
		component: PageContent,
		title: translate('templates'),
		name: 'all_templates',
		redirect: {
			name: 'template'
		},
		children: getTemplateChildrens()
	})

	const ToolsRoute = routes.addRoute('tools-page', {
		path: '/tools-page',
		component: PageContent,
		title: translate('tools'),
		redirect: {
			name: 'tools-page'
		}
	})

	ToolsRoute.addRoute('tools-page', {
		path: 'tools-page',
		name: 'tools-page',
		props: { templateType: 'tools' },
		title: translate('general'),
		component: ToolsPage
	})

	ToolsRoute.addRoute('replace-url', {
		path: 'replace-url',
		name: 'replace-url',
		props: { templateType: 'replace-url' },
		title: translate('replace_url'),
		component: ReplaceUrl
	})

	routes.addRoute('system-info', {
		path: '/system-info',
		component: SystemInfo,
		title: translate('system_info')
	})
}

export default routes
