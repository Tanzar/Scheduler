/* 
 * This code is free to use, just remember to give credit.
 */

function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        var selectDocument = new Select('documents', language.all);
        var datatable = TicketsTable(language, selectDocument);
        initDateSelection(selectDocument, datatable);
        
    });
}     

function TicketsTable(language, selectDocument){
    var div = document.getElementById('tickets');
    var selectYear = document.getElementById('selectYear');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'InspectorTickets',
            task: 'getTicketsByYear',
            year: selectYear.value
        }
    };
    var config = {
        columns: [
            {title: language.document_number, variable: 'document_number', width: 250, minWidth: 250},
            {title: language.ticket_number, variable: 'number', width: 150, minWidth: 150},
            {title: language.ticket_date, variable: 'date', width: 80, minWidth: 80},
            {title: language.position, variable: 'position', width: 150, minWidth: 150},
            {title: language.violated_rules, variable: 'violated_rules', width: 300, minWidth: 300},
            {title: language.external_company, variable: 'external_company_text', width: 100, minWidth: 100},
            {title: language.company_name, variable: 'company_name', width: 100, minWidth: 100},
            {title: language.remarks, variable: 'remarks', width: 300, minWidth: 300}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
    selectDocument.setOnChange(function(id){
        if(id === '0'){
            var selectYear = document.getElementById('selectYear');
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'InspectorTickets',
                    task: 'getTicketsByYear',
                    year: selectYear.value
                }
            });
        }
        else{
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'InspectorTickets',
                    task: 'getTickets',
                    id: id
                }
            });
        }
    });
    
    datatable.addActionButton(language.add, function(){
        var documentId = selectDocument.value();
        if(documentId !== '0'){
            RestApi.get('InspectorTickets', 'getNewTicketDetails', {id: documentId}, function(response){
                var data = JSON.parse(response);
                var laws = [];
                data.ticket_laws.forEach(item => {
                    var option = {
                        value: item.id,
                        title: item.name
                    }
                    laws.push(option);
                });
                var positions = [];
                data.position_groups.forEach(item => {
                    var option = {
                        value: item.id,
                        title: item.name
                    }
                    positions.push(option);
                });
                var date = new Date();
                var start = new Date(data.start);
                if(date < start){
                    date = start;
                }
                var fields = [
                    {type: 'text', title: language.ticket_number, variable: 'number', limit: 10, required: true},
                    {type: 'date', title: language.ticket_date, variable: 'date', min: data.start, value: date.toDateString()},
                    {type: 'select', title: language.select_position_group, variable: 'id_position_groups', options: positions, required: true},
                    {type: 'text', title: language.position, variable: 'position', limit: 255, required: true},
                    {type: 'number', title: language.ticket_value, variable: 'value', min: 0, required: true},
                    {type: 'select', title: language.select_ticket_law, variable: 'id_ticket_law', options: laws, required: true},
                    {type: 'textarea', title: language.violated_rules, variable: 'violated_rules', limit: 255, width: 30, height: 3, required: true},
                    {type: 'checkbox', title: language.external_company, variable: 'external_company'},
                    {type: 'text', title: language.company_name, variable: 'company_name', limit: 255},
                    {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 5}
                ];
                var item = {
                    id_document: documentId
                }
                openModalBox(language.new_ticket, fields, language.save, function(data){
                    RestApi.post('InspectorTickets', 'saveNewTicket', data,
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
        if(selected !== undefined){
            var documentId = selected.id_document;
            RestApi.get('InspectorTickets', 'getNewTicketDetails', {id: documentId}, 
                function(response){
                    var data = JSON.parse(response);
                    var laws = [];
                    data.ticket_laws.forEach(item => {
                        var option = {
                            value: item.id,
                            title: item.name
                        }
                        laws.push(option);
                    });
                    var positions = [];
                    data.position_groups.forEach(item => {
                        var option = {
                            value: item.id,
                            title: item.name
                        }
                        positions.push(option);
                    });
                    var date = new Date();
                    var start = new Date(data.start);
                    if(date < start){
                        date = start;
                    }
                    var fields = [
                        {type: 'text', title: language.ticket_number, variable: 'number', limit: 10, required: true},
                        {type: 'date', title: language.ticket_date, variable: 'date', min: data.start, value: date.toDateString()},
                        {type: 'select', title: language.select_position_group, variable: 'id_position_groups', options: positions, required: true},
                        {type: 'text', title: language.position, variable: 'position', limit: 255, required: true},
                        {type: 'number', title: language.ticket_value, variable: 'value', min: 0, required: true},
                        {type: 'select', title: language.select_ticket_law, variable: 'id_ticket_law', options: laws, required: true},
                        {type: 'textarea', title: language.violated_rules, variable: 'violated_rules', limit: 255, width: 30, height: 3, required: true},
                        {type: 'checkbox', title: language.external_company, variable: 'external_company'},
                        {type: 'text', title: language.company_name, variable: 'company_name', limit: 255},
                        {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 5}
                    ];
                    openModalBox(language.edit_ticket, fields, language.save, function(data){
                        RestApi.post('InspectorTickets', 'updateTicket', data,
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
            alert(language.select_ticket);
        }
    });
    datatable.addActionButton(language.remove, function(selected){
        if(selected !== undefined){
            RestApi.post('InspectorTickets', 'removeTicket', {id: selected.id},
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
            alert(language.select_ticket);
        }
    });
    return datatable;
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

function initDateSelection(selectDocument, datatable) {
    var selectYear = document.getElementById('selectYear');
    var selectDate = document.getElementById('selectDate');
    selectDate.onclick = function(){
        var data = {
            year: selectYear.value
        }
        selectDocument.loadOptions('InspectorTickets', 'getDocuments', data);
        datatable.setDatasource({
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'InspectorTickets',
                task: 'getTicketsByYear',
                year: selectYear.value
            }
        });
    }
}