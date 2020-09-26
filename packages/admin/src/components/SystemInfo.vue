<template>
	<div class="znpb-admin-content-wrapper">
		<div class="znpb-admin-content znpb-admin-content--left">
		</div>
		<PageTemplate>

			<Loader v-if="!loaded" />
			<template v-else>
				<h3>{{$translate('system_info')}}</h3>
				<component
					v-for="category in getSystemInfo"
					:key="category.category_id"
					:category-data="category"
					:is="getComponent(category.category_id)"
				/>
				<component
					:category-data="getSystemInfo"
					:is="getNewComponent(getSystemInfo)"
				/>
			</template>
			<template v-slot:right>
				<div>
					<p class="znpb-admin-info-p">{{$translate('system_info')}}</p>
					<p class="znpb-admin-info-p">{{$translate('system_info_desc')}}</p>
				</div>
			</template>
		</PageTemplate>

	</div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

import SystemList from './system-components/SystemList.vue'
import SystemPlugins from './system-components/SystemPlugins.vue'
import CopyPasteServer from './system-components/CopyPasteServer.vue'

import { Loader } from '@zb/components'

export default {
	name: 'SystemInfo',
	components: {
		SystemList,
		SystemPlugins,
		CopyPasteServer,
		Loader
	},
	data () {
		return {
			loaded: false
		}
	},
	computed: {
		...mapGetters([
			'getSystemInfo'
		])

	},
	methods: {
		...mapActions([
			'fetchSystemInfo'
		]),
		getComponent (categoryId) {
			if ((categoryId === 'wordpress_environment') || (categoryId === 'theme_info') || (categoryId === 'server_environment')) {
				return 'SystemList'
			} else if (categoryId === 'plugins_info') {
				return 'SystemPlugins'
			}
		},
		getNewComponent (getSystemInfo) {
			return 'CopyPasteServer'
		}

	},
	created () {
		// Fetch system information from rest api
		const syst = this.fetchSystemInfo()
		Promise.all([syst]).finally((result) => {
			this.loaded = true
		})
	}
}
</script>
