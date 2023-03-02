/* 
 * This code is free to use, just remember to give credit.
 */

function Timetable(weekdayNames, div, data, dayStart, dayEnd, groupBy, groupTitle, cellOnClick){
    
    dayStart.setHours(0,0,0,0);
    dayEnd.setHours(23,59,59,0);
    
    var minutesPerPixel = 5;
    var cellHeight = 40;
    var separation = 20;
    this.rangeStart = dayStart;
    this.rangeEnd = dayEnd;
    this.data = data;
    this.cellOnClick = cellOnClick;
    this.div = div;
    this.weekdayNames = weekdayNames;
    var ele = div;
    
    
    this.countDays = function(dayStart, dayEnd){
        var difference = dayEnd.getTime() - dayStart.getTime();
        var totalDays = Math.ceil(difference / (1000 * 3600 * 24));
        return totalDays;
    }
    
    function countDayWidth(){
        return (24 * 60) / minutesPerPixel;
    }
    
    function calculateTimetableElementLeft(dateTime){
        var start = new Date(dayStart);
        if(dateTime < start){
            return 0;
        }
        else{
            var diff = Math.abs(dateTime - start);
            var mins = Math.floor((diff/1000)/60);
            return mins / minutesPerPixel;
        }
    }
    
    this.countTotalWidth = function(){
        var days = this.countDays(this.rangeStart, this.rangeEnd);
        var width = days * countDayWidth();
        return width;
    }
    
    function formDatesArray(start, end) {
        var date = new Date(start.getTime());
        var difference = end.getTime() - start.getTime();
        var totalDays = Math.ceil(difference / (1000 * 3600 * 24));
        var dates = [];
        for(var i = 0; i < totalDays; i++){
            var newDate = new Date(date.getTime());
            newDate.setDate(newDate.getDate() + i);
            dates.push(newDate);
        }
        return dates;
    }
    
    function calculateCellWidth(entry){
        var start = new Date(entry.start);
        if(start < new Date(dayStart)){
            start = new Date(dayStart);
        }
        var end = new Date(entry.end);
        if(new Date(dayEnd) < end){
            end = new Date(dayEnd);
        }
        var diff = Math.abs(end - start);
        return  (Math.floor((diff/1000)/60) / minutesPerPixel) - 2;
    }
    
    function groupData(data) {
        var grouped = [];
        data.forEach(entry => {
            var groupIndex = -1;
            grouped.forEach((item, i) => {
                if(item.name === entry[groupBy]){
                    groupIndex = i;
                }
            });
            if(groupIndex === -1){
                grouped.push({
                    name: entry[groupBy],
                    title: entry[groupTitle],
                    rows: [[entry]],
                    height: cellHeight + 2 * separation
                });
            }
            else{
                var rowNumber = -1;
                for(var i = 0; i < grouped[groupIndex].rows.length; i++){
                    var canBeAdded = true;
                    grouped[groupIndex].rows[i].forEach(item => {
                        if(rowNumber === -1){
                            var startEntry = new Date(entry.start);
                            var endEntry = new Date(entry.end);
                            var startItem = new Date(item.start);
                            var endItem = new Date(item.end);
                            if((startEntry < endItem && endEntry > startItem)){
                                canBeAdded = false;
                            }
                        }
                    });
                    if(canBeAdded){
                        rowNumber = i;
                    }
                }
                if(rowNumber === -1){
                    rowNumber = grouped[groupIndex].rows.length;
                    grouped[groupIndex].rows.push([]);
                    grouped[groupIndex].height += cellHeight + 2 * separation;
                }
                grouped[groupIndex].rows[rowNumber].push(entry);
            }
        });
        return grouped;
    }
    
    function createGroupsLabel(grouped) {
        var label = document.createElement('div');
        label.setAttribute('class', 'timetable-groups-label');
        var corner = document.createElement('div');
        corner.setAttribute('class', 'timetable-group');
        corner.style.width = '50px';
        corner.style.height = '50px';
        label.appendChild(corner);
        var keys = Object.keys(grouped);
        var height = 52;
        keys.forEach(key => {
            var group = document.createElement('div');
            group.setAttribute('class', 'timetable-group');
            group.style.height = grouped[key].height + 'px';
            group.textContent = grouped[key].title;
            height += grouped[key].height + 2;
            label.appendChild(group);
        });
        label.style.height = height + 'px';
        return label;
    }
    
    function createDispalyBoard(height, dates, data, timetable) {
        var board = document.createElement('div');
        board.setAttribute('class', 'timetable-data');
        board.style.height = height;
        var width = createHeaders(dates, timetable, board);
        
        board.style.width = width + 'px';
        var top = 50;
        data.forEach(group => {
            var frame = document.createElement('div');
            frame.setAttribute('class', 'timetable-group-frame');
            frame.style.left = '0px';
            var groupTop = top + 2;
            frame.style.top = groupTop + 'px';
            frame.style.height = group.height + 'px';
            frame.style.width = width + 'px';
            top += group.height+ 2;
            board.append(frame);
            var rowTop = 0;
            group.rows.forEach(row => {
                row.forEach(entry => {
                    createCell(entry, rowTop, frame);
                });
                rowTop += cellHeight + 2 * separation;
            })
        });
        
        
        return board;
    }
    
    function createHeaders(dates, timetable, board) {
        var headers = document.createElement('div');
        headers.setAttribute('class', 'timetable-headers');
        var width = 0;
        var dayWidth = countDayWidth();
        dates.forEach(date => {
            var head = document.createElement('div');
            head.setAttribute('class', 'timetable-head');
            var weekday = '';
            if(date.getDay() === 0){
                weekday = timetable.weekdayNames[7];
            }
            else{
                weekday = timetable.weekdayNames[date.getDay()];
            }
            if(date.getDay() === 0 || date.getDay() === 6){
                var time = new Date(date);
                time.setHours(0);
                var dayOffLeft = calculateTimetableElementLeft(time);
                var dayOffBackground = document.createElement('div');
                dayOffBackground.setAttribute('class', 'timetable-day-off');
                dayOffBackground.style.width = countDayWidth() + 'px';
                dayOffBackground.style.left = dayOffLeft + 'px';
                board.appendChild(dayOffBackground);
            }
            var textDiv = document.createElement('div');
            textDiv.textContent = date.getDate() + '.' + (date.getMonth() + 1) + '.' + date.getFullYear() + ' ' + weekday;
            textDiv.style.height = '40px';
            head.appendChild(textDiv);
            head.style.width = dayWidth - 2 + 'px';
            width += dayWidth;
            headers.appendChild(head);
            var hours = document.createElement('div');
            hours.setAttribute('class', 'timetable-head-hours');
            createHoursBars(date, board, hours);
            head.appendChild(hours);
        });
        board.appendChild(headers);
        return width;
    }
    
    function createHoursBars(date, board, hours) {
        for(var hour = 0; hour < 24; hour++){
            var bar = document.createElement('div');
            if(hour === 0){
                bar.setAttribute('class', 'timetable-line-day');
            }
            else{
                bar.setAttribute('class', 'timetable-line');
            }
            var time = new Date(date);
            time.setHours(hour);
            var barLeft = calculateTimetableElementLeft(time);
            bar.style.left = barLeft + 'px';
            board.appendChild(bar);
            var hourText = document.createElement('div');
            hourText.setAttribute('class', 'timetable-head-hour-text');
            hourText.textContent = hour;
            var hourTextWidth = 60 / minutesPerPixel;
            hourText.style.width = (hourTextWidth - 1) + 'px';
            hours.appendChild(hourText);
        }
    }
    
    function createCell(entry, rowTop, frame) {
        var cell = document.createElement('div');
        cell.setAttribute('class', 'timetable-entry-cell');
        cell.style.left = calculateTimetableElementLeft(new Date(entry.start)) + 'px';
        cell.style.top = (separation + rowTop) + 'px';
        cell.style.height = cellHeight + 'px';
        cell.style.width = calculateCellWidth(entry) + 'px';
        cell.style.backgroundColor = entry.color;
        cell.textContent = entry.title;
        var overflow = document.createElement('div');
        overflow.setAttribute('class', 'timetable-entry-cell-overflow');
        overflow.style.left = calculateTimetableElementLeft(new Date(entry.start)) + 'px';
        overflow.style.top = rowTop + 'px';
        overflow.style.height = cellHeight + 2 * separation + 'px';
        overflow.style.minWidth = Math.max(cellHeight + 2 * separation, calculateCellWidth(entry)) + 'px';
        overflow.style.maxWidth = (1.4 * Math.max(cellHeight + 2 * separation, calculateCellWidth(entry))) + 'px';
        overflow.style.backgroundColor = entry.color;
        overflow.textContent = entry.title;
        overflow.style.display = 'none';
        overflow.onclick = function(){
            openModalBox(entry.group, [
                    {type: 'display', title: entry.desc}
                ]);
        }
        overflow.onmouseout = function(){
            overflow.style.display = 'none';
        }
        cell.onmouseenter = function(){
            overflow.style.display = 'flex';
        }
        frame.appendChild(cell);
        frame.appendChild(overflow);
    }
    
    let pos = { top: 0, left: 0, x: 0, y: 0 };

    const mouseUpHandler = function () {
        document.removeEventListener('mousemove', mouseMoveHandler);
        document.removeEventListener('mouseup', mouseUpHandler);

        ele.style.cursor = 'grab';
        ele.style.removeProperty('user-select');
    };

    const mouseMoveHandler = function (e) {
        // How far the mouse has been moved
        const dx = e.clientX - pos.x;
        const dy = e.clientY - pos.y;

        // Scroll the element
        ele.scrollTop = pos.top - dy;
        ele.scrollLeft = pos.left - dx;
    };

    const mouseDownHandler = function (e) {
        pos = {
            // The current scroll
            left: ele.scrollLeft,
            top: ele.scrollTop,
            // Get the current mouse position
            x: e.clientX,
            y: e.clientY,
        };
        ele.style.cursor = 'grabbing';
        ele.style.userSelect = 'none';
        document.addEventListener('mousemove', mouseMoveHandler);
        document.addEventListener('mouseup', mouseUpHandler);
    };
    
    ele.addEventListener('mousedown', mouseDownHandler);
    
    
    this.load = function(){
        var grouped = groupData(data);
        this.div.setAttribute('class', 'timetable');

        while(this.div.firstChild){
            this.div.removeChild(this.div.firstChild);
        }

        this.div.style.maxWidth = this.countTotalWidth() + 'px';
        var label = createGroupsLabel(grouped);
        var height = label.style.height;
        var dates = formDatesArray(this.rangeStart, this.rangeEnd);
        var board = createDispalyBoard(height, dates, grouped, this);
        this.div.appendChild(board);
        this.div.appendChild(label);
    }
    
    this.load();
}