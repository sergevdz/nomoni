<template>
  <div class="q-pa-md bg-grey-3">
    <div :class="`row bg-${color} border-panel`">
      <div class="col q-pa-md">
         <slot></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContentPanel',
  props: {
    color: {
      type: String,
      default: 'white'
    }
  }
}
</script>
