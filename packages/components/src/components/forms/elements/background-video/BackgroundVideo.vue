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
				@click="openMediaModal"
			>
				{{$translate('no_video_selected')}}
			</EmptyList>

			<BaseIcon
				v-if="this.videoSourceModel == 'local' && hasVideo"
				class="znpb-input-background-video__delete"
				icon="delete"
				:bg-size="30"
				bg-color="#fff"
				@click.stop="deleteVideo"
			/>

		</div>
		<OptionsForm
			:schema="schema"
			v-model="computedValue"
			class="znpb-input-background-video__holder"
		/>
	</div>
</template>
<script>
import BaseIcon from '../../../BaseIcon.vue'
import EmptyList from '../empty-list/EmptyList.vue'
import Video from '@zionbuilder/video'
import { getSchema } from '@zb/schemas'

export default {
	name: 'InputBackgroundVideo',
	components: {
		EmptyList,
		BaseIcon
	},
	props: ['modelValue', 'options', 'exclude_options'],
	data () {
		return {
			videoInstance: null,
			mediaModal: null
		}
	},
	watch: {
		computedValue (newValue, oldValue) {
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
		computedValue: {
			get () {
				return this.modelValue || {}
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		},
		hasVideo () {
			if (this.videoSourceModel === 'local' && this.computedValue.mp4) {
				return true
			}

			if (this.videoSourceModel === 'youtube' && this.computedValue.youtubeURL) {
				return true
			}

			if (this.videoSourceModel === 'vimeo' && this.computedValue.vimeoURL) {
				return true
			}

			return false
		},
		controlsModel: {
			get () {
				return !!this.computedValue.controls
			},
			set (newValue) {
				this.$emit('update:modelValue', {
					...this.computedValue,
					'controls': newValue
				})
			}
		},
		autoplayModel: {
			get () {
				return typeof this.computedValue['autoplay'] === 'undefined' ? true : this.computedValue['autoplay']
			},
			set (newValue) {
				this.$emit('update:modelValue', {
					...this.computedValue,
					'autoplay': newValue
				})
			}
		},
		mutedModel: {
			get () {
				return typeof this.computedValue['muted'] === 'undefined' ? true : this.computedValue['muted']
			},
			set (newValue) {
				this.$emit('update:modelValue', {
					...this.computedValue,
					'muted': newValue
				})
			}
		},
		videoSourceModel: {
			get () {
				return this.computedValue['videoSource'] || 'local'
			},
			set (newValue) {
				this.$emit('update:modelValue', {
					...this.modelValue,
					'videoSource': newValue
				})
			}
		}
	},
	methods: {
		initVideo () {
			this.$nextTick(() => {
				this.videoInstance = new Video(this.$refs.videoPreview, this.computedValue)
			})
		},
		openMediaModal () {
			if (this.mediaModal === null) {
				const args = {
					frame: 'select',
					state: 'library',
					library: { type: 'video' },
					button: { text: 'Add video' },
					selection: this.computedValue
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
			this.$emit('update:modelValue', {
				...this.computedValue,
				mp4: selection[0].url
			})
		},
		deleteVideo () {
			const { mp4, ...rest } = this.computedValue
			this.$emit('update:modelValue', {
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
