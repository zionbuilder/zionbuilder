<template>
	<div class="znpb-dimensions-wrapper">
		<div v-for="(dimension, i) in computedDimensions" :key="i" class="znpb-dimension" :class="`znpb-dimension--${i}`">
			<div v-if="dimension.name !== 'link'" class="znpb-dimensions_icon">
				<Icon :icon="dimension.icon"></Icon>
			</div>
			<InputNumberUnit
				v-if="dimension.name !== 'link'"
				:modelValue="valueModel[dimension.id] || null"
				:title="dimension.id"
				:min="min"
				:max="max"
				:units="['px', 'rem', 'pt', 'vh', '%']"
				:step="1"
				default-unit="px"
				:placeholder="placeholder ? placeholder[dimension.id] : null"
				@update:modelValue="onValueUpdated(dimension.id, $event)"
				@linked-value="handleLinkValues"
			/>
			<div v-if="dimension.name === 'link'" class="znpb-dimensions__center">
				<Icon
					:icon="linked ? 'link' : 'unlink'"
					:title="linked ? 'Unlink' : 'Link'"
					class="znpb-dimensions__link"
					:class="{ ['znpb-dimensions__link--linked']: linked }"
					@click="handleLinkValues"
				></Icon>
			</div>
		</div>
	</div>
</template>
<script>
export default {
	name: 'Dimensions',
	props: {
		/**
		 * v-model/value for border radius
		 */
		modelValue: {
			default() {
				return {};
			},
			type: Object,
			required: false,
		},
		dimensions: {
			type: Array,
			required: true,
		},
		min: {
			type: Number,
		},
		max: {
			type: Number,
		},
		placeholder: {
			required: false,
			type: Object,
			default: () => {
				return {};
			},
		},
	},
	data() {
		return {
			linked: false,
		};
	},

	computed: {
		valueModel() {
			return this.modelValue || {};
		},
		computedDimensions() {
			return [
				...this.dimensions,
				{
					name: 'link',
					id: 'link',
				},
			];
		},
	},
	methods: {
		handleLinkValues() {
			this.linked = !this.linked;

			if (this.linked) {
				// Check to see if we already have a saved value
				const dimensionsIDs = this.dimensions.map(dimension => dimension.id);
				const savedPositionValue = Object.keys(this.valueModel).find(
					position => dimensionsIDs.includes(position) && typeof this.valueModel[position] !== 'undefined',
				);

				if (savedPositionValue) {
					this.onValueUpdated('', this.valueModel[savedPositionValue]);
				}
			}
		},
		onValueUpdated(position, newValue) {
			/**
			 * emits new object with new value of borders
			 */
			if (this.linked) {
				const valuesToUpdate = this.dimensions.filter(dimension => {
					return dimension.id !== 'link';
				});
				let values = {};
				valuesToUpdate.forEach(value => {
					values[value.id] = newValue;
				});
				this.$emit('update:modelValue', {
					...this.valueModel,
					...values,
				});
			} else {
				this.$emit('update:modelValue', {
					...this.valueModel,
					[position]: newValue,
				});
			}
		},
	},
};
</script>
<style lang="scss">
.znpb-dimensions-wrapper {
	display: grid;

	grid-template-areas:
		'a b c'
		'd b e';
	.znpb-dimensions__link {
		color: var(--zb-surface-icon-color);
		background-color: var(--zb-surface-lighter-color);
		transition: color 0.15s;

		&:hover {
			color: var(--zb-surface-text-hover-color);
		}
	}
	.znpb-dimensions__link.znpb-dimensions__link--linked {
		color: var(--zb-secondary-text-color);
		background-color: var(--zb-secondary-color);
	}
}
.znpb-dimensions__center {
	display: flex;
	justify-content: center;
	align-items: center;
	flex: 2;
	.znpb-editor-icon-wrapper {
		display: inline-flex;
		justify-content: center;
		align-items: center;
		width: 40px;
		height: 40px;
		border-radius: 3px;
		cursor: pointer;
	}
}

.znpb-dimensions-wrapper > .znpb-tabs__content {
	padding-top: 15px;
}
.znpb-dimension {
	display: flex;
	align-items: center;
	margin: 0 0 10px;
	&--0 {
		.znpb-dimensions_icon {
			margin-right: 10px;
		}
	}
	&--1 {
		flex-direction: row-reverse;
		.znpb-dimensions_icon {
			margin-left: 10px;
		}
	}
	&--4 {
		margin: 10px;

		grid-area: b;
	}
	&--2 {
		.znpb-dimensions_icon {
			margin-right: 10px;
		}
	}
	&--3 {
		flex-direction: row-reverse;
		.znpb-dimensions_icon {
			margin-left: 10px;
		}
	}
}
</style>
