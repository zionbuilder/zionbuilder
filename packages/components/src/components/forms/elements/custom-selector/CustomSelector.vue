<template>
	<div class="znpb-custom-selector">
		<ul class="znpb-custom-selector__list-wrapper">
			<li
				v-for="(option, index) in options"
				:key="index"
				class="znpb-custom-selector__item"
				:title="option.icon ? option.name : false"
				:class="{
					['znpb-custom-selector__item--active']: modelValue === option.id,
					[`znpb-custom-selector__columns-${columns}`]: columns
				}"
				@click="changeValue(option.id)"
			>
				<span
					class="znpb-custom-selector__item-name"
					v-if="!option.icon"
				>
					{{option.name}}
				</span>
				<BaseIcon
					v-if="!textIcon && option.icon"
					:icon="option.icon"
				/>
				<div
					class="znpb-custom-selector__icon-text-content"
					v-if="textIcon"
				>
					<BaseIcon
						v-if="option.icon"
						:icon="option.icon"
					/>
					<span
						class="znpb-custom-selector__item-name"
						v-if="option.name"
					>
						{{option.name}}
					</span>
				</div>
			</li>
		</ul>
	</div>
</template>

<script>
import BaseIcon from '../../../BaseIcon.vue'

export default {
	name: 'CustomSelector',
	components: {
		BaseIcon
	},
	props: {
		options: {
			type: Array,
			required: true
		},
		columns: {
			type: Number,
			required: false
		},
		modelValue: {
			type: [String, Number, Boolean]
		},
		textIcon: {
			type: Boolean
		}
	},
	data () {
		return {}
	},
	methods: {
		changeValue (newValue) {
			let valueToSend = newValue
			// If the same value was selected, we need to delete it
			if (this.modelValue === newValue) {
				valueToSend = null
			}

			this.$emit('update:modelValue', valueToSend)
		}
	}
}
</script>

<style lang="scss">
.znpb-custom-selector {
	overflow: hidden;
	padding: 3px;
	background-color: $surface-variant;
	border-radius: 3px;

	&__list-wrapper {
		display: flex;
		flex-wrap: wrap;
	}

	&__item {
		display: flex;
		justify-content: center;
		align-items: center;
		flex: 1 1 auto;
		padding: 10px 5px;
		font-size: 13px;
		font-weight: 500;
		border-radius: 2px;
		cursor: pointer;

		&:hover {
			background-color: darken($surface-variant, 3%);
		}
		&--active {
			color: $surface;
			background-color: $secondary;
			&:hover {
				color: $surface;
				background-color: $secondary;
			}
		}
	}

	&__columns-1 {
		width: 100%;
	}
	&__columns-2 {
		width: 50%;
	}
	&__columns-3 {
		flex-basis: 33%;
		width: calc(100% / 3);
	}
	&__columns-4 {
		width: 25%;
	}
	&__icon-text-content {
		display: flex;
		flex-direction: column;
	}
}
</style>
