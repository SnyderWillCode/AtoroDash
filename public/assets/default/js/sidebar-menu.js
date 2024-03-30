$(function(){"use strict"
$.sidebarMenu=function(e){var i=300,s=".sidebar-submenu"
$(e).on("click","li a",function(e){var n=$(this),a=n.next()
if(a.is(s)&&a.is(":visible"))a.slideUp(i,function(){a.removeClass("menu-open")}),a.parent("li").removeClass("active")
else if(a.is(s)&&!a.is(":visible")){var l=n.parents("ul").first(),t=l.find("ul:visible").slideUp(i)
t.removeClass("menu-open")
var r=n.parent("li")
a.slideDown(i,function(){a.addClass("menu-open"),l.find("li.active").removeClass("active"),r.addClass("active")})}a.is(s)&&e.preventDefault()})}})
