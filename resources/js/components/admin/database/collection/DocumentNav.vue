<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      DocumentNav.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      DocumentNav.vue
  - @subpackage   Id
  - @link         https://github.com/php-mongo/admin PHP MongoDB Admin
  - @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
  - @licence      PhpMongoAdmin is Open Source and is released under the MIT licence model.
  - @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
  -  php-mongo-admin - License conditions:
  -  Contributions via our suggestion box are welcome. https://phpmongotools.com/suggestions
  -  This web application is available as Free Software and has no implied warranty or guarantee of usability.
  -  See licence.txt for the complete licensing outline.
  -  See COPYRIGHT.php for copyright notices and further details.
  -->

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
