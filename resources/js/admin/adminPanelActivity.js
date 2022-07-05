/* 
 * This code is free to use, just remember to give credit.
 */

function AdminPanelActivity(){
    var activity = document.getElementById('activity');
    var locations = document.getElementById('locations')
    
    RestApi.getInterfaceNamesPackage(function(language){
        var activityTable = new ActivityTable(language, activity);
        var locationsTable = new LocationsTable(language, locations);
        activityTable.setOnSelect(function(selected){
            locationsTable.clearSelected();
            if(selected !== undefined){
                locationsTable.selectRelated(selected.id);
            }
            console.log(selected);
        });
        activityTable.setOnUnselect(function(selected){
            locationsTable.clearSelected();
            console.log(selected);
        });
    });
}

function ActivityTable(language, div){
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'name', width: 150},
            { title: language.short, variable: 'short', width: 50},
            { title: language.symbol, variable: 'symbol', width: 50},
            { title: language.color, variable: 'color', width: 50},
            { title: language.activity_group, variable: 'activity_group', width: 30},
            { title: language.worktime_record_row, variable: 'worktime_record_row', width: 30},
            { title: language.workcard_display, variable: 'workcard_display', width: 30},
            { title: language.allow_location_input, variable: 'allow_location_input', width: 30},
            { title: language.require_document, variable: 'require_document', width: 30}
        ],
        dataSource : { method: 'post', address: getRestAddress(), data: { controller: 'AdminPanelActivity', task: 'getActivities' } }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        RestApi.get('AdminPanelActivity', 'getActivityGroups', {}, function(response){
            var data = JSON.parse(response);
            var options = [];
            var keys = Object.keys(data);
            keys.forEach(item => {
                var option = {
                    value: data[item],
                    title: data[item]
                }
                options.push(option);
            });
            var fields = [
                {type: 'text', title: language.name, variable: 'name', limit: 100},
                {type: 'text', title: language.short, variable: 'short', limit: 50},
                {type: 'text', title: language.symbol, variable: 'symbol', limit: 10},
                {type: 'color', title: language.color, variable: 'color'},
                {type: 'select', title: language.activity_group, variable: 'activity_group', options: options},
                {type: 'number', title: language.worktime_record_row, variable: 'worktime_record_row', min: -1, max: 14, value: -1},
                {type: 'checkbox', title: language.workcard_display, variable: 'workcard_display'},
                {type: 'checkbox', title: language.allow_location_input, variable: 'allow_location_input'},
                {type: 'checkbox', title: language.require_document, variable: 'require_document'}
            ];
            openModalBox(language.new_activity, fields, language.save, function(data){
                RestApi.post('AdminPanelActivity', 'saveActivity', data,
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
        })
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            RestApi.get('AdminPanelActivity', 'getActivityGroups', {}, function(response){
                var data = JSON.parse(response);
                var options = [];
                var keys = Object.keys(data);
                keys.forEach(item => {
                    var option = {
                        value: data[item],
                        title: data[item]
                    }
                    options.push(option);
                });
                var fields = [
                    {type: 'text', title: language.name, variable: 'name', limit: 100},
                    {type: 'text', title: language.short, variable: 'short', limit: 50},
                    {type: 'text', title: language.symbol, variable: 'symbol', limit: 10},
                    {type: 'color', title: language.color, variable: 'color'},
                    {type: 'select', title: language.activity_group, variable: 'activity_group', options: options},
                    {type: 'number', title: language.worktime_record_row, variable: 'worktime_record_row', min: -1, max: 14, value: -1},
                    {type: 'checkbox', title: language.workcard_display, variable: 'workcard_display'},
                    {type: 'checkbox', title: language.allow_location_input, variable: 'allow_location_input'},
                    {type: 'checkbox', title: language.require_document, variable: 'require_document'}
                ];
                openModalBox(language.edit_activity, fields, language.save, function(data){
                    RestApi.post('AdminPanelActivity', 'saveActivity', data,
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
            })
        }
        else{
            alert(language.select_activity);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelActivity', 'changeActivityStatus', selected,
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
            alert(language.select_activity)
        }
    });
    
    this.setOnSelect = function(action){
        datatable.setOnSelect(action);
    }
    
    this.setOnUnselect = function(action){
        datatable.setOnUnselect(action);
    }
}

function LocationsTable(language, div){
    var selectedActivityID = 0;
    
    var config = {
        columns : [
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'name', width: 150, minWidth: 150}
        ],
        dataSource : { method: 'post', address: getRestAddress(), data: { controller: 'AdminPanelActivity', task: 'getLocationTypes' } }
    };
    var datatable = new Datatable(div, config);
    datatable.enableSelectMultiple();
    datatable.addActionButton(language.save, function(selected){
        if(selectedActivityID !== 0){
            RestApi.post('AdminPanelActivity', 'saveActivityLocationTypes', {id_activity: selectedActivityID, location_types: selected}, function(response){
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
            alert(language.select_activity);
        }
    });
    
    this.clearSelected = function(){
        selectedActivityID = 0;
        datatable.clearSelection();
    }
    
    this.selectRelated = function(idActivity){
        selectedActivityID = idActivity;
        RestApi.get('AdminPanelActivity', 'getLocationTypesForActivity', {id: idActivity}, function(response){
            var data = JSON.parse(response);
            console.log(data);
            var ids = [];
            data.forEach(item => {
                ids.push(item.id_location_type);
            });
            datatable.selectRowsWhere('id', ids);
        });
    }
}