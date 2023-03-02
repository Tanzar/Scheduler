/* 
 * This code is free to use, just remember to give credit.
 */

function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        var daysTable = initDaysTable(language);
        initUsersTable(language, daysTable);
        initWorkDaysTable(language);
    });
}

function initDaysTable(language){
    var div = document.getElementById('days');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.date, variable: 'date', width: 70, minWidth: 70},
            { title: language.name, variable: 'name', width: 200, minWidth: 200},
            { title: language.for_all, variable: 'for_all', width: 100, minWidth: 100}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelDaysOff', 
                task: 'getDaysOff' 
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'date', title: language.date, variable: 'date'},
            {type: 'text', title: language.name, variable: 'name', limit: 100, required: true},
            {type: 'checkbox', title: language.for_all, variable: 'for_all'}
        ];
        openModalBox(language.new_free_day, fields, language.save, function(data){
            RestApi.post('AdminPanelDaysOff', 'saveDayOff', data,
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
                {type: 'date', title: language.date, variable: 'date'},
                {type: 'text', title: language.name, variable: 'name', limit: 100, required: true},
                {type: 'checkbox', title: language.for_all, variable: 'for_all'}
            ];
            openModalBox(language.edit_free_day, fields, language.save, function(data){
                RestApi.post('AdminPanelDaysOff', 'saveDayOff', data,
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
            alert(language.select_free_day);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelDaysOff', 'changeDayOffStatus', {id_days_off: selected.id},
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
            alert(language.select_free_day)
        }
    });
    return datatable;
}

function initUsersTable(language, daysTable){
    var div = document.getElementById('users');
    
    var config = {
        columns : [
            { title: language.username, variable: 'username', width: 150, minWidth: 150},
            { title: language.name_person, variable: 'name', width: 150, minWidth: 150},
            { title: language.surname, variable: 'surname', width: 150, minWidth: 150}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelDaysOff', 
                task: 'getUsers',
                id_days_off: 0
            } 
        }
    };
    var datatable = new Datatable(div, config);
    daysTable.setOnSelect(function(selected){
        if(selected !== undefined){
            datatable.setDatasource({ 
                method: 'post', 
                address: getRestAddress(), 
                data: { 
                    controller: 'AdminPanelDaysOff', 
                    task: 'getUsers',
                    id_days_off: selected.id
                } 
            });
        }
    });
    daysTable.setOnUnselect(function(){
        datatable.setDatasource({ 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelDaysOff', 
                task: 'getUsers',
                id_days_off: 0
            } 
        });
    });
    datatable.addActionButton(language.add, function(){
        var selected = daysTable.getSelected();
        if(selected === undefined || selected.for_all) {
            alert(language.select_free_day);
        }
        else{
            RestApi.post('AdminPanelDaysOff', 'getMatchingUsers', { date: selected.date },
                function(response){
                    var users = JSON.parse(response);
                    var options = [];
                    users.forEach(item => {
                        var option = {
                            value: item.username,
                            title: item.name + ' ' + item.surname
                        }
                        options.push(option);
                    });
                    var fields = [
                        {type: 'select', title: language.select_user, variable: 'username', options: options, required: true}

                    ];
                    openModalBox(language.new_user, fields, language.save, function(data){
                        RestApi.post('AdminPanelDaysOff', 'saveUserForDay', {username: data.username, id_days_off: selected.id},
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
        }
    });
    datatable.addActionButton(language.remove, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelDaysOff', 'removeUserFromDay', {username: selected.username, id_days_off: selected.id_days_off},
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
            alert(language.select_user)
        }
    });
}

function initWorkDaysTable(language){
    var div = document.getElementById('workdays');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.date, variable: 'date', width: 70, minWidth: 70},
            { title: language.description, variable: 'description', width: 150, minWidth: 150}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelDaysOff', 
                task: 'getSpecialWorkdays' 
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'date', title: language.date, variable: 'date'},
            {type: 'text', title: language.description, variable: 'description', limit: 100, required: true}
        ];
        openModalBox(language.new_special_work_day, fields, language.save, function(data){
            RestApi.post('AdminPanelDaysOff', 'saveSpecialWorkday', data,
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
                {type: 'date', title: language.date, variable: 'date'},
                {type: 'text', title: language.description, variable: 'description', limit: 100, required: true}
            ];
            openModalBox(language.edit_special_work_day, fields, language.save, function(data){
                RestApi.post('AdminPanelDaysOff', 'saveSpecialWorkday', data,
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
            alert(language.select_special_work_day);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelDaysOff', 'changeSpecialWorkdayStatus', {id: selected.id},
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
            alert(language.select_special_work_day)
        }
    });
    return datatable;
}