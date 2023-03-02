/* 
 * This code is free to use, just remember to give credit.
 */

function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        var selectMonth = document.getElementById('selectMonth');
        var selectYear = document.getElementById('selectYear');
        
        var documentsTable = DocumentDatatable(language);
        
        var selectDate = document.getElementById('selectDate');
        selectDate.onclick = function(){
            documentsTable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'InspectorMyDocuments',
                    task: 'getMyDocuments',
                    month: selectMonth.value,
                    year: selectYear.value
                }
            })
        }
    });
}

function DocumentDatatable(language){
    var selectMonth = document.getElementById('selectMonth');
    var selectYear = document.getElementById('selectYear');
    var documents = document.getElementById('documents');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'InspectorMyDocuments',
            task: 'getMyDocuments',
            month: selectMonth.value,
            year: selectYear.value
        }
    };
    var config = {
        columns: [
            {title: language.document_number, variable: 'number', width: 150, minWidth: 150},
            {title: language.location, variable: 'location', width: 250, minWidth: 250},
            {title: language.start, variable: 'start', width: 100, minWidth: 100},
            {title: language.end, variable: 'end', width: 100, minWidth: 100},
            {title: language.description, variable: 'description', width: 250, minWidth: 250}
        ],
        dataSource: datasource
    }
    var documentsTable = new Datatable(documents, config);
    documentsTable.addActionButton(language.edit, function(selected){
        if(selected === undefined){
            alert(language.select_document);
        }
        else{
            RestApi.get('InspectorDocuments', 'getInspectionLocationTypes', {}, 
                function(response){
                    var data = JSON.parse(response);
                    var options = [];
                    data.forEach(item => {
                        var option = {
                            title: item.name,
                            value: item.id
                        }
                        options.push(option);
                    });
                    var fields = [{type: 'select', title: language.select_location_type, variable: 'id_location_type', options: options, required: true}];
                    openModalBox(language.edit_document, fields, language.next, 
                        function(data){
                            var toSend = {id_location_type: data.id_location_type};
                            RestApi.get('InspectorDocuments', 'getLocationsByTypeId', toSend, 
                                    function(response){
                                        var locations = JSON.parse(response);
                                        console.log(locations);
                                        var options = [];
                                        locations.forEach(item => {
                                            var option = {
                                                title: item.name,
                                                value: item.id
                                            }
                                            options.push(option);
                                        });
                                        var fields = [
                                            {type: 'select', title: language.select_location, variable: 'id_location', options: options, required: true},
                                            {type: 'text', title: language.document_number, variable: 'number', limit: 255, required: true},
                                            {type: 'date', title: language.start, variable: 'start'},
                                            {type: 'date', title: language.end, variable: 'end'},
                                            {type: 'textarea', title: language.description, variable: 'description', limit: 255, width: 30, height: 5}
                                        ];
                                        openModalBox(language.edit_document, fields, language.save, function(data){
                                            RestApi.post('InspectorMyDocuments', 'editDocument', data, 
                                                function(response){
                                                    var data = JSON.parse(response);
                                                    console.log(data);
                                                    alert(data.message);
                                                    documentsTable.refresh();
                                                }, function(response){
                                                    console.log(response.responseText);
                                                    alert(response.responseText);
                                            });
                                        }, selected);
                                });
                            
                    }, selected);
            });
        }
    });
    
    
    return documentsTable;
}