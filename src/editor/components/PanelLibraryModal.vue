<template>
	<Modal
		v-if="UIStore.isLibraryOpen"
		v-model:fullscreen="fullSize"
		:show="true"
		append-to=".znpb-center-area"
		:width="1440"
		class="znpb-library-modal"
		@close-modal="UIStore.closeLibrary"
	>
		<template #header>
			<div class="znpb-library-modal-header">
				<span
					v-if="previewOpen || importActive"
					class="znpb-library-modal-header-preview__back"
					@click.stop="closeBody"
				>
					<Icon icon="long-arrow-right" rotate="180" />
					{{ i18n.__('Go Back', 'zionbuilder') }}
				</span>
				<div v-if="previewOpen || importActive" class="znpb-library-modal-header-preview">
					<h2 class="znpb-library-modal-header-preview__title" v-html="computedTitle"></h2>
				</div>
				<template v-else>
					<h2
						v-for="(librarySource, sourceID) in librarySources"
						:key="sourceID"
						class="znpb-library-modal-header__title"
						:class="{ 'znpb-library-modal-header__title--active': activeLibraryTab === sourceID }"
						@click="setActiveSource(sourceID)"
					>
						{{ librarySource.name }}
					</h2>
				</template>
				<div class="znpb-library-modal-header__actions">
					<template v-if="previewOpen && activeItem">
						<a
							v-if="isProInstalled && !isProActive && activeItem.pro"
							class="znpb-button znpb-button--line"
							target="_blank"
							:href="dashboardURL"
							>{{ i18n.__('Activate PRO', 'zionbuilder') }}
						</a>
						<a
							v-else-if="!isProInstalled && activeItem.pro"
							class="znpb-button znpb-button--line znpb-button-buy-pro"
							:href="purchaseURL"
							target="_blank"
							>{{ i18n.__('Buy Pro', 'zionbuilder') }}
						</a>

						<Button
							v-else
							v-znpb-tooltip="i18n.__('Insert this item into page', 'zionbuilder')"
							type="secondary"
							class="znpb-library-modal-header__insert-button"
							@click.stop="insertLibraryItem"
						>
							<span v-if="!insertItemLoading">
								{{ i18n.__('Insert', 'zionbuilder') }}
							</span>
							<Loader v-else :size="13" />
						</Button>
					</template>

					<template v-else>
						<Button type="secondary" @click="(importActive = !importActive), (templateUploaded = !templateUploaded)">
							<Icon icon="import" />
							{{ i18n.__('Import', 'zionbuilder') }}
						</Button>

						<Icon
							v-znpb-tooltip="i18n.__('Refresh data from the server ', 'zionbuilder')"
							icon="refresh"
							:size="14"
							class="znpb-modal__header-button znpb-modal__header-button--library-refresh znpb-button znpb-button--line"
							:class="{ ['loading']: activeLibraryConfig && activeLibraryConfig.loading }"
							@click="onRefresh"
						/>
					</template>

					<Icon
						:icon="fullSize ? 'shrink' : 'maximize'"
						class="znpb-modal__header-button"
						:size="14"
						@click.stop="fullSize = !fullSize"
					/>

					<Icon icon="close" :size="14" class="znpb-modal__header-button" @click="UIStore.toggleLibrary" />
				</div>
			</div>
		</template>

		<LibraryUploader v-if="importActive" @file-uploaded="onTemplateUpload" />

		<template v-else>
			<LibraryPanel
				ref="libraryContent"
				:key="activeLibraryConfig.id"
				:preview-open="previewOpen"
				:library-config="activeLibraryConfig"
				@activate-preview="activatePreview"
			/>
		</template>
	</Modal>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { ref, computed, watchEffect, provide, onMounted, onBeforeMount, onBeforeUnmount } from 'vue';
import { regenerateUIDsForContent } from '../utils';
import { useEditorData, useLocalStorage } from '../composables';
import { useUIStore } from '../store';

// Components
import LibraryPanel from './LibraryPanel.vue';
import LibraryUploader from './library-panel/LibraryUploader.vue';

const { useLibrary } = window.zb.composables;

const importActive = ref(false);
const fullSize = ref(false);
const insertItemLoading = ref(false);
const templateUploaded = ref(false);

const { addData, getData } = useLocalStorage();
const UIStore = useUIStore();
const { librarySources, getSource } = useLibrary();

const activeLibraryTab = ref(getData('libraryActiveSource', 'local_library'));

const { editorData } = useEditorData();
const isProActive = editorData.value.plugin_info.is_pro_active;
const isProInstalled = editorData.value.plugin_info.is_pro_installed;
const purchaseURL = ref(editorData.value.urls.purchase_url);
const previewOpen = ref(false);
const activeItem = ref(null);
const dashboardURL = `${editorData.value.urls.zion_admin}#/pro-license`;

const computedTitle = computed(() => {
	if (previewOpen.value) {
		return activeItem.value.post_title;
	}

	if (importActive.value) {
		return i18n.__('Import', 'zionbuilder');
	}

	return i18n.__('Library', 'zionbuilder');
});

provide('Library', {
	insertItem,
});

function setActiveSource(source, save = true) {
	activeLibraryTab.value = source;

	if (save) {
		addData('libraryActiveSource', source);
	}
}

const activeLibraryConfig = computed(() => {
	return getSource(activeLibraryTab.value) || getSource('local_library');
});

watchEffect(() => {
	if (UIStore.isLibraryOpen) {
		activeLibraryConfig.value.getData();
	}
});

function onRefresh() {
	activeLibraryConfig.value.getData(false);
}

function activatePreview(item) {
	activeItem.value = item;
	previewOpen.value = true;
}

onMounted(() => {
	document.getElementById('znpb-editor-iframe').contentWindow.document.body.style.overflow = 'hidden';
});

onBeforeUnmount(() => {
	document.getElementById('znpb-editor-iframe').contentWindow.document.body.style.overflow = null;
});

function onTemplateUpload() {
	importActive.value = false;
	setActiveSource('local');
	templateUploaded.value = true;
}

function insertLibraryItem(item) {
	insertItemLoading.value = true;
	insertItem(activeItem.value).then(() => {
		insertItemLoading.value = false;
	});
}

function closeBody() {
	previewOpen.value = false;
	importActive.value = false;
}

function insertItem(item) {
	return new Promise((resolve, reject) => {
		item
			.getBuilderData()
			.then(response => {
				const { template_data: templateData } = response.data;

				// Check to see if this is a single element or a group of elements
				const compiledTemplateData = templateData.element_type ? [templateData] : templateData;
				const newElement = regenerateUIDsForContent(compiledTemplateData);

				window.zb.run('editor/elements/add-template', {
					templateContent: newElement,
				});

				UIStore.toggleLibrary();

				resolve(true);
			})
			.catch(error => {
				reject(error);
			});
	});
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
		animation: rotation 0.55s infinite linear;
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
			transition: color 0.15s;
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
