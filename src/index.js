
import {exec, init} from "pell";

let MODAL = false;

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

    exec(command, value) {
        exec(command, value);
    }

    insertHTML(html) {
        console.log(html);
        exec('insertHTML', html);
    }

    startModal(id, initialHandler, finalizeHandler) {
        if(MODAL === false) {
            MODAL = {
                pell: this,
                selection: (()=>{if (window.getSelection) {
                    var sel = window.getSelection();
                    if (sel.getRangeAt && sel.rangeCount) {
                        return sel.getRangeAt(0);
                    }
                } else if (document.selection && document.selection.createRange) {
                    return document.selection.createRange();
                }
                    return null;})(),
                final: finalizeHandler,
                modal: $("#"+id).one("hidden.bs.modal", () => {
                    SkylinePell.stopModal(0, undefined);
                }).modal("show")
            }
            initialHandler.call(this, MODAL.selection);
        } else
            console.warn("Modal is already running. Can only run one modal session.");
    }

    static stopModal(code, ...response) {
        if(typeof MODAL === 'object') {
            let {pell,selection,final} = MODAL;
            MODAL.modal.modal("hide").off("hidden.bs.modal");
            ((range)=>{if (range) {
                $(pell.pell).focus();
                if (window.getSelection) {
                    var sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(range);
                } else if (document.selection && range.select) {
                    range.select();
                }
            }})( selection );
            final.call(this, code, ...response);
            MODAL = false;
        } else
            console.warn("No modal session is running.");
        // Prevent form submit
        return false;
    }
}

(function($) {
    $.fn.pell = function(settings) {
        this.each(function() {
            let $p= $("<div class='pell'></div>");

            $p.css("height", $(this).height() + "px");



            if($(this).hasClass('is-valid'))
                $p.addClass("is-valid");
            if($(this).hasClass('is-invalid'))
                $p.addClass("is-invalid");

            if(settings.classes) {
                settings.classes.map(function(v) {
                    $p.addClass(v);
                })
            }

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

window.SkylinePell = SkylinePell;