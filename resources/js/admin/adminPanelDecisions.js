/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        initDecisionLawTable(language);
        initDecisionsTable(language);
    });
}

function initDecisionLawTable(language) {
    var div = document.getElementById('decisionLaws');
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30, minWidth: 30},
            { title: language.active, variable: 'active', width: 50, minWidth: 50},
            { title: language.decision_law, variable: 'law', width: 150, minWidth: 150},
            { title: language.description, variable: 'description', width: 250, minWidth: 250},
            { title: language.requires_suspension, variable: 'requires_suspension', width: 50, minWidth: 50}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelDecisions', 
                task: 'getAllDecisionLaws' 
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.decision_law, variable: 'law', limit: 100, required: true},
            {type: 'text', title: language.description, variable: 'description', limit: 255, required: true},
            {type: 'checkbox', title: language.requires_suspension, variable: 'requires_suspension'}
        ];
        openModalBox(language.new_decision_law, fields, language.save, function(data){
            RestApi.post('AdminPanelDecisions', 'saveDecisionLaw', data,
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
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            var fields = [
                {type: 'text', title: language.decision_law, variable: 'law', limit: 100, required: true},
                {type: 'text', title: language.description, variable: 'description', limit: 255, required: true},
                {type: 'checkbox', title: language.requires_suspension, variable: 'requires_suspension'}
            ];
            openModalBox(language.new_decision_law, fields, language.save, function(data){
                RestApi.post('AdminPanelDecisions', 'saveDecisionLaw', data,
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
        }
        else{
            alert(language.select_decision_law);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelDecisions', 'changeDecisionLawStatus', selected,
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
            alert(language.select_activity)
        }
    });
}

function initDecisionsTable(language){
    var div = document.getElementById('decisions');
    var selectUser = document.getElementById('selectUser');
    var selectYear = document.getElementById('selectYear');
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30, minWidth: 30},
            { title: language.active, variable: 'active', width: 50, minWidth: 50},
            { title: language.document_number, variable: 'document_number', width: 150, minWidth: 150},
            { title: language.date, variable: 'date', width: 100, minWidth: 100},
            { title: language.decision_law, variable: 'law', width: 150, minWidth: 150},
            { title: language.remarks, variable: 'remarks', width: 250, minWidth: 250},
            { title: language.location, variable: 'location', width: 250, minWidth: 250}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelDecisions', 
                task: 'getAllUserDecisionsByYear',
                username: selectUser.value,
                year: selectYear.value
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            var documentId = selected.id_document;
            RestApi.get('AdminPanelDecisions', 'getEditDecisionDetails', {id_document: documentId}, 
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
                        RestApi.post('AdminPanelDecisions', 'saveDecision', data,
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
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelDecisions', 'changeDecisionStatus', selected,
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
            alert(language.select_decision)
        }
    });
    
    var selectDate = document.getElementById('selectDate');
    selectDate.onclick = function(){
        datatable.setDatasource({ 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelDecisions', 
                task: 'getAllUserDecisionsByYear',
                username: selectUser.value,
                year: selectYear.value
            } 
        });
    }
}