<template>
	<div class="znpb-admin-tools-wrapper">
		<PageTemplate>

			<h3>{{$translate('general')}}</h3>

			<div class="znpb-admin-regenerate">
				<h4>{{$translate('regenerate_css')}}</h4>
				<BaseButton
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
				</BaseButton>
			</div>
			<template slot="right">
				<p class="znpb-admin-info-p">{{$translate('tools_info')}}</p>
			</template>
		</PageTemplate>
		<PageTemplate>
			<div class="znpb-admin-regenerate">
				<h4>{{$translate('sync_library')}}</h4>
				<BaseButton
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
				</BaseButton>
			</div>
			<template slot="right">
				<p class="znpb-admin-info-p">{{$translate('regenrate_info')}}</p>
			</template>
		</PageTemplate>

	</div>
</template>

<script>
import { regenerateCache } from '@zb/rest'
import { mapActions } from 'vuex'
import { BaseButton, Loader } from '@zb/components'

export default {
	name: 'ToolsPage',
	components: {
		BaseButton,
		Loader
	},
	data () {
		return {
			loadingSync: false,
			loading: false
		}
	},
	methods: {
		...mapActions([
			'fetchTemplates'
		]),
		onRegenerateFilesClick () {
			this.loading = true
			regenerateCache().then(() => {
				this.loading = false
			})
		},
		onSyncClick () {
			this.loadingSync = true
			this.fetchTemplates(true).then(() => {
			}).catch((error) => {
				this.loadingSync = false
				console.error('error', error.message)
			})
				.finally(() => {
					this.loadingSync = false
				})
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
	border-bottom: 1px solid $surface-variant;
}
</style>
