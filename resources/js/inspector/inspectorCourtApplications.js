/* 
 * This code is free to use, just remember to give credit.
 */


function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        var selectDocument = new Select('documents', language.select_document);
        initDateSelection(selectDocument);
        var table = new ApplicationsTable(language, selectDocument);
    });
}     

function ApplicationsTable(language, selectDocument){
    var div = document.getElementById('courtApplications');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'InspectorCourtApplication',
            task: 'getCourtApplications',
            id_document: 0
        }
    };
    var config = {
        columns: [
            {title: language.date, variable: 'date', width: 70, minWidth: 70},
            {title: language.accusation, variable: 'accusation', width: 500, minWidth: 500},
            {title: language.position, variable: 'position', width: 150, minWidth: 150},
            {title: language.position_groups, variable: 'position_group', width: 100, minWidth: 100},
            {title: language.external_company, variable: 'external_company_text', width: 100, minWidth: 100},
            {title: language.company_name, variable: 'company_name', width: 100, minWidth: 100},
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
                controller: 'InspectorCourtApplication',
                task: 'getCourtApplications',
                id_document: id
            }
        });
    });
    
    datatable.addActionButton(language.add, function(){
        var documentId = selectDocument.value();
        if(documentId !== '0'){
            RestApi.get('InspectorCourtApplication', 'getNewApplicationDetails', {id_document: documentId}, function(response){
                var data = JSON.parse(response);
                var positions = [];
                data.groups.forEach(item => {
                    var option = {
                        value: item.id,
                        title: item.name
                    }
                    positions.push(option);
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
                    {type: 'select', title: language.select_position_group, variable: 'id_position_groups', options: positions, required: true},
                    {type: 'text', title: language.position, variable: 'position', limit: 255, required: true},
                    {type: 'number', title: language.court_application_value, variable: 'value', min: 0, required: true},
                    {type: 'textarea', title: language.accusation, variable: 'accusation', width: 30, height: 3, required: true},
                    {type: 'checkbox', title: language.external_company, variable: 'external_company'},
                    {type: 'text', title: language.company_name, variable: 'company_name', limit: 255},
                    {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 5}
                ];
                var item = {
                    id_document: documentId
                }
                openModalBox(language.new_court_application, fields, language.save, function(data){
                    RestApi.post('InspectorCourtApplication', 'saveNewCourtApplication', data,
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
            RestApi.get('InspectorCourtApplication', 'getNewApplicationDetails', {id_document: documentId}, 
                function(response){
                    var data = JSON.parse(response);
                    var positions = [];
                    data.groups.forEach(item => {
                        var option = {
                            value: item.id,
                            title: item.name
                        }
                        positions.push(option);
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
                        {type: 'select', title: language.select_position_group, variable: 'id_position_groups', options: positions, required: true},
                        {type: 'text', title: language.position, variable: 'position', limit: 255, required: true},
                        {type: 'number', title: language.court_application_value, variable: 'value', min: 0, required: true},
                        {type: 'textarea', title: language.accusation, variable: 'accusation', width: 30, height: 3, required: true},
                        {type: 'checkbox', title: language.external_company, variable: 'external_company'},
                        {type: 'text', title: language.company_name, variable: 'company_name', limit: 255},
                        {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 5}
                    ];
                    openModalBox(language.edit_court_application, fields, language.save, function(data){
                        RestApi.post('InspectorCourtApplication', 'saveCourtApplication', data,
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
                alert(language.select_court_application);
            }
        }
    });
    datatable.addActionButton(language.remove, function(selected){
        var documentId = selectDocument.value();
        if(documentId !== '0' && selected !== undefined){
            RestApi.post('InspectorCourtApplication', 'removeApplication', {id: selected.id},
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
                alert(language.select_court_aplication);
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
        selectDocument.loadOptions('InspectorTickets', 'getDocuments', data);
    }
}