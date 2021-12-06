<template>
  <div style="position:relative">
    <label
      v-if="label"
      :for="name"
      style="margin-bottom:10px"
      class="d-block labelv2"
      :class="error === true && !selected ? 'error-msj' : ''"
    >
      {{ label }}
    </label>
    <div
      :class="[
        error === true && !selected ? 'input-err' : '',
        disabled === true ? 'disabled' : '',
        mixed === true ? 'mixedborder' : '',
      ]"
      class="selectable"
      :style="{
        'background-color': background_color,
        'border-color': border_color,
      }"
      @click="open = !open"
    >
      <span v-if="!selected && !value">Select an option</span>
      <span v-if="!selected && value" :style="{ color: font_color }">
        {{ value }}
      </span>
      <span v-else-if="mixed === true" style="color: #fff;">
        {{ selected === "Fixed Markup" ? "$" : "%" }}
      </span>
      <span v-else :style="{ color: font_color }"
        >{{ selected.name ? selected.name : selected }}
      </span>
      <ChevronDown v-if="icon == true" />
    </div>

    <div v-if="error === true && !selected" class="error-msj-container">
      <span class="error-msj">You must select at least one option</span>
    </div>

    <div
      class="options"
      v-if="open"
      :class="mixed == true ? 'mixedOptions' : ''"
    >
      <p v-for="option in options" :key="option" @click="select(option)">
        {{ option.name ? option.name : option }}
      </p>
    </div>
  </div>
</template>

<script>
import ChevronDown from "../Icons/ChevronDown.vue";
export default {
  props: {
    label: {
      type: String,
      default: null,
    },
    name: {
      type: String,
    },
    rules: {
      default: null,
    },
    options: {
      type: Array,
      required: true,
    },
    itemValue: {
      default: "",
    },
    value: {
      default: "",
    },
    font_color: {
      default: "black",
    },
    background_color: {
      type: String,
    },
    border_color: {
      type: String,
    },
    defaultFirstOption: {
      default: null,
      type: Boolean,
    },
    default: {
      default: null,
      type: String,
    },
    error: {
      type: Boolean,
      default: false,
    },
    mixed: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    icon: {
      type: Boolean,
      default: true,
    },
  },
  components: { ChevronDown },
  data: () => ({
    open: false,
    selected: null,
  }),
  created() {
    window.addEventListener("click", (e) => {
      if (!this.$el.contains(e.target)) {
        this.open = false;
      }
    });
  },
  mounted() {
    this.$emit("input", this.selected);

    if (this.defaultFirstOption === true) {
      this.selected = this.options.length > 0 ? this.options[0] : null;
    }
  },
  methods: {
    handleChange(_value) {
      this.$emit("input", _value || null);
    },
    select(option) {
      this.selected = option;
      this.$emit("selected", option);
      this.open = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.selectable {
  width: 100%;
  height: 33px;
  border: 1px solid #d9d9d9;
  border-radius: 8px;
  margin-top: 5px;
  background-color: #fff;
  outline: none;
  padding: 2px 14px;
  letter-spacing: 0.05em;
  font-size: 13px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;

  & > span {
    color: #d3d2d2;
    font-weight: 300;
  }
}

.options {
  background-color: #fff;
  border-radius: 8px;
  position: absolute;
  top: calc(100% + 8px);
  width: 100%;
  padding: 8px 0;
  box-shadow: 0px 1px 5px rgba(188, 184, 184, 0.53);
  & > p {
    padding: 2px 14px;
    width: 100%;
    margin: 0;
    cursor: pointer;
    margin-bottom: 3px;

    &:hover {
      background: #ececec;
    }

    &:last-of-type {
      margin-bottom: 0;
    }
  }
}

.mixedOptions {
  width: 100px;
  left: 40.8px;
  transform: translateX(-100%);
  font-weight: 200;
  font-size: 11px;

  & > p {
    padding: 2px 8px;
  }
}

.disabled {
  pointer-events: none;
}

.mixedborder {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
}
</style>
