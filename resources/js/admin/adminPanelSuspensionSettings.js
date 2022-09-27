/* 
 * This code is free to use, just remember to give credit.
 */

function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        var groupTable = initGroupTable(language);
        var objectTable = initObjectTable(language);
        initTypeTable(language, groupTable, objectTable);
        initReasonTable(language);
    });
}

function initGroupTable(language){
    var div = document.getElementById('suspensionGroup');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'name', width: 150, minWidth: 150}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelSuspension', 
                task: 'getAllSuspensionGroups' 
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.name, variable: 'name', limit: 255, required: true}
        ];
        openModalBox(language.new_suspension_group, fields, language.save, function(data){
            RestApi.post('AdminPanelSuspension', 'saveSuspensionGroup', data,
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
                {type: 'text', title: language.name, variable: 'name', limit: 255, required: true}
            ];
            openModalBox(language.edit_suspension_group, fields, language.save, function(data){
                RestApi.post('AdminPanelSuspension', 'saveSuspensionGroup', data,
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
            alert(language.select_suspension_group);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelSuspension', 'changeSuspensionGroupStatus', selected,
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
            alert(language.select_suspension_group)
        }
    });
    return datatable;
}

function initTypeTable(language, groupTable, objectTable){
    var div = document.getElementById('suspensionType');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'name', width: 150, minWidth: 150}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelSuspension', 
                task: 'getAllSuspensionTypesByGroup',
                id_suspension_suzug_group: 0
            } 
        }
    };
    var datatable = new Datatable(div, config);
    groupTable.setOnSelect(function(selected){
        if(selected !== undefined){
            datatable.setDatasource({ 
                method: 'post', 
                address: getRestAddress(), 
                data: { 
                    controller: 'AdminPanelSuspension', 
                    task: 'getAllSuspensionTypesByGroup',
                    id_suspension_suzug_group: selected.id
                } 
            });
        }
    });
    groupTable.setOnUnselect(function(){
        datatable.setDatasource({ 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelSuspension', 
                task: 'getAllSuspensionSuzugByGroup',
                id_suspension_suzug_group: 0
            } 
        });
    });
    datatable.setOnSelect(function(selected){
        RestApi.post('AdminPanelSuspension', 'getRelatedObjects', { id_suspension_type: selected.id },
            function(response){
                var objects = JSON.parse(response);
                var ids = [];
                objects.forEach(item => {
                    ids.push(item.id_suspension_object);
                });
                objectTable.selectRowsWhere('id', ids);
            },
            function(response){
                console.log(response.responseText);
        });
    });
    datatable.setOnUnselect(function(){
        objectTable.clearSelection();
    });
    datatable.addActionButton(language.add, function(){
        RestApi.post('AdminPanelSuspension', 'getActiveSuspensionGroups', {},
            function(response){
                var groups = JSON.parse(response);
                var options = [];
                groups.forEach(item => {
                    var option = {
                        value: item.id,
                        title: item.name
                    }
                    options.push(option);
                });
                var fields = [
                    {type: 'text', title: language.name, variable: 'name', limit: 255, required: true},
                    {type: 'select', title: language.select_suspension_group, variable: 'id_suspension_group', options: options, required: true}
                    
                ];
                openModalBox(language.new_suspension_type, fields, language.save, function(data){
                    RestApi.post('AdminPanelSuspension', 'saveSuspensionType', data,
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
            RestApi.post('AdminPanelSuspension', 'getActiveSuspensionGroups', {},
                function(response){
                    var groups = JSON.parse(response);
                    var options = [];
                    groups.forEach(item => {
                        var option = {
                            value: item.id,
                            title: item.name
                        }
                        options.push(option);
                    });
                    var fields = [
                        {type: 'text', title: language.name, variable: 'name', limit: 255, required: true},
                        {type: 'select', title: language.select_suspension_group, variable: 'id_suspension_group', options: options, required: true}

                    ];
                    openModalBox(language.new_suspension_type, fields, language.save, function(data){
                        RestApi.post('AdminPanelSuspension', 'saveSuspensionType', data,
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
            alert(language.select_suspension_type);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelSuspension', 'changeSuspensionTypeStatus', selected,
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
            alert(language.select_suspension_type)
        }
    });
    datatable.addActionButton(language.save_relations, function(selected){
        if(selected !== undefined){
            var data = {
                id_suspension_type: selected.id,
                objects_ids: []
            }
            var objects = objectTable.getSelected();
            objects.forEach(item => {
                data.objects_ids.push(item.id);
            });
            RestApi.post('AdminPanelSuspension', 'saveTypeObjectsRelations', data,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                    objectTable.clearSelection();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_suspension_type)
        }
    });
}

function initReasonTable(language){
    
    var div = document.getElementById('suspensionReason');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'name', width: 150, minWidth: 150}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelSuspension', 
                task: 'getAllSuspensionReasons' 
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.name, variable: 'name', limit: 255, required: true}
        ];
        openModalBox(language.new_suspension_reason, fields, language.save, function(data){
            RestApi.post('AdminPanelSuspension', 'saveSuspensionReason', data,
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
                {type: 'text', title: language.name, variable: 'name', limit: 255, required: true}
            ];
            openModalBox(language.edit_suspension_reason, fields, language.save, function(data){
                RestApi.post('AdminPanelSuspension', 'saveSuspensionReason', data,
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
            alert(language.select_suspension_reason);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelSuspension', 'changeSuspensionReasonStatus', selected,
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
            alert(language.select_suspension_reason);
        }
    });
}

function initObjectTable(language){
    
    var div = document.getElementById('suspensionObject');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'name', width: 150, minWidth: 150}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelSuspension', 
                task: 'getAllSuspensionObjects' 
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.enableSelectMultiple();
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.name, variable: 'name', limit: 255, required: true}
        ];
        openModalBox(language.new_suspension_object, fields, language.save, function(data){
            RestApi.post('AdminPanelSuspension', 'saveSuspensionObject', data,
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
        if(selected !== undefined && selected.length === 1){
            var item = selected[0];
            var fields = [
                {type: 'text', title: language.name, variable: 'name', limit: 255, required: true}
            ];
            openModalBox(language.edit_suspension_object, fields, language.save, function(data){
                RestApi.post('AdminPanelSuspension', 'saveSuspensionObject', data,
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
            }, item);
        }
        else{
            alert(language.select_suspension_object);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined && selected.length === 1){
            var item = selected[0];
            RestApi.post('AdminPanelSuspension', 'changeSuspensionObjectStatus', item,
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
            alert(language.select_suspension_object);
        }
    });
    return datatable;
}