/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        var table = new ApplicationsTable(language);
        
        document.getElementById('selectDate').onclick = function(){
            var username = document.getElementById('selectUser').value;
            var year = document.getElementById('selectYear').value;
            table.refresh(username, year);
        }
    });
}

function ApplicationsTable(language) {
    var div = document.getElementById('court');
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'AdminPanelCourt',
            task: 'getCourtApplications',
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
            {title: language.accusation, variable: 'accusation', width: 100, minWidth: 100},
            {title: language.position, variable: 'position', width: 150, minWidth: 150},
            {title: language.position_groups, variable: 'position_group', width: 100, minWidth: 100},
            {title: language.external_company, variable: 'external_company_text', width: 100, minWidth: 100},
            {title: language.company_name, variable: 'company_name', width: 100, minWidth: 100},
            {title: language.remarks, variable: 'remarks', width: 200, minWidth: 200}
        ],
        dataSource: datasource
    }
    var datatable = new Datatable(div, config);
    
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            RestApi.get('AdminPanelCourt', 'getEditApplicationDetails', {id_document: selected.id_document}, function(response){
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
                    RestApi.post('AdminPanelCourt', 'saveCourtApplication', data,
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
            alert(language.select_court_application);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelCourt', 'changeApplicationStatus', {id: selected.id},
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
            alert(language.select_court_application);
        }
    });
    
    
    this.refresh = function(username, year){
        datatable.setDatasource({
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'AdminPanelCourt',
                task: 'getCourtApplications',
                username: username,
                year: year
            }
        });
    }
}