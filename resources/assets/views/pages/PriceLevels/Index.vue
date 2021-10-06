<template>
  <section>
    <div class="price-container">
      <div class="head">
        <h2>Price levels</h2>

        <MainButton
          @click="create = true"
          text="Add Price Levels"
          :add="true"
        />
      </div>

      <InputSearch @filter="filtered = $event" style="margin-bottom: 20px;" />

      <div class="list-container">
        <ListPrices :prices="prices" />
      </div>

      <p v-if="resultsQty" style="margin-top:20px">
        Total Results: {{ resultsQty }}
      </p>

      <Paginate
        :page-count="resultsQty"
        :prev-text="'Prev'"
        :next-text="'Next'"
        :page-class="'page-item'"
        :page-link-class="'page-link'"
        :container-class="'pagination'"
        :prev-class="'page-item'"
        :prev-link-class="'page-link'"
        :next-class="'page-item'"
        :next-link-class="'page-link'"
        :initialPage="1"
        style="margin-bottom: 0!important;"
      />
    </div>

    <CreateModal v-if="create" @cancel="create = false" />
  </section>
</template>

<script>
import MainButton from "../../../components/common/MainButton.vue";
import InputSearch from "../../../components/common/InputSearch.vue";
import ListPrices from "../../../components/PriceLevel/ListPrices.vue";
import Paginate from "../../../../js/components/paginate.vue";
import CreateModal from "../../../components/PriceLevel/CreateModal.vue";
export default {
  components: { MainButton, InputSearch, ListPrices, Paginate, CreateModal },
  data: () => ({
    resultsQty: 319,
    create: false,
    prices: [
      {
        id: 1,
        type: "LCL",
        name: "example name",
        display_name: "display name example",
        description:
          "Description example could be a very very very but very large description...",
        created_at: "2021-10-01 09:31:23",
        updated_at: "2021-10-01 09:31:23",
      },
      {
        id: 2,
        type: "LCL",
        name: "example name",
        display_name: "display name example",
        description:
          "Description example could be a very very very but very large description...",
        created_at: "2021-10-01 09:31:23",
        updated_at: "2021-10-01 09:31:23",
      },
      {
        id: 3,
        type: "LCL",
        name: "example name",
        display_name: "display name example",
        description:
          "Description example could be a very very very but very large description...",
        created_at: "2021-10-01 09:31:23",
        updated_at: "2021-10-01 09:31:23",
      },
      {
        id: 4,
        type: "LCL",
        name: "example name",
        display_name: "display name example",
        description:
          "Description example could be a very very very but very large description...",
        created_at: "2021-10-01 09:31:23",
        updated_at: "2021-10-01 09:31:23",
      },
      {
        id: 5,
        type: "LCL",
        name: "example name",
        display_name: "display name example",
        description:
          "Description example could be a very very very but very large description...",
        created_at: "2021-10-01 09:31:23",
        updated_at: "2021-10-01 09:31:23",
      },
      {
        id: 6,
        type: "LCL",
        name: "example name",
        display_name: "display name example",
        description:
          "Description example could be a very very very but very large description...",
        created_at: "2021-10-01 09:31:23",
        updated_at: "2021-10-01 09:31:23",
      },
      {
        id: 7,
        type: "LCL",
        name: "example name",
        display_name: "display name example",
        description:
          "Description example could be a very very very but very large description...",
        created_at: "2021-10-01 09:31:23",
        updated_at: "2021-10-01 09:31:23",
      },
      {
        id: 8,
        type: "LCL",
        name: "example name",
        display_name: "display name example",
        description:
          "Description example could be a very very very but very large description...",
        created_at: "2021-10-01 09:31:23",
        updated_at: "2021-10-01 09:31:23",
      },
      {
        id: 9,
        type: "LCL",
        name: "example name",
        display_name: "display name example",
        description:
          "Description example could be a very very very but very large description Description example could be a very very very but very large description.....",
        created_at: "2021-10-01 09:31:23",
        updated_at: "2021-10-01 09:31:23",
      },
      {
        id: 10,
        type: "LCL",
        name: "example name",
        display_name: "display name example",
        description:
          "Description example could be a very very very but very large description...",
        created_at: "2021-10-01 09:31:23",
        updated_at: "2021-10-01 09:31:23",
      },
    ],
  }),
};
</script>

<style lang="scss" scoped>
section {
  width: 100%;
  height: 100%;
  padding: 20px;
}

h2 {
  color: #006bfa;
  font-size: 24px;
  font-weight: 500;
  margin: 0;
}

.head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.price-container {
  background-color: #fff;
  border-radius: 10px;
  width: 100%;
  height: 100%;
  padding: 20px;
}

.list-container {
  padding: 10px 10px 0 0;
  max-height: 415px;
  overflow: auto;
}

/* width */
::-webkit-scrollbar {
  width: 8px;
}

/* Track */
::-webkit-scrollbar-track {
  background: transparent;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #f2f2f2;
  border-radius: 5px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: rgb(184, 184, 184);
}
</style>
