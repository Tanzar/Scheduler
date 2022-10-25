/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        var table = new UsagesTable(language);
        
        document.getElementById('selectDate').onclick = function(){
            var username = document.getElementById('selectUser').value;
            var year = document.getElementById('selectYear').value;
            table.refresh(username, year);
        }
    });
}

function UsagesTable(language) {
    var div = document.getElementById('usages');
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'AdminPanelUsages',
            task: 'getUsages',
            username: '',
            year: 0
        }
    };
    var config = {
        columns: [
            {title: 'ID', variable: 'id', width: 30, minWidth: 30},
            {title: language.active, variable: 'active', width: 50, minWidth: 50},
            {title: language.document_number, variable: 'document_number', width: 150, minWidth: 150},
            {title: language.equipment_name, variable: 'equipment_name', width: 150, minWidth: 150},
            {title: language.inventory_number, variable: 'inventory_number', width: 150, minWidth: 150},
            {title: language.date, variable: 'date', width: 100, minWidth: 100},
            {title: language.recommendation_decision, variable: 'recommendation_decision_text', width: 150, minWidth: 150},
            {title: language.remarks, variable: 'remarks', width: 200, minWidth: 200}
        ],
        dataSource: datasource
    }
    var datatable = new Datatable(div, config);
    
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            RestApi.get('AdminPanelUsages', 'getEditUsageDetails', {id_document: selected.id_document}, function(response){
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
                    RestApi.post('AdminPanelUsages', 'updateUsage', data,
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
            alert(language.select_equipment_usage);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelUsages', 'changeUsageStatus', {id: selected.id},
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
            alert(language.select_equipment_usage);
        }
    });
    
    
    this.refresh = function(username, year){
        datatable.setDatasource({
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'AdminPanelUsages',
                task: 'getUsages',
                username: username,
                year: year
            }
        });
    }
}