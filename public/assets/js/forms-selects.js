"use strict";
$(function () {
    const e = $(".selectpicker"),
        t = $(".select2"),
        n = $(".select2-icons");
    if (
        (e.length && e.selectpicker(),
        t.length &&
            t.each(function () {
                var e = $(this);
                e.wrap('<div class="position-relative"></div>').select2({
                    placeholder: "Select value",
                    dropdownParent: e.parent(),
                });
            }),
        n.length)
    ) {
        function i(e) {
            return e.id
                ? "<i class='" +
                      $(e.element).data("icon") +
                      " me-2'></i>" +
                      e.text
                : e.text;
        }
        n.wrap('<div class="position-relative"></div>').select2({
            dropdownParent: n.parent(),
            templateResult: i,
            templateSelection: i,
            escapeMarkup: function (e) {
                return e;
            },
        });
    }
});
