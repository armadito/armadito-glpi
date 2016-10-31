function A6ostatHalfDonut(svgname, jsondata, title) {

   nv.addGraph(function() {

      var width = 400,
          height = 400;

      var chart = nv.models.pieChart()
          .x(function(d) { return d.key })
          .y(function(d) { return d.value })
          .showLabels(false)
          .color(d3.scale.category20().range().slice(8))
          .width(width)
          .height(height)
          .donut(true);

      chart.pie
          .startAngle(function(d) { return d.startAngle/2 -Math.PI/2 })
          .endAngle(function(d) { return d.endAngle/2 -Math.PI/2 });

      chart.legend.margin({top: 20});

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


function A6ostatBar(svgname, jsondata, title, width) {

   nv.addGraph(function() {

      var height = 400;

      var chart = nv.models.discreteBarChart()
          .x(function(d) { return d.label })
          .y(function(d) { return d.value })
          .width(width)
          .height(height)
          .staggerLabels(true)
          .showValues(false);

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
