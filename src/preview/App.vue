<template>
	<div class="zb">
		<PageStyles
			:css-classes="getClasses"
			:page-settings-model="getPageSettings"
			:page-settings-schema="pageSettingsSchema"
		/>

		<!-- Page custom css -->
		<ElementStyles
			:styles="getPageSettings._custom_css"
		/>

		<SortableContent
			v-if="elementData"
			class="znpb-preview-page-wrapper"
			:content="elementData.content"
			:data="elementData"
		>
		</SortableContent>

		<SaveElementModal :template="false" />

	</div>

</template>
<script>
import { mapGetters, mapActions } from 'vuex'
import SaveElementModal from '@/editor/components/SaveElementModal.vue'
import PageStyles from './components/PageStyles'
import ElementStyles from './components/ElementStyles.vue'

export default {
	name: 'PreviewApp',
	data () {
		return {
			showExportModal: false,
			pageSettingsSchema: window.parent.zb.schemas.getSchema('page_settings')
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
