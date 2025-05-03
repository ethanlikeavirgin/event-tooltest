<template>
    <div :class="mergedClasses" v-bind="$attrs">
        <slot></slot>
    </div>
</template>
  
<script>
    export default {
        props: {
            /**
             * Classes to merge with the default container class.
             */
            class: {
            type: String,
            default: '',
            },
        },
        computed: {
            mergedClasses() {
            return `mx-auto max-w-7xl w-full px-6 ${this.class}`.trim();
            },
        },
    };
</script>  