<template>
	<Modal
		:show="true"
		append-to=".znpb-center-area"
		:width="1440"
		class="znpb-library-modal"
		:fullscreen="showMaximize"
	>
		<template v-slot:header>
			<div class="znpb-library-modal-header">
				<span
					v-if="previewOpen || multiple || importActive"
					@click="closeBody"
					class="znpb-library-modal-header-preview__back"
				>
					<Icon
						icon="long-arrow-right"
						rotate="180"
					/>
					{{$translate('go_back')}}
				</span>
				<div
					v-if="previewOpen || importActive"
					class="znpb-library-modal-header-preview"
				>

					<h2
						class="znpb-library-modal-header-preview__title"
						v-html="computedTitle"
					>
					</h2>
				</div>
				<template v-else>
					<h2
						class="znpb-library-modal-header__title"
						:class="{'znpb-library-modal-header__title--active': localActive}"
						@click="localActive=true, zionActive=false"
					>
						{{$translate('local_library')}}
					</h2>
					<h2
						class="znpb-library-modal-header__title"
						:class="{'znpb-library-modal-header__title--active': zionActive}"
						@click="localActive=false, zionActive=true"
					>
						{{$translate('zion_library')}}
					</h2>
				</template>
				<div class="znpb-library-modal-header__actions">
					<Tooltip
						v-if="previewOpen"
						append-to="element"
						tag="span"
						:content="$translate('library_insert_tooltip')"
						:modifiers="[{name: 'offset',options: {	offset: [0, 10]}}]"
						placement="top"
						strategy="fixed"
					>
						<Button
							type="secondary"
							@click="insertLibraryItem"
							class="znpb-library-modal-header__insert-button"
						>
							<span v-if="!insertItemLoading">{{$translate('library_insert')}}</span>
							<Loader
								v-else
								:size="13"
							/>
						</Button>
					</Tooltip>

					<template v-else>
						<Button
							v-if="localActive"
							type="secondary"
							@click="importActive = !importActive , templateUploaded=!templateUploaded "
						>

							<Icon icon="import" />
							{{$translate('import')}}
						</Button>

						<Tooltip
							v-if="!importActive"
							:content="$translate('refresh_tooltip')"
							tag="span"
							placement="top"
							class="znpb-modal__header-button znpb-modal__header-button--library-refresh znpb-button znpb-button--line"
						>
							<Icon
								icon="refresh"
								@click="onRefresh"
								:size="14"
								:class="{['loading']: libLoading}"
							/>
						</Tooltip>

					</template>

					<Icon
						:icon="fullSize ? 'shrink' : 'maximize'"
						class="znpb-modal__header-button"
						:size="14"
						@click="fullSize = ! fullSize"
					/>

					<Icon
						icon="close"
						:size="14"
						@click="togglePanel('PanelLibraryModal')"
						class="znpb-modal__header-button"
					/>
				</div>
			</div>
		</template>
		<LibraryUploader
			v-if="importActive"
			@file-uploaded="onTemplateUpload"
		/>

		<LibraryPanel
			v-if="!localActive && !importActive"
			@activate-preview="previewOpen=true, activeItem=$event"
			@activate-multiple="multiple=$event"
			@loading-start="libLoading = true"
			@loading-end="libLoading = false"
			:preview-open="previewOpen"
			:multiple="multiple"
			:import-active="importActive"
			ref="libraryContent"
		/>

		<localLibrary
			ref="localLibraryContent"
			v-if="localActive && !importActive"
			:preview-open="previewOpen"
			@activate-preview="activatePreview"
			@loading-start="libLoading = true"
			@loading-end="libLoading = false"
		/>
	</Modal>

</template>

<script>
import { addOverflow, removeOverflow } from '../utils/overflow'
import { mapActions } from 'vuex'
import { regenerateUIDsForContent } from '@utils'
import { insertTemplate } from '@zb/rest'
import { generateElements, generateUID } from '@zb/utils'
import { usePanels, useElements } from '@composables'
import { useLibrary, useLocalLibrary } from '@zionbuilder/composables'

// Components
import LibraryPanel from './LibraryPanel.vue'
import LibraryUploader from './library-panel/LibraryUploader.vue'
import localLibrary from './library-panel/localLibrary.vue'

export default {
	name: 'LibraryModal',
	components: {
		LibraryPanel,
		LibraryUploader,
		localLibrary,
	},
	provide () {
		return {
			Library: this
		}
	},
	setup (props) {
		const { togglePanel } = usePanels()
		const { fetchTemplates } = useLocalLibrary()
		return {
			togglePanel,
			fetchTemplates
		}
	},
	data () {
		return {
			libLoading: false,
			importActive: false,
			multiple: false,
			showMaximize: false,
			fullSize: false,
			localActive: false,
			zionActive: true,
			previewOpen: false,
			activeItem: null,
			insertItemLoading: false,
			templateUploaded: false
		}
	},
	watch: {
		fullSize (newVal) {
			if (newVal) {
				this.showMaximize = newVal
			} else this.showMaximize = this.fullSize
		}

	},
	computed: {
		computedTitle () {
			return this.previewOpen ? this.activeItem.post_title : this.$translate('import')
		}

	},
	mounted () {
		addOverflow(document.getElementById('znpb-editor-iframe').contentWindow.document.body)
	},
	methods: {
		...mapActions([
			'insertElements'
		]),
		onTemplateUpload () {
			this.importActive = false
			this.localActive = true
			this.templateUploaded = true
		},
		insertLibraryItem () {
			this.insertItemLoading = true
			this.insertItem(this.activeItem).then(() => {
				this.insertItemLoading = false
			})
		},

		onRefresh () {
			this.templateUploaded = false

			this.localActive ? this.fetchTemplates() : this.$refs.libraryContent.getDataFromServer(false)
		},
		closeBody () {
			if (this.multiple && this.previewOpen) {
				this.previewOpen = false
			} else {
				this.previewOpen = false
				this.multiple = false
				this.importActive = false
			}
		},
		activatePreview (item) {
			this.activeItem = item
			this.previewOpen = true
		},

		/**
		 * Insert item
		 *
		 * Handles template insertion
		 * Will generate new UIDs for elements
		 * Will add the page custom css and js
		 * Will add the custom css classes used for element
		 */
		insertItem (item) {
			return new Promise((resolve, reject) => {
				insertTemplate(item).then((response) => {
					const { template_data: templateData } = response.data
					const { activeElementForLibrary } = useLibrary()
					const { getElement } = useElements()

					// Check to see if this is a single element or a group of elements
					let compiledTemplateData = templateData.element_type ? [templateData] : templateData
					const activeElement = activeElementForLibrary.value ? activeElementForLibrary.value : getElement('content')

					activeElement.addChildren(regenerateUIDsForContent(compiledTemplateData))

					this.togglePanel('PanelLibraryModal')

					resolve(true)
				}).catch((error) => {
					reject(error)
				})
			})
		}
	},
	beforeUnmount () {
		const { unsetActiveElementForLibrary } = useLibrary()
		removeOverflow(document.getElementById('znpb-editor-iframe').contentWindow.document.body)
		unsetActiveElementForLibrary()
	}
}
</script>
<style lang="scss">
.znpb-library-modal {
	& > .znpb-modal__wrapper {
		width: 100%;
		height: 860px;

		@media (max-width: 1440px) {
			width: calc(100% - 40px);
		}
	}
}

.znpb-modal__header-button--library-refresh {
	display: flex;
	justify-content: center;
	padding: 0;

	.znpb-editor-icon-wrapper {
		padding: 11px;

		&.loading {
			animation: rotation .55s infinite linear;
		}
	}
}

@keyframes rotation {
	from {
		transform: rotate(0deg);
	}
	to {
		transform: rotate(359deg);
	}
}

.znpb-library-modal-header {
	position: relative;
	display: flex;
	justify-content: center;
	flex-shrink: 0;
	height: 58px;
	color: $surface-variant;
	border-bottom: 1px solid $surface-variant;

	&__title {
		display: flex;
		align-items: center;
		padding: 10px 30px;
		color: $surface-headings-color;
		font-size: 13px;
		font-weight: 500;
		line-height: 1;
		border-right: 1px solid white;
		border-left: 1px solid white;
		cursor: pointer;

		&--active {
			color: $surface-active-color;
			box-shadow: 0 1px 0 0 white;
			border-right: 1px solid $surface-variant;
			border-left: 1px solid $surface-variant;
		}
	}

	& > .znpb-library-modal-header__actions {
		position: absolute;
		right: 0;
		display: flex;
		align-items: center;
		align-self: center;

		.znpb-button--secondary {
			display: flex;
			align-items: center;
			padding: 13px 20px;
			margin-right: 15px;

			.znpb-editor-icon-wrapper {
				margin-right: 5px;
			}
		}
	}

	&-preview {
		&__back {
			position: absolute;
			top: 23px;
			left: 20px;
			display: flex;
			align-self: center;
			color: $font-color;
			font-weight: 500;
			transition: color .15s;
			cursor: pointer;

			&:hover {
				color: darken($font-color, 10%);
			}

			span:first-child {
				margin-right: 12px;
			}
		}

		&__title {
			padding: 21px 20px;
			color: $surface-active-color;
			font-size: 16px;
			font-weight: 500;
		}
	}
}

.znpb-editor-library-modal-loader {
	height: 100%;
}
</style>
