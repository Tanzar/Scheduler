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
            {title: language.name, variable: 'title', width: 150, minWidth: 150},
            {title: language.output_form, variable: 'output_form', width: 150, minWidth: 150},
        ],
        dataSource: datasource
    }
    var datatable = new Datatable(div, config);
    datatable.setOnSelect(function(selected){
        if(selected != undefined){
            inputs.load(selected);
            document.getElementById('generateStats').style.display = 'block';
            document.getElementById('statsDisplay').innerHTML = '';
            if(selected.output_form === 'Tabela'){
                document.getElementById('generatePDF').style.display = 'block';
                document.getElementById('generateXLSX').style.display = 'block';
            }
            else{
                document.getElementById('generatePDF').style.display = 'none';
                document.getElementById('generateXLSX').style.display = 'none';
            }
        }
    });
    datatable.setOnUnselect(function(){
        inputs.clear();
        document.getElementById('generateStats').style.display = 'none';
        document.getElementById('generatePDF').style.display = 'none';
        document.getElementById('generateXLSX').style.display = 'none';
        document.getElementById('statsDisplay').innerHTML = '';
    });
    
    $('#generateStats').click(function(){
        document.getElementById('statsDisplay').innerHTML = '';
        var selected = datatable.getSelected();
        if(selected !== undefined){
            var inputsValues = inputs.getInputs();
            var dataToSend = {
                id: selected.id,
                inputs: inputsValues
            }
            if(selected.output_form === 'Tabela'){
                RestApi.post('StatsDisplay', 'generateStats', dataToSend, function(response){
                    var data = JSON.parse(response);
                    document.getElementById('statsDisplay').innerHTML = data.HTML;
                });
            }
            else if(selected.output_form === 'Wykres kołowy' || selected.output_form === 'Wykres okręgowy'){
                RestApi.post('StatsDisplay', 'generateStats', dataToSend, function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    var div = document.createElement('div');
                    div.setAttribute('id', 'plotDisplay');
                    document.getElementById('statsDisplay').appendChild(div);
                    var traces = [];
                    if(Array.isArray(data.traces)){
                        traces = data.traces;
                    }
                    else{
                        traces = [data.traces];
                    }
                    Plotly.newPlot('plotDisplay', traces, data.layout);
                });
            }
            else if(selected.output_form === 'Wzór XLSX'){
                FileApi.post('StatsDisplay', 'generateStats', dataToSend, true);
            }
            else{
                RestApi.post('StatsDisplay', 'generateStats', dataToSend, function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    var div = document.createElement('div');
                    div.setAttribute('id', 'plotDisplay');
                    document.getElementById('statsDisplay').appendChild(div);
                    var traces = [];
                    if(Array.isArray(data.traces)){
                        traces = data.traces;
                    }
                    else{
                        traces = [data.traces];
                    }
                    Plotly.newPlot('plotDisplay', traces, data.layout);
                });
            }
        }
    });
    $('#generatePDF').click(function(){
        var selected = datatable.getSelected();
        if(selected !== undefined){
            var inputsValues = inputs.getInputs();
            var dataToSend = {
                id: selected.id,
                inputs: JSON.stringify(inputsValues)
            }
            FileApi.post('StatsDisplay', 'generatePDF', dataToSend, true);
        }
    });
    
    $('#generateXLSX').click(function(){
        var selected = datatable.getSelected();
        if(selected !== undefined){
            var inputsValues = inputs.getInputs();
            var dataToSend = {
                id: selected.id,
                inputs: JSON.stringify(inputsValues)
            }
            FileApi.post('StatsDisplay', 'generateXlsx', dataToSend, true);
        }
    });
    /*
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
    });*/
}

function Inputs(){
    var div = document.getElementById('inputs');
    var inputs = {};
    
    this.clear = function(){
        inputs = {};
        while(div.lastChild){
            div.removeChild(div.lastChild);
        }
    }
    
    this.load = function(selected){
        this.clear();
        if(selected !== undefined && selected.id !== undefined){
            var id = selected.id;
            RestApi.get('StatsDisplay', 'getInputsSettings', {id: id}, function(response){
                var data = JSON.parse(response);
                var keys = Object.keys(data);
                keys.forEach(key =>{
                    formInput(key, data[key]);
                });
            });
        }
    }
    
    function formInput(type, options){
        if(type === 'Data' || type === 'Od daty' || type === 'Do daty'){
            formDateInput(type)
        }
        else{
            formSelectInput(type, options);
        }
    }
    
    function formDateInput(index){
        var group = document.createElement('div');
        group.setAttribute('class', 'horizontal-container');
        var text = document.createElement('div');
        text.setAttribute('class', 'standard-text');
        text.textContent = index;
        group.appendChild(text);
        var input = document.createElement('input');
        input.setAttribute('class', 'standard-input');
        input.valueAsDate = new Date();
        inputs[index] = input.value;
        input.onchange = function(){
            inputs[index] = input.value;
        };
        group.appendChild(input);
        div.appendChild(group);
    }
    
    function formSelectInput(index, options){
        var group = document.createElement('div');
        group.setAttribute('class', 'horizontal-container');
        var text = document.createElement('div');
        text.setAttribute('class', 'standard-text');
        text.textContent = index;
        group.appendChild(text);
        var input = document.createElement('select');
        input.setAttribute('class', 'standard-input');
        options.forEach(item => {
            var option = document.createElement('option');
            option.value = item.value;
            option.textContent = item.title;
            input.appendChild(option);
        });
        inputs[index] = input.value;
        input.onchange = function(){
            inputs[index] = input.value;
        };
        group.appendChild(input);
        div.appendChild(group);
    }
    
    this.getInputs = function(){
        return inputs;
    }
}