<template>
	<div>
		<div
			class="znpb-admin-single-template"
			:class="{'znpb-admin-single-template--active': isActive}"
		>

			<span class="znpb-admin-single-template__title">{{template.name}}</span>
			<span class="znpb-admin-single-template__author">{{template.author}}</span>
			<div class="znpb-admin-single-template__shortcode">

				<BaseInput
					v-znpb-tooltip="isCopied ? copiedText : copyText"
					ref="templateInput"
					:modelValue="template.shortcode"
					readonly
					spellcheck="false"
					autocomplete="false"
					class="znpb-admin-single-template__input"
					@click="copyTextInput(template.shortcode)"
				>
					<template v-slot:suffix>
						<Icon
							icon="copy"
							@click="copyTextInput(template.shortcode)"
						/>
					</template>
				</BaseInput>
			</div>
			<div class="znpb-admin-single-template__actions">
				<template v-if="!template.loading">
					<Icon
						icon="edit"
						@click="editUrl"
						class="znpb-admin-single-template__action znpb-delete-icon-pop"
						v-znpb-tooltip="$translate('edit_template')"
					/>

					<Icon
						icon="delete"
						@click="$emit('delete-template', template)"
						class="znpb-admin-single-template__action znpb-delete-icon-pop"
						v-znpb-tooltip="$translate('delete_template')"
					/>

					<Icon
						icon="export"
						@click="() => template.export()"
						v-znpb-tooltip="$translate('export_template')"
						class="znpb-admin-single-template__action znpb-export-icon-pop"
					/>

					<Icon
						icon="eye"
						@click="$emit('show-modal-preview', true)"
						v-znpb-tooltip="$translate('preview_template')"
						class="znpb-admin-single-template__action znpb-preview-icon-pop"
					/>

				</template>
				<Loader v-else />
			</div>

		</div>
		<p
			class="znpb-admin-single-template--error"
			v-if="errorMessage.length > 0"
		>{{errorMessage}}</p>
	</div>
</template>

<script>
import { ref } from 'vue'

export default {
	name: 'TemplateItem',
	props: {
		template: {
			type: Object,
			required: false
		},
		loading: {
			type: Boolean,
			required: false
		},
		error: {
			type: String,
			required: false
		},
		active: {
			type: Boolean,
			required: false
		}
	},

	setup (props) {
		const isActive = ref(props.active)

		if (isActive.value) {
			setTimeout(() => {
				isActive.value = false
			}, 1000);
		}

		return {
			isActive
		}
	},
	data () {
		return {
			isCopied: false,
			copiedText: 'Copied',
			copyText: 'Copy',
			localLoading: this.loading,
			errorMessage: '',
		}
	},

	watch: {
		loading (newVal) {
			this.localLoading = newVal
		},
		error (newVal) {
			this.errorMessage = newVal
		}
	},

	methods: {

		editUrl () {
			window.open(this.template.edit_url)
		},

		copyTextInput (text) {
			this.isCopied = true
			// Create new element
			var el = document.createElement('textarea')
			// Set value (string to be copied)
			el.value = text
			// Set non-editable to avoid focus and move outside of view
			el.setAttribute('readonly', '')
			el.style = {
				position: 'absolute',
				left: '-9999px'
			}
			document.body.appendChild(el)
			// Select text inside element
			el.select()
			// Copy text to clipboard
			document.execCommand('copy')
			// Remove temporary element
			document.body.removeChild(el)
		}
	}
}
</script>
<style lang="scss">
.znpb-admin-single-template--active {
	box-shadow: 0 0 4px #006dd2;
}
.znpb-admin-single-template {
	@extend %list-item-helper;
	padding: 9px 0;

	@media (max-width: 767px) {
		flex-direction: column;
		align-items: flex-start;
		padding: 18px 0;
	}

	.zion-input {
		background: var(--zb-input-bg-color);
	}

	&__actions, &__shortcode, &__author, &__title {
		flex: 1;
		padding: 0 14px;
	}

	&__title {
		flex-grow: 2;
		padding: 0 20px;
		color: var(--zb-surface-text-active-color);
		font-size: 13px;
		font-weight: 500;
	}
	&__author {
		padding: 0 14px;
		color: var(--zb-surface-text-color);
		font-size: 13px;
		font-weight: 500;
	}
	&__shortcode {
		position: relative;
		flex-basis: 160px;
		padding: 0 14px;

		@media (max-width: 767px) {
			flex-basis: auto;
			width: 100%;
		}

		.zion-input input {
			padding: 10.5px 12px;
		}

		.zion-input__suffix {
			padding-right: 14px;
			cursor: pointer;
			.znpb-editor-icon-wrapper {
				opacity: 0;
				visibility: hidden;
			}
		}
		&:hover {
			.znpb-editor-icon-wrapper {
				opacity: 1;
				visibility: visible;
			}
		}
	}

	&__actions {
		position: relative;
		display: flex;
		justify-content: flex-end;
		align-items: center;
		padding: 0 20px;
		font-size: 14px;
		text-align: right;

		.znpb-admin-small-loader {
			top: -12px;
		}
	}

	&__action {
		cursor: pointer;
		&.znpb-insert-icon-pop, &.znpb-edit-icon-pop, &.znpb-delete-icon-pop, &.znpb-export-icon-pop {
			margin-right: 10px;
		}

		&.znpb-delete-icon-pop {
			&:last-child {
				margin-right: 0;
			}
		}

		.znpb-editor-icon-wrapper {
			display: block;
		}

		.znpb-editor-icon-wrapper, a {
			color: var(--zb-surface-text-color);
			transition: color .15s;
			cursor: pointer;

			&:hover {
				color: var(--zb-surface-text-hover-color);
			}
		}
	}

	@media (max-width: 767px) {
		&__title, &__author, &__shortcode {
			margin-bottom: 10px;
		}

		&__title {
			padding: 0 14px;
		}
	}

	&--error {
		color: var(--zb-error-color);
	}
}
</style>
