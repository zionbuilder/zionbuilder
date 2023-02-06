<template>
	<div class="znpb-input-image__wrapper">
		<component :is="customComponent" v-if="customComponent" />
		<div v-else ref="imageHolder" class="znpb-input-image-holder" :style="wrapperStyles" @click="openMediaModal">
			<ActionsOverlay :show-overlay="!isDragging">
				<component :is="imageComponent" :src="imageSrc" class="znpb-input-image-holder__image" />
				<!-- <img ref="image" :src="imageSrc" class="znpb-input-image-holder__image" /> -->
				<template #actions>
					<div class="znpb-input-image-holder__image-actions">
						<Icon :rounded="true" icon="delete" :bg-size="30" @click.stop="deleteImage" />

						<!-- Injection point -->
						<Injection location="options/image/actions" />
					</div>
				</template>
			</ActionsOverlay>
			<div
				v-if="imageSrc && shouldDragImage && (previewExpanded || !shouldDisplayExpander)"
				ref="dragButton"
				class="znpb-drag-icon-wrapper"
				:style="positionCircleStyle"
				@mousedown.stop="startDrag"
			>
				<span class="znpb-input-image-holder__drag-button"></span>
			</div>
			<div
				v-if="!isDragging && shouldDragImage && shouldDisplayExpander && imageSrc"
				class="znpb-actions-overlay__expander"
				:class="{ 'znpb-actions-overlay__expander--icon-rotated': previewExpanded }"
				@click.stop="toggleExpand"
			>
				<strong class="znpb-actions-overlay__expander-text">
					{{ previewExpanded ? 'CONTRACT' : 'EXPAND' }}
				</strong>
				<Icon icon="select" :bg-size="12" />
			</div>
			<EmptyList v-if="!imageSrc && !customComponent" class="znpb-input-image-holder__empty" :no-margin="true">
				{{ emptyText }}
				<Injection location="options/image/actions" />
			</EmptyList>
		</div>

		<!-- Image size -->
		<div v-if="show_size && imageSrc && !isSVG && !loading" class="znpb-input-image__custom-size-wrapper">
			<InputWrapper title="Image size">
				<InputSelect v-model="sizeValue" :options="imageSizes" />
			</InputWrapper>

			<InputWrapper v-if="sizeValue === 'custom'">
				<CustomSize v-model="customSizeValue" />
			</InputWrapper>
		</div>
	</div>
</template>

<script lang="ts">
const wp = window.wp;

export default {
	name: 'InputImage',
};
</script>

<script lang="ts" setup>
import { applyFilters, doAction } from '../../modules/hooks';
import {
	ref,
	Ref,
	computed,
	watch,
	onMounted,
	onBeforeUnmount,
	nextTick,
	inject,
	CSSProperties,
	watchEffect,
} from 'vue';
import Icon from '../Icon/Icon.vue';
import { ActionsOverlay } from '../ActionsOverlay';
import { EmptyList } from '../EmptyList';
import { InputWrapper } from '../InputWrapper';
import { InputSelect } from '../InputSelect';
import CustomSize from './CustomSize.vue';
import { Injection } from '../Injection';
import { clamp, startCase } from 'lodash-es';

type ImageSize = { width?: string; height?: string };
interface ImageValue {
	image?: string;
	image_size?: string;
	custom_size?: ImageSize;
}

const props = withDefaults(
	defineProps<{
		// @Todo Refactor modelValue to only accept object as prop
		modelValue?: ImageValue | string;
		emptyText?: string;
		shouldDragImage?: boolean;
		positionLeft?: string;
		positionTop?: string;
		show_size?: boolean;
	}>(),
	{
		emptyText: 'No Image Selected',
		positionLeft: '50%',
		positionTop: '50%',
	},
);

const inputWrapper = inject('inputWrapper');
const optionsForm = inject('OptionsForm');
const emit = defineEmits<{
	(e: 'background-position-change', value: { x: number; y: number }): void;
	(e: 'update:modelValue', value: ImageValue | string): void;
}>();

const imageComponent = computed(() => {
	return applyFilters('zionbuilder/options/image/image_component', 'img', props.modelValue);
});

const imageHolder: Ref<HTMLDivElement | null> = ref(null);
const image: Ref<HTMLImageElement | null> = ref(null);
const dragButton: Ref<HTMLDivElement | null> = ref(null);

const attachmentId = ref<number | null>(null);
const isDragging = ref(false);
const imageContainerPosition = ref<{ left: number | null; top: number | null }>({
	left: null,
	top: null,
});
const imageHolderWidth = ref<number | null>(null);
const imageHolderHeight = ref<number | null>(null);
const previewExpanded = ref(false);
const minHeight = ref(200);
const imageHeight = ref<number | null>(null);
const initialX = ref<number | null>(null);
const initialY = ref<number | null>(null);
const attachmentModel = ref<Record<string, any> | null>(null);
const loading = ref(true);
const dynamicImageSrc = ref(null);

let mediaModal: Record<string, any>;

watch(
	() => props.modelValue,
	(newValue, oldValue) => {
		if (newValue !== oldValue) {
			nextTick(() => {
				getImageHeight();
				if (previewExpanded.value) {
					toggleExpand();
				}
			});
		}
	},
);

const customComponent = computed(() => {
	const { applyFilters } = window.zb.hooks;
	return applyFilters('zionbuilder/options/image/display_component', null, props.modelValue, inputWrapper, optionsForm);
});

const isSVG = computed(() => {
	if (imageSrc.value) {
		return imageSrc.value.endsWith('.svg');
	}

	return imageSrc.value;
});

const imageSizes = computed(() => {
	const options = [];

	const imageSizes = attachmentModel.value?.attributes?.sizes;
	const customSizes = attachmentModel.value?.attributes?.zion_custom_sizes;
	const allSizes = {
		...imageSizes,
		...customSizes,
	};

	// Loop through all sizes and create the option
	Object.keys(allSizes).forEach(sizeKey => {
		const name = startCase(sizeKey);
		const width = allSizes[sizeKey].width;
		const height = allSizes[sizeKey].height;
		const optionName = `${name} ( ${width} x ${height} )`;
		options.push({
			name: optionName,
			id: sizeKey,
		});
	});

	// Add default values
	options.push({
		name: 'Custom',
		id: 'custom',
	});

	return options;
});

const sizeValue = computed({
	get() {
		return (typeof props.modelValue === 'object' && props.modelValue?.image_size) || 'full';
	},
	set(newValue: string) {
		const value = typeof props.modelValue === 'object' ? props.modelValue : {};
		emit('update:modelValue', {
			...value,
			image_size: newValue,
		});
	},
});

const customSizeValue = computed({
	get() {
		return (typeof props.modelValue === 'object' && props.modelValue?.custom_size) || {};
	},
	set(newValue: ImageSize) {
		emit('update:modelValue', {
			...((typeof props.modelValue === 'object' && props.modelValue) || {}),
			custom_size: newValue,
		});
	},
});

const positionCircleStyle = computed<CSSProperties>(() => {
	return {
		left: props.positionLeft.includes('%') ? props.positionLeft : '',
		top: props.positionTop.includes('%') ? props.positionTop : '',
	};
});

const wrapperStyles = computed<CSSProperties>(() => {
	if (imageSrc.value && imageHolderHeight.value) {
		return {
			height: imageHolderHeight.value + 'px',
		};
	}

	return {};
});

const imageValue = computed({
	get() {
		if (props.show_size) {
			return props.modelValue || {};
		} else {
			return props.modelValue || null;
		}
	},

	set(newValue) {
		if (props.show_size) {
			emit('update:modelValue', {
				...((typeof props.modelValue === 'object' && props.modelValue) || {}),
				...newValue,
			});
		} else {
			emit('update:modelValue', newValue);
		}
	},
});

const imageSrc = computed(() => {
	return dynamicImageSrc.value
		? dynamicImageSrc.value
		: typeof props.modelValue === 'object'
		? props.modelValue?.image || null
		: props.modelValue || null;
});

const element = inject('ZionElement', null);

watchEffect(() => {
	doAction('zionbuilder/input/image/src_url', dynamicImageSrc, props.modelValue, element);
});

const shouldDisplayExpander = computed(() => {
	return (imageHolderHeight.value as number) >= minHeight.value;
});

function getImageHeight() {
	if (!image.value) {
		return;
	}
	// Wait for the image to load before getting it's dimensions
	image.value.addEventListener('load', () => {
		const localImageHeight = (image.value as HTMLImageElement).getBoundingClientRect().height;
		imageHeight.value = localImageHeight;
		imageHolderHeight.value = localImageHeight < minHeight.value ? localImageHeight : minHeight.value;
	});
}

function toggleExpand() {
	previewExpanded.value = !previewExpanded.value;
	if (previewExpanded.value) {
		imageHolderHeight.value = imageHeight.value;
	} else {
		imageHolderHeight.value = minHeight.value;
	}
}

function startDrag(event: MouseEvent) {
	if (props.shouldDragImage) {
		window.addEventListener('mousemove', doDrag);
		window.addEventListener('mouseup', stopDrag);
		isDragging.value = true;
		const { height, width, left, top } = (imageHolder.value as HTMLDivElement).getBoundingClientRect();
		imageHolderWidth.value = width;
		imageHolderHeight.value = height;
		imageContainerPosition.value.left = left;
		imageContainerPosition.value.top = top;
		initialX.value = event.pageX;
		initialY.value = event.pageY;
	}
}

function doDrag(event: MouseEvent) {
	window.document.body.style.userSelect = 'none';

	const movedX = event.clientX - (imageContainerPosition.value.left as number);
	const movedY = event.clientY - (imageContainerPosition.value.top as number);

	let xToSend = clamp(Math.round((movedX / (imageHolderWidth.value as number)) * 100), 0, 100);
	let yToSend = clamp(Math.round((movedY / (imageHolderHeight.value as number)) * 100), 0, 100);

	if (event.shiftKey) {
		xToSend = Math.round(xToSend / 5) * 5;
		yToSend = Math.round(yToSend / 5) * 5;
	}
	emit('background-position-change', { x: xToSend, y: yToSend });
}

function stopDrag(event: MouseEvent) {
	initialX.value = event.pageX;
	initialY.value = event.pageY;
	window.removeEventListener('mousemove', doDrag);
	window.removeEventListener('mouseup', stopDrag);
	window.document.body.style.userSelect = '';
	setTimeout(() => {
		isDragging.value = false;
	}, 100);
}

function openMediaModal() {
	if (isDragging.value) {
		return;
	}
	if (!mediaModal) {
		const args = {
			frame: 'select',
			state: 'zion-media',
			library: {
				type: 'image',
			},
			button: {
				text: 'Add Image',
			},
		};

		// Create the frame
		mediaModal = new window.wp.media.view.MediaFrame.ZionBuilderFrame(args);
		mediaModal.on('select update insert', selectMedia);
		mediaModal.on('open', setMediaModalSelection);
	}

	// Open the media modal
	mediaModal.open();
}
function selectMedia() {
	const selection = mediaModal.state().get('selection').first();
	if (props.show_size) {
		// Reset all other values when selecting a new image
		emit('update:modelValue', {
			image: selection.get('url'),
		});
	} else {
		imageValue.value = selection.get('url');
	}

	attachmentId.value = selection.get('id');
	// Save the selection so we can use the sizes
	attachmentModel.value = selection;

	// Show the custom size selector again if needed
	loading.value = false;
}

function setMediaModalSelection() {
	const selection = mediaModal.state().get('selection');
	if (imageSrc.value && !attachmentId.value) {
		const attachment = wp.media.model.Attachment.get(imageSrc.value);
		attachment
			.fetch({
				data: {
					is_media_image: true,
					image_url: imageSrc.value,
				},
			})
			.done(event => {
				if (event && event.id) {
					attachmentId.value = event.id;
					const attachment = wp.media.model.Attachment.get(attachmentId.value);
					selection.reset(attachment ? [attachment] : []);
				}
			});
	} else if (imageSrc.value && attachmentId.value) {
		const attachment = wp.media.model.Attachment.get(attachmentId.value);
		selection.reset(attachment ? [attachment] : []);
	}
}

function deleteImage() {
	emit('update:modelValue', null);
	attachmentId.value = null;

	// Reset the selection
	if (mediaModal) {
		const selection = mediaModal.state().get('selection');
		selection.reset([]);
	}
}

function getAttachmentModel() {
	if (imageSrc.value && !attachmentModel.value) {
		const attachment = wp.media.model.Attachment.get(imageSrc.value);

		attachment
			.fetch({
				data: {
					is_media_image: true,
					image_url: imageSrc.value,
				},
			})
			.done((event: Record<string, any>) => {
				if (event?.id) {
					attachmentId.value = event.id;
					attachmentModel.value = wp.media.model.Attachment.get(attachmentId.value);
				}
				loading.value = false;
			});
	}
}

onMounted(() => {
	if (props.show_size) {
		getAttachmentModel();
	} else {
		loading.value = false;
	}
	getImageHeight();
});

watch(dynamicImageSrc, () => {
	getAttachmentModel();
});

onBeforeUnmount(() => {
	window.removeEventListener('mousemove', doDrag);
	window.removeEventListener('mouseup', stopDrag);
	window.document.body.style.userSelect = '';

	if (mediaModal) {
		mediaModal.detach();
	}
});
</script>

<style lang="scss">
.znpb-input-background-image .znpb-options-form-wrapper {
	padding: 0;
	margin-right: -5px;
	margin-left: -5px;
}

.znpb-input-image-holder__drag-button {
	display: block;
	width: 12px;
	height: 12px;
	background-color: #fff;
	border-radius: 50%;
}
.znpb-actions-overlay {
	&__expander {
		position: absolute;
		bottom: 10px;
		left: 50%;
		z-index: 1;
		display: flex;
		flex-direction: column;
		align-items: center;
		color: var(--zb-surface-color);
		transform: translateX(-50%);
		&-text {
			font-size: 10px;
		}
		&--icon-rotated {
			display: flex;
			flex-direction: column-reverse;
			.znpb-editor-icon-wrapper {
				transform: rotate(180deg);
			}
		}
	}
}
.znpb-input-image {
	&__wrapper {
		height: 100%;
	}

	&__custom-size-wrapper {
		.znpb-input-wrapper {
			padding: 0;
		}

		& > .znpb-input-wrapper {
			padding-bottom: 20px;

			&:last-child {
				padding-bottom: 0;
			}
		}
	}
}

.znpb-input-image-holder {
	position: relative;
	overflow: hidden;
	margin-bottom: 20px;
	border-radius: 3px;
	transition: all 0.5s ease;
	cursor: pointer;
	&__image {
		display: block;
		margin: 0 auto;
	}
	&__empty {
		.znpb-empty-list__content {
			padding: 50px 30px;
		}
	}
}
.znpb-drag-icon-wrapper {
	position: absolute;
	top: 50%;
	left: 50%;
	padding: 10px;
	font-size: 10px;
	border-radius: 50%;
	transform: translateX(-50%) translateY(-50%);
	cursor: move;
}

.znpb-input-image-holder__image-actions {
	display: flex;
}
</style>
