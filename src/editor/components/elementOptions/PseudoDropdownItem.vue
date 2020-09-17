<template>
	<div
		class="znpb-element-options__media-class-pseudo-selector"
		@click.stop="onSelectorSelected"
	>
		{{selector.name}}

		<div class="znpb-element-options__pseudo-actions">
			<Tooltip
				content="Delete PseudoClass"
				v-if="clearable"
				tag="span"
			>
				<BaseIcon
					icon="delete"
					@click.native="$emit('delete-selector', selector.id)"
				></BaseIcon>
			</Tooltip>
			<HasChanges
				v-if="hasChanges"
				@remove-styles="$emit('remove-styles', selector.id)"
			/>
		</div>

		<ZionLabel
			v-if="selector.label"
			:text="selector.label.text"
			:type="selector.label.type"
			class="znpb-label--pro"
		/>

	</div>
</template>

<script>
import HasChanges from './HasChanges.vue'
import {
	Tooltip
} from '@zb/components'

import ZionLabel from '@/editor/common/Label'

export default {
	name: 'PseudoDropdownItem',
	components: {
		HasChanges,
		Tooltip,
		ZionLabel
	},
	props: {
		selector: {
			type: Object,
			required: true
		},
		selectorsModel: {
			type: Object,
			required: false
		},
		clearable: {
			tye: Boolean,
			required: false
		}
	},
	data () {
		return {}
	},
	computed: {
		selectorsModelComputed () {
			return this.selectorsModel || {}
		},
		hasChanges () {
			const activeSelectorData = (this.selectorsModelComputed || {})[this.selector.id] || {}
			return Object.keys(activeSelectorData).length > 0
		}
	},
	methods: {
		onSelectorSelected () {
			if (this.selector.active) {
				this.$emit('selector-selected', this.selector)
			}
		}
	}

}
</script>

<style lang="scss">
.znpb-element-options__pseudo-actions {
	display: flex;
	align-items: center;
	color: $surface-headings-color;
	font-size: 14px;

	&:hover {
		color: $surface-active-color;
	}
}

.znpb-element-options__media-class-pseudo {
	&-selector {
		display: flex;
		justify-content: space-between;
		align-items: center;
		min-width: 120px;
		cursor: pointer;

		&--active {
			color: $surface-active-color;
		}
		&:hover {
			color: $surface-active-color;
		}
	}
}
</style>
