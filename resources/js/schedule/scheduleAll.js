/* 
 * This code is free to use, just remember to give credit.
 */

function scheduleAll(){
    RestApi.getInterfaceNamesPackage(function(language){    
        var goto = document.getElementById('goTo');
        goto.onclick = function(){
            var date = document.getElementById('displayDate');
            var range = new DaysRange(new Date(date.value));
            RestApi.post('ScheduleUser', 'getAllEntries', {startDate: range.getStart(), endDate: range.getEnd()}, function(response){
                var data = JSON.parse(response);
                console.log(data);
                var parsed = [];
                data.forEach(entry => {
                    var title = entry.short + ' - ' + entry.location;
                    var desc = entry.name + ' ' + entry.surname + '\n';
                    desc += entry.activity_name + '\n';
                    desc += entry.location + '\n';
                    desc += language.start + ': ' + entry.start + '\n';
                    desc += language.end + ': ' + entry.end + '\n';
                    var item = {
                        title: title, 
                        start: entry.start, 
                        end: entry.end, 
                        username: entry.username,
                        color: entry.color,
                        desc: desc
                    }
                    parsed.push(item);
                });
                RestApi.getLanguagePackage(function(package){
                    var timetable = new Timetable(
                        package.weekdays,
                        document.getElementById('timetable'), 
                        parsed, new Date(range.getStart()), new Date(range.getEnd()), 'username', 
                        function(item){
                            alert(item.desc);
                    });
                });
            });
        }
        
        var date = document.getElementById('displayDate');
        var range = new DaysRange(new Date(date.value));
        
        RestApi.post('ScheduleUser', 'getAllEntries', {startDate: range.getStart(), endDate: range.getEnd()}, function(response){
            var data = JSON.parse(response);
            console.log(data);
            var parsed = [];
            data.forEach(entry => {
                var title = entry.short + ' - ' + entry.location;
                var desc = entry.name + ' ' + entry.surname + '\n';
                desc += entry.activity_name + '\n';
                desc += entry.location + '\n';
                desc += language.start + ': ' + entry.start + '\n';
                desc += language.end + ': ' + entry.end + '\n';
                var item = {
                    title: title, 
                    start: entry.start, 
                    end: entry.end, 
                    username: entry.username,
                    color: entry.color,
                    desc: desc
                }
                parsed.push(item);
            });
            RestApi.getLanguagePackage(function(package){
                var timetable = new Timetable(
                    package.weekdays,
                    document.getElementById('timetable'), 
                    parsed, new Date(range.getStart()), new Date(range.getEnd()), 'username', 
                    function(item){
                        alert(item.desc);
                });
            });
        });
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