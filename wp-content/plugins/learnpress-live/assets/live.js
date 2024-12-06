jQuery(document).ready(function ($) {
    var $select = $("._lp_live_select_id");
    if ($select !== null) {
        $select.select2({
            width: "300px",
            placeholder: "Search Meetings...",
            allowClear: true,
        });
    }
});
