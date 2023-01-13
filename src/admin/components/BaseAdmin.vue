<template>
	<div v-if="loaded" id="znpb-admin" class="znpb-admin__wrapper">
		<div class="znpb-admin__header">
			<div class="znpb-admin__header-top">
				<div class="znpb-admin__header-logo">
					<img :src="EnvironmentStore.urls.logo" />
					<Label
						v-if="EnvironmentStore.plugin_pro.is_active"
						:text="__('pro', 'zionbuilder')"
						type="warning"
						class="znpb-option__upgrade-to-pro-label"
					/>
					<span class="znpb-admin__header-logo-version">v{{ EnvironmentStore.plugin_free.version }}</span>
				</div>
				<div class="znpb-admin__header-actions">
					<router-link
						v-if="!EnvironmentStore.plugin_pro.is_active"
						to="/get-pro"
						class="znpb-button znpb-button--secondary"
					>
						<Icon icon="quality"></Icon>
						{{ __('Upgrade to PRO', 'zionbuilder') }}
					</router-link>

					<a
						v-if="documentationLink && documentationLink.length"
						:href="documentationLink"
						:title="__('Documentation', 'zionbuilder')"
						target="_blank"
						class="znpb-button znpb-button--line"
						>{{ __('Documentation', 'zionbuilder') }}</a
					>
				</div>
			</div>
			<div class="znpb-admin__header-menu-wrapper">
				<div class="znpb-admin__header-menu">
					<router-link
						v-for="(menuItem, key) in menuItems"
						:key="key"
						:to="`${menuItem.path}`"
						class="znpb-admin__header-menu-item"
						>{{ menuItem.meta.title }}</router-link
					>
				</div>
			</div>
		</div>
		<router-view></router-view>

		<!-- notices -->
		<div class="znpb-admin-notices-wrapper">
			<Notice
				v-for="(error, index) in notificationsStore.notifications"
				:key="index"
				:error="error"
				@close-notice="error.remove()"
			/>
		</div>
		<CornerLoader :is-loading="builderOptionsStore.isLoading" />
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';

// Globals
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';

// Internal globals
import { useBuilderOptionsStore, useGoogleFontsStore, useNotificationsStore, useEnvironmentStore } from '@zb/store';

// Local components
import { CornerLoader } from '@zb/components';

const router = useRouter();
const builderOptionsStore = useBuilderOptionsStore();
const googleFontsStore = useGoogleFontsStore();
const notificationsStore = useNotificationsStore();
const EnvironmentStore = useEnvironmentStore();

const loaded = ref(false);
const hasError = ref(false);

/**
 * Get the menu items
 */
const menuItems = computed(() => {
	return router.options.routes.map(rawRoute => router.resolve(rawRoute)).filter(route => route.meta.title);
});

const documentationLink = computed(() => {
	let helpURL = 'https://zionbuilder.io/help-center/';

	if (
		builderOptionsStore.getOptionValue('white_label') !== null &&
		typeof builderOptionsStore.getOptionValue('white_label').plugin_help_url !== 'undefined'
	) {
		helpURL = builderOptionsStore.getOptionValue('white_label').plugin_help_url;
	}
	return helpURL;
});

Promise.all([googleFontsStore.fetchGoogleFonts()])
	.catch(error => {
		hasError.value = true;
		// eslint-disable-next-line
		console.error(error);
	})
	.finally(() => {
		loaded.value = true;
	});
</script>

<style lang="scss">
.znpb-admin__wrapper {
	color: var(--zb-surface-text-color);
	font-family: var(--zb-font-stack);
	font-size: 13px;
	line-height: 1;

	a,
	a:hover,
	a:focus,
	a:visited {
		text-decoration: none;
		box-shadow: none;
	}

	button {
		margin: 0;
		color: var(--zb-primary-text-color);
		font-family: var(--zb-font-stack);
		font-size: 12px;
		text-decoration: none;
		text-transform: uppercase;
		background-color: var(--zb-surface-text-active-color);
		border: none;

		-webkit-appearance: none;
		-moz-appearance: none;
	}

	input,
	select,
	textarea {
		background-color: var(--zb-surface-color);
		border-radius: 3px;
	}

	input[type='number'] {
		padding: 10.5px 12px;
		background: var(--zb-input-bg-color);
		// added to fix the arrows for mozilla firefox

		-moz-appearance: textfield;
	}

	b,
	strong {
		font-weight: 700;
	}

	h2,
	h3,
	h4,
	h5,
	h6 {
		margin-top: 0;
		margin-bottom: 25px;
		color: var(--zb-surface-text-active-color);
	}

	h3 {
		font-size: 22px;
		font-weight: 500;
		line-height: 1.4;
	}

	p {
		font-size: 12px;
		line-height: 1.8;
	}

	& * {
		box-sizing: border-box;
	}

	input[type='checkbox']:checked:before,
	input[type='radio']:checked:before {
		display: none;
	}
}

//General admin classes
.znpb-admin-modal-title-block {
	&__title {
		color: var(--zb-surface-text-active-color);
	}

	&__desc {
		color: var(--zb-surface-text-color);
	}
}

.znpb-admin {
	&__wrapper {
		position: relative;
		box-sizing: border-box;
		width: 100%;
		max-width: 1400px;
		padding-right: 20px;
		margin: 20px 0 40px 0;

		@media (max-width: 782px) {
			padding-right: 10px;
		}

		.znpb-admin__header-menu {
			.znpb-admin__header-menu-item {
				color: var(--zb-surface-text-color);
			}

			.router-link-active,
			.znpb-admin__header-menu-item:hover {
				color: var(--zb-surface-text-active-color);
			}
		}
	}

	&__header {
		position: relative;
		z-index: 1;
		padding: 0 30px;
		background: var(--zb-surface-color);
		box-shadow: 0 1px 8px rgba(0, 0, 0, 0.1);

		&-logo {
			display: flex;
			justify-content: center;
			align-items: center;

			img {
				height: 36px;
				margin-right: 10px;
			}

			span {
				margin-right: 5px;
				margin-bottom: 0;
				margin-left: 0;

				&:last-child {
					margin-right: 0;
				}
			}

			.znpb-label {
				font-size: 10px;
			}

			&-version {
				padding: 5px 6px 4px;
				font-size: 11px;
				font-weight: 500;
				background: var(--zb-surface-lighter-color);
				border-radius: 2px;
			}
		}

		&-actions {
			display: flex;
			flex-direction: row;
			align-items: center;

			.znpb-button--secondary {
				padding-top: 13px;
				padding-bottom: 13px;
				margin-right: 10px;
			}

			.znpb-editor-icon-wrapper {
				position: relative;
				top: 1px;
				margin-right: 6px;
				font-size: 14px;

				.zion-icon.zion-svg-inline {
					width: auto;
				}
			}

			.router-link-active {
				.zion-quality {
					color: var(--zb-pro-color);
				}
			}

			a:visited {
				color: #fff;
			}

			a:last-child {
				color: var(--zb-surface-text-active-color);

				&:visited {
					color: var(--zb-surface-text-active-color);
				}
			}
		}

		&-top {
			display: flex;
			justify-content: space-between;
			padding: 15px 0;
		}

		&-menu {
			margin: 0;
			margin-left: -20px;
			font-size: 15px;
			font-weight: 500;
			list-style: none;

			@media (max-width: 991px) {
				padding-bottom: 12px;
				margin-left: -16px;
			}

			&-item {
				display: inline-block;
				padding: 20px;

				@media (max-width: 991px) {
					padding: 8px 16px;
				}
			}
		}
	}

	&-notices-wrapper {
		position: fixed;
		right: 50px;
		bottom: 50px;
		display: flex;
		flex-direction: column;
		align-items: flex-end;
		min-width: 200px;
	}
}

.znpb-theme-dark .znpb-admin__header-logo img {
	filter: invert(1);

	-webkit-filter: invert(1);
}
</style>
