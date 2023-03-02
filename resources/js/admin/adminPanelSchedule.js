/* 
 * This code is free to use, just remember to give credit.
 */

function AdminPanelSchedule(){
    RestApi.getInterfaceNamesPackage(function(language){
        var selectDatasetButton = document.getElementById('selectDatasetButton');
        var entries = document.getElementById('entries');
        
        
        var dateRange = new DaysRange(new Date());
        var datasource = {
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'AdminPanelSchedule',
                task: 'getUserEntries',
                startDate: dateRange.getStart(),
                endDate: dateRange.getEnd(),
                username: ''
            }
        };
        var config = {
            columns: [
                {title: language.active, variable: 'active', width: 50, minWidth: 50},
                {title: language.start_date, variable: 'start', width: 150, minWidth: 150},
                {title: language.end_date, variable: 'end', width: 150, minWidth: 150},
                {title: language.location, variable: 'location', width: 250, minWidth: 150}
            ],
            dataSource: datasource
        }
        var datatable = new Datatable(entries, config);
        datatable.addActionButton(language.change_status, function(selected){
            if(selected !== undefined){
                RestApi.post('AdminPanelSchedule', 'changeEntryStatus', selected,
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
                alert(language.select_user)
            }
        });
        
        
        
        selectDatasetButton.onclick = function(){
            if($('#selectUser').val() === ''){
                alert(language.select_user);
            }
            else{
                var dateRange = new DaysRange(new Date($('#selectDate').val()));
                var datasource = {
                    method: 'get',
                    address: getRestAddress(),
                    data: {
                        controller: 'AdminPanelSchedule',
                        task: 'getUserEntries',
                        startDate: dateRange.getStart(),
                        endDate: dateRange.getEnd(),
                        username: $('#selectUser').val()
                    }
                };
                datatable.setDatasource(datasource);
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
