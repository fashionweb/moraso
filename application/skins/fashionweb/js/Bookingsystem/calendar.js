$(document).ready(function() {
    $('#bookingsystem_calender').fullCalendar({
        firstDay: 1,
        disableDragging: true,
        dayNames: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],
        dayNamesShort: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
        monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
        monthNamesShort: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
        titleFormat: {
            month: 'MMMM yyyy',
            week: "d. MMM [ yyyy]{ '&#8212;' d. [ MMM] yyyy}",
            day: 'dddd, d. MMMM yyyy'
        },
        columnFormat: {
            month: 'ddd',
            week: 'dddd, d. MMM',
            day: 'dddd, d. MMM'
        },
        eventSources: [
            {
                url: '?renderOnly=Bookingsystem.Calendar.Json',
                type: 'POST',
                data: {
                    booking_status: '1'
                },
                color: 'orange',
                textColor: 'black'
            }, {
                url: '?renderOnly=Bookingsystem.Calendar.Json',
                type: 'POST',
                data: {
                    booking_status: '2'
                },
                color: 'red',
                textColor: 'black'
            }
        ]
    });
});