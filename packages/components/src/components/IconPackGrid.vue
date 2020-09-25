<template>

	<div
		class="znpb-icon-pack-modal__icons"
	>
		<div
			class="znpb-icon-pack-modal__grid"
			v-if="iconList.length > 0"
		>
			<div class="znpb-icon-pack-modal-icon"
				v-for="(icon,i) in iconList"
				:key="i"
			>
				<div
					class="znpb-modal-icon-wrapper"
					:class="{'znpb-modal-icon-wrapper--active' : activeIcon===icon.name && activeFamily===family}"
					@click="$emit('icon-selected', icon)"
					@dblclick="$emit('input', icon)"
				>
					<span
						:data-znpbiconfam="family"
						:data-znpbicon="unicode(icon.unicode)"
					></span>

				</div>
				<h4 class="znpb-modal-icon-wrapper__title">{{icon.name}}</h4>
			</div>
		</div>
		<span v-else>{{$translate('no_icons_in_package')}} {{family}}</span>
	</div>

</template>

<script>

export default {
	name: 'IconsPackGrid',

	props: {
		iconList: {
			type: Array,
			required: false
		},
		family: {
			type: String,
			required: false
		},
		activeIcon: {
			type: String,
			required: false
		},
		activeFamily: {
			type: String,
			required: false
		}

	},
	data () {
		return {
		}
	},
	methods: {
		unicode (unicode) {
			return JSON.parse(('"\\' + unicode + '"'))
		}
	},
	computed: {
	}
}
</script>
<style lang="scss">

.znpb-icon-pack-modal {
	&__icons {
		padding: 0 10px 20px;
	}
	&__grid {
		display: grid;

		grid-column-gap: 20px;
		grid-row-gap: 20px;
		grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));

		h4.znpb-modal-icon-wrapper__title {
			margin-bottom: 0;
			font-size: 12px;
			font-weight: 500;
			line-height: 1;
			text-align: center;
		}
	}
}
.znpb-modal-icon-wrapper {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 92px;
	margin-bottom: 5px;
	box-shadow: 0 5px 10px 0 rgba(164, 164, 164, .08);
	border: 1px solid $surface-variant;
	border-radius: 3px;
	transition: all .2s;
	cursor: pointer;

	span {
		color: $font-color;
		font-size: 28px;
		transition: all .2s;
	}
	&:hover, &--active {
		border: 2px solid $secondary;
		span {
			color: $surface-active-color;
		}
	}
}
</style>
