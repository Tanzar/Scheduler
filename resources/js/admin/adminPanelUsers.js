/* 
 * This code is free to use, just remember to give credit.
 */

function AdminPanelUsers(){
    var users = document.getElementById('users');
    var privilages = document.getElementById('privilages');
    var employment = document.getElementById('employment');
    
    RestApi.getInterfaceNamesPackage(function(language){
        var usersTable = new UsersTable(language, users);
        var privilagesTable = new PrivilagesTable(language, privilages);
        var employmentsTable = new EmploymentsTable(language, employment);
        
        usersTable.setOnSelect(function(selected){
            if(selected === undefined){
                alert(language.select_user);
            }
            else{
                privilagesTable.setUserID(selected.id);
                employmentsTable.setUserID(selected.id);
            }
        });
        usersTable.addActionButton(language.search, function(){
            var fields = [
                {type: 'checkbox', title: language.active, variable: 'active'},
                {type: 'text', title: language.username, variable: 'username', limit: 10},
                {type: 'text', title: language.name_person, variable: 'name', limit: 25},
                {type: 'text', title: language.surname, variable: 'surname', limit: 25},
                {type: 'text', title: language.short, variable: 'short', limit: 5}
            ];
            openModalBox(language.search_data, fields, language.search, function(item){
                item.controller = 'AdminPanelUsers';
                item.task = 'findUsers';
                usersTable.setDatasource({
                    method: 'post', 
                    address: getRestAddress(), 
                    data: item
                });
                privilagesTable.setUserID(0);
            });
        });
        usersTable.addActionButton(language.add, function(){
            var fields = [
                {type: 'text', title: language.username, variable: 'username', limit: 10},
                {type: 'text', title: language.name_person, variable: 'name', limit: 25},
                {type: 'text', title: language.surname, variable: 'surname', limit: 25},
                {type: 'text', title: language.short, variable: 'short', limit: 5},
                {type: 'text', title: language.password, variable: 'password', limit: 40}
            ];
            openModalBox(language.new_user, fields, language.save, function(data){
                RestApi.post('AdminPanelUsers', 'saveUser', data,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    usersTable.refresh();
                    privilagesTable.setUserID(0);
                    employmentsTable.setUserID(0);
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
                });
            });
        });
        usersTable.addActionButton(language.edit, function(selected){
            if(selected !== undefined){
                var fields = [
                    {type: 'text', title: language.username, variable: 'username', limit: 10},
                    {type: 'text', title: language.name_person, variable: 'name', limit: 25},
                    {type: 'text', title: language.surname, variable: 'surname', limit: 25},
                    {type: 'text', title: language.short, variable: 'short', limit: 5}
                ];
                openModalBox(language.edit_user, fields, language.save, function(data){
                    RestApi.post('AdminPanelUsers', 'saveUser', data,
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        usersTable.refresh();
                        privilagesTable.setUserID(0);
                        employmentsTable.setUserID(0);
                    },
                    function(response){
                        console.log(response.responseText);
                        alert(response.responseText);
                    });;
                }, selected);
            }
            else{
                alert(language.select_user)
            }
        });
        usersTable.addActionButton(language.change_password, function(selected){
            if(selected !== undefined){
                var item = {
                    id: selected.id,
                    username: selected.username
                }
                var fields = [
                    {type: 'text', title: language.new_password, variable: 'password', limit: 40}
                ];
                openModalBox(language.change_password, fields, language.save, function(data){
                    RestApi.post('AdminPanelUsers', 'changeUserPassword', data,
                        function(response){
                            var data = JSON.parse(response);
                            console.log(data);
                            alert(data.message);
                        },
                        function(response){
                            console.log(response.responseText);
                            alert(response.responseText);
                        });
                }, item);
            }
            else{
                alert(language.select_user)
            }
        });
        usersTable.addActionButton(language.change_status, function(selected){
            if(selected !== undefined){
                RestApi.post('AdminPanelUsers', 'changeUserStatus', selected,
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        usersTable.refresh();
                        privilagesTable.setUserID(0);
                        employmentsTable.setUserID(0);
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
        usersTable.setOnUnselect(function(){
            privilagesTable.setUserID(0);
            employmentsTable.setUserID(0);
        })
    });
    
}

function UsersTable(language, div){
    div.style.width = 'fit-content';
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30, minWidth: 30},
            { title: language.active, variable: 'active', width: 50, minWidth: 30},
            { title: language.username, variable: 'username', width: 150, minWidth: 150},
            { title: language.name_person, variable: 'name', width: 150, minWidth: 150},
            { title: language.surname, variable: 'surname', width: 150, minWidth: 150},
            { title: language.short, variable: 'short', width: 50, minWidth: 50}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelUsers', 
                task: 'getAllUsers' 
            } 
        }
    };
    var datatable = new Datatable(div, config);
    
    this.addActionButton = function(text, action){
        datatable.addActionButton(text, action);
    }
    
    this.setOnSelect = function(action){
        datatable.setOnSelect(action);
    }
    
    this.setOnUnselect = function(action){
        datatable.setOnUnselect(action);
    }
    
    this.setDatasource = function(datasource){
        datatable.setDatasource(datasource);
    }
    
    this.refresh = function(){
        datatable.refresh();
    }
}

function PrivilagesTable(language, div){
    this.idUser = 0;
    
    var config = {
        columns : [
            { title: language.active, variable: 'active', width: 50, minWidth: 50},
            { title: language.name, variable: 'privilage', width: 150, minWidth: 150}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelUsers', 
                task: 'getUserPrivilages',
                id_user: this.idUser
            } 
        }
    };
    this.datatable = new Datatable(div, config);
    var table = this;
    var datatab = this.datatable;
    this.datatable.addActionButton(language.add, function(){
        if(table.idUser !== 0){
            RestApi.get('AdminPanelUsers', 'getPrivilagesList', {}, 
            function(response){
                var data = JSON.parse(response);
                var options = [];
                data.forEach(item => {
                    var option = {
                        value: item,
                        title: item
                    }
                    options.push(option);
                });
                var fields = [
                    {type: 'select', title: language.privilage, variable: 'privilage', options: options}
                ];
                openModalBox(language.add_privilage, fields, language.save, function(data){
                    if(data.privilage !== undefined){
                        data.id_user = table.idUser;
                        RestApi.post('AdminPanelUsers', 'savePrivilage', data, function(response){
                                var data = JSON.parse(response);
                                console.log(data);
                                alert(data.message);
                                datatab.refresh();
                            },
                            function(response){
                                console.log(response.responseText);
                                alert(response.responseText);
                            }
                        );
                    }
                    else{
                        alert(language.select_privilage);
                    }

                });
            });
        }
        else{
            alert(language.select_user);
        }
    });
    this.datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            var item = {
                id: selected.id
            }
            RestApi.post('AdminPanelUsers', 'changePrivilageStatus', item,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatab.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_privilage)
        }
    });
    
    this.setUserID = function(id){
        this.idUser = id;
        this.datatable.setDatasource({ 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelUsers', 
                task: 'getUserPrivilages',
                id_user: this.idUser
            } 
        });
    }
}

function EmploymentsTable(language, div){
    this.idUser = 0;
    
    var config = {
        columns : [
            { title: language.active, variable: 'active', width: 50, minWidth: 50},
            { title: language.position, variable: 'position', width: 150, minWidth: 150},
            { title: language.user_type, variable: 'user_type', width: 100, minWidth: 100},
            { title: language.start_date, variable: 'start', width: 100, minWidth: 100},
            { title: language.end_date, variable: 'end', width: 100, minWidth: 100},
            { title: language.sort_priority, variable: 'sort_priority', width: 50, minWidth: 50},
            { title: language.standard_day_start, variable: 'standard_day_start', width: 100, minWidth: 100},
            { title: language.standard_day_end, variable: 'standard_day_end', width: 100, minWidth: 100},
            { title: language.leadership, variable: 'leadership', width: 50, minWidth: 50},
            { title: language.inspector, variable: 'inspector', width: 50, minWidth: 50}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelUsers', 
                task: 'getUserEmploymentPeriods',
                id_user: this.idUser
            } 
        }
    };
    this.datatable = new Datatable(div, config);
    var table = this;
    var datatab = this.datatable;
    this.datatable.addActionButton(language.add, function(){
        if(table.idUser !== 0){
            var fields = [
                {type: 'text', title: language.position, variable: 'position', limit: 255},
                {type: 'text', title: language.user_type, variable: 'user_type', limit: 6},
                {type: 'date', title: language.start_date, variable: 'start'},
                {type: 'date', title: language.end_date, variable: 'end'},
                {type: 'number', title: language.sort_priority, variable: 'sort_priority', value: 10, min: 1},
                {type: 'time', title: language.standard_day_start, variable: 'standard_day_start'},
                {type: 'time', title: language.standard_day_end, variable: 'standard_day_end'},
                {type: 'checkbox', title: language.leadership, variable: 'leadership'},
                {type: 'checkbox', title: language.inspector, variable: 'inspector'}
            ];
            openModalBox(language.new_employment_period, fields, language.save, function(data){
                data.id_user = table.idUser;
                RestApi.post('AdminPanelUsers', 'saveEmploymentPeriod', data, 
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        datatab.refresh();
                    },
                    function(response){
                        console.log(response.responseText);
                        alert(response.responseText);
                });
            });
        }
        else{
            alert(language.select_user);
        }
    });
    this.datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            var fields = [
                {type: 'text', title: language.position, variable: 'position', limit: 255},
                {type: 'text', title: language.user_type, variable: 'user_type', limit: 6},
                {type: 'date', title: language.start_date, variable: 'start'},
                {type: 'date', title: language.end_date, variable: 'end'},
                {type: 'number', title: language.sort_priority, variable: 'sort_priority', value: 10, min: 1},
                {type: 'time', title: language.standard_day_start, variable: 'standard_day_start'},
                {type: 'time', title: language.standard_day_end, variable: 'standard_day_end'},
                {type: 'checkbox', title: language.leadership, variable: 'leadership'},
                {type: 'checkbox', title: language.inspector, variable: 'inspector'}
            ];
            openModalBox(language.new_employment_period, fields, language.save, function(data){
                data.id_user = table.idUser;
                RestApi.post('AdminPanelUsers', 'saveEmploymentPeriod', data, 
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        datatab.refresh();
                    },
                    function(response){
                        console.log(response.responseText);
                        alert(response.responseText);
                });
            }, selected);
        }
        else{
            alert(language.select_employment)
        }
    });
    this.datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            var item = {
                id: selected.id
            }
            RestApi.post('AdminPanelUsers', 'changeEmploymentPeriodStatus', item,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatab.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_employment)
        }
    });
    
    this.setUserID = function(id){
        this.idUser = id;
        this.datatable.setDatasource({ 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelUsers', 
                task: 'getUserEmploymentPeriods',
                id_user: this.idUser
            } 
        });
    }
}