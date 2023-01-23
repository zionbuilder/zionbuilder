<template>
	<div class="znpb-admin-content-wrapper">
		<div class="znpb-admin-content znpb-admin-content--left znpb-admin-content--hiddenXs"></div>
		<PageTemplate>
			<Loader v-if="!loaded" />

			<template v-else>
				<h3>{{ i18n.__('System Info', 'zionbuilder') }}</h3>

				<component
					:is="getComponent(category.category_id)"
					v-for="category in systemInfoData"
					:key="category.category_id"
					:category-data="category"
				/>

				<CopyPasteServer :category-data="systemInfoData" />
			</template>

			<template #right>
				<div>
					<p class="znpb-admin-info-p">{{ i18n.__('System Info', 'zionbuilder') }}</p>
					<p class="znpb-admin-info-p">{{ i18n.__('Scroll down to copy paste the Info shown', 'zionbuilder') }}</p>
				</div>
			</template>
		</PageTemplate>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { ref } from 'vue';
import SystemList from './system-components/SystemList.vue';
import SystemPlugins from './system-components/SystemPlugins.vue';
import CopyPasteServer from './system-components/CopyPasteServer.vue';

const loaded = ref(false);
const systemInfoData = ref({});

window.zb.api.getSystemInfo().then(response => {
	systemInfoData.value = response.data;
	loaded.value = true;
});

function getComponent(categoryId: string) {
	if (categoryId === 'wordpress_environment' || categoryId === 'theme_info' || categoryId === 'server_environment') {
		return SystemList;
	} else if (categoryId === 'plugins_info') {
		return SystemPlugins;
	}
}
</script>
