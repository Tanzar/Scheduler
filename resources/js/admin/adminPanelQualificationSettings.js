/* 
 * This code is free to use, just remember to give credit.
 */

function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        initSupervisionsTable(language);
        initOugTable(language);
        initFacilityTypesTable(language);
    });
}

function initSupervisionsTable(language){
    var div = document.getElementById('supervisionLevels');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'name', width: 50, minWidth: 50}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelQualification', 
                task: 'getSupervisionLevels'
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.name, variable: 'name', limit: 50, required: true}
        ];
        openModalBox(language.new_supervision_level, fields, language.save, function(data){
            RestApi.post('AdminPanelQualification', 'saveSupervisionLevel', data,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
                });
        });
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            var fields = [
                {type: 'text', title: language.name, variable: 'name', limit: 50, required: true}
            ];
            openModalBox(language.edit_supervision_level, fields, language.save, function(data){
                RestApi.post('AdminPanelQualification', 'saveSupervisionLevel', data,
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        datatable.refresh();
                    },
                    function(response){
                        console.log(response.responseText);
                        alert(response.responseText);
                   });
            }, selected);
        }
        else{
            alert(language.select_supervision_level);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelQualification', 'changeSupervisionLevelStatus', {id: selected.id},
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
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
    return datatable;
}

function initOugTable(language){
    var div = document.getElementById('offices');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'location', width: 50, minWidth: 50}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelQualification', 
                task: 'getOugOffices'
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.name, variable: 'location', limit: 100, required: true}
        ];
        openModalBox(language.new_office, fields, language.save, function(data){
            RestApi.post('AdminPanelQualification', 'saveOugOffice', data,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
                });
        });
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            var fields = [
                {type: 'text', title: language.name, variable: 'location', limit: 100, required: true}
            ];
            openModalBox(language.edit_office, fields, language.save, function(data){
                RestApi.post('AdminPanelQualification', 'saveOugOffice', data,
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        datatable.refresh();
                    },
                    function(response){
                        console.log(response.responseText);
                        alert(response.responseText);
                   });
            }, selected);
        }
        else{
            alert(language.select_office);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelQualification', 'changeOugOfficeStatus', {id: selected.id},
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_office)
        }
    });
    return datatable;
}

function initFacilityTypesTable(language){
    var div = document.getElementById('facilityTypes');
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id', width: 30},
            { title: language.active, variable: 'active', width: 50},
            { title: language.name, variable: 'name', width: 50, minWidth: 50}
        ],
        dataSource : { 
            method: 'post', 
            address: getRestAddress(), 
            data: { 
                controller: 'AdminPanelQualification', 
                task: 'getFacilityTypes'
            } 
        }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton(language.add, function(){
        var fields = [
            {type: 'text', title: language.name, variable: 'name', limit: 50, required: true}
        ];
        openModalBox(language.new_facility_type, fields, language.save, function(data){
            RestApi.post('AdminPanelQualification', 'saveFacilityType', data,
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
                });
        });
    });
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            var fields = [
                {type: 'text', title: language.name, variable: 'name', limit: 50, required: true}
            ];
            openModalBox(language.edit_facility_type, fields, language.save, function(data){
                RestApi.post('AdminPanelQualification', 'saveFacilityType', data,
                    function(response){
                        var data = JSON.parse(response);
                        console.log(data);
                        alert(data.message);
                        datatable.refresh();
                    },
                    function(response){
                        console.log(response.responseText);
                        alert(response.responseText);
                   });
            }, selected);
        }
        else{
            alert(language.select_facility_type);
        }
    });
    datatable.addActionButton(language.change_status, function(selected){
        if(selected !== undefined){
            RestApi.post('AdminPanelQualification', 'changeFacilityTypeStatus', {id: selected.id},
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_facility_type)
        }
    });
    return datatable;
}