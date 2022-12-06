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
            {title: language.name, variable: 'name', width: 250, minWidth: 250}
        ],
        dataSource: datasource
    }
    var datatable = new Datatable(div, config);
    datatable.setOnSelect(function(selected){
        if(selected != undefined){
            display.clear();
            inputs.load(selected.id);
            document.getElementById('generateStats').style.display = 'block';
        }
    });
    datatable.setOnUnselect(function(){
        inputs.clear();
        display.clear();
        document.getElementById('generateStats').style.display = 'none';
    });
    $('#generateStats').click(function(){
        if(inputs.haveInvalid()){
            alert(language.select_inputs_values);
        }
        else{
            var json = inputs.getValues();
            var data = JSON.parse(JSON.stringify(json));
            var selected = datatable.getSelected();
            data.id = selected.id;
            RestApi.get('StatsDisplay', 'generateStats', data, function(response){
                var data = JSON.parse(response);
                display.load(data);
            });
        }
    });
}

function Inputs() {
    this.div = document.getElementById('inputs');
    var values = {};
    
    
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
    }
    
    this.getValues = function() {
        return values;
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
    
    this.load = function(config){
        this.clear();
        console.log(config);
        if(config.type === 'table'){
            loadTable(config);
        }
        else{
            loadPlot(config);
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