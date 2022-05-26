/* 
 * This code is free to use, just remember to give credit.
 */

function AdminPanel(language){
    var display = new Display();
    
    $('#users').click(function(){
        display.clear();
        display.addTab(language.users, function(){
            return usersTab(language);
        });
        display.addTab(language.privilages, function(){
            return privilagesTab(language);
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

