<template>
	<div>
		<div class="znpb-admin-single-template" :class="{ 'znpb-admin-single-template--active': isActive }">
			<span class="znpb-admin-single-template__title">{{ template.name }}</span>
			<span class="znpb-admin-single-template__author">{{ template.author }}</span>
			<div class="znpb-admin-single-template__shortcode">
				<BaseInput
					ref="templateInputRef"
					v-znpb-tooltip="isCopied ? __('Copied', 'zionbuilder') : __('Copy', 'zionbuilder')"
					:modelValue="template.shortcode"
					readonly
					spellcheck="false"
					autocomplete="false"
					class="znpb-admin-single-template__input"
					@click="copyTextInput()"
				>
					<template #suffix>
						<Icon icon="copy" @click="copyTextInput()" />
					</template>
				</BaseInput>
			</div>
			<div class="znpb-admin-single-template__actions">
				<template v-if="!template.loading">
					<Icon
						v-znpb-tooltip="__('Edit template', 'zionbuilder')"
						icon="edit"
						class="znpb-admin-single-template__action znpb-delete-icon-pop"
						@click="editUrl"
					/>

					<Icon
						v-znpb-tooltip="__('Delete template', 'zionbuilder')"
						icon="delete"
						class="znpb-admin-single-template__action znpb-delete-icon-pop"
						@click="emit('delete-template', template)"
					/>

					<Icon
						v-znpb-tooltip="__('Export template', 'zionbuilder')"
						icon="export"
						class="znpb-admin-single-template__action znpb-export-icon-pop"
						@click="() => template.export()"
					/>

					<Icon
						v-znpb-tooltip="__('Preview template', 'zionbuilder')"
						icon="eye"
						class="znpb-admin-single-template__action znpb-preview-icon-pop"
						@click="emit('show-modal-preview', true)"
					/>
				</template>
				<Loader v-else />
			</div>
		</div>
		<p v-if="errorMessage.length > 0" class="znpb-admin-single-template--error">{{ errorMessage }}</p>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { ref, watch, Ref } from 'vue';
import { BaseInput } from '@zb/components';

const props = withDefaults(
	defineProps<{
		template: {
			name: string;
			author: string;
			shortcode: string;
			loading: boolean;
			urls: { edit_url: string };
			export: () => void;
		};
		loading?: boolean;
		error?: string;
		active?: boolean;
	}>(),
	{
		loading: false,
		error: '',
		active: false,
	},
);

const emit = defineEmits(['delete-template', 'show-modal-preview']);

const templateInputRef: Ref<typeof BaseInput | null> = ref(null);
const isCopied = ref(false);
const localLoading = ref(props.loading);
const errorMessage = ref('');

const isActive = ref(props.active);

if (isActive.value) {
	setTimeout(() => {
		isActive.value = false;
	}, 1000);
}

watch(
	() => props.loading,
	newVal => {
		localLoading.value = newVal;
	},
);

watch(
	() => props.error,
	newVal => {
		errorMessage.value = newVal;
	},
);

function editUrl() {
	window.open(props.template.urls.edit_url);
}

function copyTextInput() {
	isCopied.value = true;
	const copyText = templateInputRef.value?.input;

	if (!copyText) {
		return;
	}

	// Select the text field
	copyText.select();
	copyText.setSelectionRange(0, 99999); // For mobile devices

	// navigator clipboard api needs a secure context (https)
	if (navigator.clipboard && window.isSecureContext) {
		// navigator clipboard api method'
		return navigator.clipboard.writeText(copyText.value);
	} else {
		// text area method
		const textArea = document.createElement('textarea');
		textArea.value = copyText.value;
		// make the textarea out of viewport
		textArea.style.position = 'fixed';
		textArea.style.left = '-999999px';
		textArea.style.top = '-999999px';
		document.body.appendChild(textArea);
		textArea.focus();
		textArea.select();

		return new Promise((res, rej) => {
			// here the magic happens
			document.execCommand('copy') ? res(true) : rej();
			textArea.remove();
		});
	}
}
</script>
<style lang="scss">
@import '/@/common/scss/_mixins.scss';
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

	&__actions,
	&__shortcode,
	&__author,
	&__title {
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
		&.znpb-insert-icon-pop,
		&.znpb-edit-icon-pop,
		&.znpb-delete-icon-pop,
		&.znpb-export-icon-pop {
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

		.znpb-editor-icon-wrapper,
		a {
			color: var(--zb-surface-text-color);
			transition: color 0.15s;
			cursor: pointer;

			&:hover {
				color: var(--zb-surface-text-hover-color);
			}
		}
	}

	@media (max-width: 767px) {
		&__title,
		&__author,
		&__shortcode {
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
