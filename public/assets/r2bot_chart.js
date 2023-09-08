var colorBars = [
  "rgba(255, 50, 50, 1)",
  "rgba(255, 99, 132, 1)",
  "rgba(255, 159, 64, 1)",
  "rgba(255, 205, 86, 1)",
  "rgba(0, 200, 200, 1)",
  "rgba(75, 192, 192, 1)",
  "rgba(0, 125, 235, 1)",
  "rgba(54, 162, 235, 1)",
  "rgba(153, 102, 255, 1)",
  "rgba(153, 50, 255, 1)",
  "rgba(122, 122, 113, 1)",
  "rgba(201, 203, 207, 1)",
];

function alternatePointStyles(ctx) {
  var index = ctx.dataIndex;
  return index % 2 === 0 ? "circle" : "rect";
}

function line_onResize(chart, size) {
  if (size.width < 400) {
    chart.set_data_width(1);
  } else {
    chart.set_data_width(2);
  }

  if (size.width < 500) {
    chart.canvas.parentElement.style.height = "50vh";
  } else {
    chart.canvas.parentElement.style.height = "80vh";
  }
}

function makeHalfAsOpaque(ctx) {
  lineColor = ctx.element.options.backgroundColor;
  return lineColor.replace(",1)", ",0.2");
}

function line_legend_onClick(event, legendItem, legend) {
  if (legend.chart.config._config.data.datasets.length > 1) {
    // //Hide the dataset
    var indexData = legendItem.datasetIndex;
    legend.chart.config._config.data.datasets[indexData].hidden =
      !legend.chart.config._config.data.datasets[indexData].hidden;

    // //Hide the axis
    var axis = legend.chart.config._config.data.datasets[indexData].yAxisID;
    legend.chart.config._config.options.scales[axis].display =
      !legend.chart.config._config.options.scales[axis].display;

    //Return the border color of other datasets
    for (var i = 0; i < legend.chart.config._config.data.datasets.length; i++) {
      var accentColor =
        legend.chart.config._config.data.datasets[i].accentColor;
      legend.chart.config._config.data.datasets[i].borderColor = accentColor;
      legend.chart.config._config.data.datasets[i].pointBorderColor =
        accentColor;
      legend.chart.config._config.data.datasets[i].pointBackgroundColor =
        accentColor;
    }
    legend.chart.update();
  }
}

function line_legend_hide_others_onClick(event, legendItem, legend) {
  if (legend.chart.config._config.data.datasets.length > 1) {
    var index = legendItem.datasetIndex;
    var ci = this.chart;
    var alreadyHidden =
      ci.getDatasetMeta(index).hidden === null
        ? false
        : ci.getDatasetMeta(index).hidden;

    var anyDataHidden = false;
    var numDataShown = 0;
    ci.data.datasets.forEach(function (e, i) {
      var meta = ci.getDatasetMeta(i);
      meAlreadyHidden = meta.hidden === null ? false : meta.hidden;
      if (i !== index) {
        if (meAlreadyHidden) {
          anyDataHidden = true;
        } else {
          numDataShown++;
        }
      } else {
        if (!meAlreadyHidden) {
          numDataShown++;
        }
      }
    });

    ci.data.datasets.forEach(function (e, i) {
      var meta = ci.getDatasetMeta(i);
      meAlreadyHidden = meta.hidden === null ? false : meta.hidden;
      if (i !== index) {
        if (!alreadyHidden) {
          if (meAlreadyHidden) {
            if (numDataShown === 1) {
              meta.hidden = null;
            }
          } else {
            if (!anyDataHidden) {
              meta.hidden = true;
            }
          }
          // meta.hidden = meta.hidden === null ? !meta.hidden : null;
        } else if (meta.hidden === null) {
          // meta.hidden = true;
        }
      } else if (i === index) {
        if (meAlreadyHidden) {
          meta.hidden = null;
        } else {
          if (anyDataHidden) {
            if (numDataShown === 1) {
              meta.hidden = null;
            } else {
              meta.hidden = true;
            }
          } else {
            meta.hidden = null;
          }
        }
      }
    });

    ci.update();
  }
}

function line_legend_onHover(event, legendItem, legend) {
  if (legend.chart.config._config.data.datasets.length > 1) {
    var indexData = legendItem.datasetIndex;
    if (!legend.chart.config._config.data.datasets[indexData].hidden) {
      for (
        var i = 0;
        i < legend.chart.config._config.data.datasets.length;
        i++
      ) {
        if (i === indexData) {
          var accentColor =
            legend.chart.config._config.data.datasets[i].accentColor;
          legend.chart.config._config.data.datasets[i].borderColor =
            accentColor;
          legend.chart.config._config.data.datasets[i].pointBorderColor =
            accentColor;
          legend.chart.config._config.data.datasets[i].pointBackgroundColor =
            accentColor;
        } else {
          var accentFadedColor =
            legend.chart.config._config.data.datasets[i].accentFadedColor;
          legend.chart.config._config.data.datasets[i].borderColor =
            accentFadedColor;
          legend.chart.config._config.data.datasets[i].pointBorderColor =
            accentFadedColor;
          legend.chart.config._config.data.datasets[i].pointBackgroundColor =
            accentFadedColor;
        }
      }
      legend.chart.update();
    }
  }
}

function line_legend_onLeave(event, legendItem, legend) {
  if (legend.chart.config._config.data.datasets.length > 1) {
    var indexData = legendItem.datasetIndex;
    for (var i = 0; i < legend.chart.config._config.data.datasets.length; i++) {
      legend.chart.config._config.data.datasets[i].borderColor =
        legend.chart.config._config.data.datasets[i].accentColor;
      legend.chart.config._config.data.datasets[i].pointBorderColor =
        legend.chart.config._config.data.datasets[i].accentColor;
      legend.chart.config._config.data.datasets[i].pointBackgroundColor =
        legend.chart.config._config.data.datasets[i].accentColor;
    }
    legend.chart.update();
  }
}

function pie_get_colors(number_of_data) {
  var colors = [];
  var hue = Math.ceil(360 / number_of_data);
  for (var i = 0; i < number_of_data; i++) {
    colors.push("hsla(" + i * hue + ",100%,70%,1)");
  }
  return colors;
}

class r2bot_chart extends Chart {
  constructor(context, array_options) {
    super(context, array_options);
  }

  show_data(index, bool) {
    if (this.config._config.data.datasets.length >= 1) {
      //Hide the dataset
      this.config._config.data.datasets[index].hidden = !bool;
      // this.config._config.data.datasets[index].showLine = !bool;
      //Hide the axis
      var axis = this.config._config.data.datasets[index].yAxisID;
      this.config._config.options.scales[axis].display = bool;

      this.update();
    }
  }
}

class r2bot_line_chart extends r2bot_chart {
  constructor(context, array_options, hide_on_click = true) {
    array_options.options.maintainAspectRatio = false;

    if (typeof array_options.options.stacked === "undefined") {
      array_options.options.stacked = false;
    }

    if (typeof array_options.options.onResize === "undefined") {
      array_options.options.onResize = line_onResize;
    }

    //Set x axes border
    array_options.options.scales.x.display = "auto";
    if (typeof array_options.options.scales.x.grid === "undefined") {
      array_options.options.scales.x.grid = {};
      array_options.options.scales.x.grid.borderWidth = 2;
    }

    //Set x axes' label rotation
    if (typeof array_options.options.scales.x.ticks === "undefined") {
      array_options.options.scales.x.ticks = {};
    }
    array_options.options.scales.x.ticks.maxRotation = 90;
    array_options.options.scales.x.ticks.minRotation = 0;

    //Set chart type
    if (
      typeof array_options.type === "undefined" ||
      array_options.type != "line"
    ) {
      array_options.type = "line";
    }

    //Set datasets' color
    if (
      array_options.data.datasets.length >= 1 &&
      typeof array_options.data.datasets[0].accentColor === "undefined"
    ) {
      var hue = Math.ceil(360 / array_options.data.datasets.length);
      for (var i = 0; i < array_options.data.datasets.length; i++) {
        // var hue = Math.ceil(360/(i+1));
        array_options.data.datasets[i].accentColor =
          "hsla(" + i * hue + ",100%,70%,1)";
        array_options.data.datasets[i].accentFadedColor =
          "hsla(" + i * hue + ",100%,70%,0.2)";
        array_options.data.datasets[i].borderColor =
          array_options.data.datasets[i].accentColor;
        array_options.data.datasets[i].backgroundColor =
          array_options.data.datasets[i].accentColor;
        array_options.data.datasets[i].pointBorderColor =
          array_options.data.datasets[i].accentColor;
        if (typeof array_options.data.datasets[i].yAxisID !== "undefined") {
          if (
            typeof array_options.options.scales[
              array_options.data.datasets[i].yAxisID
            ].grid === "undefined"
          ) {
            array_options.options.scales[
              array_options.data.datasets[i].yAxisID
            ].grid = {};
          }
          array_options.options.scales[
            array_options.data.datasets[i].yAxisID
          ].display = "auto";
          array_options.options.scales[
            array_options.data.datasets[i].yAxisID
          ].grid.borderWidth = 2;
          if (array_options.data.datasets.length > 1) {
            array_options.options.scales[
              array_options.data.datasets[i].yAxisID
            ].grid.borderColor = array_options.data.datasets[i].accentColor;
          }

          if (
            typeof array_options.options.scales[
              array_options.data.datasets[i].yAxisID
            ].ticks === "undefined"
          ) {
            array_options.options.scales[
              array_options.data.datasets[i].yAxisID
            ].ticks = {};
          }
        }
      }
    }

    // Set formating for points
    // if(typeof array_options.options.elements === 'undefined') {
    //     array_options.options.elements = {};
    // }
    // array_options.options.elements.point = {};

    if (typeof array_options.options.plugins.legend === "undefined") {
      array_options.options.plugins.legend = {};
    }
    array_options.options.plugins.legend.display =
      array_options.data.datasets.length > 1;

    //Set legend's events
    if (
      array_options.options.plugins.legend.display &&
      typeof array_options.data.datasets[0].accentColor !== "undefined" &&
      typeof array_options.data.datasets[0].accentFadedColor !== "undefined"
    ) {
      array_options.options.plugins.legend.labels = {};
      array_options.options.plugins.legend.labels.color = "#ffffff";
      if (hide_on_click) {
        array_options.options.plugins.legend.onClick = line_legend_onClick;
      } else {
        array_options.options.plugins.legend.onClick =
          line_legend_hide_others_onClick;
      }

      array_options.options.plugins.legend.onHover = line_legend_onHover;
      array_options.options.plugins.legend.onLeave = line_legend_onLeave;
    }

    if (typeof array_options.options.elements === "undefined") {
      array_options.options.elements = {};
    }
    if (typeof array_options.options.elements.point === "undefined") {
      array_options.options.elements.point = {};
    }
    array_options.options.elements.point.hoverBackgroundColor =
      makeHalfAsOpaque;
    // array_options.options.elements.point.hoverRadius = 10;

    super(context, array_options);
  }

  set_data_width(width) {
    for (var i = 0; i < this.config._config.data.datasets.length; i++) {
      this.config._config.data.datasets[i].borderWidth = parseInt(width);
      if (
        this.config._config.data.datasets[i].pointRadius > 0 ||
        this.config._config.data.datasets[i].pointRadius === undefined
      ) {
        this.config._config.data.datasets[i].pointRadius = parseInt(width);
        this.config._config.data.datasets[i].pointHoverRadius =
          parseInt(width) + 5;
      }
    }
    this.update();
  }
}

class r2bot_pie_chart extends Chart {
  constructor(context, array_options) {
    //Set chart type
    if (
      typeof array_options.type === "undefined" ||
      array_options.type != "pie"
    ) {
      array_options.type = "pie";
    }

    if (array_options.data.datasets[0].data.length > 0) {
      array_options.data.datasets[0].backgroundColor = pie_get_colors(
        array_options.data.datasets[0].data.length
      );
    }
    super(context, array_options);
  }
}

class r2bot_bar_chart extends Chart {
  constructor(context, array_options) {
    //Set chart type
    if (
      typeof array_options.type === "undefined" ||
      array_options.type != "bar"
    ) {
      array_options.type = "bar";
    }

    if (
      array_options.data.datasets.length > 1 &&
      typeof array_options.data.datasets[0].accentColor === "undefined" &&
      typeof array_options.data.datasets[0].backgroundColor === "undefined"
    ) {
      var hue = Math.ceil(360 / array_options.data.datasets.length);
      for (var i = 0; i < array_options.data.datasets.length; i++) {
        array_options.data.datasets[i].backgroundColor =
          "hsla(" + i * hue + ",100%,70%,1)";
      }
    } else if (array_options.data.datasets.length === 1) {
      if (
        typeof array_options.data.datasets[0].backgroundColor === "undefined"
      ) {
        var num_data = array_options.data.datasets[0].data.length;
        var hue = Math.ceil(360 / num_data);
        var backgroundColor = [];
        var borderColor = [];
        for (var i = 0; i < num_data; i++) {
          backgroundColor.push("hsla(" + i * hue + ",100%,70%,0.3)");
          borderColor.push("hsla(" + i * hue + ",100%,70%,1)");
        }
        array_options.data.datasets[0].backgroundColor = backgroundColor;
        array_options.data.datasets[0].borderColor = borderColor;
      }
    }
    super(context, array_options);
  }
}
