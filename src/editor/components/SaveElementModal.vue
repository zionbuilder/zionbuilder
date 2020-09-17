<template>
	<Modal
		:show.sync="showExportModal"
		:title="$translate('save_to_library')"
		append-to="body"
		:width="560"
		:show-maximize="true"
		class="znpb-modal-save-element"
	>
		<div class="znpb-modal-save-element-wrapper">
			<OptionsForm
				v-model="model"
				:schema="optionsSchema"
			/>

			<div class="znpb-modal-content-save-buttons">
				<BaseButton
					@click.native="saveElement"
					class="znpb-button--secondary"
				>
					<span>{{$translate('save')}}</span>
				</BaseButton>
				<BaseButton
					@click.native="downloadElement"
					class="znpb-button--line"
				>
					{{$translate('download')}}
				</BaseButton>
			</div>
			<p
				class="znpb-modal-save-element-wrapper__message"
				:class="{'znpb-modal-save-element-wrapper__message--error' : errorMessage.length > 0}"
				v-if="loadingMessage || errorMessage.length > 0"
				v-html="loadingMessage ? loadingMessage : errorMessage"
			></p>
			<Loader
				v-if="loading"
				:size="16"
				class="znpb-modal-save-element-wrapper__loading"
			/>
		</div>
	</modal>

</template>

<script>
import { saveAs } from 'file-saver'
import { Modal, Loader } from '@zb/components'
import { mapActions, mapGetters } from 'vuex'
import { exportTemplate } from '@/api/Templates'
import { compileElement } from '@/utils'

export default {
	name: 'SaveElementModal',
	components: {
		Modal,
		Loader
	},
	props: {
		template: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	data () {
		return {
			savedCategory: null,
			showExportModal: false,
			uid: null,
			loading: false,
			loadingMessage: false,
			errorMessage: '',
			model: {}
		}
	},

	computed: {
		...mapGetters([
			'getPageContent',
			'getTemplateCategories'
		]),
		templateCategoriesOption () {
			let options = []

			this.getTemplateCategories.forEach((category) => {
				options.push({
					id: category.slug,
					name: category.name
				})
			})

			return options
		},
		optionsSchema () {
			return {
				title: {
					type: 'text',
					title: this.$translate('choose_title'),
					description: this.$translate('save_title_desc')
				},
				category: {
					type: 'select',
					title: this.$translate('add_to_categ'),
					description: this.$translate('add_to_categ_desc'),
					options: this.templateCategoriesOption,
					addable: true,
					filterable: true
				}
			}
		}
	},
	created () {
		if (this.template) {
			window.ZionBuilderApi.on('save-template', this.onSavetemplate)
		} else window.ZionBuilderApi.on('save-element', this.onSaveElement)
	},
	beforeDestroy () {
		window.ZionBuilderApi.off('save-element', this.onSaveElement)
		window.ZionBuilderApi.off('save-template', this.onSavetemplate)
		this.loadingMessage = ''
		this.errorMessage = ''
	},
	methods: {
		...mapActions([
			'addTemplate',
			'getPageContentNested',
			'updateTemplateCategories'
		]),
		async saveElement () {
			// save template
			this.loading = true
			this.loadingMessage = ''
			this.errorMessage = ''
			const compiledElementData = (this.template) ? await this.getPageContentNested() : [compileElement(this.uid, this.getPageContent)]
			let templateType = (this.template) ? 'template' : 'block'

			this.addTemplate({
				title: this.model.title,
				template_type: templateType,
				template_category: this.model.category,
				template_data: compiledElementData
			}).then((response) => {
				this.loadingMessage = this.$translate('template_was_added')
				if (response.data.template_category.length > 0) {
					let addedCat = response.data.template_category[0]
					const found = this.templateCategoriesOption.findIndex(cat => cat.id === addedCat.slug)
					if (found === -1) {
						this.updateTemplateCategories(addedCat)
					}
				}
			})
				.catch((error) => {
					if (error.response !== undefined) {
						if (typeof error.response.data === 'string') {
							this.errorMessage = error.response.data
						} else this.errorMessage = this.arrayBufferToString(error.response.data)
					} else {
						// add console warn if template was saved without a category
						// in this case there is also success and error
						// eslint-disable-next-line
						console.warn(error)
						this.errorMessage = error
					}
				})
				.finally(() => {
					this.loading = false
					setTimeout(() => {
						this.loadingMessage = false
						this.errorMessage = false
						this.model = {}
					}, 3500)
				})
		},

		async downloadElement () {
			const compiledElementData = (this.template) ? await this.getPageContentNested() : compileElement(this.uid, this.getPageContent)
			let templateType = (this.template) ? 'template' : 'block'
			this.loading = true
			this.loadingMessage = ''
			this.errorMessage = ''
			exportTemplate({
				title: this.model.title,
				template_type: templateType,
				template_category: this.model.category,
				template_data: compiledElementData
			})
				.then((response) => {
					const fileName = this.model.title.length > 0 ? this.model.title : 'export'
					var blob = new Blob([response.data], { type: 'application/zip' })
					saveAs(blob, `${fileName}.zip`)
					this.loadingMessage = ''
				})
				.catch((error) => {
					if (typeof error.response.data === 'string') {
						this.errorMessage = error.response.data
					} else this.errorMessage = this.arrayBufferToString(error.response.data)
					this.model = {}
				})
				.finally(() => {
					this.loading = false
					this.model = {}
				})
		},
		onSaveElement (event) {
			this.uid = event.detail.elementUid
			this.showExportModal = true
		},
		onSavetemplate (event) {
			this.showExportModal = true
		},
		decode_utf8 (s) {
			let obj = JSON.parse(s)
			return decodeURIComponent(escape(obj.message))
		},
		arrayBufferToString (buffer) {
			var s = String.fromCharCode.apply(null, new Uint8Array(buffer))

			return this.decode_utf8(s)
		}
	}
}
</script>
<style lang="scss">
.znpb-modal-content-save-buttons {
	display: flex;
	padding: 0 5px;
	text-align: center;

	.znpb-button {
		position: relative;
		display: block;
		flex: 1;

		&--secondary {
			margin-right: 5px;
		}

		.znpb-admin-small-loader {
			top: 0;
		}
	}
}

.znpb-modal-save-element-wrapper {
	padding: 20px 15px;

	.znpb-options-form-wrapper {
		padding: 0;
	}
}

.znpb-modal-save-element-wrapper__message {
	padding: 0 30px;
	margin-top: 20px;
	margin-bottom: 0;
	color: $success;
	font-family: $font-stack;
	font-size: 12px;
	font-weight: 500;
	text-align: center;

	&--error {
		color: $error;
	}
}
.znpb-modal-save-element {
	.znpb-modal__content {
		overflow: visible;
	}
}
.znpb-modal-save-element-wrapper__loading {
	position: relative;
	position: relative;
	top: 0;
	margin: 0 auto;
	text-align: center;
	.znpb-loader {
		margin-top: 20px;
	}
}
</style>
