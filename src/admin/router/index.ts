import { __ } from '@wordpress/i18n';
import Routes from './Routes';
import 'vue-router';

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

declare module 'vue-router' {
	interface RouteMeta {
		// is optional
		title?: string;
		// must be declared by every route
		label?: string;
	}
}

const getTemplateChildren = () => {
	const templateChildren = {};
	window.ZnPbAdminPageData.template_types.forEach(templateType => {
		templateChildren[templateType.id] = {
			path: templateType.id,
			name: templateType.id,
			props: {
				templateType: templateType.id,
				templateName: templateType.name,
			},
			component: TemplatePage,
			meta: {
				title: templateType.name,
			},
		};
	});

	// Add template routes
	return templateChildren;
};

export const routes = new Routes();

export const initRoutes = function () {
	const SettingsRoute = routes.addRoute('settings', {
		path: '/settings',
		name: 'settings',
		redirect: {
			name: 'general-settings',
		},
		component: PageContent,
		meta: {
			title: __('Settings', 'zionbuilder'),
		},
	});

	const GeneralSettingsRoute = SettingsRoute.addRoute('general-settings', {
		path: 'general-settings',
		redirect: {
			name: 'allowed-post-types',
		},
		component: SettingsPage,
		name: 'general-settings',
		meta: {
			title: __('General Settings', 'zionbuilder'),
		},
	});

	GeneralSettingsRoute.addRoute('allowed-post-types', {
		path: 'allowed-post-types',
		name: 'allowed-post-types',
		meta: {
			title: __('Allowed Post types', 'zionbuilder'),
		},
		component: PageAllowedPostTypes,
	});

	GeneralSettingsRoute.addRoute('maintenance-mode', {
		path: 'maintenance-mode',
		name: 'maintenance-mode',
		meta: {
			title: __('Maintenance mode', 'zionbuilder'),
		},
		component: MaintenanceMode,
	});

	GeneralSettingsRoute.addRoute('appearance', {
		path: 'appearance',
		name: 'appearance',
		meta: {
			title: __('Appearance', 'zionbuilder'),
		},

		component: Appearance,
	});

	const FontOptionsRoute = SettingsRoute.addRoute('font-options', {
		path: 'font-options',
		name: 'font_options',
		redirect: {
			name: 'google_fonts',
		},
		meta: {
			title: __('Font Options', 'zionbuilder'),
		},

		component: SettingsPage,
	});

	FontOptionsRoute.addRoute('google-fonts', {
		path: 'google-fonts',
		name: 'google_fonts',
		component: GoogleFonts,
		meta: {
			title: __('Google Fonts', 'zionbuilder'),
		},
	});

	FontOptionsRoute.addRoute('custom-fonts', {
		path: 'custom-fonts',
		name: 'custom_fonts',
		props: {
			message: __('With PRO you can upload your own sets of fonts and assign it to your page elements.', 'zionbuilder'),
		},
		component: GetPro,
		meta: {
			label: {
				type: 'warning',
				text: __('pro', 'zionbuilder'),
			},
			title: __('Custom Fonts', 'zionbuilder'),
		},
	});

	FontOptionsRoute.addRoute('adobe-fonts', {
		path: 'adobe-fonts',
		name: 'adobe_fonts',
		props: {
			message: __(
				'With PRO you can use the Adobe fonts library to add your fonts along side Google fonts and custom fonts.',
				'zionbuilder',
			),
		},
		component: GetPro,
		meta: {
			label: {
				type: 'warning',
				text: __('pro', 'zionbuilder'),
			},
			title: __('Adobe Fonts', 'zionbuilder'),
		},
	});

	SettingsRoute.addRoute('custom-icons', {
		path: 'custom-icons',
		name: 'icons',
		props: {
			message: __('Zion Builder PRO lets you share you templates library with multiple websites.', 'zionbuilder'),
		},
		component: GetPro,
		meta: {
			label: {
				type: 'warning',
				text: __('pro', 'zionbuilder'),
			},
			title: __('Custom Icons', 'zionbuilder'),
		},
	});

	const PresetsRoute = SettingsRoute.addRoute('presets', {
		path: 'presets',
		component: SettingsPage,
		redirect: {
			name: 'color_presets',
		},
		name: 'presets',
		meta: {
			title: __('Presets', 'zionbuilder'),
		},
	});

	PresetsRoute.addRoute('color-presets', {
		path: 'color-presets',
		name: 'color_presets',
		component: Colors,
		meta: {
			title: __('Color Presets', 'zionbuilder'),
		},
	});

	PresetsRoute.addRoute('gradients-presets', {
		path: 'gradients-presets',
		name: 'gradients_presets',
		component: Gradients,
		meta: {
			title: __('Gradients', 'zionbuilder'),
		},
	});

	SettingsRoute.addRoute('performance', {
		path: 'performance',
		name: 'performance',
		component: Performance,
		meta: {
			title: __('Performance', 'zionbuilder'),
		},
	});

	SettingsRoute.addRoute('library', {
		path: 'library',
		name: 'library',
		props: {
			message: __(
				'With PRO you can upload your own icons in addition to the Font Awesome icons that everyone is using.',
				'zionbuilder',
			),
		},
		component: GetPro,
		meta: {
			title: __('Library', 'zionbuilder'),
			label: {
				type: 'warning',
				text: __('pro', 'zionbuilder'),
			},
		},
	});

	routes.addRoute('permissions', {
		path: '/permissions',
		component: Permissions,
		meta: {
			title: __('Permissions', 'zionbuilder'),
		},
	});

	routes.addRoute('templates', {
		path: '/templates',
		component: PageContent,
		name: 'all_templates',
		redirect: {
			name: 'template',
		},
		children: getTemplateChildren(),
		meta: {
			title: __('Templates', 'zionbuilder'),
		},
	});

	routes.addRoute('custom-code', {
		path: '/custom-code',
		component: CustomCode,
		meta: {
			title: __('Custom code', 'zionbuilder'),
		},
	});

	const ToolsRoute = routes.addRoute('tools-page', {
		path: '/tools-page',
		component: PageContent,
		redirect: {
			name: 'tools-page',
		},
		meta: {
			title: __('Tools', 'zionbuilder'),
		},
	});

	ToolsRoute.addRoute('tools-page', {
		path: 'tools-page',
		name: 'tools-page',
		props: { templateType: 'tools' },
		component: ToolsPage,
		meta: {
			title: __('General', 'zionbuilder'),
		},
	});

	ToolsRoute.addRoute('replace-url', {
		path: 'replace-url',
		name: 'replace-url',
		props: { templateType: 'replace-url' },
		component: ReplaceUrl,
		meta: {
			title: __('Replace URL', 'zionbuilder'),
		},
	});

	routes.addRoute('system-info', {
		path: '/system-info',
		component: SystemInfo,
		meta: {
			title: __('System Info', 'zionbuilder'),
		},
	});

	routes.addRoute('get-pro', {
		path: '/get-pro',
		name: 'get-pro',
		component: GetPro,
	});
};
