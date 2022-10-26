/* 
 * This code is free to use, just remember to give credit.
 */


function init(){
    var selectMonth = document.getElementById('selectMonth');
    var selectYear = document.getElementById('selectYear');
    
    $('#attendanceList').click(function(){
        FileApi.post('PrintsSchedule', 'generateAttendanceList', {
            month: selectMonth.value,
            year: selectYear.value
        });
    });
}