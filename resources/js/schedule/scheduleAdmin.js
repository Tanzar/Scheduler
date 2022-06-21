/* 
 * This code is free to use, just remember to give credit.
 */

function ScheduleAdmin(){
    var entries = document.getElementById('entries');
    
    RestApi.getInterfaceNamesPackage(function(language){
        var dateRange = new DaysRange(new Date());
        
        var rangeDisplay = document.getElementById('rangeDisplay');
        rangeDisplay.textContent = dateRange.getStart() + '  :  ' + dateRange.getEnd();
        
        var selectUser = document.getElementById('selectUser');
        
        var datasource = {
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'ScheduleAdmin',
                task: 'getEntries',
                startDate: dateRange.getStart(),
                endDate: dateRange.getEnd(),
                username: selectUser.value
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
                    {type: 'dateTime', title: language.end, variable: 'end'}
                ];
                openModalBox(language.edit_entry, fields, language.save, function(data){
                    var dataToSend = {
                        id: data.id,
                        id_user: data.id_user,
                        start: data.start,
                        end: data.end
                    }
                    RestApi.post('ScheduleAdmin', 'saveEntry', dataToSend, function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        datatable.refresh();
                    }, function(response){
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
                RestApi.post('ScheduleAdmin', 'removeEntry', {id: selected.id}, function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                }, function(response){
                    alert(response.responseText);
                });
            }
            else{
                alert(language.select_entry);
            }
        });
        
        
        var goto = document.getElementById('goTo');
        goto.onclick = function(){
            if(selectUser.value === ''){
                alert(language.select_user);
            }
            else{
                var date = document.getElementById('displayDate');
                var range = new DaysRange(new Date(date.value));
                rangeDisplay.textContent = range.getStart() + '  :  ' + range.getEnd();
                datatable.setDatasource({
                    method: 'get',
                    address: getRestAddress(),
                    data: {
                        controller: 'ScheduleAdmin',
                        task: 'getEntries',
                        startDate: range.getStart(),
                        endDate: range.getEnd(),
                        username: $('#selectUser').val()
                    }
                });
            }
        }
        
        selectUser.onchange = function(){
            goto.onclick();
            /*
            var date = document.getElementById('displayDate');
            var range = new DaysRange(new Date(date.value));
            rangeDisplay.textContent = range.getStart() + '  :  ' + range.getEnd();
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'ScheduleAdmin',
                    task: 'getEntries',
                    startDate: range.getStart(),
                    endDate: range.getEnd(),
                    username: $('#selectUser').val()
                }
            });*/
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
            RestApi.get('ScheduleAdmin', 'getActivitiesForGroup', {activities_group: group}, function(response){
                var data = JSON.parse(response);
                data.forEach(item => {
                    selectActivity.addOption(item.id, item.name, item.allow_location_input);
                });
            });
        });
        selectActivity.setOnChange(function(activity, allow_location_input){
            if(allow_location_input === 0){
                addLocationButton.hide();
            }
            else{
                addLocationButton.show();
            }
            selectLocationType.clear();
            selectLocation.clear();
            RestApi.get('ScheduleAdmin', 'getLocationTypesForActivity', {id_activity: activity}, function(response){
                var data = JSON.parse(response);
                data.forEach(item => {
                    selectLocationType.addOption(item.id_location_type, item.location_type_name);
                });
            });
        });
        selectLocationType.setOnChange(function(type){
            selectLocation.clear();
            selectLocation.loadOptions('ScheduleAdmin', 'getLocationsForType', {id_location_type: type});
        });
        var newEntryButton = document.getElementById('newEntry');
        newEntryButton.onclick = function(){
            if($('#selectUser').val() === ''){
                alert(language.select_user);
            }
            else{
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
                            username: $('#selectUser').val()
                        };
                        RestApi.post('ScheduleAdmin', 'saveEntry', dataToSend, function(response){
                            var data = JSON.parse(response);
                            console.log(data);
                            alert(data.message);
                            datatable.refresh();
                        }, function(response){
                            alert(response.responseText);
                        });
                    }
                }
            }
        }
        
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
    }
    
    var me = this;
    this.loadOptions = function(controller, task, inputData){
        RestApi.get(controller, task, inputData, function(response){
            var data = JSON.parse(response);
            data.forEach(item => {
                me.addOption(item.id, item.name);
            });
        });
    }
}

function AddLocationButton(language, selectLocation){
    var addLocationButton = document.getElementById('addLocation');
    addLocationButton.onclick = function(){
        var item = {id_location_type: $('#selectLocationType').val()};
        if(item.id_location_type !==''){
            var fields = [
                {type: 'text', title: language.location, variable: 'name'}
            ];
            openModalBox(language.new_location, fields, language.save, function(data){
                console.log(data);
            }, item);
        }
        else{
            alert(language.select_location_group);
        }
    }
    
    this.hide = function(){
        addLocationButton.style.display = 'none';
    }
    
    this.show = function(){
        addLocationButton.style.display = 'block';
    }
}
