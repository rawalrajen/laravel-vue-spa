<template>
  <div class="container">
    <div class="card">
      <todos />
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Todos from '../components/Todos'

export default {
  components: { Todos },
  layout: 'basic',

  metaInfo () {
    return { title: this.$t('home') }
  },

  data: () => ({
    title: 'Todo List'
  }),

  computed: mapGetters({
    authenticated: 'auth/check'
  })
}
</script>

<style scoped>
    .top-right {
        position: absolute;
        right: 10px;
        top: 18px;
    }

    .title {
        font-size: 85px;
    }
</style>
