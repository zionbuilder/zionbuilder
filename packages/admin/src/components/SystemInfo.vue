<template>
	<div class="znpb-admin-content-wrapper">
		<div class="znpb-admin-content znpb-admin-content--left znpb-admin-content--hiddenXs">
		</div>
		<PageTemplate>
			<Loader v-if="!loaded" />

			<template v-else>
				<h3>{{$translate('system_info')}}</h3>

				<component
					v-for="category in systemInfoData"
					:key="category.category_id"
					:category-data="category"
					:is="getComponent(category.category_id)"
				/>

				<CopyPasteServer :category-data="systemInfoData" />
			</template>

			<template #right>
				<div>
					<p class="znpb-admin-info-p">{{$translate('system_info')}}</p>
					<p class="znpb-admin-info-p">{{$translate('system_info_desc')}}</p>
				</div>
			</template>
		</PageTemplate>

	</div>
</template>

<script>

import SystemList from './system-components/SystemList.vue'
import SystemPlugins from './system-components/SystemPlugins.vue'
import CopyPasteServer from './system-components/CopyPasteServer.vue'
import { getSystemInfo } from '@zionbuilder/rest'

export default {
	name: 'SystemInfo',
	components: {
		SystemList,
		SystemPlugins,
		CopyPasteServer
	},
	data () {
		return {
			loaded: false,
			systemInfoData: {}
		}
	},
	methods: {
		getComponent (categoryId) {
			if ((categoryId === 'wordpress_environment') || (categoryId === 'theme_info') || (categoryId === 'server_environment')) {
				return 'SystemList'
			} else if (categoryId === 'plugins_info') {
				return 'SystemPlugins'
			}
		}
	},
	created () {
		getSystemInfo().then((response) => {
			this.systemInfoData = response.data
			this.loaded = true
		})

	}
}
</script>
