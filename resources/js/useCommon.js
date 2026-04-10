import axios from 'axios';
import { useAdvancedAxios } from './useAdvancedAxios.js';
import { useDynamicComponents } from './useDynamicComponents.js';
import { useNotifications } from './useNotification.js';

let uniqueId = 1000;

export function useCommon(grid, columnsDefinitions, config = {}, _store = null, _dayJs = null, gridCustom = {}) {
    //COMPOSABLES
    const nuxtAxios = typeof useNuxtApp === 'function' ? (useNuxtApp().$axios ?? axios) : axios;
    const advancedAxios = useAdvancedAxios(nuxtAxios);
    const dynamicComponents = useDynamicComponents();
    const notifications = useNotifications()




    // Add extra properties to grid
    if (!grid.custom) {
        grid.custom = {
            config: {
                autoOpenCellEditor: true,
            },
        }
    }
    grid.custom.editedRow = null
    grid.custom.firstVisibleColumn = null   // the first column that is being edited when row editing is in action
    grid.custom.isReadyForData = false

    const defaultConfig = { ...grid.custom.config }
    if (gridCustom && typeof gridCustom === 'object') {
        grid.custom = { ...grid.custom, ...gridCustom }
        if (gridCustom.config && typeof gridCustom.config === 'object') {
            grid.custom.config = { ...defaultConfig, ...gridCustom.config }
        }
    }

    /*const myTheme = themeQuartz
        .withPart(colorSchemeDarkBlue)
        .withParams({
            backgroundColor: '#000',
            //foregroundColor: '#fff',
        });*/

    const defaultColDefComputed = computed(() => ({
        resizable: true,
        editable: true,
        sortable: true,
        filter: true,
        floatingFilter: true,
        suppressMenu: true,        //To be set to true, hides filter in header
        filterParams: {
            debounceMs: 500,
            suppressAndOrCondition: true
        },
        floatingFilterComponentParams: {
            suppressFilterButton: true //To be set to true, hides filter button in floating filter
        },
        tooltipComponent: NewGridTooltip,
        tooltipComponentParams: {
            show: 'overflow', // false, true, overflow
        },
        tooltipValueGetter: (params) => params.value ?? '',
        //headerComponent: CustomColumnHeader,
        enableCellChangeFlash: false,
        suppressKeyboardEvent: function (params) {
            let event = params.event;
            let key = event.key;
            let deniedKeys = ['Escape'];
            let suppress = params.editing && deniedKeys.indexOf(key) >= 0;
            if (event.key === 'Enter'/* && params.editing==true*/) {
                suppress = true;
            }
            return suppress;
        },
        cellStyle: {
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'start'
        },
        ...(typeof grid.custom.defaultColDef === 'object' ? grid.custom.defaultColDef : {}),
    }))
    const gridOptions = {
        //theme: myTheme,
        enableCellTextSelection:true,
        ensureDomOrder:true,
        pagination: false,
        rowSelection: {
            mode: 'multiRow',
            checkboxes: false,
            headerCheckbox: false,
            enableSelectionWithoutKeys: false,
            enableClickSelection: true,        
        },
        stopEditingWhenCellsLoseFocus: false,
        suppressAutoSize: false,
        suppressColumnVirtualisation: true,
        skipHeaderOnAutoSize: false,
        suppressClickEdit: true,
        editType: 'fullRow',
        tooltipShowDelay: 500,
        tooltipHideDelay: 9999999,
        enableBrowserTooltips: false,
        noRowsOverlayComponent: NoData,
        getRowId: params => {
            return params.data.uniqueId
            //return params.data.id ? params.data.id : params.data.uniqueId
        },
        rowClassRules: {
            "unvalidated": params => !params.data.valid,
            "highligthed": params => params.data.highlighted,
            "unavailable": params => params.data.unavailable,
        },
        loadingOverlayComponent: Dummy,
        loadingOverlayComponentParams: {
            component: Loading,
            loading: true,
            width: '100%',
            height: '100%',
            delay: 0,
        },
        noRowsOverlayComponent: NoData,
        onSortChanged: (params) => {
            if (!params.api) return
            ensureNewRowsFirstSort(params.api)
        },
        onCellMouseDown: (params) => {},    
        tabToNextCell: (params) => { return tabToNextCell(params) },
        onCellContextMenu: (params) => { onCellContextMenu(params) },
        onGridReady: (params) => { onGridReady(params) },
        onRowDoubleClicked: (params) => { onRowDoubleClicked(params) },
        onCellDoubleClicked: (params) => { onCellDoubleClicked(params) },
        onCellValueChanged: (params) => { onCellValueChanged(params) },
        onSelectionChanged: (params) => { onSelectionChanged(params) },
        onRowClicked: (params) => { onRowClicked(params) },
        onBodyScroll: (params) => { onBodyScroll(params) },
        onBodyScrollEnd: (params) => { onBodyScrollEnd(params) },
        onCellClicked: (params) => { onCellClicked(params) },
        onFirstDataRendered: (params) => { onFirstDataRendered(params) },
        onRowValueChanged: (params) => { onRowValueChanged(params) },
        onRowEditingStarted: (params) => { onRowEditingStarted(params) },

        onDragStarted: (params) => { onDragStarted(params) },
        onDragStopped: (params) => { onDragStopped(params) },
        onRowDragEnter: (params) => { onRowDragEnter(params) },
        onRowDragMove: (params) => { onRowDragMove(params) },
        onRowDragEnd: (params) => { onRowDragEnd(params) },
        onRowDragLeave: (params) => { onRowDragLeave(params) },
        onCellMouseOver: (params) => { onCellMouseOver(params) },
        onCellMouseOut: (params) => { onCellMouseOut(params) },
        onCellEditingStarted: (params) => { onCellEditingStarted(params) },
        defaultColDef: defaultColDefComputed.value,
        rowDragManaged: false,
        suppressMoveWhenRowDragging: false, // smoother UX for tree: highlight until drop, no jumpy repositioning
    }



    //EVENTS
    onMounted(() => {
        runEventIfExist('onMounted')
    })
    onBeforeUnmount(() => {
        document.removeEventListener('mousedown', stopEditingOnClickOutsideEditingRow)
        runEventIfExist('onBeforeUnmount')
        // Destroy grid to prevent "emitsOptions component is null" when AG Grid async ops
        // run after the Vue component has unmounted (e.g. quick navigation away)
        if (grid.gridApi && typeof grid.gridApi.destroy === 'function') {
            try {
                grid.gridApi.destroy()
            } catch (_) { /* grid may already be destroyed */ }
        }
    })



    //METHODS
    const columnDefs = () => {
        return [
            {   
                headerName: 'Valid', field: 'valid',
                hide: true,
            }
        ];
    }
    
    const stopEditingOnClickOutsideEditingRow = (event) => {
        if (!grid.gridApi || !isGridEditing()) return
        // Don't stop if click is inside the editing row (row that contains the inline editor)
        const editingRow = grid.gridRef?.querySelector('.ag-row .ag-cell-inline-editing')?.closest('.ag-row')
        if (editingRow?.contains(event.target)) return
        // Don't stop if click is inside GridBaseCellEditor (e.g. its dropdown popup which is teleported to body)
        if (event.target.closest('.neo-selector') || event.target.closest('.neo-inputer')) return
        // Don't stop if click is inside flatpickr date selector
        if (event.target.closest('.flatpickr-calendar')) return
        stopEditing()
    }
    const lookupName = (params) => {
        let valueList = (typeof params.colDef.cellEditorParams === 'function') ? params.colDef.cellEditorParams(params).valueList : params.colDef.cellEditorParams.valueList;
        let res = valueList.find(item => item.id == params.value);
        return res ? res.name : null;
    };

    const lookupKey = (params) => {
        let valueList = (typeof params.colDef.cellEditorParams === 'function') ? params.colDef.cellEditorParams(params).valueList : params.colDef.cellEditorParams.valueList;
        let res = valueList.find(item => item.name === params.value);
        return res ? res['name'] : null;
    };

    const reloadHandler = (data) => {
        data.rowData = [];
        data.pinnedTopRowData = [];
        reloadGrid();
    };

    const buildTreeChildrenMapWholeGrid = (rowData) => {
        if (!rowData || !Array.isArray(rowData)) return
        const parentIdField = grid.custom.treeParentIdField ?? 'parent_id'
        const idField = grid.custom.treeIdField ?? 'id'
        rowData.forEach((row) => {
            _.set(row, 'custom.tree.children', 
                rowData.filter((r) => r[parentIdField] == row[idField])
                .map((r) => r[idField])
            )
            if (!row.custom.tree.children) row.custom.tree.children = []
            row.custom.tree.expanded = true
            row.custom.tree.depth = getDepth(row, rowData)
        })
    }

    const buildTreeChildrenMapRow = (rowData) => {
        rowData.forEach((row) => {
            ((row.custom ??= {}).tree ??= {}).depth = getDepth(row, grid.rowData)
        })
        return rowData
    }

    const getDepth = (row, rowData, visited = new Set()) => {
        const parentIdField = grid.custom.tree.parentIdField ?? 'parent_id'
        const idField = grid.custom.tree.idField ?? 'id'
        const parentId = row[parentIdField]
        if (parentId == null || parentId === '') return 0
        const rowId = row[idField] ?? row.uniqueId
        if (visited.has(rowId)) return 0
        visited.add(rowId)
        const parent = rowData.find((r) => r[idField] == parentId)
        return parent ? 1 + getDepth(parent, rowData, visited) : 0
    }

    const setupGrid = (response, grid, modelSubmodelMap) => {

        let pointer = null;
        let submodel = null;
        let list = [];
        for (const index in modelSubmodelMap.submodels) {
            if (response.data.submodels[index]) {
                submodel = response.data.submodels[index];
                pointer = grid.columnDefs.find(def => def.field === index);
                if (pointer) {
                    list = [];
                    //When we have an array of objects
                    submodel.forEach(sub => {
                        list.push(prepareListRecord(sub, modelSubmodelMap.submodels[index]));
                    });
                    if (('unselectedOption' in modelSubmodelMap.submodels[index]) && modelSubmodelMap.submodels[index].unselectedOption === true) {
                        list.push({ id: null, name: 'Unspecified' });
                    }
                    pointer.cellEditorParams.valueList = list;
                }
            }
        }
        const responsePointer = Array.isArray(response.data) ? response.data : response.data.models[Object.keys(modelSubmodelMap.model)[0]]
        setDataToGrid(responsePointer);
    };

    const setDataToGrid = (data) => {
        let newList = [];        
        data.forEach(item => {
            newList.push(prepareRecord(item));
            
        });
        if (grid.custom?.tree?.enabled) {
            newList = reorderRowsAsTree(newList);
        }
        if (grid.custom.tree) {
            buildTreeChildrenMapWholeGrid(newList);
        }
        grid.rowData = newList;
    }

    const updateDataToGrid = (data) => {
        let newList = grid.rowData;
        data.forEach(item => {
            const index = newList.findIndex(row => row.id === item.id);
            if (index !== -1) {
                if (item.deleted_at != null) {
                    newList.splice(index, 1);
                } else {
                    newList[index] = prepareRecord(item);
                }
            } else {
                newList.push(prepareRecord(item));
            }
        });
        if (grid.custom?.tree?.enabled) {
            newList = reorderRowsAsTree(newList);
        }
        grid.rowData = newList;
    }

    const reorderRowsAsTree = (rowData) => {
        const reorderedList = []
        const parentIdField = 'parent_id'
        const idField = 'id'

        // Work on a mutable copy to avoid mutating the input
        let remainingRows = [...rowData]

        // Select root nodes (no parent_id), remove them from rowData
        let possibleParentsList = remainingRows.filter(
            (row) => row[parentIdField] == null || row[parentIdField] === ''
        )
        remainingRows = remainingRows.filter(
            (row) => row[parentIdField] != null && row[parentIdField] !== ''
        )

        // Sort roots by name
        possibleParentsList.sort((a, b) => (a.name || '').localeCompare(b.name || ''))

        const processNode = (node) => {
            reorderedList.push(node)

            // Get children of this node
            const children = remainingRows.filter((row) => row[parentIdField] == node[idField])
            remainingRows = remainingRows.filter((row) => row[parentIdField] != node[idField])

            // Sort children by name
            children.sort((a, b) => (a.name || '').localeCompare(b.name || ''))

            // Process each child recursively (they could have their own children)
            for (const child of children) {
                processNode(child)
            }
        }

        // Process each root: add to reorderedList, remove from possibleParentsList, then process children
        while (possibleParentsList.length > 0) {
            const parentRow = possibleParentsList.shift()
            processNode(parentRow)
        }

        return reorderedList
    }

    const reloadGrid = (firstLoad = false, onSuccess, onError, onBeforeSetRowData = null) => {
        /*setTimeout(() => {
            grid.gridApi.setGridOption('loading', true) 
        });*/
        if (grid.custom.config.synchronization && storage.enabled()) {

            if (firstLoad) {
                storage.getData(config.url, (err) => {
                    if (typeof onError === 'function') onError(err)
                    grid.custom.isReadyForData = true
                    grid.gridApi.setGridOption('loading', false)
                }, (data) => {
                    setDataToGrid(data);
                    if (onBeforeSetRowData) onBeforeSetRowData({ data })
                    if (typeof onSuccess === 'function') onSuccess({ data })
                    grid.custom.isReadyForData = true
                    grid.gridApi.setGridOption('loading', false)
                })
            }else{
                storage.synchronizeData(
                    config.url + '/synchronize',
                    (err) => {
                        if (typeof onError === 'function') onError(err)
                        grid.custom.isReadyForData = true
                    },
                    (data) => {
                        updateDataToGrid(data);
                        if (onBeforeSetRowData) onBeforeSetRowData({ data })
                        if (typeof onSuccess === 'function') onSuccess({ data })
                        grid.custom.isReadyForData = true
                    },
                    () => {
                        grid.gridApi.setGridOption('loading', false)
                        grid.custom.isReadyForData = true
                    }
                )
            }

        } else {
            setTimeout(function () {
                grid.gridApi.setGridOption('loading', true)
                axios.get(config.url).then(function (response) {
                    if (onBeforeSetRowData !== null){
                        onBeforeSetRowData(response);
                    }
                    setupGrid(response, grid, grid.custom.modelSubmodelMap);

                    if (typeof onSuccess === 'function') {
                        onSuccess(response);
                    }
                    /*if (gridApi2.value){
                        gridApi2.value.hideOverlay();
                    }*/
                }).catch(function (error) {
                    if (typeof onError === 'function') {
                        onError(error.message);
                    }
                    /*if (gridApi2.value){
                        gridApi2.value.hideOverlay();
                    }*/
                }).finally(() => {
                    grid.custom.isReadyForData = true;
                    grid.gridApi.setGridOption('loading', false)
                });
            }, 1000);
        }
    };

    const stopEditing = () => {
        grid.gridApi.stopEditing();
    };

    const unpinRows = (txt, data, gridApi, store) => {
        let movedRows = [];
        for (let item of data.pinnedTopRowData) {
            if (item['id'] !== '') {
                movedRows.push(item.uniqueId);
                data.rowData.push(item);
            }
        }
        for (let item of movedRows) {
            let delIndex = data.pinnedTopRowData.findIndex(o => o.uniqueId === item.uniqueId);
            if (delIndex){
                data.pinnedTopRowData.splice(delIndex, 1);
            }
        }
        gridApi.setRowData(data.rowData);
        if (data.pinnedTopRowData.length > 0) {
            store.addNotificationMessage('Some ' + txt + ' were not moved to the main list since they are not saved yet.', 'warning');
        }
    };

    const prepareListRecord = (item, sub) => {
        let idString = sub.id ? sub.id : 'id';
        let nameString = sub.name ? sub.name : 'name';

        let theName = null;
        if (Array.isArray(nameString)) {
            theName = getCombinedNames(item, nameString);
        } else {
            theName = item[nameString];
        }
        return { id: item[idString], name: theName };
    };

    const getCombinedNames = (value, path) => {
        let theName = '';
        let miniPath = [];
        path.forEach(pt => {
            miniPath = pt.split('.');
            if (theName !== '') {
                theName += ' - ';
            }
            theName += (miniPath.length === 1 ? value[miniPath] : getDeepAttribute(value, miniPath));
        });
        return theName;
    };

    const getDeepAttribute = (value, path) => {
        if (path.length === 1) {
            return value[path];
        } else {
            return getDeepAttribute(value[path[0]], path.shift() ? path : null);
        }
    };

    const getNodeId = (data) => {
        return data.id;
    };

    const onGridKeyDownHandler = (event) => {
        if (event.key === '+' && event.shiftKey) {
            addRow()
        }

        // ESCAPE
        if (event.key === 'Escape') {
            const gridCustomCancelEditRow = typeof grid.custom.cancelEditRow === 'function' ? grid.custom.cancelEditRow : () => { };
            if (editedRowChanged(grid)) {
                confirmationDialog('Changes have not been saved!<br>Discard changes?', () => {
                    grid.gridApi.stopEditing(true)
                    deleteEditingRow()
                    gridCustomCancelEditRow()
                })
            } else {
                grid.gridApi.stopEditing(true)
                deleteEditingRow()
                gridCustomCancelEditRow()
            }
        }

        // ENTER
        if (event.key === 'Enter') {
            if (event.ctrlKey === true) {
                grid.gridApi.stopEditing();
            } else {
                let focusedCell = grid.gridApi.getFocusedCell();
                if (focusedCell) {
                    grid.gridApi.setFocusedCell(focusedCell.rowIndex, focusedCell.column.colId, focusedCell.rowPinned);
                    grid.gridApi.startEditingCell({
                        rowIndex: focusedCell.rowIndex,
                        colKey: focusedCell.column.colId,
                        rowPinned: focusedCell.rowPinned
                    });
                    if (document.activeElement.firstChild) {
                        document.activeElement.firstChild.focus();
                    }
                }
            }
        }
    }

    const deleteEditingRow = () => {
        if (isGridEditing()) {
            let editingRowIndex = editingCells[0].rowIndex;
            if (grid.rowData[editingRowIndex].id === "") {
                grid.rowData.splice(editingRowIndex, 1);
                grid.gridApi.setRowData(grid.rowData);
            }
            return true;
        }
    }

    const isTopRowEditing = (gridApi, data) => {
        if (isGridEditing()) {
            let editingRowIndex = editingCells[0].rowIndex;
            let editingCellsPinned = editingCells[0].rowPinned;
            return (data.rowData[editingRowIndex].id === "") ? true : false;
        } else {
            return false;
        }
    }

    const isGridEditing = () => {
        let editingCells = grid.gridApi.getEditingCells();
        return (editingCells && editingCells.length > 0)
    }


    //GRID METHODS
    const tabToNextCell = (params) => {
        /*let result = null;

        let nextCellEditable = false;
        if (params.nextCellPosition) {
            //I had to build the params here because there was no other source to get the rowData
            let constructedParams = grid.gridApi.getDisplayedRowAtIndex(params.nextCellPosition.rowIndex);
            nextCellEditable = typeof params.nextCellPosition.column.colDef.editable === 'function' ? params.nextCellPosition.column.colDef.editable(constructedParams) : params.nextCellPosition.column.colDef.editable;
        }

        if (!nextCellEditable) {
            setTimeout(function () { grid.gridApi.tabToNextCell(); }, 1);
            return params.nextCellPosition;
        }

        if (params.nextCellPosition && nextCellEditable) {
            grid.gridApi.setFocusedCell(params.nextCellPosition.rowIndex, params.nextCellPosition.column.colId, params.nextCellPosition.rowPinned);
            let ins = grid.gridApi.getCellEditorInstances({ columns: [params.nextCellPosition.column.colId] });
            if (Array.isArray(ins) && ins.length > 0) {
                ins = ins[0];
                if (ins.activateCellEditor) {
                    ins.activateCellEditor();
                }
            }

            result = params.nextCellPosition;
        }

        return result;*/
    };

    const deactivateAllCellEditors = () => {
        if (grid.gridApi) {
            let ins = grid.gridApi.getCellEditorInstances();
            let max=ins.length;
            for (let i=0;i<max;i++){
                if (ins[i].focusOut){
                    ins[i].focusOut();
                }
            }
        }
    }

    const onCellMouseDown = (params) => {
        params.event.preventDefault();
    }

    const copyValue = (gridApi, data) => {
        let focusedCell = gridApi.getFocusedCell();
        if (focusedCell) {
            let row = gridApi.getDisplayedRowAtIndex(focusedCell.rowIndex);
            let newValue = row.data[focusedCell.column.colId];
            let newRowData = [...data.rowData];
            let max = newRowData.length;
            for (let i = 0; i < max; i++) {
                newRowData[i][focusedCell.column.colId] = newValue;
            }
            data.rowData = newRowData;
        }
    }


    const onCellContextMenu = (params) => {
        const colKey = params.column.getColId();
        const rowIndex = params.rowIndex;
        const rowPinned = params.rowPinned;
        grid.gridApi.startEditingCell({ rowIndex, colKey, rowPinned });
        if (grid.custom.config.autoOpenCellEditor) {
            nextTick(() => {
                const rowNode = rowPinned === 'top'
                    ? grid.gridApi.getPinnedTopRow(rowIndex)
                    : grid.gridApi.getDisplayedRowAtIndex(rowIndex);
                if (rowNode) {
                    const cellEditors = grid.gridApi.getCellEditorInstances({ rowNodes: [rowNode], columns: [colKey] });
                    const editor = cellEditors?.[0];
                    if (typeof editor?.focusIn === 'function') {
                        editor.focusIn();
                    }
                }
            });
        }
        runEventIfExist('onCellContextMenu', params)
    }

    const onCellClicked = (params, store) => {
        if (params.event.ctrlKey==true){
            let text = params.value;
            if (params.column.colDef.valueFormatter){
                let obj = {
                    colDef: params.column.colDef,
                    value: text
                }
                text = params.column.colDef.valueFormatter(obj);
            }
            try {
                navigator.clipboard.writeText(text);
                //store.addNotificationMessage('Value copied', 'info', 'fast');
            } catch (err) {
                //store.addNotificationMessage('Value could not be copied', 'error');
            }
            params.event.preventDefault();
        }
    }

    const onGridReady = (params) => {
        grid.gridApi = params.api
        const { getDefinition } = columnsDefinitions(store, columnDefs(), grid)
        grid.columnDefs = getColumnDefinitions(getDefinition([]))
        nextTick(() => {
            grid.custom.columns = grid.gridApi.getColumns()
            if (grid.gridApi) {
                reloadGrid(true, undefined, undefined, config.onBeforeSetRowData ?? null)
            }
        })


        grid.custom.firstVisibleColumn = getFirstVisibleColumn(grid)
        styleHeaders(grid.columnDefs);

        // Disable context menu and set grid DOM ref
        const gridDom = document.querySelector(`.ag-root-wrapper[grid-id="${grid.gridApi.getGridId()}"]`)
        grid.gridRef = gridDom
        gridDom.addEventListener('contextmenu', (event) => {
            event.preventDefault()
        })
        // Stop editing when user clicks anywhere outside the editing row
        document.addEventListener('mousedown', stopEditingOnClickOutsideEditingRow)
        runEventIfExist('onGridReady', params)
    }

    const onRowDataChanged = (gridApi) => {
        grid.custom.isReadyFordata = true;
    }

    const onCellValueChanged = (params) => {
        // Resize column based on content
        //grid.gridApi.autoSizeColumns([params.column.getColId()], false/*Skip headers*/);
        grid.custom.isReadyFordata = true;
        runEventIfExist('onCellValueChanged', params)
    }

    const onRowEditingStarted = (params) => {
        advancedAxios.cancelRequest(grid.id + (params.data.id ? params.data.id : params.data.uniqueId));
        grid.custom.editedRow = {...toRaw(params.data)};
        runEventIfExist('onRowEditingStarted', params)
    }

    const onRowEditingStopped = (params) => {

        runEventIfExist('onRowEditingStopped', params)
    }

    const onRowDoubleClicked = (params) => {
        //grid.gridApi.startEditingCell({ rowIndex: params.rowIndex, colKey: params.column.getColId() });
        runEventIfExist('onRowDoubleClicked', params)
        /*if (params.rowPinned=='top' && params.data.id==''){
            gridApi.startEditingCell({ rowIndex: params.rowIndex, colKey: grid.custom.firstVisibleColumn, rowPinned: 'top' });
        }*/
    }

    const onSelectionChanged = (params) => {
        runEventIfExist('onSelectionChanged', params)
    }

    const prepareRecord = (item) => {
        //If uniqueId is alrady set then don't change it
        return {
            ...(!item.uniqueId ? 
                    { uniqueId: uniqueId++, new: (item.new ? item.new : 0) } : 
                    { uniqueId: item.uniqueId, new: (item.new ? item.new : 0) }
                ),
            valid: true,
            ...grid.custom.prepareRecord(item)
        }
    }

    const styleHeaders = (columnDefs) => {
        for (let item of columnDefs) {
            if (item.custom && item.custom.required) {
                item.headerClass = 'ag-required-header';
            }
        }
    }
    const ensureNewRowsFirstSort = (api) => {
        const colState = api.getColumnState()
        const newCol = colState.find((c) => c.colId === 'new')
        if (!newCol) return
        if (newCol.sort === 'desc' && newCol.sortIndex === 0) return
        const newState = colState.map((c) => {
            const base = { ...c }
            if (c.colId === 'new') {
                base.sort = 'desc'
                base.sortIndex = 0
            } else if (c.sort != null) {
                base.sortIndex = (c.sortIndex ?? 0) + 1
            }
            return base
        })
        api.applyColumnState({ state: newState })
    }

    const onSortChanged = (e, grid) => {
        runEventIfExist('onSortChanged', e)
    }

    const runEventIfExist = (functionName, params) => {
        if (typeof grid.custom[functionName] === 'function'){
            grid.custom[functionName](params)
        }
    }


    //COMPUTED
    const allowReload = (data, gridApi) => {
        let allow = false;
        if (grid.custom.isReadyFordata === true && gridApi) {
            allow = true;
        }
        return allow;
    }
    const allowAdd = (data, gridApi) => {
        let allow = false;
        if (grid.custom.isReadyFordata === true && gridApi) {
            allow = true;
        }
        return allow;
    }
    const allowEdit = (data, gridApi) => {
        let allow = false;
        if (grid.custom.isReadyFordata === true && gridApi && gridApi.getSelectedNodes().length == 1) {
            allow = true;
        }
        return allow;
    }
    const allowDelete = (data, gridApi) => {
        let allow = false;
        if (grid.custom.isReadyFordata === true && gridApi) {
            if (gridApi.getSelectedNodes().length > 0) {
                allow = true;
            }
            /*if (isTopRowEditing(gridApi, data, state)) {
                allow = true;
            }*/
        }
        return allow;
    }

    const getFirstVisibleColumn = (grid) => {
        for (const item of grid.columnDefs) {
            if (!item.hide && item.editable !== false) {
                return item.field;
            }
        }
        return null;
    }

    const onExportCsv = () => {
        const customParams = grid.gridApi;
        let processHeaderCallback = (params) => false;
        let processCellCallback = (cell) => false;
        if (grid.custom.onExportCsv) {
            const result = grid.custom.onExportCsv();
            if (result) {
                if (typeof result.processHeaderCallback === 'function') {
                    processHeaderCallback = result.processHeaderCallback;
                }
                if (typeof result.processCellCallback === 'function') {
                    processCellCallback = result.processCellCallback;
                }
            }
        }
        let params = {
            fileName: 'export.csv',
            columnSeparator: ';',
            processCellCallback: function (cell) {
                let returnValue = cell.value;
                let extraReturnValue = processCellCallback(cell);
                if (extraReturnValue !== false) {
                    returnValue = extraReturnValue;
                }
                if (extraReturnValue === false && cell.column.colDef.valueFormatter) {
                    returnValue = cell.column.colDef.valueFormatter({
                        value: cell.value,
                        data: cell.node.data,
                        colDef: cell.column.colDef
                    });
                }
                return returnValue;
            }
        };

        grid.gridApi.exportDataAsCsv({ ...params, ...customParams });
    }

    const updateChangesFromBackendOnUpdatingRows = (rows, gridApi, data) =>{
        let found;
        let model;
        let rowNode;
        if (!Array.isArray(rows)){
            rows = [rows];
        }
        rows.map(item => {
            found = false;
            model=prepareRecord(item);

            data['rowData'].find((o,i) => {
                //We only check for uniqueId because the id can be changed in the backend
                //if ((o.id!=='' && o.id === item.id) || (o.id==='' && o.uniqueId === item.uniqueId)){
                if (o.uniqueId === item.uniqueId){
                    rowNode = gridApi.getRowNode(o.uniqueId);
                    found = true;
                    model.valid = true;
                    data['rowData'][i] = {...data['rowData'][i], ...model};
                    rowNode.setData(data['rowData'][i]);
                    return true;
                }
            });
            //params.node.setData(model);
            //params.data.valid=true;
            if (!found){
                data['pinnedTopRowData'].forEach((element, index) => {
                    rowNode = gridApi.getPinnedTopRow(index);
                    //if ((rowNode.data.id!=='' && rowNode.data.id === item.id) || (rowNode.data.id==='' && rowNode.data.uniqueId === item.uniqueId)){
                        if (rowNode.data.uniqueId === item.uniqueId){
                        found = true;
                        model.valid = true;
                        data['pinnedTopRowData'][index] =  {...data['pinnedTopRowData'][index], ...model};
                        rowNode.setData(data['pinnedTopRowData'][index]);
                    }
                });
            }
        });

    }

    const editedRowChanged = (grid) => {
        const currentData = getCurrentEditedRowValues(grid)        
        return !(_.isEqual(toRaw(grid.custom.editedRow), toRaw(currentData)));
    }

    const onFilterChanged = (gridApi) => {
        gridApi.stopEditing();
    }


    // Method that brings the grid in a state where only selecting a row is possible, nothing else
    const lockGrid = (value) => {
        gridOptions.suppressMovableColumns = value
        gridOptions.suppressSorting = value
        gridOptions.suppressRowDrag = value
        gridOptions.suppressClickEdit = value
    }

    const getColumnDefinitions = (definition) => {
        const defaultIdCol = {
            headerName: 'ID',
            field: 'id',
            editable: false,
            filter: false,
            sortable: true,
            initialWidth: 100,
            cellDataType: 'number',
            valueFormatter: (params) => (params.value == null || params.value === '') ? '' : params.value,
            cellRenderer: (params) => (params.value == null || params.value === '') ? 'NO ID' : params.value,
        }
        const idOverride = definition.find(def => def.field === 'id')
        const mergedIdCol = idOverride ? { ...defaultIdCol, ...idOverride } : defaultIdCol
        const definitionWithoutId = definition.filter(def => def.field !== 'id')
        return [
          ...[mergedIdCol],
          ...definitionWithoutId,
          ...[
            {
                headerName: '',
                field: '_filler',
                editable: false,
                filter: false,
                sortable: false,
                resizable: false,
                suppressSizeToFit: true,
                flex: 1,
                minWidth: 50,
                custom: { toggleHide: false },
              },
          ],
        ]
      }


    const onRowDataUpdated = (params) => {
        if (grid.custom.tree && grid.rowData) {
            buildTreeChildrenMapWholeGrid(grid.rowData)
        }
        runEventIfExist('onRowDataUpdated', params)
    }

    const onCellDoubleClicked = (params) => {
        // Start editing the row by the current cell
        /*grid.gridApi.startEditingCell({ rowIndex: params.rowIndex, colKey: params.column.getColId() });
        runEventIfExist('onCellDoubleClicked', params)*/
    }

    const onRowClicked = (params) => {
        runEventIfExist('onRowClicked', params)
    }

    const onBodyScroll = () => {
        runEventIfExist('onBodyScroll')
    }

    const onBodyScrollEnd = (params) => {
        //if (event.direction === 'horizontal') {
        deactivateAllCellEditors()
        runEventIfExist('onBodyScrollEnd', params)
    }

    const onFirstDataRendered = (params) => {
        nextTick(() => {
            // Select rows based on query params (route from useRoute())
            let selectedRows = route.query.selected ?? route.query.roleId
            if (selectedRows != null && !Array.isArray(selectedRows)) {
                selectedRows = [selectedRows]
            }
            if (selectedRows && grid.rowData) {
                selectedRows.forEach(id => {
                    const row = grid.rowData.find(row => String(row.id) === String(id))
                    if (row) {
                        const node = grid.gridApi.getRowNode(row.uniqueId)
                        if (node) node.setSelected(true)
                    }
                })
            }
        })

        runEventIfExist('onFirstDataRendered', params)
    }

    const onRowValueChanged = (params) => {
        if (grid.custom.editedRow && _.isEqual(toRaw(grid.custom.editedRow), toRaw(params.data))) {
            return
        }
        // display differences
        const url = params.data.id == '' ? config.url : config.url + '/' + params.data.id;
        advancedAxios.sendRequest(
            'Common-UpdateOrCreateRow' + params.data.id,
            {
                method: params.data.id == '' ? 'post' : 'put',
                url: url,
                data: params.data,
            }
        ).then(response => {
            if (response.status === 200 || response.status === 201) {
                params.data.valid = true;
                params.api.redrawRows({ rowNodes: [params.node] });
                // Notification values saved
                updateRow(params, response.data)
            }
        }).catch(error => {
            if (error.message !== 'canceled') {
                params.data.valid = false;
                params.api.redrawRows({ rowNodes: [params.node] });
            }
        })

        runEventIfExist('onRowValueChanged', params)
    }

    const confirmationDialog = (text, yes) => {
        const s = ref(false)
        let dialog
        dialog = dynamicComponents.createComponent('dialog', {
          title: '',
          content: text,
          show: s,
          buttons: [
            {
              label: 'NO',
              color: 'terniary',
              action: () => {
                dialog.destroy()
              },
            },
            {
              label: 'YES',
              color: 'primary',
              action: () => {
                dialog.destroy()
                yes()
              },
            },
          ],
        })
        s.value = true
    }

    const getCurrentEditedRowValues = (grid, onlyEditingCells = false) => {
        if (isGridEditing()) {
          const editingRowIndex = editingCells[0].rowIndex
          const rowNode =
              grid.gridApi.getDisplayedRowAtIndex(editingRowIndex)
          const rowData = rowNode.data
          const editedData = {}
    
          grid.gridApi.getColumns().forEach((col) => {
            const field = col.colDef.field
            const cellEditor = grid.gridApi.getCellEditorInstances({
              rowNodes: [rowNode],
              columns: [col],
            })[0]
            if (cellEditor) {
              editedData[field] = cellEditor.getValue()
            }
          })
          return {
            ...(onlyEditingCells ? {} : toRaw(grid.custom.editedRow)),
            ...editedData,
          }
        } else {
          return null
        }
    }
    
    const setCurrentEditedRowValues = (grid, rowIndex, data) => {
        for (const column in data) {
          const cellEditors = componentGrid.gridApi.getCellEditorInstances({
            rowIndex,
            columns: [column],
          })
          if (cellEditors && cellEditors[0] && cellEditors[0].setValue) {
            cellEditors[0].setValue(data[column])
          }
        }
    }

    const addRow = () => {
        const newRecord = prepareRecord({
            new: 1,
        })
        const selectedRows = grid.gridApi.getSelectedNodes()
        if (grid.custom.tree && selectedRows.length === 1 && grid.custom.tree.addRowToParent) {
            const selectedNode = selectedRows[0]
            if (selectedNode) {
                newRecord.parent_id = selectedNode.data.id
                // insert new record after the selected row
                const selectedRowIndex = grid.rowData.findIndex((row) => row.uniqueId === selectedNode.data.uniqueId)
                if (selectedRowIndex !== -1) {
                    grid.rowData.splice(selectedRowIndex + 1, 0, newRecord)
                    nextTick(() => {
                        // update the depth, children and expanded for all rows (including the new one)
                        buildTreeChildrenMapWholeGrid(grid.rowData)
                        grid.rowData = reorderRowsAsTree(grid.rowData)

                        const node = grid.gridApi?.getRowNode(newRecord.uniqueId)
                        if (node) {
                            node.data = grid.rowData.find((r) => r.uniqueId === newRecord.uniqueId)
                            grid.gridApi.redrawRows({ rowNodes: [node] })
                        }
                    })
                }
            }
        }else{
            grid.rowData.unshift(newRecord)
        }
        nextTick(() => {
            setTimeout(() => {
                const node = grid.gridApi.getRowNode(newRecord.uniqueId)
                if (node && grid.custom.firstVisibleColumn) {
                    grid.gridApi.setFocusedCell(node.rowIndex, grid.custom.firstVisibleColumn)
                    grid.gridApi.startEditingCell({
                        rowIndex: node.rowIndex,
                        colKey: grid.custom.firstVisibleColumn,
                    })
                }
            }, 100)
        })          
    }

    const updateRow = (draggedRow, data) => {
        const draggedRowUniqueId = draggedRow.data.uniqueId
        const index = grid.rowData.findIndex((o) => o.uniqueId === draggedRowUniqueId)
        if (index === -1) return
        grid.rowData[index] = prepareRecord({ ...data, uniqueId: draggedRowUniqueId })
        if (grid.custom.tree) {
            buildTreeChildrenMapWholeGrid(grid.rowData)
            grid.rowData = reorderRowsAsTree(grid.rowData)

            // Next 5 lines are needed in order for the dragged row to update correctly it's depth/indentation
            const node = grid.gridApi?.getRowNode(draggedRowUniqueId)
            const updatedRow = grid.rowData.find((r) => r.uniqueId === draggedRowUniqueId)
            if (node && updatedRow) {
                node.data = updatedRow
                grid.gridApi.redrawRows({ rowNodes: [node] })
            }
        }
    }

    const deleteRows = (ids) => {
        confirmationDialog('Are you sure you want to delete these records?', () => {
            if (!Array.isArray(ids)) {
                ids = [ids]
            }
            if (ids.length === 0) {
                notifications.showQuick('warning', 'Nothing selected')
                return
            }
            const url = config.url + '/' + ids.join(',');
            advancedAxios.sendRequest(
                'Common-DeleteRecords',
                {
                    method: 'delete',
                    url: url,
                    data: ids,
                }
            ).then(response => {
                if (response.status === 200 || response.status === 201) {
                    grid.rowData = grid.rowData.filter(row => !ids.includes(row.id))
                    storage.deleteData(config.url, row.id)
                    //grid.gridApi.setRowData(grid.rowData.filter(row => !ids.includes(row.id)));
                }
            }).catch(error => {
                if (error.message !== 'canceled') {
                }
            })

            runEventIfExist('onDeleteRows', ids)
        })
    }

    const setChildrenHeightRecursively = (toggle, childrenIds = [], parentExpanded, switchToggle = false) => {
        childrenIds.forEach((childId) => {
            const row = grid.rowData.find((row) => row.id === childId)
            const node = grid.gridApi.getRowNode(row.uniqueId)
            if (switchToggle) {
                row.custom.tree.expanded = toggle
            }
            if (toggle) {
                if (parentExpanded) {
                    node.setRowHeight(null)
                }
            } else {
                node.setRowHeight(0)
            }
            if (node.data.custom?.tree?.children?.length > 0) {
                setChildrenHeightRecursively(toggle, node.data.custom.tree.children, node.data.custom.tree.expanded, switchToggle)
            }
        })
    }

    const expandRows = (toggle, id = null) => {
        const switchToggle = id === null
        const idsToProcess = id === null ? grid.rowData.filter((row) => row.parent_id === null).map((row) => row.id) : [id]
        idsToProcess.forEach((id) => {
            const row = grid.rowData.find((row) => row.id === id)
            const node = grid.gridApi.getRowNode(row.uniqueId)
            row.custom.tree.expanded = toggle
            setChildrenHeightRecursively(toggle, node.data.custom.tree.children, toggle, switchToggle)
        })
    }

    // Dragging
    const onDragStarted = (params) => {
        runEventIfExist('onDragStarted', params)
    }

    const onDragStopped = (params) => {
        onDragOperationEnded()
        runEventIfExist('onDragStopped', params)
    }
    

    const markDescendentsAsUnavailable = (rowId) => {
        const row = grid.rowData.find(r => r.id === rowId)
        row.custom.tree.children.forEach(childId => {
            const child = grid.rowData.find(r => r.id === childId)
            child.unavailable = true
            markDescendentsAsUnavailable(childId)
        })
    }

    const onRowDragEnter = async (params) => {
        const draggedNode = params.node
        // Mark all available
        grid.rowData.forEach((row, index) => {
            grid.rowData[index].unavailable = false
        })
        // Mark the dragged node as unavailable
        let row = grid.rowData.find(r => r.id === draggedNode.data.id)
        if (row) {
            row.unavailable = true
        }
        // Mark the dragged node's descendents as unavailable
        markDescendentsAsUnavailable(draggedNode.data.id)
        // Mark the dragged node's parent as unavailable
        row = grid.rowData.find(r => r.id === draggedNode.data.parent_id)
        if (row) {
            row.unavailable = true
        }
        // Redraw the grid
        grid.gridApi.redrawRows()
        runEventIfExist('onRowDragEnter', params)
    }

    const lastDragOverNodeId = ref(null)
    const onRowDragMove = (params) => {
        let toRedraw = []
        const dragOverNode = params.overNode
        if (dragOverNode && (dragOverNode.data.unavailable === false) && (lastDragOverNodeId.value === null || (lastDragOverNodeId.value !== null && lastDragOverNodeId.value !== dragOverNode.data.uniqueId))) {
            if (lastDragOverNodeId.value !== null) {
                const lastDragOverNode = grid.gridApi.getRowNode(lastDragOverNodeId.value)
                if (lastDragOverNode) {
                    lastDragOverNode.data.highlighted = false
                    toRedraw.push(lastDragOverNode)
                }
            }
            
            if (dragOverNode.data.unavailable === true) {
                return
            }else{
                dragOverNode.data.highlighted = true
                toRedraw.push(dragOverNode)
            }
            lastDragOverNodeId.value = dragOverNode.data.uniqueId
            if (toRedraw.length) grid.gridApi.redrawRows({ rowNodes: toRedraw })
            runEventIfExist('onRowDragMove', params)
        }
    }
    
    const onRowDragEnd = (params) => {
        const dropTargetRow = params.overNode
        if (dropTargetRow && dropTargetRow.data.unavailable === false) {
            runEventIfExist('onRowDragEnd', params)
        }
        onDragOperationEnded()
    }

    const onRowDragLeave = (params) => {
        const prevNode = grid.custom.dragOverNode
        grid.custom.dragOverNode = null
        const prevDragging = grid.custom.draggingRows
        grid.custom.draggingRows = null
        if (grid.gridApi) {
            const toRedraw = [...(prevNode ? [prevNode] : []), ...(prevDragging ?? [])].filter(Boolean)
            if (toRedraw.length) grid.gridApi.redrawRows({ rowNodes: toRedraw })
        }
        runEventIfExist('onRowDragLeave', params)
    }


    const onDragOperationEnded = () => {
        lastDragOverNodeId.value = null
        grid.rowData.forEach((_, index) => {
            grid.rowData[index].unavailable = false
            grid.rowData[index].highlighted = false
        })
        grid.gridApi.redrawRows()
    }

    const onCellMouseOver = (params) => {
        runEventIfExist('onCellMouseOver', params)
    }

    const onCellMouseOut = (params) => {
        runEventIfExist('onCellMouseOut', params)
    }


    const onCellEditingStarted = (params) => {
        runEventIfExist('onCellEditingStarted', params)
    }
    
    // Because we already populate the grid.custom property in usecommon, we need to merge the current grid.custom with the custom parameter
    const setCustom = (custom) => {
        grid.custom = { ...grid.custom, ...custom }
    }

    const common = {
            //DATA
            gridOptions,
            defaultColDefComputed,
            //instance: readonly(instance),
            //METHODS
            lookupName,
            lookupKey,
            reloadHandler,
            setupGrid,
            reloadGrid,
            stopEditing,
            unpinRows,
            prepareListRecord,
            getCombinedNames,
            getDeepAttribute,
            copyValue,
            getNodeId,
            onGridKeyDownHandler,
            getColumnDefinitions,
            deleteRows,
            expandRows,
            setCustom,
            lockGrid,
            updateRow,
            reorderRowsAsTree,
            markDescendentsAsUnavailable,
            //GRID METHODS
            tabToNextCell,
            deactivateAllCellEditors,
            onCellMouseDown,
            onGridReady,
            onCellDoubleClicked,
            onRowDataChanged,
            onCellValueChanged,
            onRowEditingStarted,
            onRowEditingStopped,
            onRowDoubleClicked,
            prepareRecord,
            deleteEditingRow,
            isTopRowEditing,
            styleHeaders,
            onRowDataUpdated,
            getCurrentEditedRowValues,
            setCurrentEditedRowValues,
            onRowClicked,
            onFirstDataRendered,
            addRow,
            //COMPUTED
            allowAdd,
            allowDelete,
            allowEdit,
            getFirstVisibleColumn,
            onExportCsv,
            updateChangesFromBackendOnUpdatingRows,
            onFilterChanged,
            onSortChanged,
            onCellClicked,
            columnDefs,
            editedRowChanged,
            confirmationDialog,
    }

    grid.custom.common = common
    return { common }
}