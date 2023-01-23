<template>
	<div class="znpb-get-pro">
		<div class="znpb-get-pro__image">
			<img :src="proUrl" />
		</div>
		<h1 class="znpb-get-pro__title">{{ i18n.__('Upgrade to PRO now!', 'zionbuilder') }}</h1>
		<p class="znpb-get-pro__description">{{ message }}</p>
		<div class="znpb-get-pro__more">
			<a href="https://zionbuilder.io/documentation/pro-version/" target="_blank">
				{{ i18n.__('Click here to learn more about PRO.', 'zionbuilder') }}
			</a>
		</div>
		<a
			href="https://zionbuilder.io/"
			target="_blank"
			class="znpb-button znpb-get-pro__cta znpb-button--secondary znpb-option__upgrade-to-pro-button"
		>
			{{ i18n.__('Upgrade to PRO', 'zionbuilder') }}
		</a>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';

withDefaults(
	defineProps<{
		message?: string;
	}>(),
	{
		message: i18n.__(
			'With PRO you will have additional control over your pages, create reusable sections and elements, have dynamic data, additional elements, additional options to existing elements and many more features.',
			'zionbuilder',
		),
	},
);

const proUrl = window.ZnPbAdminPageData.urls.pro_logo;
</script>
<style lang="scss">
.znpb-get-pro {
	width: 100%;
	padding: 60px 0 140px;
	text-align: center;
	background-color: var(--zb-surface-color);

	.znpb-editor-icon-wrapper {
		margin: 0 auto;
		color: var(--zb-surface-lighter-color);
		font-size: 125px;
	}

	&__image {
		margin-bottom: 40px;
	}

	&__title {
		margin: 0 0 24px;
		color: var(--zb-surface-text-active-color);
		font-size: 22px;
		font-weight: 600;

		strong {
			font-weight: 700;
			text-transform: uppercase;
		}
	}

	&__description {
		max-width: 40%;
		margin: 0 auto 24px;
		font-size: 15px;
		text-align: center;
	}

	&__more {
		margin-bottom: 45px;
		text-align: center;

		& > a {
			padding-bottom: 5px;
			color: var(--zb-surface-text-active-color);
			font-weight: 500;
			border-bottom: 1px solid var(--zb-surface-border-color);

			&:hover {
				color: var(--zb-surface-icon-color);
			}
		}
	}

	&__cta {
		text-transform: uppercase;
	}
}

#adminmenu div.wp-menu-image > img {
	max-width: 16px;
}
</style>
