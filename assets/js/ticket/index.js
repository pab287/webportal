  $.ajax({
    url : baseUrl("ticket/ticket/all_tickets/"),
    type: "GET",
    dataType: "JSON",
    success: function(data){
      let dataCount = data.count;
      console.log(dataCount);
      let colWidth = "col-md-12";
      if(dataCount == 1){
        colWidth = "col-md-4";
      }else if(dataCount == 2){
        colWidth = "col-md-4";
      }else if(dataCount == 3){
        colWidth = "col-md-4";
      }else{
        colWidth = "col-md-4";
      }

      $.each(data, function(i ,val){
        let spanColor = "";
        let progressBar = "";
        if(val.priority === 'low'){
          spanColor = 'm-badge--info';
          progressBar = "m--bg-info";
        }else if(val.priority == 'medium'){
          spanColor = 'm-badge--warning';
          progressBar = "m--bg-warning";
        }else{
          spanColor = 'm-badge--danger';
          progressBar = "m--bg-danger";
        }
        let percent = parseInt((val.count / dataCount) * 100);
        let dashboard = '<div class="'+colWidth+'"><div class="m-portlet"><div class=""><div class="m-widget24 text-left">' +
                      '<div class="m-widget24__item">'+
                          '<h3 class="m-widget24__title m-badge '+spanColor+' m-badge--wide text-white">'+ val.priority.toUpperCase() +'</h3>'+
                          '<br>'+
                          '<span id="all" class="m-widget24__stats">'+val.count+'</span>'+
                          '<div class="m--space-10"></div>'+
                          '<div class="progress m-progress--sm">'+
                          '<div id="progress_all" class="progress-bar '+progressBar+'" role="progressbar" style="width:'+percent+'%;"  aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>'+
                      '</div>'+
                      '<span class="m-widget24__change">Percent</span>'+
                      '<span id="percent_all" class="m-widget24__number">'+percent+'%</span>'+
                      '</div>'+
                  '</div></div></div></div>';

        $("#dashboard").append(dashboard);
      });
    }
  });

  $.ajax({
    url : baseUrl("ticket/ticket/all_status/"),
    type: "GET",
    dataType: "JSON",
    success: function(data){
      let dataCount = data.count;
      let statusIcon = "";
      let statusColor = "";
        $.each(data, function(i ,val){
          if(val.status == 'completed'){
            statusIcon = "fa-check-square-o";
            statusColor = "text-success";
          }else if(val.status == 'in progress'){
            statusIcon = "fa-refresh";
            statusColor = "text-warning";
          }else{
            statusIcon = "fa-edit";
            statusColor = "text-danger";
          }
          let status_list =   '<div class="m-widget4__item">'+
                                '<div class="m-widget4__ext">'+
                                  '<span class="m-widget4__icon m--font-brand">'+
                                    '<i class="fa '+statusIcon+' text-info"></i>'+
                                  '</span>'+
                                '</div>'+
                                '<div class="m-widget4__info">'+
                                  '<span class="m-widget4__text">'+val.status.toUpperCase()+'</span>'+
                                '</div>'+
                                '<div class="m-widget4__ext text-right">'+
                                  '<span class="m-widget4__number m--font-info">'+val.count+'</span>'+
                                '</div>'+
                              '</div>';
          

          $("#status_list").append(status_list);
        });
      }
  });

  $.ajax({
    url : baseUrl("ticket/ticket/all_category/"),
    type: "GET",
    dataType: "JSON",
    success: function(data){
      let dataCount = data.count;
      let categoryIcon = "";
        $.each(data, function(i ,val){
          if(val.category == 'hardware'){
            categoryIcon = "fa-cogs";
          }else if(val.category == 'software'){
            categoryIcon = "fa-desktop";
          }else{
            categoryIcon = "fa-globe";
          }
          let status_list =   '<div class="m-widget4__item">'+
                                '<div class="m-widget4__ext">'+
                                  '<span class="m-widget4__icon m--font-brand">'+
                                    '<i class="fa '+categoryIcon+' text-info"></i>'+
                                  '</span>'+
                                '</div>'+
                                '<div class="m-widget4__info">'+
                                  '<span class="m-widget4__text">'+val.category.toUpperCase()+'</span>'+
                                '</div>'+
                                '<div class="m-widget4__ext text-right">'+
                                  '<span class="m-widget4__number m--font-info">'+val.count+'</span>'+
                                '</div>'+
                              '</div>';
          

          $("#category").append(status_list);
        });
      }
  });

  $.ajax({
    url : baseUrl("ticket/ticket/all_sub_category/"),
    type: "GET",
    dataType: "JSON",
    success: function(data){
      let dataCount = data.count;
      let status_list = "";

      if(dataCount){
        $.each(data, function(i ,val){
          if(val.sub_category == "" || val.sub_category == 0){
            status_list = "";
          }else{
            status_list =   '<div class="m-widget4__item">'+
                                '<div class="m-widget4__ext">'+
                                  '<span class="m-widget4__icon m--font-brand">'+
                                    '<i class="fa fa-tag text-info"></i>'+
                                  '</span>'+
                                '</div>'+
                                '<div class="m-widget4__info">'+
                                  '<span class="m-widget4__text">'+val.sub_category.toUpperCase()+'</span>'+
                                '</div>'+
                                '<div class="m-widget4__ext text-right">'+
                                  '<span class="m-widget4__number m--font-info">'+val.count+'</span>'+
                                '</div>'+
                              '</div>';
          
          }
          $("#sub_category").append(status_list);
        });
      }else{
        $("#sub_category").text("No tickets for Webportal.");
      }
    }
  });