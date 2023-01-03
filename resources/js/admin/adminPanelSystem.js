/* 
 * This code is free to use, just remember to give credit.
 */



function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        $('#fix').click(function(){
            var fields = [
                {type: 'checkbox', title: language.confirm, variable: 'confirm', required: true}
            ];
            openModalBox(language.confirm, fields, language.confirm, function(data){
                if(data.confirm){
                    RestApi.post('AdminPanelSystem', 'runFix', data,
                        function(response){
                            var data = JSON.parse(response);
                            console.log(data);
                            alert(data.message);
                        },
                        function(response){
                            console.log(response.responseText);
                            alert(response.responseText);
                    });
                }
            });
        });
        
    });
}
