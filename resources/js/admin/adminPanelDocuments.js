/* 
 * This code is free to use, just remember to give credit.
 */

function AdminPanelDocuments(){
    RestApi.getInterfaceNamesPackage(function(language){
        var documents = documentsTable(language);
        var users = new AssignedUsersTable(language, documents);
        
        documents.addActionButton(language.edit, function(selected){
            if(selected !== undefined){
                RestApi.get('InspectorDocuments', 'getInspectionLocationTypes', {}, 
                    function(response){
                        var data = JSON.parse(response);
                        var options = [];
                        data.forEach(item => {
                            var option = {
                                title: item.name,
                                value: item.id
                            }
                            options.push(option);
                        });
                        var fields = [{type: 'select', title: language.select_location_type, variable: 'id_location_type', options: options, required: true}];
                        openModalBox(language.select_location_type, fields, language.next, 
                            function(data){
                                var toSend = {id_location_type: data.id_location_type};
                                RestApi.get('InspectorDocuments', 'getLocationsByTypeId', toSend, 
                                        function(response){
                                            var locations = JSON.parse(response);
                                            console.log(locations);
                                            var options = [];
                                            locations.forEach(item => {
                                                var option = {
                                                    title: item.name,
                                                    value: item.id
                                                }
                                                options.push(option);
                                            });
                                            var fields = [
                                                {type: 'select', title: language.select_location, variable: 'id_location', options: options, required: true},
                                                {type: 'text', title: language.document_number, variable: 'number', limit: 255, required: true},
                                                {type: 'date', title: language.start, variable: 'start'},
                                                {type: 'date', title: language.end, variable: 'end'},
                                                {type: 'text', title: language.description, variable: 'description', limit: 255}
                                            ];
                                            openModalBox(language.new_document, fields, language.save, function(data){
                                                RestApi.post('AdminPanelDocuments', 'saveDocument', data, 
                                                    function(response){
                                                        var data = JSON.parse(response);
                                                        console.log(data);
                                                        alert(data.message);
                                                        documents.refresh();
                                                    }, function(response){
                                                        console.log(response.responseText);
                                                        alert(response.responseText);
                                                });
                                            }, selected);
                                    });

                        }, selected);
                });
            }
            else{
                alert(language.select_document);
            }
        });
        
        documents.addActionButton(language.change_status, function(selected){
            if(selected !== undefined){
                RestApi.post('AdminPanelDocuments', 'changeDocumentStatus', selected,
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        documents.refresh();
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
        
        documents.setOnSelect(function(selected){
            users.refresh(selected.id);
        });
        
        var goto = document.getElementById('selectDate');
        goto.onclick = function(){
            var selectMonth = document.getElementById('selectMonth');
            var selectYear = document.getElementById('selectYear');
            var datasource = {
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'AdminPanelDocuments',
                    task: 'getDocuments',
                    month: selectMonth.value,
                    year: selectYear.value
                }
            };
            documents.setDatasource(datasource);
            users.refresh(0);
        }
    });
}

function documentsTable(language) {
    var selectMonth = document.getElementById('selectMonth');
    var selectYear = document.getElementById('selectYear');
    var documents = document.getElementById('documents');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'AdminPanelDocuments',
            task: 'getDocuments',
            month: selectMonth.value,
            year: selectYear.value
        }
    };
    var config = {
        columns: [
            {title: 'ID', variable: 'id', width: 30, minWidth: 30},
            {title: language.active, variable: 'active', width: 50, minWidth: 50},
            {title: language.document_number, variable: 'number', width: 150, minWidth: 150},
            {title: language.start, variable: 'start', width: 150, minWidth: 150},
            {title: language.end, variable: 'end', width: 150, minWidth: 150}
        ],
        dataSource: datasource
    }
    var documentsTable = new Datatable(documents, config);
    return documentsTable;
}

function AssignedUsersTable(language, documentsTable){
    var div = document.getElementById('assignedUsers');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'AdminPanelDocuments',
            task: 'getDocumentUsers',
            id: 0
        }
    };
    var config = {
        columns: [
            {title: language.name, variable: 'name', width: 150, minWidth: 150},
            {title: language.surname, variable: 'surname', width: 150, minWidth: 150}
        ],
        dataSource: datasource
    }
    var table = new Datatable(div, config);
    table.addActionButton(language.assign, function(){
        var selected = documentsTable.getSelected();
        if(selected !== undefined){
            RestApi.get('AdminPanelDocuments', 'getUsers', {},
                function(response){
                    var users = JSON.parse(response);
                    var options = [];
                    users.forEach(user => {
                        var option = {
                            value: user.username,
                            title: user.name + ' ' + user.surname
                        };
                        options.push(option);
                    });
                    var fields = [
                        {type: 'select', title: language.select_user, variable: 'username', options: options, required: true}
                    ];
                    openModalBox(language.new_decision, fields, language.save, function(data){
                        var dataToSend = {
                            username: data.username,
                            id: selected.id
                        }
                        RestApi.post('AdminPanelDocuments', 'assignUserToDocument', dataToSend,
                            function(response){
                                var data = JSON.parse(response);
                                console.log(data);
                                alert(data.message);
                                table.refresh();
                            },
                            function(response){
                                console.log(response.responseText);
                                alert(response.responseText);
                        });
                    });
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_document);
        }
    });
    
    table.addActionButton(language.unassign, function(selected){
        if(selected !== undefined){
            if(confirm(language.document_unassign_warning)){
                var dataToSend = {
                    username: selected.username,
                    id: selected.id
                }
                RestApi.post('AdminPanelDocuments', 'unassignUserFromDocument', dataToSend,
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        table.refresh();
                    },
                    function(response){
                        console.log(response.responseText);
                        alert(response.responseText);
                });
            }
        }
        else{
            alert(language.select_user);
        }
    });
    
    
    this.refresh = function(id){
        var datasource = {
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'AdminPanelDocuments',
                task: 'getDocumentUsers',
                id: id
            }
        };
        table.setDatasource(datasource);
    }
}
