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
import { computed, ref, Ref } from 'vue';
import BaseInput from '../BaseInput/BaseInput.vue';

const props = withDefaults(
	defineProps<{
		modelValue?: string;
		media_type?: string;
		selectButtonText?: string;
		mediaConfig?: { inserTitle: string; multiple: boolean };
	}>(),
	{
		media_type: 'image',
		selectButtonText: 'select',
		mediaConfig: () => {
			return {
				inserTitle: 'Add File',
				multiple: false,
			};
		},
	},
);

const emit = defineEmits(['update:modelValue']);

const mediaModal: Ref<any> = ref(null);

const inputValue = computed({
	get() {
		return props.modelValue || '';
	},
	set(newValue: string) {
		emit('update:modelValue', newValue);
	},
});

function openMediaModal() {
	if (mediaModal.value === null) {
		let selection = getSelection();

		const args = {
			frame: 'select',
			state: 'library',
			library: { type: props.media_type },
			button: { text: props.mediaConfig.inserTitle },
			selection,
		};

		// Create the frame
		mediaModal.value = window.wp.media(args);

		mediaModal.value.on('select update insert', selectFont);
	}

	console.log(mediaModal.value);

	// Open the media modal
	mediaModal.value.open();
}

function selectFont(e) {
	console.log("e", e);

	let selection = mediaModal.value.state().get('selection').toJSON();

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

	let idArray = props.modelValue.split(',');
	let args = { orderby: 'post__in', order: 'ASC', type: 'image', perPage: -1, post__in: idArray };
	let attachments = window.wp.media.query(args);
	let selection = new window.wp.media.model.Selection(attachments.models, {
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
