<template>
	<div>
		<slot name="start" />

		<iframe
			:src="`https://www.google.com/maps?api=1&q=${location}&z=${zoom}&output=embed&t=${mapType}`"
			frameborder="0"
			style="border:0;margin-bottom:0"
			allowfullscreen=""
		></iframe>

		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'google_maps',
	props: ['options', 'element', 'api'],
	computed: {
		location () {
			return encodeURIComponent(this.options.location || 'Chicago')
		},
		zoom () {
			return this.options.zoom || 15
		},
		mapType () {
			return this.options.map_type === 'terrain' ? 'k' : ''
		}
	}
}
</script>
