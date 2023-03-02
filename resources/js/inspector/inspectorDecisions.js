/* 
 * This code is free to use, just remember to give credit.
 */


function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        var selectDocument = new Select('documents', language.select_document);
        initDateSelection(selectDocument);
        
        initDecisionsTable(language, selectDocument);
    });
}

function initDecisionsTable(language, selectDocument){
    var div = document.getElementById('decisions');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'InspectorDecisions',
            task: 'getDecisions',
            id_document: 0
        }
    };
    var config = {
        columns: [
            {title: language.date, variable: 'date', width: 70, minWidth: 70},
            {title: language.decision_law, variable: 'law', width: 150, minWidth: 150},
            {title: language.description, variable: 'description', width: 600, minWidth: 600},
            {title: language.remarks, variable: 'remarks', width: 250, minWidth: 250}
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
                controller: 'InspectorDecisions',
                task: 'getDecisions',
                id_document: id
            }
        });
    });
    
    datatable.addActionButton(language.add, function(){
        var documentId = selectDocument.value();
        if(documentId !== '0'){
            RestApi.get('InspectorDecisions', 'getNewDecisionDetails', {id_document: documentId}, 
                function(response){
                    var details = JSON.parse(response);
                    var laws = [];
                    details.laws.forEach(item => {
                        var option = {
                            value: item.id,
                            title: item.law
                        }
                        laws.push(option);
                    });
                    var date = new Date();
                    var start = new Date(details.start);
                    var end = new Date(details.end);
                    if(date < start){
                        date = start;
                    }
                    if(date > end){
                        date = end;
                    }
                    var fields = [
                        {type: 'select', title: language.select_decision_law, variable: 'id_decision_law', options: laws, required: true},
                        {type: 'date', title: language.date, variable: 'date', min: details.start, max: details.end, value: date.toISOString().split('T')[0]},
                        {type: 'textarea', title: language.description, variable: 'description', width: 40, height: 10},
                        {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 40, height: 5}
                    ];
                    var item = {
                        id_document: documentId
                    }
                    openModalBox(language.new_decision, fields, language.save, function(data){
                        var require_suspension = false;
                        var idLaw = parseInt(data.id_decision_law);
                        details.laws.forEach(item => {
                            if(item.id === idLaw && item.requires_suspension){
                                require_suspension = true;
                            }
                        });
                        console.log(data);
                        if(require_suspension){
                            RestApi.get('InspectorDecisions', 'getSuspensions', {id_document: documentId}, function(response){
                                var suspensions = JSON.parse(response);
                                var options = [];
                                suspensions.forEach(item => {
                                    if(item.description.length > 20){
                                        var suspension = {
                                            title: item.date + ' : ' + item.description.slice(0, 20) + '...',
                                            value: item.id
                                        }
                                    }
                                    else{
                                        var suspension = {
                                            title: item.date + ' : ' + item.description,
                                            value: item.id
                                        }
                                    }
                                    options.push(suspension);
                                });
                                var fields = [
                                    {type: 'select', title: language.select_suspension, variable: 'id_suspension', options: options, required: true}
                                ]
                                openModalBox(language.new_decision, fields, language.save, function(data){
                                    RestApi.post('InspectorDecisions', 'saveDecision', data,
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
                                }, data);
                            });
                        }
                        else{
                            RestApi.post('InspectorDecisions', 'saveDecision', data,
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
            RestApi.get('InspectorDecisions', 'getNewDecisionDetails', {id_document: documentId}, 
                function(response){
                    var details = JSON.parse(response);
                    var date = new Date();
                    var start = new Date(details.start);
                    var end = new Date(details.end);
                    if(date < start){
                        date = start;
                    }
                    if(date > end){
                        date = end;
                    }
                    var fields = [
                        {type: 'date', title: language.date, variable: 'date', min: details.start, max: details.end, value: date.toISOString().split('T')[0]},
                        {type: 'textarea', title: language.description, variable: 'description', width: 40, height: 10},
                        {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 40, height: 5}
                    ];
                    openModalBox(language.new_decision, fields, language.save, function(data){
                        RestApi.post('InspectorDecisions', 'saveDecision', data,
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
                alert(language.select_decision);
            }
        }
    });
    
    datatable.addActionButton(language.remove, function(selected){
        var documentId = selectDocument.value();
        if(documentId !== '0' && selected !== undefined){
            RestApi.post('InspectorDecisions', 'removeDecision', {id: selected.id},
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
                alert(language.select_decision);
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
        selectDocument.loadOptions('InspectorDecisions', 'getDocuments', data);
    }
}