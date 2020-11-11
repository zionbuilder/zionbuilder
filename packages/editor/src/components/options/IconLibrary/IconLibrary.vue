<template>
	<div class="znpb-icon-options">
		<div
			class="znpb-icon-trigger"
			@click.self="open"
			:class="{['znpb-icon-trigger--no-icon']: !value}"
		>
			<div
				v-if="value"
				class="znpb-icon-options__delete"
			>
				<span
					@click="showModal=true"
					class="znpb-icon-preview"
					:data-znpbiconfam="value.family"
					:data-znpbicon="unicode(value.unicode)"
				>
				</span>
				<Icon
					@click="$emit('update:modelValue',null)"
					icon="delete"
					:rounded="true"
					bg-color="#fff"
				/>
			</div>
			<span
				v-else
				@click="showModal=true"
			>
				{{$translate('select_icon')}}
			</span>
		</div>
		<Modal
			v-model:show="showModal"
			:width="590"
			:fullscreen="false"
			append-to=".znpb-center-area"
			:show-maximize="false"
			class="znpb-icon-library-modal"
		><template v-slot:header>
				<div class="znpb-icon-library-modal-header">
					<h2 class="znpb-icon-library-modal-header__title">Icon Library</h2>
					<div class="znpb-icon-library-modal-header__actions">
						<Button
							type="secondary"
							@click="$emit('update:modelValue',valueModel),showModal=false"
						>
							Insert
						</Button>
						<Icon
							icon="close"
							:size="16"
							@click="onClose"
							class="znpb-modal__header-button"
						/>
					</div>
				</div>
			</template>
			<IconsLibraryModalContent
				v-model="valueModel"
				@selected="$emit('update:modelValue',$event),showModal=false"
				:special-filter-pack="specialFilterPack"
			/>
		</Modal>
	</div>

</template>

<script>
import IconsLibraryModalContent from './IconsLibraryModalContent.vue'

export default {
	name: 'IconLibrary',
	components: {
		IconsLibraryModalContent
	},
	props: {
		specialFilterPack: {
			type: Array,
			required: false
		},
		title: {
			type: String,
			required: true
		},
		modelValue: {
			required: false
		}
	},
	data () {
		return {
			showModal: false,
			cachedValue: null
		}
	},
	computed: {
		valueModel: {
			get () {
				return this.modelValue || {}
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		}
	},
	watch: {
		showModal (newValue) {
			if (newValue) {
				this.cachedValue = this.modelValue
			}
		}
	},
	methods: {
		unicode (unicode) {
			return JSON.parse(('"\\' + unicode + '"'))
		},
		onClose () {
			this.valueModel = this.cachedValue
			this.showModal = false
		},
		open () {
			this.showModal = true
		}
	}
}
</script>
<style lang="scss">
.znpb-icon-options {
	width: 100%;
	padding: 0 5px 20px;
}
.znpb-icon-trigger {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	height: 100px;
	font-weight: 500;
	background-color: $surface-variant;
	border-radius: 3px;
	cursor: pointer;

	&--no-icon {
		background: transparent;
		border: 2px dashed $border-color;
	}

	.znpb-editor-icon-wrapper {
		position: absolute;
		right: 15px;
		bottom: 15px;
		padding: 7px;
		transition: all .2s;
		&:hover {
			opacity: .7;
		}
	}
}
.znpb-icon-preview {
	color: $surface-active-color;
	font-size: 28px;
}
.znpb-icon-library-modal > .znpb-modal__wrapper {
	width: 100%;
}

.znpb-icon-library-modal-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 8.5px 0;
	border-bottom: 1px solid $surface-variant;

	&__title {
		padding-left: 20px;
		color: $surface-active-color;
		font-size: 16px;
		font-weight: bold;
	}

	&__actions {
		display: flex;
		align-items: center;
	}

	& > &__actions {
		& > .znpb-button {
			margin-right: 15px;
		}
	}
}
</style>
