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
            return privilagesTab();
        });
    });
    $('#users').click();
    
    $('#schedule').click(function(){
        display.show(['ja', 'ty']);
    });
    
    $('#test').click(function(){
        RestApi.post('AdminPanelUsers', 'getPrivilagesList', {}, function(response){
            console.log(response);
        })
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

