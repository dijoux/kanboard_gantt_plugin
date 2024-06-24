KB.on('dom.ready', function () {
    function goToLink (selector, modal) {
        if (! KB.modal.isOpen()) {
            var element = KB.find(selector);

            if (element !== null && modal == false) {
                window.location = element.attr('href');
            } else if (element !== null && modal == true) {
                KB.modal.open(element.attr('href'), 'large', false);
            }
        }
    }
    
    KB.onKey('v+g', function () {
        goToLink('a.view-gantt', false);
    });
    KB.onKey('g+g', function () {
        goToLink('a.view-all-gantt', false);
    });
    KB.onKey('n', function () {
        goToLink('a.js-modal-large', true);
    });
    
    if (KB.exists('#gantt-chart')) {
        var default_view_mode = "Month";
        
        if ($ls = localStorage.getItem("gantt_view_mode")) {
            default_view_mode = $ls;
            jQuery("ul.gantt li").removeClass("active");
            jQuery('.gantt-change-mode[data-mode-view="' + default_view_mode + '"]').addClass("active");
        }
        
        var gantt = new Gantt('#gantt-chart', $('#gantt-chart').data('records'), {
            bar_height: 18,
            padding: 7,
            view_modes: ["Quarter Day", "Half Day", "Day", "Week", "Month", "Year"],
            view_mode: default_view_mode,
            date_format: "DD/MM/YYYY",
            language: jQuery("html").attr("lang"),
            custom_popup_html: function(task) {
                return task.popup;
            },
        });
        
        KB.onClick(".gantt-change-mode", function (element) {
            var mode = jQuery(element.srcElement).data("modeView");
            gantt.change_view_mode(mode);
            localStorage.setItem("gantt_view_mode", mode);
            jQuery(".gantt-change-mode").removeClass("active");
            KB.dom(element.srcElement).addClass("active");
        });
    }
});
