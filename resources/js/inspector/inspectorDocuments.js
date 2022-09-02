/* 
 * This code is free to use, just remember to give credit.
 */

function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        var selectMonth = document.getElementById('selectMonth');
        var selectYear = document.getElementById('selectYear');
        var documents = document.getElementById('documents');
        
        var datasource = {
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'InspectorDocuments',
                task: 'getDocumentsForMonthYear',
                month: selectMonth.value,
                year: selectYear.value
            }
        };
        var config = {
            columns: [
                {title: language.document_number, variable: 'number', width: 150, minWidth: 150},
                {title: language.location, variable: 'location', width: 150, minWidth: 150},
                {title: language.start, variable: 'start', width: 100, minWidth: 100},
                {title: language.end, variable: 'end', width: 100, minWidth: 100},
                {title: language.description, variable: 'description', width: 250, minWidth: 250}
            ],
            dataSource: datasource
        }
        var datatable = new Datatable(documents, config);
        datatable.addActionButton(language.add, function(){
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
                    var fields = [{type: 'select', title: language.select_location_type, variable: 'id_location_type', options: options}];
                    openModalBox(language.select_location_type, fields, language.next, 
                        function(data){
                            if(data.id_location_type === ''){
                                alert(language.select_location_type);
                            }
                            else{
                                RestApi.get('InspectorDocuments', 'getLocationsByTypeId', data, 
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
                                            {type: 'select', title: language.select_location, variable: 'id_location', options: options},
                                            {type: 'text', title: language.document_number, variable: 'number', limit: 255},
                                            {type: 'date', title: language.start, variable: 'start'},
                                            {type: 'date', title: language.end, variable: 'end'},
                                            {type: 'text', title: language.description, variable: 'description', limit: 255}
                                        ];
                                        openModalBox(language.new_document, fields, language.save, function(data){
                                            RestApi.post('InspectorDocuments', 'saveAndAssignDocument', data, 
                                                function(response){
                                                    var data = JSON.parse(response);
                                                    console.log(data);
                                                    alert(data.message);
                                                    datatable.refresh();
                                                }, function(response){
                                                    console.log(response.responseText);
                                                    alert(response.responseText);
                                            });
                                        });
                                });
                            }
                    });
            });
        });
        datatable.addActionButton(language.assign, function(selected){
            if(selected !== undefined){
                RestApi.post('InspectorDocuments', 'assignUser', {id: selected.id}, 
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        clearUsersList();
                        datatable.refresh();
                    }, function(response){
                        console.log(response.responseText);
                        alert(response.responseText);
                });
            }
            else{
                alert(language.select_document);
            }
        });
        
        
        datatable.setOnSelect(function(item){
            RestApi.post('InspectorDocuments', 'getDocumentUsers', {id: item.id}, 
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    clearUsersList();
                    var usersList = document.getElementById('usersList');
                    data.forEach(item => {
                        var li = document.createElement('li');
                        li.textContent = item.name + ' ' + item.surname;
                        usersList.appendChild(li);
                    });
            });
        });
        
        datatable.setOnUnselect(function(){
            clearUsersList();
        });
        
        var selectDate = document.getElementById('selectDate');
        selectDate.onclick = function(){
            clearUsersList();
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'InspectorDocuments',
                    task: 'getDocumentsForMonthYear',
                    month: selectMonth.value,
                    year: selectYear.value
                }
            })
        }
    });
}

function clearUsersList(){
    var usersList = document.getElementById('usersList');
    while(usersList.firstChild){
        usersList.removeChild(usersList.firstChild);
    }
}