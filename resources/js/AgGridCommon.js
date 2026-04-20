import { useCommon } from './useCommon.js';

export function createAgGridCommon({
    gridElement,
    rowData = [],
    columnDefinitions = [],
    gridOptions = {},
    gridCustom = {},
    agGrid = window.agGrid,
}) {
    if (!gridElement) {
        throw new Error('createAgGridCommon: "gridElement" is required.');
    }
    if (!agGrid || typeof agGrid.createGrid !== 'function') {
        throw new Error('createAgGridCommon: ag-Grid is not available on window.agGrid.');
    }

    const grid = {
        id: gridElement.id || `ag-grid-${Date.now()}`,
        rowData,
        columnDefs: columnDefinitions,
        gridApi: null,
        custom: gridCustom || {},
    };

    const commonConfig = (gridCustom && typeof gridCustom.config === 'object')
        ? gridCustom.config
        : {};
    const { common } = useCommon(grid, columnDefinitions, commonConfig, gridCustom);

    const resolvedOptions = {
        ...common.gridOptions,
        ...gridOptions,
        rowData: grid.rowData,
        columnDefs: common.getColumnDefinitions(columnDefinitions),
    };

    if (!resolvedOptions.theme && agGrid.themeQuartz && agGrid.colorSchemeLightWarm) {
        resolvedOptions.theme = agGrid.themeQuartz.withPart(agGrid.colorSchemeLightWarm);
    }

    const api = agGrid.createGrid(gridElement, resolvedOptions);
    grid.gridApi = api;

    return { grid, api, common };
}
