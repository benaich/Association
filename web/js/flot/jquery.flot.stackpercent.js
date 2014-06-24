/**
This plugin is based on jQuery.flot.stack.js to provide support for stacked percentage
Modified on it by skeleton9@github

It can work with the tooltip plugin modified by skeleton9.

You may need to set yaxis:{max:1000} to show proper yaxis

find it on http://github.com/skeleton9/flot.stackpercent

------------------------------------------------------------------------------
Flot plugin for stacking data sets, i.e. putting them on top of each
other, for accumulative graphs. Note that the plugin assumes the data
is sorted on x. Also note that stacking a mix of positive and negative
values in most instances doesn't make sense (so it looks weird).

Two or more series are stacked when their "stack" attribute is set to
the same key (which can be any number or string or just "true"). To
specify the default stack, you can set

	series: {
		stackpercent: null or true
	}

or specify it for a specific series

	$.plot($("#placeholder"), [{ data: [ ... ], stackpercent: true ])

The stacking order is determined by the order of the data series in
the array (later series end up on top of the previous).

Internally, the plugin modifies the datapoints in each series, adding
an offset to the y value. For line series, extra data points are
inserted through interpolation. For bar charts, the second y value is
also adjusted.


Modified by skeleton9 2012-5-3 to support percentage stack

*/

(function ($) {
	var options = {
		series: { stackpercent: null } // or number/string
	};

	function init(plot) {

		// will be built up dynamically as a hash from x-value, or y-value if horizontal
		var stackBases = {};
		var processed = false;
		var stackSums = {};
		
		//set percentage for stacked chart
		function processRawData(plot, series, data, datapoints) 
		{	
			if (!processed)
			{
				processed = true;
				stackSums = getStackSums(plot.getData());
			}
			var num = data.length;
			series.percents = [];
			for(var j=0;j<num;j++)
			{
				var sum = stackSums[data[j][0]+""];
				if(sum>0)
				{
					series.percents.push(data[j][1]*100/sum);
				}
			}
		}
		
		//calculate summary
		function getStackSums(_data)
		{
			var data_len = _data.length;
			var sums = {};
			if(data_len > 0)
			{
				//caculate summary
				for(var i=0;i<data_len;i++)
				{
					var num = _data[i].data.length;
					for(var j=0;j<num;j++)
					{
						var value = 0;
						if(_data[i].data[j][1] != null)
						{
							value = _data[i].data[j][1];
						}
						if(sums[_data[i].data[j][0]+""])
						{
							sums[_data[i].data[j][0]+""] += value;
						}
						else
						{
							sums[_data[i].data[j][0]+""] = value;
						}
						 
					}
				}
			}
			return sums;
		}
		
		function stackData(plot, s, datapoints) {
			if (!s.stackpercent)
				return;
			if (!processed)
			{
				stackSums = getStackSums(plot.getData());
			}
			var newPoints = [];
			
			
			for(var i = 0; i < datapoints.points.length; i += 3) {
				// note that the values need to be turned into absolute y-values.
				// in other words, if you were to stack (x, y1), (x, y2), and (x, y3),
				// (each from different series, which is where stackBases comes in),
				// you'd want the new points to be (x, y1, 0), (x, y1+y2, y1), (x, y1+y2+y3, y1+y2)
				// generally, (x, thisValue + (base up to this point), + (base up to this point))
				if(!stackBases[datapoints.points[i]]) {
					stackBases[datapoints.points[i]] = 0;
				}
				newPoints[i] = datapoints.points[i];
				newPoints[i + 1] = datapoints.points[i + 1] + stackBases[datapoints.points[i]];
				newPoints[i + 2] = stackBases[datapoints.points[i]];
				stackBases[datapoints.points[i]] += datapoints.points[i + 1];
                                // change points to percentage values
                                // you may need to set yaxis:{ max = 100 }
				newPoints[i + 1] = newPoints[i+1]*100/stackSums[newPoints[i]+""];
				newPoints[i + 2] = newPoints[i+2]*100/stackSums[newPoints[i]+""];
			}

			datapoints.points = newPoints;
		}

		plot.hooks.processDatapoints.push(stackData);
	}

	$.plot.plugins.push({
		init: init,
		options: options,
		name: 'stackpercent',
		version: '0.1'
	});
})(jQuery);
