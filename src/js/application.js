import Vue from 'vue'
import ElementUI from 'element-ui';
import locale from "element-ui/lib/locale/lang/tr-TR"
import MyDateVue from './lib/MyDateVue'
import IsStringVue from './lib/IsStringVue';
import Url from './lib/UrlVue'
import AxiosHttp from "./lib/AxiosHttp"
import DefaultMixin from "./lib/DefaultMixin"

import Container from './components/container.vue'
import store from './plugins/store'

import VueRouter from 'vue-router'
import routes from "./plugins/routes"


Vue.use(ElementUI,{ locale });
Vue.use(MyDateVue);
Vue.use(IsStringVue);
Vue.use(Url);
Vue.use(AxiosHttp);
Vue.mixin(DefaultMixin);
Vue.use(VueRouter);

Vue.config.productionTip = false;

var router = new VueRouter({ routes });

new Vue({
  router,
  store,
  render: h => h(Container)
}).$mount('#app')