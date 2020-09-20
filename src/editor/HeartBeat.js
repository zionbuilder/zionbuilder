import store from '@/editor/store'
class HeartBeat {
	constructor () {
		/**
		 * Check if the post was locked by another user
		 */
		window.jQuery(document).on({
			'heartbeat-tick.refresh-lock': (event, response) => {
				// Don't proceed if the post is already locked
				if (!store.getters.isPostLocked && response['wp-refresh-post-lock']) {
					const { lock_error: LockError } = response['wp-refresh-post-lock']
					if (LockError) {
						store.dispatch('setLockedUser', {
							message: LockError.text,
							avatar: LockError.avatar_src
						})
					}
				}
			}
		})

		/**
		 * Send the post id to WP post locking system
		 */
		window.jQuery(document).on({
			'heartbeat-send': (event, data) => {
				data['wp-refresh-post-lock'] = {
					post_id: store.getters.getPageId
				}
			}
		})

		/**
		 * Send the post id to WP post locking system
		 */
		window.jQuery(document).on({
			'heartbeat-tick.wp-refresh-nonces': (event, response) => {
				const { rest_nonce: restNonce, heartbeat_nonce: heartbeatNonce } = response

				// Update rest nonce
				if (restNonce) {
					store.dispatch('setNonce', restNonce)
					window.ZnCommonData.nonce = restNonce
				}

				// Update Hearbeat Nonce
				if (heartbeatNonce) {
					window.heartbeatSettings.nonce = heartbeatNonce
				}
			}
		})
	}
}

export default new HeartBeat()
