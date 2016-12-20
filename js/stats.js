function A6ostatHalfDonut(svgname, jsondata, title) {

   nv.addGraph(function() {

      var width = 400,
          height = 400;

      var chart = nv.models.pieChart()
          .x(function(d) { return d.key })
          .y(function(d) { return d.value })
          .showLabels(false)
          .width(width)
          .height(height)
          .padAngle(.08)
          .cornerRadius(5)
          .donut(true);

      chart.legend.margin({top: 15});
      chart.pie.labelsOutside(true).donut(true);

      d3.select('#' + svgname)
          .datum(JSON.parse(jsondata))
          .transition().duration(1200)
          .attr('width', width)
          .attr('height', height)
          .call(chart);

      d3.select('#' + svgname)
         .append('text')
         .attr("x", width/2)
         .attr("y", 10)
         .attr('text-anchor', 'middle')
         .style('font-weight', 'bold')
         .text(title);

      return chart;
   });
}


function A6oVerticalBar(svgname, jsondata, title, width, jsonpalette) {

   nv.addGraph(function() {

      var height = 400;

      var chart = nv.models.discreteBarChart()
          .x(function(d) { return d.label })
          .y(function(d) { return d.value })
          .width(width)
          .height(height)
          .staggerLabels(true)
          .color(JSON.parse(jsonpalette))
          .showValues(false)
          .margin({"top":30});

      d3.select('#' + svgname)
         .datum([JSON.parse(jsondata)])
         .attr('height', height)
         .call(chart);

      d3.select('#' + svgname)
         .append('text')
         .attr("x", width/2)
         .attr("y", 10)
         .attr('text-anchor', 'middle')
         .style('font-weight', 'bold')
         .text(title);

      nv.utils.windowResize(chart.update);

      return chart;
   });
}


function A6oHorizontalBar(svgname, jsondata, title, width, jsonpalette ) {

        nv.addGraph(function() {

            var height = 400;

            var chart = nv.models.multiBarHorizontalChart()
                .x(function(d) { return d.label })
                .y(function(d) { return d.value })
                .margin({top: 50, right: 10, bottom: 50, left: 100})
                .color(JSON.parse(jsonpalette))
                .showControls(false)
                .showValues(true)
                .showYAxis(false)
                .groupSpacing(0.5);

            chart.valueFormat(
                function(d)
                {
                    return d3.format(',f')(d)
                }
            );

            chart.legend.margin({top: 15, right: 50});
            chart.legend.maxKeyLength(200);

            d3.select('#' + svgname)
             .datum([JSON.parse(jsondata)])
             .attr('height', height)
             .call(chart);

            d3.select('#' + svgname)
             .append('text')
             .attr("x", width/2)
             .attr("y", 10)
             .attr('text-anchor', 'middle')
             .style('font-weight', 'bold')
             .text(title);

            nv.utils.windowResize(chart.update);

            return chart;
        });
}

