$.fn.dataTableExt.oApi.fnGetFilteredNodes = function ( oSettings ){
    var anRows = [];
    for (var i=0, iLen=oSettings.aiDisplay.length; i<iLen; i++) {
        var nRow = oSettings.aoData[oSettings.aiDisplay[i]].nTr;
        anRows.push(nRow);
    }
    return anRows;
};
  
$(document).ready(function(){
    var membersList = $('#membersList').dataTable( {
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "oLanguage": {
            "sLengthMenu": "Zeige _MENU_ Eintr&auml;ge pro Seite",
            "sZeroRecords": "Nichts gefunden - sorry",
            "sInfo": "_START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
            "sInfoEmpty": "Keine Eintr&auml;ge gefunden",
            "sInfoFiltered": "(_MAX_ Eintr&auml;ge durchsucht)",
            "sSearch": "Suche: ",
            "oPaginate": {
                "sFirst": "Erste",
                "sLast": "Letzte",
                "sNext": "n&auml;chste",
                "sPrevious": "vorherige"
            }
        }
    } );

    $('#exportMembersListAsCsv').click(function() {
   
        var membersExportList = new Array();
        var id = null;
        var rowClass = null;
       
        var rows = membersList.fnGetFilteredNodes();
   
        $(rows).each(function(index, row) {
            rowClass = $(row).attr('class');    
            id = rowClass.replace(/\D+/g,"");       
            membersExportList.push(id);
        });
    
        jQuery('<form method="post" action="?renderOnly=Members.Export"><input type="hidden" name="members" value="' + membersExportList.join(',') + '" /></form>').appendTo('body').submit().remove();
        
        return false;
    });
});