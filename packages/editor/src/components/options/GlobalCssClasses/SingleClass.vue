<template>
	<li class="znpb-editor-classes__item">
		{{classItem.name || classItem.id}}
		<div class="znpb-editor-classes__actions">
			<!-- trigger of slot level-two -->

			<Tooltip
				tag="span"
				:content="$translate('click_to_edit_class')"
			>
				<Icon
					class="znpb-edit-icon-pop"
					icon="edit"
					@click="$emit('edit-class',classItem)"
				/>
			</Tooltip>

			<Tooltip
				tag="span"
				:content="$translate('click_to_delete_globally')"
			>
				<Icon
					icon="delete"
					@click.stop="$emit('delete-class',classItem)"
				/>
			</Tooltip>

		</div>
	</li>
</template>
<script>
export default {
	name: 'SingleClass',
	props: {
		classItem: {
			type: Object,
			required: true
		}
	},
	data: () => {
		return {

		}
	},
	methods: {

	}
}

</script>
<style lang="scss">
.znpb-editor-classes {
	&__item {
		display: flex;
		justify-content: space-between;
		align-items: center;
		width: 100%;
		padding: 0 15px;
		color: $surface-active-color;
	}
	&__actions {
		.znpb-edit-icon-pop {
			margin-right: 10px;
		}
		span {
			text-transform: none;
		}
		.znpb-editor-icon-wrapper {
			padding: 0;
			color: $font-color;
			cursor: pointer;

			&:hover {
				color: darken($font-color, 15%);
			}
		}
	}
}
.znpb-horizontal-accordion__header--has-slots {
	.znpb-editor-classes {
		&__actions {
			.znpb-edit-icon-pop {
				margin-right: 10px;
			}
			span {
				text-transform: none;
			}
			.znpb-editor-icon-wrapper {
				padding: 18px 0;
			}
		}
	}
}
</style>
