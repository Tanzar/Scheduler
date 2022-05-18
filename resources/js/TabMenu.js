/* 
 * This code is free to use, just remember to give credit.
 */


function TabMenu(div){
    this.div = div;
    this.div.setAttribute('class', 'tab-menu');
    this.buttonsDiv = document.createElement('div');
    this.buttonsDiv.setAttribute('class', 'tab-menu-buttons');
    this.tabContents = document.createElement('div');
    this.tabContents.setAttribute('class', 'tab-menu-tab');
    this.tabContents.textContent = 'Select tab ^'
    
    this.div.appendChild(this.buttonsDiv);
    this.div.appendChild(this.tabContents);
    
    this.addTab = function(title, createContents){
        var tab = this.tabContents;
        var button = document.createElement('button');
        button.textContent = title;
        button.setAttribute('class', 'tab-menu-button')
        button.onclick = function(){
            var contents = createContents();
            while(tab.firstChild){
                tab.removeChild(tab.firstChild);
            }
            tab.appendChild(contents);
        }
        this.buttonsDiv.appendChild(button);
        
    }
    
}