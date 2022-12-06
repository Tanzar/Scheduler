/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        SuzugTable(language);
    });
}

function SuzugTable(language) {
    var div = document.getElementById('suzug');
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'StatsSuzug',
            task: 'getSuzugUsers',
            year: document.getElementById('selectYear').value
        }
    };
    var config = {
        columns: [
            {title: language.active, variable: 'active', width: 50, minWidth: 50},
            {title: language.suzug_number, variable: 'number', width: 50, minWidth: 50},
            {title: language.name_person, variable: 'name', width: 150, minWidth: 150},
            {title: language.surname, variable: 'surname', width: 150, minWidth: 150}
        ],
        dataSource: datasource
    }
    var datatable = new Datatable(div, config);
    
    datatable.addActionButton(language.assign, function(){
        var year = document.getElementById('selectYear').value;
        RestApi.get('StatsSuzug', 'getAssignmentOptions', {year: year}, function(response){
            var data = JSON.parse(response);
            var users = [];
            var usersIds = Object.keys(data.users);
            usersIds.forEach(id => {
                var option = {
                    value: id,
                    title: data.users[id]
                }
                users.push(option);
            });
            var numbers = [];
            data.numbers.forEach(item => {
                var option = {
                    value: item,
                    title: item
                }
                numbers.push(option);
            });
            var fields = [
                {type: 'select', title: language.select_user, variable: 'id_user', options: users, required: true},
                {type: 'select', title: language.select_number, variable: 'number', options: numbers, required: true}
            ];
            openModalBox(language.assign_suzug_number_for_year + " " + year, fields, language.save, function(data){
                data.year = year;
                RestApi.post('StatsSuzug', 'save', data,
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
            RestApi.get('StatsSuzug', 'getAssignmentOptions', {year: selected.year}, function(response){
                var data = JSON.parse(response);
                var users = [];
                var usersIds = Object.keys(data.users);
                usersIds.forEach(id => {
                    var option = {
                        value: id,
                        title: data.users[id]
                    }
                    users.push(option);
                });
                var numbers = [];
                data.numbers.forEach(item => {
                    var option = {
                        value: item,
                        title: item
                    }
                    numbers.push(option);
                });
                var fields = [
                    {type: 'select', title: language.select_number, variable: 'number', options: numbers, required: true}
                ];
                var number = selected.number;
                selected.number = '';
                openModalBox(language.assign_suzug_number_for_year + " " + selected.year, fields, language.save, function(data){
                    RestApi.post('StatsSuzug', 'save', data,
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
            alert(language.select_user);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('StatsSuzug', 'changeStatus', {id: selected.id},
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
            alert(language.select_user);
        }
    });
}