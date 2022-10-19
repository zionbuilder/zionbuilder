<template>
	<div v-if="showMessage" class="notice notice-warning znpb-assetRegenerationNotice">
		{{ translate('cache_needs_to_be_regenerated') }}

		<Button :class="{ ['-hasLoading']: AssetsStore.isLoading }" @click="regenerateAssets">
			<template v-if="AssetsStore.isLoading">
				<Loader :size="13" />
				<span v-if="AssetsStore.filesCount > 0">{{ AssetsStore.currentIndex }}/{{ AssetsStore.filesCount }}</span>
			</template>

			<span v-else>{{ translate('regenerate_files') }}</span>
		</Button>
	</div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import { translate } from '/@/common/modules/i18n';
import { useAssetsStore } from '/@/common/store';
import { Button } from '/@/common/components/Button';
import { Loader } from '/@/common/components/Loader';

const showMessage = ref(true);

const AssetsStore = useAssetsStore();
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
