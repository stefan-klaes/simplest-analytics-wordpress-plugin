function simplest_analytics_add_more(type) {
  if (type == "url") {
    var tab = document.getElementById("tab_urlparas");
  } else {
    var tab = document.getElementById("tab_events");
  }
  var table = tab.getElementsByTagName("table")[0];
  // add tr at 2nd last position
  var tr = table.insertRow(table.rows.length - 1);
  // add th, td, td to tr
  var th = document.createElement("th");
  var td1 = document.createElement("td");
  var td2 = document.createElement("td");
  tr.appendChild(th);
  tr.appendChild(td1);
  tr.appendChild(td2);
  // add text to th: Parameter {no} (length tr -2)
  th.innerHTML = "Parameter " + (tr.rowIndex - 1);
  // add input to td1: <input type="text" name="parameter[]" value="" />
  var input1 = document.createElement("input");
  input1.type = "text";
  if (type == "url") {
    input1.name = "parameter[]";
  } else {
    input1.name = "event_name[]";
  }
  input1.value = "";
  // add input to td2: <input type="text" name="label[]" value="" />
  var input2 = document.createElement("input");
  input2.type = "text";
  if (type == "url") {
    input2.name = "label[]";
  } else {
    input2.name = "event_trigger[]";
  }
  input2.value = "";
  td1.appendChild(input1);
  td2.appendChild(input2);
}
function simplest_analytics_toggle_daterange() {
  var bg = document.getElementById("sa_bg");
  var popup = document.getElementById("daterange_popup");
  if (popup.className.indexOf("daterange_popup_active") > -1) {
    popup.classList.remove("daterange_popup_active");
    bg.style.display = "none";
  } else {
    popup.classList.add("daterange_popup_active");
    bg.style.display = "";
  }
}
function simplest_analytics_close_all_popups() {
  document.getElementById("sa_bg").style.display = "none";

  var all_ele = document.getElementsByClassName("closepopup");
  for (var i = 0; i < all_ele.length; i++) {
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
  for (var i = 0; i < all_tabs.length; i++) {
    all_tabs[i].style.display = "none";
  }
  var all_tabs = document.getElementsByClassName("nav-tab");
  for (var i = 0; i < all_tabs.length; i++) {
    all_tabs[i].classList.remove("nav-tab-active");
  }

  document.getElementById(tab_id).style.display = "";
  thiss.classList.add("nav-tab-active");
}
