<template>
  <div>
    <label
      v-if="showLabel == true"
      :for="name"
      class="d-block labelv2"
      :class="$v.value.$error ? 'error-msj' : ''"
    >
      {{ label }}
    </label>
    <input
      :disabled="disabled"
      class="input-v2"
      autocomplete="off"
      :class="[
        $v.value.$error ? 'input-err' : '',
        mixed === true ? 'mixedborder' : '',
      ]"
      :placeholder="placeholder"
      :name="name"
      :type="type"
      :rules="rules"
      :value="value"
      @input="handleChange($event.target.value)"
      @blur="$emit('blur')"
    />
    <div v-if="$v.value.$error" class="error-msj-container">
      <span class="error-msj" v-text="messageError" />
    </div>
  </div>
</template>

<script>
import {
  required,
  minLength,
  maxLength,
  alpha,
  alphaNum,
  numeric,
  email,
  minValue,
} from "vuelidate/lib/validators";

export default {
  props: {
    label: {
      type: String,
      default: "Label",
    },
    placeholder: {
      type: String,
      default: "Placeholder",
    },
    type: {
      type: String,
    },
    name: {
      type: String,
      default: "input name",
    },
    radius: {
      type: String,
      default: "8px",
    },
    value: {
      default: null,
    },
    rules: {
      type: Object,
      default: null,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    mixed: {
      type: Boolean,
      default: false,
    },
    showLabel: {
      type: Boolean,
      default: true,
    },
  },
  data: () => ({
    error: true,
  }),
  validations() {
    return {
      value: this.requiredValidations,
    };
  },
  methods: {
    handleChange(_value) {
      if (this.type == "number") {
        this.$emit("input", parseInt(_value));
      } else {
        this.$emit("input", _value);
      }
    },
    validate() {
      this.$v.value.$touch();
      return this.$v.value.$error;
    },
    resetValidate() {
      this.$v.$reset();
    },
  },
  computed: {
    requiredValidations() {
      let validations = {};

      if (this.rules) {
        if (this.rules.required) {
          validations = {
            ...validations,
            required,
          };
        }

        if (this.rules.minLength) {
          validations = {
            ...validations,
            minLength: minLength(this.rules.minLength),
          };
        }

        if (this.rules.maxLength) {
          validations = {
            ...validations,
            maxLength: maxLength(this.rules.maxLength),
          };
        }

        if (this.rules.alpha) {
          validations = {
            ...validations,
            alpha,
          };
        }

        if (this.rules.alphaNum) {
          validations = {
            ...validations,
            alphaNum,
          };
        }

        if (this.rules.minValue) {
          validations = {
            ...validations,
            minValue: minValue(this.rules.minValue),
          };
        }

        if (this.rules.numeric) {
          validations = {
            ...validations,
            numeric,
          };
        }

        if (this.rules.email) {
          validations = {
            ...validations,
            email,
          };
        }
      }

      return validations;
    },
    messageError() {
      if (this.$v.value.required === false) {
        return `The input ${this.name} is required`;
      }
      if (this.$v.value.minLength === false) {
        return `The input ${this.name} must have minimum ${this.$v.value.$params.minLength.min} caracteres`;
      }
      if (this.$v.value.maxLength === false) {
        return `The input ${this.name} must have maximum ${this.$v.value.$params.maxLength.max} caracteres`;
      }
      if (this.$v.value.alpha === false) {
        return `The input ${this.name} must be only characters`;
      }
      if (this.$v.value.alphaNum === false) {
        return `The input ${this.name} must be alphanumeric`;
      }
      if (this.$v.value.minValue === false) {
        return `This input must be grater than ${this.rules.minValue}`;
      }
      if (this.$v.value.numeric === false) {
        return `The input ${this.name} must be only numeric`;
      }
      if (this.$v.value.email === false) {
        return `The input ${this.name} must be a valid email address`;
      }

      return "";
    },
  },
};
</script>

<style lang="scss">
.input-v2 {
  width: 100%;
  height: 33px;
  border: 1px solid #d9d9d9;
  margin-top: 5px;
  background-color: #fff;
  outline: none;
  padding: 2px 10px;
  letter-spacing: 0.05em;
  font-size: 13px;
  border-radius: 8px;

  &::placeholder {
    color: #d3d2d2;
    opacity: 1;
    font-weight: 300;
  }
}

.error-msj {
  color: #ff4c61 !important;
}

.input-err {
  border: 1px solid #ff4c61 !important;
}

.error-msj-container {
  width: 100%;
  text-align: right;

  & > span {
    font-size: 12px;
  }
}

.labelv2 {
  font-size: 14px;
  line-height: 21px;
  letter-spacing: 0.05em;
}

.mixedborder {
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}
</style>
