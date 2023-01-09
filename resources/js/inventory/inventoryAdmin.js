/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        var datatable = initEquipmentTable(language);
        initSearchButtons(language, datatable);
    });
    
}

function initEquipmentTable(language) {
    var div = document.getElementById('equipmentTable');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'InventoryAdmin',
            task: 'getAllEquipment'
        }
    };
    var config = {
        columns: [
            {title: language.inventory_number, variable: 'inventory_number', width: 70, minWidth: 70},
            {title: language.name, variable: 'name', width: 250, minWidth: 250},
            {title: language.equipment_type, variable: 'equipment_type', width: 100, minWidth: 100},
            {title: language.equipment_state, variable: 'state', width: 100, minWidth: 100},
            {title: language.responsible, variable: 'full_user_name', width: 70, minWidth: 70},
            {title: language.price, variable: 'price', width: 100, minWidth: 100},
            {title: language.remarks, variable: 'remarks', width: 100, minWidth: 100}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.new_equipment, function(){
        RestApi.get('InventoryAdmin', 'getNewEquipmentDetails', {}, function(response){
            var data = JSON.parse(response);
            var users = [];
            data.users.forEach(user => {
                var option = {
                    value: user.id,
                    title: user.name + ' ' + user.surname
                }
                users.push(option);
            });
            var types = [];
            data.types.forEach(type => {
                var option = {
                    value: type.id,
                    title: type.name
                }
                types.push(option);
            });
            var fields = [
                {type: 'select', title: language.select_user, variable: 'id_user', options: users, required: true},
                {type: 'text', title: language.inventory_number, variable: 'inventory_number', limit: 12, required: true},
                {type: 'textarea', title: language.name, variable: 'name', limit: 255, width: 30, height: 5, required: true},
                {type: 'text', title: language.document, variable: 'document', limit: 20, required: true},
                {type: 'date', title: language.start_date, variable: 'start_date'},
                {type: 'select', title: language.select_equipment_type, variable: 'id_equipment_type', options: types, required: true},
                {type: 'number', title: language.price, variable: 'price', min: 0, step: 0.01, required: true},
                {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 5}
            ];
            openModalBox(language.new_equipment, fields, language.save, function(data){
                RestApi.post('InventoryAdmin', 'saveNewEquipment', data,
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
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            RestApi.get('InventoryAdmin', 'getNewEquipmentDetails', {}, function(response){
                var data = JSON.parse(response);
                var types = [];
                data.types.forEach(type => {
                    var option = {
                        value: type.id,
                        title: type.name
                    }
                    types.push(option);
                });
                var fields = [
                    {type: 'text', title: language.inventory_number, variable: 'inventory_number', limit: 12, required: true},
                    {type: 'textarea', title: language.name, variable: 'name', limit: 255, width: 30, height: 5, required: true},
                    {type: 'text', title: language.document, variable: 'document', limit: 20, required: true},
                    {type: 'date', title: language.start_date, variable: 'start_date'},
                    {type: 'select', title: language.select_equipment_type, variable: 'id_equipment_type', options: types, required: true},
                    {type: 'number', title: language.price, variable: 'price', min: 0, step: 0.01, required: true},
                    {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 5}
                ];
                openModalBox(language.edit_equipment, fields, language.save, function(data){
                    RestApi.post('InventoryAdmin', 'editEquipment', data,
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
            alert(language.select_equipment);
        }
    });
    datatable.addActionButton(language.assign, function(selected){
        if(selected !== undefined){
            RestApi.get('InventoryAdmin', 'getUsersExcept', {username: selected.username}, function(response){
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
                    RestApi.post('InventoryAdmin', 'assignToUser', data,
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
    datatable.addActionButton(language.repair, function(selected){
        if(selected !== undefined){
            if(selected.state === 'list'){
                var fields = [
                    {type: 'date', title: language.date, variable: 'date'},
                    {type: 'text', title: language.document, variable: 'document', limit: 30, required: true}
                ];
                var dataToSend = {
                    id_equipment: selected.id
                }
                openModalBox(language.send_to_repair, fields, language.save, function(data){
                    RestApi.post('InventoryAdmin', 'sendToRepair', data,
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
            }
            else{
                if(selected.state === 'repair'){
                    RestApi.post('InventoryAdmin', 'returnFromRepair', {id_equipment: selected.id},
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
                    alert(language.select_equipment);
                }
            }
        }
        else{
            alert(language.select_equipment);
        }
    });
    datatable.addActionButton(language.calibration, function(selected){
        if(selected !== undefined && selected.measurement_instrument === 1){
            if(selected.state === 'list'){
                var fields = [
                    {type: 'date', title: language.date, variable: 'date'},
                    {type: 'text', title: language.document, variable: 'document', limit: 30, required: true}
                ];
                var dataToSend = {
                    id_equipment: selected.id
                }
                openModalBox(language.calibration, fields, language.save, function(data){
                    RestApi.post('InventoryAdmin', 'sendToCalibration', data,
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
            }
            else{
                if(selected.state === 'calibration'){
                    RestApi.post('InventoryAdmin', 'returnFromCalibration', {id_equipment: selected.id},
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
                    alert(language.select_equipment);
                }
            }
        }
        else{
            alert(language.select_equipment);
        }
    });
    datatable.addActionButton(language.liquidation, function(selected){
        if(selected !== undefined){
            if(selected.state === 'list'){
                var fields = [
                    {type: 'date', title: language.date, variable: 'date'},
                    {type: 'text', title: language.document, variable: 'document', limit: 30, required: true}
                ];
                var dataToSend = {
                    id_equipment: selected.id
                }
                openModalBox(language.liquidation, fields, language.save, function(data){
                    RestApi.post('InventoryAdmin', 'liquidation', data,
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
            }
            else{
                if(selected.state === 'liquidation'){
                    var fields = [
                        {type: 'text', title: language.confirm, variable: 'confirm', limit: 30, required: true}
                    ];
                    openModalBox(language.liquidation, fields, language.save, function(data){
                        if(data.confirm === 'tak'){
                            RestApi.post('InventoryAdmin', 'returnFromRepair', {id_equipment: selected.id},
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
                    });
                }
                else{
                    alert(language.select_equipment);
                }
            }
        }
        else{
            alert(language.select_equipment);
        }
    });
    datatable.addActionButton(language.count, function(){
        var summary = {
            totalCount: 0,
            totalValue: 0
        }
        var data = datatable.getData();
        data.forEach(item => {
            summary.totalCount++;
            summary.totalValue += Number.parseFloat(item.price);
        });
        var fields = [
            {type: 'display', title: language.total_count + ': ' + summary.totalCount},
            {type: 'display', title: language.total_value + ': ' + summary.totalValue}
        ];
        openModalBox(language.count, fields);
    });
    
    
    return datatable;
}

function initSearchButtons(language, datatable){
    
    $('#search').click(function(){
        var options = [
            {value: 'user', title: language.user},
            {value: 'type', title: language.equipment_type},
            {value: 'state', title: language.equipment_state},
            {value: 'number', title: language.inventory_number},
            {value: 'remarks', title: language.remarks}
        ];
        var fields = [
            {type: 'select', title: language.search_by, variable: 'searchType', options: options, required: true}
        ];
        openModalBox(language.search_by, fields, language.search, function(data){
            switch(data.searchType){
                case 'user':
                    searchByUser(language, datatable);
                    break;
                case 'type':
                    searchByType(language, datatable);
                    break;
                case 'state':
                    searchByState(language, datatable);
                    break;
                case 'number':
                    searchByNumber(language, datatable);
                    break;
                case 'remarks':
                    searchByRemarks(language, datatable);
                    break;
            }
        });
    });
    
    $('#selectReset').click(function(){
        datatable.setDatasource({
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'InventoryAdmin',
                task: 'getAllEquipment'
            }
        });
    });
}

function searchByUser(language, datatable){
    RestApi.get('InventoryAdmin', 'getUsersList', {}, function(response){
        var data = JSON.parse(response);
        var options = [];
        data.forEach(user => {
            var option = {
                value: user.username,
                title: user.name + ' ' + user.surname
            }
            options.push(option);
        });
        var fields = [
            {type: 'select', title: language.select_user, variable: 'username', options: options, required: true}
        ];
        openModalBox(language.search_by_user, fields, language.search, function(data){
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'InventoryAdmin',
                    task: 'getUserEquipment',
                    username: data.username
                }
            });
        });
    });
}

function searchByType(language, datatable){
    RestApi.get('InventoryAdmin', 'getEquipmentTypes', {}, function(response){
        var data = JSON.parse(response);
        var options = [];
        data.forEach(type => {
            var option = {
                value: type.id,
                title: type.name
            }
            options.push(option);
        });
        var fields = [
            {type: 'select', title: language.select_equipment_type, variable: 'id_equipment_type', options: options, required: true}
        ];
        openModalBox(language.search_by_type, fields, language.search, function(data){
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'InventoryAdmin',
                    task: 'getEquipmentByType',
                    id_equipment_type: data.id_equipment_type
                }
            });
        });
    });
}

function searchByState(language, datatable){
    RestApi.get('InventoryAdmin', 'getEquipmentStates', {}, function(response){
        var data = JSON.parse(response);
        var options = [
            {value: 'list', title: data.list},
            {value: 'repair', title: data.repair},
            {value: 'calibration', title: data.calibration},
            {value: 'liquidation', title: data.liquidation}
        ];
        var fields = [
            {type: 'select', title: language.select_equipment_state, variable: 'id_equipment_state', options: options, required: true}
        ];
        openModalBox(language.search_by_state, fields, language.search, function(data){
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'InventoryAdmin',
                    task: 'getEquipmentByState',
                    id_equipment_state: data.id_equipment_state
                }
            });
        });
    });
}

function searchByNumber(language, datatable){
    var fields = [
        {type: 'text', title: language.inventory_number, variable: 'inventory_number', limit: 12, required: true}
    ];
    openModalBox(language.inventory_number, fields, language.search, function(data){
        datatable.setDatasource({
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'InventoryAdmin',
                task: 'getEquipmentByInventoryNumber',
                inventory_number: data.inventory_number
            }
        });
    });
}

function searchByRemarks(language, datatable){
    var fields = [
        {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 5, required: true}
    ];
    openModalBox(language.remarks, fields, language.search, function(data){
        datatable.setDatasource({
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'InventoryAdmin',
                task: 'getEquipmentByRemarks',
                remarks: data.remarks
            }
        });
    });
}