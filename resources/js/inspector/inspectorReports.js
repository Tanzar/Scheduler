/* 
 * This code is free to use, just remember to give credit.
 */

function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        var selectYear = document.getElementById('selectYear');
        var selectDocument = new Select('selectDocument', language.select_document);
        selectDocument.loadOptions('InspectorReport', 'getMyDocuments', {year: selectYear.value});
        
        selectYear.onchange = function(){
            selectDocument.loadOptions('InspectorReport', 'getMyDocuments', {year: selectYear.value});
        }
        
        var generateRaportButton = document.getElementById('generateReport');
        generateRaportButton.onclick = function(){
            var documentId = selectDocument.getValue();
            if(documentId !== ''){
                RestApi.post('InspectorReport', 'generateReport', {documentId: documentId}, 
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        var div = document.getElementById('report');
                        formatReport(data, language);
                    }, 
                    function(response){
                        console.log(response.responseText);
                        alert(response.responseText);
                });
            }
            else{
                alert(language.select_document);
            }
        }
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

function showHideButton(buttonId, rowId, language) {
    var hidden = true;
    var button = document.getElementById(buttonId);
    var row = document.getElementById(rowId);
    
    row.style.display = 'none';
    button.textContent = language.show;
    
    button.onclick = function(){
        if(hidden){
            hidden = false;
            row.style.display = 'block';
            button.textContent = language.hide;
        }
        else{
            hidden = true;
            row.style.display = 'none';
            button.textContent = language.show;
        }
    }
}

function formatReport(data, language){
    document.getElementById('documentNumber').textContent = data.details.number;
    document.getElementById('documentStart').textContent = data.details.start;
    document.getElementById('documentEnd').textContent = data.details.end;
    document.getElementById('location').textContent = data.details.location;
    
    var contents = '';
    data.users.forEach(item => {
        contents += item + ', ';
    });
    document.getElementById('assignedUsers').textContent = contents;
    showHideButton('showHideUsers', 'assignedUsersRow', language);
    
    contents = '';
    data.entries.forEach(item => {
        contents += item + '<br>';
    });
    document.getElementById('assignedEntries').innerHTML = contents;
    showHideButton('showHideEntries', 'assignedEntriesRow', language);
    
    contents = '';
    data.tickets.forEach(item => {
        contents += item + '<br>';
    });
    document.getElementById('tickets').innerHTML = contents;
    showHideButton('showHideTickets', 'ticketsRow', language);
    
    contents = '';
    data.art_41.forEach(item => {
        contents += item + '<br>';
    });
    document.getElementById('art41').innerHTML = contents;
    showHideButton('showHideArt41', 'art41Row', language);
    
    contents = '';
    data.decisions.forEach(item => {
        contents += item + '<br>';
    });
    document.getElementById('decisions').innerHTML = contents;
    showHideButton('showHideDecisions', 'decisionsRow', language);
    
    contents = '<ul class="standard-list">';
    data.suspensions.forEach(item => {
        contents += '<li class="standard-list-item">' + item.text + '<br>';
        contents += language.art_41 + '<ul class="standard-list">';
        item.articles.forEach(article => {
            contents += '<li class="standard-list-item">' + article + '</li>';
        })
        contents += '</ul>';
        contents += language.decision + '<ul class="standard-list">';
        item.decisions.forEach(decision => {
            contents += '<li class="standard-list-item">' + decision + '</li>';
        })
        contents += '</ul>';
        contents += language.ticket + '<ul class="standard-list">';
        item.tickets.forEach(ticket => {
            contents += '<li class="standard-list-item">' + ticket + '</li>';
        })
        contents += '</ul>';
    });
    contents += '</ul>'
    document.getElementById('suspensions').innerHTML = contents;
    showHideButton('showHideSuspensions', 'suspensionsRow', language);
}