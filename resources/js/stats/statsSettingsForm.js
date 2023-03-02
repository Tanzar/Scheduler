/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        var statsTable = initStatsTable(language);
        var select = new SelectFile(language);
        var colsTable = initColumnsTable(language);
        var rowsTable = initRowsTable(language);
        var inputs = new Inputs(language);
        var cellsTable = initCellsTable(language, inputs);
        inputs.setCellsTable(cellsTable);
        
        $('#savePattern').click(function(){
            saveStatsWindow(language, statsTable, select, colsTable, rowsTable, inputs, cellsTable);
        });
        
        statsTable.setOnSelect(function(selected){
            console.log(selected);
            if(selected !== undefined){
                select.setSelected(selected.json.file);
                document.getElementById('statsName').value = selected.name;
                colsTable.setData(selected.json.cols);
                rowsTable.setData(selected.json.rows);
                if(selected.json.cells !== undefined){
                    cellsTable.setData(selected.json.cells);
                }
                else{
                    cellsTable.setData([]);
                }
                inputs.overrideData(selected);
            }
        });
        
        statsTable.setOnUnselect(function(selected){
            console.log(selected);
            if(selected !== undefined){
                select.setSelected('');
                document.getElementById('statsName').value = '';
                colsTable.setData([]);
                rowsTable.setData([]);
                inputs.clear();
                cellsTable.setData([]);
            }
        });
    });
}


function initStatsTable(language) {
    var div = document.getElementById('stats');
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'StatsSettings',
            task: 'getStatsUsingForm'
        }
    };
    var config = {
        columns: [
            {title: language.name, variable: 'name', width: 150, minWidth: 150}
        ],
        dataSource: datasource
    }
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.remove, function(selected){
        if(selected !== undefined){
            RestApi.post('StatsSettings', 'removeStats', {id: selected.id},
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_stats);
        }
    });
    return datatable;
}

function SelectFile(language) {
    var select = document.getElementById('selectPatternFile');
    
    var me = this;
    $('#uploadPattern').click(function(){
        var input = document.getElementById('uploadPatternFile');
        RestApi.upload('StatsSettings', 'uploadTemplate', input,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    me.reload();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            })
    });
    
    this.getFilename = function(){
        return select.value;
    }
    
    this.reload = function(){
        RestApi.post('StatsSettings', 'getTemplatesList', {}, 
            function(response){
                var data = JSON.parse(response);
                console.log(data);
                while(select.lastChild){
                    select.removeChild(select.lastChild);
                }
                var placeholder = document.createElement('option');
                placeholder.placeholder = true;
                placeholder.selected = true;
                placeholder.disabled = true;
                placeholder.textContent = language.select_pattern_file;
                select.appendChild(placeholder);
                data.forEach(item => {
                    var option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;
                    select.appendChild(option);
                });
            },
            function(response){
                console.log(response.responseText);
                alert(response.responseText);
            });
    }
    
    this.setSelected = function(filename){
        select.value = filename;
    }
}

function initColumnsTable(language) {
    var div = document.getElementById('columns');
    var config = {
        columns: [
            {title: language.column, variable: 'number', width: 50, minWidth: 50},
            {title: language.dataset, variable: 'dataset', width: 50, minWidth: 100},
            {title: language.groups, variable: 'groupset', width: 50, minWidth: 100}
        ],
        data: []
    }
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
       addColumnFirstWindow(language, datatable);
    });
    datatable.addActionButton(language.remove, function(selected){
        if(selected !== undefined){
            var index = datatable.getSelectedIndex();
            var tableData = datatable.getData();
            tableData.splice(index, 1);
             datatable.setData(tableData);
        }
    });
    return datatable;
}

function addColumnFirstWindow(language, datatable){
    var fields = [
        {type: 'number', title: language.column, variable: 'number', min: 0, required: true},
        {type: 'checkbox', title: language.select_dataset + '?', variable: 'dataset'}
    ];
    openModalBox(language.new_column, fields, language.next, function(column){
        addColumnSecondWindow(language, datatable, column);
    });
}

function addColumnSecondWindow(language, datatable, column){
    if(column.dataset === 0){
        RestApi.post('StatsSettings', 'getAllGroups', {}, 
            function(response){
                var data = JSON.parse(response);
                var groups = [];
                data.forEach(item => {
                    var option = {
                        title: item,
                        value: item
                    }
                    groups.push(option);
                });
                addColumnGroupAsDatasetWindow(language, datatable, column, groups);
            },
            function(response){
                console.log(response.responseText);
                alert(response.responseText);
            });
    }
    else{
        RestApi.get('StatsSettings', 'getStatsStageOne', {}, function(response){
            var data = JSON.parse(response);
            var datasets = data.datasets;
            addColumnDatasetAndMethodWindows(language, datatable, column, datasets);
        },
        function(response){
            console.log(response.responseText);
            alert(response.responseText);
        });
    }
}

function addColumnGroupAsDatasetWindow(language, datatable, column, groups){
    var fields = [
        {type: 'select', title: language.groups, variable: 'group', options: groups, required: true}
    ];
    openModalBox(language.new_column, fields, language.next, function(groupData){
        console.log(groupData);
        RestApi.post('StatsSettings', 'getGroupOptions', {group: groupData.group}, 
            function(response){
                var data = JSON.parse(response);
                var keys = Object.keys(data[0]);
                var options =  [];
                keys.forEach(key => {
                    var option = {
                        title: key,
                        value: key
                    }
                    options.push(option);
                });
                var fields = [
                    {type: 'select', title: language.groups, variable: 'key', options: options, required: true}
                ];
                openModalBox(language.new_column, fields, language.save, function(groupKeyData){
                    var result = {
                        number: column.number,
                        groupset: groupData.group,
                        groupsetKey: groupKeyData.key
                    }
                    var tableData = datatable.getData();
                    tableData.push(result);
                    datatable.setData(tableData);
                });
            },
            function(response){
                console.log(response.responseText);
                alert(response.responseText);
            });
        });
}

function addColumnDatasetAndMethodWindows(language, datatable, column, datasets){
    var fields = [
        {type: 'select', title: language.select_dataset, variable: 'dataset', options: datasets, required: true}
    ];
    openModalBox(language.new_column, fields, language.next, function(data){
        column.dataset = data.dataset;
        var dataToSend = {
            json: {
                dataset: column.dataset
            }
        }
        RestApi.get('StatsSettings', 'getStatsStageTwo', dataToSend, function(response){
            var data = JSON.parse(response);
            var groups = data.groups;
            var methods = [];
            data.methods.forEach(item => {
                var option = {
                    title: item,
                    value: item
                }
                methods.push(option);
            });
            var fields = [
                {type: 'select', title: language.select_operation, variable: 'method', options: methods, required: true}
            ];
            openModalBox(language.new_column, fields, language.next, function(data){
                data.groups = [];
                data.values = [];
                addColumnGroupWindow(language, datatable, data, groups);
            }, column);
        },
        function(response){
            console.log(response.responseText);
            alert(response.responseText);
        });
    });
    
}

function addColumnGroupWindow(language, datatable, column, groups){
    var groupsFormed = [];
    groups.forEach(item => {
        var option = {
            title: item,
            value: item
        }
        groupsFormed.push(option);
    });
    var fields = [
        {type: 'select', title: language.groups, variable: 'group', options: groupsFormed, required: true}
    ];
    openModalBox(language.new_column, fields, language.next, function(groupData){
        console.log(groupData);
        RestApi.post('StatsSettings', 'getGroupOptions', {group: groupData.group}, 
            function(response){
                var data = JSON.parse(response);
                console.log(data);
                var fields = [
                    {type: 'select', title: language.groups, variable: 'value', options: data, required: true},
                    {type: 'checkbox', title: language.next + '?', variable: 'next'}
                ];
                openModalBox(language.new_column, fields, language.save, function(groupValueData){
                    column.groups.push(groupData.group);
                    column.values.push(groupValueData.value);
                    
                    console.log(column);
                    if(groupValueData.next === 1 && groups.length > 1){
                        var index = groups.indexOf(groupData.group);
                        if (index !== -1) {
                            groups.splice(index, 1);
                        }
                        addColumnGroupWindow(language, datatable, column, groups);
                    }
                    else{
                        var tableData = datatable.getData();
                        tableData.push(column);
                        datatable.setData(tableData);
                    }
                });
            },
            function(response){
                console.log(response.responseText);
                alert(response.responseText);
            });
        });
}

function initRowsTable(language) {
    var div = document.getElementById('rows');
    var config = {
        columns: [
            {title: language.start, variable: 'start', width: 50, minWidth: 50},
            {title: language.end, variable: 'end', width: 50, minWidth: 50},
            {title: language.groups, variable: 'group', width: 50, minWidth: 100},
            {title: language.values, variable: 'value', width: 50, minWidth: 100}
        ],
        data: []
    }
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        RestApi.post('StatsSettings', 'getAllGroups', {}, 
            function(response){
                var data = JSON.parse(response);
                var groups = [];
                data.forEach(item => {
                    var option = {
                        title: item,
                        value: item
                    }
                    groups.push(option);
                });
                var fields = [
                    {type: 'number', title: language.start, variable: 'start', min: 0, required: true},
                    {type: 'number', title: language.end, variable: 'end', min: 0, required: true},
                    {type: 'select', title: language.groups, variable: 'group', options: groups, required: true},
                    {type: 'checkbox', title: language.set_value, variable: 'setValue', required: true}
                ];
                openModalBox(language.new_row, fields, language.next, function(rowData){
                    console.log(rowData);
                    if(rowData.setValue === 1){
                        delete rowData.setValue;
                        RestApi.post('StatsSettings', 'getGroupOptions', {group: rowData.group}, 
                            function(response){
                                var data = JSON.parse(response);
                                var fields = [
                                    {type: 'select', title: language.groups, variable: 'value', options: data, required: true}
                                ];
                                openModalBox(language.new_row, fields, language.save, function(data){
                                    console.log(data);
                                    var tableData = datatable.getData();
                                    tableData.push(data);
                                    datatable.setData(tableData);
                                }, rowData);
                            },
                            function(response){
                                console.log(response.responseText);
                                alert(response.responseText);
                            });
                    }
                    else{
                        delete rowData.setValue;
                        var tableData = datatable.getData();
                        tableData.push(rowData);
                        datatable.setData(tableData);
                    }
                });
            },
            function(response){
                console.log(response.responseText);
                alert(response.responseText);
            });
    });
    datatable.addActionButton(language.remove, function(selected){
        if(selected !== undefined){
            var index = datatable.getSelectedIndex();
            var tableData = datatable.getData();
            tableData.splice(index, 1);
             datatable.setData(tableData);
        }
    });
    return datatable;
}

function Inputs(language){
    var display = document.getElementById('inputsDisplay');
    var inputs = [];
    var overrides = {};
    var cellsTable = '';
    
    var me = this;
    $('#setInputs').click(function(){
        me.clear();
        RestApi.get('StatsSettings', 'getStatsStageOne', {}, function(response){
            var data = JSON.parse(response);
            var allInputs = data.inputs;
            var fields = [];
            allInputs.forEach(item => {
                fields.push({type: 'checkbox', title: item.value, variable: item.value});
            });
            openModalBox(language.new_statistic, fields, language.save, function(inputsSelection){
                console.log(inputsSelection);
                var selectedInputs = [];
                allInputs.forEach(item => {
                    if(inputsSelection[item.value] === 1){
                        selectedInputs.push(item.value);
                    }
                });
                if(selectedInputs.length > 0){
                    RestApi.post('StatsSettings', 'getInputsOptions', {inputs: selectedInputs},
                        function(response){
                            var allInputs = JSON.parse(response);
                            var fields = [];
                            allInputs.forEach(item =>{
                                fields.push({type: 'select', title: item.title, variable: item.variable, options: item.values});
                            });
                            openModalBox(language.new_statistic, fields, language.save, function(inputsOverrides){
                                allInputs.forEach(item => {
                                    display.textContent += item.title + '; ';
                                    var key = item.variable;
                                    if(inputsOverrides[key] !== ''){
                                        overrides[key] = inputsOverrides[key];
                                    }
                                    else{
                                        inputs.push(item.title);
                                    }
                                });
                            });
                        },
                        function(response){
                            console.log(response.responseText);
                            alert(response.responseText);
                    });
                }
            });
        },
        function(response){
            console.log(response.responseText);
            alert(response.responseText);
        });
    });
    
    this.overrideData = function(stats){
        if(stats.json.inputs !== undefined){
            inputs = stats.json.inputs;
        }
        else{
            inputs = [];
        }
        if(stats.json.inputsOverride !== undefined){
            overrides = stats.json.inputsOverride;
        }
        else{
            overrides = [];
        }
        display.textContent = '[]';
    }
    
    this.setCellsTable = function(table){
        cellsTable = table;
    }
    
    this.clear = function(){
        display.textContent = '';
        inputs = [];
        overrides = [];
        if(cellsTable !== ''){
            cellsTable.setData([]);
        }
    }
    
    this.getInputs = function(){
        return inputs;
    }
    
    this.getOverides = function(){
        return overrides;
    }
}

function initCellsTable(language, inputsObject) {
    var div = document.getElementById('cells');
    var config = {
        columns: [
            {title: language.column, variable: 'column', width: 50, minWidth: 50},
            {title: language.row, variable: 'row', width: 50, minWidth: 50},
            {title: language.inputs, variable: 'input', width: 50, minWidth: 100},
            {title: language.values, variable: 'operation', width: 50, minWidth: 100}
        ],
        data: []
    }
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var inputs = inputsObject.getInputs();
        if(inputs.length> 0){
            var options = [];
            inputs.forEach(input => {
                var option = {
                    title: input,
                    value: input
                }
                options.push(option);
            });
            var operations =[
                {title: 'Text', value: 'text'},
                {title: language.values, value: 'value'},
                {title: language.count, value: 'count'}
            ];
            var fields = [
                {type: 'number', title: language.column, variable: 'column', min: 0, required: true},
                {type: 'number', title: language.row, variable: 'row', min: 0, required: true},
                {type: 'select', title: language.select_inputs, variable: 'input', options: options, required: true},
                {type: 'select', title: language.select_operation, variable: 'operation', options: operations, required: true}
            ];
            openModalBox(language.new_cell, fields, language.save, function(cell){
                var data = datatable.getData();
                data.push(cell);
                datatable.setData(data);
            });
            
        }
    });
    datatable.addActionButton(language.remove, function(selected){
        if(selected !== undefined){
            var index = datatable.getSelectedIndex();
            var tableData = datatable.getData();
            tableData.splice(index, 1);
             datatable.setData(tableData);
        }
    });
    return datatable;
}

function saveStatsWindow(language, statsTable, select, colsTable, rowsTable, inputs, cellsTable){
    var name = document.getElementById('statsName').value;
    var sort = document.getElementById('statsSort').value;
    var stats = {
        name: name,
        sort_priority: parseInt(sort),
        json: {
            inputs: inputs.getInputs(),
            inputsOverride: inputs.getOverides(),
            file: select.getFilename(),
            cols: colsTable.getData(),
            rows: rowsTable.getData(),
            cells: cellsTable.getData()
        }
    }
    if(stats.json.cols.length > 0 && stats.json.rows.length > 0 && name !== '' && stats.json.file !== ''){
        var selectedStats = statsTable.getSelected();
        if(selectedStats !== undefined){
            stats.id = selectedStats.id;
        }
        saveStats(stats, statsTable);
    }
    else{
        alert(language.set_column_row_name_file);
    }
    console.log(stats);
}

function saveStats(stats, datatable){
    RestApi.post('StatsSettings', 'saveFormStats', stats,
        function(response){
            var data = JSON.parse(response);
            console.log(data);
            alert(data.message);
            datatable.refresh();
        },
        function(response){
            console.log(response.responseText);
            alert(response.responseText);
        });
}