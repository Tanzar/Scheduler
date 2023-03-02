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
            {title: language.name, variable: 'name', width: 250, minWidth: 250},
            {title: language.stats_type, variable: 'type', width: 100, minWidth: 100},
            {title: language.result_form, variable: 'form', width: 200, minWidth: 200}
        ],
        dataSource: datasource
    }
    var datatable = new Datatable(div, config);
    datatable.setOnSelect(function(selected){
        var options = document.getElementById('tableOptions');
        options.style.display = 'none';
        if(selected != undefined){
            display.clear();
            inputs.load(selected.id);
            document.getElementById('generateStats').style.display = 'block';
            document.getElementById('generatePDF').style.display = 'none';
            document.getElementById('generateExcel').style.display = 'none';
        }
    });
    datatable.setOnUnselect(function(){
        var options = document.getElementById('tableOptions');
        options.style.display = 'none';
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
        var options = document.getElementById('tableOptions');
        options.style.display = 'block';
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
        
        document.getElementById('hideRows').onchange = function(){
            for (var i = 1; i < table.children.length; i++) {
                var tr = table.children[i];
                if(this.checked){
                    var empty = true;
                    for (var j = 1; j < tr.children.length; j++) {
                        var td = tr.children[j];
                        if(td.textContent !== ''){
                            empty = false;
                        }
                    }
                    if(empty){
                        tr.style.display = 'none';
                    }
                }
                else{
                    tr.style.display = '';
                }
            }
        }
        
        document.getElementById('hideColumns').onchange = function(){
            if(table.children[2] !== undefined){
                var cols = table.children[2].children.length;
                var rows = table.children.length - 2;
            }
            for(var col = 0; col < cols; col++){
                if(this.checked){
                    var empty = true;
                    for(var row = 2; row < rows; row++){
                        var cell = table.children[row].children[col];
                        if(cell.textContent !== ''){
                            empty = false;
                        }
                    }
                    if(empty){
                        for(var row = 1; row < rows; row++){
                            var cell = table.children[row].children[col];
                            cell.style.display = 'none';
                        }
                        
                    }
                }
                else{
                    for(var row = 1; row < rows; row++){
                        var cell = table.children[row].children[col];
                        cell.style.display = '';
                    }
                }
            }
        }
    }
    
    function loadPlot(config){
        var plotDiv = document.createElement('div');
        plotDiv.style.width = '100%';
        plotDiv.style.height = '100%';
        var layout = {
            title: config.title,
            xaxis: {
                type: 'category'
            },
            yaxis: {
                rangemode: 'nonnegative',
                autorange: true
            },
            autosize: false,
            height: 600,
            width: 800,
            margin: {
                l: 50,
                r: 50,
                b: 150,
                t: 100,
                pad: 4
            },
            plot_bgcolor: '#f9f9f9'
        }
        if(Array.isArray(config.data)){
            if(config.data[0].type === 'pie'){
                var font = 15;
                var cols = 3;
                var xPerTrace = 1 / Math.min(config.data.length, cols);
                var startX = 0;
                var yPerTrace = 1 / Math.ceil(config.data.length / cols);
                var startY = 1;
                layout.annotations = [];
                config.data.forEach(item => {
                    item.domain = {
                        x:[startX + (xPerTrace * 0.1), startX + (xPerTrace * 0.9)],
                        y:[startY - (yPerTrace * 0.8), startY - (yPerTrace * 0.1)]
                    }
                    layout.annotations.push({
                        font: {
                            family: 'Arial',
                            size: font
                        },
                        xanchor: 'left',
                        yanchor: 'bottom',
                        xref: 'paper',
                        showarrow: false,
                        text: item.name,
                        width: 0.8 * xPerTrace * layout.width,
                        x: startX,
                        y: startY - (yPerTrace * 0.1)
                    })
                    startX += xPerTrace;
                    if(startX >= 1){
                        startX = 0;
                        startY -= yPerTrace;
                    }
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