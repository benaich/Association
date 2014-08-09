// Some general UI pack related JS
// Extend JS String with repeat method
String.prototype.repeat = function(num) {
  return new Array(num + 1).join(this);
};
  
  function hereDoc(f) {
    return f.toString().
        replace(/^[^\/]+\/\*!?/, '').
        replace(/\*\/[^\/]+$/, '');
  }
  function confirmation(msg) {
    console.log(msg, typeof msg);
    msg=(typeof msg === 'string')?msg:'voullez-vous vraiment effectué cette action';
    return window.confirm(msg, 'Alert');
  }
  function findById (data, id) {
    return $.grep(data, function(obj) {
      return obj.id == id;
    })[0];
  }
  
(function($) { 
  /* helper functions*/
  function getCheckedRows (dataContainer) {
    var entities = [];
    dataContainer.find('input:checkbox:checked').each(function() {
      entities.push($(this).val());
    });
    return entities.join(',');
  }

      
  // Add segments to a slider
  $.fn.addSliderSegments = function (amount) {
    return this.each(function () {
      var segmentGap = 100 / (amount - 1) + "%"
        , segment = "<div class='ui-slider-segment' style='margin-left: " + segmentGap + ";'></div>";
      $(this).prepend(segment.repeat(amount - 2));
    });
  };

  $(function() {
    /*** SET HEIGHT BASED ON VIEW PORT ***/       
    function setHeight() {
    var height = $(window).height(),
        newHeight = (100 * height) / 100;
    newHeight = parseInt(newHeight) + 'px';
    $(".menu").css('height',newHeight);
    }
    setHeight();
    $(window).bind('resize', setHeight);

    /*table*/
    // $('table').addClass('table table-hover table-bordered');

    // Custom Selects
    $("select.info").selectpicker({style: 'btn-info'});
    $("select.primary").selectpicker({style: 'btn-primary', menuStyle: 'dropdown-inverse', noneSelectedText : 'Tous'});
    $("select.huge.primary").selectpicker({style: 'btn-hg btn-primary', menuStyle: 'dropdown-inverse'});
    $("select.large").selectpicker({style: 'btn-lg btn-danger'});
    $("select.info").selectpicker({style: 'btn-info'});
    $("select.small").selectpicker({style: 'btn-sm btn-primary'});

    // Tabs
    $(".nav-tabs a").on('click', function (e) {
      e.preventDefault();
      $(this).tab("show");
    })

    // jQuery UI Spinner
    $.widget( "ui.customspinner", $.ui.spinner, {
      widgetEventPrefix: $.ui.spinner.prototype.widgetEventPrefix,
      _buttonHtml: function() { // Remove arrows on the buttons
        return "" +
        "<a class='ui-spinner-button ui-spinner-up ui-corner-tr'>" +
          "<span class='ui-icon " + this.options.icons.up + "'></span>" +
        "</a>" +
        "<a class='ui-spinner-button ui-spinner-down ui-corner-br'>" +
          "<span class='ui-icon " + this.options.icons.down + "'></span>" +
        "</a>";
      }
    });

    $('.spinner').customspinner({
      min: -99,
      max: 99
    }).on('focus', function () {
      $(this).closest('.ui-spinner').addClass('focus');
    }).on('blur', function () {
      $(this).closest('.ui-spinner').removeClass('focus');
    });

    // Tooltips
    $("[data-toggle=tooltip]").tooltip();

    // Add style class name to a tooltips
    $(".tooltip").addClass(function() {
      if ($(this).prev().attr("data-tooltip-style")) {
        return "tooltip-" + $(this).prev().attr("data-tooltip-style");
      }
    });


    // Focus state for append/prepend inputs
    $('.input-group').on('focus', '.form-control', function () {
      $(this).closest('.form-group, .navbar-search').addClass('focus');
    }).on('blur', '.form-control', function () {
      $(this).closest('.form-group, .navbar-search').removeClass('focus');
    });

    // Table: Toggle all checkboxes
    $('.table .toggle-all').on('click', function() {
      var ch = $(this).find(':checkbox').prop('checked');
      $(this).closest('.table').find('tbody :checkbox').checkbox(!ch ? 'check' : 'uncheck');
    });

    // Table: Add class row selected
    $('.table tbody').on('check uncheck toggle', ':checkbox', function (e) {
      var $this = $(this)
        , check = $this.prop('checked')
        , toggle = e.type == 'toggle'
        , checkboxes = $('.table tbody :checkbox')
        , checkAll = checkboxes.length == checkboxes.filter(':checked').length

      $this.closest('tr')[check ? 'addClass' : 'removeClass']('selected-row');
      if (toggle) $this.closest('.table').find('.toggle-all :checkbox').checkbox(checkAll ? 'check' : 'uncheck');
    });

    // jQuery UI Datepicker
    var datepickerSelector = '.has-datepicker';
    $(datepickerSelector).datepicker({
      showOtherMonths: true,
      selectOtherMonths: true,
      dateFormat: "yy-mm-dd",
    }).prev('.btn').on('click', function (e) {
      e && e.preventDefault();
      $(datepickerSelector).focus();
    });
    $.extend($.datepicker, {_checkOffset:function(inst,offset,isFixed){return offset}});
    // Now let's align datepicker with the prepend button
    $(datepickerSelector).datepicker('widget').css({'margin-left': -$(datepickerSelector).prev('.input-group-btn').find('.btn').outerWidth()});

    // Switch
    $("[data-toggle='switch']").wrap('<div class="switch" />').parent().bootstrapSwitch();

    /* close an alert */
    $('.js-close').on('click', function () {
      $(this).parent().hide();
    });

    /* print button */
    $('#btnPrint').on('click', function () {
      window.print();
    });

    /* form-wide */
    $('.form-wide').find('.form-control').parent().addClass('col-md-8');
    $('.special-form').find('.col-md-4').removeClass('col-md-4');


    /* menu config */
    var menu = $('.menu'),
        navLinks = menu.find('a'),
        shelfBtn = menu.find('#trigger-shelf');

          $.each(navLinks, function(i, a){
            $this = $(this);
            $this.attr('title', $this.find('span:last-child').text());
            $this.tooltip({'placement':'right'});
          });
    shelfBtn.on('click', function() {
        $(document.body).toggleClass('shelf');
        if($(document.body).hasClass('shelf')) {
          $.cookie('shelf_class', 'shelf', { expires: 7, path: '/' });
        }
        else {
          $.cookie('shelf_class', '', { expires: 7, path: '/' });
          $.each(navLinks, function(i, a){
            $this = $(this);
          });
        }

        shelfBtn.find('.block').toggleClass('fui-arrow-right');
    });
    if($.cookie('shelf_class') === 'shelf'){
        shelfBtn.find('.block').toggleClass('fui-arrow-right');
        $(document.body).addClass($.cookie('shelf_class'));
    }

    /* delete confirmation */
    $("[data-toggle=delete]").on('click', function(){
      return confirmation($(this).data('msg'));
    });


    /* slidedown button */   
    $('.js-toggle').on('click', function(){
      $(this).toggleClass('glyphicon-chevron-down').closest('header').next().slideToggle();
      return false;
    }); 

    // make code pretty
    window.prettyPrint && prettyPrint();
  });
})(jQuery);
