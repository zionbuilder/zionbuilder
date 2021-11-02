<template>
	<Modal
		v-if="activeSaveElement.type"
		:title="$translate('save_to_library')"
		append-to="body"
		:width="560"
		:show-maximize="false"
		class="znpb-modal-save-element"
		:show="true"
		@close-modal="hideSaveElement"
	>
		<div class="znpb-modal-save-element-wrapper">
			<OptionsForm
				v-model="computedFormModel"
				:schema="optionsSchema"
			/>

			<div class="znpb-modal-content-save-buttons">
				<Button
					@click="saveElement"
					class="znpb-button--secondary"
				>
					<span>{{$translate('save')}}</span>
				</Button>
				<Button
					@click="downloadElement"
					class="znpb-button--line"
				>
					{{$translate('download')}}
				</Button>
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
import { ref, computed } from 'vue'
import { saveAs } from 'file-saver'

import { useElements, useTemplateParts, useEditorData, useSaveTemplate } from '@composables'
import { useLocalLibrary } from '@zionbuilder/composables'
import { exportTemplate } from '@zb/rest'

export default {
	name: 'SaveElementModal',
	setup () {
		const { activeSaveElement, hideSaveElement } = useSaveTemplate()
		const { getElement } = useElements()
		const { getActivePostTemplatePart } = useTemplateParts()
		const { editorData } = useEditorData()
		const formModel = ref({})
		const computedFormModel = computed({
			get () {
				return formModel.value
			},
			set (newValue) {
				formModel.value = null !== newValue ? newValue : {}
			}
		})

		return {
			getElement,
			getActivePostTemplatePart,
			templateCategories: editorData.value.template_categories,
			activeSaveElement,
			hideSaveElement,
			formModel,
			computedFormModel
		}
	},

	data () {
		return {
			loading: false,
			loadingMessage: false,
			errorMessage: ''
		}
	},

	computed: {
		templateCategoriesOption () {
			let options = []

			this.templateCategories.forEach((category) => {
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
	beforeUnmount () {
		this.loadingMessage = ''
		this.errorMessage = ''
	},
	methods: {
		saveElement () {
			const { addTemplate } = useLocalLibrary()
			const { element, type } = this.activeSaveElement
			const compiledElementData = type === 'template' ? this.getActivePostTemplatePart().toJSON() : [element.toJSON()]
			const templateType = type === 'template' ? 'template' : 'block'

			// save template
			this.loading = true
			this.loadingMessage = ''
			this.errorMessage = ''

			addTemplate({
				title: this.formModel.title,
				template_category: this.formModel.category,
				template_type: templateType,
				template_data: compiledElementData
			}).then((response) => {
				this.loadingMessage = this.$translate('template_was_added')

				if (response.data.template_category.length > 0) {
					let addedCat = response.data.template_category[0]
					const found = this.templateCategoriesOption.findIndex(cat => cat.id === addedCat.slug)

					if (found === -1) {
						this.templateCategories.push(addedCat)
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
						console.error(error)
						this.errorMessage = error
					}
				})
				.finally(() => {
					this.loading = false
					this.formModel = {}

					setTimeout(() => {
						this.loadingMessage = false
						this.errorMessage = false
					}, 3500)

				})
		},

		downloadElement () {
			const { element, type } = this.activeSaveElement
			const compiledElementData = type === 'template' ? this.getActivePostTemplatePart().toJSON() : element.toJSON()
			const templateType = type === 'template' ? 'template' : 'block'

			this.loading = true
			this.loadingMessage = ''
			this.errorMessage = ''

			exportTemplate({
				title: this.formModel.title,
				template_category: this.formModel.category,
				template_type: templateType,
				template_data: compiledElementData
			})
				.then((response) => {
					const fileName = this.formModel.title && this.formModel.title.length > 0 ? this.formModel.title : 'export'

					var blob = new Blob([response.data], { type: 'application/zip' })
					saveAs(blob, `${fileName}.zip`)
					this.loadingMessage = ''
					this.hideSaveElement()
				})
				.catch((error) => {
					if (typeof error.response.data === 'string') {
						this.errorMessage = error.response.data
					} else this.errorMessage = this.arrayBufferToString(error.response.data)
				})
				.finally(() => {
					this.loading = false
					this.formModel = {}
				})
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
	color: var(--zb-success-color);
	font-family: var(--zb-font-stack);
	font-size: 12px;
	font-weight: 500;
	text-align: center;

	&--error {
		color: var(--zb-error-color);
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
