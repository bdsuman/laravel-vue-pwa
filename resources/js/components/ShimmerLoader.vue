<scriptsetup>
defineProps({
  type: {
    type: String,
    default: 'text'
  },
  width: {
    type: String,
    default: '100%'
  },
  height: {
    type: String,
    default: '1rem'
  },
  rounded: {
    type: String,
    default: 'rounded'
  }
})
</script>

<template>
  <div 
    class="bg-gray-200 dark:bg-gray-700 animate-pulse"
    :class="[
      type === 'circle' ? 'rounded-full' : rounded,
      type === 'card' ? 'rounded-lg' : ''
    ]"
    :style="{
      width: type === 'circle' ? height : width,
      height: type === 'circle' ? height : height
    }"
  ></div>
</template>