window.zb = window.parent.zb;

// import { createApp } from 'vue';
// import { createPinia } from 'pinia';

// import previewApp from './PreviewApp.vue';
// import { install as ComponentsInstall } from '../common';

// // Components
// import { InlineEditor } from './components/InlineEditor';
// import Element from './components/Element.vue';
// import ElementWrapper from './components/ElementWrapper.vue';
// import SortableContent from './components/SortableContent.vue';
// import RenderValue from './components/RenderValue.vue';
// import ElementIcon from './components/ElementIcon.vue';

// // Render the app
// const renderElement = document.getElementById(`znpb-preview-${window.ZnPbPreviewData.post.ID}-area`);
// if (renderElement) {
// 	const app = createApp(previewApp);
// 	app.component('Element', Element);
// 	app.component('ElementWrapper', ElementWrapper);
// 	app.component('InlineEditor', InlineEditor);
// 	app.component('SortableContent', SortableContent);
// 	app.component('RenderValue', RenderValue);
// 	app.component('ElementIcon', ElementIcon);

// 	app.use(ComponentsInstall);
// 	app.use(createPinia());

// 	app.mount(renderElement);
// } else {
// 	console.log('preview element not found');
// }
