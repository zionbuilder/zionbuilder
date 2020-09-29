<template>
	<div
		class="zion-inline-editor-popover-wrapper zion-inline-editor-popover-wrapper--big-pd"
		:class="{
		'zion-inline-editor-popover-wrapper--full-width' : fullWidth,
		'zion-inline-editor-popover-wrapper--vertical': direction,
		'zion-inline-editor-popover-wrapper--open': visible && isPopOverVisible
		}"
	>
		<BaseIcon
			icon="ite-link"
			@mousedown="togglePopper"
			:class='buttonClasses'
		/>
		<transition name="bar-show">
			<div
				v-if="isPopOverVisible"
				class="zion-inline-editor-dropdown zion-inline-editor-dropdown--popover"
			>
				<inputWrapper :title="$translate('add_a_link')">
					<BaseInput
						v-model="linkModel"
						:clearable="true"
						placeholder="www.address.com"
						@keyup.enter="addLink"
					>
						<BaseIcon
							slot="prepend"
							icon="link"
							@click="addLink"
						/>
					</BaseInput>
				</inputWrapper>
				<div class="zion-inline-editor-popover__link-title">
					<InputWrapper :title="$translate('target')">
						<InputSelect
							:options="selectOptions"
							v-model="linkTargetModel"
							placeholder="Select target"
						/>
					</InputWrapper>
					<InputWrapper :title="$translate('title')">
						<BaseInput
							v-model="linkTitleModel"
							placeholder="link_title"
							:clearable="true"
						/>
					</InputWrapper>
				</div>
			</div>
		</transition>
	</div>

</template>

<script>
import { BaseInput, InputSelect, InputWrapper } from '@/common/components/forms'

export default {
	inject: {
		Editor: {
			default () {
				return {}
			}
		}
	},
	components: {
		BaseInput,
		InputSelect,
		InputWrapper
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
	watch: {
		visible (newVal) {
			this.isPopOverVisible = newVal
		}
	},
	data: function () {
		return {
			isPopOverVisible: this.visible,
			linkTarget: '_self',
			linkUrl: '',
			linkTitle: '',
			selectOptions: [
				{
					id: '_self',
					name: 'Self'
				},
				{
					id: '_blank',
					name: 'New Window'
				}
			],
			justChangedNode: null
		}
	},

	computed: {
		linkModel: {
			get () {
				return this.linkUrl
			},
			set (newVal) {
				this.linkUrl = newVal
			}
		},
		linkTargetModel: {
			get () {
				return this.linkTarget
			},
			set (newVal) {
				this.linkTarget = newVal
			}
		},
		linkTitleModel: {
			get () {
				return this.linkTitle
			},
			set (newVal) {
				this.linkTitle = newVal
			}
		},
		buttonClasses () {
			let classes = []

			if (this.isPopOverVisible) {
				classes.push('zion-inline-editor-button--active')
			}

			return classes.join(' ')
		}
	},

	beforeMount: function () {
		this.Editor.editor.on('NodeChange', this.onNodeChange)

		this.getLink(this.Editor.editor.selection.getNode())
	},
	methods: {
		togglePopper () {
			if (this.isPopOverVisible) {
				this.isPopOverVisible = false
				this.$emit('close-panel', this)
				this.addLink()
			} else {
				this.isPopOverVisible = true
				this.$emit('open-panel', this)
			}
		},
		onNodeChange (node) {
			if (node.selectionChange && !this.justChangedNode) {
				this.getLink(node.element)
			}
			this.justChangedNode = false
		},
		getLink (node) {
			let link = this.Editor.editor.dom.getParent(node, 'a[href]')

			if (link) {
				this.linkTarget = link.target || '_self'
				this.linkUrl = link.getAttribute('href')
			} else {
				this.linkUrl = null
				this.linkTitle = ''
			}
		},
		addLink () {
			if (this.linkUrl) {
				// Make the selection a link
				this.Editor.editor.formatter.apply('link', {
					href: this.linkUrl,
					target: this.linkTarget,
					title: this.linkTitle
				})
			} else {
				this.Editor.editor.formatter.remove('link')
			}
		}

	}
}
</script>

<style lang="scss">
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
/* popover animations */
.bar-show-enter-active, .bar-show-leave-active {
	transition: all .2s;
}
.bar-show-enter, .bar-show-leave-to {
	opacity: 0;
}
</style>
