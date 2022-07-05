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
            {title: language.document_number, variable: 'document_number', width: 150},
            {title: language.start, variable: 'start', width: 150},
            {title: language.end, variable: 'end', width: 150}
        ],
        dataSource: datasource
    }
    var documentsTable = new Datatable(documents, config);
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