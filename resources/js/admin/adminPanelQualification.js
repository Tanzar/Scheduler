/* 
 * This code is free to use, just remember to give credit.
 */

function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        var qualificationsTable = initQualificatinsTable(language);
        var personsTable = initPersonsTable(language, qualificationsTable);
        initQualificationsTableButtons(language, qualificationsTable, personsTable);
        
        $('#searchButton').click(function(){
            personsTable.setDatasource({ 
                method: 'post', 
                address: getRestAddress(), 
                data: { 
                    controller: 'AdminPanelQualification', 
                    task: 'getPersons',
                    name: document.getElementById('nameSearch').value,
                    surname: document.getElementById('surnameSearch').value,
                } 
            });
        });
    });
}

function initPersonsTable(language, qualificationTable){
    var div = document.getElementById('persons');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name_person, variable: 'name', width: 50, minWidth: 50},
            { title: language.surname, variable: 'surname', width: 50, minWidth: 50},
            { title: language.birthplace, variable: 'birthplace', width: 100, minWidth: 100},
            { title: language.birthdate, variable: 'birthday', width: 100, minWidth: 100}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelQualification', 
                task: 'getPersons',
                name: '---',
                surname: '---'
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        RestApi.post('AdminPanelQualification', 'getEducationLevels', {},
            function(response){
                var data = JSON.parse(response);
                var fields = [
                    {type: 'text', title: language.name_person, variable: 'name', limit: 50, required: true},
                    {type: 'text', title: language.surname, variable: 'surname', limit: 50, required: true},
                    {type: 'text', title: language.birthplace, variable: 'birthplace', limit: 150},
                    {type: 'date', title: language.birthdate, variable: 'birthday'},
                    {type: 'select', title: language.select_education_level, variable: 'education_level', options: data, required: true},
                    {type: 'text', title: language.home_town, variable: 'home_town', limit: 50},
                    {type: 'text', title: language.post_code, variable: 'post_code', limit: 10},
                    {type: 'text', title: language.street, variable: 'street', limit: 50},
                    {type: 'text', title: language.home_number, variable: 'home_number', limit: 15}
                ];
                openModalBox(language.new_person, fields, language.save, function(data){
                    RestApi.post('AdminPanelQualification', 'savePerson', data,
                        function(response){
                            var data = JSON.parse(response);
                            console.log(data);
                            alert(data.message);
                            datatable.refresh();
                            qualificationTable.setDatasource({ 
                                method: 'post', 
                                address: getRestAddress(), 
                                data: { 
                                    controller: 'AdminPanelQualification', 
                                    task: 'getPersonQualifications',
                                    id_person: 0
                                } 
                            });
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
            RestApi.post('AdminPanelQualification', 'getEducationLevels', {},
                function(response){
                    var data = JSON.parse(response);
                    var fields = [
                        {type: 'text', title: language.name_person, variable: 'name', limit: 50, required: true},
                        {type: 'text', title: language.surname, variable: 'surname', limit: 50, required: true},
                        {type: 'text', title: language.birthplace, variable: 'birthplace', limit: 150},
                        {type: 'date', title: language.birthdate, variable: 'birthday'},
                        {type: 'select', title: language.select_education_level, variable: 'education_level', options: data, required: true},
                        {type: 'text', title: language.home_town, variable: 'home_town', limit: 50},
                        {type: 'text', title: language.post_code, variable: 'post_code', limit: 10},
                        {type: 'text', title: language.street, variable: 'street', limit: 50},
                        {type: 'text', title: language.home_number, variable: 'home_number', limit: 15}
                    ];
                    openModalBox(language.edit_person, fields, language.save, function(data){
                        RestApi.post('AdminPanelQualification', 'savePerson', data,
                            function(response){
                                var data = JSON.parse(response);
                                console.log(data);
                                alert(data.message);
                                datatable.refresh();
                                qualificationTable.setDatasource({ 
                                method: 'post', 
                                address: getRestAddress(), 
                                data: { 
                                    controller: 'AdminPanelQualification', 
                                    task: 'getPersonQualifications',
                                    id_person: 0
                                } 
                            });
                            },
                            function(response){
                                console.log(response.responseText);
                                alert(response.responseText);
                        });
                    }, selected);
                });
        }
        else{
            alert(language.select_person);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelQualification', 'changePersonStatus', {id: selected.id},
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                    qualificationTable.setDatasource({ 
                        method: 'post', 
                        address: getRestAddress(), 
                        data: { 
                            controller: 'AdminPanelQualification', 
                            task: 'getPersonQualifications',
                            id_person: 0
                        } 
                    });
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_person)
        }
    });
    datatable.setOnSelect(function(selected){
        if(selected !== undefined){
            qualificationTable.setDatasource({ 
                method: 'post', 
                address: getRestAddress(), 
                data: { 
                    controller: 'AdminPanelQualification', 
                    task: 'getPersonQualifications',
                    id_person: selected.id
                } 
            });
        }
    });
    datatable.setOnUnselect(function(){
        qualificationTable.setDatasource({ 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelQualification', 
                task: 'getPersonQualifications',
                id_person: 0
            } 
        });
    });
    return datatable;
}

function initQualificatinsTable(language) {
    var div = document.getElementById('qualifications');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.number, variable: 'number', width: 100, minWidth: 100},
            { title: language.date, variable: 'date', width: 80, minWidth: 80},
            { title: language.position, variable: 'position', width: 150, minWidth: 150},
            { title: language.facility, variable: 'facility', width: 150, minWidth: 150},
            { title: language.facility_type, variable: 'facility_type', width: 100, minWidth: 100},
            { title: language.supervision_level, variable: 'supervision_level', width: 120, minWidth: 120},
            { title: language.oug, variable: 'oug', width: 75, minWidth: 75}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelQualification', 
                task: 'getPersonQualifications',
                id_person: 0
            } 
        }
    };
    var datatable = new Datatable(div, config);
    return datatable;
}

function initQualificationsTableButtons(language, qualificationsTable, personsTable){
    qualificationsTable.addActionButton(language.add, function(){
        RestApi.post('AdminPanelQualification', 'getQualificationOptions', {},
            function(response){
                var data = JSON.parse(response);
                var ougs = [];
                data.oug.forEach(item => {
                    ougs.push({value: item.id, title: item.location});
                });
                var facilityTypes = [];
                data.facilityTypes.forEach(item => {
                    facilityTypes.push({ value: item.id, title: item.name});
                });
                var supervisionLevels = [];
                data.supervisionLevels.forEach(item => {
                    supervisionLevels.push({value: item.id, title: item.name});
                });
                var fields = [
                    {type: 'text', title: language.number, variable: 'number', limit: 50, required: true},
                    {type: 'date', title: language.date, variable: 'date'},
                    {type: 'select', title: language.select_oug, variable: 'id_oug_offices', options: ougs, required: true},
                    {type: 'select', title: language.select_facility_type, variable: 'id_facility_type', options: facilityTypes, required: true},
                    {type: 'select', title: language.select_supervision_level, variable: 'id_supervision_level', options: supervisionLevels, required: true},
                    {type: 'textarea', title: language.position, variable: 'position', limit: 255, width: 30, height: 3, required: true},
                    {type: 'textarea', title: language.facility, variable: 'facility', limit: 255, width: 30, height: 3, required: true},
                    {type: 'textarea', title: language.specialization, variable: 'specialization', limit: 100, width: 30, height: 3, required: true}
                ];
                openModalBox(language.new_qualification, fields, language.save, function(data){
                    var person = personsTable.getSelected();
                    if(person !== undefined){
                        data.id_person = person.id;
                        RestApi.post('AdminPanelQualification', 'saveQualification', data,
                            function(response){
                                var data = JSON.parse(response);
                                console.log(data);
                                alert(data.message);
                                qualificationsTable.refresh();
                            },
                            function(response){
                                console.log(response.responseText);
                                alert(response.responseText);
                        });
                    }
                });
            });
    });
    qualificationsTable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelQualification', 'getEducationLevels', {},
                function(response){
                    var data = JSON.parse(response);
                    var ougs = [];
                    data.oug.forEach(item => {
                        ougs.push({value: item.id, title: item.location});
                    });
                    var facilityTypes = [];
                    data.facilityTypes.forEach(item => {
                        facilityTypes.push({ value: item.id, title: item.name});
                    });
                    var supervisionLevels = [];
                    data.supervisionLevels.forEach(item => {
                        supervisionLevels.push({value: item.id, title: item.name});
                    });
                    var fields = [
                        {type: 'text', title: language.number, variable: 'number', limit: 50, required: true},
                        {type: 'date', title: language.date, variable: 'date'},
                        {type: 'select', title: language.select_oug, variable: 'id_oug_offices', options: ougs, required: true},
                        {type: 'select', title: language.select_facility_type, variable: 'id_facility_type', options: facilityTypes, required: true},
                        {type: 'select', title: language.select_supervision_level, variable: 'id_supervision_level', options: supervisionLevels, required: true},
                        {type: 'textarea', title: language.position, variable: 'position', limit: 255, width: 30, height: 3, required: true},
                        {type: 'textarea', title: language.facility, variable: 'facility', limit: 255, width: 30, height: 3, required: true},
                        {type: 'textarea', title: language.specialization, variable: 'specialization', limit: 100, width: 30, height: 3, required: true}
                    ];
                    openModalBox(language.edit_qualification, fields, language.save, function(data){
                        var person = personsTable.getSelected();
                        if(person !== undefined){
                            data.id_person = person.id;
                            RestApi.post('AdminPanelQualification', 'saveQualification', data,
                                function(response){
                                    var data = JSON.parse(response);
                                    console.log(data);
                                    alert(data.message);
                                    qualificationsTable.refresh();
                                },
                                function(response){
                                    console.log(response.responseText);
                                    alert(response.responseText);
                            });
                        }
                    }, selected);
                });
        }
        else{
            alert(language.select_person);
        }
    });
    qualificationsTable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelQualification', 'changeQualificationStatus', {id: selected.id},
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    qualificationsTable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_person)
        }
    });
}