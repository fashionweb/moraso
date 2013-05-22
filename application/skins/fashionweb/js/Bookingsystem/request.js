function noReservedDays(date) {
    var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();

    for (i = 0; i < reservedDays.length; i++) {
        if ($.inArray((m + 1) + '-' + d + '-' + y, reservedDays) !== -1) {
            return [false];
        }
    }

    return [true];
}

$("#input_from").datepicker({
    dayNames: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"],
    dayNamesShort: ["Son", "Mon", "Din", "Mit", "Don", "Fra", "Sam"],
    dayNamesMin: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
    monthNames: ["Januar", "Februar", ";ärz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
    monthNamesShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sept", "Okt", "Nov", "Dez"],
    firstDay: 1,
    dateFormat: "dd.mm.yy",
    constrainInput: true,
    beforeShowDay: noReservedDays,
    minDate: 0,
    onSelect: function(selected) {
        $("#input_until").datepicker("option", "minDate", selected);
    }
});

$("#input_until").datepicker({
    dayNames: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"],
    dayNamesShort: ["Son", "Mon", "Din", "Mit", "Don", "Fra", "Sam"],
    dayNamesMin: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
    monthNames: ["Januar", "Februar", ";ärz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
    monthNamesShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sept", "Okt", "Nov", "Dez"],
    firstDay: 1,
    dateFormat: "dd.mm.yy",
    constrainInput: true,
    beforeShowDay: noReservedDays,
    minDate: 1,
    onSelect: function(selected) {
        $("#input_from").datepicker("option", "maxDate", selected);
    }
});



