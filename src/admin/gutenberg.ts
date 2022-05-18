// import scss file for edit with zion builder button
import './scss/edit-page.scss';

const $ = window.jQuery;
const wp = window.wp;

function initGutenberg(args) {
	const isGuttenbergActive = wp.data !== 'undefined';

	// Set class args
	let isEditorEnabled = args.is_editor_enabled;
	const postId = args.post_id;

	// Button Lock
	let isProcessingAction = false;

	// Dom
	const $body = $('body');
	let $buttonsWrapper = null;
	let $editorBlockFrame = null;
	let $editorHeader = null;
	let $editorLayout = null;

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
		$buttonsWrapper = $($('#zionbuilder-gutenberg-buttons').html());
		$editorBlockFrame = $($('#zionbuilder-gutenberg-editor-block').html());
		$editorHeader = $('.edit-post-header-toolbar');
		$editorLayout = $('.editor-block-list__layout');
		console.log('attach buttons');
		// Fix compatibility with WP 5.4
		if (!$editorLayout.length) {
			$editorLayout = $('.block-editor-block-list__layout');
		}

		if (!$editorHeader.length || !$editorLayout.length) {
			return;
		}

		// Don't proceed if already present
		if (!$editorHeader.find('.znpb-admin-post__edit').length && $editorHeader.length) {
			$editorHeader.append($buttonsWrapper);
		}

		if (!$editorLayout.find('.znpb-admin-post__edit-block').length && $editorLayout.length) {
			$editorLayout.append($editorBlockFrame);
		}

		updateUi();
	}

	function attachEvents() {
		$(document).on('click', '.znpb-admin-post__edit-button--activate', onEditButtonPress);
		$(document).on('click', '.znpb-admin-post__edit-button--deactivate', onDisableButtonPress);
	}

	/**
	 * Triggers WP autosave if it is available
	 * If WP autosave is not available, it will display an error message
	 *
	 * @param {*} event
	 */
	function onEditButtonPress(event) {
		// If editor is already enabled, just go to edit page
		if (!isEditorEnabled) {
			event.preventDefault();

			// Don't proceed if the user clicks on the button multiple times
			if (isProcessingAction) {
				return;
			}

			isProcessingAction = true;

			// Add loading class
			$('.znpb-admin-post__edit-button--activate').addClass('znpb-admin-post__edit-button--loading');

			// Set WP Title and trigger save detection
			const pageTitle = wp.data.select('core/editor').getEditedPostAttribute('title');
			if (!pageTitle) {
				wp.data.dispatch('core/editor').editPost({
					title: `ZionBuilder #${postId}`,
				});
			}

			wp.data.dispatch('core/editor').editPost({
				zion_builder_status: true,
			});

			savePost(function () {
				location.href = $('.znpb-admin-post__edit-button--activate').attr('href');
			});
		}
	}

	function performActionAfterSave(callback) {
		const saveInterval = setInterval(function () {
			if (!wp.data.select('core/editor').isSavingPost()) {
				clearInterval(saveInterval);

				if (callback) {
					callback.call();
				}

				setEditorStatus();

				isProcessingAction = false;
				$('.znpb-admin-post__edit-button--activate').removeClass('znpb-admin-post__edit-button--loading');
				$('.znpb-admin-post__edit-button--deactivate').removeClass('znpb-admin-post__edit-button--loading');
			}
		}, 300);
	}

	function updateUi() {
		if (isEditorEnabled) {
			$body.addClass('znpb-admin-post-editor--active');
		} else {
			$body.removeClass('znpb-admin-post-editor--active');
		}
	}

	/**
	 * Toogle editor status ( active/inactive )
	 */
	function setEditorStatus() {
		isEditorEnabled = wp.data.select('core/editor').getEditedPostAttribute('zion_builder_status');

		updateUi();
	}

	/**
	 * Will save the post
	 *
	 */
	function savePost(callback) {
		wp.data.dispatch('core/editor').savePost();
		performActionAfterSave(callback);
	}

	/**
	 * Will disable the editor
	 *
	 * @param {MouseEvent} event The mouse event from click
	 */
	function onDisableButtonPress(event) {
		event.preventDefault();

		if (isEditorEnabled) {
			$('.znpb-admin-post__edit-button--deactivate').addClass('znpb-admin-post__edit-button--loading');

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
