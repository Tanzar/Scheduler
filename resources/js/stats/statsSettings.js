/* 
 * This code is free to use, just remember to give credit.
 */

function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        var statsTable = initStatsTable(language);
        var datasetsTable = new DatasetsTable(language);
        var inputs = new Inputs();
        var outputConfigMenu = new OutputConfigMenu(language, datasetsTable, inputs);
        datasetsTable.addOberver(outputConfigMenu);
        
        statsTable.setOnSelect(function(selected){
            console.log(selected);
            $('#title').val(selected.title);
            $('#sortPriority').val(selected.sort_priority);
            $('#outputForm').val(selected.output_form);
            inputs.load(selected.inputs);
            datasetsTable.setData(selected.datasets);
            outputConfigMenu.load(selected.output_form, selected.output_config);
        });
        
        statsTable.setOnUnselect(function(){
            $('#title').val('');
            $('#sortPriority').val('');
            $('#outputForm').val('');
            inputs.load([]);
            datasetsTable.setData([]);
            outputConfigMenu.load('', '');
        });
        
        $('#saveStats').click(function(){
            saveStats(language, statsTable, inputs, datasetsTable, outputConfigMenu);
        });
        
        $('#uploadPattern').click(function(){
            var input = document.getElementById('uploadPatternFile');
            RestApi.upload('StatsSettings', 'uploadTemplate', input,
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        outputConfigMenu.reloadFiles();
                    },
                    function(response){
                        console.log(response.responseText);
                        alert(response.responseText);
                })
        });
    });
}

function saveStats(language, statsTable, inputs, datasetsTable, outputConfigMenu){
    var selected = statsTable.getSelected();
    var data = {
        title: $('#title').val(),
        sort_priority: $('#sortPriority').val(),
        inputs: inputs.getInputs(),
        datasets: datasetsTable.getFormatedData(),
        output_form: $('#outputForm').val(),
        output_config: outputConfigMenu.getConfig()
    };
    if(isValid(data)){
        if(selected !== undefined){
            data.id = selected.id;
            var fields = [
                {type: 'display', title: language.warning_start_overwrite},
                {type: 'checkbox', title: language.continue_questionmark, variable: 'confirm', required: true}
            ];
            openModalBox(language.warning, fields, language.confirm, function(modalData){
                if(modalData.confirm){
                    RestApi.post('StatsSettings', 'saveStats', data,
                        function(response){
                            var data = JSON.parse(response);
                            alert(data.message);
                            statsTable.refresh();
                        },
                        function(response){
                            console.log(response.responseText);
                            alert(response.responseText);
                    });
                }
            });
        }
        else{
            RestApi.post('StatsSettings', 'saveStats', data,
                function(response){
                    var data = JSON.parse(response);
                    alert(data.message);
                    statsTable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
    }
    else{
        alert(language.invalid_stats_settings);
    }
}

function isValid(data){
    if(data.title === '' || data.output_form === ''){
        return false;
    }
    if(data.datasets.length === 0){
        return false;
    }
    if(data.output_config === {}){
        return false;
    }
    else{
        switch(data.output_form){
            case 'Tabela':
                if(data.output_config.dataset === '' || data.output_config.cols === '' || data.output_config.rows === ''){
                    return false;
                }
                break;
            case 'Wzór XLSX':
                if(data.output_config.filename === ''){
                    return false;
                }
                break;
            default:
                if(data.output_config.dataset === '' || data.output_config.axis.length === 0){
                    return false;
                }
                break;
        }
    }
    return true;
}

function initStatsTable(language) {
    var div = document.getElementById('stats');
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'StatsSettings',
            task: 'getAllStats'
        }
    };
    var config = {
        columns: [
            { title: 'ID', variable: 'id', width: 30, minWidth: 30},
            { title: language.active, variable: 'active', width: 50, minWidth: 50},
            {title: language.title, variable: 'title', width: 150, minWidth: 150},
            {title: language.output_form, variable: 'output_form', width: 150, minWidth: 150}
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

function DatasetsTable(language){
    var observers = [];
    var me = this;
    
    function notifyObservers(){
        observers.forEach(observer => {
            if(observer.notify !== undefined){
                observer.notify(me);
            }
        });
    }
    
    var div = document.getElementById('datasets');
    var config = {
        columns: [
            {title: 'Index', variable: 'index', width: 150, minWidth: 150},
            {title: language.dataset, variable: 'dataset', width: 150, minWidth: 150},
            {title: language.operation, variable: 'operation', width: 150, minWidth: 150}
        ],
        data: []
    }
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        RestApi.get('StatsSettings', 'getDatasets', {},
            function(response){
                var data = JSON.parse(response);
                var options = [];
                data.forEach(item => {
                    options.push({
                        title: item,
                        value: item
                    });
                });
                var fields = [
                   {type: 'text', title: language.name, variable: 'index', limit: 50, required: true},
                   {type: 'select', title: language.select_dataset, variable: 'dataset', options: options, required: true}
                ];
                openModalBox(language.dataset, fields, language.next, function(dataStageOne){
                    var tableData = datatable.getData();
                    var notFound = true;
                    tableData.forEach(item => {
                        if(dataStageOne.index === item.index){
                            notFound = false;
                        }
                    });
                    if(notFound){
                        RestApi.get('StatsSettings', 'getOperationsAndGroups', {dataSource: dataStageOne.dataset},
                            function(response){
                                var optionsData = JSON.parse(response);
                                var options = [];
                                optionsData.operations.forEach(item => {
                                    options.push({
                                        title: item,
                                        value: item
                                    });
                                });
                                var fields = [
                                   {type: 'select', title: language.select_operation, variable: 'operation', options: options, required: true},
                                   {type: 'display', title: language.select_groupings}
                                ];
                                optionsData.groups.forEach(item => {
                                    fields.push(
                                            {type: 'checkbox', title: item, variable: item}
                                    );
                                });
                                openModalBox(language.dataset, fields, language.save, function(dataStageTwo){
                                    var dataToSave = {
                                        index: dataStageOne.index,
                                        dataset: dataStageOne.dataset,
                                        operation: dataStageTwo.operation,
                                        groups: []
                                    }
                                    var keys = Object.keys(dataStageTwo);
                                    keys.forEach(key => {
                                        if(key !== 'operation' && dataStageTwo[key] === 1){
                                            dataToSave.groups.push(key);
                                        }
                                    });
                                    tableData.push(dataToSave);
                                    datatable.setData(tableData);
                                    notifyObservers();
                                });
                            },
                            function(response){
                                console.log(response.responseText);
                                alert(response.responseText);
                        });
                    }
                    else{
                        alert(language.index_not_unique);
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
            var data = datatable.getData();
            var index = datatable.getSelectedIndex();
            if(index !== -1){
                data.splice(index, 1);
            }
            datatable.setData(data);
            notifyObservers();
            
        }
        else{
            alert(language.select_dataset);
        }
    });
    
    this.getFormatedData = function(){
        return datatable.getData();
    }
    
    this.addOberver = function(observer){
        observers.push(observer);
    }
    
    this.setData = function(data){
        datatable.setData(data);
    }
}

function Inputs() {
    var table = document.getElementById('inputsOptions');
    var nodes = [];
    
    function allDescendants (node) {
        for (var i = 0; i < node.childNodes.length; i++) {
          var child = node.childNodes[i];
          if(child.id !== undefined && child.id !== ''){
              nodes.push(child);
          }
          allDescendants(child);
          
        }
    }
    
    allDescendants(table);
    
    this.getInputs = function(){
        var activeInputs = {};
        nodes.forEach(node => {
            var id = node.id.slice(6);
            if(node.type === 'checkbox' && node.checked === true){
                activeInputs[id] = '';
            }
            else if (id.endsWith('_value')){
                var index = id.slice(0, -6);
                if(activeInputs[index] !== undefined){
                    activeInputs[index] = node.value;
                }
            }
        });
        var result = [];
        var keys = Object.keys(activeInputs);
        keys.forEach(key => {
            result.push({
                type: key,
                value: activeInputs[key]
            })
        });
        return result;
    }
    
    this.load = function(data){
        nodes.forEach(node => {
            var id = node.id.slice(6);
            if(node.type === 'checkbox'){
                node.checked = false;
                var found = false;
                data.forEach(item => {
                    if(item.type === id){
                        found = true;
                    }
                });
                if(found){
                    node.checked = true;
                }
            }
            else if (id.endsWith('_value')){
                node.value = '';
                var index = id.slice(0, -6);
                data.forEach(item => {
                    if(item.type === index){
                        node.value = item.value;
                    }
                });
            }
        })
    }
}

function OutputConfigMenu(language, datasetsTable, inputs) {
    var div = document.getElementById('outputs');
    var config = {};
    var mode = 'none';
    var datasets = [];
    var me = this;
    var filesSelect;
    var cellsDatatable;
    
    function formConfig(type, data){
        if(data !== undefined){
            config = data;
        }
        else{
            if(type === 'Tabela'){
                config = {
                    dataset: '',
                    cols: '',
                    rows: ''
                }
            }
            else if (type === 'Wzór XLSX'){
                config = {
                    filename: '',
                    cells: []
                }
            }
            else if (type !== 'none'){
                config = {
                    dataset: '',
                    traces_group: '',
                    axis: []
                }
            }
        }
    }
    
    this.load = function(type, data){
        mode = type;
        datasets = datasetsTable.getFormatedData();
        clear(div);
        formConfig(type, data);
        if(type === 'Tabela'){
            loadTableConfig();
        }
        else if (type === 'Wzór XLSX'){
            loadPatternConfig();
        }
        else if (type !== 'none'){
            loadPlotConfig();
        }
    }
    
    function clear(element){
        while(element.lastChild){
            element.removeChild(element.lastChild);
        }
    }
    
    function createDatasetSelect(){
        var selectDataset = document.createElement('select');
        selectDataset.setAttribute('class', 'standard-input');
        selectDataset.required = true;
        var option = document.createElement('option');
        option.value = '';
        option.disabled = true;
        option.selected = true;
        option.textContent = language.select_dataset;
        selectDataset.appendChild(option);
        datasets.forEach(item => {
            var option = document.createElement('option');
            option.value = item.index;
            option.textContent = item.index;
            if(config.dataset === item.index){
                option.selected = true;
            }
            selectDataset.appendChild(option);
        });
        return selectDataset;
    }
    
    function selectLoadGroups(select, groups, placeholder, ignore, selected) {
        clear(select);
        var option = document.createElement('option');
        option.value = '';
        option.disabled = true;
        if(groups.length !== 1){
            option.selected = true;
        }
        option.textContent = placeholder;
        select.appendChild(option);
        groups.forEach(item => {
            if(ignore === undefined || ignore !== item){
                var option = document.createElement('option');
                option.value = item;
                option.textContent = item;
                if(groups.length === 1 || selected === item){
                    option.selected = true;
                }
                select.appendChild(option);
            }
        });
        if(select.ochange !== undefined){
            select.onchange();
        }
    }
    
    function loadTableConfig(){
        
        var selectDataset = createDatasetSelect();
        
        var selectColumn = document.createElement('select');
        selectColumn.setAttribute('class', 'standard-input');
        selectColumn.required = true;
        var selectRow = document.createElement('select');
        selectRow.setAttribute('class', 'standard-input');
        selectRow.required = true;
        var groups = [];
        if(config.dataset !==  ''){
            datasets.forEach(item => {
                if(config.dataset === item.index){
                    groups = item.groups;
                }
            });
        }
        selectLoadGroups(selectColumn, groups, language.column, '', config.cols);
        selectLoadGroups(selectRow, groups, language.row, '', config.rows);
        
        selectDataset.onchange = function(){
            var selected = {groups: [], index: ''};
            datasets.forEach(item => {
                if(item.index === selectDataset.value){
                    selected = item;
                }
            });
            config.dataset = selected.index;
            groups = selected.groups;
            selectLoadGroups(selectColumn, groups, language.column, '', config.cols);
            selectLoadGroups(selectRow, groups, language.row,'' , config.rows);
        }
        selectColumn.onchange = function(){
            config.cols = selectColumn.value;
            selectLoadGroups(selectRow, groups, language.row, selectColumn.value, config.rows);
        }
        selectRow.onchange = function(){
            config.rows = selectRow.value;
            selectLoadGroups(selectColumn, groups, language.column, selectRow.value, config.cols);
        }
        
        
        div.appendChild(selectDataset);
        div.appendChild(selectColumn);
        div.appendChild(selectRow);
    }
    
    function loadPatternConfig(){
        filesSelect = document.createElement('select');
        filesSelect.setAttribute('class', 'standard-input');
        filesSelect.required = true;
        me.reloadFiles();
        
        filesSelect.onchange = function(){
            config.filename = filesSelect.value;
        }
        
        div.appendChild(filesSelect);
        
        var cells = [];
        config.cells.forEach(item => {
            var found = false;
            datasets.forEach(set => {
                if(set.index === item.dataset){
                    found = true;
                }
            })
            if(found){
               cells.push(item); 
            }
        });
        
        var cellsTableDiv = document.createElement('div');
        var tableConfig = {
            columns: [
                {title: language.column, variable: 'col', width: 75, minWidth: 75},
                {title: language.row, variable: 'row', width: 75, minWidth: 75}
            ],
            data: cells
        }
        var cellsDatatable = new Datatable(cellsTableDiv, tableConfig);
        cellsDatatable.addActionButton(language.add, function(){
            var datasets = datasetsTable.getFormatedData();
            var options = [];
            datasets.forEach((item, i) =>{
                options.push({
                    title: item.index,
                    value: i
                });
            });
            var fields = [
                {type: 'text', title: language.column, variable: 'col', limit: 6, required: true},
                {type: 'number', title: language.row, variable: 'row', min: 1, required: true},
                {type: 'select', title: language.select_dataset, variable: 'dataset', options: options, required: true}
            ];
            openModalBox(language.cell, fields, language.next, function(mainData){
                var dataToSend = {
                    inputs: inputs.getInputs(),
                    groups: datasets[mainData.dataset].groups
                }
                RestApi.post('StatsSettings', 'getSelectedGroupsOptions', dataToSend, 
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        var fields = [];
                        dataToSend.groups.forEach(group => {
                            var options = data[group];
                            fields.push({
                                type: 'select', title: group, variable: group, options: options, required: true
                            });
                        });
                        openModalBox(language.cell, fields, language.add, function(groupsData){
                            var cellsData = cellsDatatable.getData();
                            var groups = [];
                            var keys = Object.keys(groupsData);
                            keys.forEach(key => {
                                groups.push({
                                    type: key,
                                    value: groupsData[key]
                                })
                            });
                            cellsData.push({
                                col: mainData.col,
                                row: mainData.row,
                                dataset: datasets[mainData.dataset].index,
                                groups: groups
                            });
                            cellsDatatable.setData(cellsData);
                            config.cells = cellsData;
                        });
                        
                    },
                    function(response){
                        console.log(response.responseText);
                        alert(response.responseText);
                });
            });
        });
        cellsDatatable.addActionButton(language.remove, function(selected){
            if(selected !== undefined){
                var data = cellsDatatable.getData();
                var index = cellsDatatable.getSelectedIndex();
                if(index !== -1){
                    data.splice(index, 1);
                }
                cellsDatatable.setData(data);
                config.cells = data;
            }
            else{
                alert(language.select_cell);
            }
        });
        div.appendChild(cellsTableDiv);
    }
    
    function selectLoadGroupsSelectablePlaceholder(select, groups, placeholder, ignore, traces) {
        clear(select);
        var option = document.createElement('option');
        option.value = '';
        option.selected = true;
        option.textContent = placeholder;
        select.appendChild(option);
        groups.forEach(item => {
            if(ignore === undefined || ignore !== item){
                var option = document.createElement('option');
                option.value = item;
                option.textContent = item;
                if(traces !== undefined && traces === item){
                    option.selected = true;
                }
                select.appendChild(option);
            }
        });
        if(select.ochange !== undefined){
            select.onchange();
        }
    }
    
    function createGroupsCheckboxes(div, groups, ignore){
        clear(div);
        var oldAxis = config.axis;
        config.axis = [];
        groups.forEach(item => {
            if(ignore === undefined || ignore !== item){
                var input = document.createElement('input');
                input.setAttribute('type', 'checkbox');
                input.setAttribute('name', 'group_' + item);
                input.onchange = function() {
                    if(input.checked){
                        config.axis.push(item);
                    }
                    else{
                        config.axis = config.axis.filter(function(e) { return e !== item })
                    }
                }
                if(oldAxis.includes(item)){
                    input.checked = true;
                    config.axis.push(item);
                }
                div.appendChild(input);
                var label = document.createElement('label');
                label.setAttribute('for', 'group_' + item);
                label.textContent = item;
                div.appendChild(label);
                var br = document.createElement('br');
                div.appendChild(br);
            }
        });
    }
    
    function loadPlotConfig(){
        var selectDataset = createDatasetSelect();
        
        var selectTraces = document.createElement('select');
        selectTraces.setAttribute('class', 'standard-input');
        var groups = [];
        if(config.dataset !==  ''){
            datasets.forEach(item => {
                if(config.dataset === item.index){
                    groups = item.groups;
                }
            });
        }
        selectLoadGroupsSelectablePlaceholder(selectTraces, groups, language.trace, undefined, config.traces_group);
        
        var axisGroupsDiv = document.createElement('div');
        if(config.traces_group !== undefined && config.traces_group !== ''){
            createGroupsCheckboxes(axisGroupsDiv, groups, config.traces_group);
        }
        else{
            createGroupsCheckboxes(axisGroupsDiv, groups);
        }
        
        
        selectDataset.onchange = function(){
            var selected = {groups: [], index: ''};
            datasets.forEach(item => {
                if(item.index === selectDataset.value){
                    selected = item;
                }
            });
            config.dataset = selected.index;
            groups = selected.groups;
            config.traces_group = '';
            selectTraces.value = '';
            selectLoadGroupsSelectablePlaceholder(selectTraces, groups, language.trace, undefined, config.traces_group);
            createGroupsCheckboxes(axisGroupsDiv, groups);
        }
        selectTraces.onchange = function(){
            config.traces_group = selectTraces.value;
            createGroupsCheckboxes(axisGroupsDiv, groups, selectTraces.value);
        }
        
        var infoDiv = document.createElement('div');
        infoDiv.setAttribute('class', 'standard-text');
        infoDiv.textContent = language.select_groupings;
        
        div.appendChild(selectDataset);
        div.appendChild(selectTraces);
        div.appendChild(infoDiv);
        div.appendChild(axisGroupsDiv);
    }
    
    $('#outputForm').change(function(){
        me.load($('#outputForm').val());
    });
    
    this.getConfig = function(){
        return config;
    }
    
    this.notify = function(datasetsTable){
        var sets = datasetsTable.getFormatedData();
        if(mode === 'Wzór XLSX'){
            config.cells.forEach((cell, i) => {
                var notFound = true;
                sets.forEach(set => {
                    if(cell.dataset === set.index){
                        notFound = false;
                    }
                });
                if(notFound){
                    config.cells.splice(i, 1);
                }
            });
            this.load(mode, config);
        }
        else{
            var notFound = true;
            sets.forEach(set => {
                if(config.dataset === set.index){
                    notFound = false;
                }
            });
            if(notFound){
                this.load(mode);
            }
            else{
                this.load(mode, config);
            }
        }
    }
    
    this.reloadFiles = function(){
        if(mode === 'Wzór XLSX'){
            clear(filesSelect);
            RestApi.post('StatsSettings', 'getTemplatesList', {}, 
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    var placeholder = document.createElement('option');
                    placeholder.placeholder = true;
                    placeholder.selected = true;
                    placeholder.disabled = true;
                    placeholder.value = '';
                    placeholder.textContent = language.select_pattern_file;
                    filesSelect.appendChild(placeholder);
                    data.forEach(item => {
                        var option = document.createElement('option');
                        option.value = item;
                        option.textContent = item;
                        if(item === config.filename){
                            option.selected = true;
                        }
                        filesSelect.appendChild(option);
                    });
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
    }
    
}