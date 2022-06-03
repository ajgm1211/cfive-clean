<template>
  <div @blur="$emit('blur')" class="dropdown" v-if="returnItems.length">
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
      @focus="is_open = true"
      :class="error === true && !selectedItem ? 'error-border' : ''"
    />
    <div
      v-else
      class="dropdown-selected"
      @focus="is_open = true"
      @keydown.delete="resetSelection()"
      tabindex="0"
    >
      {{ selectedItem[show_by] ? selectedItem[show_by] : selectedItem }}
    </div>

    <div v-if="error === true && !selectedItem" class="error-msj-container">
      <span class="error-msj">This is required</span>
    </div>

    <div v-if="is_open" class="dropdown-list">
      <div
        v-show="itemVisible(item)"
        class="dropdown-item"
        v-for="(item, index) in returnItems"
        :key="index"
        @click="selectItem(item)"
        @blur="blur(item)"
      >
        {{ item[show_by] ? item[show_by] : item }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: () => ({
    is_open: false,
    inputValue: "",
    selectedItem: "",
    loading: true,
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
    show_by: {
      type: String,
      default: "alphacode",
    },
    preselected: {
      type: [Object, String, Boolean],
      default() {
        return false;
      },
    },
  },
  mounted() {
    if (this.preselected) {
      this.selectedItem = this.preselected;
    }

    window.addEventListener("click", (e) => {
      if (!this.$el.contains(e.target)) {
        this.is_open = false
      }
    });
  },
  methods: {
    itemVisible(item) {
      if (item[this.show_by]) {
        let currentName = item[this.show_by].toLowerCase();
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
      this.is_open = false
    },
    resetSelection() {
      this.selectedItem = "";
      this.$nextTick(() => this.$refs.dropdowninput.focus());
      this.$emit("reset");
    },
  },
  computed: {
    returnItems() {
      return this.itemList;
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
  z-index: 1000;
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

.error-border {
  border: 1px solid #ff4c61 !important;
}
</style>
