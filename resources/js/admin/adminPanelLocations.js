/* 
 * This code is free to use, just remember to give credit.
 */

function AdminPanelLocations(){
    var locations = document.getElementById('location');
    var locationType = document.getElementById('locationType');
    var locationGroup = document.getElementById('locationGroup');
    
    
    RestApi.getInterfaceNamesPackage(function(language){
        var locationsTable = new LocationsTable(language, locations);
        var typesTable = new LocationTypesTable(language, locationType);
        var groupsTable = new LocationGroupsTable(language, locationGroup);
    });
}

function LocationsTable(language, div){
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'name', width: 150, minWidth: 150},
            { title: language.location_group, variable: 'id_location_group', width: 50},
            { title: language.location_type, variable: 'id_location_type', width: 50}
        ],
        dataSource : { method: 'post', address: getRestAddress(), data: { controller: 'AdminPanelLocation', task: 'getLocations' } }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        RestApi.get('AdminPanelLocation', 'getActiveLocationGroups', {}, function(response){
            var groups = JSON.parse(response);
            var groupsOptions = [];
            groups.forEach(item => {
                var option = {
                    value: item.id,
                    title: item.name
                }
                groupsOptions.push(option);
            });
            RestApi.get('AdminPanelLocation', 'getActiveLocationTypes', {}, function(response){
                var types = JSON.parse(response);
                var typesOptions = [];
                types.forEach(item => {
                    var option = {
                        value: item.id,
                        title: item.name
                    }
                    typesOptions.push(option);
                });
                var fields = [
                    {type: 'text', title: language.name, variable: 'name', limit: 100},
                    {type: 'select', title: language.location_group, variable: 'id_location_group', options: groupsOptions},
                    {type: 'select', title: language.location_type, variable: 'id_location_type', options: typesOptions}
                ];
                openModalBox(language.new_location, fields, language.save, function(data){
                    RestApi.post('AdminPanelLocation', 'saveLocation', data,
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
        })
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            RestApi.get('AdminPanelLocation', 'getActiveLocationGroups', {}, function(response){
                var groups = JSON.parse(response);
                var groupsOptions = [];
                groups.forEach(item => {
                    var option = {
                        value: item.id,
                        title: item.name
                    }
                    groupsOptions.push(option);
                });
                RestApi.get('AdminPanelLocation', 'getActiveLocationTypes', {}, function(response){
                    var types = JSON.parse(response);
                    var typesOptions = [];
                    types.forEach(item => {
                        var option = {
                            value: item.id,
                            title: item.name
                        }
                        typesOptions.push(option);
                    });
                    var fields = [
                        {type: 'text', title: language.name, variable: 'name', limit: 100},
                        {type: 'select', title: language.location_group, variable: 'id_location_group', options: groupsOptions},
                        {type: 'select', title: language.location_type, variable: 'id_location_type', options: typesOptions}
                    ];
                    openModalBox(language.edit_location, fields, language.save, function(data){
                        RestApi.post('AdminPanelLocation', 'saveLocation', data,
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
            });
        }
        else{
            alert(language.select_location);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelLocation', 'changeLocationStatus', selected,
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
            alert(language.select_location)
        }
    });
}

function LocationGroupsTable(language, div){
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'name', width: 150, minWidth: 150},
            { title: language.short, variable: 'short', width: 50},
            { title: language.inspection, variable: 'inspection', width: 50}
        ],
        dataSource : { method: 'post', address: getRestAddress(), data: { controller: 'AdminPanelLocation', task: 'getLocationTypes' } }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.name, variable: 'name', limit: 255},
            {type: 'text', title: language.short, variable: 'short', limit: 100},
            {type: 'checkbox', title: language.inspection, variable: 'inspection'}
        ];
        openModalBox(language.new_location_type, fields, language.save, function(data){
            RestApi.post('AdminPanelLocation', 'saveLocationType', data,
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
        );
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            var fields = [
                {type: 'text', title: language.name, variable: 'name', limit: 255},
                {type: 'text', title: language.short, variable: 'short', limit: 100},
                {type: 'checkbox', title: language.inspection, variable: 'inspection'}
            ];
            openModalBox(language.edit_location_type, fields, language.save, function(data){
                RestApi.post('AdminPanelLocation', 'saveLocationType', data,
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
                }, selected
            );
        }
        else{
            alert(language.select_location_type);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelLocation', 'changeLocationTypeStatus', selected,
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
            alert(language.select_location_type)
        }
    });
}

function LocationTypesTable(language, div){
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'name', width: 150, minWidth: 150}
        ],
        dataSource : { method: 'post', address: getRestAddress(), data: { controller: 'AdminPanelLocation', task: 'getLocationGroups' } }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.name, variable: 'name', limit: 255}
        ];
        openModalBox(language.new_location_group, fields, language.save, function(data){
            RestApi.post('AdminPanelLocation', 'saveLocationGroup', data,
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
        );
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            var fields = [
                {type: 'text', title: language.name, variable: 'name', limit: 255}
            ];
            openModalBox(language.edit_location_group, fields, language.save, function(data){
                RestApi.post('AdminPanelLocation', 'saveLocationGroup', data,
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
                }, selected
            );
        }
        else{
            alert(language.select_location_group);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelLocation', 'changeLocationGroupStatus', selected,
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
            alert(language.select_location_group)
        }
    });
    
}
