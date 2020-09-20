<template>
	<div class="zb">
		<SortableContent
			v-if="elementData"
			class="znpb-preview-page-wrapper"
			:content="elementData.content"
			:data="elementData"
		>
		</SortableContent>

		<SaveElementModal :template="false" />

		<PageStyles
			:css-classes="getClasses"
			:page-settings-model="getPageSettings"
			:page-settings-schema="pageSettingsSchema"
		/>

		<!-- Page custom css -->
		<ElementStyles
			:styles="getPageSettings._custom_css"
		/>
	</div>

</template>
<script>
import { mapGetters, mapActions } from 'vuex'
import SaveElementModal from '@/editor/components/SaveElementModal.vue'
import PageStyles from './components/PageStyles'
import ElementStyles from './components/ElementStyles.vue'
import { getSchema } from '@zb/schemas'

export default {
	name: 'PreviewApp',
	data () {
		return {
			showExportModal: false,
			pageSettingsSchema: getSchema('page_settings')
		}
	},
	components: {
		SaveElementModal,
		PageStyles,
		ElementStyles
	},

	created () {
		this.setStylesLoading(false)
	},

	computed: {
		...mapGetters([
			'getPageContent',
			'isPreviewMode',
			'getClasses',
			'getPageSettings'
		]),
		elementData () {
			return this.getPageContent['contentRoot']
		}
	},
	methods: {
		...mapActions([
			'setStylesLoading'
		])
	},

	beforeDestroy () {
		window.zb.on('beforeunload', this.setStylesLoading(true))
	},
	watch: {
		isPreviewMode (newValue) {
			if (newValue) {
				window.document.body.classList.add('znpb-editor-preview--active')
			} else {
				window.document.body.classList.remove('znpb-editor-preview--active')
			}
		}
	}
}
</script>

<style lang="scss">
@import "@/scss/preview.scss";
</style>
