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
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'StatsSettings',
            task: 'getStatsWithoutForm'
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
    datatable.addActionButton(language.add, function(){
        openNewStatsWindow(language, datatable);
    });
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
}

function openNewStatsWindow(language, datatable) {
    RestApi.get('StatsSettings', 'getStatsStageOne', {}, function(response){
        var data = JSON.parse(response);
        console.log(data);
        var fields = [
            {type: 'textarea', title: language.name, variable: 'name', limit: 255, width: 30, height: 5, required: true},
            {type: 'select', title: language.select_stats_type, variable: 'type', options: data.types, required: true},
            {type: 'select', title: language.select_dataset, variable: 'dataset', options: data.datasets, required: true},
            {type: 'select', title: language.select_result_form, variable: 'resultForm', options: data.resultForms, required: true}
        ];
        fields.push({type: 'display', title: language.select_inputs});
        data.inputs.forEach(item => {
            fields.push({type: 'checkbox', title: item.title, variable: item.value});
        });
        var inputs = data.inputs;
        openModalBox(language.new_statistic, fields, language.next, function(data){
            var dataToSend = {
                name: data.name,
                type: data.type,
                json: {
                    dataset: data.dataset,
                    resultForm: data.resultForm,
                    inputs: []
                }
            };
            inputs.forEach(item => {
                if(data[item.value] === 1){
                    dataToSend.json.inputs.push(item.value);
                }
            });
            startResultOptionsPhase(language, datatable, dataToSend);
        });
    })
}

function startResultOptionsPhase(language, datatable, data) {
    var settings = JSON.parse(JSON.stringify(data));
    RestApi.post('StatsSettings', 'getStatsStageTwo', data,
        function(response){
            var responseData = JSON.parse(response);
            openXaxisWindow(language, datatable, settings, responseData);
            
        },
        function(response){
            console.log(response.responseText);
            alert(response.responseText);
    });
    
}

function openXaxisWindow(language, datatable, settings, options) {
    var title = '';
    if(settings.json.resultForm.includes('Tabela')){
        title = language.columns;
    }
    else{
        title = language.x_axis;
    }
    var selectOptions = [];
    options.groups.forEach(item => {
        var option = {
            title: item,
            value: item
        }
        selectOptions.push(option);
    });
    var fields = [
        {type: 'select', title: title, variable: 'x', options: selectOptions, required: true}
    ];
    openModalBox(title, fields, language.next, function(data){
        settings.json.x = data.x;
        options.groups.forEach(function (item, i) {
            if(data.x === item){
                options.groups.splice(i, 1);
            }
        });
        if(settings.json.resultForm.includes("Tabela")){
            openYaxisWindow(language, datatable, settings, options);
        }
        else{
            openOperationWindow(language, datatable, settings, options);
        }
    });
}

function openYaxisWindow(language, datatable, settings, options) {
    var title = language.rows;
    var selectOptions = [];
    options.groups.forEach(item => {
        var option = {
            title: item,
            value: item
        }
        selectOptions.push(option);
    });
    var fields = [
        {type: 'select', title: title, variable: 'y', options: selectOptions, required: true}
    ];
    openModalBox(title, fields, language.next, function(data){
        settings.json.y = data.y;
        openOperationWindow(language, datatable, settings, options);
    });
}

function openOperationWindow(language, datatable, settings, options) {
    var title = language.operation;
    var operations = [];
    options.methods.forEach(method => {
        var option = {
            title: method,
            value: method
        }
        operations.push(option);
    });
    var fields = [
        {type: 'select', title: language.select_operation, variable: 'method', options: operations, required: true}
    ];
    openModalBox(title, fields, language.save, function(data){
        settings.json.method = data.method;
        RestApi.post('StatsSettings', 'saveStats', settings,
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
    });
}