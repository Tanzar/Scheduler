/* 
 * This code is free to use, just remember to give credit.
 */



function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        initTable(language);
    });
}

function initTable(language){
    var div = document.getElementById('nightShiftDocumentNumber');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.year, variable: 'year', width: 50, minWidth: 50},
            { title: language.document_number, variable: 'number', width: 150, minWidth: 150}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelPrints', 
                task: 'getNightShiftReportNumbers' 
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        RestApi.post('AdminPanelPrints', 'getYear', {},
            function(response){
                var data = JSON.parse(response);
                var year = parseInt(data.year);
                var fields = [
                    {type: 'number', title: language.select_year, variable: 'year', min: year, value: year, required: true},
                    {type: 'text', title: language.document_number, variable: 'number', limit: 25, required: true}
                ];
                openModalBox(language.new_night_shift_report_document_number, fields, language.save, function(data){
                    RestApi.post('AdminPanelPrints', 'saveNightShiftReportNumber', data,
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
            RestApi.post('AdminPanelPrints', 'getYear', {},
                function(response){
                    var data = JSON.parse(response);
                    var year = parseInt(data.year);
                    var fields = [
                        {type: 'number', title: language.select_year, variable: 'year', min: year, value: year, required: true},
                        {type: 'text', title: language.document_number, variable: 'number', limit: 25, required: true}
                    ];
                    openModalBox(language.edit_night_shift_report_document_number, fields, language.save, function(data){
                        RestApi.post('AdminPanelPrints', 'saveNightShiftReportNumber', data,
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
            alert(language.select_night_shift_report_document_number);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelPrints', 'changeNightShiftReportNumberStatus', {id: selected.id},
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
            alert(language.select_night_shift_report_document_number)
        }
    });
    return datatable;
}
