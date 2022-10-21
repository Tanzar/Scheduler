/* 
 * This code is free to use, just remember to give credit.
 */


function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        
        var selectDocument = new Select('documents', language.select_document);
        initDateSelection(selectDocument);
        
        var datatable = new UsagesTable(language, selectDocument);
    });
}     

function UsagesTable(language, selectDocument){
    var div = document.getElementById('usages');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'InspectorInstruments',
            task: 'getUsages',
            id_document: 0
        }
    };
    var config = {
        columns: [
            {title: language.equipment_name, variable: 'equipment_name', width: 150, minWidth: 150},
            {title: language.inventory_number, variable: 'inventory_number', width: 150, minWidth: 150},
            {title: language.date, variable: 'date', width: 100, minWidth: 100},
            {title: language.recommendation_decision, variable: 'recommendation_decision_text', width: 150, minWidth: 150},
            {title: language.remarks, variable: 'remarks', width: 200, minWidth: 200}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
    selectDocument.setOnChange(function(id){
        if(id === undefined){
            id = 0;
        }
        datatable.setDatasource({
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'InspectorInstruments',
                task: 'getUsages',
                id_document: id
            }
        });
    });
    
    datatable.addActionButton(language.add, function(){
        var documentId = selectDocument.value();
        if(documentId !== '0'){
            RestApi.get('InspectorInstruments', 'getNewUsageDetails', {id_document: documentId}, function(response){
                var data = JSON.parse(response);
                var instruments = [];
                data.instruments.forEach(item => {
                    var option = {
                        value: item.id,
                        title: item.name
                    }
                    instruments.push(option);
                });
                var date = new Date();
                var start = new Date(data.start);
                var end = new Date(data.end);
                if(date < start){
                    date = start;
                }
                if(date > end){
                    date = end;
                }
                var fields = [
                    {type: 'date', title: language.date, variable: 'date', min: data.start, max: data.end, value: date.toDateString()},
                    {type: 'select', title: language.select_measurement_instrument, variable: 'id_equipment', options: instruments, required: true},
                    {type: 'checkbox', title: language.recommendation_decision, variable: 'recommendation_decision'},
                    {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 5}
                ];
                var item = {
                    id_document: documentId
                }
                openModalBox(language.new_equipment_usage, fields, language.save, function(data){
                    RestApi.post('InspectorInstruments', 'saveNewUsage', data,
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
                }, item);
            });
        }
        else{
            alert(language.select_document);
        }
    });
    datatable.addActionButton(language.edit, function(selected){
        var documentId = selectDocument.value();
        if(documentId !== '0' && selected !== undefined){
            RestApi.get('InspectorInstruments', 'getNewUsageDetails', {id_document: documentId}, 
                function(response){
                    var data = JSON.parse(response);
                    var instruments = [];
                    data.instruments.forEach(item => {
                        var option = {
                            value: item.id,
                            title: item.name
                        }
                        instruments.push(option);
                    });
                    var date = new Date();
                    var start = new Date(data.start);
                    var end = new Date(data.end);
                    if(date < start){
                        date = start;
                    }
                    if(date > end){
                        date = end;
                    }
                    var fields = [
                        {type: 'date', title: language.date, variable: 'date', min: data.start, max: data.end, value: date.toDateString()},
                        {type: 'select', title: language.select_measurement_instrument, variable: 'id_equipment', options: instruments, required: true},
                        {type: 'checkbox', title: language.recommendation_decision, variable: 'recommendation_decision'},
                        {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 5}
                    ];
                    openModalBox(language.edit_equipment_usage, fields, language.save, function(data){
                        RestApi.post('InspectorInstruments', 'updateUsage', data,
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
                    }, selected);
            });
        }
        else{
            if(documentId === '0'){
                alert(language.select_document);
            }
            else{
                alert(language.select_equipment_usage);
            }
        }
    });
    datatable.addActionButton(language.remove, function(selected){
        var documentId = selectDocument.value();
        if(documentId !== '0' && selected !== undefined){
            RestApi.post('InspectorInstruments', 'removeUsage', {id: selected.id},
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
            if(documentId === '0'){
                alert(language.select_document);
            }
            else{
                alert(language.select_equipment_usage);
            }
        }
    });
}

function Select(id, placeholder){
    var select = document.getElementById(id);
    var hiddenValues = {};
    var dataToSend = {};
    
    this.clear = function(){
        while(select.firstChild){
            select.removeChild(select.firstChild);
        }
        
        var option = document.createElement('option');
        option.selected = true;
        option.disabled = true;
        option.placeholder = true;
        option.value = '0';
        option.textContent = placeholder;
        select.appendChild(option);
        hiddenValues = {};
    }
    
    this.setOnChange = function(action){
        select.onchange = function(){
            var hidden = hiddenValues[select.value];
            action(select.value, hidden);
        }
    }
    
    this.addOption = function(value, text, hidden){
        var option = document.createElement('option');
        option.value = value;
        option.textContent = text;
        hiddenValues[value] = hidden;
        select.appendChild(option);
    }
    
    var me = this;
    this.loadOptions = function(controller, task, inputData){
        RestApi.get(controller, task, inputData, function(response){
            me.clear();
            dataToSend = inputData;
            var data = JSON.parse(response);
            data.forEach(item => {
                me.addOption(item.id, item.number);
            });
        });
    }
    
    this.value = function(){
        return select.value;
    }
}

function initDateSelection(selectDocument) {
    var selectMonth = document.getElementById('selectMonth');
    var selectYear = document.getElementById('selectYear');
    var selectDate = document.getElementById('selectDate');
    selectDate.onclick = function(){
        var data = {
            month: selectMonth.value,
            year: selectYear.value
        }
        selectDocument.loadOptions('InspectorInstruments', 'getDocuments', data);
    }
}