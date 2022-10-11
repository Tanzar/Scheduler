/* 
 * This code is free to use, just remember to give credit.
 */

function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        var table = new SuspensionTable(language);
        
        document.getElementById('selectDate').onclick = function(){
            var username = document.getElementById('selectUser').value;
            var year = document.getElementById('selectYear').value;
            table.refresh(username, year);
        }
    });
}

function SuspensionTable(language){
    var div = document.getElementById('suspensions');
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'AdminPanelSuspension',
            task: 'getUserSuspensions',
            username: '',
            year: 0
        }
    };
    var config = {
        columns: [
            {title: 'ID', variable: 'id', width: 30, minWidth: 30},
            {title: language.active, variable: 'active', width: 50, minWidth: 50},
            {title: language.document_number, variable: 'document_number', width: 150, minWidth: 150},
            {title: language.date, variable: 'date', width: 100, minWidth: 100},
            {title: language.region, variable: 'region', width: 150, minWidth: 150},
            {title: language.description, variable: 'description', width: 300, minWidth: 300}
        ],
        dataSource: datasource
    }
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.edit, function(selected){
        if(selected === undefined){
            alert(language.select_suspension);
        }
        else{
            var idDocument = selected.id_document;
            RestApi.get('AdminPanelSuspension', 'getEditSuspensionDetails', {id_document: idDocument}, function(response){
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
                            {type: 'text', title: language.correction_shift, variable: 'correction_shift', limit: 5, required: true},
                            {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 3},
                            {type: 'checkbox', title: language.external_company, variable: 'external_company'},
                            {type: 'text', title: language.company_name, variable: 'company_name', limit: 255}
                        ];
                        openModalBox(language.new_suspension, fields, language.save, function(data){
                            RestApi.post('AdminPanelSuspension', 'saveSuspension', data,
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
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelSuspension', 'changeSuspensionStatus', {id: selected.id},
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
    
    
    this.refresh = function(username, year){
        datatable.setDatasource({
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'AdminPanelSuspension',
                task: 'getUserSuspensions',
                username: username,
                year: year
            }
        });
    }
}