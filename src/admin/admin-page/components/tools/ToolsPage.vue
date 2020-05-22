<template>
	<div class="znpb-admin-tools-wrapper">
		<PageTemplate>

			<h3>{{$translate('general')}}</h3>

			<div class="znpb-admin-regenerate">
				<h4>Regenerate CSS</h4>
				<BaseButton
					type="line"
					@click.native="onRegenerateFilesClick"
				>
					<Loader v-if="loading" />
					Regenerate Files
				</BaseButton>
			</div>
			<template slot="right">
				<p class="znpb-admin-info-p">Styles set in Zion are saved in CSS files in the uploads folder. Recreate those files, according to the most recent settings.</p>
			</template>
		</PageTemplate>
		<PageTemplate>
			<div class="znpb-admin-regenerate">
				<h4>Sync Library</h4>
				<BaseButton
					type="line"
					@click.native="onSyncClick"
				>
					<Loader v-if="loadingSync" />
					Sync Library
				</BaseButton>
			</div>
			<template slot="right">
				<p class="znpb-admin-info-p">Zion Library automatically updates on a daily basis. You can also manually update it by clicking on the sync button.</p>
			</template>
		</PageTemplate>

	</div>
</template>

<script>
import { regenerateCache } from '@/api/RegenerateCache'
import { mapActions } from 'vuex'
export default {
	name: 'ToolsPage',
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
