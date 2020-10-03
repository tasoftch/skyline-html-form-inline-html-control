
import {init} from "pell";

(function($) {
    $.fn.editor = function(settings) {
        this.each(function() {
            let $p= $("<div class='pell'></div>");

            $p.css("width", $(this).width() + "px");
            $p.css("height", $(this).height() + "px");

            $p.insertBefore(this);
            $(this).hide();

            settings = $.extend({}, settings, {
                element: $p[0],
                onChange: html => {
                    $(this).val( html );
                }
            });

            this.editor = init(settings);
        });
        return this;
    };
})(window.jQuery);