/* 
 * This code is free to use, just remember to give credit.
 */



function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        initTable(language);
    });
}

function initTable(language){
    var div = document.getElementById('overtime');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.username, variable: 'username', width: 150, minWidth: 150},
            { title: language.name, variable: 'name', width: 150, minWidth: 150},
            { title: language.surname, variable: 'surname', width: 150, minWidth: 150},
            { title: language.date, variable: 'date', width: 150, minWidth: 150},
            { title: language.time, variable: 'time', width: 150, minWidth: 150}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelOvertime', 
                task: 'getOvertimeReductions' 
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        RestApi.post('AdminPanelOvertime', 'getOptions', {},
            function(response){
                var data = JSON.parse(response);
                var users = [];
                data.users.forEach(user =>{
                    var option = {
                        title: user.name + ' ' + user.surname,
                        value: user.id
                    }
                    users.push(option);
                });
                var months = [];
                var keys = Object.keys(data.months);
                keys.forEach(monthNumber => {
                    var option = {
                        title: data.months[monthNumber],
                        value: monthNumber
                    }
                    months.push(option);
                });
                var year = parseInt(data.year);
                var fields = [
                    {type: 'select', title: language.select_user, variable: 'id_user', options: users, required: true},
                    {type: 'number', title: language.time_in_ms, variable: 'time', required: true},
                    {type: 'select', title: language.select_month, variable: 'month', options: months, required: true},
                    {type: 'number', title: language.select_year, variable: 'year', min: year, value: year, required: true}
                ];
                openModalBox(language.new_overtime_reduction, fields, language.save, function(data){
                    RestApi.post('AdminPanelOvertime', 'saveOvertimeReduction', data,
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
            RestApi.post('AdminPanelOvertime', 'getOptions', {},
                function(response){
                    var data = JSON.parse(response);
                    var users = [];
                    data.users.forEach(user =>{
                        var option = {
                            title: user.name + ' ' + user.surname,
                            value: user.id
                        }
                        users.push(option);
                    });
                    var months = [];
                    var keys = Object.keys(data.months);
                    keys.forEach(monthNumber => {
                        var option = {
                            title: data.months[monthNumber],
                            value: monthNumber
                        }
                        months.push(option);
                    })
                    var fields = [
                        {type: 'select', title: language.select_user, variable: 'id_user', options: users, required: true},
                        {type: 'number', title: language.time_in_ms, variable: 'time', required: true},
                        {type: 'select', title: language.select_month, variable: 'month', options: months, required: true},
                        {type: 'number', title: language.ticket_year, variable: 'year', min: data.year, required: true}
                    ];
                    openModalBox(language.new_overtime_reduction, fields, language.save, function(data){
                        RestApi.post('AdminPanelOvertime', 'saveOvertimeReduction', data,
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
            alert(language.select_overtime_reduction);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelOvertime', 'changeOvertimeReductionStatus', {id: selected.id},
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
            alert(language.select_overtime_reduction)
        }
    });
    return datatable;
}
