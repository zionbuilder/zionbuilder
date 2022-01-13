<template>
	<Modal
		:show="true"
		append-to=".znpb-center-area"
		:width="1440"
		class="znpb-library-modal"
		v-model:fullscreen="fullSize"
		:close-on-escape="true"
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
						v-for="librarySource in librarySources"
						:key="librarySource.id"
						class="znpb-library-modal-header__title"
						:class="{'znpb-library-modal-header__title--active': activeLibraryTab === librarySource.id}"
						@click="setActiveSource(librarySource.id)"
					>
						{{librarySource.name}}
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

						<a
							v-if="!isProActive && activeItem.pro"
							class="znpb-button znpb-button--line znpb-button-buy-pro"
							:href="purchaseURL"
							target="_blank"
						>{{$translate('buy_pro')}}
						</a>

						<a
							v-else-if="isProActive && !isProConnected && activeItem.pro"
							class="znpb-button znpb-button--line"
							target="_blank"
							:href="dashboardURL"
						>{{$translate('activate_pro')}}
						</a>

						<Button
							v-else
							type="secondary"
							@click.stop="insertLibraryItem"
							class="znpb-library-modal-header__insert-button"
						>
							<span v-if="!insertItemLoading">
								{{$translate('library_insert')}}
							</span>
							<Loader
								v-else
								:size="13"
							/>
						</Button>
					</Tooltip>

					<template v-else>
						<Button
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
								:class="{['loading']: activeLibraryConfig && activeLibraryConfig.loading}"
							/>
						</Tooltip>

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
import { insertTemplate } from '@zb/rest'
import { useUI, useElements, useEditorData, useLocalStorage } from '@composables'
import { useLibrary, useLibrarySources } from '@zionbuilder/composables'

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
		const { librarySources } = useLibrarySources()

		const activeLibraryTab = ref(getData('libraryActiveSource', librarySources.value[0].id))

		const { editorData } = useEditorData()
		const isProActive = ref(editorData.value.plugin_info.is_pro_active)
		const isProConnected = ref(editorData.value.plugin_info.is_pro_connected)
		const purchaseURL = ref(editorData.value.urls.purchase_url)
		const previewOpen = ref(false)
		const activeItem = ref(null)

		function setActiveSource (source, save = true) {
			activeLibraryTab.value = source

			if (save) {
				addData('libraryActiveSource', source)
			}
		}

		const activeLibraryConfig = computed(() => {
			const activeLibrary = librarySources.value.find(librarySource => librarySource.id === activeLibraryTab.value) || librarySources.value[0]

			return activeLibrary
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

			// computed
			librarySources,
			activeLibraryConfig,

			// methods
			closeLibrary,
			toggleLibrary,
			editorData,
			isProActive,
			isProConnected,
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
				insertTemplate(item.toJSON()).then((response) => {
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

		.znpb-button--secondary, .znpb-button-buy-pro {
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
