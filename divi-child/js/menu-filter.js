
  (function ($) {
    function setup_collapsible_submenus() {
      var parent = $(".et_mobile_menu .toggle-parent > a");
    //   var fparents = $(".ls_mobile_filter .toggle-parent > label");
      $(".et_mobile_menu .toggle-parent li").addClass("toggle-child");

      parent.off("click").click(function (e) {
        //$(this).attr("href", "#");
        e.preventDefault();
        $(this).parent().children().children().toggleClass("reveal-items");
        $(this).toggleClass("icon-switch");
      });
    }

    function setup_collapsible_filters() {
        var parent = $(".ls_mobile_filter .toggle-parent > label");
      //   var fparents = $(".ls_mobile_filter .toggle-parent > label");
        $(".ls_mobile_filter .toggle-parent > ul, .ls_mobile_filter .toggle-parent > div").addClass("toggle-fchild");
  
        parent.off("click").click(function (e) {
          //$(this).attr("href", "#");
          console.log("clicked");
          e.preventDefault();
          $(this).parent().find("> .toggle-fchild").toggleClass("reveal-fitems");
          $(this).toggleClass("icon-switch");
        });
      }

    

    $(window).load(function () {
      setTimeout(function () {
        setup_collapsible_submenus();
        setup_collapsible_filters();
      }, 700);
    });
  })(jQuery);

