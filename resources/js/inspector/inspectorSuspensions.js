/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        
        var selectDocument = new Select('documents', language.all);
        var suspensionsTable = SuspensionsTable(language, selectDocument);
        initDateSelection(selectDocument, suspensionsTable);
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

function initDateSelection(selectDocument, suspensionsTable) {
    var selectYear = document.getElementById('selectYear');
    var selectDate = document.getElementById('selectDate');
    selectDate.onclick = function(){
        var data = {
            year: selectYear.value
        }
        selectDocument.loadOptions('InspectorSuspensions', 'getDocuments', data);
        suspensionsTable.setDatasource({
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'InspectorSuspensions',
                task: 'getSuspensionsByYear',
                year: selectYear.value
            }
        });
        clearLists();
    }
}

function SuspensionsTable(language, selectDocument){
    var div = document.getElementById('suspensions');
    var selectYear = document.getElementById('selectYear');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'InspectorSuspensions',
            task: 'getSuspensionsByYear',
            year: selectYear.value
        }
    };
    var config = {
        columns: [
            {title: language.document_number, variable: 'document_number', width: 250, minWidth: 250},
            {title: language.date, variable: 'date', width: 80, minWidth: 80},
            {title: language.suspension_group, variable: 'group_name', width: 100, minWidth: 100},
            {title: language.suspension_type, variable: 'type_name', width: 100, minWidth: 100},
            {title: language.suspension_object, variable: 'object_name', width: 100, minWidth: 100},
            {title: language.suspension_reason, variable: 'reason', width: 100, minWidth: 100},
            {title: language.shift, variable: 'shift', width: 50, minWidth: 50},
            {title: language.region, variable: 'region', width: 150, minWidth: 150},
            {title: language.description, variable: 'description', width: 400, minWidth: 400},
            {title: language.correction_date, variable: 'correction_date', width: 80, minWidth: 80},
            {title: language.correction_shift, variable: 'correction_shift', width: 50, minWidth: 50},
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
                    controller: 'InspectorSuspensions',
                    task: 'getSuspensionsByYear',
                    year: selectYear.value
                }
            });
        }
        else{
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'InspectorSuspensions',
                    task: 'getSuspensions',
                    id_document: id
                }
            });
        }
        clearLists();
    });
    datatable.addActionButton(language.add, function(){
        var idDocument = selectDocument.value();
        if(idDocument !== '0'){
            RestApi.get('InspectorSuspensions', 'getNewSuspensionDetails', {id_document: idDocument}, function(response){
                var details = JSON.parse(response);
                var groups = [];
                details.groups.forEach(item => {
                    var group = {
                        title: item.name,
                        value: item.id
                    }
                    groups.push(group);
                });
                var fields = [
                    {type: 'select', title: language.select_suspension_group, variable: 'id_suspension_group', options: groups, required: true}
                ];
                openModalBox(language.new_suspension, fields, language.next, function(data){
                    var types = [];
                    details.types.forEach(item => {
                        if(parseInt(item.id_suspension_group) === parseInt(data.id_suspension_group)){
                            var type = {
                                value: item.id,
                                title: item.name
                            }
                            types.push(type);
                        }
                    });
                    var fields = [
                        {type: 'select', title: language.select_suspension_type, variable: 'id_suspension_type', options: types, required: true}
                    ];
                    openModalBox(language.new_suspension, fields, language.next, function(data){
                        data.id_document = idDocument;
                        var date = new Date();
                        var start = new Date(details.start);
                        var end = new Date(details.end);
                        if(date < start){
                            date = start;
                        }
                        if(date > end){
                            date = end;
                        }
                        var objects = [];
                        details.typeObjectRelations.forEach(item => {
                            var type = {
                                value: item.id_suspension_object,
                                title: item.suspension_object
                            }
                            if(parseInt(item.id_suspension_type) === parseInt(data.id_suspension_type)){
                                objects.push(type);
                            }
                        });
                        var reasons = [];
                        details.reasons.forEach(item => {
                            var reason = {
                                value: item.id,
                                title: item.name
                            }
                            reasons.push(reason);
                        });
                        var fields = [
                            {type: 'date', title: language.suspension_date, variable: 'date', min: details.start, max: details.end, value: date.toDateString()},
                            {type: 'select', title: language.select_suspension_object, variable: 'id_suspension_object', options: objects, required: true},
                            {type: 'select', title: language.select_suspension_reason, variable: 'id_suspension_reason', options: reasons, required: true},
                            {type: 'text', title: language.shift, variable: 'shift', limit: 5, required: true},
                            {type: 'textarea', title: language.region, variable: 'region', limit: 255, width: 30, height: 3, required: true},
                            {type: 'textarea', title: language.description, variable: 'description', width: 30, height: 3},
                            {type: 'date', title: language.correction_date, variable: 'correction_date', min: details.start, value: date.toDateString()},
                            {type: 'text', title: language.correction_shift, variable: 'correction_shift', limit: 5},
                            {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 3},
                            {type: 'checkbox', title: language.external_company, variable: 'external_company'},
                            {type: 'text', title: language.company_name, variable: 'company_name', limit: 255}
                        ];
                        openModalBox(language.new_suspension, fields, language.save, function(data){
                            RestApi.post('InspectorSuspensions', 'saveSuspension', data,
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
                    }, data);
                });
            });
        }
        else{
            alert(language.select_document);
        }
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            var documentId = selected.id_document;
            RestApi.get('InspectorSuspensions', 'getNewSuspensionDetails', {id_document: documentId}, function(response){
                var details = JSON.parse(response);
                var groups = [];
                details.groups.forEach(item => {
                    var group = {
                        title: item.name,
                        value: item.id
                    }
                    groups.push(group);
                });
                var fields = [
                    {type: 'select', title: language.select_suspension_group, variable: 'id_suspension_group', options: groups, required: true}
                ];
                openModalBox(language.new_suspension, fields, language.next, function(data){
                    data.id_document = documentId;
                    var date = new Date();
                    var start = new Date(details.start);
                    var end = new Date(details.end);
                    if(date < start){
                        date = start;
                    }
                    if(date > end){
                        date = end;
                    }
                    var types = [];
                    details.types.forEach(item => {
                        if(parseInt(item.id_suspension_group) === parseInt(data.id_suspension_group)){
                            var type = {
                                value: item.id,
                                title: item.name
                            }
                            types.push(type);
                        }
                    });
                    var fields = [
                        {type: 'select', title: language.select_suspension_type, variable: 'id_suspension_type', options: types, required: true}
                    ];
                    openModalBox(language.new_suspension, fields, language.next, function(data){
                        data.id_document = documentId;
                        var date = new Date();
                        var start = new Date(details.start);
                        var end = new Date(details.end);
                        if(date < start){
                            date = start;
                        }
                        if(date > end){
                            date = end;
                        }
                        var objects = [];
                        details.typeObjectRelations.forEach(item => {
                            var type = {
                                value: item.id_suspension_object,
                                title: item.suspension_object
                            }
                            if(parseInt(item.id_suspension_type) === parseInt(data.id_suspension_type)){
                                objects.push(type);
                            }
                        });
                        var reasons = [];
                        details.reasons.forEach(item => {
                            var reason = {
                                value: item.id,
                                title: item.name
                            }
                            reasons.push(reason);
                        });
                        var fields = [
                            {type: 'date', title: language.suspension_date, variable: 'date', min: details.start, max: details.end, value: date.toDateString()},
                            {type: 'select', title: language.select_suspension_object, variable: 'id_suspension_object', options: objects, required: true},
                            {type: 'select', title: language.select_suspension_reason, variable: 'id_suspension_reason', options: reasons, required: true},
                            {type: 'text', title: language.shift, variable: 'shift', limit: 5, required: true},
                            {type: 'textarea', title: language.region, variable: 'region', limit: 255, width: 30, height: 3, required: true},
                            {type: 'textarea', title: language.description, variable: 'description', width: 30, height: 3},
                            {type: 'date', title: language.correction_date, variable: 'correction_date', min: details.start, value: date.toDateString()},
                            {type: 'text', title: language.correction_shift, variable: 'correction_shift', limit: 5},
                            {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 3},
                            {type: 'checkbox', title: language.external_company, variable: 'external_company'},
                            {type: 'text', title: language.company_name, variable: 'company_name', limit: 255}
                        ];
                        openModalBox(language.new_suspension, fields, language.save, function(data){
                            RestApi.post('InspectorSuspensions', 'saveSuspension', data,
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
                    }, data);
                }, selected);
            });
        }
        else{
            alert(language.select_suspension);
        }
    });
    datatable.addActionButton(language.remove, function(selected){
        if(selected !== undefined){
            RestApi.post('InspectorSuspensions', 'removeSuspension', {id: selected.id},
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
            alert(language.select_suspension);
        }
    });
    datatable.addActionButton(language.add_sanction, function(selected){
        if(selected !== undefined){
            openAddSanctionModal(language, selected, selected.id_document);
        }
        else{
            alert(language.select_suspension);
        }
    });
    datatable.addActionButton(language.remove_sanction, function(selected){
        if(selected !== undefined){
            openRemoveSanctionModal(language, selected, selected.id_document);
        }
        else{
            alert(language.select_suspension);
        }
    });
    datatable.setOnSelect(function(selected){
        refreshAssignedArticlesList(selected.id);
        refreshAssignedTicketsList(selected.id);
        refreshAssignedDecisionsList(selected.id);
    });
    datatable.setOnUnselect(function(){
        clearLists();
    });
    return datatable;
}

function openAddSanctionModal(language, suspension, documentId){
    var options = [
        { title: language.art_41, value: 'art41'},
        { title: language.ticket, value: 'ticket'},
        { title: language.decision, value: 'decision'}
    ];
    var fields = [
        {type: 'select', title: language.select_sanction_type, variable: 'sanction_type', options: options, required: true}
    ];
    openModalBox(language.new_sanction, fields, language.next, function(data){
        switch(data.sanction_type){
            case 'art41':
                openAddArtModal(language, suspension, documentId);
                break;
            case 'ticket':
                openAddTicketModal(language, suspension, documentId);
                break;
            case 'decision':
                openDecisionModal(language, suspension, documentId);
                break;
        }
    });
}

function openAddArtModal(language, suspension, documentId){
    RestApi.get('InspectorSuspensions', 'getMyArticles', {id_document: documentId}, function(response){
        var data = JSON.parse(response);
        var articles = [];
        data.forEach(item => {
            var article = {
                title: item.date + ' ' + item.position,
                value: item.id
            }
            articles.push(article);
        });
        var fields = [
            {type: 'select', title: language.select_art_41, variable: 'id_art_41', options: articles, required: true}
        ];
        var item = { id_suspension: suspension.id };
        openModalBox(language.new_sanction, fields, language.save, function(data){
            RestApi.post('InspectorSuspensions', 'assignExistingArticle', data,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    refreshAssignedArticlesList(suspension.id);
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }, item);
    });
}

function openAddTicketModal(language, suspension, documentId){
    RestApi.get('InspectorSuspensions', 'getMyTickets', {id_document: documentId}, function(response){
        var data = JSON.parse(response);
        var tickets = [];
        data.forEach(item => {
            var ticket = {
                title: item.date + ' ' + item.number,
                value: item.id
            }
            tickets.push(ticket);
        });
        var fields = [
            {type: 'select', title: language.select_ticket, variable: 'id_ticket', options: tickets, required: true}
        ];
        var item = { id_suspension: suspension.id };
        openModalBox(language.new_sanction, fields, language.save, function(data){
            RestApi.post('InspectorSuspensions', 'assignExistingTicket', data,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    refreshAssignedTicketsList(suspension.id);
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }, item);
    });
}

function openDecisionModal(language, suspension, documentId){
    RestApi.get('InspectorSuspensions', 'getMyDecisions', {id_document: documentId}, function(response){
        var data = JSON.parse(response);
        var decisions = [];
        data.forEach(item => {
            var description = item.description;
            if(description.length > 25){
                description = description.slice(0, 25) + '...';
            }
            var decision = {
                title: item.date + ' ' + description,
                value: item.id
            }
            decisions.push(decision);
        });
        var fields = [
            {type: 'select', title: language.select_decision, variable: 'id_decision', options: decisions, required: true}
        ];
        var item = { id_suspension: suspension.id };
        openModalBox(language.new_sanction, fields, language.save, function(data){
            RestApi.post('InspectorSuspensions', 'assignExistingDecision', data,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    refreshAssignedDecisionsList(suspension.id);
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }, item);
    });
}

function openRemoveSanctionModal(language, suspension){
    var options = [
        { title: language.art_41, value: 'art41'},
        { title: language.ticket, value: 'ticket'},
        { title: language.decision, value: 'decision'}
    ];
    var fields = [
        {type: 'select', title: language.select_sanction_type, variable: 'sanction_type', options: options, required: true}
    ];
    openModalBox(language.remove_sanction, fields, language.next, function(data){
        switch(data.sanction_type){
            case 'art41':
                openRemoveArticleModal(language, suspension);
                break;
            case 'ticket':
                openRemoveTicketModal(language, suspension);
                break;
            case 'decision':
                openRemoveDecisionModal(language, suspension);
                break;
        }
    });
}

function openRemoveArticleModal(language, suspension){
    RestApi.get('InspectorSuspensions', 'getAssignedArticles', {id_suspension: suspension.id}, function(response){
        var data = JSON.parse(response);
        var articles = [];
        data.forEach(item => {
            var article = {
                title: item.art_41_date + ' ' + item.art_41_position,
                value: item.id
            }
            articles.push(article);
        });
        var fields = [
            {type: 'select', title: language.select_art_41, variable: 'id_art_41', options: articles, required: true}
        ];
        var item = { id_suspension: suspension.id };
        openModalBox(language.remove_sanction, fields, language.save, function(data){
            RestApi.post('InspectorSuspensions', 'unassignArticle', data,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    refreshAssignedArticlesList(suspension.id);
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }, item);
    });
}

function openRemoveTicketModal(language, suspension){
    RestApi.get('InspectorSuspensions', 'getAssignedTickets', {id_suspension: suspension.id}, function(response){
        var data = JSON.parse(response);
        var tickets = [];
        data.forEach(item => {
            var ticket = {
                title: item.ticket_date + ' : ' + item.ticket_number,
                value: item.id
            }
            tickets.push(ticket);
        });
        var fields = [
            {type: 'select', title: language.select_ticket, variable: 'id_ticket', options: tickets, required: true}
        ];
        var item = { id_suspension: suspension.id };
        openModalBox(language.remove_sanction, fields, language.save, function(data){
            RestApi.post('InspectorSuspensions', 'unassignTicket', data,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    refreshAssignedTicketsList(suspension.id);
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }, item);
    });
}

function openRemoveDecisionModal(language, suspension){
    RestApi.get('InspectorSuspensions', 'getAssignedDecisions', {id_suspension: suspension.id}, function(response){
        var data = JSON.parse(response);
        var decisions = [];
        data.forEach(item => {
            var decision = {
                title: item.decision_date + ' : ' + item.decision_description,
                value: item.id
            }
            decisions.push(decision);
        });
        var fields = [
            {type: 'select', title: language.select_decision, variable: 'id_decision', options: decisions, required: true}
        ];
        var item = { id_suspension: suspension.id };
        openModalBox(language.remove_sanction, fields, language.save, function(data){
            RestApi.post('InspectorSuspensions', 'unassignDecision', data,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    refreshAssignedDecisionsList(suspension.id);
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }, item);
    });
}

function refreshAssignedArticlesList(idSuspension){
    var list = document.getElementById('articlesList');
    while(list.firstChild){
        list.removeChild(list.firstChild);
    }
    
    RestApi.get('InspectorSuspensions', 'getAssignedArticles', {id_suspension: idSuspension}, function(response){
        var data = JSON.parse(response);
        data.forEach(item => {
            var li = document.createElement('li');
            li.textContent = item.art_41_date + ' : ' + item.art_41_form_name + ' - ' + item.art_41_position_group + ' : ' + item.art_41_position;
            list.appendChild(li);
        })
    });
}

function refreshAssignedTicketsList(idSuspension){
    var list = document.getElementById('ticketsList');
    while(list.firstChild){
        list.removeChild(list.firstChild);
    }
    
    RestApi.get('InspectorSuspensions', 'getAssignedTickets', {id_suspension: idSuspension}, function(response){
        var data = JSON.parse(response);
        data.forEach(item => {
            var li = document.createElement('li');
            li.textContent = item.ticket_date + ' : ' + item.ticket_number + ' - ' + item.ticket_law;
            list.appendChild(li);
        })
    });
}

function refreshAssignedDecisionsList(idSuspension){
    var list = document.getElementById('decisionsList');
    while(list.firstChild){
        list.removeChild(list.firstChild);
    }
    
    RestApi.get('InspectorSuspensions', 'getAssignedDecisions', {id_suspension: idSuspension}, function(response){
        var data = JSON.parse(response);
        data.forEach(item => {
            var li = document.createElement('li');
            li.textContent = item.decision_date + ' : ' + item.decision_description;
            list.appendChild(li);
        })
    });
}

function clearLists() {
    var articles = document.getElementById('articlesList');
    while(articles.firstChild){
        articles.removeChild(articles.firstChild);
    }
    
    var tickets = document.getElementById('ticketsList');
    while(tickets.firstChild){
        tickets.removeChild(tickets.firstChild);
    }
    
    var decisions = document.getElementById('decisionsList');
    while(decisions.firstChild){
        decisions.removeChild(decisions.firstChild);
    }
}