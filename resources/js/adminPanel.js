/* 
 * This code is free to use, just remember to give credit.
 */

function AdminPanel(){
    var display = new Display();
    
    $('#users').click(function(){
        display.clear();
        display.addTab('Użytkownicy', function(){
            return usersTab();
        });
        display.addTab('Uprawnienia', function(){
            var input = document.createElement('input');
            input.setAttribute('type', 'date');
            return input;
        });
    });
    $('#users').click();
    
    $('#schedule').click(function(){
        display.show(['ja', 'ty']);
    });
}

function Display(){
    this.div = document.getElementById('display');
    this.contents = document.createElement('div');
    this.div.appendChild(this.contents);
    this.tabs;
    
    this.clear = function(){
        while (this.contents.firstChild){
            this.contents.removeChild(this.contents.firstChild);
        }
        this.tabs = new TabMenu(this.contents);
    }
    
    this.addTab = function (title, create){
        this.tabs.addTab(title, create);
    }
}

function usersTab(){
    var div = document.createElement('div');
    div.style.width = 'fit-content';
    
    var config = {
        columns : [
            { title: 'ID', variable: 'id'},
            { title: 'Active', variable: 'active'},
            { title: 'Username', variable: 'username'},
            { title: 'Name', variable: 'name'},
            { title: 'Surname', variable: 'surname'}
        ],
        dataSource : { method: 'get', address: getRestAddress(), data: { controller: 'AdminPanel', task: 'getAllUsers' } }
    };
    var datatable = new Datatable(div, config);
    datatable.addActionButton('Dodaj', function(){
        console.log('add');
    });
    datatable.addActionButton('Edytuj', function(selected){
        console.log('edit');
    });
    datatable.addActionButton('Zmień hasło', function(selected){
        console.log('edit');
    });
    datatable.addActionButton('Zmień Status', function(selected){
        console.log('edit');
    });
    
    return div;
}

function getRestAddress(){
    var myScript = document.getElementById('RestApi.js');
    var path = myScript.getAttribute('src');
    var index = path.search('resources/js');
    path = path.substring(0, index);
    var address = path + 'sys/scripts/requests/rest.php';
    return address;
}