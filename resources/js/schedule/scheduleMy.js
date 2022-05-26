/* 
 * This code is free to use, just remember to give credit.
 */

function scheduleMy(){
    var pageContents = document.getElementById('pageContents');
        while(pageContents.firstChild){
            pageContents.removeChild(pageContents.firstChild);
        }
        
        var div = document.createElement('div');
        var input = document.createElement('input');
        input.setAttribute('type', 'date');
        div.appendChild(input);
        var button = document.createElement('button');
        button.textContent = 'Wybierz';
        div.appendChild(button);
        pageContents.appendChild(div);
}
