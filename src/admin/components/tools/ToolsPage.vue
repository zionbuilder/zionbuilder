<template>
	<div class="znpb-admin-tools-wrapper">
		<PageTemplate>
			<h3>{{ __('General', 'zionbuilder') }}</h3>

			<div class="znpb-admin-regenerate">
				<h4>{{ __('Regenerate CSS & JS', 'zionbuilder') }}</h4>
				<Button type="line" :class="{ ['-hasLoading']: AssetsStore.isLoading }" @click="AssetsStore.regenerateCache">
					<template v-if="AssetsStore.isLoading">
						<Loader :size="13" />
						<span v-if="AssetsStore.filesCount > 0">{{ AssetsStore.currentIndex }}/{{ AssetsStore.filesCount }}</span>
					</template>

					<span v-else>{{ __('Regenerate Files', 'zionbuilder') }}</span>
				</Button>
			</div>
			<template #right>
				<p class="znpb-admin-info-p">
					{{
						__(
							'Styles are saved in CSS files in the uploads folder. Recreate those files, according to the most recent settings.',
							'zionbuilder',
						)
					}}
				</p>
			</template>
		</PageTemplate>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';

const AssetsStore = window.zb.store.useAssetsStore();
</script>
<style lang="scss">
.znpb-admin-tools-wrapper {
	display: flex;
	flex-direction: column;
	flex-basis: 100%;
	.znpb-button {
		min-width: 140px;
		padding-right: 15px;
		padding-left: 15px;
		text-align: center;
	}
	.znpb-admin-content__center {
		min-height: 165px;
		padding-bottom: 0;
	}
}
.znpb-admin-regenerate {
	display: flex;
	justify-content: space-between;
	padding-bottom: 90px;
	border-bottom: 1px solid var(--zb-surface-border-color);

	@media (max-width: 575px) {
		flex-direction: column;
	}
}
</style>
