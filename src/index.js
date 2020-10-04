
import {init} from "pell";

class SkylinePell {
    constructor(pell, output) {
        this.pell = pell;
        this.output = output;
    }

    get content() {
        return this.pell.content.innerHTML;
    }

    set content(content) {
        this.pell.content.innerHTML = content;
        return this;
    }
}

(function($) {
    $.fn.pell = function(settings) {
        this.each(function() {
            let $p= $("<div class='pell'></div>");

            $p.css("width", $(this).width() + "px");
            $p.css("height", $(this).height() + "px");

            if($(this).hasClass('is-valid'))
                $p.addClass("is-valid");
            if($(this).hasClass('is-invalid'))
                $p.addClass("is-invalid");

            $p.insertBefore(this);
            $(this).hide();

            settings = $.extend({}, settings, {
                element: $p[0],
                onChange: html => {
                    $(this).val( html );
                }
            });

            this.pell = new SkylinePell(init(settings), this);
            $p.find(".pell-content").css("height", ($(this).height() - 30) + "px");
        });
        return this;
    };
})(window.jQuery);