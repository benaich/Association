
/*!
 * BenCalendar v1.0
 * (c) 2014 Benaich med
 */
(function($) {
    function setPlot (elem, data) {
        $.plot(elem, data, {
            series: {
                 pie: {
                     show: true,
                     label: {
                         show: true,
                         formatter: function(label,point){
                             return(point.percent.toFixed(2) + '%');
                         }
                     }
                }
            },        
            legend: {show: true}
        });
    }
    setPlot('#statsbycity', data.city);
    setPlot('#statsbystatus', data.status);
    setPlot('#statsbygender', data.gender);
    setPlot('#statsbycotisation', data.cotisation);


var options = {
    series: {
        points: {
            show: true,
            radius: 4
        },
        lines: {
            show:true,
            fill:true,
            fillColor:"rgba(5, 141, 199, 0.3)",
            lineWidth:4
        },
        shadowSize: 0
    },
    grid: {
        color: '#646464',
        borderColor: 'transparent',
        borderWidth: 20,
        hoverable: true
    },
    xaxis: {
        mode:'time',
        timeformat: "%y/%m/%d",
        tickColor: 'transparent',
    },
    tooltip: true,
    tooltipOpts: {
        content: "<strong>%x</strong><br> <strong>%s</strong>: %y",
        shifts: {
            x: -60,
            y: 25
        }
    }
};
// Lines
$.plot($('#createdstats'), data.created, options);
// options.yaxis.tickSize = 1000;
options.tooltipOpts.content= "<strong>%x</strong><br> <strong>%s</strong>: %y DH";
$.plot($('#revenustats'), data.revenu, options);

    
    var form = $('#jsForm');

      form.on('submit', function() {
        form.addClass('working');
        $.ajax({ 
          type: "POST", 
          data: form.serialize(),
          url: form.attr('action'), 
          success: function(data){ 
            form.removeClass('working');
            setPlot('#configstats', data);
            console.log(data);
          }
        });
        return false;
    });

})(jQuery);