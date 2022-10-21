/*
 * Creates table for data not as configurable as datatables lib but its simple version
 * Variables:
 * a) div - html element (preferably div) to whitch table will attach
 * b) config - object containing table configuration
 * - example {columns: columns, data: [], dataSource: datasource }
 * - columns is array of column settings 
 *  { title:'', variable: '', minWidth: 0, maxWidth: 0, width: 0}
 *  title is text displayed in thead, 
 *  variable tells whitch value from data will be put in column,
 *  width set corresponding parameters for html
 *  - data is seting array as information to be shown in table
 *  - dataSource is setting from where data will be loaded (! it uses ajax so jQuery is required)
 *  example datasource { method: 'get', address: '', data: { } )
 *  method can only be get/post
 *  address from where data will be loaded (for this project base it mostly will be paths.handleRequest)
 *  data - additional information send with request (for this project mostly { task: 'functionName'}
 *  
 *  Addiitonal methods to customize datatable:
 *  1) addActionButton(text, action) - adds button atop table
 *  - text - shows on button
 *  - action - function(selected){}, allows to set action for selected items
 *  2) enable / disable SelectMultiple() - allows / disallows selection of multiple items
 *  3) getSelected - returns selected items (array if enabled select multiple), undefined if no items where selected
 *  4) refresh - clears table and requests data again
 *  5) refreshWithWait - ! important ! clears data and puts it in wait mode
 *  6) endWait - ends wait mode and loads data
 *  7) setDatasource - sets datasource ! data must still match columns !
 *  8) setOn Select / Unselect - sets function called when selecting / unselecting item in table (function(selected){})
 *  9) selectRowsWhere(field, values) - selects tows where value in field is equal to one of values (array) 
 */
function Datatable(div, config){
    this.config = config;
    this.data = config.data;
    this.columns = config.columns;
    this.div = div;
    this.div.setAttribute('class', 'datatable-main');
    this.waitMode = false;
    this.buttonsContainer = document.createElement('div');
    this.buttonsContainer.setAttribute('class', 'datatable-buttons-container');
    this.tableContainer = document.createElement('div');
    this.tableContainer.setAttribute('class', 'datatable-table-container');
    this.table;
    this.rows = [];
    this.selectedIndex = [];
    this.selectMultiple = false;
    this.dataSource = config.dataSource;
    this.buttons = [];
    this.onSelect = function(selected){
        console.log('Selected:');
        console.log(selected);
    }
    this.onUnselect = function(selected){
        console.log('Unselected:');
        console.log(selected);
    }
    
    
    
    this.loadDataGET = function(address, sendData, async){
        var datatable = this;
        $.ajax({
                async: async,
                url: address,
                type: 'GET',
                data: sendData,
                success: function (data) {
                    var response = JSON.parse(data); 
                    datatable.loadData(response);
                },
                error: function (data) {
                    console.log(data);
                    datatable.loadData([]);
                },
                complete: function () {                 
                }
        });
    }
    
    this.loadDataPOST = function(address, sendData, async){
        var datatable = this;
        $.ajax({
                async: async,
                url: address,
                type: 'POST',
                data: sendData,
                success: function (data) {
                    var response = JSON.parse(data); 
                    datatable.loadData(response);
                },
                error: function (data) {
                    console.log(data);
                    datatable.loadData([]);
                },
                complete: function () {                 
                }
        });
    }
    
    this.loadDataFromDatasource = function(async){
        if(this.dataSource != undefined){
            var address = this.dataSource.address;
            var method = this.dataSource.method;
            var sendData = this.dataSource.data;
            if(method != undefined && address != undefined && sendData != undefined){
                switch(method){
                    case 'get':
                        this.loadDataGET(address, sendData, async);
                        break;
                    case 'post':
                        this.loadDataPOST(address, sendData, async);
                        break;
                    default:
                        this.loadData([]);
                        break;
                }
            }
        }
    }
    
    this.createDataRow = function(index, item){
        var row = document.createElement('tr');
        var datatable = this;
        row.index = index;
        row.onclick = function(){
            datatable.selectRow(row);
        };
        for(const column of this.columns){
            var cell = document.createElement('td');
            if(item[column.variable] !== undefined){
                cell.innerHTML = item[column.variable];
            }
            else{
                cell.innerHTML = '';
            }
            if(typeof column.minWidth !== 'undefined'){
                cell.style.minWidth = column.minWidth + 'px';
            }
            if(typeof column.width !== 'undefined'){
                cell.style.width = column.width + 'px';
            }
            if(typeof column.maxWidth !== 'undefined'){
                cell.style.maxWidth = column.maxWidth + 'px';
            }
            row.appendChild(cell);
        }
        return row;
    }
    
    this.selectRow = function(row){
        if(this.selectMultiple){
            if(row.classList.contains('selected')){
                $(row).removeClass("selected");
                for(var i = 0; i < this.selectedIndex.length; i++){
                    if(this.selectedIndex[i] == row.index){
                        var item = this.getAtIndex(this.selectedIndex[i]);
                        this.onUnselect(item);
                        this.selectedIndex.splice(i, 1);
                    }
                }
            }
            else{
                row.className += ' selected';
                this.selectedIndex.push(row.index);
                this.onSelect(this.getSelected());
            }
        }
        else{
            if(row.classList.contains('selected')){
                $(row).removeClass("selected");
                var item = this.getAtIndex(this.selectedIndex);
                this.onUnselect(item);
                this.selectedIndex = [];
            }
            else{
                $(row).siblings().removeClass("selected");
                row.className += ' selected';
                this.selectedIndex = row.index;
                this.onSelect(this.getSelected());
            }
        }
    }
    
    this.createEmptyDataDisplay = function(){
        var tr = document.createElement('tr');
        var display = document.createElement('td');
        display.setAttribute('class', 'datatable-loading');
        display.textContent = 'No Data Found';
        display.setAttribute('colspan', this.columns.length);
        var tbody = document.createElement('tbody');
        tr.appendChild(display);
        tbody.appendChild(tr);
        return tbody;
    }
    
    this.createFilledDataDisplay = function(data){
        this.rows = [];
        this.data = data;
        var count = 0;
        var tbody = document.createElement('tbody');
        for(const item of this.data){
            var row = this.createDataRow(count, item);
            tbody.appendChild(row);
            this.rows.push(row);
            count++;
        }
        return tbody;
    }
    
    this.loadData = function(data){
        this.table.removeChild(this.table.lastChild);
        var tbody;
        if(data.length === 0){
            tbody = this.createEmptyDataDisplay();
        }
        else{
            tbody = this.createFilledDataDisplay(data);
        }
        this.table.appendChild(tbody);
    }
    
    this.addActionButton = function(text, action){
        var dataTable = this;
        var button = document.createElement('button');
        button.setAttribute('class', 'datatable-button')
        button.textContent = text;
        button.onclick = function(){
            var selected = dataTable.getSelected();
            action(selected, button);
        }
        this.buttons.push(button);
        this.buttonsContainer.appendChild(button);
        return button;
    }
    
    this.createHeader = function(){
        var thead = document.createElement('thead');
        var row = document.createElement('tr');
        row.setAttribute('class', 'datatable-header');
        for(const column of this.columns){
            var cell = document.createElement('th');
            cell.innerHTML = column.title;
            if(typeof column.minWidth !== 'undefined'){
                cell.style.minWidth = column.minWidth + 'px';
            }
            if(typeof column.width !== 'undefined'){
                cell.style.width = column.width + 'px';
            }
            if(typeof column.maxWidth !== 'undefined'){
                cell.style.maxWidth = column.maxWidth + 'px';
            }
            row.appendChild(cell);
        }
        thead.appendChild(row);
        return thead;
    }
    
    this.clearSelection = function(){
        this.selectedIndex = [];
        if(this.table != undefined){
            var list = this.table.children[1].children;
            for(var item of list){
                item.setAttribute('class', '');
            }
        }
    }
    
    this.enableSelectMultiple = function(){
        this.clearSelection();
        this.selectMultiple = true;
        this.selectedIndex = [];
    }
    
    this.disableSelectMultiple = function(){
        this.clearSelection();
        this.selectMultiple = false;
        this.selectedIndex = 0;
    }
    
    this.createLoadingDataDisplay = function(){
        var tr = document.createElement('tr');
        var loadingDiv = document.createElement('div');
        loadingDiv.setAttribute('class', 'datatable-loading-icon');
        var display = document.createElement('td');
        display.appendChild(loadingDiv);
        display.setAttribute('class', 'datatable-loading');
        display.setAttribute('colspan', this.columns.length);
        var tbody = document.createElement('tbody');
        tr.appendChild(display);
        tbody.appendChild(tr);
        return tbody;
    }
    
    this.construct = function(div, waitWithLoading){
        var table = document.createElement('table');
        table.setAttribute('class', 'datatable');
        
        var row = this.createHeader();
        table.appendChild(row);
        if(this.config.selectMultiple === true){
            this.enableSelectMultiple();
        }
        var loadingDisplay = this.createLoadingDataDisplay();
        table.appendChild(loadingDisplay);
        this.tableContainer = document.createElement('div');
        this.tableContainer.setAttribute('class', 'datatable-table-container');
        this.tableContainer.appendChild(table);
        div.appendChild(this.buttonsContainer);
        div.appendChild(this.tableContainer);
        this.table = table;
        if(!waitWithLoading){
            this.loadDataFromDatasource(true);
        }
    }
    this.construct(div, false);
    
    this.getAtIndex = function(index){
        return this.data[index];
    }
    
    this.getSelected = function(){
        if(this.data === undefined || this.data.length === 0 ){
            return undefined;
        }
        if(this.selectMultiple){
            var result = [];
            for(var i = 0; i < this.selectedIndex.length; i++){
                var index = this.selectedIndex[i];
                var item = this.getAtIndex(index);
                result.push(item);
            }
            return result;
        }
        else{
            return this.getAtIndex(this.selectedIndex);
        }
    }
    
    this.clearTable = function(){
        this.selectedIndex = [];
        while (this.div.firstChild) {
            this.div.removeChild(this.div.lastChild);
        }
    }
    
    this.refresh = function(){
        this.clearTable();
        this.construct(this.div, false);
    }
    
    this.refreshWithWait = function(){
        this.clearTable();
        this.construct(this.div, true);
        this.waitMode = true;
    }
    
    this.endWait = function(){
        if(this.waitMode){
            this.loadDataFromDatasource(false);
        }
    }
    
    this.setDatasource = function(dataSource){
        this.dataSource = dataSource;
        this.refresh();
    }
    
    this.setOnSelect = function(onSelect){
        this.onSelect = onSelect;
    }
    
    this.setOnUnselect = function(onUnselect){
        this.onUnselect = onUnselect;
    }
    
    this.selectRowsWhere = function(field, values){
        this.clearSelection();
        for(const row of this.rows){
            var item = this.data[row.index];
            var found = false;
            for(const value of values){
                if(item[field] == value){
                    found = true;
                    break;
                }
            }
            if(found){
                this.selectRow(row);
            }
        }
    }
    
    this.getData = function(){
        var copy = JSON.parse(JSON.stringify(this.data));
        return copy;
    }
}
