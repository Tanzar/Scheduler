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
            {title: language.document_number, variable: 'document_number', width: 150, minWidth: 150},
            {title: language.location, variable: 'location', width: 150, minWidth: 150},
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
            var fields = [
                {type: 'text', title: language.document_number, variable: 'document_number', limit: 255},
                {type: 'date', title: language.start, variable: 'start'},
                {type: 'date', title: language.end, variable: 'end'},
                {type: 'text', title: language.description, variable: 'description', limit: 255}
            ];
            openModalBox(language.new_document, fields, language.save, function(data){
                RestApi.post('InspectorMyDocuments', 'editDocument', data, function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    documentsTable.refresh();
                    }, function(response){
                        alert(response.responseText);
                    });
                }, selected);
        }
    });
    
    
    return documentsTable;
}

function EntriesTable(language){
    var documentId = 0;
    
    
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'InspectorMyDocuments',
            task: 'getDocumentDaysForUser',
            id_document: documentId
        }
    };
    var config = {
        columns: [
            {title: language.start, variable: 'start', width: 150},
            {title: language.end, variable: 'end', width: 150},
            {title: language.underground, variable: 'underground', width: 150}
        ],
        dataSource: datasource
    }
    var datatable = new Datatable(documents, config);
    
    this.refresh = function(id){
        if(id === undefined){
            this.refresh(0);
        }
        else{
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'InspectorMyDocuments',
                    task: 'getDocumentDaysForUser',
                    id_document: documentId
                }
            })
        }
    }
}