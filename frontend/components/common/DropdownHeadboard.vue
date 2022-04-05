<template>
    <div>
        <b-dropdown id="dropdown-left" text="Import">

            <b-dropdown-item v-for="(item, i) in items" 
                :href="item.link" 
                @click="func" 
                :ref="item.ref" 
                :key="i">{{item.label}}
            </b-dropdown-item>

        </b-dropdown>
    </div>
</template>
<script>
    export default {
        props: {
            whitelabel:{
                type: Number,
                default: 0,
            },
            items:{
                type: Array,
                default:[
                    
                ]
            }
        },
        methods:{
            createMasiveEntity(){
                this.$emit('createMasive',true)
            },
            exportEntityShow(){
                this.$emit('exportEntityModalShow')
            },
            AddToWhiteLevel(){
                this.$emit('AddToWhiteLevelModal')    
            }
        }
    }
</script>