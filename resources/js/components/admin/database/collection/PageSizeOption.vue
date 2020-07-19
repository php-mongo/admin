<style lang="scss">
   /* nothing to show here */
</style>

<template>
    <option :value="size" :selected="size === value">
        Rows:{{ size }}
    </option>
</template>

<script>
    export default {
        props: {
            size: Number,
            value: Number
        }
    }
</script>
