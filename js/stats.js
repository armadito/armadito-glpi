function A6oDonutChart(jsonparams) {

   var params = JSON.parse(jsonparams);

   nv.addGraph(function() {

      var chart = nv.models.pieChart()
          .x(function(d) { return d.key })
          .y(function(d) { return d.value })
          .showLabels(false)
          .width(params.width)
          .height(params.height)
          .padAngle(.08)
          .cornerRadius(5)
          .donut(true);

      chart.legend.margin({top: 15});
      chart.pie.labelsOutside(true).donut(true);

      d3.select('#' + params.svgname)
          .datum(params.data)
          .transition().duration(1200)
          .attr('width', params.width)
          .attr('height', params.height)
          .call(chart);

      d3.select('#' + params.svgname)
         .append('text')
         .attr("x", params.width/2)
         .attr("y", 15)
         .attr('text-anchor', 'middle')
         .style('font-weight', 'bold')
         .text(params.title);

      return chart;
   });
}


function A6oVerticalBarChart(jsonparams) {

   var params = JSON.parse(jsonparams);

   nv.addGraph(function() {

      var chart = nv.models.discreteBarChart()
          .x(function(d) { return d.label })
          .y(function(d) { return d.value })
          .width(params.width)
          .height(params.height)
          .color(params.palette)
          .margin({"top":30});

      applyParamsOnChart(chart, params);

      d3.select('#' + params.svgname)
         .datum([params.data])
         .attr('height', params.height)
         .call(chart);

      d3.select('#' + params.svgname)
         .append('text')
         .attr("x", params.width/2)
         .attr("y", 15)
         .attr('text-anchor', 'middle')
         .style('font-weight', 'bold')
         .text(params.title);

      nv.utils.windowResize(chart.update);

      return chart;
   });

}

function A6oHorizontalBarChart(jsonparams) {

    var params = JSON.parse(jsonparams);

    nv.addGraph(function() {

        var chart = nv.models.multiBarHorizontalChart()
            .x(function(d) { return d.label })
            .y(function(d) { return d.value })
            .margin({top: 50, right: 10, bottom: 50, left: 100})
            .color(params.palette)
            .showControls(false)
            .showValues(true)
            .showYAxis(false)
            .groupSpacing(0.5);

        chart.valueFormat(
            function(d)
            {
                return d3.format('r')(d)
            }
        );

        chart.legend.margin({top: 15, right: 50});
        chart.legend.maxKeyLength(200);

        d3.select('#' + params.svgname)
         .datum([params.data])
         .attr('height', params.height)
         .call(chart);

        d3.select('#' + params.svgname)
         .append('text')
         .attr("x", params.width/2)
         .attr("y", 15)
         .attr('text-anchor', 'middle')
         .style('font-weight', 'bold')
         .text(params.title);

        nv.utils.windowResize(chart.update);

        return chart;
    });
}

function applyParamsOnChart(chart, params) {

      if(params.showXAxis != null) {
          chart.showXAxis(params.showXAxis);
      }

      if(params.showYAxis != null) {
          chart.showYAxis(params.showYAxis);
      }

      if(params.showValues != null) {
          chart.showValues(params.showValues);
      }

      if(params.showLabels != null) {
          chart.showLabels(params.showLabels);
      }

      if(params.staggerLabels != null) {
          chart.staggerLabels(params.staggerLabels);
      }

      return chart;
}


