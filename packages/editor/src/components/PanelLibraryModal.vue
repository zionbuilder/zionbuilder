<template>
	<Modal
		:show="true"
		append-to=".znpb-center-area"
		:width="1440"
		class="znpb-library-modal"
		v-model:fullscreen="fullSize"
		@close-modal="closeLibrary"
		v-if="isLibraryOpen"
	>
		<template v-slot:header>
			<div class="znpb-library-modal-header">
				<span
					v-if="previewOpen || importActive"
					@click.stop="closeBody"
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
						v-for="(librarySource, sourceID) in librarySources"
						:key="sourceID"
						class="znpb-library-modal-header__title"
						:class="{'znpb-library-modal-header__title--active': activeLibraryTab === sourceID}"
						@click="setActiveSource(sourceID)"
					>
						{{librarySource.name}}
					</h2>
				</template>
				<div class="znpb-library-modal-header__actions">
					<template v-if="previewOpen">

						<a
							v-if="isProInstalled && !isProActive && activeItem.pro"
							class="znpb-button znpb-button--line"
							target="_blank"
							:href="dashboardURL"
						>{{$translate('activate_pro')}}
						</a>
						<a
							v-else-if="!isProInstalled && activeItem.pro"
							class="znpb-button znpb-button--line znpb-button-buy-pro"
							:href="purchaseURL"
							target="_blank"
						>{{$translate('buy_pro')}}
						</a>

						<Button
							v-else
							type="secondary"
							@click.stop="insertLibraryItem"
							class="znpb-library-modal-header__insert-button"
							v-znpb-tooltip="$translate('library_insert_tooltip')"
						>
							<span v-if="!insertItemLoading">
								{{$translate('library_insert')}}
							</span>
							<Loader
								v-else
								:size="13"
							/>
						</Button>
					</template>

					<template v-else>
						<Button
							type="secondary"
							@click="importActive = !importActive , templateUploaded=!templateUploaded "
						>

							<Icon icon="import" />
							{{$translate('import')}}
						</Button>

						<Icon
							icon="refresh"
							@click="onRefresh"
							:size="14"
							class="znpb-modal__header-button znpb-modal__header-button--library-refresh znpb-button znpb-button--line"
							:class="{['loading']: activeLibraryConfig && activeLibraryConfig.loading}"
							v-znpb-tooltip="$translate('refresh_tooltip')"
						/>

					</template>

					<Icon
						:icon="fullSize ? 'shrink' : 'maximize'"
						class="znpb-modal__header-button"
						:size="14"
						@click.stop="fullSize=!fullSize"
					/>

					<Icon
						icon="close"
						:size="14"
						@click="toggleLibrary"
						class="znpb-modal__header-button"
					/>
				</div>
			</div>
		</template>

		<LibraryUploader
			v-if="importActive"
			@file-uploaded="onTemplateUpload"
		/>

		<template v-else>
			<LibraryPanel
				ref="libraryContent"
				@activate-preview="activatePreview"
				:preview-open="previewOpen"
				:library-config="activeLibraryConfig"
				:key="activeLibraryConfig.id"
			/>
		</template>

	</Modal>

</template>

<script>
import { ref, computed, watchEffect } from 'vue'
import { addOverflow, removeOverflow } from '../utils/overflow'
import { regenerateUIDsForContent } from '@utils'
import { useUI, useElements, useEditorData, useLocalStorage } from '@composables'
import { useLibrary } from '@zionbuilder/composables'

// Components
import LibraryPanel from './LibraryPanel.vue'
import LibraryUploader from './library-panel/LibraryUploader.vue'

export default {
	name: 'LibraryModal',
	components: {
		LibraryPanel,
		LibraryUploader
	},
	provide () {
		return {
			Library: this
		}
	},
	setup (props) {
		const { addData, getData } = useLocalStorage()
		const { toggleLibrary, closeLibrary, isLibraryOpen } = useUI()
		const { librarySources, getSource } = useLibrary()

		const activeLibraryTab = ref(getData('libraryActiveSource', 'local_library'))

		const { editorData } = useEditorData()
		const isProActive = editorData.value.plugin_info.is_pro_active
		const isProInstalled = editorData.value.plugin_info.is_pro_installed
		const purchaseURL = ref(editorData.value.urls.purchase_url)
		const previewOpen = ref(false)
		const activeItem = ref(null)
		const dashboardURL = `${editorData.value.urls.zion_admin}#/pro-license`

		function setActiveSource (source, save = true) {
			activeLibraryTab.value = source

			if (save) {
				addData('libraryActiveSource', source)
			}
		}

		const activeLibraryConfig = computed(() => {
			return getSource(activeLibraryTab.value) || getSource('local_library')
		})

		watchEffect(() => {
			if (isLibraryOpen.value) {
				activeLibraryConfig.value.getData()
			}
		})


		function onRefresh () {
			activeLibraryConfig.value.getData(false)
		}

		function activatePreview (item) {
			activeItem.value = item
			previewOpen.value = true
		}

		return {
			// refs
			isLibraryOpen,
			activeLibraryTab,
			previewOpen,
			activeItem,
			dashboardURL,

			// computed
			librarySources,
			activeLibraryConfig,

			// methods
			closeLibrary,
			toggleLibrary,
			editorData,
			isProActive,
			isProInstalled,
			purchaseURL,
			setActiveSource,
			onRefresh,
			activatePreview
		}
	},
	data () {
		return {
			importActive: false,
			fullSize: false,
			insertItemLoading: false,
			templateUploaded: false
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
		onTemplateUpload () {
			this.importActive = false
			this.setActiveSource('local')
			this.templateUploaded = true
		},
		insertLibraryItem (item) {
			this.insertItemLoading = true
			this.insertItem(this.activeItem).then(() => {
				this.insertItemLoading = false
			})
		},
		closeBody () {
			this.previewOpen = false
			this.importActive = false
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
				item.getBuilderData().then((response) => {
					const { template_data: templateData } = response.data
					const { insertElement, activeElement } = useLibrary()

					const { toggleLibrary } = useUI()

					// Check to see if this is a single element or a group of elements
					let compiledTemplateData = templateData.element_type ? [templateData] : templateData
					const newElement = regenerateUIDsForContent(compiledTemplateData)

					if (activeElement.value) {
						insertElement(newElement)
					} else {
						const { getElement } = useElements()
						const element = getElement(this.editorData.page_id)
						element.addChildren(newElement)
					}

					toggleLibrary()

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
	z-index: 999;
	& > .znpb-modal__wrapper {
		width: 100%;
		height: 860px;

		@media (max-width: 1440px) {
			width: calc(100% - 40px);
		}
	}

	& > .znpb-modal__wrapper--full-size {
		width: 100% !important;
		max-width: 100% !important;
		height: 100% !important;
	}
}

.znpb-modal__header-button--library-refresh {
	display: flex;
	justify-content: center;
	padding: 11px;

	&.loading svg {
		animation: rotation .55s infinite linear;
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
	color: var(--zb-surface-lighter-color);
	border-bottom: 1px solid var(--zb-surface-border-color);

	&__title {
		display: flex;
		align-items: center;
		padding: 10px 30px;
		color: var(--zb-surface-text-color);
		font-size: 13px;
		font-weight: 500;
		line-height: 1;
		border-right: 1px solid var(--zb-surface-color);
		border-left: 1px solid var(--zb-surface-color);
		cursor: pointer;

		&--active {
			color: var(--zb-surface-text-active-color);
			box-shadow: 0 1px 0 0 var(--zb-surface-color);
			border-right: 1px solid var(--zb-surface-border-color);
			border-left: 1px solid var(--zb-surface-border-color);
		}
	}

	& > .znpb-library-modal-header__actions {
		position: absolute;
		right: 0;
		display: flex;
		align-items: center;
		align-self: center;

		.znpb-button {
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
			color: var(--zb-surface-text-color);
			font-weight: 500;
			transition: color .15s;
			cursor: pointer;

			&:hover {
				color: var(--zb-surface-text-hover-color);
			}

			span:first-child {
				margin-right: 12px;
			}
		}

		&__title {
			padding: 21px 20px;
			color: var(--zb-surface-text-active-color);
			font-size: 16px;
			font-weight: 500;
		}
	}
}

.znpb-editor-library-modal-loader {
	height: 100%;
}
</style>
