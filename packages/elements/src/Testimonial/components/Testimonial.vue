<template>
	<div>
		<slot name="start" />
		<img
			v-if="image && options.position!==undefined && options.position ==='top'"
			class="zb-el-testimonial__userImage"
			:class="api.getStyleClasses('inner_content_styles_image')"
			v-bind="api.getAttributesForTag('inner_content_styles_image')"
			:src="image"
		/>
		<RenderValue
			option="content"
			class="zb-el-testimonial-content"
			:class="api.getStyleClasses('inner_content_styles_misc')"
			v-bind="api.getAttributesForTag('inner_content_styles_misc')"
		/>

		<div class="zb-el-testimonial__user">
			<img
				v-if="image && options.position !==undefined && options.position !=='top'"
				class="zb-el-testimonial__userImage"
				v-bind="api.getAttributesForTag('inner_content_styles_image')"
				:src="image"
			/>

			<div class="zb-el-testimonial__userInfo">
				<RenderValue
					option="name"
					:class="api.getStyleClasses('inner_content_styles_user')"
					class="zb-el-testimonial__userInfo-name"
					v-bind="api.getAttributesForTag('inner_content_styles_user')"
				/>
				<RenderValue
					option="description"
					:class="api.getStyleClasses('inner_content_styles_description')"
					class="zb-el-testimonial__userInfo-description"
					v-bind="api.getAttributesForTag('inner_content_styles_description')"
				/>
				<div
					class="zb-el-testimonial__stars"
					:class="api.getStyleClasses('inner_content_styles_stars')"
					v-bind="api.getAttributesForTag('inner_content_styles_stars')"
					v-if="stars && stars !== 'no_stars'"
				>
					<ElementIcon
						v-for="(star, index) in stars"
						class="zb-el-testimonial__stars--full"
						:key="index+10"
						:iconConfig="getStar"
					/>
					<ElementIcon
						v-for="star in (5 - stars)"
						:key="star"
						:iconConfig="getEmptyStar"
					/>
				</div>
			</div>
		</div>

		<slot name="end" />
	</div>
</template>
<script>
export default {
	name: 'testimonial',
	props: ['element', 'options', 'api'],
	computed: {
		image () {
			return this.options && this.options.image ? this.options.image : null
		},
		content () {
			return (this.options && this.options.content) ? this.options.content : null
		},
		name () {
			return this.options && this.options.name ? this.options.name : null
		},
		description () {
			return this.options && this.options.description ? this.options.description : null
		},
		getStar () {
			return {
				family: 'Font Awesome 5 Free Solid',
				name: 'star',
				unicode: 'uf005'
			}
		},
		getEmptyStar () {
			return {
				family: 'Font Awesome 5 Free Regular',
				name: 'star',
				unicode: 'uf005'
			}
		},
		stars () {
			return this.options.stars || 5
		}
	}

}
</script>
