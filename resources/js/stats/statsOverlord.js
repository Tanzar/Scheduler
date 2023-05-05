/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        initTable(language);
    });
}

function initTable(language) {
    var div = document.getElementById('inspections');
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'StatsOverlord',
            task: 'getInspections',
            month: $('#selectMonth').val(),
            year: $('#selectYear').val()
        }
    };
    var config = {
        columns: [
            {title: language.user, variable: 'UserWithSUZUG', width: 250, minWidth: 250},
            {title: language.location, variable: 'InspectableLocation', width: 250, minWidth: 250},
            {title: language.activity_name, variable: 'Activity', width: 200, minWidth: 200},
            {title: language.level, variable: 'Level', width: 50, minWidth: 50},
            {title: language.value, variable: 'value', width: 50, minWidth: 50}
        ],
        dataSource: datasource,
        selectMultiple : true
    }
    var datatable = new Datatable(div, config);
    $('#selectDate').click(function(){
        datatable.setDatasource({
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'StatsOverlord',
                task: 'getInspections',
                month: $('#selectMonth').val(),
                year: $('#selectYear').val()
            }
        });
    });
}