<template>
	<div class="znpb-admin-tools-wrapper">
		<PageTemplate>

			<h3>{{$translate('general')}}</h3>

			<div class="znpb-admin-regenerate">
				<h4>{{$translate('regenerate_css')}}</h4>
				<Button
					type="line"
					@click="onRegenerateFilesClick"
					:class="{['-hasLoading'] : loading}"
				>
					<transition
						name="fade"
						mode="out-in"
					>
						<Loader
							v-if="loading"
							:size="13"
						/>
						<span v-else>{{$translate('regenerate_files')}}</span>
					</transition>
				</Button>
			</div>
			<template v-slot:right>
				<p class="znpb-admin-info-p">{{$translate('tools_info')}}</p>
			</template>
		</PageTemplate>
		<PageTemplate>
			<div class="znpb-admin-regenerate">
				<h4>{{$translate('sync_library')}}</h4>
				<Button
					type="line"
					@click="onSyncClick"
				>
					<transition
						name="fade"
						mode="out-in"
					>
						<Loader
							v-if="loadingSync"
							:size="13"
						/>
						<span v-else>{{$translate('sync_library')}}</span>
					</transition>
				</Button>
			</div>
			<template v-slot:right>
				<p class="znpb-admin-info-p">{{$translate('regenrate_info')}}</p>
			</template>
		</PageTemplate>

	</div>
</template>

<script>
import { ref } from 'vue'
import { regenerateCache } from '@zb/rest'
import { useLibrary } from '@zionbuilder/composables'

export default {
	name: 'ToolsPage',
	setup () {
		const loadingSync = ref(false)
		const loading = ref(false)

		const { fetchLibraryItems } = useLibrary()

		function onRegenerateFilesClick () {
			loading.value = true
			regenerateCache().then(() => {
				loading.value = false
			})
		}

		function onSyncClick () {
			loadingSync.value = true
			fetchLibraryItems().finally(() => {
				loadingSync.value = false
			})

		}


		return {
			loadingSync,
			loading,
			onRegenerateFilesClick,
			onSyncClick
		}
	}
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
