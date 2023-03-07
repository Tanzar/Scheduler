/* 
 * This code is free to use, just remember to give credit.
 */

function scheduleAll(){
    RestApi.getInterfaceNamesPackage(function(language){    
        var goto = document.getElementById('goTo');
        goto.onclick = function(){
            var date = document.getElementById('displayDate');
            var range = new DaysRange(new Date(date.value));
            RestApi.post('ScheduleUser', 'getTimestableData', 
                {startDate: range.getStart(), endDate: range.getEnd()}, 
                function(response){
                    var data = JSON.parse(response);
                    var parsed = [];
                    data.entries.forEach(entry => {
                        var title = entry.location;
                        var desc = entry.name + ' ' + entry.surname + '\n';
                        desc += entry.activity_name + '\n';
                        desc += entry.location + '\n';
                        desc += language.start + ': ' + entry.start + '\n';
                        desc += language.end + ': ' + entry.end + '\n';
                        desc += language.description + ': ' + entry.description + '\n';
                        var item = {
                            title: title, 
                            start: entry.start, 
                            end: entry.end, 
                            username: entry.username,
                            group: entry.short,
                            color: entry.color,
                            desc: desc
                        }
                        parsed.push(item);
                    });
                    RestApi.getLanguagePackage(function(package){
                        var timetable = new Timetable(
                            package.weekdays,
                            document.getElementById('timetable'), 
                            parsed, 
                            new Date(range.getStart()), 
                            new Date(range.getEnd()), 
                            'username', 
                            'group',
                            function(item){
                                alert(item.desc);
                            },
                            data.groups);
                    });
            });
        }
        var nextDay = document.getElementById('nextDay');
        nextDay.onclick = function(){
            var date = document.getElementById('displayDate');
            var nextDate = new Date(date.value);
            nextDate.setDate(nextDate.getDate() + 7);
            var nextValue = nextDate.getFullYear() + '-';
            if(nextDate.getMonth() + 1 < 10){
                nextValue += '0' + (nextDate.getMonth() + 1) + '-';
            }
            else{
                nextValue += (nextDate.getMonth() + 1) + '-';
            }
            if(nextDate.getDate() < 10){
                nextValue += '0' + nextDate.getDate();
            }
            else{
                nextValue += nextDate.getDate();
            }
            date.value = nextValue;
            goto.onclick();
        }
        
        var previousDay = document.getElementById('previousDay');
        previousDay.onclick = function(){
            var date = document.getElementById('displayDate');
            var previousDate = new Date(date.value);
            previousDate.setDate(previousDate.getDate() - 7);
            var nextValue = previousDate.getFullYear() + '-';
            if(previousDate.getMonth() + 1 < 10){
                nextValue += '0' + (previousDate.getMonth() + 1) + '-';
            }
            else{
                nextValue += (previousDate.getMonth() + 1) + '-';
            }
            if(previousDate.getDate() < 10){
                nextValue += '0' + previousDate.getDate();
            }
            else{
                nextValue += previousDate.getDate();
            }
            date.value = nextValue;
            goto.onclick();
        }
        
        var date = document.getElementById('displayDate');
        var range = new DaysRange(new Date(date.value));
        
        RestApi.post('ScheduleUser', 'getTimestableData', 
                {startDate: range.getStart(), endDate: range.getEnd()}, 
                function(response){
                    var data = JSON.parse(response);
                    var parsed = [];
                    data.entries.forEach(entry => {
                        var title = entry.location;
                        var desc = entry.name + ' ' + entry.surname + '\n';
                        desc += entry.activity_name + '\n';
                        desc += entry.location + '\n';
                        desc += language.start + ': ' + entry.start + '\n';
                        desc += language.end + ': ' + entry.end + '\n';
                        desc += language.description + ': ' + entry.description + '\n';
                        var item = {
                            title: title, 
                            start: entry.start, 
                            end: entry.end, 
                            username: entry.username,
                            group: entry.short,
                            color: entry.color,
                            desc: desc
                        }
                        parsed.push(item);
                    });
                    RestApi.getLanguagePackage(function(package){
                        var timetable = new Timetable(
                            package.weekdays,
                            document.getElementById('timetable'), 
                            parsed, 
                            new Date(range.getStart()), 
                            new Date(range.getEnd()), 
                            'username', 
                            'group',
                            function(item){
                                alert(item.desc);
                            },
                            data.groups);
                    });
            });
    });
}


function DaysRange(date){
    var start = new Date(date);
    start.setDate(date.getDate() - 3);
    
    var d = new Date(date);
    var day = d.getDay(),
            diff = d.getDate() - day + (day == 0 ? -6:1); // adjust when day is sunday
    var start = new Date(d.setDate(diff));
    var end = new Date(start);
    end.setDate(start.getDate() + 6);
    
    this.getStart = function(){
        return start.toISOString().split('T')[0];
    }
    
    this.getEnd = function(){
        return end.toISOString().split('T')[0];
    }
}