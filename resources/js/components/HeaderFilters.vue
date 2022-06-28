<template>
    <div>
        <span v-if="options"
            class="mr-1 btn-filter"
            @click="changeFilterStatus()"
        >
            <b-icon icon="funnel-fill"></b-icon>
        </span>
        {{ field.label }}

        <template v-if="field.isObject">
            <Multiselect
                v-if="field.filterisOpen && show"
                :options="options"
                v-model="filterValues"
                :close-on-select="false"
                :multiple="true"
                :show-labels="false"
                track-by="id"
                label="label"
                @input="filterTable" />
        </template>
        <template v-else>
            <Multiselect
                v-if="field.filterisOpen && show"
                :options="options"
                v-model="filterValues"
                :close-on-select="false"
                :multiple="true"
                :show-labels="false"
                @input="filterTable" />
        </template>
    </div>
</template>
<script>
import Multiselect from "vue-multiselect";
export default {
    props: {
        field: Object,
        isObject: Boolean,
        options: Array
    },
    components: {
        Multiselect,
    },
    data () {
        return {
            filterValues: [],
            show: false
        };
    },
    created() {
        this.initialOptions();
    },
    methods: {
        changeFilterStatus() {
            this.show = !this.show;
        },
        filterTable() {
            this.$emit('onNewFilterValues', this.getFilterObject());
        },
        getFilterObject() {
            return {
                key: this.field.key,
                values: this.getSelectedValues()
            };
        },
        getSelectedValues() {
            if (this.field.isObject) {
                // Array of Ids
                return this.filterValues.map(obj => obj.id);
            } else {
                // Array of Strings
                return this.filterValues;
            }
        },
        initialOptions() {
            console.log(this.field.key);
        }
    }
}
</script>