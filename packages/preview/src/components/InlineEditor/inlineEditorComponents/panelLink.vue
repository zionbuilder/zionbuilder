<template>
	<PopOver icon="ite-link">
		<div class="zion-inline-editor-link-wrapper">
			<InputWrapper :title="$translate('add_a_link')">
				<BaseInput
					v-model="linkUrl"
					:clearable="true"
					placeholder="www.address.com"
					@keyup.enter="addLink"
				>
					<template v-slot:prepend>
						<Icon icon="link" />
					</template>
				</BaseInput>
			</InputWrapper>
			<div class="zion-inline-editor-popover__link-title">
				<InputWrapper :title="$translate('target')">
					<InputSelect
						:options="selectOptions"
						v-model="linkTarget"
						placeholder="Select target"
					/>
				</InputWrapper>
				<InputWrapper :title="$translate('title')">
					<BaseInput
						v-model="linkTitle"
						placeholder="link_title"
						:clearable="true"
						@keyup.enter="addLink"
					/>
				</InputWrapper>
			</div>
		</div>

	</PopOver>
</template>

<script>
import { inject, computed, ref, onMounted, onBeforeUnmount } from 'vue'

// Components
import PopOver from './PopOver.vue'

export default {
	components: {
		PopOver
	},
	props: {
		fullWidth: {
			type: Boolean,
			required: false
		},
		direction: {
			type: String,
			required: false
		},
		visible: {
			type: Boolean
		}
	},
	setup () {
		const editor = inject('ZionInlineEditor')
		const isPopOverVisible = ref(false)
		const justChangedNode = ref(false)
		const linkTarget = ref('_self')
		const linkUrl = ref('')
		const linkTitle = ref('')
		const selectOptions = [
			{
				id: '_self',
				name: 'Self'
			},
			{
				id: '_blank',
				name: 'New Window'
			}
		]

		const hasLink = ref(false)


		const buttonClasses = computed(() => {
			let classes = []

			// Check if the button is active
			if (hasLink.value) {
				classes.push('zion-inline-editor-button--active')
			}

			return classes.join(' ')
		})

		function onNodeChange (node) {
			if (node.selectionChange) {
				getLink()
			}
		}

		function getLink () {
			let link = editor.value.dom.getParent(editor.value.selection.getStart(), 'a[href]')

			if (link) {
				linkTarget.value = link.target || '_self'
				linkUrl.value = link.getAttribute('href')
				linkTitle.value = link.getAttribute('title')
				hasLink.value = true
			} else {
				linkUrl.value = null
				linkTitle.value = ''
				hasLink.value = false
			}

		}

		function addLink (closePopper = true) {
			if (linkUrl.value) {
				// Make the selection a link
				editor.value.formatter.apply('link', {
					href: linkUrl.value,
					target: linkTarget.value,
					title: linkTitle.value
				})
			} else {
				editor.value.formatter.remove('link')
			}

			if (closePopper) {
				isPopOverVisible.value = false
			}
		}

		onMounted(() => {
			getLink()
			editor.value.on('NodeChange', onNodeChange)
		})

		onBeforeUnmount(() => {
			editor.value.off('NodeChange', onNodeChange)
		})

		return {
			isPopOverVisible,
			linkTarget,
			linkUrl,
			linkTitle,
			selectOptions,
			buttonClasses,
			addLink,
			hasLink
		}
	}
}
</script>

<style lang="scss">
.zion-inline-editor-link-wrapper {
	padding: 15px 15px 0;
}

.zion-inline-editor-popover__link-title {
	display: flex;

	label {
		color: $surface-headings-color;
		font-size: 10px;
		text-transform: uppercase;
	}

	.znpb-baseselect__trigger {
		margin-right: 15px;
	}

	.znpb-baseselect-overwrite, .znpb-form-label > .zion-input {
		margin-bottom: 8px;
	}

	.znpb-form-label {
		margin-bottom: 0;
	}
}
</style>
