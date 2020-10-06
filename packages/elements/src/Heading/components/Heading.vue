<template>
	<component
		:is="options.tag || 'h1'"
	>
		<slot name="start" />

		<a
			@click.prevent="(e) => {e.preventDefault()}"
			v-if="hasLink"
			:href="options.link.link"
			:title="options.link.title"
			:target="options.link.target"
		>
			<RenderValue
				option="content"
				:forced-root-node="false"
			/>
		</a>

		<RenderValue
			v-else
			option="content"
				:forced-root-node="false"
		/>

		<slot name="end" />
	</component>
</template>

<script>
export default {
	name: 'zion_heading',
	props: ['options', 'data', 'api'],
	computed: {
		hasLink () {
			return this.options.link && this.options.link.link
		}
	}
}
</script>
