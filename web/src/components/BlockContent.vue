<template>
  <div class="q-pb-md q-px-md bg-grey-3" :class="{ 'q-mt-md': first}">
    <div class="row bg-white content-panel">
      <div class="col q-pa-md">
        <slot />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BlockContent',
  props: {
    // title: String,
    // likes: Number,
    first: Boolean
    // commentIds: Array,
    // author: Object,
    // callback: Function,
    // contactsPromise: Promise // or any other constructor
  }
}
</script>

<style lang="stylus" scoped>
</style>
