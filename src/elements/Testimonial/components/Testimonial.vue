<template>
	<div>
		<slot name="start" />
		<img
			v-if="image && options.position !== undefined && options.position === 'top'"
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
				v-if="image && options.position !== undefined && options.position !== 'top'"
				class="zb-el-testimonial__userImage"
				:class="api.getStyleClasses('inner_content_styles_image')"
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
					v-if="stars && stars !== 'no_stars'"
					class="zb-el-testimonial__stars"
					:class="api.getStyleClasses('inner_content_styles_stars')"
					v-bind="api.getAttributesForTag('inner_content_styles_stars')"
				>
					<ElementIcon
						v-for="(star, index) in stars"
						:key="index + 10"
						class="zb-el-testimonial__stars--full"
						:icon-config="getStar"
					/>
					<ElementIcon v-for="star in 5 - stars" :key="star" :icon-config="getEmptyStar" />
				</div>
			</div>
		</div>

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: {
		image: string;
		content: string;
		name: string;
		description: string;
		stars: string | number;
		position: string;
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const image = computed(() => {
	return props.options && props.options.image ? props.options.image : null;
});

const getStar = computed(() => {
	return {
		family: 'Font Awesome 5 Free Solid',
		name: 'star',
		unicode: 'uf005',
	};
});

const getEmptyStar = computed(() => {
	return {
		family: 'Font Awesome 5 Free Regular',
		name: 'star',
		unicode: 'uf005',
	};
});

const stars = computed(() => {
	return props.options.stars || 5;
});
</script>
