<template>
	<div class="znpb-input-background-video">

		<div class="znpb-input-background-video__holder">
			<div
				v-if="hasVideo"
				ref="videoPreview"
				class="znpb-input-background-video__source"
			/>

			<EmptyList
				v-else
				class="znpb-input-background-video__empty znpb-input-background-video__source"
				:no-margin="true"
				@click.native="openMediaModal"
			>
				{{$translate('no_video_selected')}}
			</EmptyList>

			<BaseIcon
				v-if="this.videoSourceModel == 'local' && hasVideo"
				class="znpb-input-background-video__delete"
				icon="delete"
				:bg-size="30"
				bg-color="#fff"
				@click.native.stop="deleteVideo"
			/>

		</div>
		<OptionsForm
			:schema="schema"
			v-model="valueModel"
			class="znpb-input-background-video__holder"
		/>
	</div>
</template>
<script>
import BaseIcon from '@/common/components/BaseIcon.vue'
import EmptyList from '../empty-list/EmptyList'
import Video from '@/common/vendors/Video.js'
import { getSchema } from '@zb/schemas'

export default {
	name: 'InputBackgroundVideo',
	components: {
		EmptyList,
		BaseIcon
	},
	props: ['value', 'options', 'exclude_options'],
	data () {
		return {
			videoInstance: null,
			mediaModal: null
		}
	},
	watch: {
		valueModel (newValue, oldValue) {
			if (this.videoInstance) {
				this.videoInstance.destroy()
			}
			if (this.hasVideo) {
				this.initVideo()
			}
		}
	},
	mounted () {
		if (this.hasVideo) {
			this.initVideo()
		}
	},
	computed: {
		schema () {
			let schema = { ...getSchema('video') }

			if (this.exclude_options) {
				this.exclude_options.forEach(optionToRemove => {
					if (schema[optionToRemove]) {
						delete schema[optionToRemove]
					}
				})
			}
			return schema
		},
		valueModel: {
			get () {
				return this.value || {}
			},
			set (newValue) {
				this.$emit('input', newValue)
			}
		},
		hasVideo () {
			if (this.videoSourceModel === 'local' && this.valueModel.mp4) {
				return true
			}

			if (this.videoSourceModel === 'youtube' && this.valueModel.youtubeURL) {
				return true
			}

			if (this.videoSourceModel === 'vimeo' && this.valueModel.vimeoURL) {
				return true
			}

			return false
		},
		controlsModel: {
			get () {
				return !!this.valueModel.controls
			},
			set (newValue) {
				this.$emit('input', {
					...this.valueModel,
					'controls': newValue
				})
			}
		},
		autoplayModel: {
			get () {
				return typeof this.valueModel['autoplay'] === 'undefined' ? true : this.valueModel['autoplay']
			},
			set (newValue) {
				this.$emit('input', {
					...this.valueModel,
					'autoplay': newValue
				})
			}
		},
		mutedModel: {
			get () {
				return typeof this.valueModel['muted'] === 'undefined' ? true : this.valueModel['muted']
			},
			set (newValue) {
				this.$emit('input', {
					...this.valueModel,
					'muted': newValue
				})
			}
		},
		videoSourceModel: {
			get () {
				return this.valueModel['videoSource'] || 'local'
			},
			set (newValue) {
				this.$emit('input', {
					...this.value,
					'videoSource': newValue
				})
			}
		}
	},
	methods: {
		initVideo () {
			this.$nextTick(() => {
				this.videoInstance = new Video(this.$refs.videoPreview, this.valueModel)
			})
		},
		openMediaModal () {
			if (this.mediaModal === null) {
				const args = {
					frame: 'select',
					state: 'library',
					library: { type: 'video' },
					button: { text: 'Add video' },
					selection: this.valueModel
				}

				// Create the frame
				this.mediaModal = window.wp.media(args)

				this.mediaModal.on('select update insert', this.selectMedia)
			}

			// Open the media modal
			this.mediaModal.open()
		},
		selectMedia (e) {
			let selection = this.mediaModal.state().get('selection').toJSON()
			// In case we have multiple items
			if (typeof e !== 'undefined') { selection = e }
			this.$emit('input', {
				...this.valueModel,
				mp4: selection[0].url
			})
		},
		deleteVideo () {
			const { mp4, ...rest } = this.valueModel
			this.$emit('input', {
				...rest
			})
		}
	}
}
</script>

<style lang="scss">
.znpb-input-background-video .znpb-options-form-wrapper {
	padding: 0;
	margin-right: -5px;
	margin-left: -5px;
}

.znpb-input-background-video {
	&__delete {
		position: absolute;
		top: 10px;
		right: 10px;
		z-index: 1;
		display: flex;
		justify-content: flex-end;
		border-radius: 3px;
		transition: all .2s ease;
		cursor: pointer;
		opacity: 0;
	}
	&__holder {
		position: relative;
		margin-bottom: 20px;

		&:hover {
			.znpb-input-background-video__delete {
				opacity: 1;
			}
		}
		iframe {
			width: 100%;
		}
		&--preview {
			width: 100%;
		}
	}
	&__input {
		.znpb-input-content {
			display: flex;
		}
		.zion-input {
			margin-right: 5px;
		}
	}
	.znpb-input-media-wrapper {
		width: 100%;
	}
	.znpb-empty-list__content {
		padding: 50px 30px;
	}

	& > .znpb-input-wrappers__wrapper {
		.znpb-input-wrapper {
			padding: 0;
		}
	}

	&__source iframe, &__source video {
		max-width: 100%;
		height: auto;
		border: none;
	}
}
</style>
