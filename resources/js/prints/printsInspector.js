/* 
 * This code is free to use, just remember to give credit.
 */


function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        
        $('#instrumentUsageCard').click(function(){
            FileApi.post('PrintsInspector', 'generateInstrumentUsageCard', {
                id: document.getElementById('selectInstrument').value,
                year: document.getElementById('selectInstrumentYear').value
            });
        });
        
    });
}