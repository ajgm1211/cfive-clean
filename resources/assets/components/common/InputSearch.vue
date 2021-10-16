<template>
  <div>
    <form action="" class="input-search">
      <div
        role="button"
        class="d-flex justify-content-center align-items-center"
      >
        <Search color="#d3d2d2" />
      </div>
      <input
        @input="filterSearch"
        type="search"
        name=""
        id=""
        v-model="search"
        placeholder="Search..."
      />
    </form>
  </div>
</template>

<script>
import Search from "../Icons/Search.vue";
export default {
  components: { Search },
  data: () => ({
    search: null,
  }),
  computed: {
    filterSearch() {
      this.$emit("filter", this.search);
    },
  },
};
</script>

<style lang="scss" scoped>
.input-search {
  display: grid;
  background-color: #f8f8fb;
  width: 185px;
  height: 30px;
  grid-template-columns: 34px 1fr;
  border-radius: 4px;

  & > input {
    border: none;
    outline: none;
    background-color: #f8f8fb;

    &::placeholder {
      color: #d3d2d2;
      opacity: 1;
    }
  }
}
</style>
