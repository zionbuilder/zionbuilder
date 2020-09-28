<template>
	<Modal
		v-if="isPostLocked"
		:show="true"
		:width="570"
		append-to="body"
		:show-maximize="false"
		:show-close="false"
	>
		<div class="znpb-post-lock-modal">
			<div class="znpb-post-lock-modal__avatar">
				<img :src="userAvatar" />
			</div>
			<div class="znpb-post-lock-modal__content">
				<div class="znpb-post-lock-modal__content-text">
					<p
						class="znpb-post-lock-modal__error-message"
						v-if="showError"
					>
						{{$translate("post_could_not_lock")}}
					</p>
					<p>{{getMessage}}</p>
				</div>
				<div class="znpb-post-lock-modal__content-buttons">
					<BaseButton type="gray">
						<a :href="previewUrl">{{$translate('post_preview')}}</a>
					</BaseButton>
					<BaseButton type="gray">
						<a :href="getAllPagesUrl">{{$translate('post_go_back')}}</a>
					</BaseButton>
					<BaseButton
						@click.native.prevent="lockPages"
						type="gray"
					>
						<a href="">{{$translate('post_take_over')}}</a>
					</BaseButton>
				</div>
			</div>
			<div
				class="znpb-post-lock-modal__loader-wrapper"
				v-if="this.showLoader"
			>
				<span class="znpb-post-lock-modal__loader">
				</span>
			</div>
		</div>
	</Modal>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { lockPage } from '@zb/rest'
import { Modal } from '@zb/components'

export default {
	name: 'PostLock',
	components: {
		Modal
	},
	data () {
		return {
			showing: true,
			showLoader: false,
			showError: false
		}
	},
	computed: {
		...mapGetters([
			'isPostLocked',
			'getLockedUserInfo',
			'getPreviewUrl',
			'getAllPagesUrl',
			'getPageId'
		]),
		userAvatar () {
			return this.getLockedUserInfo.avatar
		},
		getMessage () {
			return this.getLockedUserInfo.message
		},
		previewUrl () {
			return this.getPreviewUrl
		}
	},
	methods: {
		...mapActions([
			'takeOverPost'
		]),
		lockPages () {
			this.showLoader = true
			lockPage(this.getPageId).then((result) => {
				if (result.status === 200) {
					this.takeOverPost()
				} else if (result.status === 500) {
					this.showError = true
					// eslint-disable-next-line
					console.error(this.$translate('post_could_not_lock'))
				}
			}).finally(() => { this.showLoader = false })
		}
	}
}
</script>

<style lang="scss">
.znpb-post-lock-modal {
	display: flex;
	padding: 40px;
	&__error-message {
		color: red;
	}
	&__loader-wrapper {
		position: absolute;
		top: 0;
		left: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, .1);
	}
	&__loader {
		position: absolute;
		display: block;
		width: 35px;
		height: 35px;
		border: .2em solid #fff;
		border-bottom-color: transparent;
		border-radius: 50%;
		animation: 1s loader-animation linear infinite;
	}
	&__avatar {
		display: flex;
		align-content: center;
		align-items: center;
		margin-right: 30px;
	}
	&__content {
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		&-text {
			color: #858585;

			p {
				margin-bottom: 15px;
			}
		}
		&-buttons {
			display: flex;
			.znpb-button {
				padding: 0;
				margin-right: 5px;
				a {
					display: block;
					padding: 16px 30px;
					color: #a8a8a8;
					text-decoration: none;
					border-radius: 3px;
					transition: all .2s ease;
					&:hover {
						color: $primary-color--accent;
						background-color: $secondary !important;
					}
				}
			}
		}
	}
	@keyframes loader-animation {
		0% {
			transform: rotate(0deg);
		}
		100% {
			transform: rotate(360deg);
		}
	}
}
</style>
