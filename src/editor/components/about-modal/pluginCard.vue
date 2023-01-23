<template>
	<div
		class="znpb-about-modal__version-card"
		:class="{ 'znpb-about-modal__version-card--active': !EnvironmentStore.plugin_pro.is_active || isProActive }"
	>
		<Icon icon="zion-icon-logo" />
		<div v-if="isPro" class="znpb-pro-item">{{ i18n.__('pro', 'zionbuilder') }}</div>
		<span class="znpb-about-modal__plugin-title"
			>{{ EnvironmentStore.plugin_name }}
			<span v-if="isPro">{{ i18n.__('pro', 'zionbuilder') }}</span>
			<span v-else>{{ i18n.__('FREE', 'zionbuilder') }}</span>
		</span>
		<div class="znpb-about-modal-text-wrapper">
			<template v-if="!isPro">
				<template v-if="version !== null && updateVersion !== null">
					<span>{{ version }}</span>
					<a
						:href="EnvironmentStore.urls.free_changelog"
						target="_blank"
						title="changelog"
						class="znpb-about-modal__help"
						>{{ i18n.__('View changelog', 'zionbuilder') }}</a
					>
				</template>
			</template>
			<template v-else>
				<span v-if="!isProActive && isPro">
					{{ i18n.__('Not installed!', 'zionbuilder') }}
				</span>
				<template v-if="version !== null && updateVersion !== null">
					<span>{{ version }}</span>
					<a
						:href="EnvironmentStore.urls.pro_changelog"
						target="_blank"
						title="changelog"
						class="znpb-about-modal__help"
						>{{ i18n.__('View changelog', 'zionbuilder') }}</a
					>
				</template>
			</template>
		</div>
		<template v-if="!isPro">
			<a
				v-if="updateVersion !== null && updateVersion !== version && version !== null"
				:href="EnvironmentStore.urls.updates_page"
				target="_blank"
				class="znpb-button znpb-about-modal__version-card-button"
				>{{ i18n.__('Update to', 'zionbuilder') }} {{ updateVersion }}
			</a>
			<span v-else class="znpb-about-modal-text-wrapper__up-to-date">
				{{ i18n.__('You are up to date!', 'zionbuilder') }}
			</span>
		</template>
		<template v-else>
			<a
				v-if="!isProActive"
				:href="EnvironmentStore.urls.purchase_url"
				target="_blank"
				title="purchase"
				class="znpb-button znpb-button--secondary"
				>{{ i18n.__('Buy Pro', 'zionbuilder') }}
			</a>
			<template v-else>
				<span
					v-if="updateVersion === undefined || version === null || updateVersion === version"
					class="znpb-about-modal-text-wrapper__up-to-date"
				>
					{{ i18n.__('You are up to date!', 'zionbuilder') }}
				</span>
				<a
					v-else
					:href="EnvironmentStore.urls.updates_page"
					title="updates"
					target="_blank"
					class="znpb-button znpb-about-modal__version-card-button"
					>{{ i18n.__('Update to version', 'zionbuilder') }} {{ updateVersion }}
				</a>
			</template>
		</template>
	</div>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';

import { useEnvironmentStore } from '@zb/store';

withDefaults(
	defineProps<{
		isPro?: boolean;
		isProActive?: boolean;
		version?: string | null;
		updateVersion?: string | null;
	}>(),
	{
		isPro: false,
		isProActive: false,
		version: null,
		updateVersion: null,
	},
);

const EnvironmentStore = useEnvironmentStore();
</script>

<style lang="scss">
.znpb-about-modal-text-wrapper {
	display: flex;
	flex-direction: column;
	justify-content: space-around;
	align-items: center;
	height: 100%;
}
.znpb-about-modal {
	&__plugin-title {
		margin-bottom: 16px;
		color: var(--zb-surface-text-active-color);
		font-weight: 500;
		span {
			text-transform: uppercase;
		}
	}

	&__version-card {
		position: relative;
		display: flex;
		flex-direction: column;
		align-items: center;
		flex: 1;
		background: var(--zb-surface-light-color);
		margin-bottom: 25px;
		border-radius: 3px;

		span {
			margin-bottom: 15px;
			opacity: 0.7;
		}
		.znpb-editor-icon-wrapper {
			padding: 30px 0 20px 0;
			margin-bottom: 0;
			font-size: 44px;
			opacity: 0.7;
		}

		&:hover,
		&--active {
			background: var(--zb-surface-lighter-color);
			.znpb-editor-icon-wrapper,
			span {
				opacity: 1;
			}
		}
		&:first-of-type {
			margin-right: 20px;
		}
		&:last-of-type {
			margin-right: 0;
		}
		.znpb-button {
			padding: 14px 20px;
		}

		.znpb-about-modal__version-card-button {
			background-color: #66c461;
		}
		.znpb-about-modal__help {
			margin-bottom: 20px;
		}
		& > .znpb-button {
			&.znpb-about-modal__version-card-button,
			&.znpb-button--secondary {
				margin-bottom: 13px;
			}
		}
		.znpb-about-modal-text-wrapper__up-to-date {
			margin-bottom: 28px;
		}
	}
}

.znpb-pro-item {
	position: absolute;
	top: 30px;
	right: 90px;
	padding: 4px 8px;
	color: #fff;
	font-size: 10px;
	font-weight: 700;
	text-transform: uppercase;
	background-color: var(--zb-pro-color);
	border-radius: 2px;
}
</style>
