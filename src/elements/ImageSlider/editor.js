import { registerElementComponent } from '@zb/editor';
import ImageSlider from './components/ImageSlider.vue';

registerElementComponent({
	elementType: 'image_slider',
	component: ImageSlider,
});
