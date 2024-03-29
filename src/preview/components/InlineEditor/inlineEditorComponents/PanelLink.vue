<template>
	<PopOver icon="ite-link" :full-size="true">
		<div class="zion-inline-editor-link-wrapper">
			<InputWrapper :title="i18n.__('Add a link', 'zionbuilder')">
				<BaseInput v-model="linkUrl" :clearable="true" placeholder="www.address.com" @keyup.enter="addLink">
					<template #prepend>
						<Icon icon="link" />
					</template>
				</BaseInput>
			</InputWrapper>
			<div class="zion-inline-editor-popover__link-title">
				<InputWrapper :title="i18n.__('Target', 'zionbuilder')">
					<InputSelect
						v-model="linkTarget"
						:options="selectOptions"
						:placeholder="i18n.__('Select target', 'zionbuilder')"
					/>
				</InputWrapper>
				<InputWrapper :title="i18n.__('Title', 'zionbuilder')">
					<BaseInput v-model="linkTitle" placeholder="link_title" :clearable="true" @keyup.enter="addLink" />
				</InputWrapper>
			</div>
		</div>
	</PopOver>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { inject, computed, ref, onMounted, onBeforeUnmount } from 'vue';

// Components
import PopOver from './PopOver.vue';

const props = withDefaults(
	defineProps<{
		fullWidth?: boolean;
		direction?: string;
		visible?: boolean;
	}>(),
	{
		fullWidth: false,
		direction: 'bottom',
		visible: false,
	},
);

const editor = inject('ZionInlineEditor');
const isPopOverVisible = ref(false);
const justChangedNode = ref(false);
const linkTarget = ref('_self');
const linkUrl = ref('');
const linkTitle = ref('');
const selectOptions = [
	{
		id: '_self',
		name: 'Self',
	},
	{
		id: '_blank',
		name: 'New Window',
	},
];

const hasLink = ref(false);

const buttonClasses = computed(() => {
	const classes = [];

	// Check if the button is active
	if (hasLink.value) {
		classes.push('zion-inline-editor-button--active');
	}

	return classes.join(' ');
});

function onNodeChange(node) {
	if (node.selectionChange) {
		getLink();
	}
}

function getLink() {
	const link = editor.editor.dom.getParent(editor.editor.selection.getStart(), 'a[href]');

	if (link) {
		linkTarget.value = link.target || '_self';
		linkUrl.value = link.getAttribute('href');
		linkTitle.value = link.getAttribute('title');
		hasLink.value = true;
	} else {
		linkUrl.value = null;
		linkTitle.value = '';
		hasLink.value = false;
	}
}

function addLink(closePopper = true) {
	if (linkUrl.value) {
		// Make the selection a link
		editor.editor.formatter.apply('link', {
			href: linkUrl.value,
			target: linkTarget.value,
			title: linkTitle.value,
		});
	} else {
		editor.editor.formatter.remove('link');
	}

	if (closePopper) {
		isPopOverVisible.value = false;
	}
}

onMounted(() => {
	getLink();
	editor.editor.on('NodeChange', onNodeChange);
});

onBeforeUnmount(() => {
	editor.editor.off('NodeChange', onNodeChange);
});
</script>

<style lang="scss">
.zion-inline-editor-link-wrapper {
	padding: 15px 15px 0;
}

.zion-inline-editor-popover__link-title {
	display: flex;

	label {
		color: var(--zb-surface-icon-color);
		font-size: 10px;
		text-transform: uppercase;
	}

	.znpb-baseselect__trigger {
		margin-right: 15px;
	}

	.znpb-baseselect-overwrite,
	.znpb-form-label > .zion-input {
		margin-bottom: 8px;
	}

	.znpb-form-label {
		margin-bottom: 0;
	}
}
</style>
