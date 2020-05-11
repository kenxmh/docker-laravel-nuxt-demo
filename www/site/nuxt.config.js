
export default {
  mode: 'universal',
  env: {
    BAIDU_ANALYTICS_KEY: '',
    GOOGLE_ANALYTICS_KEY: '',
  },
  server: {
    port: 3000, // default: 3000
    host: '0.0.0.0' // default: localhost,
  },
  /*
  ** Headers of the page
  */
  head: {
    title: 'Kenbucket',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },
  /*
  ** Customize the progress-bar color
  */
  loading: { color: '#fff' },
  // loading: '~/components/loading.vue',
  /*
  ** Global CSS
  */
  css: [
    '~/assets/styles/main.scss'
  ],
  // styleResources: {
  //   scss: ['~/assets/styles/main.scss']
  // },
  /*
  ** Plugins to load before mounting the App
  */
  plugins: [
    '~/plugins/axios',
    '~/plugins/font-awesome',
    '~/plugins/filters',
    '~/plugins/kenbucket',
    { src: '~/plugins/infinite-scroll', ssr: false },
    { src: '~/plugins/lazyload', ssr: false },
    { src: '~/plugins/ga-baidu', ssr: false },
    { src: '~/plugins/ga-google', ssr: false },
  ],
  /*
  ** Nuxt.js dev-modules
  */
  buildModules: [
  ],
  /*
  ** Nuxt.js modules
  */
  modules: [
    // Doc: https://github.com/nuxt-community/modules/tree/master/packages/bulma

    'nuxt-buefy',
    '@nuxtjs/style-resources',
    'nuxt-fontawesome',
    '@nuxtjs/axios',
    '@nuxtjs/toast',
    ['cookie-universal-nuxt', { alias: 'cookies' }],
  ],

  /*
  ** Axios module configuration
  ** See https://axios.nuxtjs.org/options
  */
  axios: {
    baseURL: 'http://nginx:80/',
    proxy: true,
    debug: false
  },
  // proxy: {
  //   '/api': {
  //     target: 'http://localhost:80',
  //     changeOrigin: true
  //   },
  // },
  // Doc: https://github.com/shakee93/vue-toasted
  // Doc: https://github.com/nuxt-community/modules/tree/master/packages/toast
  toast: {
    position: 'top-right',
    duration: 2000, // Display time of the toast in millisecond
    keepOnHover: true // When mouse is over a toast's element, the corresponding duration timer is paused until the cursor leaves the element
  },
  
  /*
  ** FontAwesome module configuration 配置FontAwesome
  */
  fontawesome: {
    // See https://github.com/vaso2/nuxt-fontawesome
    // 这里设置了组建的标签为fa
    // 如果不设置，则默认为在font-awesome.js中，生成的：font-awesome-icon
    component: 'fa',
    imports: [
      {
        set: '@fortawesome/free-solid-svg-icons',
        icons: ['fas']
      },
      {
        set: '@fortawesome/free-regular-svg-icons',
        icons: ['far']
      },
      {
        set: '@fortawesome/free-brands-svg-icons',
        icons: ['fab']
      }
    ]
  },

  /*
  ** Build configuration
  */
  build: {
    postcss: {
      preset: {
        features: {
          customProperties: false
        }
      }
    },
    /*
    ** You can extend webpack config here
    */
    extend(config, ctx) {
    }
  },

  // 
  // watchers: {
  //   webpack: {
  //     poll: true
  //   }
  // }
}
