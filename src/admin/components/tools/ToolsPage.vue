<template>
	<div class="znpb-admin-tools-wrapper">
		<PageTemplate>
			<h3>{{ translate('general') }}</h3>

			<div class="znpb-admin-regenerate">
				<h4>{{ translate('regenerate_css') }}</h4>
				<Button type="line" :class="{ ['-hasLoading']: loading }" @click="onRegenerateFilesClick">
					<template v-if="loading">
						<Loader :size="13" />
						<span v-if="filesCount > 0">{{ currentIndex }}/{{ filesCount }}</span>
					</template>

					<span v-else>{{ translate('regenerate_files') }}</span>
				</Button>
			</div>
			<template #right>
				<p class="znpb-admin-info-p">{{ translate('tools_info') }}</p>
			</template>
		</PageTemplate>
	</div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import { regenerateCache, getCacheList } from '/@/common/api';
import { translate } from '/@/common/modules/i18n';

const loading = ref(false);
const currentIndex = ref(0);
const filesCount = ref(0);

async function onRegenerateFilesClick() {
	loading.value = true;
	try {
		const { data: cacheFiles } = await getCacheList();
		filesCount.value = cacheFiles.length;

		if (filesCount.value > 0) {
			for (const fileData of cacheFiles) {
				try {
					currentIndex.value++;
					await regenerateCache(fileData);
				} catch (error) {
					console.error(error);
				}
			}
		}
	} catch (error) {
		console.error(error);
	}

	loading.value = false;
	filesCount.value = 0;
	currentIndex.value = 0;
}
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
