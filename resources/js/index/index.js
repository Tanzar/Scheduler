/* 
 * This code is free to use, just remember to give credit.
 */

function init(){
    var div = document.getElementById('myData');
    if(div !== undefined){
        var selectYear = document.getElementById('selectYear');
        var selectDate = document.getElementById('selectDate');
        RestApi.get('Index', 'getReport', {year : selectYear.value}, function(data){
            var json = JSON.parse(data);
            makeTables(div, json);
        });
        selectDate.onclick = function(){
            RestApi.get('Index', 'getReport', {year : selectYear.value}, function(data){
                var json = JSON.parse(data);
                makeTables(div, json);
            });
        }
    }
}

function makeTables(div, data) {
    console.log(data);
    while(div.lastChild){
        div.removeChild(div.lastChild);
    }
    
    if(data.schedule !== undefined){
        makeTable(div, data.schedule.title, data.schedule.cells, false);
    }
    if(data.inspector !== undefined){
        makeTable(div, data.inspector.title, data.inspector.cells, true);
    }
}

function makeTable(div, title, rows, showEmpty) {
    var table = document.createElement('table');
    table.setAttribute('class', 'standard-table');
    var thead = document.createElement('thead');
    var tr = document.createElement('tr');
    tr.setAttribute('class', 'standard-table-tr');
    var colspan = 1;
    if(rows[0].length > 1){
        colspan = rows[0].length + 1;
    }
    var td = document.createElement('td');
    td.textContent = title;
    td.setAttribute('colspan', colspan);
    td.setAttribute('class', 'standard-table-td');
    tr.appendChild(td);
    thead.appendChild(tr);
    table.appendChild(thead);
    var tbody = document.createElement('tbody');
    rows.forEach((row, index) => {
        var show = false;
        var tr = document.createElement('tr');
        tr.setAttribute('class', 'standard-table-tr');
        var sum = 0;
        row.forEach((cell, index) => {
            var td = document.createElement('td');
            if(parseInt(cell) === 0){
                td.textContent = '';
            }
            else{
                td.textContent = cell;
            }
            td.setAttribute('class', 'standard-table-td');
            if(index !== 0 && cell !== ''){
                show = true;
                sum += parseInt(cell);
            }
            tr.appendChild(td);
        })
        var text = (index === 0) ? 'Σ' : sum;
        var td = document.createElement('td');
        td.textContent = text;
        td.setAttribute('class', 'standard-table-td');
        tr.appendChild(td);
        if(showEmpty || show){
            tbody.appendChild(tr);
        }
    })
    
    table.appendChild(tbody);
    div.appendChild(table);
}