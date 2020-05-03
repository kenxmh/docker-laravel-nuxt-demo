<template>
  <div>
    <MyNav />
    <nuxt />

    <MyFooter />
  </div>
</template>

<script>
import MyNav from '~/components/MyNav'
import MyFooter from '~/components/MyFooter'

export default {
  middleware: 'global',
  components: {
    MyNav,
    MyFooter
  }
}
</script>


