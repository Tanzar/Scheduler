/* 
 * This code is free to use, just remember to give credit.
 */


function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        var selectYear = document.getElementById('selectDocumentYear');
        var selectMonth = document.getElementById('selectDocumentMonth');
        var selectDocument = new Select('selectDocumentIndex', language.select_document);
        selectDocument.loadOptions('PrintsInspector', 'getDocuments', {year: selectYear.value, month: selectMonth.value});
        
        selectYear.onchange = function(){
            selectDocument.loadOptions('PrintsInspector', 'getDocuments', {year: selectYear.value, month: selectMonth.value});
        }
        
        selectMonth.onchange = function(){
            selectDocument.loadOptions('PrintsInspector', 'getDocuments', {year: selectYear.value, month: selectMonth.value});
        }
        
        $('#instrumentUsageCard').click(function(){
            FileApi.post('PrintsInspector', 'generateInstrumentUsageCard', {
                id: document.getElementById('selectInstrument').value,
                year: document.getElementById('selectInstrumentYear').value
            });
        });
        
        $('#generateDocumentReport').click(function(){
            if(document.getElementById('selectDocumentIndex').value !== ''){
                FileApi.post('PrintsInspector', 'generateDocumentRaport', {
                    id: document.getElementById('selectDocumentIndex').value
                });
            }
        });
    });
}

function Select(id, placeholder){
    var select = document.getElementById(id);
    
    this.clear = function(){
        while(select.firstChild){
            select.removeChild(select.firstChild);
        }
        
        var option = document.createElement('option');
        option.selected = true;
        option.disabled = true;
        option.placeholder = true;
        option.value = '';
        option.textContent = placeholder;
        select.appendChild(option);
    }
    
    this.setOnChange = function(action){
        select.onchange = function(){
            action(select.value);
        }
    }
    
    this.addOption = function(value, text){
        var option = document.createElement('option');
        option.value = value;
        option.textContent = text;
        select.appendChild(option);
    }
    
    var me = this;
    this.loadOptions = function(controller, task, inputData){
        RestApi.get(controller, task, inputData, 
            function(response){
                me.clear();
                var data = JSON.parse(response);
                data.forEach(item => {
                    me.addOption(item.id, item.number);
                });
            }, 
            function(response){
                console.log(response.responseText);
                alert(response.responseText);
        });
    }
    
    this.getValue = function(){
        return select.value;
    }
}