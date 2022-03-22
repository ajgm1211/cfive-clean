<template>
    <div class="dropdown show" align="right" style="margin:20px;">
        <a class="dropdown-toggle" style="font-size:16px" href="#" role="button" id="helpOptions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            See how it works
        </a>

        <div 
            class="dropdown-menu" 
            aria-labelledby="helpOptions"
        >
            <a 
                v-for="(option, key) in options" 
                :key="key"
                class="dropdown-item" 
                target="_blank" 
                :href="option.link"
                style="overflow-x:hidden;"
            > {{ option.title }} 
            </a>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        options: {
            type: Array,
            required: false,
            default: [],
        },
    }
}

</script>