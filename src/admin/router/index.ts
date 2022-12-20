import { __ } from '@wordpress/i18n';
import Routes from './Routes';

// Components
import SettingsPage from '../components/SettingsPage.vue';
import SystemInfo from '../components/SystemInfo.vue';
import Colors from '../components/colors/Colors.vue';
import Permissions from '../components/permissions/Permissions.vue';
import TemplatePage from '../components/templates/TemplatePage.vue';
import GoogleFonts from '../components/google-fonts/GoogleFonts.vue';
import PageAllowedPostTypes from '../components/PageAllowedPostTypes.vue';
import PageContent from '../components/PageContent.vue';
import Gradients from '../components/gradients/Gradients.vue';
import GetPro from '../components/GetPro.vue';
import ToolsPage from '../components/tools/ToolsPage.vue';
import ReplaceUrl from '../components/tools/ReplaceUrl.vue';
import MaintenanceMode from '../components/MaintenanceMode.vue';
import Appearance from '../components/Appearance.vue';
import CustomCode from '../components/CustomCode.vue';
import Performance from '../components/Performance.vue';

const getTemplateChildrens = () => {
	const templateChilds = {};
	window.ZnPbAdminPageData.template_types.forEach(templateType => {
		templateChilds[templateType.id] = {
			path: templateType.id,
			name: templateType.id,
			props: {
				templateType: templateType.id,
				templateName: templateType.name,
			},
			title: templateType.name,
			component: TemplatePage,
		};
	});

	// Add template routes
	return templateChilds;
};

export const routes = new Routes();

export const initRoutes = function () {
	routes.addRoute('home', {
		path: '/',
		component: PageContent,
		name: 'home',
		redirect: {
			name: 'settings',
		},
	});

	routes.addRoute('get-pro', {
		path: '/get-pro',
		name: 'get-pro',
		component: GetPro,
	});

	const SettingsRoute = routes.addRoute('settings', {
		path: '/settings',
		name: 'settings',
		redirect: {
			name: 'general-settings',
		},
		component: PageContent,
		title: __('Settings', 'zionbuilder'),
	});

	const GeneralSettingsRoute = SettingsRoute.addRoute('general-settings', {
		path: 'general-settings',
		redirect: {
			name: 'allowed-post-types',
		},
		component: SettingsPage,
		title: __('General Settings', 'zionbuilder'),
		name: 'general-settings',
	});

	GeneralSettingsRoute.addRoute('allowed-post-types', {
		path: 'allowed-post-types',
		name: 'allowed-post-types',
		title: __('Allowed Post types', 'zionbuilder'),
		component: PageAllowedPostTypes,
	});

	GeneralSettingsRoute.addRoute('maintenance-mode', {
		path: 'maintenance-mode',
		name: 'maintenance-mode',
		title: __('Maintenance mode', 'zionbuilder'),
		component: MaintenanceMode,
	});

	GeneralSettingsRoute.addRoute('appearance', {
		path: 'appearance',
		name: 'appearance',
		title: __('Appearance', 'zionbuilder'),
		component: Appearance,
	});

	const FontOptionsRoute = SettingsRoute.addRoute('font-options', {
		path: 'font-options',
		name: 'font_options',
		redirect: {
			name: 'google_fonts',
		},
		title: __('Font Options', 'zionbuilder'),
		component: SettingsPage,
	});

	FontOptionsRoute.addRoute('google-fonts', {
		path: 'google-fonts',
		name: 'google_fonts',
		title: __('Google Fonts', 'zionbuilder'),
		component: GoogleFonts,
	});

	FontOptionsRoute.addRoute('custom-fonts', {
		path: 'custom-fonts',
		name: 'custom_fonts',
		title: __('Custom Fonts', 'zionbuilder'),
		props: {
			message: __('With PRO you can upload your own sets of fonts and assign it to your page elements.', 'zionbuilder'),
		},
		component: GetPro,
		label: {
			type: 'warning',
			text: __('pro', 'zionbuilder'),
		},
	});

	FontOptionsRoute.addRoute('adobe-fonts', {
		path: 'adobe-fonts',
		name: 'adobe_fonts',
		title: __('Adobe Fonts', 'zionbuilder'),
		props: {
			message: __(
				'With PRO you can use the Adobe fonts library to add your fonts along side Google fonts and custom fonts.',
				'zionbuilder',
			),
		},
		component: GetPro,
		label: {
			type: 'warning',
			text: __('pro', 'zionbuilder'),
		},
	});

	SettingsRoute.addRoute('custom-icons', {
		path: 'custom-icons',
		title: __('Custom Icons', 'zionbuilder'),
		name: 'icons',
		props: {
			message: __('Zion Builder PRO lets you share you templates library with multiple websites.', 'zionbuilder'),
		},
		component: GetPro,
		label: {
			type: 'warning',
			text: __('pro', 'zionbuilder'),
		},
	});

	const PresetsRoute = SettingsRoute.addRoute('presets', {
		path: 'presets',
		component: SettingsPage,
		redirect: {
			name: 'color_presets',
		},
		title: __('Presets', 'zionbuilder'),
		name: 'presets',
	});

	PresetsRoute.addRoute('color-presets', {
		path: 'color-presets',
		name: 'color_presets',
		title: __('Color Presets', 'zionbuilder'),
		component: Colors,
	});

	PresetsRoute.addRoute('gradients-presets', {
		path: 'gradients-presets',
		name: 'gradients_presets',
		title: __('Gradients', 'zionbuilder'),
		component: Gradients,
	});

	SettingsRoute.addRoute('performance', {
		path: 'performance',
		title: __('Performance', 'zionbuilder'),
		name: 'performance',
		component: Performance,
	});

	SettingsRoute.addRoute('library', {
		path: 'library',
		title: __('Library', 'zionbuilder'),
		name: 'library',
		label: {
			type: 'warning',
			text: __('pro', 'zionbuilder'),
		},
		props: {
			message: __(
				'With PRO you can upload your own icons in addition to the Font Awesome icons that everyone is using.',
				'zionbuilder',
			),
		},
		component: GetPro,
	});

	routes.addRoute('permissions', {
		path: '/permissions',
		component: Permissions,
		title: __('Permissions', 'zionbuilder'),
	});

	routes.addRoute('templates', {
		path: '/templates',
		component: PageContent,
		title: __('Templates', 'zionbuilder'),
		name: 'all_templates',
		redirect: {
			name: 'template',
		},
		children: getTemplateChildrens(),
	});

	routes.addRoute('custom-code', {
		path: '/custom-code',
		component: CustomCode,
		title: __('Custom code', 'zionbuilder'),
	});

	const ToolsRoute = routes.addRoute('tools-page', {
		path: '/tools-page',
		component: PageContent,
		title: __('Tools', 'zionbuilder'),
		redirect: {
			name: 'tools-page',
		},
	});

	ToolsRoute.addRoute('tools-page', {
		path: 'tools-page',
		name: 'tools-page',
		props: { templateType: 'tools' },
		title: __('General', 'zionbuilder'),
		component: ToolsPage,
	});

	ToolsRoute.addRoute('replace-url', {
		path: 'replace-url',
		name: 'replace-url',
		props: { templateType: 'replace-url' },
		title: __('Replace URL', 'zionbuilder'),
		component: ReplaceUrl,
	});

	routes.addRoute('system-info', {
		path: '/system-info',
		component: SystemInfo,
		title: __('System Info', 'zionbuilder'),
	});
};
