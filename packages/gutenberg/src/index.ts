// import scss file for edit with zion builder button
import '@zionbuilder/css-variables/edit-page.scss'

const $ = window.jQuery
const wp = window.wp

class Gutenberg {
	constructor (args) {
		// Set class args
		this.isEditorEnabled = args.is_editor_enabled
		this.postId = args.post_id
		this.l10n = args.l10n
		this.guttenbergInitInterval = null
		this.guttenbergInitErrorCount = 0

		// Button Lock
		this.isProcessingAction = false

		if (this.isGuttenbergActive) {
			this.attachButtons().then(() => {
				this.cacheDom()
				this.updateUi()
				this.attachEvents()
			}).catch(() => {
				// If guttenberg wasn't found in 10 seconds, clear interval
				if (this.guttenbergInitErrorCount > 20) {
					clearInterval(this.guttenbergInitInterval)
				}

				this.guttenbergInitErrorCount += 1
			})
		}
	}

	get isGuttenbergActive () {
		return typeof wp.data !== 'undefined'
	}

	cacheDom () {
		// DOM CACHE
		this.$document = $(document)
		this.$window = $(window)
		this.$postTitle = $('#title')
		this.$body = $('body')
		this.$editorActivateButton = $('.znpb-admin-post__edit-button--activate')
		this.$editorDeactivateButton = $('.znpb-admin-post__edit-button--deactivate')
	}

	attachEvents () {
		// Bind events
		this.$editorActivateButton.on('click', this.onEditButtonPress.bind(this))
		this.$editorDeactivateButton.on('click', this.onDisableButtonPress.bind(this))
	}

	/**
	 *
	 * Add Editor buttons to Gutenberg editor
	 */
	attachButtons () {
		let i = 0
		return new Promise((resolve, reject) => {
			this.guttenbergInitInterval = setInterval(() => {
				if (i > 20) {
					clearInterval(this.guttenbergInitInterval)
				}
				i++

				this.$buttonsWrapper = $($('#zionbuilder-gutenberg-buttons').html())
				this.$editorBlockFrame = $($('#zionbuilder-gutenberg-editor-block').html())
				this.$editorHeader = $('.edit-post-header-toolbar')
				this.$editorLayout = $('.editor-block-list__layout')

				// Fix compatibility with WP 5.4
				if (!this.$editorLayout.length) {
					this.$editorLayout = $('.block-editor-block-list__layout')
				}

				if (this.$editorHeader.length > 0 && this.$editorLayout.length > 0) {
					this.$editorHeader.append(this.$buttonsWrapper)
					this.$editorLayout.append(this.$editorBlockFrame)

					clearInterval(this.guttenbergInitInterval)

					resolve(true)
				} else {
					// eslint-disable-next-line prefer-promise-reject-errors
					reject('Guttenberg not ready')
				}
			}, 500)
		})
	}

	/**
	 *
	 * @param {string} stringId The string id for which we need to return the translated string
	 */
	getTranslatedString (stringId) {
		if (typeof this.l10n[stringId] !== 'undefined') {
			return this.l10n[stringId]
		}

		// eslint-disable-next-line
		console.info(`String with id ${stringId} was not found.`)
	}

	/**
	 * Triggers WP autosave if it is available
	 * If WP autosave is not available, it will display an error message
	 *
	 * @param {*} event
	 */
	onEditButtonPress (event) {
		// If editor is already enabled, just go to edit page
		if (!this.isEditorEnabled) {
			event.preventDefault()

			// Don't proceed if the user clicks on the button multiple times
			if (this.isProcessingAction) {
				return
			}

			this.isProcessingAction = true

			// Add loading class
			this.$editorActivateButton.addClass('znpb-admin-post__edit-button--loading')

			// Set WP Title and trigger save detection
			const pageTitle = wp.data.select('core/editor').getEditedPostAttribute('title')
			if (!pageTitle) {
				wp.data.dispatch('core/editor').editPost({
					title: `ZionBuilder #${this.postId}`
				})
			}

			wp.data.dispatch('core/editor').editPost({
				zion_builder_status: true
			})

			this.savePost(() => {
				location.href = this.$editorActivateButton.attr('href')
			})
		}
	}

	updateUi () {
		if (this.isEditorEnabled) {
			this.$body.addClass('znpb-admin-post-editor--active')
		} else {
			this.$body.removeClass('znpb-admin-post-editor--active')
		}
	}

	/**
	 * Toogle editor status ( active/inactive )
	 */
	setEditorStatus () {
		this.isEditorEnabled = wp.data.select('core/editor').getEditedPostAttribute('zion_builder_status')

		this.updateUi()
	}

	/**
	 * Will save the post
	 *
	 * @param {function} callback The callback to call when the post is succesfully saved
	 */
	savePost (callback) {
		wp.data.dispatch('core/editor').savePost().then(() => {
			this.setEditorStatus()

			if (callback) {
				callback.call()
			}
		}).finally(() => {
			this.isProcessingAction = false
			this.$editorActivateButton.removeClass('znpb-admin-post__edit-button--loading')
			this.$editorDeactivateButton.removeClass('znpb-admin-post__edit-button--loading')
		})
	}

	/**
	 * Will disable the editor
	 *
	 * @param {MouseEvent} event The mouse event from click
	 */
	onDisableButtonPress (event) {
		event.preventDefault()

		if (this.isEditorEnabled) {
			this.$editorDeactivateButton.addClass('znpb-admin-post__edit-button--loading')

			wp.data.dispatch('core/editor').editPost({
				zion_builder_status: false
			})

			this.savePost()
		}
	}
}

/* eslint-disable no-new */
new Gutenberg(window.ZnPbEditPostData.data)