/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        var datatable = initTable(language)
        initFilter(language, datatable);
    });
}

function initTable(language){
    var div = document.getElementById('logsTable');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'InventoryLog',
            task: 'getTodayLogs'
        }
    };
    var config = {
        columns: [
            {title: language.date, variable: 'save_time', width: 150, minWidth: 150},
            {title: language.operation, variable: 'operation', width: 100, minWidth: 100},
            {title: language.inventory_number, variable: 'inventory_number', width: 150, minWidth: 150},
            {title: language.name, variable: 'equipment_name', width: 300, minWidth: 300},
            {title: language.source_user, variable: 'source_user_name_full', width: 120, minWidth: 120},
            {title: language.target_user, variable: 'target_user_name_full', width: 120, minWidth: 120},
            {title: language.confirmation, variable: 'confirmation_text', width: 100, minWidth: 100}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
    return datatable;
}

function initFilter(language, datatable){
    var div = document.getElementById('filters');
    var datafilter = new DataFilters(div, function(filters){
        if(filters.length === 0){
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'InventoryLog',
                    task: 'getTodayLogs'
                }
            });
        }
        else{
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'InventoryLog',
                    task: 'getFilteredLogs',
                    filters: filters
                }
            });
        }
    });
    
    $('#addFilter').click(function(){
        var options = [
            { title: language.date, value: 'date' },
            { title: language.source_user, value: 'sourceUser' },
            { title: language.target_user, value: 'targetUser' },
            { title: language.inventory_number, value: 'inventoryNumber' },
            { title: language.name, value: 'equipmentName' },
            { title: language.operation, value: 'operation' },
            { title: language.confirmation, value: 'confirmation' }
        ];
        var fields = [
            {type: 'select', title: language.search_by, variable: 'option', options: options, required: true}
        ];
        openModalBox(language.add_filter, fields, language.next, function(data){
            switch(data.option){
                case 'date':
                    addDateFilter(language, datafilter);
                    break;
                case 'sourceUser':
                    addSourceUserFilter(language, datafilter);
                    break;
                case 'targetUser':
                    addTargetUserFilter(language, datafilter);
                    break;
                case 'inventoryNumber':
                    addInventoryNumberFilter(language, datafilter);
                    break;
                case 'equipmentName':
                    addEquipmentNameFilter(language, datafilter);
                    break;
                case 'operation':
                    addOperationFilter(language, datafilter);
                    break;
                case 'confirmation':
                    addConfirmationFilter(language, datafilter);
                    break;
            }
        });
    });
}

function addDateFilter(language, datafilter){
    var options = [
        { title: language.equal, value: 'equal' },
        { title: language.before, value: 'before' },
        { title: language.after, value: 'after' },
    ];
    var fields = [
        {type: 'select', title: language.select_compare, variable: 'option', options: options, required: true},
        {type: 'date', title: language.date, variable: 'date'}
    ];
    openModalBox(language.add_filter, fields, language.next, function(data){
        switch(data.option){
            case 'equal':
                datafilter.addEqual('save_time_date', data.date, data.date);
                break;
            case 'before':
                datafilter.addLess('save_time_date', data.date, language.before + ' ' + data.date);
                break;
            case 'after':
                datafilter.addEqual('save_time_date', data.date, language.after + ' ' + data.date);
                break;
        }
    });
}

function addSourceUserFilter(language, datafilter){
    RestApi.get('InventoryLog', 'getUsers', {}, function(response){
        var data = JSON.parse(response);
        var users = [];
        data.forEach(user => {
            var option = {
                value: user.username,
                title: user.name + ' ' + user.surname
            }
            users.push(option);
        });
        var fields = [
            {type: 'select', title: language.select_user, variable: 'username', options: users, required: true}
        ];
        openModalBox(language.add_filter, fields, language.next, function(data){
            datafilter.addEqual('source_username', data.username, language.source_user + ': ' + data.username);
        });
    });
}

function addTargetUserFilter(language, datafilter){
    RestApi.get('InventoryLog', 'getUsers', {}, function(response){
        var data = JSON.parse(response);
        var users = [];
        data.forEach(user => {
            var option = {
                value: user.username,
                title: user.name + ' ' + user.surname
            }
            users.push(option);
        });
        var fields = [
            {type: 'select', title: language.select_user, variable: 'username', options: users, required: true}
        ];
        openModalBox(language.add_filter, fields, language.next, function(data){
            datafilter.addEqual('target_username', data.username, language.target_user + ': ' + data.username);
        });
    });
}

function addInventoryNumberFilter(language, datafilter){
    var fields = [
        {type: 'text', title: language.inventory_number, variable: 'inventoryNumber', limit: 12, required: true}
    ];
    openModalBox(language.add_filter, fields, language.next, function(data){
        datafilter.addContains('inventory_number', data.inventoryNumber, language.inventory_number + ' ' + data.inventoryNumber);
    });
}

function addEquipmentNameFilter(language, datafilter){
    var fields = [
        {type: 'text', title: language.name, variable: 'name', limit: 12, required: true}
    ];
    openModalBox(language.add_filter, fields, language.next, function(data){
        datafilter.addContains('equipment_name', data.name, language.name + ' ' + data.name);
    });
}

function addOperationFilter(language, datafilter){
    var options = [
        { title: language.new, value: 'new' },
        { title: language.assign, value: 'assign' },
        { title: language.repair, value: 'repair' },
        { title: language.calibration, value: 'calibration' },
        { title: language.liquidation, value: 'liquidation' },
        { title: language.rejection, value: 'cancel_assign' }
    ];
    var fields = [
        {type: 'select', title: language.select_operation, variable: 'operation', options: options, required: true}
    ];
    openModalBox(language.add_filter, fields, language.next, function(data){
        datafilter.addEqual('operation', data.operation, language.operation + ' ' + data.operation);
    });
}

function addConfirmationFilter(language, datafilter){
    var fields = [
        {type: 'checkbox', title: language.confirmation, variable: 'confirmation'}
    ];
    openModalBox(language.add_filter, fields, language.next, function(data){
        var text = language.no;
        if(data.confirmation){
            text = language.yes;
        }
        datafilter.addContains('confirmation', data.confirmation, language.confirmation + ' ' + text);
    });
}