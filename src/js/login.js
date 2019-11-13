import Vue from 'vue'
import Login from "./components/login.vue";

import ElementUI from 'element-ui';
import locale from "element-ui/lib/locale/lang/tr-TR"
import MyDateVue from './lib/MyDateVue'
import IsString from './lib/IsString';
import Url from './lib/UrlVue'
import AxiosHttp from "./lib/AxiosHttp"
import DefaultMixin from "./lib/DefaultMixin"


Vue.use(ElementUI,{ locale });
Vue.use(MyDateVue);
Vue.use(IsString);
Vue.use(Url);
Vue.use(AxiosHttp);
Vue.mixin(DefaultMixin);


new Vue({    
  render: h => h(Login)
  }).$mount('#app')