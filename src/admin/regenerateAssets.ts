import { createApp } from 'vue';
import RegenerateAssetsApp from './components/RegenerateAssetsApp.vue';
import { createPinia } from 'pinia';
import '/@/common/scss/index.scss';

import { install as I18nInstall } from '/@//common/modules/i18n';

const appInstance = createApp(RegenerateAssetsApp);

appInstance.use(I18nInstall, window.ZnI18NStrings);
appInstance.use(createPinia());

appInstance.mount('#znpb-regenerateAssetsNotice');
