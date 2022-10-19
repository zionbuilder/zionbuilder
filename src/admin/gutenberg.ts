// import scss file for edit with zion builder button
import './scss/edit-page.scss';

const wp = window.wp;

function initGutenberg(args: { post_id: number; is_editor_enabled: boolean; l10n: Record<string, string> }) {
	const isGuttenbergActive = wp.data !== 'undefined';

	// Set class args
	let isEditorEnabled = args.is_editor_enabled;
	const postId = args.post_id;

	// Button Lock
	let isProcessingAction = false;

	function init() {
		if (isGuttenbergActive) {
			wp.data.subscribe(() => {
				setTimeout(() => {
					attachButtons();
				}, 100);
			});

			attachEvents();
		}
	}

	function attachButtons() {
		const buttonWrapperMarkup = document.getElementById('zionbuilder-gutenberg-buttons')?.innerHTML;
		const editorBlockFrame = document.getElementById('zionbuilder-gutenberg-editor-block')?.innerHTML;
		const editorHeader = document.querySelector('.edit-post-header-toolbar');
		let editorLayout = document.querySelector('.editor-block-list__layout');

		// Fix compatibility with WP 5.4
		if (!editorLayout) {
			editorLayout = document.querySelector('.block-editor-block-list__layout');
		}

		if (!editorHeader || !editorLayout || !buttonWrapperMarkup || !editorBlockFrame) {
			return;
		}

		// Don't proceed if already present
		if (!editorHeader.querySelector('.znpb-admin-post__edit') && editorHeader) {
			editorHeader.insertAdjacentHTML('beforeend', buttonWrapperMarkup);
		}

		if (!editorLayout.querySelector('.znpb-admin-post__edit-block') && editorLayout) {
			editorLayout.insertAdjacentHTML('beforeend', editorBlockFrame);
		}

		updateUi();
	}

	function attachEvents() {
		document.addEventListener('click', onEditButtonPress);
		document.addEventListener('click', onDisableButtonPress);
	}

	/**
	 * Triggers WP autosave if it is available
	 * If WP autosave is not available, it will display an error message
	 *
	 * @param {*} event
	 */
	function onEditButtonPress(event: MouseEvent) {
		const target = <HTMLElement>event.target;
		if (
			target &&
			(!target.classList.contains('znpb-admin-post__edit-button--activate') ||
				!target.closest('.znpb-admin-post__edit-button--activate'))
		) {
			return;
		}

		// If editor is already enabled, just go to edit page
		if (!isEditorEnabled) {
			event.preventDefault();

			// Don't proceed if the user clicks on the button multiple times
			if (isProcessingAction) {
				return;
			}

			isProcessingAction = true;

			// Add loading class
			document
				.querySelector('.znpb-admin-post__edit-button--activate')
				?.classList.add('znpb-admin-post__edit-button--loading');

			// Set WP Title and trigger save detection
			const pageTitle = wp.data.select('core/editor').getEditedPostAttribute('title');

			if (!pageTitle || pageTitle.length === 0) {
				wp.data.dispatch('core/editor').editPost({
					title: `ZionBuilder #${postId}`,
				});
			}

			wp.data.dispatch('core/editor').editPost({
				zion_builder_status: true,
			});

			savePost(function () {
				const editURL = document.querySelector('.znpb-admin-post__edit-button--activate')?.getAttribute('href');
				if (editURL) {
					location.href = editURL;
				}
			});
		}
	}

	function performActionAfterSave(callback?: () => void) {
		const saveInterval = setInterval(function () {
			if (!wp.data.select('core/editor').isSavingPost()) {
				clearInterval(saveInterval);

				if (callback) {
					callback.call(null);
				}

				setEditorStatus();

				isProcessingAction = false;
				document
					.querySelector('.znpb-admin-post__edit-button--activate')
					?.classList.remove('znpb-admin-post__edit-button--loading');

				document
					.querySelector('.znpb-admin-post__edit-button--deactivate')
					?.classList.remove('znpb-admin-post__edit-button--loading');
			}
		}, 300);
	}

	function updateUi() {
		if (isEditorEnabled) {
			document.body.classList.add('znpb-admin-post-editor--active');
		} else {
			document.body.classList.remove('znpb-admin-post-editor--active');
		}
	}

	/**
	 * Toggle editor status ( active/inactive )
	 */
	function setEditorStatus() {
		isEditorEnabled = wp.data.select('core/editor').getEditedPostAttribute('zion_builder_status');

		updateUi();
	}

	/**
	 * Will save the post
	 *
	 */
	function savePost(callback?: () => void) {
		wp.data.dispatch('core/editor').savePost();
		performActionAfterSave(callback);
	}

	/**
	 * Will disable the editor
	 *
	 * @param {MouseEvent} event The mouse event from click
	 */
	function onDisableButtonPress(event: MouseEvent) {
		const target = <HTMLElement>event.target;
		if (
			target &&
			(!target.classList.contains('znpb-admin-post__edit-button--deactivate') ||
				!target.closest('.znpb-admin-post__edit-button--deactivate'))
		) {
			return;
		}

		event.preventDefault();

		if (isEditorEnabled) {
			document
				.querySelector('.znpb-admin-post__edit-button--deactivate')
				?.classList.add('znpb-admin-post__edit-button--loading');

			wp.data.dispatch('core/editor').editPost({
				zion_builder_status: false,
			});

			savePost();
		}
	}

	return {
		init,
	};
}

initGutenberg(window.ZnPbEditPostData.data).init();
