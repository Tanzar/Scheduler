/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        
        var selectDocument = new Select('documents', language.select_document);
        initDateSelection(selectDocument);
        SuspensionsTable(language, selectDocument);
    });
}


function Select(id, placeholder){
    var select = document.getElementById(id);
    var hiddenValues = {};
    var dataToSend = {};
    
    this.clear = function(){
        while(select.firstChild){
            select.removeChild(select.firstChild);
        }
        
        var option = document.createElement('option');
        option.selected = true;
        option.disabled = true;
        option.placeholder = true;
        option.value = '0';
        option.textContent = placeholder;
        select.appendChild(option);
        hiddenValues = {};
    }
    
    this.setOnChange = function(action){
        select.onchange = function(){
            var hidden = hiddenValues[select.value];
            action(select.value, hidden);
        }
    }
    
    this.addOption = function(value, text, hidden){
        var option = document.createElement('option');
        option.value = value;
        option.textContent = text;
        hiddenValues[value] = hidden;
        select.appendChild(option);
    }
    
    var me = this;
    this.loadOptions = function(controller, task, inputData){
        RestApi.get(controller, task, inputData, function(response){
            me.clear();
            dataToSend = inputData;
            var data = JSON.parse(response);
            data.forEach(item => {
                me.addOption(item.id_document, item.document_number);
            });
        });
    }
    
    this.value = function(){
        return select.value;
    }
}

function initDateSelection(selectDocument) {
    var selectMonth = document.getElementById('selectMonth');
    var selectYear = document.getElementById('selectYear');
    var selectDate = document.getElementById('selectDate');
    selectDate.onclick = function(){
        var data = {
            month: selectMonth.value,
            year: selectYear.value
        }
        selectDocument.loadOptions('InspectorSuspensions', 'getDocuments', data);
    }
}

function SuspensionsTable(language, selectDocument){
    var div = document.getElementById('suspensions');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'InspectorSuspensions',
            task: 'getSuspensions',
            id_document: 0
        }
    };
    var config = {
        columns: [
            {title: language.date, variable: 'date', width: 100, minWidth: 100},
            {title: language.shift, variable: 'shift', width: 150, minWidth: 150},
            {title: language.region, variable: 'region', width: 200, minWidth: 200},
            {title: language.description, variable: 'description', width: 100, minWidth: 100},
            {title: language.correction_date, variable: 'correction_date', width: 100, minWidth: 100},
            {title: language.correction_shift, variable: 'shift', width: 100, minWidth: 100},
            {title: language.remarks, variable: 'remarks', width: 100, minWidth: 100}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
    selectDocument.setOnChange(function(id){
        if(id === undefined){
            id = 0;
        }
        datatable.setDatasource({
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'InspectorSuspensions',
                task: 'getSuspensions',
                id_document: id
            }
        });
    });
    
    
}