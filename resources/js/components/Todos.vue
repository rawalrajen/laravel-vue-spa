<template>
  <div class="card">
    <div class="card-header">
      <div class="form-group">
        <div
          class="btn-group-vertical buttons"
          role="group"
          aria-label="Basic example"
        >
          <button class="btn btn-outline-primary" @click="add">
            Add Element
          </button>
        </div>
      </div>
    </div>

    <div class="card-body">
      <draggable
        :list="list"
        class="list-group"
        ghost-class="ghost"
        :disabled="loading"
        @change="update"
      >
        <transition-group name="list-group">
          <div
            v-for="element in list"
            :key="element.id"
            class="list-group-item"
          >
            {{ element.name }}
          </div>
        </transition-group>
      </draggable>
    </div>
    <div class="card-footer">
      <p class="pull-right">
        Total Elements: {{ count }}
      </p>
    </div>
  </div>
</template>

<script>
import draggable from 'vuedraggable'
import Resource from '../api/resource'

const elementResource = new Resource('elements')
export default {
  name: 'ElementList',
  display: 'Simple',
  components: {
    draggable
  },
  data () {
    return {
      list: [],
      loading: true
    }
  },
  computed: {
    count () {
      return this.list.length
    }
  },
  created () {
    this.getList()
  },
  methods: {
    async getList () {
      this.loading = true
      const { data } = await elementResource.list({})
      this.list = data
      this.loading = false
    },
    add: function () {
      const elementName = 'Element ' + (this.count + 1)
      this.loading = true
      elementResource
        .store({ name: elementName })
        .then(response => {
          this.list.push(response.data)
        })
        .catch(error => {
          console.log(error)
        })
        .finally(() => {
          this.loading = false
        })
    },
    update: function (e) {
      if (typeof e.moved !== 'undefined') {
        this.loading = true
        const movedElement = e.moved
        console.log('old position ' + movedElement.oldIndex)
        console.log('new position ' + movedElement.newIndex)
        elementResource.updatePosition(movedElement.element.id,
          {
            old_index: movedElement.oldIndex,
            new_index: movedElement.newIndex
          }
        ).then(response => {
          console.log(response.data)
        }).catch(error => {
          console.log(error)
        }).finally(() => {
          this.getList()
          this.loading = false
        })
      }
    }
  }
}
</script>
<style scoped>
    .buttons {
        margin-top: 35px;
    }

    .ghost {
        opacity: 0.5;
        background: #c8ebfb;
    }

    .list-group-item {
        cursor: move; /* fallback if grab cursor is unsupported */
        cursor: -moz-grab;
        cursor: -webkit-grab;
    }

    .card-body {
        overflow-y: scroll;
        max-height: 500px;
    }
    .list-group-enter-active, .list-leave-active {
      transition: all 1s;
    }
    .list-group-enter, .list-leave-to /* .list-leave-active below version 2.1.8 */ {
      opacity: 0;
      transform: translateY(30px);
    }
</style>
