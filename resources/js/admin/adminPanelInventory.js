/* 
 * This code is free to use, just remember to give credit.
 */


function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        initEquipmentTypeTable(language);
    });
}

function initEquipmentTypeTable(language){
    var div = document.getElementById('equipmentType');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'name', width: 150, minWidth: 150},
            { title: language.measurement_instrument, variable: 'measurement_instrument', width: 50}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelInventory', 
                task: 'getEquipmentTypes' 
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.name, variable: 'name', limit: 255, required: true},
            {type: 'checkbox', title: language.measurement_instrument, variable: 'measurement_instrument'}
        ];
        openModalBox(language.new_equipment_type, fields, language.save, function(data){
            RestApi.post('AdminPanelInventory', 'saveEquipmentType', data,
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
                {type: 'text', title: language.name, variable: 'name', limit: 255, required: true},
                {type: 'checkbox', title: language.measurement_instrument, variable: 'measurement_instrument'}
            ];
            openModalBox(language.edit_equipment_type, fields, language.save, function(data){
                RestApi.post('AdminPanelInventory', 'saveEquipmentType', data,
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
            alert(language.select_equipment_type);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelInventory', 'changeEquipmentTypeStatus', selected,
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
            alert(language.select_equipment_type)
        }
    });
}