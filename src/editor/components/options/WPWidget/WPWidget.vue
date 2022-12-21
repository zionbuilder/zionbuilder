<template>
	<div class="znpb-element-form__wp_widget">
		<div v-if="loading" class="znpb-element-form__wp_widget-loading">
			{{ __('Loading', 'zionbuilder') }}
		</div>

		<form v-else ref="form" v-html="optionsFormContent"></form>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { ref, nextTick } from 'vue';
import { serialize } from 'dom-form-serializer';
import { useUIStore } from '/@/editor/store';

// Common API
const { getOptionsForm } = window.zb.api;

const props = withDefaults(
	defineProps<{
		value?: Record<string, unknown>;
		// eslint-disable-next-line vue/prop-name-casing
		element_type: string;
	}>(),
	{
		value: () => {
			return {};
		},
	},
);

const UIStore = useUIStore();
const form = ref(null);
const loading = ref(true);
const optionsFormContent = ref('');
const emit = defineEmits(['update:modelValue']);

// Get the options form from server
getOptionsForm(UIStore.editedElement).then(response => {
	optionsFormContent.value = response.data.form;
	loading.value = false;

	const wp = window.wp;
	const jQuery = window.jQuery;

	nextTick(() => {
		if (wp.textWidgets) {
			let widgetContainer = jQuery(form.value);
			const event = new jQuery.Event('widget-added');

			widgetContainer.addClass('open');
			wp.textWidgets.handleWidgetAdded(event, widgetContainer);
			wp.mediaWidgets.handleWidgetAdded(event, widgetContainer);

			// // WP >= 4.9
			if (wp.customHtmlWidgets) {
				wp.customHtmlWidgets.handleWidgetAdded(event, widgetContainer);
			}

			// Setup event listeners
			jQuery(':input', jQuery(form.value)).on('input', onInputChange);
			jQuery(':input', jQuery(form.value)).on('change', onInputChange);
		}
	});
});

function onInputChange() {
	const widgetId = `widget-${props.element_type}`;
	const formData = serialize(form.value);

	emit('update:modelValue', formData[widgetId]['ZION_BUILDER_PLACEHOLDER_ID']);
}
</script>

<style lang="scss">
.znpb-element-form__wp_widget-loading {
	position: relative;
	width: 24px;
	height: 24px;
	margin: 0 auto;
	text-align: center;
	transition: none;
	&:before,
	&:after {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		border: 2px solid var(--zb-surface-lighter-color);
		border-radius: 50%;
	}
	&:after {
		border-right-color: var(--zb-surface-border-color);
		animation: Rotate 0.6s linear infinite;
	}
}
.znpb-element-form__wp_widget {
	.widget-content {
		p {
			margin-bottom: 15px;
		}
	}

	.widget-inside {
		display: block;
	}
}
.widget-content {
	input[type='text'],
	input[type='number'],
	select {
		width: 100%;
		height: auto;
		height: 42px;
		padding: 13px;
		margin: 0;
		color: var(--zb-surface-text-color);
		font-family: var(--zb-font-stack);
		font-size: 13px;
		line-height: 1;
		background-color: #f1f1f1;
		background-image: none;
		box-shadow: none;
		border: 0;
		border-radius: 3px;

		-webkit-appearance: none;
	}
	label {
		padding: 15px 15px 15px 0;
		color: var(--zb-surface-icon-color);
		font-family: var(--zb-font-stack);
		font-size: 11px;
		font-weight: 700;
		line-height: 1;
	}
}
.text-widget-fields {
	input[type='text'],
	input[type='number'],
	select {
		width: 100%;
		height: auto;
		height: 42px;
		padding: 13px;
		margin: 0;
		color: var(--zb-surface-text-color);
		font-family: var(--zb-font-stack);
		font-size: 13px;
		line-height: 1;
		background-color: var(--zb-surface-lighter-color);
		background-image: none;
		box-shadow: none;
		border: 0;
		border-radius: 3px;

		-webkit-appearance: none;
	}
	label {
		padding: 15px 15px 15px 0;
		color: var(--zb-surface-icon-color);
		font-family: var(--zb-font-stack);
		font-size: 11px;
		font-weight: 700;
		line-height: 1;
	}
	.button {
		margin: 0;
		margin-bottom: 15px;
		font-family: var(--zb-font-stack);
		font-size: 13px;
		text-decoration: none;
		text-transform: uppercase;
		border: none;

		-webkit-appearance: none;
		-moz-appearance: none;
	}
}
.media-widget-control {
	font-family: var(--zb-font-stack);

	.button-add-media {
		font-family: var(--zb-font-stack);
	}

	input[type='text'],
	input[type='number'],
	select {
		width: 100%;
		height: auto;
		height: 42px;
		padding: 13px;
		margin: 0;
		margin-bottom: 15px;
		color: var(--zb-surface-text-color);
		font-family: var(--zb-font-stack);
		font-size: 13px;
		line-height: 1;
		background-color: #f1f1f1;
		background-image: none;
		box-shadow: none;
		border: 0;
		border-radius: 3px;

		-webkit-appearance: none;
	}
}
</style>
