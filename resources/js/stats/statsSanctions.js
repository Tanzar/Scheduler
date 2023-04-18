/* 
 * This code is free to use, just remember to give credit.
 */


function init() {
    RestApi.getInterfaceNamesPackage(function(language){
        $('#selectDate').click(function(){
            var month = $('#selectMonth').val();
            var year = $('#selectYear').val();
            loadBySanctionType(language, month, year);
        });
    });
}

function loadBySanctionType(language, month, year){
    var sanction = $('#sanctionType').val();
    switch(sanction){
        case 'tickets':
            loadTickets(language, month, year);
            break;
        case 'articles':
            loadArticles(language, month, year);
            break;
        case 'decisions':
            loadDecisions(language, month, year);
            break;
        case 'suspensions':
            loadSuspensions(language, month, year);
            break;
        case 'usages':
            loadUsages(language, month, year);
            break;
        case 'court':
            loadCourtApplications(language, month, year);
            break;
        default:
            clear();
            break;
    }
}

function loadTickets(language, month, year){
    var container = document.getElementById('datatable');
    while(container.lastChild){
        container.removeChild(container.lastChild);
    }
    
    var div = document.createElement('div');
    container.appendChild(div);
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'StatsSanctions',
            task: 'getTickets',
            month: month,
            year: year
        }
    };
    var config = {
        columns: [
            {title: 'L.p.', variable: 'LP', width: 30, minWidth: 30},
            {title: language.name_person, variable: 'name', width: 100, minWidth: 100},
            {title: language.surname, variable: 'surname', width: 100, minWidth: 100},
            {title: language.document_number, variable: 'document_number', width: 250, minWidth: 250},
            {title: language.ticket_number, variable: 'number', width: 150, minWidth: 150},
            {title: language.ticket_date, variable: 'date', width: 80, minWidth: 80},
            {title: language.position, variable: 'position', width: 150, minWidth: 150},
            {title: language.violated_rules, variable: 'violated_rules', width: 300, minWidth: 300},
            {title: language.external_company, variable: 'external_company_text', width: 100, minWidth: 100},
            {title: language.company_name, variable: 'company_name', width: 100, minWidth: 100},
            {title: language.remarks, variable: 'remarks', width: 300, minWidth: 300}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
}

function loadArticles(language, month, year){
    var container = document.getElementById('datatable');
    while(container.lastChild){
        container.removeChild(container.lastChild);
    }
    
    var div = document.createElement('div');
    container.appendChild(div);
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'StatsSanctions',
            task: 'getArticles',
            month: month,
            year: year
        }
    };
    var config = {
        columns: [
            {title: 'L.p.', variable: 'LP', width: 30, minWidth: 30},
            {title: language.name_person, variable: 'name', width: 100, minWidth: 100},
            {title: language.surname, variable: 'surname', width: 100, minWidth: 100},
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
}

function loadDecisions(language, month, year){
    var container = document.getElementById('datatable');
    while(container.lastChild){
        container.removeChild(container.lastChild);
    }
    
    var div = document.createElement('div');
    container.appendChild(div);
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'StatsSanctions',
            task: 'getDecisions',
            month: month,
            year: year
        }
    };
    var config = {
        columns: [
            {title: 'L.p.', variable: 'LP', width: 30, minWidth: 30},
            {title: language.name_person, variable: 'name', width: 100, minWidth: 100},
            {title: language.surname, variable: 'surname', width: 100, minWidth: 100},
            {title: language.document_number, variable: 'document_number', width: 250, minWidth: 250},
            {title: language.date, variable: 'date', width: 80, minWidth: 80},
            {title: language.decision_law, variable: 'law', width: 150, minWidth: 150},
            {title: language.description, variable: 'description', width: 700, minWidth: 700},
            {title: language.remarks, variable: 'remarks', width: 250, minWidth: 250}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
}

function loadSuspensions(language, month, year){
    var container = document.getElementById('datatable');
    while(container.lastChild){
        container.removeChild(container.lastChild);
    }
    
    var div = document.createElement('div');
    container.appendChild(div);
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'StatsSanctions',
            task: 'getSuspensions',
            month: month,
            year: year
        }
    };
    var config = {
        columns: [
            {title: 'L.p.', variable: 'LP', width: 30, minWidth: 30},
            {title: language.name_person, variable: 'name', width: 100, minWidth: 100},
            {title: language.surname, variable: 'surname', width: 100, minWidth: 100},
            {title: language.document_number, variable: 'document_number', width: 250, minWidth: 250},
            {title: language.date, variable: 'date', width: 80, minWidth: 80},
            {title: language.suspension_group, variable: 'group_name', width: 100, minWidth: 100},
            {title: language.suspension_type, variable: 'type_name', width: 100, minWidth: 100},
            {title: language.suspension_object, variable: 'object_name', width: 100, minWidth: 100},
            {title: language.suspension_reason, variable: 'reason', width: 100, minWidth: 100},
            {title: language.shift, variable: 'shift', width: 50, minWidth: 50},
            {title: language.region, variable: 'region', width: 150, minWidth: 150},
            {title: language.description, variable: 'description', width: 400, minWidth: 400},
            {title: language.correction_date, variable: 'correction_date', width: 80, minWidth: 80},
            {title: language.correction_shift, variable: 'correction_shift', width: 50, minWidth: 50},
            {title: language.remarks, variable: 'remarks', width: 300, minWidth: 300}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
}

function loadUsages(language, month, year){
    var container = document.getElementById('datatable');
    while(container.lastChild){
        container.removeChild(container.lastChild);
    }
    
    var div = document.createElement('div');
    container.appendChild(div);
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'StatsSanctions',
            task: 'getUsages',
            month: month,
            year: year
        }
    };
    var config = {
        columns: [
            {title: 'L.p.', variable: 'LP', width: 30, minWidth: 30},
            {title: language.name_person, variable: 'document_assigned_name', width: 100, minWidth: 100},
            {title: language.surname, variable: 'document_assigned_surname', width: 100, minWidth: 100},
            {title: language.document_number, variable: 'document_number', width: 250, minWidth: 250},
            {title: language.equipment_name, variable: 'equipment_name', width: 300, minWidth: 300},
            {title: language.inventory_number, variable: 'inventory_number', width: 150, minWidth: 150},
            {title: language.date, variable: 'date', width: 80, minWidth: 80},
            {title: language.recommendation_decision, variable: 'recommendation_decision_text', width: 75, minWidth: 75},
            {title: language.remarks, variable: 'remarks', width: 300, minWidth: 300}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
}

function loadCourtApplications(language, month, year){
    var container = document.getElementById('datatable');
    while(container.lastChild){
        container.removeChild(container.lastChild);
    }
    
    var div = document.createElement('div');
    container.appendChild(div);
    var datasource = {
        method: 'get',
        address: getRestAddress(),
        data: {
            controller: 'StatsSanctions',
            task: 'getCourtApplications',
            month: month,
            year: year
        }
    };
    var config = {
        columns: [
            {title: 'L.p.', variable: 'LP', width: 30, minWidth: 30},
            {title: language.name_person, variable: 'name', width: 100, minWidth: 100},
            {title: language.surname, variable: 'surname', width: 100, minWidth: 100},
            {title: language.document_number, variable: 'document_number', width: 250, minWidth: 250},
            {title: language.date, variable: 'date', width: 80, minWidth: 80},
            {title: language.accusation, variable: 'accusation', width: 600, minWidth: 600},
            {title: language.position, variable: 'position', width: 150, minWidth: 150},
            {title: language.position_groups, variable: 'position_group', width: 100, minWidth: 100},
            {title: language.external_company, variable: 'external_company_text', width: 100, minWidth: 100},
            {title: language.company_name, variable: 'company_name', width: 100, minWidth: 100},
            {title: language.remarks, variable: 'remarks', width: 300, minWidth: 300}
        ],
        dataSource: datasource
    }
    
    var datatable = new Datatable(div, config);
}

function clear(){
    var container = document.getElementById('datatable');
    while(container.lastChild){
        container.removeChild(container.lastChild);
    }
}