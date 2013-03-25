var loggedIn;
var dataTableObj;
$(document).ready(function(){

    //add to all ajax requests csrf tokens
    $.ajaxSetup({
        data: {wm4d_token: getCookie('wm4d_cookie')}
    });
    loggedIn = getCookie('AW_SESSION_ACTIVE');

    //this code running only if active page is main
    if(typeof($('#accounts-table').html()) != 'undefined'){

        $('#accounts-table').dataTable({
            sPaginationType: "full_numbers",
            iDisplayLength : 20,
            bFilter: false,
            bInfo: false,
            bLengthChange : false,
            aoColumns: [
                null, null, null, {bSortable: false}
            ]

        });
        if(loggedIn == 'true'){
            var customerID = $('.icon-eye-open :first').attr('data-customer-id');
            getCampaigns(customerID, function(data){
                renderCompanies(data);
            });
        }
    }

    if(typeof($('#date-input').html()) != 'undefined'){
        $('#date-input').datepicker();
    }

    //load campaigns for selected adwords account
    $('#accounts-table').on('click', '.icon-eye-open', function(){
        var customerID = $(this).attr('data-customer-id');
        getCampaigns(customerID, function(data){
            renderCompanies(data);
        });
    });

    //get page rank
    $('#check-page-rank-btn').click(function(){
        var url = $('#page-url').val();
        var keywords = $('#page-keywords').val();
//        if(url == ''){
//            $.jGrowl('Page Url can\'t be empty.', {theme: 'red', header: 'Error'});
//            return false;
//        }
        getPageRank(url, keywords, function(data){
            renderPageRank(data.page);
            renderKeywordsResult(data.keywords_result);
        })
    });

    //this code running only if active page is billing
    if(typeof($('#customer-alerts-table').html()) != 'undefined'){
        $('#customer-alerts-table').dataTable({
            sPaginationType: "full_numbers",
            iDisplayLength : 20,
            bFilter: false,
            bInfo: false,
            bLengthChange : false,
            aoColumns: [
                null, {bSortable: false}, {bSortable: false}, {bSortable: false}
            ]
        });
    }
});

function getAccounts(callback){
    $('#progress-modal').modal('show');
    $.ajax({
        url: '/ajax/get_accounts/',
        type: 'GET',
        success: function(response, textStatus, jqXHR){
            var answer = $.parseJSON(response);
            if(answer.status == 200){
                $('#progress-modal').modal('hide');
                if(callback && typeof(callback) === "function"){
                    callback(answer.data);
                }
            }
            else{
                $('#progress-modal').modal('hide');
                $.jGrowl(answer.reason, {theme: 'red', header: 'Error'});
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            $.jGrowl(textStatus, {theme: 'red', header: 'Error'});
        }
    });
}

function getCampaigns(customerID, callback){
    $('#progress-modal').modal('show');
    $.ajax({
        url: '/ajax/get_account_campaigns/' + customerID,
        type: 'GET',
        success: function(response, textStatus, jqXHR){
            var answer = $.parseJSON(response);
            if(answer.status == 200){
                $('#progress-modal').modal('hide');
                if(callback && typeof(callback) === "function"){
                    callback(answer.data);
                }
            }
            else{
                $('#progress-modal').modal('hide');
                $.jGrowl(answer.reason, {theme: 'red', header: 'Error'});
                $('#account-campaigns-table tbody').html('');
                $('#account-campaigns-table').hide();
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            $.jGrowl(textStatus, {theme: 'red', header: 'Error'});
        }
    });
}

function renderCompanies(data){
    if(typeof(dataTableObj) != 'undefined'){
        dataTableObj.fnDestroy();
    }

    var size = data.length;
    var tpl = '';
    for(var i = 0; i < size; i ++){
        //1 million of units == 1 dollar
        var microAmount = data[i].campaignStats.cost.microAmount / 1000000;
        tpl += '<tr>';
        tpl += '<td>' + data[i].name + '</td>';
        tpl += '<td>';
        tpl += '<p>ID: <code>' + data[i].id + '</code></p>';
        tpl += '<p>Status: ' + data[i].status + '</p>';
        tpl += '<p>Clicks: ' + data[i].campaignStats.clicks + '</p>';
        tpl += '<p>CTR: ' + data[i].campaignStats.ctr + '</p>';
        tpl += '<p>Cost: $' + microAmount + '</p>';
        tpl += '</td>';
        tpl += '</tr>';
    }
    $('#account-campaigns-table').show();
    $('#account-campaigns-table tbody').html('');
    $('#account-campaigns-table tbody').html(tpl);
    dataTableObj = $('#account-campaigns-table').dataTable({
        sPaginationType: "full_numbers",
        iDisplayLength : 5,
        bFilter: false,
        bInfo: false,
        bLengthChange : false,
        bAutoWidth: true,
        bRetrieve: true,
        bDestroy: true,
        aoColumns: [
            null, {bSortable: false}
        ]
    });
    dataTableObj.fnDraw();
}

function getPageRank(url, keywords, callback){
    $('#progress-modal').modal('show');
    $.ajax({
        url: '/ajax/get_page_rank/',
        type: 'GET',
        data: {page_url: url, keywords: keywords},
        success: function(response, textStatus, jqXHR){
            var answer = $.parseJSON(response);
            if(answer.status == 200){
                $('#progress-modal').modal('hide');
                if(callback && typeof(callback) === "function"){
                    callback(answer.data);
                }
            }
            else{
                $('#progress-modal').modal('hide');
                $('#page-rank').text('-');
                $('#page-speed').text('-/100');
                $('#keywords-result-container').hide();
                $.jGrowl(answer.reason, {theme: 'red', header: 'Error'});
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            $.jGrowl(textStatus, {theme: 'red', header: 'Error'});
        }
    });
}

function renderPageRank(data){
    if(typeof(data.page_rank) != 'undefined'){
        $('#page-rank').text(data.page_rank);
        $('#page-speed').text(data.page_speed + '/100');
    }
    else{
        $('#page-rank').text('-');
        $('#page-speed').text('-/100');
    }
}

function renderKeywordsResult(data){
    if(typeof(dataTableObj) != 'undefined'){
        dataTableObj.fnDestroy();
    }
    var tpl = '';
    var size = data.length;
    for(var i = 0; i < size; i ++){
        tpl += '<tr>';
        tpl += '<td>' + data[i].headline + '</td>';
        tpl += '<td>' + data[i].url + '</td>';
        tpl += '<td>' + data[i].page_rank + '</td>';
//        tpl += '<td>' + data[i].page_speed + '/100</td>';
        tpl += '</tr>'
    }

    $('#keywords-result-container').show();
    $('#keywords-result-table tbody').html('');
    $('#keywords-result-table tbody').html(tpl);
    dataTableObj = $('#keywords-result-table').dataTable({
        sPaginationType: "full_numbers",
        iDisplayLength : 10,
        bFilter: false,
        bInfo: false,
        bLengthChange : false,
        bAutoWidth: true,
        bRetrieve: true,
        bDestroy: true,
        bSortable: false
    });
    dataTableObj.fnDraw();
}