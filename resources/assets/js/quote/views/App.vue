<template>
    <div class="quote-header">
        <div class="container-fluid">
            <div class="row mt-5">
                <div class="col-12">
                    <b-card>
                        <div class="row">
                            <div class="col-6">
                                <b-card-title>FCL Quotes</b-card-title>
                            </div>
                            <div class="col-6">
                                <div class="float-right">
                                    <a
                                        href="/v2/quotes/search"
                                        class="btn btn-primary btn-bg"
                                        ><i class="fa fa-search"></i> Search Rates</a
                                    >
                                </div>
                            </div>
                        </div>

                        <DataTable
                            :fields="fields"
                            :actions="actions.quotes"
                            @onEdit="onEdit"
                        ></DataTable>
                    </b-card>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import Quote from "./Quote";
import Inland from "./Inland";
import Ocean from "./Ocean";
import actions from "../../actions";
import Local from "./Local";
import DataTable from "../../components/DataTable";

export default {
    components: {
        DataTable,
        Quote,
        Inland,
        Ocean,
        Local,
    },
    data() {
        return {
            activeOcean: false,
            actions: actions,
            fields: [
                { key: "quote_id", label: "#" },
                {
                    key: "company_id",
                    label: "Client",
                    formatter: (value) => {
                        return this.setClient(value);
                    },
                },
                { key: "type", label: "Type" },
                {
                    key: "origin",
                    label: "Origin",
                    collapse: "Show origins",
                },
                {
                    key: "destiny",
                    label: "Destiny",
                    collapse: "Show destinations",
                },
                {
                    key: "user_id",
                    label: "User",
                    formatter: (value) => {
                        return value.name;
                    },
                },
                { key: "validity_start", label: "Created at" },
            ],
        };
    },
    created() {},
    methods: {
        onEdit(data) {
            window.location = `/api/quote/${data.id}/edit`;
        },
        setClient(value) {
            if (value == null) {
                return "--";
            } else {
                return value.business_name;
            }
        },
        setPortList(value) {
            let ports = "";

            if (value) {
                value.forEach(function (val) {
                    ports += "<li>" + val + "</li> ";
                });

                return ports;
            } else {
                return "--";
            }
        },
    },
};
</script>
