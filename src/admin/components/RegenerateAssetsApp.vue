<template>
	<div v-if="showMessage" class="notice notice-warning znpb-assetRegenerationNotice">
		{{
			i18n.sprintf(
				i18n.__(/* translators: %s: Plugin name */ '%s assets needs to be regenerated.', 'zionbuilder'),
				EnvironmentStore.plugin_name,
			)
		}}

		<Button :class="{ ['-hasLoading']: AssetsStore.isLoading }" @click="regenerateAssets">
			<template v-if="AssetsStore.isLoading">
				<Loader :size="13" />
				<span v-if="AssetsStore.filesCount > 0">{{ AssetsStore.currentIndex }}/{{ AssetsStore.filesCount }}</span>
			</template>

			<span v-else>{{ i18n.__('Regenerate Files', 'zionbuilder') }}</span>
		</Button>
	</div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import * as i18n from '@wordpress/i18n';
import { useAssetsStore, useEnvironmentStore } from '@zb/store';

const { Button, Loader } = window.zb.components;
const showMessage = ref(true);

const AssetsStore = useAssetsStore();
const EnvironmentStore = useEnvironmentStore();

async function regenerateAssets() {
	try {
		await AssetsStore.regenerateCache();

		// Update Flag
		AssetsStore.finish();

		showMessage.value = false;
	} catch (error) {
		console.error(error);
	}
}
</script>

<style>
.znpb-assetRegenerationNotice {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 15px 25px;
}
</style>
