<template>
	<div class="znpb-input-media-wrapper">
		<BaseInput
			v-model="inputValue"
			@click="openMediaModal"
			class="znpb-form__input-text"
			placeholder="Type your text here"
		/>

		<BaseButton
			@click.native="openMediaModal"
			type="line"
		>{{selectButtonText}}
		</BaseButton>
	</div>
</template>
<script>
/**
* required properties received:
 *   model - string
 * other properties received:
 */
import BaseInput from '../input/BaseInput'
export default {
	name: 'InputMedia',
	props: {
		/**
		 * Value for input
		 */
		value: {
			type: String,
			required: false
		},
		/**
		 * Type of media
		 */
		media_type: {
			required: false,
			type: String,
			default: 'image'
		},
		/**
		 * Text on button
		 */
		selectButtonText: {
			required: false,
			type: String,
			default: 'Select'
		},
		/**
		 * Configuration
		 */
		mediaConfig: {
			type: Object,
			required: false,
			default () {
				return {
					inserTitle: 'Add File',
					multiple: false
				}
			}
		}
	},
	components: {
		BaseInput
	},
	data () {
		return {
			mediaModal: null
		}
	},
	computed: {
		inputValue: {
			get () {
				return this.value
			},
			set (newValue) {
				/**
				* Emits new string
				*/
				this.$emit('input', newValue)
			}
		}
	},
	methods: {
		openMediaModal () {
			if (this.mediaModal === null) {
				let selection = this.get_selection()

				const args = {
					frame: 'select',
					state: 'library',
					library: { type: this.media_type },
					button: { text: this.mediaConfig.inserTitle },
					selection
				}

				// Create the frame
				this.mediaModal = window.wp.media(args)

				this.mediaModal.on('select update insert', this.selectFont)
			}

			// Open the media modal
			this.mediaModal.open()
		},
		selectFont (e) {
			let selection = this.mediaModal.state().get('selection').toJSON()

			// In case we have multiple items
			if (typeof e !== 'undefined') { selection = e }

			if (this.mediaConfig.multiple) {
				this.inputValue = selection.map((selectedItem) => {
					return selectedItem.url
				}).join(',')
			} else {
				this.inputValue = selection[0].url
			}
		},
		get_selection () {
			if (typeof this.value === 'undefined') return

			let idArray = this.value.split(',')
			let args = { orderby: 'post__in', order: 'ASC', type: 'image', perPage: -1, post__in: idArray }
			let attachments = window.wp.media.query(args)
			let selection = new window.wp.media.model.Selection(attachments.models, {
				props: attachments.props.toJSON(),
				multiple: true
			})

			// Change the state to the edit gallery if we have images
			// if( idArray.length && !isNaN( parseInt( idArray[0],10 ) ) ){
			// 	this.media_data.state = 'gallery-edit';
			// }
			return selection
		}
	}
}

</script>
<style lang="scss">
.znpb-input-media-wrapper {
	display: flex;

	.zion-input {
		margin-right: 7px;
	}
}

</style>
