import { createApp } from 'vue';
import RegenerateAssetsApp from './components/RegenerateAssetsApp.vue';
import { createPinia } from 'pinia';
import '/@/common/scss/index.scss';

const appInstance = createApp(RegenerateAssetsApp);

appInstance.use(createPinia());

appInstance.mount('#znpb-regenerateAssetsNotice');
