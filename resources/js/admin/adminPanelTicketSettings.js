/* 
 * This code is free to use, just remember to give credit.
 */

function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        initTicketLawTable(language);
        initPositionGroupTable(language);
    });
}

function initTicketLawTable(language){
    var div = document.getElementById('ticketLaws');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'AdminPanelTicket',
            task: 'getAllTicketLaws'
        }
    };
    var config = {
        columns: [
            {title: 'ID', variable: 'id', width: 30, minWidth: 30},
            {title: language.active, variable: 'active', width: 50, minWidth: 50},
            {title: language.name, variable: 'name', width: 150, minWidth: 150},
            {title: language.short, variable: 'short', width: 100, minWidth: 100}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.name, variable: 'name', limit: 255, required: true},
            {type: 'text', title: language.short, variable: 'short', limit: 10, required: true}
        ];
        openModalBox(language.new_ticket_law, fields, language.save, function(data){
            RestApi.post('AdminPanelTicket', 'saveTicketLaw', data,
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
        if(selected === undefined){
            alert(language.select_ticket_law);
        }
        else{
            var fields = [
                {type: 'text', title: language.name, variable: 'name', limit: 255, required: true},
                {type: 'text', title: language.short, variable: 'short', limit: 10, required: true}
            ];
            openModalBox(language.edit_ticket_law, fields, language.save, function(data){
                RestApi.post('AdminPanelTicket', 'saveTicketLaw', data,
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
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected === undefined){
            alert(language.select_ticket_law);
        }
        else{
            RestApi.post('AdminPanelTicket', 'changeTicketLawStatus', {id: selected.id},
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
    });
}

function initPositionGroupTable(language){
    var div = document.getElementById('positionGroups');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'AdminPanelTicket',
            task: 'getAllPositionGroups'
        }
    };
    var config = {
        columns: [
            {title: 'ID', variable: 'id', width: 30, minWidth: 30},
            {title: language.active, variable: 'active', width: 50, minWidth: 50},
            {title: language.name, variable: 'name', width: 150, minWidth: 150},
            {title: language.short, variable: 'short', width: 100, minWidth: 100}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.name, variable: 'name', limit: 255, required: true},
            {type: 'text', title: language.short, variable: 'short', limit: 5, required: true}
        ];
        openModalBox(language.new_ticket_law, fields, language.save, function(data){
            RestApi.post('AdminPanelTicket', 'savePositionGroup', data,
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
        if(selected === undefined){
            alert(language.select_ticket_law);
        }
        else{
            var fields = [
                {type: 'text', title: language.name, variable: 'name', limit: 255, required: true},
                {type: 'text', title: language.short, variable: 'short', limit: 5, required: true}
            ];
            openModalBox(language.edit_ticket_law, fields, language.save, function(data){
                RestApi.post('AdminPanelTicket', 'savePositionGroup', data,
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
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected === undefined){
            alert(language.select_ticket_law);
        }
        else{
            RestApi.post('AdminPanelTicket', 'changePositionGroupStatus', {id: selected.id},
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
    });
}