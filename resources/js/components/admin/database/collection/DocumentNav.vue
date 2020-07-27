<style lang="scss">
    /* no style yet */
</style>

<template>
    <div :id="'document-nav-' + index" ref="doc-nav" class="doc-nav">
        <span v-if="document.can_modify"><span class="pma-link" @click="$emit('update', document._id)">Update</span> |</span>
        <span v-if="document.can_delete"><span class="pma-link" @click="$emit('delete', document._id)">Delete</span></span>
        <span v-if="!document.can_delete"><span class="disabled-link" >Delete</span></span> |
        <span v-if="document.can_add_field"><span class="pma-link" @click="$emit('delete', document._id)">New Field</span></span>
        <span v-if="!document.can_add_field"><span class="disabled-link">New Field</span></span> |
        <span v-if="document.can_duplicate"><span class="pma-link" @click="$emit('duplicate', document._id)">Duplicate</span> |</span>
        <span v-if="document.can_refresh"><span class="pma-link" @click="$emit('refresh', document._id)">Refresh</span> |</span>
        <span class="pma-link" @click="$emit('text', $event.target)">Text</span> |
        <span class="pma-link" @click="$emit('expand', $event.target)">Expand</span>
    </div>
</template>

<script>
    export default {
        props: ['document','collection', 'index'],

        methods: {
            // to come ??
        }
    }
</script>
