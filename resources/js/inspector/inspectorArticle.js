/* 
 * This code is free to use, just remember to give credit.
 */

function init(){
    RestApi.getInterfaceNamesPackage(function(language){
        var selectDocument = new Select('documents', language.all);
        var datatable = initArticlesTable(language, selectDocument);
        initDateSelection(selectDocument, datatable);
    });
}

function initArticlesTable(language, selectDocument){
    var div = document.getElementById('articles');
    var selectYear = document.getElementById('selectYear');
    
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'InspectorArticle',
            task: 'getArticlesByYear',
            year: selectYear.value
        }
    };
    var config = {
        columns: [
            {title: language.document_number, variable: 'document_number', width: 250, minWidth: 250},
            {title: language.date, variable: 'date', width: 80, minWidth: 80},
            {title: language.art_41_form, variable: 'art_41_form_short', width: 150, minWidth: 150},
            {title: language.position_groups, variable: 'position_group', width: 100, minWidth: 100},
            {title: language.position, variable: 'position', width: 150, minWidth: 150},
            {title: language.external_company, variable: 'external_company_text', width: 100, minWidth: 100},
            {title: language.company_name, variable: 'company_name', width: 100, minWidth: 100},
            {title: language.remarks, variable: 'remarks', width: 300, minWidth: 300}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
    selectDocument.setOnChange(function(id){
        if(id === '0'){
            var selectYear = document.getElementById('selectYear');
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'InspectorArticle',
                    task: 'getArticlesByYear',
                    year: selectYear.value
                }
            });
        }
        else{
            datatable.setDatasource({
                method: 'get',
                address: getRestAddress(),
                data: {
                    controller: 'InspectorArticle',
                    task: 'getArticles',
                    id: id
                }
            });
        }
    });
    datatable.addActionButton(language.add, function(){
        var documentId = selectDocument.value();
        if(documentId !== '0'){
            RestApi.get('InspectorArticle', 'getNewArticleDetails', {id: documentId}, 
                function(response){
                    var details = JSON.parse(response);
                    var forms = [];
                    details.forms.forEach(item => {
                        var option = {
                            value: item.id,
                            title: item.short
                        }
                        forms.push(option);
                    });
                    var positions = [];
                    details.position_groups.forEach(item => {
                        var option = {
                            value: item.id,
                            title: item.name
                        }
                        positions.push(option);
                    });
                    var date = new Date();
                    var start = new Date(details.start);
                    if(date < start){
                        date = start;
                    }
                    var fields = [
                        {type: 'select', title: language.select_art_41_form, variable: 'id_art_41_form', options: forms, required: true},
                        {type: 'date', title: language.date, variable: 'date', min: details.start, value: date.toISOString().split('T')[0]},
                        {type: 'select', title: language.select_position_group, variable: 'id_position_groups', options: positions, required: true},
                        {type: 'text', title: language.position, variable: 'position', limit: 255, required: true},
                        {type: 'checkbox', title: language.external_company, variable: 'external_company'},
                        {type: 'text', title: language.company_name, variable: 'company_name', limit: 255},
                        {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 5}
                    ];
                    var item = {
                        id_document: documentId,
                        applicant: '',
                        application_number: '',
                        application_date: date.toISOString().split('T')[0]
                    }
                    openModalBox(language.new_art_41, fields, language.save, function(data){
                        var require_application = false;
                        var idArtForm = parseInt(data.id_art_41_form);
                        details.forms.forEach(item => {
                            if(item.id === idArtForm && item.require_application_info){
                                require_application = true;
                            }
                        });
                        if(require_application){
                            var fields = [
                                {type: 'text', title: language.applicant, variable: 'applicant', limit: 255, required: true},
                                {type: 'text', title: language.application_number, variable: 'application_number', limit: 255, required: true},
                                {type: 'date', title: language.application_date, variable: 'application_date', min: details.start, max: details.end, value: date.toDateString()}
                            ];
                            openModalBox(language.new_art_41, fields, language.save, function(data){
                                console.log(data);
                                RestApi.post('InspectorArticle', 'saveNewArticle', data,
                                    function(response){
                                        var data = JSON.parse(response);
                                        console.log(data);
                                        alert(data.message);
                                        datatable.refresh();
                                    },
                                    function(response){
                                        console.log(response.responseText);
                                        alert(response.responseText);
                                });
                            }, data);
                        }
                        else{
                            RestApi.post('InspectorArticle', 'saveNewArticle', data,
                                function(response){
                                    var data = JSON.parse(response);
                                    console.log(data);
                                    alert(data.message);
                                    datatable.refresh();
                                },
                                function(response){
                                    console.log(response.responseText);
                                    alert(response.responseText);
                            });
                        }
                    }, item);
            });
        }
        else{
            alert(language.select_document);
        }
    });
    
    datatable.addActionButton(language.edit, function(selected){
        if(selected !== undefined){
            var documentId = selected.id_document;
            RestApi.get('InspectorArticle', 'getNewArticleDetails', {id: documentId}, 
                function(response){
                    var details = JSON.parse(response);
                    var forms = [];
                    details.forms.forEach(item => {
                        var option = {
                            value: item.id,
                            title: item.short
                        }
                        forms.push(option);
                    });
                    var positions = [];
                    details.position_groups.forEach(item => {
                        var option = {
                            value: item.id,
                            title: item.name
                        }
                        positions.push(option);
                    });
                    var date = new Date();
                    var start = new Date(details.start);
                    if(date < start){
                        date = start;
                    }
                    var fields = [
                        {type: 'select', title: language.select_art_41_form, variable: 'id_art_41_form', options: forms, required: true},
                        {type: 'date', title: language.date, variable: 'date', min: details.start, value: date.toISOString().split('T')[0]},
                        {type: 'select', title: language.select_position_group, variable: 'id_position_groups', options: positions, required: true},
                        {type: 'text', title: language.position, variable: 'position', limit: 255, required: true},
                        {type: 'checkbox', title: language.external_company, variable: 'external_company'},
                        {type: 'text', title: language.company_name, variable: 'company_name', limit: 255},
                        {type: 'textarea', title: language.remarks, variable: 'remarks', limit: 255, width: 30, height: 5}
                    ];
                    openModalBox(language.new_art_41, fields, language.save, 
                        function(data){
                            var require_application = false;
                            var idArtForm = parseInt(data.id_art_41_form);
                            details.forms.forEach(item => {
                                if(item.id === idArtForm && item.require_application_info){
                                    require_application = true;
                                }
                            });
                            if(require_application){
                                var fields = [
                                    {type: 'text', title: language.applicant, variable: 'applicant', limit: 255, required: true},
                                    {type: 'text', title: language.application_number, variable: 'application_number', limit: 255, required: true},
                                    {type: 'date', title: language.application_date, variable: 'application_date', min: details.start, max: details.end, value: date.toDateString()}
                                ];
                                openModalBox(language.new_art_41, fields, language.save, function(data){
                                    console.log(data);
                                    RestApi.post('InspectorArticle', 'updateArticle', data,
                                        function(response){
                                            var data = JSON.parse(response);
                                            console.log(data);
                                            alert(data.message);
                                            datatable.refresh();
                                        },
                                        function(response){
                                            console.log(response.responseText);
                                            alert(response.responseText);
                                    });
                                }, data);
                            }
                            else{
                                RestApi.post('InspectorArticle', 'updateArticle', data,
                                    function(response){
                                        var data = JSON.parse(response);
                                        console.log(data);
                                        alert(data.message);
                                        datatable.refresh();
                                    },
                                    function(response){
                                        console.log(response.responseText);
                                        alert(response.responseText);
                                });
                            }
                    }, selected);
            });
        }
        else{
            alert(language.select_art_41);
        }
    });
    datatable.addActionButton(language.remove, function(selected){
        if(selected !== undefined){
            RestApi.post('InspectorArticle', 'removeArticle', {id: selected.id},
                function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                    alert(data.message);
                    datatable.refresh();
                },
                function(response){
                    console.log(response.responseText);
                    alert(response.responseText);
            });
        }
        else{
            alert(language.select_art_41);
        }
    });
    return datatable;
}

function Select(id, placeholder){
    var select = document.getElementById(id);
    var hiddenValues = {};
    var dataToSend = {};
    
    this.clear = function(){
        while(select.firstChild){
            select.removeChild(select.firstChild);
        }
        
        var option = document.createElement('option');
        option.selected = true;
        option.value = '0';
        option.textContent = placeholder;
        select.appendChild(option);
        hiddenValues = {};
    }
    
    this.setOnChange = function(action){
        select.onchange = function(){
            var hidden = hiddenValues[select.value];
            action(select.value, hidden);
        }
    }
    
    this.addOption = function(value, text, hidden){
        var option = document.createElement('option');
        option.value = value;
        option.textContent = text;
        hiddenValues[value] = hidden;
        select.appendChild(option);
    }
    
    var me = this;
    this.loadOptions = function(controller, task, inputData){
        RestApi.get(controller, task, inputData, function(response){
            me.clear();
            dataToSend = inputData;
            var data = JSON.parse(response);
            data.forEach(item => {
                me.addOption(item.id, item.number);
            });
        });
    }
    
    this.value = function(){
        return select.value;
    }
}

function initDateSelection(selectDocument, datatable) {
    var selectYear = document.getElementById('selectYear');
    var selectDate = document.getElementById('selectDate');
    selectDate.onclick = function(){
        var data = {
            year: selectYear.value
        }
        selectDocument.loadOptions('InspectorArticle', 'getDocuments', data);
        datatable.setDatasource({
            method: 'get',
            address: getRestAddress(),
            data: {
                controller: 'InspectorArticle',
                task: 'getArticlesByYear',
                year: selectYear.value
            }
        });
    }
}