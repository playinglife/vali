<!-- DONE -->
<template>
    <GridBaseCellEditor
        ref="baseRef"
        :focusRef="input"
        :actions="props.params.actions ?? []"
        @keyup="keyPressHandler"
        @popup-mouseup="mouseUpHandler"
        class="GridSelectCellEditor-root"
    >
        <template #trigger="{ opened, toggle }">
            <div class="x-selected-value-container" @click="toggle">
                <div v-if="props.params.colDef.cellEditorParams.showIcon !== false" class="x-selected-value-container-icon">
                    <component :is="formattedIcon" :class="'h-[2em] w-[2em]'" />
                </div>
                <div class="x-selected-value-container-text">
                    <input v-show="!props.params.colDef.cellEditorParams.onlyIcons"
                        type="text"
                        v-model="filter"
                        ref="input"
                        style="font-size: 14px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;"
                        class="truncate"
                    />
                </div>
            </div>
        </template>

        <template #popup="{ popupPosition }">
            <MenuItem v-if="!loading && filteredItemsLength==0" :selected="true" :style="{ minWidth: ((popupPosition.minWidth+'px')) }">
                <span>{{ props.noOptions }}</span>
            </MenuItem>

            <MenuItem v-if="loading && filteredItemsLength==0" :selected="true" :style="{ minWidth: ((popupPosition.minWidth+'px')) }">
                <DataLoading :type=2 :delay="1000"/>
            </MenuItem>

            <div v-if="!loading && filteredItemsLength>0" class="x-menu-items" :style="{ minWidth: ((popupPosition.minWidth+'px')) }">
                <MenuItem
                    ref="itemsRef"
                    v-show = "!item.noSelect"
                    v-for="(item, i) in filteredItems"
                    :key="i"
                    :value="item"
                    :selected="item.id==value"
                    :style=" props.params.onlyIcons ? 'justify-content:center;' : '' "
                    @click="selectOption(item.id)"
                >
                    <div v-if="props.params.colDef.cellEditorParams.showIcon !== false" class="x-menu-item-icon">
                        <component :is="item.icon" :class="[props.layout === 'horizontal' ? 'h-[1.5em] w-[1.5em]' : 'h-[2em] w-[2em]']" v-tippy="{ content: item.tooltip }" />
                    </div>
                    <div v-if="!props.params.onlyIcons && item.name" :class="[props.layout === 'horizontal' ? 'justify-center' : 'justify-start flex-1', 'flex items-center']">
                        <p class="x-menu-item-text">{{ item.name }}</p>
                    </div>
                </MenuItem>
            </div>
        </template>
    </GridBaseCellEditor>
</template>



<script>
    import { ref, watch, computed, nextTick, onBeforeMount, inject } from 'vue';
    import GridBaseCellEditor from '@/components/ag-grid/cellEditors/GridBaseCellEditor.vue';
    import MenuItem from '@/components/common/MenuItem.vue';
    import Dummy from '@/components/common/Dummy.vue';



    export default {
        name: 'GridSelectEditorNeo',
        components: { GridBaseCellEditor },
        props: {
            noOptions: {
            type: String,
            default: 'No Matches'
            }
        },
        setup(props, context) {
            const baseRef = ref(null);
            const value = ref();
            const formattedIcon = ref('');
            const items = ref([]);
            const filter = ref('');
            const filteredItems = computed(() => {
                if (filter.value === '' || (filter.value !== '' && exactValueSelected.value)){
                    return items.value;
                }else{
                    return items.value.filter(item => item.name && item.name.toUpperCase().indexOf(filter.value.toUpperCase())!=-1);
                }
            });
            const filteredItemsLength = computed(()=>{
                return filteredItems.value.length;
            });
            const exactValueSelected = ref(false);
            const newValueSelected = ref(false);
            const loading=ref(false);
            const input = ref();
            const iconInput = ref();
            const itemsRef = ref();


            
            //INJECTIONS
            const axios = inject('axios');



            //VARIABLES

            
            
            
            watch(() => baseRef.value?.opened?.value, (newOpened) => {
                if (newOpened) {
                    nextTick(() => {
                        if (value.value && itemsRef.value?.length) {
                            const list = filteredItems.value;
                            const index = list.findIndex(item => item.id == value.value);
                            if (index >= 0 && itemsRef.value[index]?.$el) {
                                itemsRef.value[index].$el.scrollIntoView({ block: 'nearest', inline: 'nearest' });
                            }
                        }
                    });
                } else {
                    if (value.value === null) {
                        filter.value = '';
                    }
                }
            });
            watch([value, exactValueSelected], ([newValue, newexactValueSelected], [prevValue, prevexactValueSelected]) => {
                if (/*props.params.colDef.cellEditorParams.onlyIcons && */newValue){
                    let elem = (typeof props.params.colDef.cellEditorParams === 'function') ? props.params.colDef.cellEditorParams(props.params).valueList : props.params.colDef.cellEditorParams.valueList;
                    elem = elem.find(item => item.id == newValue);
                    if (elem){
                        filter.value = elem.name;
                        formattedIcon.value = elem && elem.icon ? elem.icon : Dummy;    
                    }else{
                        filter.value = '';
                        formattedIcon.value = Dummy;
                    }
                }
/*                if (!exactValueSelected.value){
                    formattedValue.value = filter.value;
                }else{
                    let elem = (typeof props.params.colDef.cellEditorParams === 'function') ? props.params.colDef.cellEditorParams(props.params).valueList : props.params.colDef.cellEditorParams.valueList;
                    elem = elem.find(item => item.id == newValue);
                    if (elem){
                        formattedValue.value = elem.name;
                    }else{
                        formattedValue.value = '';
                    }
                }*/
            });

            watch(filter, (newFilter) => {
                if (newFilter.length > 0 && !newValueSelected.value){
                    value.value = null
                    exactValueSelected.value=false
                    formattedIcon.value = Dummy
                }
                newValueSelected.value=false
            });


            
            //EVENTS
            onBeforeMount(() => {
                selectOption(props.params.value);
                if (!props.params.colDef.cellEditorParams.loadFromUrl){
                    let tmpItems=[];
                    setTimeout(function(){
                        tmpItems=(typeof props.params.colDef.cellEditorParams === 'function') ? props.params.colDef.cellEditorParams(props.params).valueList : props.params.colDef.cellEditorParams.valueList;
                        items.value=[...(tmpItems.sort((a,b) => b.id==null ? 1 : ( a.id==null ? -1 : ((a.name>b.name) ? 1 : ((b.name > a.name) ? -1 : 0))) )) ];
                        if (tmpItems.some(item => item.icon)){
                            tmpItems.forEach(item => {
                                if (!item.icon){
                                    item.icon = Dummy;
                                }
                            });
                        }
                        if (props.params.default=='first' && !props.params.value && tmpItems.length==1){
                            selectOption(tmpItems[0].id);
                        }
                    },1);
                }else{
                    loadListFromUrl(items);
                }
            });


            
            //METHODS
            const getValue = () => value.value;
            const isPopup = () => false;
            const getPopupPosition = () => 'over';
            const focusIn = () => baseRef.value?.focusIn?.();
            const focusOut = () => baseRef.value?.focusOut?.();
            const afterGuiAttached = () => baseRef.value?.afterGuiAttached?.();            
            const selectOption = (sel) => {
                if (value.value !== sel){
                    newValueSelected.value=true;
                    exactValueSelected.value=true;
                    value.value=sel;
                    baseRef.value?.toggle?.();
                }
            }
            
            const keyPressHandler = (event) => {
                const opened = baseRef.value?.opened?.value ?? false;
                switch (event.keyCode) {
                    case 38:
                        if (opened) {
                            exactValueSelected.value=true;
                            let list=filteredItems.value;
                            let index=list.findIndex(item => item.id==value.value);
                            if (index<0 && list.length>0){
                                index=list.length-1;
                            }else if (index>0){
                                index--;
                            }else{
                                index=list.length-1;
                            }
                            if (index>=0){
                                selectOption(list[index].id);
                                itemsRef.value[index].$el.scrollIntoView({block: "nearest", inline: "nearest"});
                            }
                        }
                        break;
                    case 40:
                        if (opened) {
                            exactValueSelected.value=true;
                            let list=filteredItems.value;
                            let index=list.findIndex(item => item.id==value.value);
                            if (index<0 && list.length>0){
                                index=0;
                            }else if (index<list.length-1){
                                index++;
                            }else{
                                index=0;
                            }
                            if (index>=0){
                                selectOption(list[index].id);
                                itemsRef.value[index].$el.scrollIntoView({block: "nearest", inline: "nearest"});
                            }
                        }
                        break;
                    case 8:
                        if (opened) {
                            if (exactValueSelected.value){
                                exactValueSelected.value=false;
                                filter.value='';
                                value.value=null;
                            }
                            if (filter.value.length>0){
                                value.value=null;
                            }
                        }
                        break;
                    case 13:
                        baseRef.value?.toggle?.();
                        selectOption(value.value);
                        break;                
                    default:
                        /*if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 97 && event.keyCode <= 105) || (event.keyCode >= 65 && event.keyCode <= 90) || event.keyCode==32) {
                            if (event.key.length === 1) {
                                if (exactValueSelected.value){
                                    exactValueSelected.value=false;
                                    filter.value='';
                                }else{
                                    value.value=null;
                                }
                            }
                        }*/
                }

            }
            
            const mouseUpHandler = (event) => {
                input.value.focus();
            }

            const loadListFromUrl = () => {
                loading.value = true;
                let loadConfig = props.params.colDef.cellEditorParams.loadFromUrl;
                axios.get(loadConfig.url)
                    .then(response => {
                        let tmpItems=response.data.models.stockContainers;
                        tmpItems = response.data.models.stockContainers.map(({ id, unit_number }) => ({ id: unit_number, name: unit_number }));
                        items.value=[...(tmpItems.sort((a,b) => b.id==null ? 1 : ( a.id==null ? -1 : ((a.name>b.name) ? 1 : ((b.name > a.name) ? -1 : 0))) )) ];
                        if (props.params.default=='first' && !props.params.value && tmpItems.value.length==1){
                            selectOption(items.value[0]);
                        }else{
                            selectOption(props.params.value);
                        }
                        loading.value = false;
                    })
                    .catch(error => {
                        //
                    });
            }

            return {
                props,
                baseRef,
                input,
                iconInput,
                itemsRef,
                value,
                loading,
                filter,
                filteredItems,
                filteredItemsLength,
                exactValueSelected,
                getValue,
                isPopup,
                getPopupPosition,
                focusIn,
                focusOut,
                afterGuiAttached,
                selectOption,
                keyPressHandler,
                mouseUpHandler,
                formattedIcon,
                loadListFromUrl,
            };

        }
    }
</script>

<style lang="scss">
    @use '@/assets/scss/variables.scss' as *;

    .neo-inputer{
        width: 100%;
        height: 100%;
    }
    .neo-inputer input{
        width: 100%;
        height: 100%;
        padding-left: 5px;
    }
    .neo-selector{
        position: absolute !important;
        z-index: 3;
        display: flex;
        flex-direction: row;
        background-color: transparent;
    }
    .neo-selector .v-list{
        padding: 0;
        max-height: 100%; 
        flex:1; 
        overflow:auto;
        border:1px solid lightgray;
        box-shadow: 0px 2px 1px -1px rgba(0, 0, 0, 0.2), 0px 1px 1px 0px rgba(0, 0, 0, 0.14), 0px 1px 3px 0px rgba(0, 0, 0, 0.12) !important;
    }
    .neo-selector .v-list .v-list-item{
        padding: 12px 5px 12px 5px;
    }
    .neo-selector .v-list .v-list-item .v-input__control .v-field__field{
        padding: 0 !important;
        min-height: auto !important;
    }
    .neo-selector .v-list .v-list-item input{
        font-size: 14px;
        text-align: right;
    }
    .neo-selector .v-field__field{
        padding: 0 !important;
    }
    .neo-selector .v-list-item-selected{
        color: #fff !important;
        background-color: #4E6F8E;
    }

    .GridSelectCellEditor-root {
        .x-selected-value-container {
            display: flex;
            align-items: center;
            padding: $padding-normal;
            gap: $gap-normal;
            width: 100%;
            .x-selected-value-container-icon {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .x-selected-value-container-text {
                display: flex;
                justify-content: start;
                flex: 1;
                align-items: center;
            }
        }
    }

    /* Popup is teleported to body, so selectors must target .neo-selector (the popup container) */
    .neo-selector {
        .x-menu-items {
            //background-color: $color-background-transparent !important;
            //backdrop-filter: blur(10px) saturate(1) !important;
        }

        .x-menu-item-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .x-menu-item-text {
            font-size: $font-size-text;
            font-weight: $font-weight-normal;
            color: $text-color-main;
        }
    }
</style>