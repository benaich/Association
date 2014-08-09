
/*!
 * BenCalendar v1.0
 * (c) 2014 Benaich med
 */
(function($) {
    var Calendar = {
        init: function(options, elem){
            var self = this;
            self.elem = elem;
            self.$elem = $(elem);
            self.date = new Date();
            self.year = self.date.getUTCFullYear();
            self.options = $.extend({}, $.fn.calendar.options, options)

            self.data = self.options.data;
            self.$elem.html(self.options.template).on('click', $.proxy(self.click, this));
            self.fill();
        },
        fill: function () {
            var html = '',
                i = 0;

            while (i < 12) {
                currentDate = new Date((i+1)+'/2/'+this.year);
                var id = this.isChecked(currentDate);
                active = (id) ? 'active' : '';
                html += '<span data-id="'+id+'" class="month '+active+'" data-date="'+currentDate.toDateString()+'">' + this.options.dates.months[i++] + '</span>';
            }
            this.$elem.find('.datetimepicker-months td').html(html);
            this.$elem.find('.year').text(this.year);
        },
        setDate: function(date){
            this.date = date;
        },
        isChecked: function(d){
            var id = 0;
            $.each(this.data, function(i, obj){
                if(d >= obj.start && d <= obj.end)
                    id = obj.id
            });
            return id;
        },
        click: function(e){
            var self = this,
                target = $(e.target).closest('span, td, th');
            switch (target[0].nodeName.toLowerCase()) {
                case 'th': {
                    if(!target.is('.year')){
                        var step = (target[0].className == 'prev') ? - 1 : 1;
                        self.year += step; 
                        self.fill();
                    }
                    break;
                }
                case 'span': {
                    self.options.monthClick(e);
                    break;
                }
            }
        }
    }

    $.fn.calendar = function(options){
        var cal = Object.create(Calendar);
        cal.init(options, this);
    }

    $.fn.calendar.options = {
        data: [],
        dates: {
            months: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
            monthsShort: ["Jan", "Fev", "Mar", "Avr", "Mai", "Jui", "Jul", "Aou", "Sep", "Oct", "Nov", "Dec"]
        },
        template: '<div class="datetimepicker-months" style="display:block;">' +
                    '<table class="table table-bordered table-condensed">' +
                    '<thead>' +
                        '<tr>' +
                            '<th class="prev"><i class="glyphicon glyphicon-arrow-left"></i> </th>' +
                            '<th class="year text-center"></th>' +
                            '<th class="next"><i class="glyphicon glyphicon-arrow-right pull-right"></i> </th>' +
                        '</tr>' +
                    '</thead>'+
                    '<tbody><tr><td colspan="7"></td></tr></tbody>'+
                    '</table>' +
                    '</div>',
        monthClick: function(e){
            if(confirm("really !!")) $(e.target).addClass('active');
        }
    }
})(jQuery);