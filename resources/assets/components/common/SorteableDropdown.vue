<template>
  <div class="dropdown" v-if="itemList.length">
    <label
      v-if="label"
      style="margin-bottom:10px"
      class="d-block labelv2"
      :class="{ 'error-msj': error }"
    >
      {{ label }}
    </label>

    <input
      v-if="selected_item === ''"
      v-model.trim="input_value"
      type="text"
      ref="dropdown_input"
      class="dropdown-input"
      :class="{ 'error-border': error }"
      :placeholder="placeholder"
      @focus="is_open = true"
      @keydown="highlightItem"
    />
    <div
      v-else
      class="dropdown-selected"
      @focus="is_open = true"
      @click="resetSelection"
    >
      {{ selected_item[show_by] ? selected_item[show_by] : selected_item }}
    </div>

    <div v-if="error && !selected_item" class="error-msj-container">
      <span class="error-msj">This is required</span>
    </div>

    <div v-if="is_open" ref="dropdown_list" class="dropdown-list">
      <div
        v-for="(item, index) in filteredItemList"
        :key="index"
        class="dropdown-item"
        :class="{ 'dropdown-item-focused': focused_item_index === index }"
        @click="selectItem(item)"
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
    input_value: '',
    selected_item: '',
    focused_item_index: null,
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
      this.selected_item = this.preselected
    }

    window.addEventListener("click", (e) => {
      if (!this.$el.contains(e.target)) {
        this.is_open = false
      }
    })
  },
  methods: {
    selectItem(item) {
      this.$emit("selected", item)
      this.selected_item = item
      this.input_value = ''
      this.is_open = false
      this.focused_item_index = null
    },
    resetSelection() {
      this.$emit("reset")
      this.selected_item = ''
      this.is_open = true
      this.$nextTick(() => this.$refs.dropdown_input.focus())
    },
    highlightItem(event) {
      const scroll_movement_units = 40
      switch (event.key) {
        case 'ArrowUp':
          if (this.focused_item_index === null) {
            this.focused_item_index = 0
          } else if (this.focused_item_index > 0) {
            this.focused_item_index--
            this.$refs.dropdown_list.scrollTop -= scroll_movement_units
          }
          break
        case 'ArrowDown':
          if (this.focused_item_index === null) {
            this.focused_item_index = 0
          } else if (this.focused_item_index < this.maxIndexDropdown) {
            this.focused_item_index++
            this.$refs.dropdown_list.scrollTop += scroll_movement_units
          }
          break
        case 'Enter':
          this.selectItem(this.filteredItemList[this.focused_item_index])
          break
      }
    }
  },
  computed: {
    filteredItemList() {
      const filtered_item_list = []
      this.itemList.forEach(element => {
        const is_included = element[this.show_by].toLowerCase().includes(this.input_value.toLowerCase())
        if (is_included) {
          filtered_item_list.push(element)
        }
      })
      return filtered_item_list
    },
    maxIndexDropdown() {
      return this.filteredItemList.length - 1
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
.dropdown-item:hover,
.dropdown-item-focused {
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
