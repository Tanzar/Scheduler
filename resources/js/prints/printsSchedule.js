/* 
 * This code is free to use, just remember to give credit.
 */


function init(username){
    RestApi.getInterfaceNamesPackage(function(language){
        var selectMonth = document.getElementById('selectMonth');
        var selectYear = document.getElementById('selectYear');
        var selectUser = document.getElementById('selectUser');

        function updateUserList(){
            if(selectUser !== null){
                RestApi.post('PrintsSchedule', 'getUsers', {
                        month: selectMonth.value,
                        year: selectYear.value
                    },function(response){
                        var users = JSON.parse(response);
                        while(selectUser.lastChild){
                            selectUser.removeChild(selectUser.lastChild);
                        }
                        var placeholder = document.createElement('option');
                        placeholder.value = username;
                        placeholder.disabled = true;
                        placeholder.selected = true;
                        placeholder.placeholder = true;
                        placeholder.textContent = language.select_user;
                        selectUser.appendChild(placeholder);
                        users.forEach(user => {
                            var option = document.createElement('option');
                            option.value = user.username;
                            option.textContent = user.name + ' ' + user.surname;
                            selectUser.appendChild(option);
                        })
                    });
            }
        }

        selectMonth.onchange = function(){
            updateUserList();
        }

        selectYear.onchange = function(){
            updateUserList();
        }

        $('#attendanceList').click(function(){
            FileApi.post('PrintsSchedule', 'generateAttendanceList', {
                month: selectMonth.value,
                year: selectYear.value
            });
        });

        $('#notificationList').click(function(){
            FileApi.post('PrintsSchedule', 'generateNotificationList', {
                month: selectMonth.value,
                year: selectYear.value
            });
        });
        
        $('#timesheets').click(function(){
            var dataToSend = {
                month: selectMonth.value,
                year: selectYear.value
            }
            if(selectUser !== null){
                dataToSend.username = selectUser.value;
            }
            FileApi.post('PrintsSchedule', 'generateTimesheets', dataToSend);
        });

        $('#workCard').click(function(){
            var dataToSend = {
                month: selectMonth.value,
                year: selectYear.value
            }
            if(selectUser !== null){
                dataToSend.username = selectUser.value;
            }
            FileApi.post('PrintsSchedule', 'generateWorkCard', dataToSend);
        });
    });
}