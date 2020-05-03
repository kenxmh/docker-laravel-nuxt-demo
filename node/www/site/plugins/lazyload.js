import Vue from 'vue'
import VueLazyLoad from 'vue-lazyload'

Vue.use(VueLazyLoad, {
    loading: '/loading.gif',
    error: '/error.png'
})