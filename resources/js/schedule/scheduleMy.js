/* 
 * This code is free to use, just remember to give credit.
 */

function ScheduleMy(){
    var entries = document.getElementById('entries');
    
    RestApi.getInterfaceNamesPackage(function(language){
        var requireDocument = false;
        var dateRange = new DaysRange(new Date());
        
        var rangeDisplay = document.getElementById('rangeDisplay');
        rangeDisplay.textContent = dateRange.getStart() + '  :  ' + dateRange.getEnd();
        
        var datasource = {
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'ScheduleUser',
                task: 'getMyEntries',
                startDate: dateRange.getStart(),
                endDate: dateRange.getEnd()
            }
        };
        var config = {
            columns: [
                {title: language.start_date, variable: 'start', width: 150},
                {title: language.end_date, variable: 'end', width: 150},
                {title: language.location, variable: 'location', width: 200}
            ],
            dataSource: datasource
        }
        var datatable = new Datatable(entries, config);
        datatable.addActionButton(language.edit, function(selected){
            if(selected !== undefined){
                var fields = [
                    {type: 'dateTime', title: language.start, variable: 'start'},
                    {type: 'dateTime', title: language.end, variable: 'end'},
                    {type: 'textarea', title: language.description, variable: 'description', limit: 255, width: 30, height: 5}
                ];
                if(selected.can_be_inspection === 1){
                    fields.push({type: 'checkbox', title: (language.underground + '?'), variable: 'underground'});
                }
                openModalBox(language.edit_entry, fields, language.save, function(data){
                    var dataToSend = {
                        id: selected.id,
                        username: selected.username,
                        start: data.start,
                        end: data.end,
                        description: data.description,
                        underground: data.underground,
                        id_activity: selected.id_activity,
                        id_location: selected.id_location,
                    }
                    RestApi.post('ScheduleUser', 'saveEntry', dataToSend, 
                        function(response){
                            var data = JSON.parse(response);
                            console.log(data);
                            alert(data.message);
                            datatable.refresh();
                        }, function(response){
                            console.log(response.responseText);
                            alert(response.responseText);
                    });
                }, selected);
            }
            else{
                alert(language.select_entry);
            }
        });
        datatable.addActionButton(language.remove, function(selected){
            if(selected !== undefined){
                RestApi.post('ScheduleUser', 'removeEntry', {id: selected.id}, 
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        datatable.refresh();
                    }, function(response){
                        console.log(response.responseText);
                        alert(response.responseText);
                });
            }
            else{
                alert(language.select_entry);
            }
        });
        
        
        var goto = document.getElementById('goTo');
        goto.onclick = function(){
            var date = document.getElementById('displayDate');
            var range = new DaysRange(new Date(date.value));
            rangeDisplay.textContent = range.getStart() + '  :  ' + range.getEnd();
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'ScheduleUser',
                    task: 'getMyEntries',
                    startDate: range.getStart(),
                    endDate: range.getEnd()
                }
            })
        }
        
        var selectActivityGroup = new Select('selectActivityGroup', language.select_activity_group);
        var selectActivity = new Select('selectActivity', language.select_activity);
        var selectLocationType = new Select('selectLocationType', language.select_location_type);
        var selectLocation = new Select('selectLocation', language.select_location);
        var addLocationButton = new AddLocationButton(language, selectLocation);
        
        selectActivityGroup.setOnChange(function(group){
            addLocationButton.hide();
            selectActivity.clear();
            selectLocationType.clear();
            selectLocation.clear();
            RestApi.get('ScheduleUser', 'getActivitiesForGroup', 
                {activities_group: group}, 
                function(response){
                    var data = JSON.parse(response);
                    data.forEach(item => {
                        var option = selectActivity.addOption(item.id, item.name, {
                            allowLocationInput: item.allow_location_input,
                            requireDocument: item.require_document
                        });
                        if(data.length === 1){
                            option.selected = true;
                            selectActivity.callOnChange();
                        }
                    });
            });
        });
        selectActivity.setOnChange(function(activity, hidden){
            if(hidden.allowLocationInput === 0){
                addLocationButton.hide();
            }
            else{
                addLocationButton.show();
            }
            requireDocument = hidden.requireDocument;
            selectLocationType.clear();
            selectLocation.clear();
            RestApi.get('ScheduleUser', 'getLocationTypesForActivity', 
                {id_activity: activity}, 
                function(response){
                    var data = JSON.parse(response);
                    data.forEach(item => {
                        var option = selectLocationType.addOption(item.id_location_type, item.location_type_name);
                        if(data.length === 1){
                            option.selected = true;
                            selectLocationType.callOnChange();
                        }
                    });
            });
        });
        selectLocationType.setOnChange(function(type){
            selectLocation.clear();
            selectLocation.loadOptions('ScheduleUser', 'getLocationsForType', {id_location_type: type});
        });
        var newEntryButton = document.getElementById('newEntry');
        newEntryButton.onclick = function(){
            if($('#selectActivity').val() === ''){
                alert(language.select_activity);
            }
            else{
                if($('#selectLocation').val() === ''){
                    alert(language.select_location);
                }
                else{
                    var dataToSend = {
                        id_location: $('#selectLocation').val(),
                        id_activity: $('#selectActivity').val(),
                        start: $('#startDate').val().replace('T', ' '),
                        end: $('#endDate').val().replace('T', ' '),
                        description: $('#description').val()
                    };
                    if(requireDocument === 1){
                        var item = {
                            start: dataToSend.start,
                            end: dataToSend.end,
                            id_location: dataToSend.id_location
                        }
                        RestApi.post('ScheduleUser', 'getMyMatchingDocuments', item, 
                            function(response){
                                var data = JSON.parse(response);
                                var options = [];
                                data.forEach(item => {
                                    var option = {
                                        value: item.id,
                                        title: item.number
                                    }
                                    options.push(option);
                                });
                                var fields = [
                                    {type: 'select', title: language.select_document, variable: 'id_document', options: options, required: true},
                                    {type: 'checkbox', title: (language.underground + '?'), variable: 'underground'}
                                ];
                                openModalBox(language.select_document, fields, language.save, function(data){
                                    RestApi.post('ScheduleUser', 'saveEntry', data, 
                                        function(response){
                                            var data = JSON.parse(response);
                                            console.log(data);
                                            alert(data.message);
                                            datatable.refresh();
                                        }, 
                                        function(response){
                                            alert(response.responseText);
                                    });
                                }, dataToSend);
                        });
                    }
                    else{
                        var date = new Date(dataToSend.start);
                        var fields = [];
                        for(var i = 1; i < 5; i++){
                            date.setDate(date.getDate() + 1);
                            fields.push({type: 'checkbox', title: '' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate(), variable: '' + i});
                        }
                        openModalBox(language.select_additional_dates, fields, language.save, function(data){
                            var toSend ={
                                entry: dataToSend,
                                pushes: data
                            }
                            RestApi.post('ScheduleUser', 'saveMultipleEntries', toSend, 
                                function(response){
                                    var data = JSON.parse(response);
                                    console.log(data);
                                    alert(data.message);
                                    datatable.refresh();
                                }, 
                                function(response){
                                    alert(response.responseText);
                            });
                        });
                    }
                }
            }
        };
        
        
    });
}

function DaysRange(date){
    var start = new Date(date);
    start.setDate(date.getDate() - 3);
    var end = new Date(date);
    end.setDate(date.getDate() + 3);
    
    this.getStart = function(){
        return start.toISOString().split('T')[0];
    }
    
    this.getEnd = function(){
        return end.toISOString().split('T')[0];
    }
}

function Select(id, placeholder){
    var select = document.getElementById(id);
    var hiddenValues = {};
    var controllerName = '';
    var taskName = '';
    var dataToSend = {};
    
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
        return option;
    }
    
    var me = this;
    this.loadOptions = function(controller, task, inputData){
        RestApi.get(controller, task, inputData, function(response){
            controllerName = controller;
            taskName = task;
            dataToSend = inputData;
            var data = JSON.parse(response);
            data.forEach(item => {
                var option = me.addOption(item.id, item.name);
                if(data.length === 1){
                    option.selected = true;
                    if(select.onchange instanceof Function){
                        select.onchange();
                    }
                }
            });
        });
    }
    
    this.callOnChange = function(){
        select.onchange();
    }
    
    this.refresh = function(){
        if(controllerName !== '', taskName !== '', dataToSend !== {}){
            this.clear();
            this.loadOptions(controllerName, taskName, dataToSend);
        }
    }
}

function AddLocationButton(language, selectLocation){
    var addLocationButton = document.getElementById('addLocation');
    addLocationButton.onclick = function(){
        var item = {id_location_type: $('#selectLocationType').val()};
        if(item.id_location_type !==''){
            var fields = [
                {type: 'text', title: language.location, variable: 'name', limit: 100, required: true}
            ];
            openModalBox(language.new_location, fields, language.save, function(data){
                RestApi.post('ScheduleUser', 'saveLocation', data, 
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        selectLocation.refresh();
                    }, 
                    function(response){
                        alert(response.responseText);
                });
            }, item);
        }
        else{
            alert(language.select_location_type);
        }
    }
    
    this.hide = function(){
        addLocationButton.style.display = 'none';
    }
    
    this.show = function(){
        addLocationButton.style.display = 'block';
    }
}
