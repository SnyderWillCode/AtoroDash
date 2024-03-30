$(function () {
  "use strict";
  function t() {
    $("body").attr("class", "bg-theme bg-theme1");
  }
  function e() {
    $("body").attr("class", "bg-theme bg-theme2");
  }
  function c() {
    $("body").attr("class", "bg-theme bg-theme3");
  }
  function o() {
    $("body").attr("class", "bg-theme bg-theme4");
  }
  function n() {
    $("body").attr("class", "bg-theme bg-theme5");
  }
  function a() {
    $("body").attr("class", "bg-theme bg-theme6");
  }
  function i() {
    $("body").attr("class", "bg-theme bg-theme7");
  }
  function l() {
    $("body").attr("class", "bg-theme bg-theme8");
  }
  function s() {
    $("body").attr("class", "bg-theme bg-theme9");
  }
  function b() {
    $("body").attr("class", "bg-theme bg-theme10");
  }
  function r() {
    $("body").attr("class", "bg-theme bg-theme11");
  }
  function h() {
    $("body").attr("class", "bg-theme bg-theme12");
  }
  function m() {
    $("body").attr("class", "bg-theme bg-theme13");
  }
  function g() {
    $("body").attr("class", "bg-theme bg-theme14");
  }
  function d() {
    $("body").attr("class", "bg-theme bg-theme15");
  }
  $.sidebarMenu($(".sidebar-menu")),
    $(".toggle-menu").on("click", function (t) {
      t.preventDefault(), $("#wrapper").toggleClass("toggled");
    }),
    $(function () {
      for (
        var t = window.location,
          e = $(".sidebar-menu a")
            .filter(function () {
              return this.href == t;
            })
            .addClass("active")
            .parent()
            .addClass("active");
        e.is("li");

      )
        e = e.parent().addClass("in").parent().addClass("active");
    }),
    $(document).ready(function () {
      $(window).on("scroll", function () {
        $(this).scrollTop() > 60
          ? $(".topbar-nav .navbar").addClass("bg-dark")
          : $(".topbar-nav .navbar").removeClass("bg-dark");
      });
    }),
    $(document).ready(function () {
      $(window).on("scroll", function () {
        $(this).scrollTop() > 300
          ? $(".back-to-top").fadeIn()
          : $(".back-to-top").fadeOut();
      }),
        $(".back-to-top").on("click", function () {
          return $("html, body").animate({ scrollTop: 0 }, 600), !1;
        });
    }),
    $(function () {
      $('[data-toggle="popover"]').popover();
    }),
    $(function () {
      $('[data-toggle="tooltip"]').tooltip();
    }),
    $(".switcher-icon").on("click", function (t) {
      t.preventDefault(), $(".right-sidebar").toggleClass("right-toggled");
    }),
    $("#theme1").click(t),
    $("#theme2").click(e),
    $("#theme3").click(c),
    $("#theme4").click(o),
    $("#theme5").click(n),
    $("#theme6").click(a),
    $("#theme7").click(i),
    $("#theme8").click(l),
    $("#theme9").click(s),
    $("#theme10").click(b),
    $("#theme11").click(r),
    $("#theme12").click(h),
    $("#theme13").click(m),
    $("#theme14").click(g),
    $("#theme15").click(d);
});
var options = {
  classname: 'loadingbar',
    id: 'loadingbar'
};
var nanobar = new Nanobar( options );
nanobar.go( 0 );
nanobar.go( 25 );
nanobar.go( 50 );
nanobar.go( 75 );
nanobar.go( 100 );

function copyToClipboard(elementId) {
  const inputElement = document.getElementById(elementId);
  inputElement.select();
  inputElement.setSelectionRange(0, 99999);

  try {
    document.execCommand("copy");
    console.log("Text copied to clipboard");
  } catch (error) {
    console.error("Failed to copy text: ", error);
  }
}

const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');
  const toggleIcon = document.getElementById('toggleIcon');

  togglePassword.addEventListener('click', function () {
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      toggleIcon.classList.remove('fa-eye');
      toggleIcon.classList.add('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      toggleIcon.classList.remove('fa-eye-slash');
      toggleIcon.classList.add('fa-eye');
    }
  });
