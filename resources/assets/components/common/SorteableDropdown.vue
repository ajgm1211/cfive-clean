<template>
  <div @blur="$emit('blur')" class="dropdown">
    <label
      :class="error === true && !selectedItem ? 'error-msj' : ''"
      v-if="label"
      style="margin-bottom:10px"
      class="d-block labelv2"
    >
      {{ label }}
    </label>

    <input
      v-if="Object.keys(selectedItem).length === 0"
      ref="dropdowninput"
      v-model.trim="inputValue"
      class="dropdown-input"
      type="text"
      :placeholder="placeholder"
      @click="open = !open"
      :class="error === true && !selectedItem ? 'error-border' : ''"
    />
    <div v-else @click="resetSelection()" class="dropdown-selected">
      {{ selectedItem.alphacode ? selectedItem.alphacode : selectedItem }}
    </div>

    <div v-if="error === true && !selectedItem" class="error-msj-container">
      <span class="error-msj">This is required</span>
    </div>

    <div v-if="open" class="dropdown-list">
      <div
        v-show="itemVisible(item)"
        class="dropdown-item"
        v-for="(item, index) in itemList"
        :key="index"
        @click="selectItem(item)"
      >
        {{ item.alphacode ? item.alphacode : item }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: () => ({
    open: false,
    inputValue: "",
    selectedItem: "",
  }),
  props: {
    itemList: {
      type: Array,
    },
    label: {
      type: String,
    },
    error: {
      type: Boolean,
      default: false,
    },
    placeholder: {
      type: String,
      default: "Select an option",
    },
  },
  mounted() {
    // console.log(this.itemList);
    window.addEventListener("click", (e) => {
      if (!this.$el.contains(e.target)) {
        this.open = false;
      }
    });
  },
  methods: {
    itemVisible(item) {
      if (item.alphacode) {
        let currentName = item.alphacode.toLowerCase();
        let currentInput = this.inputValue.toLowerCase();
        return currentName.includes(currentInput);
      } else {
        let currentName = item.toLowerCase();
        let currentInput = this.inputValue.toLowerCase();
        return currentName.includes(currentInput);
      }
    },
    selectItem(theItem) {
      this.selectedItem = theItem;
      this.inputValue = "";
      this.$emit("selected", theItem);
      this.open = false;
    },
    resetSelection() {
      this.selectedItem = "";
      // this.$nextTick(() => this.$refs.dropdowninput.focus());
      this.$emit("reset");
    },
  },
};
</script>

<style>
.dropdown {
  position: relative;
  width: 100%;
  height: 33px;
  max-width: 400px;
  margin: 0 auto;
}
.dropdown-input,
.dropdown-selected {
  border: 1px solid #d9d9d9;
  height: 33px;
  width: 100%;
  padding: 10px 16px;
  background: #fff;
  line-height: 1.5em;
  outline: none;
  border-radius: 8px;
}
.dropdown-input:focus,
.dropdown-selected:hover {
  background: #fff;
  border-color: #e2e8f0;
}
.dropdown-input::placeholder {
  opacity: 0.7;
}
.dropdown-selected {
  font-weight: bold;
  cursor: pointer;
}
.dropdown-list {
  position: absolute;
  width: 100%;
  max-height: 100px;
  margin-top: 4px;
  overflow-y: auto;
  background: #ffffff;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
    0 4px 6px -2px rgba(0, 0, 0, 0.05);
  border-radius: 8px;
}
.dropdown-item {
  display: flex;
  width: 100%;
  padding: 11px 16px;
  cursor: pointer;
}
.dropdown-item:hover {
  background: #edf2f7;
}
.dropdown-item-flag {
  max-width: 24px;
  max-height: 18px;
  margin: auto 12px auto 0px;
}

.error-border{
  border: 1px solid #ff4c61 !important
}
</style>
