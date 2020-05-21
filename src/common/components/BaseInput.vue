<template>
	<div class="znpb-form-element">
		<h5
			class="znpb-form__input-title"
			v-if="title"
		>{{title}}</h5>
		<slot></slot>
		<p
			class="znpb-form__input-description"
			v-if="description"
		>{{description}}</p>
	</div>
</template>
<script>
export default {
	name: 'BaseInput',
	props: {
		title: {
			type: String,
			required: false
		},
		description: {
			type: String,
			required: false
		}
	},
	data: function () {
		return {}
	}
}
</script>
<style lang="scss">
.znpb-form {
	&__input-title {
		margin-bottom: 14px;
		color: $surface-active-color;
		font-size: 13px;
		font-weight: 600;
		line-height: 17px;
	}
	&__input-description {
		margin-bottom: 14px;
		color: $surface-headings-color;
		font-size: 13px;
		font-weight: 400;
	}
}

.znpb-form-element {
	margin-bottom: 25px;
	input {
		width: 100%;
	}
}
</style>
