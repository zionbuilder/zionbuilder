<template>
	<div class="znpb-input-media-wrapper">
		<BaseInput
			v-model="inputValue"
			class="znpb-form__input-text"
			placeholder="Type your text here"
			@click="openMediaModal"
		/>

		<Button type="line" @click="openMediaModal">{{ selectButtonText }} </Button>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputMedia',
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';
import BaseInput from '../BaseInput/BaseInput.vue';

const props = withDefaults(
	defineProps<{
		// eslint-disable-next-line vue/require-default-prop
		modelValue?: string;
		// eslint-disable-next-line vue/prop-name-casing
		media_type?: string;
		selectButtonText?: string;
		mediaConfig?: { insertTitle: string; multiple: boolean };
	}>(),
	{
		media_type: 'image',
		selectButtonText: 'select',
		mediaConfig: () => {
			return {
				insertTitle: 'Add File',
				multiple: false,
			};
		},
	},
);

const emit = defineEmits(['update:modelValue']);

let mediaModal = null;

const inputValue = computed({
	get() {
		return props.modelValue || '';
	},
	set(newValue: string) {
		emit('update:modelValue', newValue);
	},
});

function openMediaModal() {
	if (mediaModal === null) {
		const selection = getSelection();

		const args = {
			frame: 'select',
			state: 'library',
			library: { type: props.media_type },
			button: { text: props.mediaConfig.insertTitle },
			selection,
		};

		// Create the frame
		mediaModal = window.wp.media(args);

		mediaModal.on('select update insert', selectMedia);
	}

	// Open the media modal
	mediaModal.open();
}

function selectMedia(e) {
	let selection = mediaModal.state().get('selection').toJSON();

	// In case we have multiple items
	if (e !== undefined) {
		selection = e;
	}

	if (props.mediaConfig.multiple) {
		inputValue.value = selection.map((selectedItem: { url: string }) => selectedItem.url).join(',');
	} else {
		inputValue.value = selection[0].url;
	}
}

function getSelection() {
	if (typeof props.modelValue === 'undefined') return;

	const idArray = props.modelValue.split(',');
	const args = { orderby: 'post__in', order: 'ASC', type: 'image', perPage: -1, post__in: idArray };
	const attachments = window.wp.media.query(args);
	const selection = new window.wp.media.model.Selection(attachments.models, {
		props: attachments.props.toJSON(),
		multiple: true,
	});

	// Change the state to the edit gallery if we have images
	// if( idArray.length && !isNaN( parseInt( idArray[0],10 ) ) ){
	// 	this.media_data.state = 'gallery-edit';
	// }
	return selection;
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
