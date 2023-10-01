function simplest_analytics_toggle_daterange() {
    var bg = document.getElementById("sa_bg");
    var popup = document.getElementById("daterange_popup");
    if ( popup.className.indexOf("daterange_popup_active") > -1 ) {
        popup.classList.remove("daterange_popup_active");
        bg.style.display = "none";
    }
    else {
        popup.classList.add("daterange_popup_active");
        bg.style.display = "";
    }
}
function simplest_analytics_close_all_popups() {
    document.getElementById("sa_bg").style.display = "none";

    var all_ele = document.getElementsByClassName("closepopup");
    for ( var i=0; i<all_ele.length; i++ ) {
        all_ele[i].classList.remove("daterange_popup_active");
    }
}
function simplest_analytics_show_ele(ele_id) {
	document.getElementById(ele_id).style.display = "";
}
function simplest_analytics_hide_ele(ele_id) {
	document.getElementById(ele_id).style.display = "none";
}
function simplest_analytics_toggle_tabs_by_id(thiss) {
    var ele_id = thiss.id;
    var tab_id = "tab_" + ele_id;
    var all_tabs = document.getElementsByClassName("all_tabs");
    for ( var i=0; i<all_tabs.length; i++ ) {
        all_tabs[i].style.display = "none";
    }
    var all_tabs = document.getElementsByClassName("nav-tab");
    for ( var i=0; i<all_tabs.length; i++ ) {
        all_tabs[i].classList.remove("nav-tab-active");
    }
    
    document.getElementById(tab_id).style.display = "";
    thiss.classList.add("nav-tab-active");
}