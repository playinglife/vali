<!-- DONE -->
<template>
    <div class="ag-floating-filter-input GridTextCellEditor-root">
      <input
        ref="input"
        v-model="value"
        type="text"
        class="ag-input-field-input ag-text-field-input"
      />
    </div>
</template>
  
<script>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { useUtilities } from '@/composables/useUtilities.js'

  export default {
    name: 'GridTextCellEditor',
    props: {
      params: {
        type: Object,
        required: false,
      },
    },
    setup(props) {
      // COMPOSABLES
      const utilities = useUtilities()
      // CONSTANTS
      const value = ref('')
      const lastValue = ref('')
      const regex = ref(new RegExp(props.params.pattern))
      const highlightAllOnFocus = ref(true)

      const input = ref(null)

      const getValue = () => {
        if (value.value === '' && props.params?.emptyStringIsNull === true) {
          return null
        }
        return typeof props.params?.value === 'number'
          ? parseFloat(value.value)
          : value.value
      }

      const setValue = (v) => {
        value.value = v
      }

      const setInitialState = (params) => {
        let startValue
        highlightAllOnFocus.value = true

        if (!params) {
          startValue = ''
        } else if (params.key === 'Backspace' || params.key === 'Delete') {
          // if backspace or delete pressed, we clear the cell
          startValue = ''
        } else if (params.charPress) {
          // if a letter was pressed, we start with the letter
          startValue = params.charPress
          highlightAllOnFocus.value = false
        } else {
          // otherwise we start with the current value
          startValue = params.value == null ? '' : params.value
          if (params.key === 'F2') {
            highlightAllOnFocus.value = false
          }
        }

        value.value = startValue
        lastValue.value = startValue
      }

      // Called when editing ends. If return true then result of edit will be ignored.
      const isCancelAfterEnd = () => {
        return false
      }

      /* const onKeyUp = (event) => {
                if (event.keyCode===13){
                    runOnUpdateCallback();
                } */
      /* if (event.keyCode===8 || event.keyCode===46){
                    lastValue.value = value.value;
                }else{ */
      /* } */

      watch(value, (newValue, oldValue) => {
        const partialMatchRegex = regex.value.toPartialMatchRegex()
        const result = partialMatchRegex.exec(newValue)
        const match = regex.value.exec(input)
        if (match === 'F' || (result && result[0])) {
          lastValue.value = newValue
          nextTick(() => {
            runOnUpdateCallback(oldValue, newValue)
          })
        } else if (newValue === '') {
          lastValue.value = newValue
        } else {
          nextTick(() => {
            value.value = oldValue
          })
        }
      })

      // This method is called when pressing the TAB key when editing
      const activateCellEditor = () => {
        input.value.focus()
      }

      const focusIn = () => input.value?.focus()

      onMounted(() => {
        setInitialState(props.params)
      })
      onUnmounted(() => {})

      const runOnUpdateCallback = (oldValue, newValue) => {
        if (typeof props.params.onUpdate === 'function') {
          const processedValue = props.params.onUpdate(oldValue, newValue)
          if (newValue !== processedValue) {
            if (processedValue.length === 11) {
              props.params.api.dispatchEvent({
                type: 'edit-select',
                data: {
                  value: processedValue,
                  column: props.params.colDef,
                  rowNode: props.params.node,
                },
              })
            }
            value.value = processedValue
            lastValue.value = value.value
          }
        }
      }

      // CODE
      // eslint-disable-next-line no-extend-native
      RegExp.prototype.toPartialMatchRegex = utilities.toPartialMatchRegex

      return {
        value,
        getValue,
        setValue,
        input,
        activateCellEditor,
        focusIn,
        runOnUpdateCallback,
        isCancelAfterEnd,
      }
    },
  }
</script>

<style lang="scss">
    @use '@/assets/scss/variables.scss' as *;

    .GridTextCellEditor-root {
        & > input {
            width: 100%;
            height: 100%;
        }
    }
</style>