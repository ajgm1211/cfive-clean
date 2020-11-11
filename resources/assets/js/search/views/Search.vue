<template>
    <div class="search">

         <b-form ref="form">

            <div class="row justify-content-between mr-0 ml-0">

                <div class="col-4">FCL</div>
                <div class="col-4">
                    <b-button v-b-toggle.collapse-1 class="btn-link-blue">aditional services</b-button>
                </div>

            </div>

            <div class="row mr-0 ml-0">

                <div class="col-12 col-sm-2">import</div>
                <div class="col-12 col-sm-3">
                    <multiselect
                        v-model="valueOrigen"
                        :multiple="true"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :show-labels="false"
                        :options="optionsOrigen"
                        placeholder="From" 
                        class="s-input"
                    >
                    </multiselect>
                </div>
                <div class="col-12 col-sm-3">
                    <multiselect
                        v-model="valueDestination"
                        :multiple="true"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :show-labels="false"
                        :options="optionsDestination"
                        placeholder="To" 
                        class="s-input"
                    >
                    </multiselect>
                </div>
                <div class="col-12 col-sm-2">
                    <date-range-picker
                        :opens="'center'"
                        :locale-data="{
                            firstDay: 1,
                            format: 'yyyy/mm/dd',
                        }"
                        :singleDatePicker="false"
                        :autoApply="true"
                        :timePicker="false"
                        v-model="date"
                        :linkedCalendars="true"
                    >
                    </date-range-picker>
                </div>
                <div class="col-12 col-sm-2">
                    <multiselect
                        v-model="container"
                        :multiple="false"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :show-labels="false"
                        :options="container"
                        placeholder="Select Containers" 
                        class="s-input"
                    >
                    </multiselect>
                </div>

            </div>

            <b-collapse id="collapse-1" class="mt-2">

                <h4>ADDITIONAL SERVICES</h4>
            
                <div class="row mr-0 ml-0">

                <div class="col-12 col-sm-3">
                    <multiselect
                        v-model="valueOrigen"
                        :multiple="true"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :show-labels="false"
                        :options="optionsOrigen"
                        placeholder="From" 
                        class="s-input"
                    >
                    </multiselect>
                </div>
                <div class="col-12 col-sm-3">
                    <multiselect
                        v-model="valueDestination"
                        :multiple="true"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :show-labels="false"
                        :options="optionsDestination"
                        placeholder="To" 
                        class="s-input"
                    >
                    </multiselect>
                </div>
                <div class="col-12 col-sm-3">
                    <multiselect
                        v-model="container"
                        :multiple="false"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :show-labels="false"
                        :options="container"
                        placeholder="Select Containers" 
                        class="s-input"
                    >
                    </multiselect>
                </div>
                <div class="col-12 col-sm-3">
                    <multiselect
                        v-model="container"
                        :multiple="false"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :show-labels="false"
                        :options="container"
                        placeholder="Select Containers" 
                        class="s-input"
                    >
                    </multiselect>
                </div>

            </div>
                
            </b-collapse>

            <div class="row justify-content-center mr-0 ml-0">
                <div class="col-2"><button class="btn-search">SEARCH</button></div>
            </div>

        </b-form>

    </div>
</template>

<script>
import Search from './Search'; 
import Multiselect from "vue-multiselect";
import DateRangePicker from "vue2-daterange-picker";
import "vue-multiselect/dist/vue-multiselect.min.css";

export default {
    components: {
        Search,
        Multiselect,
        DateRangePicker,
    },
    data() {
        return {
            date: '',
            valueOrigen: [],
            valueDestination: [],
            optionsOrigen: [
                { name: 'Vue.js', language: 'JavaScript' },
                { name: 'Adonis', language: 'JavaScript' },
                { name: 'Rails', language: 'Ruby' },
                { name: 'Sinatra', language: 'Ruby' },
                { name: 'Laravel', language: 'PHP' },
                { name: 'Phoenix', language: 'Elixir' }
            ],
            optionsDestination: [
                { name: 'Vue.js', language: 'JavaScript' },
                { name: 'Adonis', language: 'JavaScript' },
                { name: 'Rails', language: 'Ruby' },
                { name: 'Sinatra', language: 'Ruby' },
                { name: 'Laravel', language: 'PHP' },
                { name: 'Phoenix', language: 'Elixir' }
            ],
            container: ['Select option', 'options', 'selected', 'mulitple', 'label', 'searchable', 'clearOnSelect', 'hideSelected', 'maxHeight', 'allowEmpty', 'showLabels', 'onChange', 'touched']
        }
    }
}
</script>