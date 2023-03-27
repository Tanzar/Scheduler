/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        var myEquipmentTable = initAssignedTable(language);
        initUnconfirmedTable(language, myEquipmentTable);
    });
}

function initAssignedTable(language) {
    var div = document.getElementById('assigned');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'InventoryMy',
            task: 'getMyEquipment'
        }
    };
    var config = {
        columns: [
            {title: language.inventory_number, variable: 'inventory_number', width: 70, minWidth: 70},
            {title: language.name, variable: 'name', width: 300, minWidth: 300},
            {title: language.equipment_type, variable: 'equipment_type', width: 100, minWidth: 100}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.assign, function(selected){
        if(selected !== undefined){
            RestApi.get('InventoryMy', 'getUsers', {}, function(response){
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
                var dataToSend = {
                    id: selected.id
                }
                openModalBox(language.select_user, fields, language.save, function(data){
                    RestApi.post('InventoryMy', 'assignToUser', data,
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
                }, dataToSend);
            });
        }
        else{
            alert(language.select_equipment);
        }
    });
    return datatable;
}

function initUnconfirmedTable(language, myEquipmentTable) {
    var div = document.getElementById('unconfirmed');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'InventoryMy',
            task: 'getMyUnconfirmedEquipment'
        }
    };
    var config = {
        columns: [
            {title: language.inventory_number, variable: 'inventory_number', width: 70, minWidth: 70},
            {title: language.name, variable: 'equipment_name', width: 300, minWidth: 300},
            {title: language.equipment_type, variable: 'equipment_type', width: 100, minWidth: 100}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.confirm, function(selected){
        if(selected !== undefined){
            RestApi.post('InventoryMy', 'confirmAssign', {id: selected.id},
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                    myEquipmentTable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_equipment);
        }
    });
    datatable.addActionButton(language.reject, function(selected){
        if(selected !== undefined){
            RestApi.post('InventoryMy', 'cancelAssign', selected,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                    myEquipmentTable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_equipment);
        }
    });
}