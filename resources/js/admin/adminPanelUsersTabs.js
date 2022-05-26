/* 
 * This code is free to use, just remember to give credit.
 */

function usersTab(language){
    var div = document.createElement('div');
    div.style.width = 'fit-content';
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id'},
            { title: language.active, variable: 'active'},
            { title: language.username, variable: 'username'},
            { title: language.name_person, variable: 'name'},
            { title: language.surname, variable: 'surname'}
        ],
        dataSource : { method: 'post', address: getRestAddress(), data: { controller: 'AdminPanelUsers', task: 'getAllUsers' } }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.search, function(){
        var fields = [
            {type: 'checkbox', title: language.active, variable: 'active'},
            {type: 'text', title: language.username, variable: 'username'},
            {type: 'text', title: language.name_person, variable: 'name'},
            {type: 'text', title: language.surname, variable: 'surname'}
        ];
        openModalBox(language.search_data, fields, language.search, function(item){
            item.controller = 'AdminPanelUsers';
            item.task = 'findUsers';
            datatable.setDatasource({
                method: 'post', 
                address: getRestAddress(), 
                data: item
            });
        });
    });
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.username, variable: 'username'},
            {type: 'text', title: language.name_person, variable: 'name'},
            {type: 'text', title: language.surname, variable: 'surname'},
            {type: 'text', title: language.password, variable: 'password'}
        ];
        openModalBox(language.new_user, fields, language.save, function(data){
            RestApi.post('AdminPanelUsers', 'saveUser', data,
            function(response){
                console.log(response);
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
                {type: 'text', title: language.username, variable: 'username'},
                {type: 'text', title: language.name_person, variable: 'name'},
                {type: 'text', title: language.surname, variable: 'surname'}
            ];
            openModalBox(language.edit_user, fields, language.save, function(data){
                RestApi.post('AdminPanelUsers', 'saveUser', data,
                function(response){
                    console.log(response);
                    datatable.refresh();
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
    datatable.addActionButton(language.change_password, function(selected){
        if(selected !== undefined){
            var item = {
                id: selected.id,
                username: selected.username
            }
            var fields = [
                {type: 'text', title: language.new_password, variable: 'password'}
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
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelUsers', 'changeUserStatus', selected,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    datatable.refresh();
                    alert(data.message);
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
    
    return div;
}

function privilagesTab(language){
    var user = {id: 0};
    var container = document.createElement('div');
    
    var selectUser = document.createElement('select');
    RestApi.post('AdminPanelUsers', 'findUsers', { active: '1' }, 
        function(response){
            var data = JSON.parse(response);
            var option = document.createElement('option');
            option.selected = true;
            option.disabled = true;
            option.textContent = language.select_user;
            selectUser.appendChild(option);
            for(var i = 0; data.length > i; i++){
                var item = data[i];
                option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.username;
                selectUser.appendChild(option);
            }
        });
    
    
    container.appendChild(selectUser);
    
    var tableDiv = document.createElement('div');
    tableDiv.style.width = 'fit-content';
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id'},
            { title: language.active, variable: 'active'},
            { title: language.name, variable: 'privilage'}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelUsers', 
                task: 'getUserPrivilages',
                id_user: 0
            } 
        }
    };
    var datatable = new Datatable(tableDiv, config);
    datatable.addActionButton(language.add, function(){
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
                    data.id_user = user.id;
                    RestApi.post('AdminPanelUsers', 'addPrivilage', data, function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        datatable.refresh();
                    });
                }
                else{
                    console.log(language.select_user)
                }

            });
        })
        
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            var item = {
                id: selected.id
            }
            RestApi.post('AdminPanelUsers', 'changePrivilageStatus', item,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    datatable.refresh();
                    alert(data.message);
            });
        }
        else{
            alert(language.select_privilage)
        }
    });
    
    selectUser.onchange = function(){
        var id = this.value;
        user.id = id;
        datatable.setDatasource({
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelUsers', 
                task: 'getUserPrivilages',
                id_user: id
            } 
        });
    }
    
    container.appendChild(tableDiv);
    
    return container;
}

function getRestAddress(){
    var myScript = document.getElementById('RestApi.js');
    var path = myScript.getAttribute('src');
    var index = path.search('resources/js');
    path = path.substring(0, index);
    var address = path + 'sys/scripts/requests/rest.php';
    return address;
}