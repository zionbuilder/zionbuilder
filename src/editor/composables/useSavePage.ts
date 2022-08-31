import { savePage as savePageREST } from '/@/common/api';
import { ref, Ref } from 'vue';
import { usePageSettingsStore, useCSSClassesStore, useContentStore, useHistoryStore } from '../store';
import { useEditorData } from './useEditorData';
import { translate } from '/@/common/modules/i18n';
import { useNotificationsStore } from '/@/common/store';
import { useResponsiveDevices } from '/@/common/composables';

const isSavePageLoading: Ref<boolean> = ref(false);
let previewWindow: Window | null = null;

export function useSavePage() {
	const save = (status = 'publish') => {
		const contentStore = useContentStore();
		const notificationsStore = useNotificationsStore();
		const pageSettings = usePageSettingsStore();
		const cssClasses = useCSSClassesStore();
		const { editorData } = useEditorData();
		const historyStore = useHistoryStore();

		const { responsiveDevices } = useResponsiveDevices();

		const pageData = {
			page_id: editorData.value.page_id,
			template_data: contentStore.getAreaContentAsJSON(editorData.value.page_id),
			page_settings: pageSettings.settings,
			css_classes: cssClasses.CSSClasses,
			breakpoints: responsiveDevices.value,
		};

		// Check if this is a draft
		if (status) {
			pageData.status = status;
		}

		if (status !== 'autosave') {
			isSavePageLoading.value = true;
		}

		return new Promise((resolve, reject) => {
			savePageREST(pageData)
				.then(response => {
					if (status !== 'autosave') {
						notificationsStore.add({
							message: status === 'publish' ? translate('page_saved_publish') : translate('page_saved'),
							delayClose: 5000,
							type: 'success',
						});
					}

					refreshPreviewWindow();

					historyStore.isDirty = false;

					return Promise.resolve(response);
				})
				.catch(error => {
					notificationsStore.add({
						message: error.message,
						type: 'error',
						delayClose: 5000,
					});

					reject(error);
				})
				.finally(() => {
					isSavePageLoading.value = false;
					resolve();
				});
		});
	};

	const savePage = () => {
		return save();
	};

	const saveDraft = () => {
		return save('draft');
	};

	const saveAutosave = () => {
		return save('autosave');
	};

	async function openPreviewPage(event) {
		const { editorData } = useEditorData();

		await saveDraft();

		previewWindow = window.open(editorData.value.urls.preview_url, `zion-preview-${editorData.value.page_id}`);

		event.preventDefault();
	}

	function refreshPreviewWindow() {
		if (previewWindow) {
			try {
				previewWindow.location.reload();
			} catch (error) {
				// Will not trigger if the preview windows is not available
			}
		}
	}

	return {
		savePage,
		saveDraft,
		saveAutosave,
		isSavePageLoading,
		previewWindow,
		openPreviewPage,
		refreshPreviewWindow,
	};
}
