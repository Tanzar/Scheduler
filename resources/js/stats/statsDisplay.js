/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        initStatsTable(language);
    });
}

function initStatsTable(language) {
    var div = document.getElementById('stats');
    var inputs = new Inputs();
    var display = new Display();
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'StatsDisplay',
            task: 'getStats'
        }
    };
    var config = {
        columns: [
            {title: language.name, variable: 'name', width: 150, minWidth: 150},
            {title: language.stats_type, variable: 'type', width: 100, minWidth: 100},
            {title: language.result_form, variable: 'form', width: 150, minWidth: 150}
        ],
        dataSource: datasource
    }
    var datatable = new Datatable(div, config);
    datatable.setOnSelect(function(selected){
        if(selected != undefined){
            display.clear();
            inputs.load(selected.id);
            document.getElementById('generateStats').style.display = 'block';
            document.getElementById('generatePDF').style.display = 'none';
            document.getElementById('generateExcel').style.display = 'none';
        }
    });
    datatable.setOnUnselect(function(){
        inputs.clear();
        display.clear();
        document.getElementById('generateStats').style.display = 'none';
        document.getElementById('generatePDF').style.display = 'none';
        document.getElementById('generateExcel').style.display = 'none';
    });
    $('#generateStats').click(function(){
        if(inputs.haveInvalid()){
            alert(language.select_inputs_values);
        }
        else{
            var json = inputs.getValues();
            var inputsData = JSON.parse(JSON.stringify(json));
            var selected = datatable.getSelected();
            inputsData.id = selected.id;
            if(selected.type !== 'Ze wzoru'){
                RestApi.post('StatsDisplay', 'generateStats', inputsData, function(response){
                    var statsData = JSON.parse(response);
                    display.load(statsData, inputs.getTexts());
                    if(statsData.type === 'table' || statsData.type === 'multiple_tables'){
                        document.getElementById('generatePDF').style.display = 'block';
                        document.getElementById('generateExcel').style.display = 'block';
                    }
                });
            }
            else{
                FileApi.post('StatsDisplay', 'generateStats', inputsData, false);
            }
        }
    });
    $('#generatePDF').click(function(){
        if(inputs.haveInvalid()){
            alert(language.select_inputs_values);
        }
        else{
            var json = inputs.getValues();
            var data = JSON.parse(JSON.stringify(json));
            var selected = datatable.getSelected();
            data.id = selected.id;
            FileApi.post('StatsDisplay', 'generatePDF', data, true);
        }
    });
    $('#generateExcel').click(function(){
        if(inputs.haveInvalid()){
            alert(language.select_inputs_values);
        }
        else{
            var json = inputs.getValues();
            var data = JSON.parse(JSON.stringify(json));
            var selected = datatable.getSelected();
            data.id = selected.id;
            FileApi.post('StatsDisplay', 'generateXlsx', data, false);
        }
    });
}

function Inputs() {
    this.div = document.getElementById('inputs');
    var values = {};
    var texts = {};
    
    
    var inputs = this;
    this.load = function(id) {
        RestApi.get('StatsDisplay', 'getInputsSettings', {id: id}, function(response){
            var data = JSON.parse(response);
            inputs.clear();
            console.log(data);
            data.forEach(item =>{
                addInput(item);
            });
        });
    }
    
    function addInput(item) {
        console.log(item);
        switch(item.variable){
            case 'month':
                createInput(item, item.variable, item.title);
                break;
            case 'monthsRange':
                createInput(item, 'monthStart', 'Początek');
                createInput(item, 'monthEnd', 'Koniec');
                break;
            case 'year':
                createInput(item, item.variable, item.title);
                break;
            case 'yearsRange':
                createInput(item, 'yearStart', 'Początek');
                createInput(item, 'yearEnd', 'Koniec');
                break;
            case 'user':
                createInput(item, item.variable, item.title);
                break;
            case 'location':
                createInput(item, item.variable, item.title);
                break;
            case 'locationGroup':
                createInput(item, item.variable, item.title);
                break;
        }
    }
    
    function createInput(item, variable, placeholder) {
        var input = document.createElement('select');
        input.setAttribute('class', 'standard-input');
        input.required = true;
        input.onchange = function(){
            values[variable] = input.value;
            texts[variable] = input.options[input.selectedIndex].text;
        }
        var firstOption = document.createElement('option');
        firstOption.textContent = placeholder;
        firstOption.setAttribute('value', '')
        firstOption.selected = true;
        firstOption.disabled = true;
        input.appendChild(firstOption);
        values[variable] = '';
        item.values.forEach(value => {
            var option = document.createElement('option');
            option.textContent = value.title;
            option.value = value.value;
            input.appendChild(option);
        })
        inputs.div.appendChild(input);
    }
    
    this.clear = function() {
        while(this.div.lastChild){
            this.div.removeChild(this.div.lastChild);
        }
        values = {};
        texts = {};
    }
    
    this.getValues = function() {
        return values;
    }
    
    this.getTexts = function() {
        return texts;
    }
    
    this.haveInvalid = function() {
        var list = this.div.querySelectorAll(':invalid');
        if(list.length > 0){
            return true;
        }
        return false;
    }
}

function Display() {
    var div = document.getElementById('statsDisplay');
    
    this.load = function(config, inputsTexts){
        this.clear();
        var title = '';
        var keys = Object.keys(inputsTexts);
        keys.forEach(key => {
            if(key !== 'yearStart' && key !== 'yearEnd' && key !== 'monthStart' && key !== 'monthEnd'){
                title += ' ' + inputsTexts[key];
            }
        })
        if(inputsTexts.yearStart !== undefined && inputsTexts.yearEnd !== undefined){
            title += ' ' + inputsTexts.yearStart + ' - ' + inputsTexts.yearEnd;
        }
        if(inputsTexts.monthStart !== undefined && inputsTexts.monthEnd !== undefined){
            title += ' ' + inputsTexts.monthStart + ' - ' + inputsTexts.monthEnd;
        }
        config.title += title;
        console.log(config);
        if(config.type === 'table'){
            loadTable(config);
        }
        else if(config.type === 'plot'){
            loadPlot(config);
        }
        else if(config.type === 'multiple_tables'){
            config.data.forEach(item => {
                loadTable(item);
                var br = document.createElement('br');
                div.appendChild(br);
            });
        }
        else if(config.type === 'multiple_plots'){
            var cfg = {
                title: config.title,
                data: []
            }
            config.data.forEach(item => {
                var trace = item.data;
                trace.name = item.title;
                cfg.data.push(trace);
            });
            loadPlot(cfg);
        }
    }
    
    function loadTable(config){
        var table = document.createElement('table');
        table.setAttribute('class', 'standard-table');
        var cells = config.cells;
        var tr = document.createElement('tr');
        tr.setAttribute('class', 'standard-table-tr');
        var td = document.createElement('td');
        td.setAttribute('class', 'standard-table-td');
        td.setAttribute('colspan', cells[0].length);
        td.textContent = config.title;
        tr.appendChild(td);
        table.appendChild(tr);
        cells.forEach(row => {
            var tr = document.createElement('tr');
            tr.setAttribute('class', 'standard-table-tr');
            row.forEach(cell => {
                var td = document.createElement('td');
                td.setAttribute('class', 'standard-table-td');
                td.textContent = cell;
                tr.appendChild(td);
            });
            table.appendChild(tr);
        });
        div.appendChild(table);
    }
    
    function loadPlot(config){
        var plotDiv = document.createElement('div');
        var layout = {
            title: config.title,
            xaxis: {
                type: 'category'
            },
            yaxis: {
                rangemode: 'nonnegative',
                autorange: true
            }
        }
        if(Array.isArray(config.data)){
            if(config.data[0].type === 'pie'){
                var rows = Math.ceil(config.data.length / 2);
                var cols = config.data.length > 1 ? 2 : 1;
                layout.grid = {
                    rows: rows,
                    columns: cols
                }
                layout.annotations = [];
                var counter = 1;
                config.data.forEach(item => {
                    var row = Math.floor(counter / 2);
                    var col = counter % 2 + 1;
                    item.domain = {
                        row: row,
                        column: col
                    }/*
                    layout.annotations.push({
                        text: item.name,
                        x: (col - 1) * 0.5,
                        y: (row + 2) / config.data.length,
                        showarrow: false
                    });*/
                    counter++;
                })
            }
            
            Plotly.newPlot(plotDiv, config.data, layout);
        }
        else{
            Plotly.newPlot(plotDiv, [config.data], layout);
        }
        div.append(plotDiv);
    }
    
    this.clear = function() {
        while(div.lastChild){
            div.removeChild(div.lastChild);
        }
    }
}