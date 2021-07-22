<template>
	<div
		class="znpb-element-options__media-class-pseudo-selector"
		@click.stop="onSelectorSelected"
	>
		{{selector.name}}

		<div class="znpb-element-options__pseudo-actions">
			<Tooltip
				content="Delete Pseudo Selector"
				v-if="clearable"
				tag="span"
			>
				<Icon
					icon="delete"
					@click.stop="onDeleteSelector"
				></Icon>
			</Tooltip>
			<ChangesBullet
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
import ZionLabel from '../../common/Label.vue'

export default {
	name: 'PseudoDropdownItem',
	components: {
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
		onDeleteSelector () {
			this.$emit('delete-selector', this.selector)
			this.$emit('selector-selected', null)
		},
		onSelectorSelected () {
			this.$emit('selector-selected', this.selector)
		}
	}

}
</script>

<style lang="scss">
.znpb-element-options__pseudo-actions {
	display: flex;
	align-items: center;
	color: var(--zb-surface-icon-color);
	font-size: 14px;

	&:hover {
		color: var(--zb-surface-text-hover-color);
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
			color: var(--zb-surface-text-active-color);
		}
		&:hover {
			color: var(--zb-surface-text-active-color);
		}
	}
}
</style>
