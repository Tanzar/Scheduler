/* 
 * This code is free to use, just remember to give credit.
 */

function init(){
    var today = new Date();
    var start = new Date();
    start.setDate(today.getDate() - 3);
    var end = new Date();
    end.setDate(today.getDate() + 3);
    
    
    var timetable = new Timetable(
        [
            {title: "test1", start: '2022-05-23 7:30', end: '2022-05-23 15:30', group: 0, color: '#0066ff'},
            {title: "test2", start: '2022-05-22 21:30', end: '2022-05-23 6:30', group: 1},
            {title: "test3", start: '2022-05-24 6:30', end: '2022-05-24 11:30', group: 0},
            {title: "test4", start: '2022-05-24 7:00', end: '2022-05-24 15:00', group: 1, color: '#0066ff'},
            {title: "test5", start: '2022-05-25 0:30', end: '2022-05-25 7:30', group: 0},
            {title: "test6", start: '2022-05-25 7:30', end: '2022-05-25 15:30', group: 1},
            {title: "test7", start: '2022-05-26 7:30', end: '2022-05-26 15:30', group: 0},
            {title: "test8", start: '2022-05-26 7:30', end: '2022-05-26 15:30', group: 1},
            {title: "test9", start: '2022-05-26 22:30', end: '2022-05-27 5:30', group: 0, color: '#0066ff'},
            {title: "test10", start: '2022-05-27 7:30', end: '2022-05-27 15:30', group: 1},
            {title: "test11", start: '2022-05-28 7:30', end: '2022-05-28 15:30', group: 0},
            {title: "test12", start: '2022-05-27 22:30', end: '2022-05-28 6:30', group: 1},
            {title: "test13", start: '2022-05-29 7:30', end: '2022-05-29 15:30', group: 0},
            {title: "test14", start: '2022-05-29 7:30', end: '2022-05-29 15:30', group: 1, color: 'red'},
            {title: "test15", start: '2022-05-30 7:30', end: '2022-05-30 15:30', group: 0}
        ], start, end, 'group', 
        function(item){
            console.log(item);
        });
}
