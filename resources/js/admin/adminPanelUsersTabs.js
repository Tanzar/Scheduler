/* 
 * This code is free to use, just remember to give credit.
 */

function usersTab(){
    var div = document.createElement('div');
    div.style.width = 'fit-content';
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id'},
            { title: 'Aktywny', variable: 'active'},
            { title: 'Nazwa Użytkonika', variable: 'username'},
            { title: 'Imie', variable: 'name'},
            { title: 'Nazwisko', variable: 'surname'}
        ],
        dataSource : { method: 'post', address: getRestAddress(), data: { controller: 'AdminPanelUsers', task: 'getAllUsers' } }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton('Szukaj', function(){
        var fields = [
            {type: 'checkbox', title: 'Aktywny', variable: 'active'},
            {type: 'text', title: 'Nazwa Użytkonika', variable: 'username'},
            {type: 'text', title: 'Imie', variable: 'name'},
            {type: 'text', title: 'Nazwisko', variable: 'surname'}
        ];
        openModalBox('Dane do wyszukania', fields, 'Szukaj', function(item){
            item.controller = 'AdminPanelUsers';
            item.task = 'findUsers';
            datatable.setDatasource({
                method: 'post', 
                address: getRestAddress(), 
                data: item
            });
        });
    });
    datatable.addActionButton('Dodaj', function(){
        var fields = [
            {type: 'text', title: 'Nazwa Użytkonika', variable: 'username'},
            {type: 'text', title: 'Imie', variable: 'name'},
            {type: 'text', title: 'Nazwisko', variable: 'surname'},
            {type: 'text', title: 'Hasło', variable: 'password'}
        ];
        openModalBox('Nowy użytkownik', fields, 'Zapisz', function(data){
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
    datatable.addActionButton('Edytuj', function(selected){
        if(selected !== undefined){
            var fields = [
                {type: 'text', title: 'Nazwa Użytkonika', variable: 'username'},
                {type: 'text', title: 'Imie', variable: 'name'},
                {type: 'text', title: 'Nazwisko', variable: 'surname'}
            ];
            openModalBox('Edytuj użytkownika', fields, 'Zapisz', function(data){
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
            alert('Wybierz użytkownika.')
        }
    });
    datatable.addActionButton('Zmień hasło', function(selected){
        if(selected !== undefined){
            var item = {
                id: selected.id,
                username: selected.username
            }
            var fields = [
                {type: 'text', title: 'Nowe Hasło', variable: 'password'}
            ];
            openModalBox('Zmień Hasło', fields, 'Zapisz', function(data){
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
            alert('Wybierz użytkownika.')
        }
    });
    datatable.addActionButton('Zmień Status', function(selected){
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
            alert('Wybierz użytkownika.')
        }
    });
    
    return div;
}

function privilagesTab(){
    var user = {id: 0};
    var container = document.createElement('div');
    
    var selectUser = document.createElement('select');
    RestApi.post('AdminPanelUsers', 'findUsers', { active: '1' }, 
        function(response){
            var data = JSON.parse(response);
            var option = document.createElement('option');
            option.selected = true;
            option.disabled = true;
            option.textContent = 'Wybierz użytkownika';
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
            { title: 'Active', variable: 'active'},
            { title: 'Nazwa', variable: 'privilage'}
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
    datatable.addActionButton('Dodaj', function(){
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
                {type: 'select', title: 'Uprawnienie', variable: 'privilage', options: options}
            ];
            openModalBox('Dodaj uprawnienie', fields, 'Zapisz', function(data){
                if(data.privilage !== undefined){
                    data.id_user = user.id;
                    RestApi.post('AdminPanelUsers', 'addPrivilage', data, function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        datatable.refresh();
                    });
                }
                else{
                    console.log('Wybierz Użytkownika')
                }

            });
        })
        
    });
    datatable.addActionButton('Zmień Status', function(selected){
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
            alert('Wybierz uprawnienie.')
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