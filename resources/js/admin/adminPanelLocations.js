/* 
 * This code is free to use, just remember to give credit.
 */

function AdminPanelLocations(){
    var locations = document.getElementById('location');
    
    
    
    RestApi.getInterfaceNamesPackage(function(language){
        var locationsTable = new LocationsTable(language, locations);
    });
}

function LocationsTable(language, div){
    var selectLocationType = document.getElementById('selectLocationType');
    var idLocationType = selectLocationType.value;
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'name', width: 150, minWidth: 150},
            { title: language.start, variable: 'active_from', width: 150, minWidth: 150},
            { title: language.end, variable: 'active_to', width: 150, minWidth: 150},
            { title: language.location_group, variable: 'location_group', width: 150, minWidth: 150},
            { title: language.location_type, variable: 'location_type', width: 150, minWidth: 150}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelLocation', 
                task: 'getLocationsByGroupId', 
                id_location_group: idLocationType 
            } 
        }
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
                    {type: 'text', title: language.name, variable: 'name', limit: 100, required: true},
                    {type: 'date', title: language.start, variable: 'active_from'},
                    {type: 'date', title: language.end, variable: 'active_to'},
                    {type: 'select', title: language.location_group, variable: 'id_location_group', options: groupsOptions, required: true},
                    {type: 'select', title: language.location_type, variable: 'id_location_type', options: typesOptions, required: true}
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
                        {type: 'text', title: language.name, variable: 'name', limit: 100, required: true},
                        {type: 'date', title: language.start, variable: 'active_from'},
                        {type: 'date', title: language.end, variable: 'active_to'},
                        {type: 'select', title: language.location_group, variable: 'id_location_group', options: groupsOptions, required: true},
                        {type: 'select', title: language.location_type, variable: 'id_location_type', options: typesOptions, required: true}
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
    
    selectLocationType.onchange = function(){
        var datasource = { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelLocation', 
                task: 'getLocationsByGroupId', 
                id_location_group: selectLocationType.value 
            } 
        };
        datatable.setDatasource(datasource);
    }
}
